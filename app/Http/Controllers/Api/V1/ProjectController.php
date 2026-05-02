<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $projects = Project::forUser($request->user()->id)
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $projects]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (!empty($data['is_default'])) {
            Project::forUser($user->id)->update(['is_default' => false]);
        }

        $project = Project::create(array_merge($data, ['user_id' => $user->id]));

        return response()->json(['data' => $project], 201);
    }

    public function update(StoreProjectRequest $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $project = Project::forUser($user->id)->findOrFail($id);
        $data    = $request->validated();

        if (!empty($data['is_default'])) {
            Project::forUser($user->id)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $project->update($data);

        return response()->json(['data' => $project->fresh()]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $project = Project::forUser($request->user()->id)->findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Projeto excluído com sucesso.']);
    }
}
