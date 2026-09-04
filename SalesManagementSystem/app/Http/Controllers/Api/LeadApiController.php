<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\Request;

class LeadApiController extends Controller
{
    public function __construct(private LeadService $leadService) {}

    public function index(Request $request)
    {
        $query = Lead::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $user = auth()->user();
        if ($user->isSalesExecutive()) {
            $query->where('assigned_to', (string) $user->id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate(20));
    }

    public function show(string $id)
    {
        $lead = Lead::findOrFail($id);
        return response()->json($lead);
    }

    public function assign(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);
        $request->validate(['assigned_to' => 'required|string']);

        $this->leadService->assignLead($lead, $request->input('assigned_to'), auth()->user());

        return response()->json(['success' => true, 'message' => 'Lead assigned.']);
    }

    public function updateStatus(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);
        $request->validate(['status' => 'required|string']);

        $this->leadService->updateStatus($lead, $request->input('status'), auth()->user());

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }
}
