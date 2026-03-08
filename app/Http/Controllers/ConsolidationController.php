<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidationFilterRequest;
use App\Http\Requests\ConsolidationPdfRequest;
use App\Models\Company;
use App\Models\OnCallPeriod;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Services\TimeCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\View\View;

class ConsolidationController extends Controller
{
    public function __construct(
        protected TimeCalculatorService $calculator
    ) {}

    /**
     * Exibe a tela — lê filtros da sessão, sem parâmetros na URL.
     */
    public function index(): View
    {
        $user = auth()->user();
        $userId = $user->id;

        $allProjects = Project::forUser($userId)->active()->orderBy('name')->get();
        $allCompanies = Company::forUser($userId)->active()->orderBy('name')->with('projects')->get();

        $filters = session('consolidation_filters', []);
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;
        $filterCompanyIds = $filters['filter_company_ids'] ?? [];
        $filterProjectIds = $filters['filter_project_ids'] ?? [];

        if (!$startDate || !$endDate) {
            return view('consolidation', [
                'hasData' => false,
                'isPremium' => $user->isPremium(),
                'allProjects' => $allProjects,
                'allCompanies' => $allCompanies,
                'filterCompanyIds' => [],
                'filterProjectIds' => [],
            ]);
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // IDs do usuário (fonte de verdade)
        $allProjectIds = $allProjects->pluck('id')->toArray();
        $allCompanyIds = $allCompanies->pluck('id')->toArray();

        // Garante que só IDs do próprio usuário são usados
        $filterCompanyIds = array_values(array_intersect($filterCompanyIds, $allCompanyIds));
        $filterProjectIds = array_values(array_intersect($filterProjectIds, $allProjectIds));

        // Filtro ativo somente quando algum item foi excluído
        $companyFilterActive = !empty($filterCompanyIds) && count($filterCompanyIds) < count($allCompanyIds);
        $projectFilterActive = !empty($filterProjectIds) && count($filterProjectIds) < count($allProjectIds);

        // Resolver project IDs via empresas (quando filtro de empresa ativo)
        $projectIdsFromCompanies = [];
        if ($companyFilterActive) {
            $projectIdsFromCompanies = Project::forUser($userId)
                ->whereHas('companies', fn($q) => $q->whereIn('companies.id', $filterCompanyIds))
                ->pluck('id')
                ->toArray();
        }

        // Restrição de project IDs para as queries
        $restrictedProjectIds = null;
        if ($projectFilterActive && $companyFilterActive) {
            $restrictedProjectIds = array_unique(array_merge($filterProjectIds, $projectIdsFromCompanies));
        } elseif ($projectFilterActive) {
            $restrictedProjectIds = $filterProjectIds;
        } elseif ($companyFilterActive) {
            $restrictedProjectIds = $projectIdsFromCompanies;
        }

        // Lançamentos
        $entriesQuery = TimeEntry::forUser($userId)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->with('project');

        if ($restrictedProjectIds !== null) {
            $entriesQuery->whereIn('project_id', $restrictedProjectIds);
        }

        $entries = $entriesQuery
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->map(function ($entry) use ($userId) {
                $hourlyRate = $this->calculator->getHourlyRate($userId, $entry->month_reference);
                $entry->computed_revenue = round($entry->hours * $hourlyRate, 2);
                return $entry;
            });

        // Sobreavisos
        $onCallQuery = OnCallPeriod::forUser($userId)
            ->where('start_datetime', '<=', $end)
            ->where('end_datetime', '>=', $start)
            ->with('project');

        if ($restrictedProjectIds !== null) {
            if ($projectFilterActive) {
                $onCallQuery->whereIn('project_id', $restrictedProjectIds);
            } else {
                $onCallQuery->where(function ($q) use ($restrictedProjectIds) {
                    $q->whereIn('project_id', $restrictedProjectIds)
                      ->orWhereNull('project_id');
                });
            }
        }

        $onCallPeriods = $onCallQuery
            ->orderBy('start_datetime')
            ->get()
            ->map(function ($period) {
                $period->computed_on_call_revenue = round((float) $period->on_call_hours * (float) $period->hourly_rate, 2);
                return $period;
            });

        [$extraValue, $discountValue] = $this->getAdjustmentsForRange($userId, $start, $end);

        return view('consolidation', [
            'hasData' => true,
            'isPremium' => $user->isPremium(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateFormatted' => $start->format('d/m/Y'),
            'endDateFormatted' => $end->format('d/m/Y'),
            'entries' => $entries,
            'onCallPeriods' => $onCallPeriods,
            'extraValue' => $extraValue,
            'discountValue' => $discountValue,
            'companies' => $allCompanies,
            'allProjects' => $allProjects,
            'allCompanies' => $allCompanies,
            'filterCompanyIds' => $filterCompanyIds,
            'filterProjectIds' => $filterProjectIds,
        ]);
    }

    /**
     * Recebe os filtros via POST, salva na sessão e redireciona para index.
     */
    public function filter(ConsolidationFilterRequest $request)
    {
        session(['consolidation_filters' => [
            'start_date'        => $request->input('start_date'),
            'end_date'          => $request->input('end_date'),
            'filter_company_ids' => array_filter((array) $request->input('filter_company_ids', [])),
            'filter_project_ids' => array_filter((array) $request->input('filter_project_ids', [])),
        ]]);

        return redirect()->route('consolidation.index');
    }

    /**
     * Limpa apenas os filtros de empresa/projeto, mantendo o período.
     */
    public function clear()
    {
        $filters = session('consolidation_filters', []);
        $filters['filter_company_ids'] = [];
        $filters['filter_project_ids'] = [];
        session(['consolidation_filters' => $filters]);

        return redirect()->route('consolidation.index');
    }

    /**
     * Gera o PDF via POST — IDs dos itens selecionados ficam no corpo da requisição.
     */
    public function pdf(ConsolidationPdfRequest $request)
    {

        $filters = session('consolidation_filters', []);
        $startDate = $filters['start_date'] ?? null;
        $endDate   = $filters['end_date'] ?? null;

        if (!$startDate || !$endDate) {
            return redirect()->route('consolidation.index');
        }

        $userId = auth()->id();
        $user = auth()->user();
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $entryIds  = array_filter((array) $request->input('entry_ids', []));
        $onCallIds = array_filter((array) $request->input('on_call_ids', []));

        // Lançamentos selecionados (valida ownership via forUser)
        $entries = collect();
        if (!empty($entryIds)) {
            $entries = TimeEntry::forUser($userId)
                ->whereIn('id', $entryIds)
                ->with('project')
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
                ->map(function ($entry) use ($userId) {
                    $hourlyRate = $this->calculator->getHourlyRate($userId, $entry->month_reference);
                    $entry->computed_revenue = round($entry->hours * $hourlyRate, 2);
                    return $entry;
                });
        }

        // Sobreavisos selecionados
        $onCallPeriods = collect();
        if (!empty($onCallIds)) {
            $onCallPeriods = OnCallPeriod::forUser($userId)
                ->whereIn('id', $onCallIds)
                ->with('project')
                ->orderBy('start_datetime')
                ->get()
                ->map(function ($period) {
                    $period->computed_on_call_revenue = round((float) $period->on_call_hours * (float) $period->hourly_rate, 2);
                    return $period;
                });
        }

        $totalHours        = (float) $entries->sum('hours');
        $totalRevenue      = (float) $entries->sum('computed_revenue');
        $totalOnCallHours  = (float) $onCallPeriods->sum('on_call_hours');
        $totalOnCallRevenue = (float) $onCallPeriods->sum('computed_on_call_revenue');

        [$extraValue, $discountValue] = $this->getAdjustmentsForRange($userId, $start, $end);

        $totalFinal = round($totalRevenue + $totalOnCallRevenue + $extraValue - $discountValue, 2);

        $companies = Company::forUser($userId)->active()->with('projects')->get();
        $companyRevenues = $this->distributeByCompany($companies, $totalFinal);

        $unassignedRevenue = round($totalFinal - array_sum(array_column($companyRevenues, 'revenue')), 2);
        if (abs($unassignedRevenue) <= 0.05) {
            $unassignedRevenue = 0;
        }

        $data = [
            'entries'              => $entries,
            'onCallPeriods'        => $onCallPeriods,
            'start_date_formatted' => $start->format('d/m/Y'),
            'end_date_formatted'   => $end->format('d/m/Y'),
            'total_hours'          => $totalHours,
            'total_revenue'        => $totalRevenue,
            'total_on_call_hours'  => $totalOnCallHours,
            'total_on_call_revenue' => $totalOnCallRevenue,
            'extra_value'          => $extraValue,
            'discount_value'       => $discountValue,
            'total_final'          => $totalFinal,
            'company_revenues'     => $companyRevenues,
            'unassigned_revenue'   => $unassignedRevenue,
            'user'                 => $user,
            'generated_at'         => now(),
        ];

        $pdf = Pdf::loadView('exports.pdf.consolidation-report', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("consolidacao-{$startDate}-a-{$endDate}.pdf");
    }

    protected function getAdjustmentsForRange(int $userId, Carbon $start, Carbon $end): array
    {
        $extraTotal = 0.0;
        $discountTotal = 0.0;

        $current = $start->copy()->startOfMonth();
        while ($current->lte($end)) {
            $monthRef = $current->format('Y-m');
            $extraTotal += $this->calculator->getExtraValue($userId, $monthRef);
            $discountTotal += $this->calculator->getDiscountValue($userId, $monthRef);
            $current->addMonth();
        }

        return [round($extraTotal, 2), round($discountTotal, 2)];
    }

    protected function distributeByCompany($companies, float $totalFinal): array
    {
        $companyRevenues = [];
        $totalPercentage = 0;

        foreach ($companies as $company) {
            $companyPercentage = 0;
            if ($company->projects->count() > 0) {
                foreach ($company->projects as $project) {
                    $companyPercentage += $project->pivot->percentage;
                }
                $companyPercentage /= $company->projects->count();
            }

            $companyRevenues[$company->id] = [
                'id'         => $company->id,
                'name'       => $company->name,
                'cnpj'       => $company->cnpj,
                'percentage' => $companyPercentage,
                'revenue'    => 0.0,
            ];
            $totalPercentage += $companyPercentage;
        }

        if ($totalPercentage > 0) {
            $distributed = 0;
            $lastId = null;
            foreach ($companyRevenues as $id => $data) {
                $rev = round($totalFinal * ($data['percentage'] / $totalPercentage), 2);
                $companyRevenues[$id]['revenue'] = $rev;
                $distributed += $rev;
                $lastId = $id;
            }
            if ($lastId !== null) {
                $diff = round($totalFinal - $distributed, 2);
                if (abs($diff) > 0 && abs($diff) <= 0.03) {
                    $companyRevenues[$lastId]['revenue'] += $diff;
                }
            }
        }

        foreach ($companyRevenues as $id => $_) {
            unset($companyRevenues[$id]['percentage']);
        }

        return $companyRevenues;
    }
}
