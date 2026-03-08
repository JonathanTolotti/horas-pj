<?php

namespace App\Http\Controllers;

use App\Models\OnCallPeriod;
use App\Models\Project;
use App\Models\SupervisorAccess;
use App\Models\SupervisorInvitation;
use App\Models\TimeEntry;
use App\Models\User;
use App\Services\TimeCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupervisorController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}

    /**
     * Lista de usuários supervisionados
     */
    public function index(): View
    {
        $accesses = SupervisorAccess::where('supervisor_id', auth()->id())
            ->active()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $monthReference = Carbon::now()->format('Y-m');

        $supervisedData = $accesses->map(function ($access) use ($monthReference) {
            $stats = $this->calculator->getMonthlyStats($access->user_id, $monthReference);
            $lastEntry = TimeEntry::forUser($access->user_id)->orderBy('date', 'desc')->first();

            return [
                'access' => $access,
                'user' => $access->user,
                'total_hours' => $stats['total_hours'],
                'last_entry_date' => $lastEntry?->date,
            ];
        });

        return view('supervisor.index', compact('supervisedData'));
    }

    /**
     * Lista de convites recebidos (pendentes)
     */
    public function invitations(): View
    {
        $invitations = SupervisorInvitation::where('supervisor_id', auth()->id())
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('supervisor.invitations', compact('invitations'));
    }

    /**
     * Aceitar convite
     */
    public function accept(SupervisorInvitation $supervisorInvitation): JsonResponse
    {
        if ($supervisorInvitation->supervisor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        if (!$supervisorInvitation->isPending()) {
            return response()->json(['success' => false, 'message' => 'Este convite não está mais pendente.'], 422);
        }

        // Cria o acesso
        SupervisorAccess::updateOrCreate(
            ['user_id' => $supervisorInvitation->user_id, 'supervisor_id' => auth()->id()],
            [
                'can_view_financials' => $supervisorInvitation->can_view_financials,
                'can_view_analytics' => $supervisorInvitation->can_view_analytics,
                'can_export' => $supervisorInvitation->can_export,
                'expires_at' => $supervisorInvitation->expires_at,
            ]
        );

        $supervisorInvitation->update(['status' => 'accepted']);

        return response()->json([
            'success' => true,
            'message' => 'Convite aceito. Você agora tem acesso aos dados de ' . $supervisorInvitation->user->name . '.',
        ]);
    }

    /**
     * Rejeitar convite
     */
    public function reject(SupervisorInvitation $supervisorInvitation): JsonResponse
    {
        if ($supervisorInvitation->supervisor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        if (!$supervisorInvitation->isPending()) {
            return response()->json(['success' => false, 'message' => 'Este convite não está mais pendente.'], 422);
        }

        $supervisorInvitation->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Convite recusado.',
        ]);
    }

    /**
     * Dashboard read-only do usuário supervisionado
     */
    public function show(Request $request, SupervisorAccess $access): View
    {
        $access->load('user');
        $supervisedUser = $access->user;

        $monthReference = $request->session()->get(
            'supervisor_month_' . $supervisedUser->id,
            Carbon::now()->format('Y-m')
        );

        if ($request->has('month')) {
            $monthReference = $request->input('month');
            $request->session()->put('supervisor_month_' . $supervisedUser->id, $monthReference);
        }

        $baseQuery = TimeEntry::forUser($supervisedUser->id)
            ->forMonth($monthReference)
            ->with('project')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');

        $entries = (clone $baseQuery)->paginate(10);
        $allEntries = (clone $baseQuery)->get();

        $stats = $this->calculator->getMonthlyStats($supervisedUser->id, $monthReference);

        $entriesByDay = $this->groupEntriesByDay($allEntries, $stats['hourly_rate']);

        $onCallPeriods = OnCallPeriod::forUser($supervisedUser->id)
            ->forMonth($monthReference)
            ->with('project')
            ->orderBy('start_datetime', 'desc')
            ->get();

        $months = $this->getAvailableMonths($supervisedUser->id);

        return view('supervisor.show', [
            'supervisedUser' => $supervisedUser,
            'access' => $access,
            'entries' => $entries,
            'entriesByDay' => $entriesByDay,
            'stats' => $stats,
            'currentMonth' => $monthReference,
            'months' => $months,
            'onCallPeriods' => $onCallPeriods,
        ]);
    }

    /**
     * Exportar PDF do usuário supervisionado
     */
    public function exportPdf(Request $request, SupervisorAccess $access)
    {
        if (!$access->can_export) {
            abort(403, 'Você não tem permissão para exportar relatórios deste usuário.');
        }

        $monthReference = $request->input('month', Carbon::now()->format('Y-m'));
        $data = $this->getExportData($access->user_id, $monthReference);

        $pdf = Pdf::loadView('exports.pdf.monthly-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $monthDate = Carbon::createFromFormat('Y-m', $monthReference);
        $filename = 'relatorio-' . $monthDate->format('Y-m') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Exportar Excel do usuário supervisionado
     */
    public function exportExcel(Request $request, SupervisorAccess $access): StreamedResponse
    {
        if (!$access->can_export) {
            abort(403, 'Você não tem permissão para exportar relatórios deste usuário.');
        }

        $monthReference = $request->input('month', Carbon::now()->format('Y-m'));
        $data = $this->getExportData($access->user_id, $monthReference);

        $monthDate = Carbon::createFromFormat('Y-m', $monthReference);
        $filename = 'relatorio-' . $monthDate->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['Data', 'Início', 'Fim', 'Horas', 'Projeto', 'Descrição'], ';');

            foreach ($data['entries'] as $entry) {
                fputcsv($handle, [
                    $entry->date->format('d/m/Y'),
                    $entry->start_time ? substr($entry->start_time, 0, 5) : '-',
                    $entry->end_time ? substr($entry->end_time, 0, 5) : '-',
                    sprintf('%02d:%02d', floor($entry->hours), round(($entry->hours - floor($entry->hours)) * 60)),
                    $entry->project?->name ?? '-',
                    $entry->description,
                ], ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function getExportData(int $userId, string $monthReference): array
    {
        $user = User::find($userId);
        $entries = TimeEntry::forUser($userId)
            ->forMonth($monthReference)
            ->with('project.companies')
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $stats = $this->calculator->getMonthlyStats($userId, $monthReference);
        $onCallPeriods = OnCallPeriod::forUser($userId)
            ->forMonth($monthReference)
            ->with('project')
            ->orderBy('start_datetime', 'asc')
            ->get();

        $monthDate = Carbon::createFromFormat('Y-m', $monthReference);
        $monthLabel = ucfirst($monthDate->isoFormat('MMMM [de] YYYY'));

        return [
            'entries' => $entries,
            'stats' => $stats,
            'month_reference' => $monthReference,
            'month_label' => $monthLabel,
            'projects' => Project::forUser($userId)->active()->orderBy('name')->get(),
            'companies' => collect(),
            'onCallPeriods' => $onCallPeriods,
            'user' => $user,
            'generated_at' => now(),
        ];
    }

    protected function getAvailableMonths(int $userId): array
    {
        $months = [];
        $current = Carbon::now();

        for ($i = 0; $i < 12; $i++) {
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
                $totalHours = round((float) $dayEntries->sum('hours'), 2);
                $totalMinutes = (int) round($totalHours * 60);
                $totalValue = round(($totalMinutes / 60) * $hourlyRate, 2);

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

