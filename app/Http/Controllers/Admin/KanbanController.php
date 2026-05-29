<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KanbanTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class KanbanController extends Controller
{
    public function index(): View
    {
        $tasks = KanbanTask::with(['user', 'assignedTo'])->orderBy('position')->get();
        $tasksByStatus = $tasks->groupBy('status');
        
        $statuses = ['Pendente', 'Em andamento', 'Concluído', 'Cancelado'];
        $priorities = ['Baixa', 'Média', 'Alta', 'Crítica'];
        
        $admins = User::where('is_admin', true)->get();
        
        return view('admin.kanban.index', compact('tasksByStatus', 'statuses', 'priorities', 'admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['position'] = KanbanTask::where('status', $validated['status'])->count();

        KanbanTask::create($validated);

        return back()->with('success', 'Tarefa criada com sucesso!');
    }

    public function updateStatus(Request $request, KanbanTask $task): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'position' => 'required|integer',
        ]);

        $task->update([
            'status' => $validated['status'],
            'position' => $validated['position']
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, KanbanTask $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return back()->with('success', 'Tarefa atualizada!');
    }

    public function destroy(KanbanTask $task)
    {
        $task->delete();
        return back()->with('success', 'Tarefa removida!');
    }
}
