<?php

use App\Models\User;
use App\Models\OtpChallenge;
use App\Services\Security\OtpService;
use App\Services\Security\OtpCryptography;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use App\Models\OtpAttempt;


beforeEach(function () {
    $this->travelTo(
        \Illuminate\Support\Carbon::parse(
            '2026-01-15 12:00:00',
            'UTC'
        )
    );

    config([
        'otp.secret' => str_repeat('ab', 32),
        'otp.expiration_minutes' => 5,
        'otp.resend_seconds' => 60,
        'otp.max_requests_per_hour' => 5,
        'otp.max_attempts_per_hour' => 15,
    ]);

    Notification::fake();

    $this->service = app(OtpService::class);
});
afterEach(function () {
    $this->travelBack();
});

test('active user can request email verification otp', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
        'email_verified_at' => null,
    ]);

    $challenge = $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    );

    expect($challenge->exists)->toBeTrue()
        ->and($challenge->attempts)->toBe(0)
        ->and($challenge->verified_at)->toBeNull()
        ->and($challenge->consumed_at)->toBeNull()
        ->and($challenge->revoked_at)->toBeNull()
        ->and($challenge->destination)->toBe($user->email);

    Notification::assertSentOnDemand(
        OtpVerificationNotification::class
    );
});

test('otp is stored as a protected hash', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
    ]);

    $challenge = $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    );

    expect($challenge->code_hash)
        ->toMatch('/\A[a-f0-9]{64}\z/');

    expect(
        app(OtpCryptography::class)->verifySession(
            'test-session',
            $challenge->session_hash
        )
    )->toBeTrue();
});

test('inactive user cannot request otp', function () {
    $user = User::factory()->create([
        'estado' => 'inactivo',
    ]);

    expect(fn() => $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    ))->toThrow(ValidationException::class);

    expect(OtpChallenge::count())->toBe(0);

    Notification::assertNothingSent();
});

test('unverified user cannot request password reset otp', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
        'email_verified_at' => null,
    ]);

    expect(fn() => $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET,
        'test-session'
    ))->toThrow(ValidationException::class);

    Notification::assertNothingSent();
});

test('verified user can request password reset otp', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
        'email_verified_at' => now(),
    ]);

    $challenge = $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET,
        'test-session'
    );

    expect($challenge->purpose)
        ->toBe(OtpChallenge::PURPOSE_PASSWORD_RESET);

    Notification::assertSentOnDemand(
        OtpVerificationNotification::class
    );
});

test('otp expires after five minutes', function () {
    $this->freezeTime();

    $user = User::factory()->create([
        'estado' => 'activo',
    ]);

    $challenge = $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    );

    expect($challenge->isExpired())->toBeFalse();

    $this->travel(5)->minutes();

    expect($challenge->isExpired())->toBeTrue();
});

test('otp cannot be requested again before sixty seconds', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
    ]);

    $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    );

    expect(fn() => $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    ))->toThrow(ValidationException::class);

    expect(OtpChallenge::count())->toBe(1);
});

test('new otp revokes the previous challenge', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
    ]);

    $first = $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    );

    $this->travel(61)->seconds();

    $second = $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    );

    expect($first->fresh()->isRevoked())->toBeTrue()
        ->and($second->isRevoked())->toBeFalse()
        ->and($first->id)->not->toBe($second->id);
});

test('user cannot exceed five otp requests per hour', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
    ]);

    for ($i = 0; $i < 5; $i++) {
        $this->service->issue(
            $user,
            OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
            'test-session'
        );


        $this->travel(61)->seconds();
    }
    for ($i = 0; $i < 5; $i++) {
        // Código existente de emisión y avance temporal.
    }
    // Expectativa original de la excepción.

    expect(fn() => $this->service->issue(
        $user,
        OtpChallenge::PURPOSE_EMAIL_VERIFICATION,
        'test-session'
    ))->toThrow(ValidationException::class);

    expect(OtpChallenge::count())->toBe(5);
});

test('invalid otp purpose is rejected', function () {
    $user = User::factory()->create([
        'estado' => 'activo',
    ]);

    expect(fn() => $this->service->issue(
        $user,
        'INVALID_PURPOSE',
        'test-session'
    ))->toThrow(InvalidArgumentException::class);

    expect(OtpChallenge::count())->toBe(0);
});


function issueTestOtp(
    User $user,
    string $purpose = 'EMAIL_VERIFICATION',
    string $sessionId = 'test-session'
): OtpChallenge {
    $service = app(OtpService::class);

    $challenge = $service->issue(
        $user,
        $purpose,
        $sessionId
    );

    $challenge->code_hash = app(OtpCryptography::class)
        ->hashCode($challenge->id, '123456');

    $challenge->save();

    return $challenge;
}

test('correct otp marks challenge as verified', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeTrue();

    expect($challenge->fresh()->isVerified())->toBeTrue()
        ->and($challenge->fresh()->isConsumed())->toBeFalse();
});

test('incorrect otp increments persisted attempts', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '654321'
    ))->toBeFalse();

    expect($challenge->fresh()->attempts)->toBe(1)
        ->and($challenge->fresh()->isVerified())->toBeFalse();
});

test('five failed attempts revoke challenge', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    for ($i = 0; $i < 5; $i++) {
        expect($this->service->verify(
            $user,
            $challenge->id,
            $challenge->purpose,
            'test-session',
            '654321'
        ))->toBeFalse();
    }

    expect($challenge->fresh()->attempts)->toBe(5)
        ->and($challenge->fresh()->isRevoked())->toBeTrue();

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();
});

test('expired otp cannot be verified', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    $this->travel(5)->minutes();

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();
});

test('otp cannot be verified from another session', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'different-session',
        '123456'
    ))->toBeFalse();
});

test('otp cannot be verified by another user', function () {
    $user = User::factory()->create();

    $otherUser = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->verify(
        $otherUser,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();
});

test('otp cannot be used for another purpose', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $challenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $challenge->id,
        OtpChallenge::PURPOSE_PASSWORD_RESET,
        'test-session',
        '123456'
    ))->toBeFalse();
});

test('verified otp cannot be verified again', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    $this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    );

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();
});

test('unverified otp cannot be consumed', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->consume(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        function () {
            throw new RuntimeException(
                'Operation should not execute.'
            );
        }
    ))->toBeFalse();

    expect($challenge->fresh()->isConsumed())->toBeFalse();
});

test('verified otp can only be consumed once', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $challenge = issueTestOtp($user);

    $this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    );

    expect($this->service->consume(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        function (User $lockedUser) {
            $lockedUser->forceFill([
                'email_verified_at' => now(),
            ])->save();
        }
    ))->toBeTrue();

    expect($challenge->fresh()->isConsumed())->toBeTrue()
        ->and($user->fresh()->email_verified_at)->not->toBeNull();

    expect($this->service->consume(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        function () {
            throw new RuntimeException(
                'Operation should not execute twice.'
            );
        }
    ))->toBeFalse();
});

test('failed operation rolls back otp consumption', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $challenge = issueTestOtp($user);

    $this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    );

    expect(fn() => $this->service->consume(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        function (User $lockedUser) {
            $lockedUser->forceFill([
                'email_verified_at' => now(),
            ])->save();

            throw new RuntimeException('Simulated failure.');
        }
    ))->toThrow(RuntimeException::class);

    expect($challenge->fresh()->isConsumed())->toBeFalse()
        ->and($challenge->fresh()->isVerified())->toBeTrue()
        ->and($user->fresh()->email_verified_at)->toBeNull();
});

test('verified but expired otp cannot be consumed', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    $this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    );

    $this->travel(5)->minutes();

    expect($this->service->consume(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        function () {
            throw new RuntimeException(
                'Operation should not execute.'
            );
        }
    ))->toBeFalse();
});

test('revoked otp cannot be verified', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->revoke(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session'
    ))->toBeTrue();

    expect($challenge->fresh()->isRevoked())->toBeTrue();

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();
});

test('changing email invalidates previous otp binding', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    $user->update([
        'email' => 'changed@example.com',
    ]);

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();
});


test('failed otp verification creates attempt record', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    $this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '654321'
    );

    expect(OtpAttempt::count())->toBe(1)
        ->and($challenge->failedAttempts()->count())->toBe(1)
        ->and($challenge->fresh()->attempts)->toBe(1);

    $attempt = OtpAttempt::first();

    expect($attempt->challenge_id)->toBe($challenge->id)
        ->and($attempt->attempted_at)->not->toBeNull();
});

test('successful verification does not create failed attempt', function () {
    $user = User::factory()->create();

    $challenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $challenge->id,
        $challenge->purpose,
        'test-session',
        '123456'
    ))->toBeTrue();

    expect(OtpAttempt::count())->toBe(0)
        ->and($challenge->fresh()->attempts)->toBe(0);
});

test('user cannot exceed fifteen accumulated failed attempts', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        $challenge = issueTestOtp($user);

        for ($j = 0; $j < 5; $j++) {
            expect($this->service->verify(
                $user,
                $challenge->id,
                $challenge->purpose,
                'test-session',
                '654321'
            ))->toBeFalse();
        }

        $this->travel(61)->seconds();
    }

    expect(OtpAttempt::count())->toBe(15);

    $newChallenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $newChallenge->id,
        $newChallenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();

    expect($newChallenge->fresh()->attempts)->toBe(0)
        ->and($newChallenge->fresh()->isVerified())->toBeFalse()
        ->and(OtpAttempt::count())->toBe(15);
});

test('old failed attempts no longer count after one hour', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        $challenge = issueTestOtp($user);

        for ($j = 0; $j < 5; $j++) {
            $this->service->verify(
                $user,
                $challenge->id,
                $challenge->purpose,
                'test-session',
                '654321'
            );
        }

        $this->travel(61)->seconds();
    }

    expect(OtpAttempt::count())->toBe(15);

    $this->travel(61)->minutes();

    $newChallenge = issueTestOtp($user);

    expect($this->service->verify(
        $user,
        $newChallenge->id,
        $newChallenge->purpose,
        'test-session',
        '123456'
    ))->toBeTrue();

    expect(OtpAttempt::count())->toBe(15);
});

test('accumulated attempt limit is isolated by user', function () {
    config(['otp.max_attempts_per_hour' => 1]);

    $firstUser = User::factory()->create();

    $secondUser = User::factory()->create();

    $firstChallenge = issueTestOtp($firstUser);

    $this->service->verify(
        $firstUser,
        $firstChallenge->id,
        $firstChallenge->purpose,
        'test-session',
        '654321'
    );

    expect($this->service->verify(
        $firstUser,
        $firstChallenge->id,
        $firstChallenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();

    $secondChallenge = issueTestOtp($secondUser);

    expect($this->service->verify(
        $secondUser,
        $secondChallenge->id,
        $secondChallenge->purpose,
        'test-session',
        '123456'
    ))->toBeTrue();
});

test('accumulated attempt limit applies across otp purposes', function () {
    config(['otp.max_attempts_per_hour' => 1]);

    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $firstChallenge = issueTestOtp(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_RESET
    );

    $this->service->verify(
        $user,
        $firstChallenge->id,
        $firstChallenge->purpose,
        'test-session',
        '654321'
    );

    $secondChallenge = issueTestOtp(
        $user,
        OtpChallenge::PURPOSE_PASSWORD_CHANGE
    );

    expect($this->service->verify(
        $user,
        $secondChallenge->id,
        $secondChallenge->purpose,
        'test-session',
        '123456'
    ))->toBeFalse();

    expect(OtpAttempt::count())->toBe(1)
        ->and($secondChallenge->fresh()->attempts)->toBe(0);
});
