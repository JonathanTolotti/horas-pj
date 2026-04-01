<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function index(): JsonResponse
    {
        $accounts = BankAccount::forUser(Auth::id())
            ->orderBy('bank_name')
            ->get();

        return response()->json(['success' => true, 'accounts' => $accounts]);
    }

    public function store(StoreBankAccountRequest $request): JsonResponse
    {
        $account = BankAccount::create(array_merge(
            $request->validated(),
            ['user_id' => Auth::id(), 'active' => $request->boolean('active', true)]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Conta bancária criada com sucesso.',
            'account' => $account,
        ]);
    }

    public function update(StoreBankAccountRequest $request, string $uuid): JsonResponse
    {
        $account = BankAccount::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        $account->update(array_merge(
            $request->validated(),
            ['active' => $request->boolean('active', $account->active)]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Conta bancária atualizada com sucesso.',
            'account' => $account->fresh(),
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $account = BankAccount::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        if ($account->invoices()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir uma conta bancária com faturas vinculadas.',
            ], 422);
        }

        $account->delete();

        return response()->json(['success' => true, 'message' => 'Conta bancária excluída com sucesso.']);
    }

    public function toggle(string $uuid): JsonResponse
    {
        $account = BankAccount::forUser(Auth::id())->where('uuid', $uuid)->firstOrFail();

        $account->update(['active' => !$account->active]);

        $status = $account->active ? 'ativada' : 'desativada';

        return response()->json([
            'success' => true,
            'message' => "Conta bancária {$status} com sucesso.",
            'account' => $account->fresh(),
        ]);
    }
}
