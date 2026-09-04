<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query();

        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }

        if ($action = $request->input('action')) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($actor = $request->input('actor')) {
            $query->where('actor_name', 'like', "%{$actor}%");
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        $modules = AuditLog::distinct('module')->pluck('module')->filter();

        return view('audit-logs.index', compact('logs', 'modules'));
    }

    public function show(string $id)
    {
        $log = AuditLog::findOrFail($id);
        return view('audit-logs.show', compact('log'));
    }
}
