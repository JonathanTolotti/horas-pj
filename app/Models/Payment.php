<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'abacatepay_id',
        'amount',
        'months',
        'status',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->status === 'pending' && $this->expires_at->isPast());
    }

    public function markAsExpired(): void
    {
        if ($this->status === 'pending') {
            $this->update(['status' => 'expired']);
        }
    }
}
