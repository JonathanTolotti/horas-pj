<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::forUser(auth()->id());
        $projects = Project::forUser(auth()->id())->orderBy('name')->get();

        return view('settings', [
            'settings' => $settings,
            'projects' => $projects,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'hourly_rate' => 'required|numeric|min:0',
            'extra_value' => 'required|numeric|min:0',
        ]);

        $settings = Setting::forUser(auth()->id());
        $settings->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Configuracoes atualizadas com sucesso!',
            'settings' => $settings,
        ]);
    }

    public function storeProject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['active'] = $validated['active'] ?? true;
        $validated['is_default'] = $validated['is_default'] ?? false;

        // Se este projeto será o padrão, remover padrão dos outros
        if ($validated['is_default']) {
            Project::forUser(auth()->id())->update(['is_default' => false]);
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
}
