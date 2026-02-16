<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'on_call_period_id',
        'date',
        'start_time',
        'end_time',
        'hours',
        'description',
        'month_reference',
    ];

    protected $casts = [
        'date' => 'date',
        'hours' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function onCallPeriod(): BelongsTo
    {
        return $this->belongsTo(OnCallPeriod::class);
    }

    public function scopeForMonth($query, string $monthReference)
    {
        return $query->where('month_reference', $monthReference);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
