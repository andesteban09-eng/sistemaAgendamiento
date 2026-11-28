<?php

namespace Tests\Support;

use App\Notifications\OtpVerificationNotification;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Assert;
use ReflectionProperty;

final class OtpTestHelper
{
    public static function configure(): void
    {
        config([
            'otp.secret' => str_repeat('ab', 32),
        ]);

        Notification::fake();
    }

    public static function sentCode(): string
    {
        $code = null;

        Notification::assertSentOnDemand(
            OtpVerificationNotification::class,
            function ($notification) use (&$code) {
                $property = new ReflectionProperty(
                    $notification,
                    'code'
                );

                $code = (string) $property->getValue(
                    $notification
                );

                return true;
            }
        );

        Assert::assertIsString($code);

        Assert::assertMatchesRegularExpression(
            '/\A[0-9]{6}\z/',
            $code
        );

        return $code;
    }

    public static function invalidCode(string $validCode): string
    {
        return $validCode === '000000'
            ? '000001'
            : '000000';
    }
}
