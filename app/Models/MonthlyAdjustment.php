<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyAdjustment extends Model
{
    protected $fillable = [
        'user_id',
        'month_reference',
        'hourly_rate',
        'extra_value',
        'discount_value',
    ];

    protected $casts = [
        'hourly_rate' => 'float',
        'extra_value' => 'float',
        'discount_value' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, string $monthReference)
    {
        return $query->where('month_reference', $monthReference);
    }
}
