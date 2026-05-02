<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimeEntryRequest;
use App\Models\TimeEntry;
use App\Services\TimeCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeEntryController extends Controller
{
    public function __construct(private TimeCalculatorService $calculator) {}

    public function index(Request $request): JsonResponse
    {
        $user     = $request->user();
        $month    = $request->query('month', now()->format('Y-m'));
        $cycleDay = $this->calculator->getCycleDay($user->id);

        $entries = TimeEntry::forUser($user->id)
            ->forMonth($month, $cycleDay)
            ->with('project', 'onCallPeriod')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(50);

        return response()->json([
            'data' => $entries->items(),
            'meta' => [
                'current_page' => $entries->currentPage(),
                'last_page'    => $entries->lastPage(),
                'per_page'     => $entries->perPage(),
                'total'        => $entries->total(),
                'month'        => $month,
            ],
        ]);
    }

    public function store(StoreTimeEntryRequest $request): JsonResponse
    {
        $user  = $request->user();
        $data  = $request->validated();
        $hours = $this->calculator->calculateHours($data['start_time'], $data['end_time']);

        if ($hours <= 0) {
            return response()->json(['message' => 'O horário de término deve ser posterior ao de início.'], 422);
        }

        $month = $data['month_reference'] ?? substr($data['date'], 0, 7);

        $entry = TimeEntry::create([
            'user_id'         => $user->id,
            'project_id'      => $data['project_id'] ?? null,
            'date'            => $data['date'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'],
            'hours'           => $hours,
            'description'     => $data['description'] ?? null,
            'month_reference' => $month,
        ]);

        return response()->json(['data' => $entry->fresh(['project'])], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $entry = TimeEntry::forUser($request->user()->id)->findOrFail($id);
        $entry->delete();

        return response()->json(['message' => 'Lançamento excluído com sucesso.']);
    }

    public function stats(Request $request): JsonResponse
    {
        $user  = $request->user();
        $month = $request->query('month', now()->format('Y-m'));
        $stats = $this->calculator->getMonthlyStats($user->id, $month);

        return response()->json(['data' => $stats]);
    }
}
