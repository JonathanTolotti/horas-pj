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

    public function getDiscountValue(int $userId): float
    {
        $settings = Setting::forUser($userId);
        return (float) $settings->discount_value;
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
        $discountValue = $this->getDiscountValue($userId);
        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $totalRevenue = ($totalHours * $hourlyRate) + $extraValue - $discountValue;

        $companies = Company::forUser($userId)->active()->with('projects')->get();
        $companyRevenues = [];
        $totalPercentage = 0;

        // Calcular a soma total de porcentagens de cada empresa (média dos projetos vinculados)
        foreach ($companies as $company) {
            // Pegar a maior porcentagem entre os projetos vinculados
            // Se a empresa está vinculada a múltiplos projetos, usa a soma das porcentagens
            $companyPercentage = 0;

            if ($company->projects->count() > 0) {
                // Soma as porcentagens de todos os projetos vinculados
                foreach ($company->projects as $project) {
                    $companyPercentage += $project->pivot->percentage;
                }
                // Divide pelo número de projetos para ter a média (porcentagem da empresa)
                $companyPercentage = $companyPercentage / $company->projects->count();
            }

            $companyRevenues[$company->id] = [
                'id' => $company->id,
                'name' => $company->name,
                'cnpj' => $company->cnpj,
                'percentage' => $companyPercentage,
                'revenue' => 0,
            ];

            $totalPercentage += $companyPercentage;
        }

        // Distribuir o total baseado nas porcentagens
        if ($totalPercentage > 0) {
            $distributedTotal = 0;
            $lastCompanyId = null;

            foreach ($companyRevenues as $companyId => $data) {
                // Calcula a proporção desta empresa em relação ao total de porcentagens
                $proportion = $data['percentage'] / $totalPercentage;
                $companyRevenue = round($totalRevenue * $proportion, 2);
                $companyRevenues[$companyId]['revenue'] = $companyRevenue;
                $distributedTotal += $companyRevenue;
                $lastCompanyId = $companyId;
            }

            // Ajustar a última empresa para compensar erros de arredondamento
            if ($lastCompanyId !== null) {
                $difference = round($totalRevenue - $distributedTotal, 2);
                if (abs($difference) > 0 && abs($difference) <= 0.03) {
                    $companyRevenues[$lastCompanyId]['revenue'] += $difference;
                }
            }
        }

        // Limpar campo auxiliar
        foreach ($companyRevenues as $companyId => $data) {
            unset($companyRevenues[$companyId]['percentage']);
        }

        return $companyRevenues;
    }

    public function getUnassignedRevenue(int $userId, string $monthReference): float
    {
        $hourlyRate = $this->getHourlyRate($userId);
        $totalRevenue = $this->getTotalHoursForMonth($userId, $monthReference) * $hourlyRate;
        $extraValue = $this->getExtraValue($userId);
        $discountValue = $this->getDiscountValue($userId);
        $totalFinal = $totalRevenue + $extraValue - $discountValue;

        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference);
        $assignedRevenue = array_sum(array_column($companyRevenues, 'revenue'));

        $unassigned = round($totalFinal - $assignedRevenue, 2);

        // Ignorar diferenças de arredondamento muito pequenas
        if (abs($unassigned) <= 0.03) {
            return 0;
        }

        return $unassigned;
    }

    public function getMonthlyStats(int $userId, string $monthReference): array
    {
        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $hourlyRate = $this->getHourlyRate($userId);
        $extraValue = $this->getExtraValue($userId);
        $discountValue = $this->getDiscountValue($userId);
        $totalRevenue = $this->calculateTotalRevenue($totalHours, $hourlyRate);
        $totalWithExtra = $totalRevenue + $extraValue;
        $totalFinal = $totalWithExtra - $discountValue;
        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference);
        $unassignedRevenue = $this->getUnassignedRevenue($userId, $monthReference);

        return [
            'total_hours' => $totalHours,
            'hourly_rate' => $hourlyRate,
            'extra_value' => $extraValue,
            'discount_value' => $discountValue,
            'total_revenue' => $totalRevenue,
            'total_with_extra' => $totalWithExtra,
            'total_final' => $totalFinal,
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
