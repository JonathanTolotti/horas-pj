<?php

namespace App\Http\Controllers;

use App\Models\ActiveTracking;
use App\Models\Project;
use App\Models\Setting;
use App\Models\TimeEntry;
use App\Services\TimeCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}
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

    public function stop(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $tracking = ActiveTracking::getForUser($userId);

        if (!$tracking) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum tracking ativo.',
            ], 404);
        }

        $now = now();
        $endTime = $now->format('H:i');

        // Verificar se auto_save_tracking está ativo
        $settings = Setting::where('user_id', $userId)->first();
        $autoSave = $settings?->auto_save_tracking ?? false;

        $data = [
            'date' => $tracking->date->format('Y-m-d'),
            'start_time' => substr($tracking->start_time, 0, 5),
            'started_at' => $tracking->started_at->timestamp * 1000,
        ];

        // Pegar projeto padrão
        $defaultProject = Project::getDefault($userId);

        $response = [
            'success' => true,
            'end_time' => $endTime,
            'tracking' => $data,
            'auto_saved' => false,
        ];

        if ($autoSave) {
            $projectId = $defaultProject?->id;
            $description = $defaultProject?->default_description ?? 'Trabalho';

            // Calcular horas usando o timestamp preciso (started_at)
            $startTimeParsed = $tracking->started_at;
            $endDateTime = $now;

            // Usar segundos e converter para horas (valor absoluto para evitar negativos)
            $diffSeconds = $startTimeParsed->diffInSeconds($endDateTime);
            $hours = $diffSeconds / 3600;

            // Mínimo de 1 minuto para evitar lançamentos de 0 horas
            if ($diffSeconds > 0 && $hours < (1/60)) {
                $hours = 1/60; // 1 minuto
            }

            if ($hours > 0) {
                // Criar lançamento
                $entry = TimeEntry::create([
                    'user_id' => $userId,
                    'project_id' => $projectId,
                    'date' => $tracking->date,
                    'start_time' => $tracking->start_time,
                    'end_time' => $endTime,
                    'hours' => $hours,
                    'description' => $description,
                    'month_reference' => $tracking->date->format('Y-m'),
                ]);

                // Calcular estatísticas atualizadas
                $monthReference = $tracking->date->format('Y-m');
                $stats = $this->calculator->getMonthlyStats($userId, $monthReference);

                $response['auto_saved'] = true;
                $response['entry'] = [
                    'id' => $entry->id,
                    'date' => $entry->date->format('Y-m-d'),
                    'date_formatted' => $entry->date->format('d/m/Y'),
                    'start_time' => substr($entry->start_time, 0, 5),
                    'end_time' => substr($entry->end_time, 0, 5),
                    'hours' => $entry->hours,
                    'description' => $entry->description,
                    'project_id' => $entry->project_id,
                    'project_name' => $defaultProject?->name,
                ];
                $response['stats'] = $stats;
            }
        }

        // Deletar tracking
        $tracking->delete();

        return response()->json($response);
    }
}
