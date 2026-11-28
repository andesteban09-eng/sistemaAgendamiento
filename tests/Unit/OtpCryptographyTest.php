
<?php

use App\Services\Security\OtpCryptography;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config([
        'otp.secret' => str_repeat('ab', 32),
    ]);

    $this->cryptography = new OtpCryptography();
});

test('otp contains exactly six numeric digits', function () {
    for ($i = 0; $i < 100; $i++) {
        $code = $this->cryptography->generateCode();

        expect($code)->toMatch('/\A[0-9]{6}\z/');
    }
});

test('otp hash contains 64 hexadecimal characters', function () {
    $hash = $this->cryptography->hashCode(
        'challenge-1',
        '123456'
    );

    expect($hash)->toMatch('/\A[a-f0-9]{64}\z/');
});

test('correct otp can be verified', function () {
    $hash = $this->cryptography->hashCode(
        'challenge-1',
        '123456'
    );

    expect(
        $this->cryptography->verifyCode(
            'challenge-1',
            '123456',
            $hash
        )
    )->toBeTrue();
});

test('incorrect otp is rejected', function () {
    $hash = $this->cryptography->hashCode(
        'challenge-1',
        '123456'
    );

    expect(
        $this->cryptography->verifyCode(
            'challenge-1',
            '654321',
            $hash
        )
    )->toBeFalse();
});

test('same otp produces different hashes for different challenges', function () {
    $first = $this->cryptography->hashCode(
        'challenge-1',
        '123456'
    );

    $second = $this->cryptography->hashCode(
        'challenge-2',
        '123456'
    );

    expect($first)->not->toBe($second);
});

test('otp cannot be verified against another challenge', function () {
    $hash = $this->cryptography->hashCode(
        'challenge-1',
        '123456'
    );

    expect(
        $this->cryptography->verifyCode(
            'challenge-2',
            '123456',
            $hash
        )
    )->toBeFalse();
});

test('otp preserves leading zeros', function () {
    $hash = $this->cryptography->hashCode(
        'challenge-1',
        '000123'
    );

    expect(
        $this->cryptography->verifyCode(
            'challenge-1',
            '000123',
            $hash
        )
    )->toBeTrue();
});

test('invalid otp format is rejected', function () {
    expect(
        $this->cryptography->verifyCode(
            'challenge-1',
            'abc123',
            'invalid-hash'
        )
    )->toBeFalse();
});

test('correct session can be verified', function () {
    $hash = $this->cryptography->hashSession('session-1');

    expect(
        $this->cryptography->verifySession(
            'session-1',
            $hash
        )
    )->toBeTrue();
});

test('different session is rejected', function () {
    $hash = $this->cryptography->hashSession('session-1');

    expect(
        $this->cryptography->verifySession(
            'session-2',
            $hash
        )
    )->toBeFalse();
});

test('missing cryptographic secret is rejected', function () {
    config(['otp.secret' => null]);

    expect(
        fn() => $this->cryptography->hashCode(
            'challenge-1',
            '123456'
        )
    )->toThrow(RuntimeException::class);
});

test('invalid cryptographic secret is rejected', function () {
    config(['otp.secret' => str_repeat('z', 64)]);

    expect(
        fn() => $this->cryptography->hashCode(
            'challenge-1',
            '123456'
        )
    )->toThrow(RuntimeException::class);
});
