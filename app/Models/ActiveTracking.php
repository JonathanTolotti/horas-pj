<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActiveTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'started_at',
    ];

    protected $casts = [
        'date' => 'date',
        'started_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getForUser(int $userId): ?self
    {
        return static::where('user_id', $userId)->first();
    }

    public static function startForUser(int $userId): self
    {
        static::where('user_id', $userId)->delete();

        $now = now();

        return static::create([
            'user_id' => $userId,
            'date' => $now->toDateString(),
            'start_time' => $now->format('H:i'),
            'started_at' => $now,
        ]);
    }

    public static function stopForUser(int $userId): ?array
    {
        $tracking = static::where('user_id', $userId)->first();

        if (!$tracking) {
            return null;
        }

        $data = [
            'date' => $tracking->date->format('Y-m-d'),
            'start_time' => substr($tracking->start_time, 0, 5),
            'started_at' => $tracking->started_at->timestamp * 1000,
        ];

        $tracking->delete();

        return $data;
    }
}
