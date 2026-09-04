<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tasks = Task::assignedTo((string) $user->id)->pending()->orderBy('due_date', 'asc')->get();

        return response()->json($tasks);
    }

    public function complete(string $id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'status'       => Task::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return response()->json(['success' => true, 'task' => $task]);
    }
}
