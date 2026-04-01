<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Company;
use App\Models\Notice;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $settings = Setting::forUser($user->id);
        $projects = Project::forUser($user->id)->with('companies')->orderBy('name')->get();
        $companies = Company::forUser($user->id)->orderBy('name')->get();
        $bankAccounts = $user->isPremium()
            ? BankAccount::forUser($user->id)->orderBy('bank_name')->get()
            : collect();
        $notices = Notice::forUser($user->id)->orderBy('start_date', 'desc')->get();

        // Limites do plano
        $projectLimit = $user->getLimit('projects');
        $companyLimit = $user->getLimit('companies');
        $canAddProject = $projectLimit === null || $projects->count() < $projectLimit;
        $canAddCompany = $companyLimit === null || $companies->count() < $companyLimit;

        $auditLogs = AuditLog::forUser($user->id)->orderBy('created_at', 'desc')->limit(30)->get();

        return view('settings', [
            'settings'      => $settings,
            'projects'      => $projects,
            'companies'     => $companies,
            'bankAccounts'  => $bankAccounts,
            'notices'       => $notices,
            'canAddProject' => $canAddProject,
            'canAddCompany' => $canAddCompany,
            'projectLimit'  => $projectLimit,
            'companyLimit'  => $companyLimit,
            'isPremium'     => $user->isPremium(),
            'auditLogs'     => $auditLogs,
        ]);
    }

    public function lookupCnpj(string $cnpj): JsonResponse
    {
        $cnpjClean = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpjClean) !== 14) {
            return response()->json(['error' => 'CNPJ inválido'], 422);
        }

        $response = Http::timeout(10)
            ->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpjClean}");

        if (!$response->ok()) {
            return response()->json(['error' => 'CNPJ não encontrado'], 404);
        }

        $data = $response->json();

        // Formata CEP: "01310100" → "01310-100"
        $cep = preg_replace('/\D/', '', $data['cep'] ?? '');
        if (strlen($cep) === 8) {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
        } else {
            $cep = null;
        }

        // Formata telefone: "11 98765-4321" → "(11) 98765-4321"
        $tel = preg_replace('/\D/', '', $data['ddd_telefone_1'] ?? '');
        if (strlen($tel) >= 10) {
            $ddd = substr($tel, 0, 2);
            $num = substr($tel, 2);
            $telefone = strlen($num) === 9
                ? "({$ddd}) " . substr($num, 0, 5) . '-' . substr($num, 5)
                : "({$ddd}) " . substr($num, 0, 4) . '-' . substr($num, 4);
        } else {
            $telefone = null;
        }

        return response()->json([
            'nome_fantasia' => $data['nome_fantasia'] ?? null,
            'razao_social'  => $data['razao_social'] ?? null,
            'email'         => $data['email'] ?? null,
            'telefone'      => $telefone,
            'cep'           => $cep,
            'logradouro'    => $data['logradouro'] ?? null,
            'numero'        => $data['numero'] ?? null,
            'complemento'   => $data['complemento'] ?? null,
            'bairro'        => $data['bairro'] ?? null,
            'cidade'        => $data['municipio'] ?? null,
            'uf'            => $data['uf'] ?? null,
        ]);
    }

    public function lookupCep(string $cep): JsonResponse
    {
        $cepClean = preg_replace('/\D/', '', $cep);
        if (strlen($cepClean) !== 8) {
            return response()->json(['error' => 'CEP inválido'], 422);
        }

        $response = Http::timeout(10)
            ->get("https://viacep.com.br/ws/{$cepClean}/json/");

        if (!$response->ok()) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }

        $data = $response->json();

        if (isset($data['erro'])) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }

        return response()->json([
            'logradouro' => $data['logradouro'] ?? null,
            'bairro'     => $data['bairro'] ?? null,
            'cidade'     => $data['localidade'] ?? null,
            'uf'         => $data['uf'] ?? null,
        ]);
    }

    public function auditLogsPartial(Request $request): \Illuminate\View\View
    {
        $validFilters = ['all', 'setting', 'project', 'company', 'company_project'];
        $filter = in_array($request->query('filter'), $validFilters)
            ? $request->query('filter')
            : 'all';

        $query = AuditLog::forUser(auth()->id())->orderBy('created_at', 'desc');
        if ($filter !== 'all') {
            $query->where('entity_type', $filter);
        }
        $auditLogs = $query->limit(30)->get();

        return view('partials.audit-logs', compact('auditLogs'));
    }

    public function updateSettings(UpdateSettingsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $settings = Setting::forUser(auth()->id());
        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Configurações atualizadas com sucesso!',
            'settings' => $settings,
        ]);
    }

    public function storeProject(StoreProjectRequest $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $user->getLimit('projects');

        // Verificar limite de projetos para plano Free
        if ($limit !== null) {
            $currentCount = Project::forUser($user->id)->count();
            if ($currentCount >= $limit) {
                return response()->json([
                    'success' => false,
                    'message' => "Limite de {$limit} projeto(s) atingido. Faça upgrade para Premium para criar projetos ilimitados.",
                    'premium_required' => true,
                ], 403);
            }
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['active'] = $validated['active'] ?? true;
        $validated['is_default'] = $validated['is_default'] ?? false;

        // Se este projeto será o padrão, remover padrão dos outros
        if ($validated['is_default']) {
            Project::forUser($user->id)->update(['is_default' => false]);
        }

        $project = Project::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Projeto criado com sucesso!',
            'project' => $project,
        ]);
    }

    public function updateProject(StoreProjectRequest $request, Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $validated = $request->validated();

        // Se este projeto será o padrão, remover padrão dos outros
        if ($validated['is_default'] ?? false) {
            Project::forUser(auth()->id())->where('id', '!=', $project->id)->update(['is_default' => false]);
        }

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Projeto atualizado com sucesso!',
            'project' => $project,
        ]);
    }

    public function destroyProject(Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        // Verificar se há lançamentos vinculados
        if ($project->timeEntries()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nao e possivel excluir um projeto com lancamentos vinculados.',
            ], 422);
        }

        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Projeto excluido com sucesso!',
        ]);
    }

    public function storeCompany(StoreCompanyRequest $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $user->getLimit('companies');

        // Verificar limite de empresas para plano Free
        if ($limit !== null) {
            $currentCount = Company::forUser($user->id)->count();
            if ($currentCount >= $limit) {
                return response()->json([
                    'success' => false,
                    'message' => "Limite de {$limit} empresa(s) atingido. Faça upgrade para Premium para cadastrar empresas ilimitadas.",
                    'premium_required' => true,
                ], 403);
            }
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['active'] = $validated['active'] ?? true;

        $company = Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Empresa criada com sucesso!',
            'company' => $company,
        ]);
    }

    public function updateCompany(StoreCompanyRequest $request, Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $validated = $request->validated();
        $company->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Empresa atualizada com sucesso!',
            'company' => $company,
        ]);
    }

    public function destroyCompany(Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        // Verificar se há vínculos com projetos
        if ($company->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Nao e possivel excluir uma empresa com vinculos a projetos. Remova os vinculos primeiro.',
            ], 422);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Empresa excluida com sucesso!',
        ]);
    }

    public function attachCompany(Request $request, Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'percentage' => 'required|numeric|min:0.01|max:100',
        ]);

        // Verificar se a empresa pertence ao usuário
        $company = Company::find($validated['company_id']);
        if ($company->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa nao encontrada.',
            ], 404);
        }

        // Verificar se a soma das porcentagens não ultrapassa 100%
        $currentTotal = $project->companies()->sum('percentage');
        if ($currentTotal + $validated['percentage'] > 100) {
            return response()->json([
                'success' => false,
                'message' => 'A soma das porcentagens nao pode ultrapassar 100%. Disponivel: ' . (100 - $currentTotal) . '%',
            ], 422);
        }

        // Verificar se já existe vínculo
        if ($project->companies()->where('company_id', $validated['company_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta empresa ja esta vinculada ao projeto.',
            ], 422);
        }

        $project->companies()->attach($validated['company_id'], [
            'percentage' => $validated['percentage'],
        ]);

        AuditLog::record(
            userId: auth()->id(),
            entityType: 'company_project',
            entityId: null,
            entityLabel: "{$project->name} ← {$company->name}",
            action: 'created',
            oldValues: null,
            newValues: [
                'project_id' => $project->id,
                'company_id' => $company->id,
                'percentage' => $validated['percentage'],
            ],
            ipAddress: request()->ip(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Empresa vinculada com sucesso!',
        ]);
    }

    public function updateCompanyPercentage(Request $request, Project $project, Company $company): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $validated = $request->validate([
            'percentage' => 'required|numeric|min:0.01|max:100',
        ]);

        // Verificar se a soma das porcentagens não ultrapassa 100%
        $currentTotal = $project->companies()
            ->where('company_id', '!=', $company->id)
            ->sum('percentage');

        if ($currentTotal + $validated['percentage'] > 100) {
            return response()->json([
                'success' => false,
                'message' => 'A soma das porcentagens nao pode ultrapassar 100%. Disponivel: ' . (100 - $currentTotal) . '%',
            ], 422);
        }

        $oldPercentage = $project->companies()->where('company_id', $company->id)->first()?->pivot?->percentage;

        $project->companies()->updateExistingPivot($company->id, [
            'percentage' => $validated['percentage'],
        ]);

        AuditLog::record(
            userId: auth()->id(),
            entityType: 'company_project',
            entityId: null,
            entityLabel: "{$project->name} ← {$company->name}",
            action: 'updated',
            oldValues: ['percentage' => $oldPercentage],
            newValues: ['percentage' => $validated['percentage']],
            ipAddress: request()->ip(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Porcentagem atualizada com sucesso!',
        ]);
    }

    public function detachCompany(Project $project, Company $company): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $oldPercentage = $project->companies()->where('company_id', $company->id)->first()?->pivot?->percentage;

        $project->companies()->detach($company->id);

        AuditLog::record(
            userId: auth()->id(),
            entityType: 'company_project',
            entityId: null,
            entityLabel: "{$project->name} ← {$company->name}",
            action: 'deleted',
            oldValues: ['percentage' => $oldPercentage],
            newValues: null,
            ipAddress: request()->ip(),
        );

        return response()->json([
            'success' => true,
            'message' => 'Vinculo removido com sucesso!',
        ]);
    }
}
