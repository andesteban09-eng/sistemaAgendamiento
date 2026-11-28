<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OtpChallenge extends Model
{
    use HasUuids;

    protected $table = 'otp_challenges';

    public const PURPOSE_EMAIL_VERIFICATION = 'EMAIL_VERIFICATION';
    public const PURPOSE_PASSWORD_RESET = 'PASSWORD_RESET';
    public const PURPOSE_PASSWORD_CHANGE = 'PASSWORD_CHANGE';

    public const CHANNEL_EMAIL = 'EMAIL';

    public const MAX_ATTEMPTS = 5;

    protected $fillable = [
        'user_id',
        'purpose',
        'channel',
        'destination',
        'code_hash',
        'session_hash',
        'attempts',
        'expires_at',
        'verified_at',
        'consumed_at',
        'revoked_at',
    ];

    protected $hidden = [
        'code_hash',
        'session_hash',
        'destination',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'expires_at' => 'immutable_datetime',
            'verified_at' => 'immutable_datetime',
            'consumed_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThanOrEqualTo($this->expires_at);
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function hasAttemptsAvailable(): bool
    {
        return $this->attempts < self::MAX_ATTEMPTS;
    }

    public function canBeVerified(): bool
    {
        return !$this->isExpired()
            && !$this->isVerified()
            && !$this->isConsumed()
            && !$this->isRevoked()
            && $this->hasAttemptsAvailable();
    }

    public function canBeConsumed(): bool
    {
        return $this->isVerified()
            && !$this->isExpired()
            && !$this->isConsumed()
            && !$this->isRevoked();
    }
    public function failedAttempts(): HasMany
    {
        return $this->hasMany(
            OtpAttempt::class,
            'challenge_id'
        );
    }
}
