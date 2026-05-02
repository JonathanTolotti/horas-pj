<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOnCallRequest;
use App\Models\OnCallPeriod;
use App\Services\TimeCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnCallController extends Controller
{
    public function __construct(private TimeCalculatorService $calculator) {}

    public function index(Request $request): JsonResponse
    {
        $month   = $request->query('month', now()->format('Y-m'));
        $periods = OnCallPeriod::forUser($request->user()->id)
            ->forMonth($month)
            ->with('project')
            ->orderBy('start_datetime', 'desc')
            ->get();

        return response()->json(['data' => $periods]);
    }

    public function store(StoreOnCallRequest $request): JsonResponse
    {
        $data   = $request->validated();
        $start  = Carbon::parse($data['start_datetime']);
        $end    = Carbon::parse($data['end_datetime']);
        $total  = round($start->diffInMinutes($end) / 60, 2);

        $period = OnCallPeriod::create([
            'user_id'         => $request->user()->id,
            'project_id'      => $data['project_id'] ?? null,
            'start_datetime'  => $data['start_datetime'],
            'end_datetime'    => $data['end_datetime'],
            'hourly_rate'     => $data['hourly_rate'],
            'total_hours'     => $total,
            'worked_hours'    => 0,
            'on_call_hours'   => $total,
            'month_reference' => substr($data['start_datetime'], 0, 7),
            'description'     => $data['description'] ?? null,
        ]);

        return response()->json(['data' => $period->fresh(['project'])], 201);
    }

    public function update(StoreOnCallRequest $request, int $id): JsonResponse
    {
        $period = OnCallPeriod::forUser($request->user()->id)->findOrFail($id);
        $data   = $request->validated();
        $start  = Carbon::parse($data['start_datetime']);
        $end    = Carbon::parse($data['end_datetime']);
        $total  = round($start->diffInMinutes($end) / 60, 2);

        $period->update(array_merge($data, [
            'total_hours'   => $total,
            'on_call_hours' => max($total - $period->worked_hours, 0),
        ]));

        return response()->json(['data' => $period->fresh(['project'])]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $period = OnCallPeriod::forUser($request->user()->id)->findOrFail($id);
        $period->delete();

        return response()->json(['message' => 'Período de sobreaviso excluído com sucesso.']);
    }

    public function stats(Request $request): JsonResponse
    {
        $user  = $request->user();
        $month = $request->query('month', now()->format('Y-m'));
        $stats = $this->calculator->getOnCallStats($user->id, $month);

        return response()->json(['data' => $stats]);
    }
}
