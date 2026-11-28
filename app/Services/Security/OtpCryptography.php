<?php

namespace App\Services\Security;

use RuntimeException;
use InvalidArgumentException;

class OtpCryptography
{
    private function secret(): string
    {
        $secret = config('otp.secret');

        if (
            !is_string($secret)
            || strlen($secret) !== 64
            || !ctype_xdigit($secret)
        ) {
            throw new RuntimeException(
                'La configuración criptográfica OTP no es válida.'
            );
        }

        return hex2bin($secret);
    }

    public function generateCode(): string
    {
        return str_pad(
            (string) random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    public function hashCode(
        string $challengeId,
        string $code
    ): string {
        if (!preg_match('/\A[0-9]{6}\z/', $code)) {
            throw new InvalidArgumentException(
                'El código OTP debe contener seis dígitos.'
            );
        }

        return hash_hmac(
            'sha256',
            'otp:code:' . $challengeId . ':' . $code,
            $this->secret()
        );
    }

    public function hashSession(string $sessionId): string
    {
        if ($sessionId === '') {
            throw new InvalidArgumentException(
                'La sesión no puede estar vacía.'
            );
        }

        return hash_hmac(
            'sha256',
            'otp:session:' . $sessionId,
            $this->secret()
        );
    }

    public function verifyCode(
        string $challengeId,
        string $code,
        string $storedHash
    ): bool {
        if (!preg_match('/\A[0-9]{6}\z/', $code)) {
            return false;
        }

        return hash_equals(
            $storedHash,
            $this->hashCode($challengeId, $code)
        );
    }

    public function verifySession(
        string $sessionId,
        string $storedHash
    ): bool {
        if ($sessionId === '') {
            return false;
        }

        return hash_equals(
            $storedHash,
            $this->hashSession($sessionId)
        );
    }

public function hashIp(string $ip): string
{
    $packedIp = inet_pton($ip);

    if ($packedIp === false) {
        throw new InvalidArgumentException(
            'La dirección IP no es válida.'
        );
    }

    // Normalizar direcciones IPv4 representadas como IPv6.
    $ipv4Prefix = str_repeat("\x00", 10) . "\xff\xff";

    if (
        strlen($packedIp) === 16
        && substr($packedIp, 0, 12) === $ipv4Prefix
    ) {
        $packedIp = substr($packedIp, 12);
    }

    return hash_hmac(
        'sha256',
        'otp:ip:' . bin2hex($packedIp),
        $this->secret()
    );
}

}
