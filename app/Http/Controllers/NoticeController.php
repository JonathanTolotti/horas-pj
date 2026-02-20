<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoticeRequest;
use App\Models\Notice;
use Illuminate\Http\JsonResponse;

class NoticeController extends Controller
{
    public function index(): JsonResponse
    {
        $notices = Notice::forUser(auth()->id())->orderBy('start_date', 'desc')->get();

        return response()->json(['notices' => $notices]);
    }

    public function store(StoreNoticeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();
        $validated['is_active'] = $validated['is_active'] ?? true;

        $notice = Notice::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aviso criado com sucesso!',
            'notice'  => $notice,
        ]);
    }

    public function update(StoreNoticeRequest $request, Notice $notice): JsonResponse
    {
        if ($notice->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $notice->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Aviso atualizado com sucesso!',
            'notice'  => $notice,
        ]);
    }

    public function destroy(Notice $notice): JsonResponse
    {
        if ($notice->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $notice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aviso excluído com sucesso!',
        ]);
    }

    public function dismiss(Notice $notice): JsonResponse
    {
        if ($notice->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        if ($notice->type !== 'one_time') {
            return response()->json(['success' => false, 'message' => 'Apenas avisos do tipo "uma vez" podem ser fechados.'], 422);
        }

        $notice->update(['dismissed_at' => now()]);

        return response()->json(['success' => true]);
    }
}
