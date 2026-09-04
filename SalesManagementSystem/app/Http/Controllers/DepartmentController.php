<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        $managers = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER])->get()->keyBy(fn($u) => (string) $u->id);

        return view('departments.index', compact('departments', 'managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'manager_id'  => 'nullable|string',
        ]);

        Department::create($validated);

        return back()->with('success', 'Department created successfully!');
    }

    public function update(Request $request, string $id)
    {
        $dept = Department::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'manager_id'  => 'nullable|string',
        ]);

        $dept->update($validated);

        return back()->with('success', 'Department updated successfully!');
    }

    public function destroy(string $id)
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return back()->with('success', 'Department deleted.');
    }
}
