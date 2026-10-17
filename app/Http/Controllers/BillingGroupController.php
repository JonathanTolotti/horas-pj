<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillingGroupRequest;
use App\Models\BillingGroup;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingGroupController extends Controller
{
    public function store(StoreBillingGroupRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $group = DB::transaction(function () use ($validated) {
            $group = BillingGroup::create([
                'user_id'            => Auth::id(),
                'name'               => $validated['name'],
                'primary_company_id' => $validated['primary_company_id'] ?? $validated['company_ids'][0],
            ]);

            $this->syncCompanies($group, $validated['company_ids']);

            return $group;
        });

        return response()->json([
            'success' => true,
            'message' => 'Grupo de faturamento criado com sucesso!',
            'group'   => $group->load('companies'),
        ]);
    }

    public function update(StoreBillingGroupRequest $request, string $uuid): JsonResponse
    {
        $group = BillingGroup::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();
        $validated = $request->validated();

        DB::transaction(function () use ($group, $validated) {
            $group->update([
                'name'               => $validated['name'],
                'primary_company_id' => $validated['primary_company_id'] ?? $validated['company_ids'][0],
            ]);

            $this->syncCompanies($group, $validated['company_ids']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Grupo de faturamento atualizado com sucesso!',
            'group'   => $group->fresh()->load('companies'),
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $group = BillingGroup::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        // FKs com "set null" desvinculam empresas e faturas
        $group->delete();

        return response()->json(['success' => true, 'message' => 'Grupo de faturamento excluído com sucesso!']);
    }

    /**
     * Cada empresa pertence a no máximo um grupo: as selecionadas passam para este grupo
     * e as que saíram da seleção ficam sem grupo.
     */
    private function syncCompanies(BillingGroup $group, array $companyIds): void
    {
        Company::forUser(Auth::id())
            ->where('billing_group_id', $group->id)
            ->whereNotIn('id', $companyIds)
            ->get()
            ->each->update(['billing_group_id' => null]);

        Company::forUser(Auth::id())
            ->whereIn('id', $companyIds)
            ->get()
            ->each->update(['billing_group_id' => $group->id]);
    }
}
