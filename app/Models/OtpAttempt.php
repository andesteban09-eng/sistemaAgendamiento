<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpAttempt extends Model
{
    use HasUuids;

    protected $table = 'otp_attempts';

    public $timestamps = false;

    protected $fillable = [
        'challenge_id',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'immutable_datetime',
        ];
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(
            OtpChallenge::class,
            'challenge_id'
        );
    }
}
