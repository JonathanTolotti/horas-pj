<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $settings = Setting::forUser($user->id);
        $projects = Project::forUser($user->id)->with('companies')->orderBy('name')->get();
        $companies = Company::forUser($user->id)->orderBy('name')->get();

        // Limites do plano
        $projectLimit = $user->getLimit('projects');
        $companyLimit = $user->getLimit('companies');
        $canAddProject = $projectLimit === null || $projects->count() < $projectLimit;
        $canAddCompany = $companyLimit === null || $companies->count() < $companyLimit;

        return view('settings', [
            'settings' => $settings,
            'projects' => $projects,
            'companies' => $companies,
            'canAddProject' => $canAddProject,
            'canAddCompany' => $canAddCompany,
            'projectLimit' => $projectLimit,
            'companyLimit' => $companyLimit,
            'isPremium' => $user->isPremium(),
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hourly_rate' => 'required|numeric|min:0',
            'on_call_hourly_rate' => 'nullable|numeric|min:0',
            'extra_value' => 'required|numeric|min:0',
            'discount_value' => 'required|numeric|min:0',
            'auto_save_tracking' => 'boolean',
        ]);

        $settings = Setting::forUser(auth()->id());
        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Configurações atualizadas com sucesso!',
            'settings' => $settings,
        ]);
    }

    public function storeProject(Request $request): JsonResponse
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'is_default' => 'boolean',
            'default_description' => 'nullable|string|max:255',
        ]);

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

    public function updateProject(Request $request, Project $project): JsonResponse
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'is_default' => 'boolean',
            'default_description' => 'nullable|string|max:255',
        ]);

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

    public function storeCompany(Request $request): JsonResponse
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => ['required', 'string', 'size:18', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/'],
            'active' => 'boolean',
        ]);

        $validated['user_id'] = $user->id;
        $validated['active'] = $validated['active'] ?? true;

        $company = Company::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Empresa criada com sucesso!',
            'company' => $company,
        ]);
    }

    public function updateCompany(Request $request, Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Nao autorizado.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => ['required', 'string', 'size:18', 'regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/'],
            'active' => 'boolean',
        ]);

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

        $project->companies()->updateExistingPivot($company->id, [
            'percentage' => $validated['percentage'],
        ]);

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

        $project->companies()->detach($company->id);

        return response()->json([
            'success' => true,
            'message' => 'Vinculo removido com sucesso!',
        ]);
    }
}
