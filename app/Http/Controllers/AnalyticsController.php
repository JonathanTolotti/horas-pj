<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $settings = Setting::forUser($user->id);

        return view('analytics', [
            'isPremium' => $user->isPremium(),
            'hourlyRate' => $settings->hourly_rate,
        ]);
    }

    /**
     * Dados para o gráfico de comparativo mensal (últimos 12 meses)
     */
    public function monthlyComparison(): JsonResponse
    {
        $userId = auth()->id();
        $settings = Setting::forUser($userId);

        // Últimos 12 meses
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthRef = $date->format('Y-m');

            $hours = TimeEntry::forUser($userId)
                ->forMonth($monthRef)
                ->sum('hours');

            $revenue = $hours * $settings->hourly_rate;

            $months->push([
                'month' => $date->translatedFormat('M/y'),
                'month_full' => $date->translatedFormat('F Y'),
                'hours' => round($hours, 2),
                'revenue' => round($revenue, 2),
            ]);
        }

        return response()->json($months);
    }

    /**
     * Dados para o gráfico de horas por dia da semana
     */
    public function hoursByWeekday(): JsonResponse
    {
        $userId = auth()->id();

        // Últimos 3 meses para ter dados representativos
        $startDate = now()->subMonths(3)->startOfDay();

        $entries = TimeEntry::forUser($userId)
            ->where('date', '>=', $startDate)
            ->get();

        // Agrupar por dia da semana (0=Dom, 6=Sáb)
        $weekdays = [
            0 => ['name' => 'Domingo', 'short' => 'Dom', 'hours' => 0, 'count' => 0],
            1 => ['name' => 'Segunda', 'short' => 'Seg', 'hours' => 0, 'count' => 0],
            2 => ['name' => 'Terça', 'short' => 'Ter', 'hours' => 0, 'count' => 0],
            3 => ['name' => 'Quarta', 'short' => 'Qua', 'hours' => 0, 'count' => 0],
            4 => ['name' => 'Quinta', 'short' => 'Qui', 'hours' => 0, 'count' => 0],
            5 => ['name' => 'Sexta', 'short' => 'Sex', 'hours' => 0, 'count' => 0],
            6 => ['name' => 'Sábado', 'short' => 'Sáb', 'hours' => 0, 'count' => 0],
        ];

        foreach ($entries as $entry) {
            $dayOfWeek = $entry->date->dayOfWeek;
            $weekdays[$dayOfWeek]['hours'] += $entry->hours;
            $weekdays[$dayOfWeek]['count']++;
        }

        // Calcular média
        foreach ($weekdays as $key => $day) {
            $weekdays[$key]['hours'] = round($day['hours'], 2);
            $weekdays[$key]['average'] = $day['count'] > 0
                ? round($day['hours'] / $day['count'], 2)
                : 0;
        }

        return response()->json(array_values($weekdays));
    }

    /**
     * Dados para o gráfico de horas por projeto
     */
    public function hoursByProject(): JsonResponse
    {
        $userId = auth()->id();
        $currentMonth = now()->format('Y-m');

        $projects = TimeEntry::forUser($userId)
            ->forMonth($currentMonth)
            ->select('project_id', DB::raw('SUM(hours) as total_hours'))
            ->groupBy('project_id')
            ->with('project:id,name')
            ->get();

        $data = $projects->map(function ($item) {
            return [
                'project' => $item->project?->name ?? 'Sem Projeto',
                'hours' => round($item->total_hours, 2),
            ];
        });

        // Ordenar por horas (decrescente)
        $data = $data->sortByDesc('hours')->values();

        return response()->json($data);
    }

    /**
     * Dados para tendência de faturamento (últimos 6 meses com previsão)
     */
    public function revenueTrend(): JsonResponse
    {
        $userId = auth()->id();
        $settings = Setting::forUser($userId);

        $data = collect();
        $totalRevenue = 0;
        $monthsWithData = 0;

        // Últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthRef = $date->format('Y-m');

            $hours = TimeEntry::forUser($userId)
                ->forMonth($monthRef)
                ->sum('hours');

            $revenue = $hours * $settings->hourly_rate;

            if ($hours > 0) {
                $monthsWithData++;
                $totalRevenue += $revenue;
            }

            $data->push([
                'month' => $date->translatedFormat('M/y'),
                'revenue' => round($revenue, 2),
                'hours' => round($hours, 2),
                'type' => 'actual',
            ]);
        }

        // Calcular previsão para os próximos 2 meses
        $average = $monthsWithData > 0 ? $totalRevenue / $monthsWithData : 0;

        for ($i = 1; $i <= 2; $i++) {
            $date = now()->addMonths($i);
            $data->push([
                'month' => $date->translatedFormat('M/y'),
                'revenue' => round($average, 2),
                'hours' => round($average / ($settings->hourly_rate ?: 1), 2),
                'type' => 'forecast',
            ]);
        }

        return response()->json([
            'data' => $data,
            'average' => round($average, 2),
        ]);
    }

    /**
     * Resumo geral dos analytics
     */
    public function summary(): JsonResponse
    {
        $userId = auth()->id();
        $settings = Setting::forUser($userId);
        $currentMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');

        // Horas do mês atual
        $currentMonthHours = TimeEntry::forUser($userId)
            ->forMonth($currentMonth)
            ->sum('hours');

        // Horas do mês anterior
        $lastMonthHours = TimeEntry::forUser($userId)
            ->forMonth($lastMonth)
            ->sum('hours');

        // Variação percentual
        $variation = $lastMonthHours > 0
            ? round((($currentMonthHours - $lastMonthHours) / $lastMonthHours) * 100, 1)
            : 0;

        // Total do ano
        $yearStart = now()->startOfYear()->format('Y-m');
        $yearEnd = now()->format('Y-m');
        $yearHours = TimeEntry::forUser($userId)
            ->where('month_reference', '>=', $yearStart)
            ->where('month_reference', '<=', $yearEnd)
            ->sum('hours');

        // Média diária do mês atual
        $daysWorked = TimeEntry::forUser($userId)
            ->forMonth($currentMonth)
            ->distinct('date')
            ->count('date');

        $dailyAverage = $daysWorked > 0 ? $currentMonthHours / $daysWorked : 0;

        // Projeto mais trabalhado do mês
        $topProject = TimeEntry::forUser($userId)
            ->forMonth($currentMonth)
            ->select('project_id', DB::raw('SUM(hours) as total_hours'))
            ->groupBy('project_id')
            ->orderByDesc('total_hours')
            ->with('project:id,name')
            ->first();

        return response()->json([
            'current_month' => [
                'hours' => round($currentMonthHours, 2),
                'revenue' => round($currentMonthHours * $settings->hourly_rate, 2),
                'days_worked' => $daysWorked,
                'daily_average' => round($dailyAverage, 2),
            ],
            'last_month' => [
                'hours' => round($lastMonthHours, 2),
                'revenue' => round($lastMonthHours * $settings->hourly_rate, 2),
            ],
            'variation' => $variation,
            'year' => [
                'hours' => round($yearHours, 2),
                'revenue' => round($yearHours * $settings->hourly_rate, 2),
            ],
            'top_project' => $topProject ? [
                'name' => $topProject->project?->name ?? 'Sem Projeto',
                'hours' => round($topProject->total_hours, 2),
            ] : null,
        ]);
    }
}
