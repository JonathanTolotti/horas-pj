<?php

namespace App\Http\Controllers;

use App\Models\ActiveTracking;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    public function status(): JsonResponse
    {
        $tracking = ActiveTracking::getForUser(auth()->id());

        if (!$tracking) {
            return response()->json([
                'active' => false,
            ]);
        }

        return response()->json([
            'active' => true,
            'date' => $tracking->date->format('Y-m-d'),
            'start_time' => substr($tracking->start_time, 0, 5),
            'started_at' => $tracking->started_at->timestamp * 1000,
        ]);
    }

    public function start(): JsonResponse
    {
        $tracking = ActiveTracking::startForUser(auth()->id());

        return response()->json([
            'success' => true,
            'date' => $tracking->date->format('Y-m-d'),
            'start_time' => substr($tracking->start_time, 0, 5),
            'started_at' => $tracking->started_at->timestamp * 1000,
        ]);
    }

    public function stop(): JsonResponse
    {
        $data = ActiveTracking::stopForUser(auth()->id());

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum tracking ativo.',
            ], 404);
        }

        $now = now();

        return response()->json([
            'success' => true,
            'end_time' => $now->format('H:i'),
            'tracking' => $data,
        ]);
    }
}
