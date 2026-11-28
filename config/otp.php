<?php

return [

    'secret' => env('OTP_SECRET'),

    'length' => 6,

    'expiration_minutes' => 5,

    'max_attempts' => 5,

    'resend_seconds' => 60,

    'max_requests_per_hour' => 5,

    'max_attempts_per_hour' => 15,

    'max_requests_per_ip_per_hour' => 30,

    'max_attempts_per_ip_per_hour' => 60,

    'retention_days' => 30,

];
