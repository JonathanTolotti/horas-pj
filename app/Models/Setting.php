<?php

namespace App\Models;

use App\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([SettingObserver::class])]
class Setting extends Model
{
    protected $fillable = [
        'user_id',
        'hourly_rate',
        'extra_value',
        'discount_value',
        'on_call_hourly_rate',
        'auto_save_tracking',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'extra_value' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'on_call_hourly_rate' => 'decimal:2',
        'auto_save_tracking' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function forUser(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['hourly_rate' => 150.00, 'extra_value' => 0.00, 'discount_value' => 0.00, 'on_call_hourly_rate' => null, 'auto_save_tracking' => false]
        );
    }
}
