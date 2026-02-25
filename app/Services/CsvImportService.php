<?php

namespace App\Services;

use App\Models\Project;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CsvImportService
{
    protected TimeCalculatorService $timeCalculator;

    public function __construct(TimeCalculatorService $timeCalculator)
    {
        $this->timeCalculator = $timeCalculator;
    }

    /**
     * Parse CSV content and return preview data
     */
    public function parse(string $content, int $userId): array
    {
        $lines = $this->parseLines($content);
        $validEntries = [];
        $errors = [];
        $projects = $this->getProjectsMap($userId);

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 2; // +2 because index starts at 0 and we skip header

            $result = $this->validateLine($line, $lineNumber, $userId, $projects);

            if ($result['valid']) {
                $validEntries[] = $result['entry'];
            } else {
                $errors[] = $result['error'];
            }
        }

        return [
            'valid_entries' => $validEntries,
            'errors' => $errors,
            'total_lines' => count($lines),
            'valid_count' => count($validEntries),
            'error_count' => count($errors),
        ];
    }

    /**
     * Import entries from CSV content
     */
    public function import(string $content, int $userId, bool $ignoreOverlaps = false): array
    {
        $parseResult = $this->parse($content, $userId);
        $imported = 0;
        $skipped = 0;
        $skippedEntries = [];

        DB::beginTransaction();

        try {
            foreach ($parseResult['valid_entries'] as $entry) {
                // Check for overlaps unless ignoring
                if (!$ignoreOverlaps) {
                    $hasOverlap = $this->timeCalculator->hasOverlappingEntry(
                        $userId,
                        $entry['date'],
                        $entry['start_time'],
                        $entry['end_time']
                    );

                    if ($hasOverlap) {
                        $skipped++;
                        $skippedEntries[] = [
                            'date' => $entry['date_formatted'],
                            'start_time' => $entry['start_time'],
                            'end_time' => $entry['end_time'],
                            'reason' => 'Sobreposicao de horario',
                        ];
                        continue;
                    }
                }

                TimeEntry::create([
                    'user_id' => $userId,
                    'project_id' => $entry['project_id'],
                    'date' => $entry['date'],
                    'start_time' => $entry['start_time'],
                    'end_time' => $entry['end_time'],
                    'hours' => $entry['hours'],
                    'description' => $entry['description'],
                    'month_reference' => $entry['month_reference'],
                ]);

                $imported++;
            }

            DB::commit();

            return [
                'success' => true,
                'imported' => $imported,
                'skipped' => $skipped,
                'skipped_entries' => $skippedEntries,
                'errors' => $parseResult['errors'],
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Erro ao importar: ' . $e->getMessage(),
                'imported' => 0,
                'skipped' => 0,
            ];
        }
    }

    /**
     * Parse CSV lines from content
     */
    protected function parseLines(string $content): array
    {
        $lines = [];
        $rows = str_getcsv($content, "\n");

        // Skip header row
        $isFirstLine = true;

        foreach ($rows as $row) {
            $row = trim($row);
            if (empty($row)) {
                continue;
            }

            // Skip header line
            if ($isFirstLine) {
                $isFirstLine = false;
                // Check if it's actually a header
                $parts = str_getcsv($row, ';');
                if (count($parts) >= 3 && (
                    stripos($parts[0], 'data') !== false ||
                    stripos($parts[1], 'inicio') !== false ||
                    stripos($parts[0], 'date') !== false
                )) {
                    continue;
                }
            }

            $parts = str_getcsv($row, ';');
            if (count($parts) >= 3) {
                $lines[] = [
                    'date' => trim($parts[0] ?? ''),
                    'start' => trim($parts[1] ?? ''),
                    'end' => trim($parts[2] ?? ''),
                    'project' => trim($parts[3] ?? ''),
                    'description' => trim($parts[4] ?? ''),
                ];
            }
        }

        return $lines;
    }

    /**
     * Get projects map (name => id) for matching
     */
    protected function getProjectsMap(int $userId): array
    {
        $projects = Project::forUser($userId)->get();
        $map = [];

        foreach ($projects as $project) {
            $map[mb_strtolower($project->name)] = $project->id;
        }

        return $map;
    }

    /**
     * Validate a single line from CSV
     */
    protected function validateLine(array $line, int $lineNumber, int $userId, array $projects): array
    {
        $errors = [];

        // Validate date (DD/MM/YYYY format)
        $date = $this->parseDate($line['date']);
        if (!$date) {
            $errors[] = "Data invalida: '{$line['date']}'";
        }

        // Validate start time (HH:MM format)
        $startTime = $this->parseTime($line['start']);
        if (!$startTime) {
            $errors[] = "Hora inicio invalida: '{$line['start']}'";
        }

        // Validate end time (HH:MM format)
        $endTime = $this->parseTime($line['end']);
        if (!$endTime) {
            $errors[] = "Hora fim invalida: '{$line['end']}'";
        }

        // Validate end > start
        if ($startTime && $endTime && $endTime <= $startTime) {
            $errors[] = "Hora fim deve ser maior que hora inicio";
        }

        // Description is required
        if (empty($line['description'])) {
            $errors[] = "Descricao obrigatoria";
        }

        // If there are errors, return error result
        if (!empty($errors)) {
            return [
                'valid' => false,
                'error' => [
                    'line' => $lineNumber,
                    'data' => $line,
                    'messages' => $errors,
                ],
            ];
        }

        // Match project by name (case-insensitive)
        $projectId = null;
        if (!empty($line['project'])) {
            $projectKey = mb_strtolower($line['project']);
            if (!isset($projects[$projectKey])) {
                return [
                    'valid' => false,
                    'error' => [
                        'line' => $lineNumber,
                        'data' => $line,
                        'messages' => ["Projeto não encontrado: '{$line['project']}'"],
                    ],
                ];
            }
            $projectId = $projects[$projectKey];
        }

        // Calculate hours
        $hours = $this->timeCalculator->calculateHours($startTime, $endTime);

        // Format month reference
        $monthReference = Carbon::parse($date)->format('Y-m');

        return [
            'valid' => true,
            'entry' => [
                'date' => $date,
                'date_formatted' => Carbon::parse($date)->format('d/m/Y'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'hours' => $hours,
                'project_id' => $projectId,
                'project_name' => $line['project'],
                'description' => $line['description'],
                'month_reference' => $monthReference,
            ],
        ];
    }

    /**
     * Parse date from DD/MM/YYYY format
     */
    protected function parseDate(string $dateStr): ?string
    {
        $dateStr = trim($dateStr);

        // Try DD/MM/YYYY format
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateStr, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];

            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return "{$year}-{$month}-{$day}";
            }
        }

        // Try YYYY-MM-DD format (ISO)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateStr, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];

            if (checkdate((int)$month, (int)$day, (int)$year)) {
                return $dateStr;
            }
        }

        return null;
    }

    /**
     * Parse time from HH:MM format
     */
    protected function parseTime(string $timeStr): ?string
    {
        $timeStr = trim($timeStr);

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $timeStr, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];

            if ($hours >= 0 && $hours <= 23 && $minutes >= 0 && $minutes <= 59) {
                return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            }
        }

        return null;
    }
}
