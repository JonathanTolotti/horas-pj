<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'color',
        'start_date',
        'end_date',
        'is_active',
        'dismissed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'dismissed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        $today = Carbon::today();

        return $query->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->where(function (Builder $q) use ($today) {
                $q->whereNull('end_date')->orWhereDate('end_date', '>=', $today);
            });
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->active()->where(function (Builder $q) {
            $q->where('type', 'persistent')
              ->orWhere(function (Builder $q2) {
                  $q2->where('type', 'one_time')->whereNull('dismissed_at');
              });
        });
    }
}
