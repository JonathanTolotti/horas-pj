<?php

namespace App\Services;

use App\Models\Company;
use App\Models\OnCallPeriod;
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
        $sum = TimeEntry::forUser($userId)
            ->forMonth($monthReference)
            ->sum('hours');

        return round((float) $sum, 2);
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

    public function getOnCallHourlyRate(int $userId): ?float
    {
        $settings = Setting::forUser($userId);
        return $settings->on_call_hourly_rate ? (float) $settings->on_call_hourly_rate : null;
    }

    public function calculateTotalRevenue(float $totalHours, float $hourlyRate): float
    {
        return round($totalHours * $hourlyRate, 2);
    }

    public function calculateTotalRevenueFromEntries(int $userId, string $monthReference): float
    {
        $hourlyRate = $this->getHourlyRate($userId);
        $entries = TimeEntry::forUser($userId)->forMonth($monthReference)->get();

        $total = 0;
        foreach ($entries as $entry) {
            $total += round((float) $entry->hours * $hourlyRate, 2);
        }

        return $total;
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
        $extraValue = $this->getExtraValue($userId);
        $discountValue = $this->getDiscountValue($userId);

        // Usar o mesmo método de cálculo para consistência
        $hoursRevenue = $this->calculateTotalRevenueFromEntries($userId, $monthReference);
        $totalRevenue = round($hoursRevenue + $extraValue - $discountValue, 2);

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
        $hoursRevenue = $this->calculateTotalRevenueFromEntries($userId, $monthReference);
        $extraValue = $this->getExtraValue($userId);
        $discountValue = $this->getDiscountValue($userId);
        $totalFinal = round($hoursRevenue + $extraValue - $discountValue, 2);

        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference);
        $assignedRevenue = array_sum(array_column($companyRevenues, 'revenue'));

        $unassigned = round($totalFinal - $assignedRevenue, 2);

        // Ignorar diferenças de arredondamento muito pequenas
        if (abs($unassigned) <= 0.05) {
            return 0;
        }

        return $unassigned;
    }

    public function getOnCallStats(int $userId, string $monthReference): array
    {
        $periods = OnCallPeriod::forUser($userId)->forMonth($monthReference)->get();

        $totalOnCallHours = 0;
        $totalOnCallRevenue = 0;

        foreach ($periods as $period) {
            $totalOnCallHours += (float) $period->on_call_hours;
            $totalOnCallRevenue += (float) $period->on_call_hours * (float) $period->hourly_rate;
        }

        return [
            'total_on_call_hours' => round($totalOnCallHours, 2),
            'total_on_call_revenue' => round($totalOnCallRevenue, 2),
            'periods_count' => $periods->count(),
        ];
    }

    public function getMonthlyStats(int $userId, string $monthReference): array
    {
        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $hourlyRate = $this->getHourlyRate($userId);
        $extraValue = $this->getExtraValue($userId);
        $discountValue = $this->getDiscountValue($userId);

        // Calcular receita somando cada lançamento individualmente para evitar erros de arredondamento
        $totalRevenue = $this->calculateTotalRevenueFromEntries($userId, $monthReference);

        $totalWithExtra = round($totalRevenue + $extraValue, 2);
        $totalFinal = round($totalWithExtra - $discountValue, 2);
        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference);
        $unassignedRevenue = $this->getUnassignedRevenue($userId, $monthReference);

        // Estatísticas de sobreaviso
        $onCallStats = $this->getOnCallStats($userId, $monthReference);
        $onCallHours = $onCallStats['total_on_call_hours'];
        $onCallRevenue = $onCallStats['total_on_call_revenue'];

        // Total final incluindo sobreaviso
        $totalFinalWithOnCall = round($totalFinal + $onCallRevenue, 2);

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
            'on_call_hours' => $onCallHours,
            'on_call_revenue' => $onCallRevenue,
            'total_final_with_on_call' => $totalFinalWithOnCall,
        ];
    }

    public function hasOverlappingEntry(int $userId, string $date, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        // Usar Carbon para garantir formato consistente entre SQLite e MySQL
        $parsedDate = Carbon::parse($date);
        $startOfDay = $parsedDate->copy()->startOfDay();
        $endOfDay = $parsedDate->copy()->endOfDay();

        $query = TimeEntry::forUser($userId)
            ->whereBetween('date', [$startOfDay, $endOfDay])
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
