<?php

namespace App\Http\Controllers;

use App\Services\CsvImportService;
use App\Services\TimeCalculatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    protected CsvImportService $importService;
    protected TimeCalculatorService $timeCalculator;

    public function __construct(CsvImportService $importService, TimeCalculatorService $timeCalculator)
    {
        $this->importService = $importService;
        $this->timeCalculator = $timeCalculator;
    }

    /**
     * Preview CSV import
     */
    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());

        // Detect and convert encoding to UTF-8 if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        $userId = auth()->id();
        $result = $this->importService->parse($content, $userId);

        return response()->json([
            'success' => true,
            'preview' => $result,
        ]);
    }

    /**
     * Execute CSV import
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
            'ignore_overlaps' => 'boolean',
        ]);

        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());

        // Detect and convert encoding to UTF-8 if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        $userId = auth()->id();
        $ignoreOverlaps = $request->boolean('ignore_overlaps', false);

        $result = $this->importService->import($content, $userId, $ignoreOverlaps);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Erro ao importar arquivo',
            ], 422);
        }

        // Get updated stats
        $currentMonth = now()->format('Y-m');
        $stats = $this->timeCalculator->getMonthlyStats($userId, $currentMonth);

        return response()->json([
            'success' => true,
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'skipped_entries' => $result['skipped_entries'] ?? [],
            'errors' => $result['errors'] ?? [],
            'stats' => $stats,
        ]);
    }
}
