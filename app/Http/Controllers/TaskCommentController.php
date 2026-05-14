<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function store(Request $request, int $taskId)
    {
        $task = Task::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($taskId);

        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'user_name' => $request->user()->name,
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('tasks.show', $task->id)
            ->with('success', 'Comentario agregado.');
    }
}
