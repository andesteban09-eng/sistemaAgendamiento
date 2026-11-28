<?php

namespace App\Services\Security;

use App\Models\User;
use App\Models\OtpChallenge;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;
use App\Models\OtpAttempt;

class OtpService
{
    public function __construct(
        private readonly OtpCryptography $cryptography
    ) {}

    public function issue(
        User $user,
        string $purpose,
        string $sessionId
    ): OtpChallenge {
        $this->validatePurpose($purpose);

        $challengeId = (string) Str::uuid();

        $code = $this->cryptography->generateCode();

        $codeHash = $this->cryptography->hashCode(
            $challengeId,
            $code
        );

        $sessionHash = $this->cryptography->hashSession(
            $sessionId
        );

        $challenge = DB::transaction(function () use (
            $user,
            $purpose,
            $challengeId,
            $codeHash,
            $sessionHash
        ) {
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateUser($lockedUser, $purpose);

            $now = now();

            $this->ensureCanIssue(
                $lockedUser,
                $purpose
            );

            $this->revokePrevious(
                $lockedUser,
                $purpose
            );

            $challenge = new OtpChallenge([
                'user_id' => $lockedUser->getKey(),
                'purpose' => $purpose,
                'channel' => OtpChallenge::CHANNEL_EMAIL,
                'destination' => $lockedUser->email,
                'code_hash' => $codeHash,
                'session_hash' => $sessionHash,
                'attempts' => 0,
                'expires_at' => $now->copy()->addMinutes(
                    config('otp.expiration_minutes')
                ),
            ]);

            $challenge->id = $challengeId;

            $challenge->save();

            return $challenge;
        });

        try {
            Notification::route(
                'mail',
                $challenge->destination
            )->notify(
                new OtpVerificationNotification(
                    $code,
                    $purpose,
                    config('otp.expiration_minutes')
                )
            );
        } catch (Throwable $exception) {
            OtpChallenge::query()
                ->whereKey($challenge->getKey())
                ->whereNull('consumed_at')
                ->whereNull('revoked_at')
                ->update([
                    'revoked_at' => now(),
                ]);

            // No propagar excepciones SMTP que puedan
            // contener información sensible del mensaje.
            throw ValidationException::withMessages([
                'otp' => 'No fue posible enviar el código.',
            ]);
        }

        return $challenge;
    }

    private function validatePurpose(string $purpose): void
    {
        $allowedPurposes = [
            OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
            OtpChallenge::PURPOSE_PASSWORD_RESET,
            OtpChallenge::PURPOSE_PASSWORD_CHANGE,
        ];

        if (!in_array($purpose, $allowedPurposes, true)) {
            throw new InvalidArgumentException(
                'El propósito del OTP no es válido.'
            );
        }
    }

    private function validateUser(
        User $user,
        string $purpose
    ): void {
        if ($user->estado !== 'activo') {
            throw ValidationException::withMessages([
                'otp' => 'La operación no está disponible.',
            ]);
        }

        if (
            $purpose !== OtpChallenge::PURPOSE_EMAIL_VERIFICATION
            && $user->email_verified_at === null
        ) {
            throw ValidationException::withMessages([
                'otp' => 'La operación no está disponible.',
            ]);
        }
    }

    private function ensureCanIssue(
        User $user,
        string $purpose
    ): void {
        $recentChallenge = OtpChallenge::query()
            ->where('user_id', $user->getKey())
            ->where('purpose', $purpose)
            ->where(
                'created_at',
                '>',
                now()->subSeconds(
                    config('otp.resend_seconds')
                )
            )
            ->exists();

        if ($recentChallenge) {
            throw ValidationException::withMessages([
                'otp' => 'Espera antes de solicitar otro código.',
            ]);
        }

        $requests = OtpChallenge::query()
            ->where('user_id', $user->getKey())
            ->where(
                'created_at',
                '>=',
                now()->subHour()
            )
            ->count();

        if ($requests >= config('otp.max_requests_per_hour')) {
            throw ValidationException::withMessages([
                'otp' => 'Has alcanzado el límite de solicitudes.',
            ]);
        }
    }

    private function revokePrevious(
        User $user,
        string $purpose
    ): void {
        OtpChallenge::query()
            ->where('user_id', $user->getKey())
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->update([
                'revoked_at' => now(),
            ]);
    }


    public function verify(
        User $user,
        string $challengeId,
        string $purpose,
        string $sessionId,
        string $code
    ): bool {
        $this->validatePurpose($purpose);

        return DB::transaction(function () use (
            $user,
            $challengeId,
            $purpose,
            $sessionId,
            $code
        ) {
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateUser($lockedUser, $purpose);

            $challenge = $this->findBoundChallenge(
                $lockedUser,
                $challengeId,
                $purpose,
                $sessionId
            );

            if (
                $challenge === null
                || !$challenge->canBeVerified()
            ) {
                return false;
            }

            if ($this->hasReachedAttemptLimit($lockedUser)) {
                return false;
            }

            if (!$this->cryptography->verifyCode(
                $challenge->id,
                $code,
                $challenge->code_hash
            )) {
                $now = now();

                $challenge->attempts++;

                if (!$challenge->hasAttemptsAvailable()) {
                    $challenge->revoked_at = $now;
                }

                $challenge->save();

                $challenge->failedAttempts()->create([
                    'attempted_at' => $now,
                ]);

                return false;
            }

            $challenge->verified_at = now();

            $challenge->save();

            return true;
        });
    }


    private function findBoundChallenge(
        User $user,
        string $challengeId,
        string $purpose,
        string $sessionId
    ): ?OtpChallenge {
        if (!Str::isUuid($challengeId)) {
            return null;
        }

        $challenge = OtpChallenge::query()
            ->whereKey($challengeId)
            ->where('user_id', $user->getKey())
            ->where('purpose', $purpose)
            ->where('channel', OtpChallenge::CHANNEL_EMAIL)
            ->lockForUpdate()
            ->first();

        if ($challenge === null) {
            return null;
        }

        if ($challenge->destination !== $user->email) {
            return null;
        }

        if (!$this->cryptography->verifySession(
            $sessionId,
            $challenge->session_hash
        )) {
            return null;
        }

        return $challenge;
    }

    public function consume(
        User $user,
        string $challengeId,
        string $purpose,
        string $sessionId,
        \Closure $operation
    ): bool {
        $this->validatePurpose($purpose);

        return DB::transaction(function () use (
            $user,
            $challengeId,
            $purpose,
            $sessionId,
            $operation
        ) {
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateUser($lockedUser, $purpose);

            $challenge = $this->findBoundChallenge(
                $lockedUser,
                $challengeId,
                $purpose,
                $sessionId
            );

            if (
                $challenge === null
                || !$challenge->canBeConsumed()
            ) {
                return false;
            }

            $challenge->consumed_at = now();

            $challenge->save();

            $operation($lockedUser);

            return true;
        });
    }

    public function revoke(
        User $user,
        string $challengeId,
        string $purpose,
        string $sessionId
    ): bool {
        $this->validatePurpose($purpose);

        return DB::transaction(function () use (
            $user,
            $challengeId,
            $purpose,
            $sessionId
        ) {
            $lockedUser = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $challenge = $this->findBoundChallenge(
                $lockedUser,
                $challengeId,
                $purpose,
                $sessionId
            );

            if (
                $challenge === null
                || $challenge->isConsumed()
                || $challenge->isRevoked()
            ) {
                return false;
            }

            $challenge->revoked_at = now();

            $challenge->save();

            return true;
        });
    }

    private function hasReachedAttemptLimit(User $user): bool
    {
        $failedAttempts = OtpAttempt::query()
            ->whereHas('challenge', function ($query) use ($user) {
                $query->where('user_id', $user->getKey());
            })
            ->where('attempted_at', '>', now()->subHour())
            ->count();

        return $failedAttempts >= config(
            'otp.max_attempts_per_hour'
        );
    }
}
