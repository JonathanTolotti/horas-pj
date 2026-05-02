<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ActiveTracking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $tracking = ActiveTracking::where('user_id', $request->user()->id)->first();

        return response()->json([
            'data' => [
                'active'     => (bool) $tracking,
                'started_at' => $tracking?->started_at,
            ],
        ]);
    }

    public function start(Request $request): JsonResponse
    {
        $userId   = $request->user()->id;
        $existing = ActiveTracking::where('user_id', $userId)->first();

        if ($existing) {
            return response()->json(['message' => 'Tracking já está em andamento.'], 409);
        }

        $tracking = ActiveTracking::create([
            'user_id'    => $userId,
            'started_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'active'     => true,
                'started_at' => $tracking->started_at,
            ],
        ], 201);
    }

    public function stop(Request $request): JsonResponse
    {
        $userId   = $request->user()->id;
        $tracking = ActiveTracking::where('user_id', $userId)->first();

        if (!$tracking) {
            return response()->json(['message' => 'Nenhum tracking ativo.'], 404);
        }

        $startedAt   = $tracking->started_at;
        $now         = now();
        $diffSeconds = $startedAt->diffInSeconds($now);
        $hours       = max(round($diffSeconds / 3600, 4), 1 / 60);

        $tracking->delete();

        return response()->json([
            'data' => [
                'started_at' => $startedAt,
                'stopped_at' => $now,
                'hours'      => $hours,
            ],
        ]);
    }
}
