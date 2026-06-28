<?php

namespace App\Http\Controllers;

use App\Models\TaskNote;
use App\Models\TimeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskNoteController extends Controller
{
    public function index(TimeEntry $timeEntry): JsonResponse
    {
        $this->authorizeEntry($timeEntry);

        $tasks = $timeEntry->taskNotes()->get(['id', 'content', 'minutes', 'status', 'created_at']);

        return response()->json(['tasks' => $tasks]);
    }

    public function store(Request $request, TimeEntry $timeEntry): JsonResponse
    {
        $this->authorizeEntry($timeEntry);

        $data = $request->validate([
            'content' => 'required|string|max:1000',
            'minutes' => 'nullable|integer|min:1|max:9999',
        ], [
            'content.required' => 'A descrição da tarefa é obrigatória.',
            'content.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'minutes.integer' => 'O tempo deve ser um número inteiro.',
            'minutes.min' => 'O tempo mínimo é 1 minuto.',
            'minutes.max' => 'O tempo máximo é 9999 minutos.',
        ]);

        $task = $timeEntry->taskNotes()->create([
            'user_id' => auth()->id(),
            'content' => $data['content'],
            'minutes' => $data['minutes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'task' => ['id' => $task->id, 'content' => $task->content, 'minutes' => $task->minutes, 'status' => $task->status, 'created_at' => $task->created_at],
        ], 201);
    }

    public function updateStatus(TimeEntry $timeEntry, TaskNote $taskNote): JsonResponse
    {
        $this->authorizeEntry($timeEntry);

        if ($taskNote->time_entry_id !== $timeEntry->id || $taskNote->user_id !== auth()->id()) {
            abort(403);
        }

        $taskNote->update([
            'status' => $taskNote->status === 'pending' ? 'done' : 'pending',
        ]);

        return response()->json(['success' => true, 'status' => $taskNote->status]);
    }

    public function destroy(TimeEntry $timeEntry, TaskNote $taskNote): JsonResponse
    {
        $this->authorizeEntry($timeEntry);

        if ($taskNote->time_entry_id !== $timeEntry->id || $taskNote->user_id !== auth()->id()) {
            abort(403);
        }

        $taskNote->delete();

        return response()->json(['success' => true]);
    }

    private function authorizeEntry(TimeEntry $timeEntry): void
    {
        if ($timeEntry->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
