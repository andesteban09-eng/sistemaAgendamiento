<?php

namespace App\Services\Security;

use App\Models\User;
use App\Models\OtpChallenge;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use RuntimeException;

class PasswordSecurityService
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}

    public function verifyEmail(
        User $user,
        string $challengeId,
        string $sessionId
    ): bool {
        return $this->otpService->consume(
            $user,
            $challengeId,
            OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
            $sessionId,
            function (User $lockedUser): void {
                if ($lockedUser->email_verified_at !== null) {
                    throw ValidationException::withMessages([
                        'otp' => 'El correo ya se encuentra verificado.',
                    ]);
                }

                $lockedUser->email_verified_at = now();

                if (! $lockedUser->save()) {
                    throw new RuntimeException(
                        'No fue posible verificar el correo.'
                    );
                }
            }
        );
    }

    public function resetPassword(
        User $user,
        string $challengeId,
        string $sessionId,
        string $password,
        string $passwordConfirmation
    ): bool {
        $this->validatePassword(
            $password,
            $passwordConfirmation
        );

        return $this->otpService->consume(
            $user,
            $challengeId,
            OtpChallenge::PURPOSE_PASSWORD_RESET,
            $sessionId,
            function (User $lockedUser) use ($password): void {
                $this->replacePassword(
                    $lockedUser,
                    $password
                );
            }
        );
    }

    public function changePassword(
        User $user,
        string $challengeId,
        string $sessionId,
        string $currentPassword,
        string $password,
        string $passwordConfirmation
    ): bool {
        $this->validatePassword(
            $password,
            $passwordConfirmation
        );

        return $this->otpService->consume(
            $user,
            $challengeId,
            OtpChallenge::PURPOSE_PASSWORD_CHANGE,
            $sessionId,
            function (User $lockedUser) use (
                $currentPassword,
                $password
            ): void {
                if (! Hash::check(
                    $currentPassword,
                    $lockedUser->password
                )) {
                    throw ValidationException::withMessages([
                        'current_password' =>
                        'La contraseña actual no es correcta.',
                    ]);
                }

                $this->replacePassword(
                    $lockedUser,
                    $password
                );
            }
        );
    }

    private function validatePassword(
        string $password,
        string $passwordConfirmation
    ): void {
        Validator::make(
            [
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::defaults(),
                    function ($attribute, $value, $fail): void {
                        if (strlen($value) > 72) {
                            $fail(
                                'La contraseña supera el límite de 72 bytes.'
                            );
                        }
                    },
                ],
            ]
        )->validate();
    }

    private function replacePassword(
        User $user,
        string $password
    ): void {
        if (Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' =>
                'La nueva contraseña debe ser diferente de la actual.',
            ]);
        }

        $user->password = Hash::make($password);

        $user->setRememberToken(Str::random(60));

        if (! $user->save()) {
            throw new RuntimeException(
                'No fue posible actualizar las credenciales.'
            );
        }

        $this->revokePendingChallenges($user);

        $this->revokeSessions($user);
    }

    private function revokePendingChallenges(
        User $user
    ): void {
        $user->otpChallenges()
            ->whereNull('consumed_at')
            ->whereNull('revoked_at')
            ->update([
                'revoked_at' => now(),
            ]);
    }

    private function revokeSessions(User $user): void
    {
        $connection = config('session.connection')
            ?? config('database.default');

        if (
            config('session.driver') !== 'database'
            || $connection !== config('database.default')
        ) {
            throw new RuntimeException(
                'La infraestructura de sesiones no permite '
                    . 'una revocación transaccional.'
            );
        }

        DB::connection($connection)
            ->table(config('session.table'))
            ->where('user_id', $user->getKey())
            ->delete();
    }
}
