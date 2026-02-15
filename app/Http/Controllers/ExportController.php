<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Services\TimeCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}

    /**
     * Pagina de relatorios
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $monthReference = $request->session()->get('month_reference', Carbon::now()->format('Y-m'));

        $projects = Project::forUser(auth()->id())->active()->orderBy('name')->get();
        $companies = Company::forUser(auth()->id())->active()->orderBy('name')->get();

        return view('reports.index', [
            'currentMonth' => $monthReference,
            'months' => $this->getAvailableMonths(),
            'projects' => $projects,
            'companies' => $companies,
            'isPremium' => $user->isPremium(),
        ]);
    }

    /**
     * Meses disponiveis para filtro
     */
    protected function getAvailableMonths(): array
    {
        $months = [];
        $current = Carbon::now();
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

    /**
     * Exportar relatório em PDF
     */
    public function pdf(Request $request)
    {
        $monthReference = $request->input('month', session('month_reference', Carbon::now()->format('Y-m')));
        $projectId = $request->input('project_id');
        $companyId = $request->input('company_id');

        $data = $this->getExportData($monthReference, $projectId, $companyId);

        $pdf = Pdf::loadView('exports.pdf.monthly-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = $this->generateFilename('relatorio', $monthReference, 'pdf');

        return $pdf->download($filename);
    }

    /**
     * Exportar relatório para Nota Fiscal (PDF)
     */
    public function nf(Request $request)
    {
        $monthReference = $request->input('month', session('month_reference', Carbon::now()->format('Y-m')));
        $companyId = $request->input('company_id');

        if (!$companyId) {
            return back()->with('error', 'Selecione uma empresa para gerar o relatório de NF.');
        }

        $company = Company::where('id', $companyId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $data = $this->getExportData($monthReference, null, $companyId);
        $data['company'] = $company;
        $data['is_nf_report'] = true;

        $pdf = Pdf::loadView('exports.pdf.nf-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = $this->generateFilename('nf-' . str_replace(' ', '-', strtolower($company->name)), $monthReference, 'pdf');

        return $pdf->download($filename);
    }

    /**
     * Exportar relatório em Excel/CSV
     */
    public function excel(Request $request): StreamedResponse
    {
        $monthReference = $request->input('month', session('month_reference', Carbon::now()->format('Y-m')));
        $projectId = $request->input('project_id');
        $companyId = $request->input('company_id');

        $data = $this->getExportData($monthReference, $projectId, $companyId);
        $filename = $this->generateFilename('relatorio', $monthReference, 'csv');

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8 (Excel reconhecer acentos)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Cabeçalho das colunas
            fputcsv($handle, ['Data', 'Início', 'Fim', 'Horas', 'Projeto', 'Descrição'], ';');

            // Registros
            foreach ($data['entries'] as $entry) {
                fputcsv($handle, [
                    $entry->date->format('d/m/Y'),
                    substr($entry->start_time, 0, 5),
                    substr($entry->end_time, 0, 5),
                    number_format($entry->hours, 2, ',', '.'),
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

    /**
     * Obter dados para exportação
     */
    protected function getExportData(string $monthReference, ?int $projectId = null, ?int $companyId = null): array
    {
        $userId = auth()->id();
        $user = auth()->user();

        // Query de lançamentos
        $query = TimeEntry::forUser($userId)
            ->forMonth($monthReference)
            ->with('project.companies')
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc');

        // Filtro por projeto
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        // Se filtrar por empresa, pegar apenas projetos dessa empresa
        if ($companyId) {
            $projectIds = Project::forUser($userId)
                ->whereHas('companies', fn($q) => $q->where('companies.id', $companyId))
                ->pluck('id');
            $query->whereIn('project_id', $projectIds);
        }

        $entries = $query->get();

        // Estatísticas
        $stats = $this->calculator->getMonthlyStats($userId, $monthReference);

        // Se filtrou por empresa, recalcular valor
        if ($companyId && !empty($stats['companies'])) {
            $companyStats = collect($stats['companies'])->firstWhere('id', $companyId);
            if ($companyStats) {
                $stats['filtered_value'] = $companyStats['value'];
            }
        }

        // Formatar mês
        $monthDate = Carbon::createFromFormat('Y-m', $monthReference);
        $monthLabel = ucfirst($monthDate->isoFormat('MMMM [de] YYYY'));

        // Projetos do usuário
        $projects = Project::forUser($userId)->active()->orderBy('name')->get();

        // Empresas do usuário
        $companies = Company::forUser($userId)->active()->orderBy('name')->get();

        return [
            'entries' => $entries,
            'stats' => $stats,
            'month_reference' => $monthReference,
            'month_label' => $monthLabel,
            'projects' => $projects,
            'companies' => $companies,
            'user' => $user,
            'generated_at' => now(),
        ];
    }

    /**
     * Relatório Anual para IR
     */
    public function annualReport(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $userId = auth()->id();
        $user = auth()->user();

        // Dados mensais do ano
        $monthlyData = [];
        $totalHours = 0;
        $totalRevenue = 0;
        $hourlyRate = $this->calculator->getHourlyRate($userId);
        $extraValue = $this->calculator->getExtraValue($userId);

        for ($month = 1; $month <= 12; $month++) {
            $monthReference = sprintf('%d-%02d', $year, $month);
            $monthDate = Carbon::createFromFormat('Y-m', $monthReference);

            // Se o mês ainda não passou, não incluir
            if ($monthDate->isFuture()) {
                continue;
            }

            $hours = $this->calculator->getTotalHoursForMonth($userId, $monthReference);
            $revenue = ($hours * $hourlyRate) + $extraValue;

            $monthlyData[] = [
                'month' => $month,
                'month_name' => ucfirst($monthDate->isoFormat('MMMM')),
                'month_short' => ucfirst($monthDate->isoFormat('MMM')),
                'hours' => $hours,
                'revenue' => $revenue,
                'extra_value' => $extraValue,
                'hours_revenue' => $hours * $hourlyRate,
            ];

            $totalHours += $hours;
            $totalRevenue += $revenue;
        }

        // Faturamento por empresa no ano
        $companyRevenues = [];
        $companies = Company::forUser($userId)->active()->get();

        foreach ($companies as $company) {
            $companyTotal = 0;

            // Somar o faturamento de cada mês para esta empresa
            for ($month = 1; $month <= 12; $month++) {
                $monthReference = sprintf('%d-%02d', $year, $month);
                $monthDate = Carbon::createFromFormat('Y-m', $monthReference);

                if ($monthDate->isFuture()) {
                    continue;
                }

                $monthlyCompanyRevenues = $this->calculator->calculateRevenueByCompany($userId, $monthReference);

                if (isset($monthlyCompanyRevenues[$company->id])) {
                    $companyTotal += $monthlyCompanyRevenues[$company->id]['revenue'];
                }
            }

            if ($companyTotal > 0) {
                $companyRevenues[] = [
                    'id' => $company->id,
                    'name' => $company->name,
                    'cnpj' => $company->cnpj,
                    'revenue' => $companyTotal,
                    'percentage' => $totalRevenue > 0 ? round(($companyTotal / $totalRevenue) * 100, 1) : 0,
                ];
            }
        }

        // Ordenar empresas por faturamento (maior primeiro)
        usort($companyRevenues, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        // Estatísticas
        $monthsWorked = count(array_filter($monthlyData, fn($m) => $m['hours'] > 0));
        $averageMonthlyHours = $monthsWorked > 0 ? $totalHours / $monthsWorked : 0;
        $averageMonthlyRevenue = $monthsWorked > 0 ? $totalRevenue / $monthsWorked : 0;

        // Melhor e pior mês
        $bestMonth = null;
        $worstMonth = null;

        if (count($monthlyData) > 0) {
            $sortedByRevenue = $monthlyData;
            usort($sortedByRevenue, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
            $bestMonth = $sortedByRevenue[0];

            $monthsWithRevenue = array_filter($sortedByRevenue, fn($m) => $m['revenue'] > 0);
            if (count($monthsWithRevenue) > 0) {
                $worstMonth = end($monthsWithRevenue);
            }
        }

        // Anos disponíveis para seleção
        $firstEntry = TimeEntry::forUser($userId)->orderBy('date', 'asc')->first();
        $startYear = $firstEntry ? $firstEntry->date->year : Carbon::now()->year;
        $availableYears = range(Carbon::now()->year, $startYear);

        $data = [
            'year' => $year,
            'monthly_data' => $monthlyData,
            'total_hours' => $totalHours,
            'total_revenue' => $totalRevenue,
            'hourly_rate' => $hourlyRate,
            'extra_value' => $extraValue,
            'company_revenues' => $companyRevenues,
            'months_worked' => $monthsWorked,
            'average_monthly_hours' => $averageMonthlyHours,
            'average_monthly_revenue' => $averageMonthlyRevenue,
            'best_month' => $bestMonth,
            'worst_month' => $worstMonth,
            'available_years' => $availableYears,
            'user' => $user,
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('exports.pdf.annual-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = "relatorio-anual-ir-{$year}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Gerar nome do arquivo
     */
    protected function generateFilename(string $prefix, string $monthReference, string $extension): string
    {
        $monthDate = Carbon::createFromFormat('Y-m', $monthReference);
        $monthSlug = $monthDate->format('Y-m');

        return "{$prefix}-{$monthSlug}.{$extension}";
    }
}
