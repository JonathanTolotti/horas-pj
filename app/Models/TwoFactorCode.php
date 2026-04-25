<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'attempts',
        'locked_until',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'   => 'datetime',
            'locked_until' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
