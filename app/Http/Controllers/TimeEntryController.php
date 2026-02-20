<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTimeEntryRequest;
use App\Models\Company;
use App\Models\Notice;
use App\Models\OnCallPeriod;
use App\Models\Project;
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
        $monthReference = $request->session()->get(
            'month_reference',
            Carbon::now()->format('Y-m')
        );

        if ($request->has('month')) {
            $monthReference = $request->input('month');
            $request->session()->put('month_reference', $monthReference);
        }

        // Query base para o mês
        $baseQuery = TimeEntry::forUser(auth()->id())
            ->forMonth($monthReference)
            ->with('project')
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
            ->forMonth($monthReference)
            ->with('project')
            ->orderBy('start_datetime', 'desc')
            ->get();

        // Buscar avisos ativos
        $activeNotices = Notice::forUser(auth()->id())
            ->visible()
            ->orderBy('start_date')
            ->get();

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

        $entry = TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'] ?? null,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'hours' => $hours,
            'description' => $validated['description'],
            'month_reference' => $date->format('Y-m'),
        ]);

        // Vinculo com sobreaviso e feito automaticamente pelo TimeEntryObserver

        $entry->refresh();
        $entry->load('project');

        $stats = $this->calculator->getMonthlyStats(
            auth()->id(),
            $date->format('Y-m')
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
        $current = Carbon::now();

        // Limite de histórico baseado no plano
        $limit = auth()->user()->getLimit('history_months') ?? 12;

        for ($i = 0; $i < $limit; $i++) {
            $date = $current->copy()->subMonths($i);
            $months[] = [
                'value' => $date->format('Y-m'),
                'label' => ucfirst($date->isoFormat('MMMM Y')),
            ];
        }

        return $months;
    }

    protected function groupEntriesByDay($entries, float $hourlyRate): array
    {
        return $entries->groupBy(fn($e) => $e->date->format('Y-m-d'))
            ->map(function ($dayEntries) use ($hourlyRate) {
                // Somar o valor individual de cada lançamento para evitar erros de arredondamento
                $totalValue = $dayEntries->sum(function ($entry) use ($hourlyRate) {
                    return round((float) $entry->hours * $hourlyRate, 2);
                });
                $totalHours = round((float) $dayEntries->sum('hours'), 2);

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
