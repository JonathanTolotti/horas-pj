<?php

namespace App\Http\Controllers;

use App\Models\SupervisorAccess;
use App\Models\SupervisorInvitation;
use App\Models\User;
use App\Mail\SupervisorInvitationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupervisorAccessController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $accesses = SupervisorAccess::where('user_id', $user->id)
            ->with('supervisor')
            ->orderBy('created_at', 'desc')
            ->get();

        $invitations = SupervisorInvitation::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'rejected'])
            ->with('supervisor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('supervisors.index', compact('accesses', 'invitations'));
    }

    public function invite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'can_view_financials' => ['boolean'],
            'can_view_analytics' => ['boolean'],
            'can_export' => ['boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'expires_at.after' => 'A data de expiração deve ser no futuro.',
        ]);

        $user = auth()->user();

        $supervisor = User::where('email', $validated['email'])->first();

        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum usuário encontrado com este e-mail. O supervisor deve ter uma conta no sistema.',
            ], 422);
        }

        if ($supervisor->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não pode se convidar como supervisor.',
            ], 422);
        }

        // Verifica se já existe acesso ativo
        $existingAccess = SupervisorAccess::where('user_id', $user->id)
            ->where('supervisor_id', $supervisor->id)
            ->first();

        if ($existingAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuário já é seu supervisor. Edite as permissões existentes.',
            ], 422);
        }

        // Remove convite anterior se houver (pendente ou rejeitado)
        SupervisorInvitation::where('user_id', $user->id)
            ->where('supervisor_id', $supervisor->id)
            ->delete();

        $invitation = SupervisorInvitation::create([
            'user_id' => $user->id,
            'supervisor_id' => $supervisor->id,
            'can_view_financials' => $validated['can_view_financials'] ?? false,
            'can_view_analytics' => $validated['can_view_analytics'] ?? false,
            'can_export' => $validated['can_export'] ?? false,
            'expires_at' => $validated['expires_at'] ?? null,
            'status' => 'pending',
        ]);

        Mail::to($supervisor->email)->send(new SupervisorInvitationMail($user, $invitation, $supervisor));

        return response()->json([
            'success' => true,
            'message' => 'Convite enviado para ' . $supervisor->name . '.',
        ]);
    }

    public function update(Request $request, SupervisorAccess $supervisorAccess): JsonResponse
    {
        if ($supervisorAccess->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $validated = $request->validate([
            'can_view_financials' => ['boolean'],
            'can_view_analytics' => ['boolean'],
            'can_export' => ['boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'permanent' => ['boolean'],
        ], [
            'expires_at.after' => 'A data de expiração deve ser no futuro.',
        ]);

        $supervisorAccess->update([
            'can_view_financials' => $validated['can_view_financials'] ?? $supervisorAccess->can_view_financials,
            'can_view_analytics' => $validated['can_view_analytics'] ?? $supervisorAccess->can_view_analytics,
            'can_export' => $validated['can_export'] ?? $supervisorAccess->can_export,
            'expires_at' => ($validated['permanent'] ?? false) ? null : ($validated['expires_at'] ?? $supervisorAccess->expires_at),
        ]);

        return response()->json(['success' => true, 'message' => 'Permissões atualizadas.']);
    }

    public function destroy(SupervisorAccess $supervisorAccess): JsonResponse
    {
        if ($supervisorAccess->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $supervisorAccess->delete();

        return response()->json(['success' => true, 'message' => 'Acesso revogado.']);
    }

    public function cancelInvite(SupervisorInvitation $supervisorInvitation): JsonResponse
    {
        if ($supervisorInvitation->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $supervisorInvitation->delete();

        return response()->json(['success' => true, 'message' => 'Convite cancelado.']);
    }
}

