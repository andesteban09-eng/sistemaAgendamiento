<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Contracts\Cache\LockTimeoutException;

class OtpRateLimiter
{
    private const WINDOW_SECONDS = 3600;

    private const LOCK_SECONDS = 30;

    private const LOCK_WAIT_SECONDS = 3;

    public function __construct(
        private readonly OtpCryptography $cryptography
    ) {}

    public function allowIssue(string $ip): bool
    {
        return $this->acquire(
            $ip,
            'issue',
            (int) config('otp.max_requests_per_ip_per_hour')
        );
    }

    public function allowVerification(string $ip): bool
    {
        return $this->acquire(
            $ip,
            'verify',
            (int) config('otp.max_attempts_per_ip_per_hour')
        );
    }

    private function acquire(
        string $ip,
        string $operation,
        int $maxAttempts
    ): bool {
        if ($maxAttempts <= 0) {
            return false;
        }

        $ipHash = $this->cryptography->hashIp($ip);

        $key = "otp:rate:{$operation}:{$ipHash}";

        try {
            return Cache::lock(
                "{$key}:lock",
                self::LOCK_SECONDS
            )->block(
                self::LOCK_WAIT_SECONDS,
                function () use ($key, $maxAttempts): bool {
                    if (RateLimiter::tooManyAttempts(
                        $key,
                        $maxAttempts
                    )) {
                        return false;
                    }

                    RateLimiter::hit(
                        $key,
                        self::WINDOW_SECONDS
                    );

                    return true;
                }
            );
        } catch (LockTimeoutException) {
            return false;
        }
    }
}
