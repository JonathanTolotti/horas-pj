<?php

namespace App\Services;

use App\Models\Company;
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

    public function getCompanies(int $userId): array
    {
        return Company::forUser($userId)
            ->active()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function calculateRevenueByCompany(int $userId, string $monthReference): array
    {
        $hourlyRate = $this->getHourlyRate($userId);
        $extraValue = $this->getExtraValue($userId);
        $companies = Company::forUser($userId)->active()->with('projects')->get();
        $companyRevenues = [];
        $totalHoursRevenue = 0;

        // Primeira passada: calcular revenue de horas por empresa
        foreach ($companies as $company) {
            $companyRevenue = 0;

            foreach ($company->projects as $project) {
                $projectHours = TimeEntry::forUser($userId)
                    ->forMonth($monthReference)
                    ->where('project_id', $project->id)
                    ->sum('hours');

                $projectRevenue = $projectHours * $hourlyRate;
                $percentage = $project->pivot->percentage / 100;
                $companyRevenue += $projectRevenue * $percentage;
            }

            $totalHoursRevenue += $companyRevenue;

            $companyRevenues[$company->id] = [
                'id' => $company->id,
                'name' => $company->name,
                'cnpj' => $company->cnpj,
                'hours_revenue' => $companyRevenue,
                'revenue' => $companyRevenue,
            ];
        }

        // Segunda passada: distribuir valor extra proporcionalmente
        if ($extraValue > 0 && $totalHoursRevenue > 0) {
            foreach ($companyRevenues as $companyId => $data) {
                $proportion = $data['hours_revenue'] / $totalHoursRevenue;
                $extraShare = $extraValue * $proportion;
                $companyRevenues[$companyId]['revenue'] = round($data['hours_revenue'] + $extraShare, 2);
            }
        }

        // Limpar campo auxiliar
        foreach ($companyRevenues as $companyId => $data) {
            unset($companyRevenues[$companyId]['hours_revenue']);
        }

        return $companyRevenues;
    }

    public function getUnassignedRevenue(int $userId, string $monthReference): float
    {
        $hourlyRate = $this->getHourlyRate($userId);
        $totalRevenue = $this->getTotalHoursForMonth($userId, $monthReference) * $hourlyRate;
        $extraValue = $this->getExtraValue($userId);
        $totalWithExtra = $totalRevenue + $extraValue;

        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference);
        $assignedRevenue = array_sum(array_column($companyRevenues, 'revenue'));

        return round($totalWithExtra - $assignedRevenue, 2);
    }

    public function getMonthlyStats(int $userId, string $monthReference): array
    {
        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $hourlyRate = $this->getHourlyRate($userId);
        $extraValue = $this->getExtraValue($userId);
        $totalRevenue = $this->calculateTotalRevenue($totalHours, $hourlyRate);
        $totalWithExtra = $totalRevenue + $extraValue;
        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference);
        $unassignedRevenue = $this->getUnassignedRevenue($userId, $monthReference);

        return [
            'total_hours' => $totalHours,
            'hourly_rate' => $hourlyRate,
            'extra_value' => $extraValue,
            'total_revenue' => $totalRevenue,
            'total_with_extra' => $totalWithExtra,
            'company_revenues' => $companyRevenues,
            'unassigned_revenue' => $unassignedRevenue,
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
