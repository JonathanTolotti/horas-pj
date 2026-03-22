<?php

namespace App\Services;

use App\Models\Company;
use App\Models\MonthlyAdjustment;
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

    public function getCycleDay(int $userId): int
    {
        return (int) (Setting::forUser($userId)->billing_cycle_day ?? 1);
    }

    public function getTotalHoursForMonth(int $userId, string $monthReference): float
    {
        $sum = TimeEntry::forUser($userId)
            ->forMonth($monthReference, $this->getCycleDay($userId))
            ->sum('hours');

        return round((float) $sum, 2);
    }

    public function getHourlyRate(int $userId, ?string $monthReference = null): float
    {
        if ($monthReference) {
            $adjustment = MonthlyAdjustment::forUser($userId)->forMonth($monthReference)->first();
            if ($adjustment) {
                return (float) $adjustment->hourly_rate;
            }
        }
        return (float) Setting::forUser($userId)->hourly_rate;
    }

    public function getExtraValue(int $userId, ?string $monthReference = null): float
    {
        if ($monthReference) {
            $adjustment = MonthlyAdjustment::forUser($userId)->forMonth($monthReference)->first();
            if ($adjustment) {
                return (float) $adjustment->extra_value;
            }
        }
        return (float) Setting::forUser($userId)->extra_value;
    }

    public function getDiscountValue(int $userId, ?string $monthReference = null): float
    {
        if ($monthReference) {
            $adjustment = MonthlyAdjustment::forUser($userId)->forMonth($monthReference)->first();
            if ($adjustment) {
                return (float) $adjustment->discount_value;
            }
        }
        return (float) (Setting::forUser($userId)->discount_value ?? 0);
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

    public function calculateTotalRevenueFromEntries(int $userId, string $monthReference, ?int $cycleDay = null): float
    {
        $cycleDay ??= $this->getCycleDay($userId);
        $hourlyRate = $this->getHourlyRate($userId, $monthReference);
        $totalHours = (float) TimeEntry::forUser($userId)
            ->forMonth($monthReference, $cycleDay)
            ->sum('hours');

        // Converte para minutos inteiros, igual à lógica de exibição hh:mm
        $totalMinutes = (int) round($totalHours * 60);

        return round(($totalMinutes / 60) * $hourlyRate, 2);
    }

    public function getProjects(int $userId): array
    {
        return Project::forUser($userId)
            ->active()
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function getProjectsStats(int $userId, string $monthReference, ?int $cycleDay = null): array
    {
        $cycleDay ??= $this->getCycleDay($userId);
        $projects = Project::forUser($userId)->active()->get();
        $stats = [];

        foreach ($projects as $project) {
            $hours = TimeEntry::forUser($userId)
                ->forMonth($monthReference, $cycleDay)
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
            ->forMonth($monthReference, $cycleDay)
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

    public function calculateRevenueByCompany(int $userId, string $monthReference, ?int $cycleDay = null): array
    {
        $cycleDay ??= $this->getCycleDay($userId);
        $extraValue = $this->getExtraValue($userId, $monthReference);
        $discountValue = $this->getDiscountValue($userId, $monthReference);

        // Usar o mesmo método de cálculo para consistência
        $hoursRevenue = $this->calculateTotalRevenueFromEntries($userId, $monthReference, $cycleDay);
        $onCallRevenue = $this->getOnCallStats($userId, $monthReference, $cycleDay)['total_on_call_revenue'];
        $totalRevenue = round($hoursRevenue + $extraValue - $discountValue + $onCallRevenue, 2);

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

    public function getUnassignedRevenue(int $userId, string $monthReference, ?int $cycleDay = null): float
    {
        $cycleDay ??= $this->getCycleDay($userId);
        $hoursRevenue = $this->calculateTotalRevenueFromEntries($userId, $monthReference, $cycleDay);
        $extraValue = $this->getExtraValue($userId, $monthReference);
        $discountValue = $this->getDiscountValue($userId, $monthReference);
        $onCallRevenue = $this->getOnCallStats($userId, $monthReference, $cycleDay)['total_on_call_revenue'];
        $totalFinal = round($hoursRevenue + $extraValue - $discountValue + $onCallRevenue, 2);

        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference, $cycleDay);
        $assignedRevenue = array_sum(array_column($companyRevenues, 'revenue'));

        $unassigned = round($totalFinal - $assignedRevenue, 2);

        // Ignorar diferenças de arredondamento muito pequenas
        if (abs($unassigned) <= 0.05) {
            return 0;
        }

        return $unassigned;
    }

    public function getOnCallStats(int $userId, string $monthReference, ?int $cycleDay = null): array
    {
        $cycleDay ??= $this->getCycleDay($userId);
        $periods = OnCallPeriod::forUser($userId)->forMonth($monthReference, $cycleDay)->get();

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
        $cycleDay = $this->getCycleDay($userId);

        $totalHours = $this->getTotalHoursForMonth($userId, $monthReference);
        $hourlyRate = $this->getHourlyRate($userId, $monthReference);
        $extraValue = $this->getExtraValue($userId, $monthReference);
        $discountValue = $this->getDiscountValue($userId, $monthReference);

        // Calcular receita somando cada lançamento individualmente para evitar erros de arredondamento
        $totalRevenue = $this->calculateTotalRevenueFromEntries($userId, $monthReference, $cycleDay);

        $totalWithExtra = round($totalRevenue + $extraValue, 2);
        $totalFinal = round($totalWithExtra - $discountValue, 2);
        $companyRevenues = $this->calculateRevenueByCompany($userId, $monthReference, $cycleDay);
        $unassignedRevenue = $this->getUnassignedRevenue($userId, $monthReference, $cycleDay);

        // Estatísticas de sobreaviso
        $onCallStats = $this->getOnCallStats($userId, $monthReference, $cycleDay);
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

    /**
     * Dado uma data e o dia de início do ciclo, retorna o período (YYYY-MM) ao qual a data pertence.
     * Ex: cycleDay=15, date=2026-03-10 → '2026-02' (período de fev 15 a mar 14)
     *     cycleDay=15, date=2026-03-20 → '2026-03' (período de mar 15 a abr 14)
     */
    public function getPeriodForDate(Carbon $date, int $cycleDay): string
    {
        if ($cycleDay <= 1) {
            return $date->format('Y-m');
        }

        if ($date->day >= $cycleDay) {
            return $date->format('Y-m');
        }

        return $date->copy()->subMonthNoOverflow()->format('Y-m');
    }

    /**
     * Retorna [start, end] Carbon para um período (YYYY-MM) com o ciclo dado.
     */
    public function getPeriodDateRange(string $periodMonth, int $cycleDay): array
    {
        if ($cycleDay <= 1) {
            $start = Carbon::parse($periodMonth . '-01')->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();
        } else {
            $dayStr = str_pad($cycleDay, 2, '0', STR_PAD_LEFT);
            $start = Carbon::parse($periodMonth . '-' . $dayStr)->startOfDay();
            $end = $start->copy()->addMonthNoOverflow()->subDay()->endOfDay();
        }

        return [$start, $end];
    }

    /**
     * Retorna o rótulo legível de um período.
     * Ex: cycleDay=1, '2026-03' → 'Março de 2026'
     *     cycleDay=15, '2026-03' → '15/03 – 14/04/2026'
     */
    public function getPeriodLabel(string $periodMonth, int $cycleDay): string
    {
        if ($cycleDay <= 1) {
            return ucfirst(Carbon::parse($periodMonth . '-01')->isoFormat('MMMM [de] YYYY'));
        }

        [$start, $end] = $this->getPeriodDateRange($periodMonth, $cycleDay);

        return $start->format('d/m') . ' – ' . $end->format('d/m/Y');
    }

    /**
     * Retorna o período atual (YYYY-MM) considerando o ciclo do usuário.
     */
    public function getCurrentPeriod(int $userId): string
    {
        $cycleDay = (int) (Setting::forUser($userId)->billing_cycle_day ?? 1);
        return $this->getPeriodForDate(Carbon::now(), $cycleDay);
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
