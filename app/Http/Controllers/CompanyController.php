<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\CompanyDocument;
use App\Models\CompanyNote;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $companies = Company::forUser($user->id)->with('projects')->orderBy('name')->get();

        $companyLimit = $user->getLimit('companies');
        $canAddCompany = $companyLimit === null || $companies->count() < $companyLimit;

        return view('companies.index', [
            'companies'    => $companies,
            'canAddCompany' => $canAddCompany,
            'companyLimit'  => $companyLimit,
            'isPremium'     => $user->isPremium(),
        ]);
    }

    public function show(Company $company): View
    {
        if ($company->user_id !== auth()->id()) {
            abort(403);
        }

        $company->load(['projects', 'invoices', 'documents', 'notes' => function ($q) {
            $q->orderBy('note_date', 'desc')->orderBy('created_at', 'desc');
        }]);

        $projects = Project::forUser(auth()->id())->active()->orderBy('name')->get();

        $totalHours = 0;
        $totalRevenue = 0;
        foreach ($company->projects as $project) {
            $entries = $project->timeEntries()->where('user_id', auth()->id())->get();
            $totalHours += $entries->sum('hours');
            $totalRevenue += $entries->sum('hours') * ($project->pivot->percentage / 100);
        }

        return view('companies.show', [
            'company'      => $company,
            'projects'     => $projects,
            'totalHours'   => $totalHours,
            'isPremium'    => auth()->user()->isPremium(),
        ]);
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $user = auth()->user();
        $limit = $user->getLimit('companies');

        if ($limit !== null) {
            $currentCount = Company::forUser($user->id)->count();
            if ($currentCount >= $limit) {
                return response()->json([
                    'success'          => false,
                    'message'          => "Limite de {$limit} empresa(s) atingido. Faça upgrade para Premium para cadastrar empresas ilimitadas.",
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

    public function update(StoreCompanyRequest $request, Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $company->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Empresa atualizada com sucesso!',
            'company' => $company,
        ]);
    }

    public function destroy(Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        if ($company->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir uma empresa com vínculos a projetos. Remova os vínculos primeiro.',
            ], 422);
        }

        // Remove documents from storage
        foreach ($company->documents as $doc) {
            Storage::delete($doc->file_path);
        }

        $company->delete();

        return response()->json(['success' => true, 'message' => 'Empresa excluída com sucesso!']);
    }

    public function attachProject(Request $request, Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'percentage' => 'required|numeric|min:0.01|max:100',
        ]);

        $project = Project::find($validated['project_id']);
        if ($project->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Projeto não encontrado.'], 404);
        }

        $currentTotal = $project->companies()->sum('percentage');
        if ($currentTotal + $validated['percentage'] > 100) {
            return response()->json([
                'success' => false,
                'message' => 'A soma das porcentagens não pode ultrapassar 100%. Disponível: ' . (100 - $currentTotal) . '%',
            ], 422);
        }

        if ($project->companies()->where('company_id', $company->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Esta empresa já está vinculada ao projeto.'], 422);
        }

        $project->companies()->attach($company->id, ['percentage' => $validated['percentage']]);

        AuditLog::record(
            userId: auth()->id(),
            entityType: 'company_project',
            entityId: null,
            entityLabel: "{$project->name} ← {$company->name}",
            action: 'created',
            oldValues: null,
            newValues: ['project_id' => $project->id, 'company_id' => $company->id, 'percentage' => $validated['percentage']],
            ipAddress: request()->ip(),
        );

        return response()->json(['success' => true, 'message' => 'Empresa vinculada com sucesso!']);
    }

    public function updateProjectPercentage(Request $request, Company $company, Project $project): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'percentage' => 'required|numeric|min:0.01|max:100',
        ]);

        $currentTotal = $project->companies()->where('company_id', '!=', $company->id)->sum('percentage');
        if ($currentTotal + $validated['percentage'] > 100) {
            return response()->json([
                'success' => false,
                'message' => 'A soma das porcentagens não pode ultrapassar 100%. Disponível: ' . (100 - $currentTotal) . '%',
            ], 422);
        }

        $oldPercentage = $project->companies()->where('company_id', $company->id)->first()?->pivot?->percentage;
        $project->companies()->updateExistingPivot($company->id, ['percentage' => $validated['percentage']]);

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

        return response()->json(['success' => true, 'message' => 'Porcentagem atualizada com sucesso!']);
    }

    public function detachProject(Company $company, Project $project): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
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

        return response()->json(['success' => true, 'message' => 'Vínculo removido com sucesso!']);
    }

    public function lookupCnpj(string $cnpj): JsonResponse
    {
        $cnpjClean = preg_replace('/\D/', '', $cnpj);
        if (strlen($cnpjClean) !== 14) {
            return response()->json(['error' => 'CNPJ inválido'], 422);
        }

        $response = Http::timeout(10)->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpjClean}");

        if (!$response->ok()) {
            return response()->json(['error' => 'CNPJ não encontrado'], 404);
        }

        $data = $response->json();

        $cep = preg_replace('/\D/', '', $data['cep'] ?? '');
        if (strlen($cep) === 8) {
            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);
        } else {
            $cep = null;
        }

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

        $response = Http::timeout(10)->get("https://viacep.com.br/ws/{$cepClean}/json/");

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

    public function storeDocument(Request $request, Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'file'        => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            'name'        => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $file = $request->file('file');
        $path = $file->store("company-docs/" . auth()->id(), 'local');

        $doc = CompanyDocument::create([
            'user_id'     => auth()->id(),
            'company_id'  => $company->id,
            'name'        => $request->input('name') ?: $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getMimeType(),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Documento enviado com sucesso!',
            'document' => [
                'id'             => $doc->id,
                'name'           => $doc->name,
                'description'    => $doc->description,
                'formatted_size' => $doc->formatted_size,
                'mime_type'      => $doc->mime_type,
                'created_at'     => $doc->created_at->format('d/m/Y'),
            ],
        ]);
    }

    public function viewDocument(Company $company, CompanyDocument $document)
    {
        if ($company->user_id !== auth()->id() || $document->company_id !== $company->id) {
            abort(403);
        }

        return Storage::response($document->file_path, $document->name, [
            'Content-Disposition' => 'inline; filename="' . $document->name . '"',
        ]);
    }

    public function downloadDocument(Company $company, CompanyDocument $document)
    {
        if ($company->user_id !== auth()->id() || $document->company_id !== $company->id) {
            abort(403);
        }

        return Storage::download($document->file_path, $document->name);
    }

    public function destroyDocument(Company $company, CompanyDocument $document): JsonResponse
    {
        if ($company->user_id !== auth()->id() || $document->company_id !== $company->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        Storage::delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true, 'message' => 'Documento excluído com sucesso!']);
    }

    public function storeNote(Request $request, Company $company): JsonResponse
    {
        if ($company->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'type'      => 'required|in:meeting,negotiation,call,email,visit,other',
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'note_date' => 'required|date',
        ], [
            'type.required'      => 'O tipo é obrigatório.',
            'type.in'            => 'Tipo inválido.',
            'title.required'     => 'O título é obrigatório.',
            'content.required'   => 'O conteúdo é obrigatório.',
            'note_date.required' => 'A data é obrigatória.',
            'note_date.date'     => 'Data inválida.',
        ]);

        $note = CompanyNote::create([
            'user_id'    => auth()->id(),
            'company_id' => $company->id,
            ...$validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro criado com sucesso!',
            'note'    => [
                'id'         => $note->id,
                'type'       => $note->type,
                'type_label' => $note->type_label,
                'type_color' => $note->type_color,
                'title'      => $note->title,
                'content'    => $note->content,
                'note_date'  => $note->note_date->format('d/m/Y'),
            ],
        ]);
    }

    public function updateNote(Request $request, Company $company, CompanyNote $note): JsonResponse
    {
        if ($company->user_id !== auth()->id() || $note->company_id !== $company->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'type'      => 'required|in:meeting,negotiation,call,email,visit,other',
            'title'     => 'required|string|max:255',
            'content'   => 'required|string',
            'note_date' => 'required|date',
        ]);

        $note->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Registro atualizado com sucesso!',
            'note'    => [
                'id'         => $note->id,
                'type'       => $note->type,
                'type_label' => $note->type_label,
                'type_color' => $note->type_color,
                'title'      => $note->title,
                'content'    => $note->content,
                'note_date'  => $note->note_date->format('d/m/Y'),
            ],
        ]);
    }

    public function destroyNote(Company $company, CompanyNote $note): JsonResponse
    {
        if ($company->user_id !== auth()->id() || $note->company_id !== $company->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $note->delete();

        return response()->json(['success' => true, 'message' => 'Registro excluído com sucesso!']);
    }
}
