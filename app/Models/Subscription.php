<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'trial_ends_at',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        if ($this->status === 'trial') {
            return $this->trial_ends_at && $this->trial_ends_at->isFuture();
        }

        return $this->status === 'active' && $this->ends_at && $this->ends_at->isFuture();
    }

    public function isPremium(): bool
    {
        return $this->plan === 'premium' && $this->isActive();
    }

    public function daysRemaining(): int
    {
        if ($this->status === 'trial' && $this->trial_ends_at) {
            return max(0, (int) now()->diffInDays($this->trial_ends_at, false));
        }

        if ($this->ends_at) {
            return max(0, (int) now()->diffInDays($this->ends_at, false));
        }

        return 0;
    }
}
