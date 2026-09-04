<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $user = auth()->user();
        if ($request->has('my_tasks') || $user->isSalesExecutive() || $user->isSupportAgent()) {
            $query->where('assigned_to', (string) $user->id);
        } elseif ($assignedTo = $request->input('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        $tasks = $query->orderBy('due_date', 'asc')->paginate(15)->withQueryString();
        $users = User::where('status', User::STATUS_ACTIVE)->get();

        return view('tasks.index', compact('tasks', 'users'));
    }

    public function create(Request $request)
    {
        $users = User::where('status', User::STATUS_ACTIVE)->get();
        $leads = Lead::open()->get();
        $deals = Deal::open()->get();
        $customers = Customer::orderBy('name')->get();

        $relatedType = $request->input('related_type');
        $relatedId = $request->input('related_id');

        return view('tasks.create', compact('users', 'leads', 'deals', 'customers', 'relatedType', 'relatedId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|in:call,meeting,email,follow_up,demo,other',
            'priority'     => 'required|in:low,medium,high',
            'assigned_to'  => 'nullable|string',
            'related_type' => 'nullable|string|in:lead,deal,customer,ticket',
            'related_id'   => 'nullable|string',
            'due_date'     => 'nullable|date',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = Task::STATUS_PENDING;
        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = auth()->id();
        }

        $task = Task::create($validated);

        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            NotificationService::send(
                userId: $task->assigned_to,
                type: 'task_assigned',
                title: 'New Task Assigned',
                message: "You have been assigned: {$task->title}",
                relatedType: 'task',
                relatedId: (string) $task->id,
                actionUrl: route('tasks.index')
            );
        }

        return redirect()->route('tasks.index')
            ->with('success', "Task '{$task->title}' scheduled successfully!");
    }

    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        $assignedUser = User::find($task->assigned_to);

        return view('tasks.show', compact('task', 'assignedUser'));
    }

    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $users = User::where('status', User::STATUS_ACTIVE)->get();

        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, string $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|in:call,meeting,email,follow_up,demo,other',
            'priority'     => 'required|in:low,medium,high',
            'status'       => 'required|in:pending,completed,cancelled',
            'assigned_to'  => 'nullable|string',
            'due_date'     => 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        if ($validated['status'] === Task::STATUS_COMPLETED && $task->status !== Task::STATUS_COMPLETED) {
            $validated['completed_at'] = now();
        }

        $task->update($validated);

        return redirect()->route('tasks.index')
            ->with('success', "Task updated successfully!");
    }

    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task removed.');
    }

    public function complete(string $id)
    {
        $task = Task::findOrFail($id);
        $task->update([
            'status'       => Task::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        return back()->with('success', "Task '{$task->title}' marked as completed! ✅");
    }
}
