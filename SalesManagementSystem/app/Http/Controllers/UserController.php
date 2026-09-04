<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $departments = Department::all();

        return view('users.index', compact('users', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|in:admin,manager,sales_executive,support_agent,customer',
            'department_id' => 'nullable|string',
            'phone'         => 'nullable|string|max:50',
            'status'        => 'required|in:active,inactive,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        AuditService::log(
            action: 'user.created',
            module: 'users',
            entityType: 'User',
            entityId: (string) $user->id,
            entityLabel: $user->name,
            description: "User {$user->name} ({$user->email}) created with role {$user->role}"
        );

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' created successfully!");
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $department = $user->department_id ? Department::find($user->department_id) : null;

        return view('users.show', compact('user', 'department'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();

        return view('users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255|unique:users,email,' . $id,
            'password'      => 'nullable|string|min:8|confirmed',
            'role'          => 'required|in:admin,manager,sales_executive,support_agent,customer',
            'department_id' => 'nullable|string',
            'phone'         => 'nullable|string|max:50',
            'status'        => 'required|in:active,inactive,suspended',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $oldRole = $user->role;
        $user->update($validated);

        $roleChanged = $oldRole !== $user->role;
        AuditService::log(
            action: $roleChanged ? 'user.role_changed' : 'user.updated',
            module: 'users',
            entityType: 'User',
            entityId: (string) $user->id,
            entityLabel: $user->name,
            description: $roleChanged
                ? "Admin changed {$user->name}'s role from '{$oldRole}' to '{$user->role}'"
                : "User {$user->name} updated"
        );

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' updated successfully!" . ($roleChanged ? " Role set to " . ucwords(str_replace('_', ' ', $user->role)) . "." : ""));
    }

    public function destroy(string $id)
    {
        if ($id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User '{$name}' deleted.");
    }

    public function updateStatus(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $request->validate(['status' => 'required|in:active,inactive,suspended']);

        $user->update(['status' => $request->input('status')]);

        return back()->with('success', 'User status updated!');
    }

    public function updateRole(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $request->validate(['role' => 'required|in:admin,manager,sales_executive,support_agent,customer']);

        $oldRole = $user->role;
        $newRole = $request->input('role');
        $user->update(['role' => $newRole]);

        AuditService::log(
            action: 'user.role_changed',
            module: 'users',
            entityType: 'User',
            entityId: (string) $user->id,
            entityLabel: $user->name,
            description: "Admin assigned role '{$newRole}' to {$user->name} (previous: '{$oldRole}')"
        );

        $roleTitle = ucwords(str_replace('_', ' ', $newRole));
        return back()->with('success', "Role for '{$user->name}' successfully changed to {$roleTitle}!");
    }
}
