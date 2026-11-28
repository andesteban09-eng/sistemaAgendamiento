<?php

use App\Services\Security\OtpRateLimiter;
use App\Services\Security\OtpCryptography;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\RateLimiter;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

if (! $app->environment('local')) {
    exit("Este diagnostico requiere el entorno local.\n");
}

if (
    config('database.default') !== 'oracle'
    || config('cache.default') !== 'database'
    || ! in_array(config('cache.limiter'), [null, 'database'], true)
) {
    exit("Infraestructura de diagnostico incorrecta.\n");
}

$mode = $argv[1] ?? '';
$ip = $argv[2] ?? '';

if (
    ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
    || ! str_starts_with($ip, '192.0.2.')
) {
    exit("IP de diagnostico no valida.\n");
}

config([
    'otp.max_requests_per_ip_per_hour' => 5,
]);

$cryptography = app(OtpCryptography::class);

$hash = $cryptography->hashIp($ip);

$key = "otp:rate:issue:{$hash}";

try {
    switch ($mode) {
        case 'worker':
            $startAt = (float) ($argv[3] ?? 0);

            if ($startAt <= 0) {
                throw new InvalidArgumentException(
                    'Instante de inicio no valido.'
                );
            }

            while (microtime(true) < $startAt) {
                usleep(10000);
            }

            $limiter = app(OtpRateLimiter::class);

            $allowed = $limiter->allowIssue($ip);

            echo $allowed
                ? "ALLOWED\n"
                : "DENIED\n";

            break;

        case 'status':
            echo 'COUNTER='
                . RateLimiter::attempts($key)
                . PHP_EOL;

            break;

        case 'clear':
            RateLimiter::clear($key);

            echo "CLEARED\n";

            break;

        default:
            throw new InvalidArgumentException(
                'Modo de diagnostico no valido.'
            );
    }
} catch (Throwable $exception) {
    fwrite(
        STDERR,
        'ERROR: ' . get_class($exception) . PHP_EOL
    );

    exit(1);
}
