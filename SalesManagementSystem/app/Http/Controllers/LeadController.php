<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use App\Models\User;
use App\Services\AuditService;
use App\Services\LeadService;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(private LeadService $leadService) {}

    public function index(Request $request)
    {
        $query = Lead::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($source = $request->input('source')) {
            $query->where('source', $source);
        }

        if ($assignedTo = $request->input('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        $user = auth()->user();
        if ($user->isSalesExecutive() && !$request->has('view_all')) {
            $query->where('assigned_to', (string) $user->id);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();

        return view('leads.index', compact('leads', 'salesReps'));
    }

    public function create()
    {
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();
        return view('leads.create', compact('salesReps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'first_name'     => 'nullable|string|max:100',
            'last_name'      => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:255',
            'source'         => 'required|string',
            'priority'       => 'required|in:low,medium,high',
            'status'         => 'required|in:new,contacted,qualified,unqualified,converted,lost',
            'assigned_to'    => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'value_estimate' => 'nullable|numeric',
            'notes'          => 'nullable|string',
        ]);

        $lead = $this->leadService->createLead($validated, auth()->user());

        return redirect()->route('leads.show', $lead->id)
            ->with('success', "Lead '{$lead->title}' created successfully!");
    }

    public function show(string $id)
    {
        $lead = Lead::findOrFail($id);
        $assignedUser = User::find($lead->assigned_to);
        $createdUser = User::find($lead->created_by);
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();
        $stages = DealStage::orderBy('order')->get();

        $convertedCustomer = $lead->customer_id ? Customer::find($lead->customer_id) : null;
        $convertedDeal = $lead->deal_id ? Deal::find($lead->deal_id) : null;
        $activities = Activity::where('related_type', 'lead')->where('related_id', $id)->orderBy('occurred_at', 'desc')->get();

        return view('leads.show', compact('lead', 'assignedUser', 'createdUser', 'salesReps', 'stages', 'convertedCustomer', 'convertedDeal', 'activities'));
    }

    public function edit(string $id)
    {
        $lead = Lead::findOrFail($id);
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();

        return view('leads.edit', compact('lead', 'salesReps'));
    }

    public function update(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);

        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'first_name'     => 'nullable|string|max:100',
            'last_name'      => 'nullable|string|max:100',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:255',
            'source'         => 'required|string',
            'priority'       => 'required|in:low,medium,high',
            'status'         => 'required|in:new,contacted,qualified,unqualified,converted,lost',
            'assigned_to'    => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'value_estimate' => 'nullable|numeric',
            'notes'          => 'nullable|string',
        ]);

        $this->leadService->updateLead($lead, $validated, auth()->user());

        return redirect()->route('leads.show', $lead->id)
            ->with('success', "Lead '{$lead->title}' updated successfully!");
    }

    public function destroy(string $id)
    {
        $lead = Lead::findOrFail($id);
        $title = $lead->title;

        AuditService::log(
            action: 'lead.deleted',
            module: 'leads',
            entityType: 'Lead',
            entityId: (string) $lead->id,
            entityLabel: $title,
            oldValues: $lead->toArray()
        );

        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', "Lead '{$title}' has been deleted.");
    }

    public function assign(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);
        $request->validate(['assigned_to' => 'required|string']);

        $this->leadService->assignLead($lead, $request->input('assigned_to'), auth()->user());

        return back()->with('success', 'Lead assigned successfully!');
    }

    public function updateStatus(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);
        $request->validate(['status' => 'required|in:new,contacted,qualified,unqualified,converted,lost']);

        $this->leadService->updateStatus($lead, $request->input('status'), auth()->user());

        return back()->with('success', 'Lead status updated!');
    }

    public function convert(Request $request, string $id)
    {
        $lead = Lead::findOrFail($id);

        if ($lead->isConverted()) {
            return back()->with('error', 'This lead is already converted.');
        }

        $validated = $request->validate([
            'company_name'        => 'nullable|string|max:255',
            'create_deal'         => 'nullable|boolean',
            'deal_title'          => 'nullable|string|max:255',
            'deal_value'          => 'nullable|numeric',
            'currency'            => 'nullable|string|max:10',
            'stage_id'            => 'nullable|string',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
        ]);

        $result = $this->leadService->convertLead($lead, $validated, auth()->user());

        return redirect()->route('customers.show', $result['customer']->id)
            ->with('success', "🎉 Lead converted successfully to Customer '{$result['customer']->name}'!" . ($result['deal'] ? " and Deal created." : ""));
    }
}
