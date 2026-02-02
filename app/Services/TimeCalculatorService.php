<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Setting;
use App\Models\TimeEntry;
use Carbon\Carbon;

class TimeCalculatorService
{
    public function calculateHours(string $startTime, string $endTime): float
    {
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        $diffInMinutes = $start->diffInMinutes($end, false);

        return round($diffInMinutes / 60, 2);
    }

    public function getTotalHoursForMonth(int $userId, string $monthReference): float
    {
        return TimeEntry::forUser($userId)
            ->forMonth($monthReference)
            ->sum('hours');
    }

    public function getHourlyRate(int $userId): float
    {
        $settings = Setting::forUser($userId);
        return (float) $settings->hourly_rate;
    }

    public function getExtraValue(int $userId): float
    {
        $settings = Setting::forUser($userId);
        return (float) $settings->extra_value;
    }

    public function calculateTotalRevenue(float $totalHours, float $hourlyRate): float
    {
        return round($totalHours * $hourlyRate, 2);
    }

    public function getProjects(int $userId): array
    {
        return Project::forUser($userId)
            ->active()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getProjectsStats(int $userId, string $monthReference): array
    {
        $projects = Project::forUser($userId)->active()->get();
        $stats = [];

        foreach ($projects as $project) {
            $hours = TimeEntry::forUser($userId)
                ->forMonth($monthReference)
                ->where('project_id', $project->id)
                ->sum('hours');

            $stats[$project->id] = [
                'id' => $project->id,
                'name' => $project->name,
                'hours' => $hours,
            ];
        }

        // Lançamentos sem projeto
        $unassignedHours = TimeEntry::forUser($userId)
            ->forMonth($monthReference)
            ->whereNull('project_id')
            ->sum('hours');

        if ($unassignedHours > 0) {
            $stats['unassigned'] = [
                'id' => null,
                'name' => 'Sem Projeto',
                'hours' => $unassignedHours,
            ];
        }

        return $stats;
    }

    public function getCnpjs(): array
    {
        return config('pj.cnpjs', []);
    }

    public function calculateRevenuePerCnpj(float $totalRevenueWithExtra): float
    {
        return round($totalRevenueWithExtra / 3, 2);
    }

    public function getMonthlyStats(int $userId, string $monthReference): array
    {
        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $hourlyRate = $this->getHourlyRate($userId);
        $extraValue = $this->getExtraValue($userId);
        $totalRevenue = $this->calculateTotalRevenue($totalHours, $hourlyRate);
        $totalWithExtra = $totalRevenue + $extraValue;
        $revenuePerCnpj = $this->calculateRevenuePerCnpj($totalWithExtra);
        $cnpjs = $this->getCnpjs();

        return [
            'total_hours' => $totalHours,
            'hourly_rate' => $hourlyRate,
            'extra_value' => $extraValue,
            'total_revenue' => $totalRevenue,
            'total_with_extra' => $totalWithExtra,
            'revenue_per_cnpj' => $revenuePerCnpj,
            'cnpjs' => $cnpjs,
        ];
    }

    public function hasOverlappingEntry(int $userId, string $date, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $query = TimeEntry::forUser($userId)
            ->where('date', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($sub) use ($startTime, $endTime) {
                    $sub->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
