<?php

namespace App\Services;

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

    public function getHourlyRate(): float
    {
        return (float) config('pj.hourly_rate', 150);
    }

    public function getExtraHomeOffice(): float
    {
        return (float) config('pj.extra_home_office', 0);
    }

    public function calculateTotalRevenue(float $totalHours): float
    {
        return round($totalHours * $this->getHourlyRate(), 2);
    }

    public function calculateRevenuePerCnpj(float $totalRevenueWithExtra): float
    {
        return round($totalRevenueWithExtra / 3, 2);
    }

    public function getCnpjs(): array
    {
        return config('pj.cnpjs', []);
    }

    public function getMonthlyStats(int $userId, string $monthReference): array
    {
        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $hourlyRate = $this->getHourlyRate();
        $extraHomeOffice = $this->getExtraHomeOffice();
        $totalRevenue = $this->calculateTotalRevenue($totalHours);
        $totalWithExtra = $totalRevenue + $extraHomeOffice;
        $revenuePerCnpj = $this->calculateRevenuePerCnpj($totalWithExtra);
        $cnpjs = $this->getCnpjs();

        return [
            'total_hours' => $totalHours,
            'hourly_rate' => $hourlyRate,
            'extra_home_office' => $extraHomeOffice,
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
