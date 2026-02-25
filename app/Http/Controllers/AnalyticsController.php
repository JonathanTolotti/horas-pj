<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use App\Services\TimeCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}

    public function index(): View
    {
        $user = auth()->user();

        return view('analytics', [
            'isPremium' => $user->isPremium(),
        ]);
    }

    /**
     * Dados para o gráfico de comparativo mensal (últimos 12 meses)
     */
    public function monthlyComparison(): JsonResponse
    {
        $userId = auth()->id();

        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthRef = $date->format('Y-m');

            $stats = $this->calculator->getMonthlyStats($userId, $monthRef);

            $months->push([
                'month'      => $date->translatedFormat('M/y'),
                'month_full' => $date->translatedFormat('F Y'),
                'hours'      => $stats['total_hours'],
                'revenue'    => $stats['total_final_with_on_call'],
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

        $startDate = now()->subMonths(3)->startOfDay();

        $entries = TimeEntry::forUser($userId)
            ->where('date', '>=', $startDate)
            ->get();

        $weekdays = [
            0 => ['name' => 'Domingo',  'short' => 'Dom', 'hours' => 0, 'count' => 0],
            1 => ['name' => 'Segunda',  'short' => 'Seg', 'hours' => 0, 'count' => 0],
            2 => ['name' => 'Terça',    'short' => 'Ter', 'hours' => 0, 'count' => 0],
            3 => ['name' => 'Quarta',   'short' => 'Qua', 'hours' => 0, 'count' => 0],
            4 => ['name' => 'Quinta',   'short' => 'Qui', 'hours' => 0, 'count' => 0],
            5 => ['name' => 'Sexta',    'short' => 'Sex', 'hours' => 0, 'count' => 0],
            6 => ['name' => 'Sábado',   'short' => 'Sáb', 'hours' => 0, 'count' => 0],
        ];

        foreach ($entries as $entry) {
            $dayOfWeek = $entry->date->dayOfWeek;
            $weekdays[$dayOfWeek]['hours'] += $entry->hours;
            $weekdays[$dayOfWeek]['count']++;
        }

        foreach ($weekdays as $key => $day) {
            $weekdays[$key]['hours']   = round($day['hours'], 2);
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
                'hours'   => round($item->total_hours, 2),
            ];
        })->sortByDesc('hours')->values();

        return response()->json($data);
    }

    /**
     * Dados para tendência de faturamento (últimos 6 meses com previsão)
     */
    public function revenueTrend(): JsonResponse
    {
        $userId = auth()->id();

        $data          = collect();
        $totalRevenue  = 0;
        $monthsWithData = 0;

        for ($i = 5; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $monthRef = $date->format('Y-m');

            $stats   = $this->calculator->getMonthlyStats($userId, $monthRef);
            $hours   = $stats['total_hours'];
            $revenue = $stats['total_final_with_on_call'];

            if ($hours > 0) {
                $monthsWithData++;
                $totalRevenue += $revenue;
            }

            $data->push([
                'month'   => $date->translatedFormat('M/y'),
                'revenue' => $revenue,
                'hours'   => $hours,
                'type'    => 'actual',
            ]);
        }

        $average = $monthsWithData > 0 ? $totalRevenue / $monthsWithData : 0;

        for ($i = 1; $i <= 2; $i++) {
            $date = now()->addMonths($i);
            $data->push([
                'month'   => $date->translatedFormat('M/y'),
                'revenue' => round($average, 2),
                'hours'   => 0,
                'type'    => 'forecast',
            ]);
        }

        return response()->json([
            'data'    => $data,
            'average' => round($average, 2),
        ]);
    }

    /**
     * Projeção de faturamento até o fim do mês (apenas dias úteis)
     */
    public function monthlyProjection(Request $request): JsonResponse
    {
        $userId       = auth()->id();
        $now          = now();
        $currentMonth = $now->format('Y-m');

        $selectedMonth = $request->input('month', $currentMonth);
        if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $selectedMonth = $currentMonth;
        }

        $monthStart = Carbon::parse($selectedMonth . '-01');
        $endOfMonth = $monthStart->copy()->endOfMonth();

        // Define "hoje" para lógica de realizado vs projetado
        if ($selectedMonth < $currentMonth) {
            $today = $endOfMonth->toDateString(); // mês passado: tudo realizado
        } elseif ($selectedMonth === $currentMonth) {
            $today = $now->toDateString();
        } else {
            $today = $monthStart->copy()->subDay()->toDateString(); // mês futuro: tudo projetado
        }

        // Lançamentos do mês selecionado
        $entries = TimeEntry::forUser($userId)
            ->forMonth($selectedMonth)
            ->get();

        // Receita total real do mês (considera extras, descontos, sobreaviso)
        $stats         = $this->calculator->getMonthlyStats($userId, $selectedMonth);
        $actualRevenue = $stats['total_final_with_on_call'];
        $hourlyRate    = $this->calculator->getHourlyRate($userId, $selectedMonth);

        // Agrupa horas por dia útil (seg–sex)
        $dailyHours = [];
        foreach ($entries as $entry) {
            $dateStr   = $entry->date->toDateString();
            $dayOfWeek = $entry->date->dayOfWeek; // 0=Dom, 6=Sáb
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                $dailyHours[$dateStr] = ($dailyHours[$dateStr] ?? 0) + $entry->hours;
            }
        }

        $weekdaysWorked = count($dailyHours);

        // Enumera todos os dias úteis do mês
        $cursor            = $monthStart->copy();
        $allWeekdays       = [];
        $elapsedWeekdays   = 0;
        $remainingWeekdays = 0;

        while ($cursor->lte($endOfMonth)) {
            if ($cursor->isWeekday()) {
                $dateStr = $cursor->toDateString();
                $allWeekdays[] = $dateStr;
                if ($dateStr <= $today) {
                    $elapsedWeekdays++;
                } else {
                    $remainingWeekdays++;
                }
            }
            $cursor->addDay();
        }

        $totalWeekdays = count($allWeekdays);

        // Média diária = receita real / dias úteis com lançamentos
        $dailyAvg       = $weekdaysWorked > 0 ? $actualRevenue / $weekdaysWorked : 0;
        $projectedTotal = $actualRevenue + ($dailyAvg * $remainingWeekdays);

        // Monta dados para o gráfico
        $chartData = [];
        foreach ($allWeekdays as $dateStr) {
            $carbon = Carbon::parse($dateStr);
            $isPast = $dateStr <= $today;
            $hours  = $dailyHours[$dateStr] ?? 0;

            $chartData[] = [
                'date'    => $carbon->format('d/m'),
                'label'   => $carbon->translatedFormat('D d/m'),
                'hours'   => round($hours, 2),
                'revenue' => $isPast
                    ? round($hours * $hourlyRate, 2)
                    : round($dailyAvg, 2),
                'type'    => $isPast ? 'actual' : 'projected',
            ];
        }

        return response()->json([
            'current_revenue'       => round($actualRevenue, 2),
            'current_hours'         => round($stats['total_hours'], 2),
            'weekdays_worked'       => $weekdaysWorked,
            'elapsed_weekdays'      => $elapsedWeekdays,
            'remaining_weekdays'    => $remainingWeekdays,
            'total_weekdays'        => $totalWeekdays,
            'daily_average_revenue' => round($dailyAvg, 2),
            'projected_total'       => round($projectedTotal, 2),
            'is_past_month'         => $selectedMonth < $currentMonth,
            'chart_data'            => $chartData,
        ]);
    }

    /**
     * Resumo geral dos analytics
     */
    public function summary(): JsonResponse
    {
        $userId       = auth()->id();
        $currentMonth = now()->format('Y-m');
        $lastMonth    = now()->subMonth()->format('Y-m');

        $current = $this->calculator->getMonthlyStats($userId, $currentMonth);
        $last    = $this->calculator->getMonthlyStats($userId, $lastMonth);

        $lastHours = $last['total_hours'];
        $variation = $lastHours > 0
            ? round((($current['total_hours'] - $lastHours) / $lastHours) * 100, 1)
            : 0;

        // Total do ano
        $yearStart = now()->startOfYear()->format('Y-m');
        $yearEnd   = now()->format('Y-m');
        $yearHours = TimeEntry::forUser($userId)
            ->where('month_reference', '>=', $yearStart)
            ->where('month_reference', '<=', $yearEnd)
            ->sum('hours');

        // Média diária do mês atual
        $daysWorked = TimeEntry::forUser($userId)
            ->forMonth($currentMonth)
            ->distinct('date')
            ->count('date');

        $dailyAverage = $daysWorked > 0 ? $current['total_hours'] / $daysWorked : 0;

        // Projeto mais trabalhado do mês
        $topProject = TimeEntry::forUser($userId)
            ->forMonth($currentMonth)
            ->select('project_id', DB::raw('SUM(hours) as total_hours'))
            ->groupBy('project_id')
            ->orderByDesc('total_hours')
            ->with('project:id,name')
            ->first();

        // Receita anual (soma dos meses com stats completas)
        $yearRevenue = 0;
        for ($m = 1; $m <= (int) now()->format('m'); $m++) {
            $mRef = now()->format('Y') . '-' . str_pad($m, 2, '0', STR_PAD_LEFT);
            $mStats = $this->calculator->getMonthlyStats($userId, $mRef);
            $yearRevenue += $mStats['total_final_with_on_call'];
        }

        return response()->json([
            'current_month' => [
                'hours'         => $current['total_hours'],
                'revenue'       => $current['total_final_with_on_call'],
                'days_worked'   => $daysWorked,
                'daily_average' => round($dailyAverage, 2),
                'on_call_hours'   => $current['on_call_hours'],
                'on_call_revenue' => $current['on_call_revenue'],
            ],
            'last_month' => [
                'hours'   => $last['total_hours'],
                'revenue' => $last['total_final_with_on_call'],
            ],
            'variation' => $variation,
            'year' => [
                'hours'   => round($yearHours, 2),
                'revenue' => round($yearRevenue, 2),
            ],
            'top_project' => $topProject ? [
                'name'  => $topProject->project?->name ?? 'Sem Projeto',
                'hours' => round($topProject->total_hours, 2),
            ] : null,
        ]);
    }
}
