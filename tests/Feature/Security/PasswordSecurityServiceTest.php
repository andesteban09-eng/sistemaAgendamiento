
<?php

use App\Models\User;
use App\Models\OtpChallenge;
use App\Services\Security\OtpCryptography;
use App\Services\Security\OtpService;
use App\Services\Security\PasswordSecurityService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->travelTo(
        Carbon::parse('2026-01-15 12:00:00', 'UTC')
    );

    config([
        'otp.secret' => str_repeat('ab', 32),
        'otp.expiration_minutes' => 5,
        'otp.resend_seconds' => 60,
        'otp.max_requests_per_hour' => 5,
        'otp.max_attempts_per_hour' => 15,

        'session.driver' => 'database',
        'session.connection' => null,
        'session.table' => 'sessions',
    ]);

    Notification::fake();

    $this->service = app(
        PasswordSecurityService::class
    );
});

afterEach(function () {
    $this->travelBack();
});

function createPasswordSecurityChallenge(
    User $user,
    string $purpose,
    string $sessionId = 'password-security-session',
    bool $verified = true
): OtpChallenge {
    $otpService = app(OtpService::class);

    $cryptography = app(OtpCryptography::class);

    $challenge = $otpService->issue(
        $user,
        $purpose,
        $sessionId
    );

    $challenge->code_hash = $cryptography->hashCode(
        $challenge->id,
        '123456'
    );

    $challenge->save();

    if ($verified) {
        expect(
            $otpService->verify(
                $user,
                $challenge->id,
                $purpose,
                $sessionId,
                '123456'
            )
        )->toBeTrue();
    }

    return $challenge->refresh();
}

it('verifies user email using a verified otp', function () {
    $user = User::factory()->unverified()->create();

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION
    );

    expect($user->email_verified_at)->toBeNull();

    $result = $this->service->verifyEmail(
        $user,
        $challenge->id,
        'password-security-session'
    );

    expect($result)->toBeTrue();

    expect($user->fresh()->email_verified_at)
        ->not->toBeNull();

    expect($challenge->fresh()->consumed_at)
        ->not->toBeNull();
});

it('rejects email verification using an unverified otp', function () {
    $user = User::factory()->unverified()->create();

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        verified: false
    );

    $result = $this->service->verifyEmail(
        $user,
        $challenge->id,
        'password-security-session'
    );

    expect($result)->toBeFalse();

    expect($user->fresh()->email_verified_at)
        ->toBeNull();

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('rejects email verification using an expired otp', function () {
    $user = User::factory()->unverified()->create();

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION
    );

    $this->travel(6)->minutes();

    $result = $this->service->verifyEmail(
        $user,
        $challenge->id,
        'password-security-session'
    );

    expect($result)->toBeFalse();

    expect($user->fresh()->email_verified_at)
        ->toBeNull();

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('rejects email verification from another session', function () {
    $user = User::factory()->unverified()->create();

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION
    );

    $result = $this->service->verifyEmail(
        $user,
        $challenge->id,
        'different-session'
    );

    expect($result)->toBeFalse();

    expect($user->fresh()->email_verified_at)
        ->toBeNull();

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('cannot reuse an otp after email verification', function () {
    $user = User::factory()->unverified()->create();

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION
    );

    expect(
        $this->service->verifyEmail(
            $user,
            $challenge->id,
            'password-security-session'
        )
    )->toBeTrue();

    $consumedAt = $challenge->fresh()->consumed_at;

    expect($consumedAt)->not->toBeNull();

    expect(
        $this->service->verifyEmail(
            $user,
            $challenge->id,
            'password-security-session'
        )
    )->toBeFalse();

    expect(
        $challenge->fresh()->consumed_at->equalTo(
            $consumedAt
        )
    )->toBeTrue();
});

it('does not consume an otp when email is already verified', function () {
    $user = User::factory()->unverified()->create();

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION
    );

    $user->email_verified_at = now();

    $user->save();

    $verifiedAt = $user->fresh()->email_verified_at;

    expect(fn () => $this->service->verifyEmail(
        $user,
        $challenge->id,
        'password-security-session'
    ))->toThrow(ValidationException::class);

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();

    expect(
        $user->fresh()->email_verified_at->equalTo(
            $verifiedAt
        )
    )->toBeTrue();
});

it('resets password using a verified otp', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Anterior123*'),
    ]);

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    $result = $this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'NuevaSegura123*'
    );

    expect($result)->toBeTrue();

    expect(
        Hash::check(
            'NuevaSegura123*',
            $user->fresh()->password
        )
    )->toBeTrue();

    expect(
        Hash::check(
            'Anterior123*',
            $user->fresh()->password
        )
    )->toBeFalse();

    expect($challenge->fresh()->consumed_at)
        ->not->toBeNull();
});

it('cannot reset password using an unverified otp', function () {
    $user = User::factory()->create();

    $originalHash = $user->password;

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET,
        verified: false
    );

    $result = $this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'NuevaSegura123*'
    );

    expect($result)->toBeFalse();

    expect($user->fresh()->password)
        ->toBe($originalHash);

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('rejects resetting password to the current password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Anterior123*'),
    ]);

    $originalHash = $user->password;

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    expect(fn () => $this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'Anterior123*',
        'Anterior123*'
    ))->toThrow(ValidationException::class);

    expect($user->fresh()->password)
        ->toBe($originalHash);

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('rejects password confirmation mismatch', function () {
    $user = User::factory()->create();

    $originalHash = $user->password;

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    expect(fn () => $this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'Diferente123*'
    ))->toThrow(ValidationException::class);

    expect($user->fresh()->password)
        ->toBe($originalHash);

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('cannot reset password using an otp for another purpose', function () {
    $user = User::factory()->create();

    $originalHash = $user->password;

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_CHANGE
    );

    $result = $this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'NuevaSegura123*'
    );

    expect($result)->toBeFalse();

    expect($user->fresh()->password)
        ->toBe($originalHash);

    expect($challenge->fresh()->consumed_at)
        ->toBeNull();
});

it('rotates remember token after password reset', function () {
    $user = User::factory()->create([
        'remember_token' => str_repeat('a', 60),
    ]);

    $originalToken = $user->remember_token;

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    expect($this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'NuevaSegura123*'
    ))->toBeTrue();

    $newToken = $user->fresh()->remember_token;

    expect($newToken)->not->toBeNull();

    expect($newToken)->not->toBe($originalToken);

    expect(strlen($newToken))->toBe(60);
});

it('revokes other pending otp challenges after password reset', function () {
    $user = User::factory()->create();

    $otherChallenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_CHANGE
    );

    $resetChallenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    expect($otherChallenge->revoked_at)->toBeNull();

    expect($this->service->resetPassword(
        $user,
        $resetChallenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'NuevaSegura123*'
    ))->toBeTrue();

    expect($otherChallenge->fresh()->revoked_at)
        ->not->toBeNull();

    expect($otherChallenge->fresh()->consumed_at)
        ->toBeNull();

    expect($resetChallenge->fresh()->consumed_at)
        ->not->toBeNull();

    expect($resetChallenge->fresh()->revoked_at)
        ->toBeNull();
});

it('revokes user sessions without affecting other users', function () {
    $user = User::factory()->create();

    $otherUser = User::factory()->create();

    DB::table('sessions')->insert([
        [
            'id' => 'security-session-one',
            'user_id' => $user->id,
            'ip_address' => '192.0.2.10',
            'user_agent' => 'Test Browser',
            'payload' => base64_encode('test-payload'),
            'last_activity' => now()->timestamp,
        ],
        [
            'id' => 'security-session-two',
            'user_id' => $user->id,
            'ip_address' => '192.0.2.11',
            'user_agent' => 'Test Browser',
            'payload' => base64_encode('test-payload'),
            'last_activity' => now()->timestamp,
        ],
        [
            'id' => 'other-user-session',
            'user_id' => $otherUser->id,
            'ip_address' => '192.0.2.12',
            'user_agent' => 'Test Browser',
            'payload' => base64_encode('test-payload'),
            'last_activity' => now()->timestamp,
        ],
    ]);

    $challenge = createPasswordSecurityChallenge(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    expect(
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->count()
    )->toBe(2);

    expect($this->service->resetPassword(
        $user,
        $challenge->id,
        'password-security-session',
        'NuevaSegura123*',
        'NuevaSegura123*'
    ))->toBeTrue();

    expect(
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->count()
    )->toBe(0);

    expect(
        DB::table('sessions')
            ->where('user_id', $otherUser->id)
            ->count()
    )->toBe(1);
});
