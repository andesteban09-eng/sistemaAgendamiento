<?php

use App\Services\Security\OtpRateLimiter;
use App\Services\Security\OtpCryptography;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    \Illuminate\Support\Carbon::setTestNow(
        \Illuminate\Support\Carbon::parse(
            '2026-01-15 12:00:00',
            'UTC'
        )
    );

    config([
        'otp.secret' => str_repeat('ab', 32),
        'otp.max_requests_per_ip_per_hour' => 30,
        'otp.max_attempts_per_ip_per_hour' => 60,
    ]);
});

afterEach(function () {
    \Illuminate\Support\Carbon::setTestNow();
});

it('allows thirty otp requests per ip per hour', function () {
    $limiter = app(OtpRateLimiter::class);
    $ip = '192.0.2.10';

    for ($i = 0; $i < 30; $i++) {
        expect($limiter->allowIssue($ip))
            ->toBeTrue();
    }

    expect($limiter->allowIssue($ip))
        ->toBeFalse();
});

it('allows sixty otp verifications per ip per hour', function () {
    $limiter = app(OtpRateLimiter::class);
    $ip = '192.0.2.20';

    for ($i = 0; $i < 60; $i++) {
        expect($limiter->allowVerification($ip))
            ->toBeTrue();
    }

    expect($limiter->allowVerification($ip))
        ->toBeFalse();
});

it('isolates rate limits by ip address', function () {
    $limiter = app(OtpRateLimiter::class);

    config([
        'otp.max_requests_per_ip_per_hour' => 1,
    ]);

    $firstIp = '192.0.2.30';
    $secondIp = '192.0.2.31';

    expect($limiter->allowIssue($firstIp))
        ->toBeTrue();

    expect($limiter->allowIssue($firstIp))
        ->toBeFalse();

    expect($limiter->allowIssue($secondIp))
        ->toBeTrue();
});

it('isolates issuance and verification limits', function () {
    $limiter = app(OtpRateLimiter::class);

    config([
        'otp.max_requests_per_ip_per_hour' => 1,
        'otp.max_attempts_per_ip_per_hour' => 1,
    ]);

    $ip = '192.0.2.40';

    expect($limiter->allowIssue($ip))
        ->toBeTrue();

    expect($limiter->allowIssue($ip))
        ->toBeFalse();

    expect($limiter->allowVerification($ip))
        ->toBeTrue();

    expect($limiter->allowVerification($ip))
        ->toBeFalse();
});

it('allows new requests after the rate limit window expires', function () {
    $limiter = app(OtpRateLimiter::class);

    config([
        'otp.max_requests_per_ip_per_hour' => 1,
    ]);

    $ip = '192.0.2.50';

    expect($limiter->allowIssue($ip))
        ->toBeTrue();

    \Illuminate\Support\Carbon::setTestNow(now()->addSeconds(3599));

    expect($limiter->allowIssue($ip))
        ->toBeFalse();

    \Illuminate\Support\Carbon::setTestNow(now()->addSecond());

    expect($limiter->allowIssue($ip))
        ->toBeTrue();
});

it('normalizes equivalent ipv6 addresses', function () {
    $limiter = app(OtpRateLimiter::class);

    config([
        'otp.max_requests_per_ip_per_hour' => 1,
    ]);

    expect(
        $limiter->allowIssue('2001:db8::1')
    )->toBeTrue();

    expect(
        $limiter->allowIssue(
            '2001:0db8:0000:0000:0000:0000:0000:0001'
        )
    )->toBeFalse();
});

it('normalizes ipv4 mapped ipv6 addresses', function () {
    $limiter = app(OtpRateLimiter::class);

    config([
        'otp.max_requests_per_ip_per_hour' => 1,
    ]);

    expect(
        $limiter->allowIssue('192.0.2.60')
    )->toBeTrue();

    expect(
        $limiter->allowIssue('::ffff:192.0.2.60')
    )->toBeFalse();
});

it('rejects invalid ip addresses', function () {
    $limiter = app(OtpRateLimiter::class);

    expect(
        fn() => $limiter->allowIssue('invalid-ip')
    )->toThrow(\InvalidArgumentException::class);
});

it('rejects requests when the configured limit is zero', function () {
    $limiter = app(OtpRateLimiter::class);

    config([
        'otp.max_requests_per_ip_per_hour' => 0,
        'otp.max_attempts_per_ip_per_hour' => 0,
    ]);

    $ip = '192.0.2.70';

    expect($limiter->allowIssue($ip))
        ->toBeFalse();

    expect($limiter->allowVerification($ip))
        ->toBeFalse();
});


it('rejects requests when the exclusive lock cannot be acquired', function () {
    $this->travelBack();

    $cryptography = app(OtpCryptography::class);
    $limiter = app(OtpRateLimiter::class);

    $ip = '192.0.2.90';

    $hash = $cryptography->hashIp($ip);

    $key = "otp:rate:issue:{$hash}";

    $lock = Cache::lock("{$key}:lock", 30);

    expect($lock->get())->toBeTrue();

    try {
        expect($limiter->allowIssue($ip))
            ->toBeFalse();

        expect(RateLimiter::attempts($key))
            ->toBe(0);
    } finally {
        $lock->release();
    }
});

it('does not increment the counter after reaching the limit', function () {
    config([
        'otp.max_requests_per_ip_per_hour' => 1,
    ]);

    $cryptography = app(OtpCryptography::class);
    $limiter = app(OtpRateLimiter::class);

    $ip = '192.0.2.80';

    $hash = $cryptography->hashIp($ip);

    $key = "otp:rate:issue:{$hash}";

    expect($limiter->allowIssue($ip))->toBeTrue();

    expect($limiter->allowIssue($ip))->toBeFalse();

    expect(RateLimiter::attempts($key))->toBe(1);
});
