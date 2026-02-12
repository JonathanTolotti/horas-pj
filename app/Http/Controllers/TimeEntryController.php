<?php

namespace App\Http\Controllers;

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
        $defaultProject = Project::getDefault(auth()->id());

        $user = auth()->user();
        $canViewByDay = $user->canUseFeature('view_by_day');
        $entriesByDay = $canViewByDay ? $this->groupEntriesByDay($allEntries, $stats['hourly_rate']) : [];

        return view('dashboard', [
            'entries' => $entries,
            'entriesByDay' => $entriesByDay,
            'stats' => $stats,
            'currentMonth' => $monthReference,
            'months' => $this->getAvailableMonths(),
            'projects' => $projects,
            'defaultProjectId' => $defaultProject?->id,
            'canViewByDay' => $canViewByDay,
            'isPremium' => $user->isPremium(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'required|string|max:1000',
            'project_id' => 'nullable|exists:projects,id',
        ]);

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
                $totalHours = $dayEntries->sum('hours');

                return [
                    'date' => $dayEntries->first()->date,
                    'entries' => $dayEntries,
                    'total_hours' => $totalHours,
                    'total_value' => $totalHours * $hourlyRate,
                    'entries_count' => $dayEntries->count(),
                ];
            })
            ->toArray();
    }
}
