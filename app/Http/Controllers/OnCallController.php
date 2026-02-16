<?php

namespace App\Http\Controllers;

use App\Models\OnCallPeriod;
use App\Models\Setting;
use App\Models\TimeEntry;
use App\Services\TimeCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnCallController extends Controller
{
    protected TimeCalculatorService $timeCalculator;

    public function __construct(TimeCalculatorService $timeCalculator)
    {
        $this->timeCalculator = $timeCalculator;
    }

    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $monthReference = $request->get('month', now()->format('Y-m'));

        $periods = OnCallPeriod::forUser($userId)
            ->forMonth($monthReference)
            ->with('project')
            ->orderBy('start_datetime', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'periods' => $periods,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'project_id' => 'nullable|exists:projects,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        // Se não informar taxa, usa a configuração padrão
        if (empty($validated['hourly_rate'])) {
            $settings = Setting::forUser($userId);
            $validated['hourly_rate'] = $settings->on_call_hourly_rate ?? ($settings->hourly_rate / 3);
        }

        $startDatetime = Carbon::parse($validated['start_datetime']);
        $endDatetime = Carbon::parse($validated['end_datetime']);

        $totalHours = round($startDatetime->diffInSeconds($endDatetime) / 3600, 2);

        $period = OnCallPeriod::create([
            'user_id' => $userId,
            'project_id' => $validated['project_id'] ?? null,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'hourly_rate' => $validated['hourly_rate'],
            'total_hours' => $totalHours,
            'worked_hours' => 0,
            'on_call_hours' => $totalHours,
            'month_reference' => $startDatetime->format('Y-m'),
            'description' => $validated['description'] ?? null,
        ]);

        // Vincular lançamentos existentes que estão dentro do período
        $this->linkExistingEntries($period);

        return response()->json([
            'success' => true,
            'message' => 'Período de sobreaviso criado com sucesso.',
            'period' => $period->load('project'),
        ]);
    }

    public function update(Request $request, OnCallPeriod $onCall): JsonResponse
    {
        if ($onCall->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'project_id' => 'nullable|exists:projects,id',
            'hourly_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $startDatetime = Carbon::parse($validated['start_datetime']);
        $endDatetime = Carbon::parse($validated['end_datetime']);

        $totalHours = round($startDatetime->diffInSeconds($endDatetime) / 3600, 2);

        // Desvincular lançamentos anteriores
        TimeEntry::where('on_call_period_id', $onCall->id)->update(['on_call_period_id' => null]);

        $onCall->update([
            'project_id' => $validated['project_id'] ?? null,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'hourly_rate' => $validated['hourly_rate'] ?? $onCall->hourly_rate,
            'total_hours' => $totalHours,
            'month_reference' => $startDatetime->format('Y-m'),
            'description' => $validated['description'] ?? null,
        ]);

        // Revincular lançamentos
        $this->linkExistingEntries($onCall);

        return response()->json([
            'success' => true,
            'message' => 'Período de sobreaviso atualizado com sucesso.',
            'period' => $onCall->fresh()->load('project'),
        ]);
    }

    public function destroy(OnCallPeriod $onCall): JsonResponse
    {
        if ($onCall->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        // Desvincular lançamentos
        TimeEntry::where('on_call_period_id', $onCall->id)->update(['on_call_period_id' => null]);

        $onCall->delete();

        return response()->json([
            'success' => true,
            'message' => 'Período de sobreaviso excluído com sucesso.',
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $monthReference = $request->get('month', now()->format('Y-m'));

        $stats = $this->timeCalculator->getOnCallStats($userId, $monthReference);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    protected function linkExistingEntries(OnCallPeriod $period): void
    {
        $userId = $period->user_id;

        // Buscar lançamentos que podem estar dentro do período
        $startDate = $period->start_datetime->format('Y-m-d');
        $endDate = $period->end_datetime->format('Y-m-d');

        // Buscar todos os lançamentos no intervalo de datas (sem filtrar por on_call_period_id)
        $entries = TimeEntry::forUser($userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        foreach ($entries as $entry) {
            // Normalizar o formato de hora (garantir que seja H:i)
            $startTime = substr($entry->start_time, 0, 5);
            $endTime = substr($entry->end_time, 0, 5);

            $overlapHours = $period->getOverlapHours(
                $entry->date->format('Y-m-d'),
                $startTime,
                $endTime
            );

            if ($overlapHours > 0 && $entry->on_call_period_id !== $period->id) {
                $entry->on_call_period_id = $period->id;
                $entry->save();
            }
        }

        $period->recalculateHours();
    }

    public static function linkEntryToOnCallPeriod(TimeEntry $entry): void
    {
        $periods = OnCallPeriod::forUser($entry->user_id)
            ->where('start_datetime', '<=', $entry->date->format('Y-m-d') . ' 23:59:59')
            ->where('end_datetime', '>=', $entry->date->format('Y-m-d') . ' 00:00:00')
            ->get();

        // Normalizar o formato de hora (garantir que seja H:i)
        $startTime = substr($entry->start_time, 0, 5);
        $endTime = substr($entry->end_time, 0, 5);

        foreach ($periods as $period) {
            $overlapHours = $period->getOverlapHours(
                $entry->date->format('Y-m-d'),
                $startTime,
                $endTime
            );

            if ($overlapHours > 0) {
                $entry->on_call_period_id = $period->id;
                $entry->save();
                $period->recalculateHours();
                break;
            }
        }
    }

    public static function unlinkEntryFromOnCallPeriod(TimeEntry $entry): void
    {
        if ($entry->on_call_period_id) {
            $period = OnCallPeriod::find($entry->on_call_period_id);
            $entry->on_call_period_id = null;
            $entry->save();

            if ($period) {
                $period->recalculateHours();
            }
        }
    }

    public function recalculate(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $monthReference = $request->get('month', now()->format('Y-m'));

        $periods = OnCallPeriod::forUser($userId)
            ->forMonth($monthReference)
            ->get();

        foreach ($periods as $period) {
            // Desvincular todos os lançamentos primeiro
            TimeEntry::where('on_call_period_id', $period->id)->update(['on_call_period_id' => null]);

            // Revincular os lançamentos
            $this->linkExistingEntries($period);
        }

        return response()->json([
            'success' => true,
            'message' => 'Períodos de sobreaviso recalculados com sucesso.',
            'periods_count' => $periods->count(),
        ]);
    }
}
