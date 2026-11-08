<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeEntryRequest;
use App\Models\Company;
use App\Models\Notice;
use App\Models\OnCallPeriod;
use App\Models\Project;
use App\Models\Setting;
use App\Models\SupervisorInvitation;
use App\Models\TimeEntry;
use App\Services\TimeCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimeEntryController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}

    public function index(Request $request): View
    {
        $defaultPeriod = $this->calculator->getCurrentPeriod(auth()->id());
        $monthReference = $request->session()->get('month_reference', $defaultPeriod);

        if ($request->has('month')) {
            $monthReference = $request->input('month');
            $request->session()->put('month_reference', $monthReference);
        }

        $cycleDay = $this->calculator->getCycleDay(auth()->id());

        // Query base para o mês
        $baseQuery = TimeEntry::forUser(auth()->id())
            ->forMonth($monthReference, $cycleDay)
            ->with('project')
            ->withCount('taskNotes')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        // Paginado para visualização por batidas
        $entries = (clone $baseQuery)->paginate(10);

        // Todos os registros para visualização por dia (sem paginação)
        $allEntries = (clone $baseQuery)->get();

        $stats = $this->calculator->getMonthlyStats(auth()->id(), $monthReference);
        $projects = Project::forUser(auth()->id())->active()->orderBy('name')->get();
        $companies = Company::forUser(auth()->id())->active()->orderBy('name')->get();
        $defaultProject = Project::getDefault(auth()->id());

        $user = auth()->user();
        $canViewByDay = $user->canUseFeature('view_by_day');
        $canUseOnCall = $user->canUseFeature('on_call');
        $entriesByDay = $canViewByDay ? $this->groupEntriesByDay($allEntries, $stats['hourly_rate']) : [];

        // Buscar períodos de sobreaviso
        $onCallPeriods = OnCallPeriod::forUser(auth()->id())
            ->forMonth($monthReference, $cycleDay)
            ->with('project')
            ->orderBy('start_datetime', 'desc')
            ->get();

        // Buscar avisos ativos
        $activeNotices = Notice::forUser(auth()->id())
            ->visible()
            ->orderBy('start_date')
            ->get();

        // Convites de supervisão pendentes
        $pendingInvitationsCount = 0;
        if ($user->isPremium()) {
            $pendingInvitationsCount = SupervisorInvitation::where('supervisor_id', auth()->id())
                ->where('status', 'pending')
                ->count();
        }

        $userSettings = Setting::forUser($user->id);

        return view('dashboard', [
            'entries' => $entries,
            'entriesByDay' => $entriesByDay,
            'stats' => $stats,
            'currentMonth' => $monthReference,
            'months' => $this->getAvailableMonths(),
            'projects' => $projects,
            'companies' => $companies,
            'defaultProjectId' => $defaultProject?->id,
            'canViewByDay' => $canViewByDay,
            'canUseOnCall' => $canUseOnCall,
            'isPremium' => $user->isPremium(),
            'subscriptionAlert' => $user->getSubscriptionAlert(),
            'onCallPeriods' => $onCallPeriods,
            'activeNotices' => $activeNotices,
            'pendingInvitationsCount' => $pendingInvitationsCount,
            'standardDayPeriods' => $userSettings->standard_day_periods ?? [],
            'standardDayProjectId' => $userSettings->standard_day_project_id,
            'standardDayDescription' => $userSettings->standard_day_description ?? '',
        ]);
    }

    /**
     * Painel novo (Beta): mesma base de dados do dashboard clássico,
     * organizado como uma folha de ponto do ciclo.
     */
    public function beta(Request $request): View
    {
        $userId = auth()->id();
        $user = auth()->user();
        $currentPeriod = $this->calculator->getCurrentPeriod($userId);
        $monthReference = $request->session()->get('month_reference', $currentPeriod);

        $months = $this->getAvailableMonths();
        $allowedMonths = array_column($months, 'value');

        if ($request->has('month') && in_array($request->input('month'), $allowedMonths, true)) {
            $monthReference = $request->input('month');
            $request->session()->put('month_reference', $monthReference);
        }

        $cycleDay = $this->calculator->getCycleDay($userId);
        [$cycleStart, $cycleEnd] = $this->calculator->getPeriodDateRange($monthReference, $cycleDay);

        $entries = TimeEntry::forUser($userId)
            ->forMonth($monthReference, $cycleDay)
            ->with('project')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $stats = $this->calculator->getMonthlyStats($userId, $monthReference);
        $settings = Setting::forUser($userId);
        $today = Carbon::today();

        // Horas por dia do ciclo (faixa de dias do topo)
        $hoursByDate = $entries->groupBy(fn ($e) => $e->date->format('Y-m-d'))
            ->map(fn ($day) => round((float) $day->sum('hours'), 2));

        $canUseOnCall = $user->canUseFeature('on_call');
        $onCallPeriods = OnCallPeriod::forUser($userId)
            ->forMonth($monthReference, $cycleDay)
            ->with('project')
            ->orderBy('start_datetime', 'desc')
            ->get();

        // Projeção do ciclo (mesma regra do Analytics, feature premium 'analytics')
        $canProject = $user->canUseFeature('analytics');
        $projection = $canProject ? $this->calculator->getCycleProjection($userId, $monthReference) : null;
        $projectedDates = $projection ? array_flip($projection['remaining_dates']) : [];

        $days = [];
        for ($d = $cycleStart->copy(); $d->lte($cycleEnd); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $dayStart = $d->copy()->startOfDay();
            $dayEnd = $d->copy()->endOfDay();
            $days[] = [
                'projected' => isset($projectedDates[$key]),
                'on_call' => $onCallPeriods->contains(
                    fn ($p) => $p->start_datetime->lte($dayEnd) && $p->end_datetime->gt($dayStart)
                ),
                'key' => $key,
                'day' => $d->day,
                'weekday' => $d->isoFormat('ddd'),
                'label' => ucfirst($d->isoFormat('dddd, DD/MM')),
                'hours' => $hoursByDate[$key] ?? 0,
                'weekend' => $d->isWeekend(),
                'today' => $d->isSameDay($today),
                'future' => $d->gt($today),
            ];
        }

        $isCurrentCycle = $today->between($cycleStart, $cycleEnd);
        $workdaysLeft = 0;
        if ($isCurrentCycle) {
            for ($d = $today->copy()->addDay(); $d->lte($cycleEnd); $d->addDay()) {
                if (! $d->isWeekend()) {
                    $workdaysLeft++;
                }
            }
        }

        $currentLabel = collect($months)->firstWhere('value', $monthReference)['label']
            ?? $this->calculator->getPeriodLabel($monthReference, $cycleDay);

        return view('dashboard-beta', [
            'entriesByDay' => $entries->groupBy(fn ($e) => $e->date->format('Y-m-d')),
            'entriesCount' => $entries->count(),
            'days' => $days,
            'daysWorked' => $hoursByDate->filter(fn ($h) => $h > 0)->count(),
            'stats' => $stats,
            'currentMonth' => $monthReference,
            'currentLabel' => $currentLabel,
            'months' => $months,
            'cycleStart' => $cycleStart,
            'cycleEnd' => $cycleEnd,
            'isCurrentCycle' => $isCurrentCycle,
            'workdaysLeft' => $workdaysLeft,
            'projects' => Project::forUser($userId)->active()->orderBy('name')->get(),
            'defaultProjectId' => Project::getDefault($userId)?->id,
            'isPremium' => $user->isPremium(),
            'canUseOnCall' => $canUseOnCall,
            'canProject' => $canProject,
            'projection' => $projection,
            'todayHours' => (float) TimeEntry::forUser($userId)->whereDate('date', $today)->sum('hours'),
            'autoSaveTracking' => (bool) $settings->auto_save_tracking,
            'onCallPeriods' => $onCallPeriods,
            'standardDayPeriods' => $settings->standard_day_periods ?? [],
            'standardDayProjectId' => $settings->standard_day_project_id,
            'standardDayDescription' => $settings->standard_day_description ?? '',
        ]);
    }

    public function store(StoreTimeEntryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $date = Carbon::parse($validated['date']);

        if ($this->calculator->hasOverlappingEntry(
            auth()->id(),
            $validated['date'],
            $validated['start_time'],
            $validated['end_time']
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Já existe um lançamento neste horário.',
            ], 422);
        }

        $hours = $this->calculator->calculateHours(
            $validated['start_time'],
            $validated['end_time']
        );

        $cycleDay = (int) (\App\Models\Setting::forUser(auth()->id())->billing_cycle_day ?? 1);
        $monthRef = $this->calculator->getPeriodForDate($date, $cycleDay);

        $entry = TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'] ?? null,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hours' => $hours,
            'description' => $validated['description'],
            'month_reference' => $monthRef,
        ]);

        // Vinculo com sobreaviso e feito automaticamente pelo TimeEntryObserver

        $entry->refresh();
        $entry->load('project');

        $stats = $this->calculator->getMonthlyStats(
            auth()->id(),
            $monthRef
        );

        return response()->json([
            'success' => true,
            'entry' => [
                'id' => $entry->id,
                'date' => $entry->date->format('Y-m-d'),
                'date_formatted' => $entry->date->format('d/m/Y'),
                'start_time' => substr($entry->start_time, 0, 5),
                'end_time' => substr($entry->end_time, 0, 5),
                'hours' => $entry->hours,
                'description' => $entry->description,
                'project_id' => $entry->project_id,
                'project_name' => $entry->project?->name,
            ],
            'stats' => $stats,
        ]);
    }

    public function destroy(TimeEntry $timeEntry): JsonResponse
    {
        if ($timeEntry->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado.',
            ], 403);
        }

        $monthReference = $timeEntry->month_reference;

        // Desvinculo com sobreaviso e feito automaticamente pelo TimeEntryObserver
        $timeEntry->delete();

        $stats = $this->calculator->getMonthlyStats(auth()->id(), $monthReference);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $monthReference = $request->input('month', Carbon::now()->format('Y-m'));
        $stats = $this->calculator->getMonthlyStats(auth()->id(), $monthReference);

        return response()->json($stats);
    }

    protected function getAvailableMonths(): array
    {
        $months = [];
        $limit = auth()->user()->getLimit('history_months') ?? 12;
        $cycleDay = (int) (\App\Models\Setting::forUser(auth()->id())->billing_cycle_day ?? 1);
        $currentPeriod = $this->calculator->getCurrentPeriod(auth()->id());

        for ($i = 0; $i < $limit; $i++) {
            $periodDate = Carbon::parse($currentPeriod . '-01')->subMonths($i);
            $periodValue = $periodDate->format('Y-m');
            $months[] = [
                'value' => $periodValue,
                'label' => $this->calculator->getPeriodLabel($periodValue, $cycleDay),
            ];
        }

        return $months;
    }

    protected function groupEntriesByDay($entries, float $hourlyRate): array
    {
        return $entries->groupBy(fn($e) => $e->date->format('Y-m-d'))
            ->map(function ($dayEntries) use ($hourlyRate) {
                $totalHours = round((float) $dayEntries->sum('hours'), 2);
                $totalMinutes = (int) round($totalHours * 60);
                $totalValue = round(($totalMinutes / 60) * $hourlyRate, 2);

                return [
                    'date' => $dayEntries->first()->date,
                    'entries' => $dayEntries,
                    'total_hours' => $totalHours,
                    'total_value' => $totalValue,
                    'entries_count' => $dayEntries->count(),
                ];
            })
            ->toArray();
    }
}
