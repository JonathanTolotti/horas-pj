<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $companies = Company::forUser($request->user()->id)
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $companies]);
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create(array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        ));

        return response()->json(['data' => $company], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $company = Company::forUser($request->user()->id)->findOrFail($id);

        return response()->json(['data' => $company]);
    }

    public function update(StoreCompanyRequest $request, int $id): JsonResponse
    {
        $company = Company::forUser($request->user()->id)->findOrFail($id);
        $company->update($request->validated());

        return response()->json(['data' => $company->fresh()]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $company = Company::forUser($request->user()->id)->findOrFail($id);
        $company->delete();

        return response()->json(['message' => 'Empresa excluída com sucesso.']);
    }
}
