<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'hourly_rate',
        'extra_value',
        'discount_value',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'extra_value' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function forUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['hourly_rate' => 150.00, 'extra_value' => 0.00, 'discount_value' => 0.00]
        );
    }
}
