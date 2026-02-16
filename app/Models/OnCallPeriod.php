<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnCallPeriod extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'start_datetime',
        'end_datetime',
        'hourly_rate',
        'total_hours',
        'worked_hours',
        'on_call_hours',
        'month_reference',
        'description',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'hourly_rate' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'worked_hours' => 'decimal:2',
        'on_call_hours' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, string $monthReference)
    {
        return $query->where('month_reference', $monthReference);
    }

    public function scopeActive($query)
    {
        return $query->where('end_datetime', '>=', now());
    }

    public function calculateTotalHours(): float
    {
        $diffInSeconds = $this->start_datetime->diffInSeconds($this->end_datetime);
        return round($diffInSeconds / 3600, 2);
    }

    public function getWorkedHours(): float
    {
        return (float) $this->timeEntries()->sum('hours');
    }

    public function calculateOnCallHours(): float
    {
        $worked = $this->getWorkedHours();
        return max(0, round($this->total_hours - $worked, 2));
    }

    public function recalculateHours(): void
    {
        $this->worked_hours = $this->getWorkedHours();
        $this->on_call_hours = $this->calculateOnCallHours();
        $this->save();
    }

    public function containsEntry(string $date, string $startTime, string $endTime): bool
    {
        $entryStart = Carbon::parse($date . ' ' . $startTime);
        $entryEnd = Carbon::parse($date . ' ' . $endTime);

        return $entryStart >= $this->start_datetime && $entryEnd <= $this->end_datetime;
    }

    public function getOverlapHours(string $date, string $startTime, string $endTime): float
    {
        $entryStart = Carbon::parse($date . ' ' . $startTime);
        $entryEnd = Carbon::parse($date . ' ' . $endTime);

        // Verificar se há sobreposição
        if ($entryEnd <= $this->start_datetime || $entryStart >= $this->end_datetime) {
            return 0;
        }

        // Calcular início e fim da sobreposição
        $overlapStart = $entryStart->gt($this->start_datetime) ? $entryStart : $this->start_datetime;
        $overlapEnd = $entryEnd->lt($this->end_datetime) ? $entryEnd : $this->end_datetime;

        $diffInSeconds = $overlapStart->diffInSeconds($overlapEnd);
        return round($diffInSeconds / 3600, 2);
    }
}
