<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Quotation;
use App\Models\User;
use App\Services\AuditService;
use App\Services\DealService;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function __construct(private DealService $dealService) {}

    public function index(Request $request)
    {
        $query = Deal::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($stageId = $request->input('stage_id')) {
            $query->where('stage_id', $stageId);
        }

        if ($assignedTo = $request->input('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        $user = auth()->user();
        if ($user->isSalesExecutive() && !$request->has('view_all')) {
            $query->where('assigned_to', (string) $user->id);
        }

        $deals = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $stages = DealStage::orderBy('order')->get();
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();
        $customers = Customer::orderBy('name')->get();

        return view('deals.index', compact('deals', 'stages', 'salesReps', 'customers'));
    }

    public function pipeline(Request $request)
    {
        $user = auth()->user();
        $assignedTo = $request->input('assigned_to');

        if ($user->isSalesExecutive() && !$assignedTo) {
            $assignedTo = (string) $user->id;
        }

        $pipeline = $this->dealService->getPipelineData($assignedTo);
        $stages = DealStage::orderBy('order')->get();
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();
        $customers = Customer::all()->keyBy(fn($c) => (string) $c->id);

        return view('deals.pipeline', compact('pipeline', 'stages', 'salesReps', 'customers', 'assignedTo'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $stages = DealStage::orderBy('order')->get();
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();
        $selectedCustomer = $request->input('customer_id');

        return view('deals.create', compact('customers', 'stages', 'salesReps', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'customer_id'         => 'required|string',
            'stage_id'            => 'required|string',
            'value'               => 'required|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'assigned_to'         => 'nullable|string',
            'notes'               => 'nullable|string',
        ]);

        $deal = $this->dealService->createDeal($validated, auth()->user());

        return redirect()->route('deals.show', $deal->id)
            ->with('success', "Deal '{$deal->title}' created successfully!");
    }

    public function show(string $id)
    {
        $deal = Deal::findOrFail($id);
        $customer = Customer::find($deal->customer_id);
        $stage = DealStage::find($deal->stage_id);
        $stages = DealStage::orderBy('order')->get();
        $assignedUser = User::find($deal->assigned_to);
        $quotations = Quotation::where('deal_id', $id)->get();
        $activities = Activity::where('related_type', 'deal')->where('related_id', $id)->orderBy('occurred_at', 'desc')->get();

        return view('deals.show', compact('deal', 'customer', 'stage', 'stages', 'assignedUser', 'quotations', 'activities'));
    }

    public function edit(string $id)
    {
        $deal = Deal::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $stages = DealStage::orderBy('order')->get();
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();

        return view('deals.edit', compact('deal', 'customers', 'stages', 'salesReps'));
    }

    public function update(Request $request, string $id)
    {
        $deal = Deal::findOrFail($id);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'customer_id'         => 'required|string',
            'stage_id'            => 'required|string',
            'value'               => 'required|numeric|min:0',
            'currency'            => 'nullable|string|max:10',
            'probability'         => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'assigned_to'         => 'nullable|string',
            'status'              => 'required|in:open,won,lost',
            'notes'               => 'nullable|string',
        ]);

        $this->dealService->updateDeal($deal, $validated, auth()->user());

        return redirect()->route('deals.show', $deal->id)
            ->with('success', "Deal '{$deal->title}' updated successfully!");
    }

    public function destroy(string $id)
    {
        $deal = Deal::findOrFail($id);
        $title = $deal->title;

        AuditService::log(
            action: 'deal.deleted',
            module: 'deals',
            entityType: 'Deal',
            entityId: (string) $deal->id,
            entityLabel: $title,
            oldValues: $deal->toArray()
        );

        $deal->delete();

        return redirect()->route('deals.index')
            ->with('success', "Deal '{$title}' has been deleted.");
    }

    public function updateStage(Request $request, string $id)
    {
        $deal = Deal::findOrFail($id);
        $request->validate(['stage_id' => 'required|string']);

        $this->dealService->updateStage($deal, $request->input('stage_id'), auth()->user());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'deal' => $deal]);
        }

        return back()->with('success', 'Deal stage updated!');
    }

    public function markWon(string $id)
    {
        $deal = Deal::findOrFail($id);
        $this->dealService->markWon($deal, auth()->user());

        return back()->with('success', '🏆 Deal marked as WON!');
    }

    public function markLost(Request $request, string $id)
    {
        $deal = Deal::findOrFail($id);
        $request->validate(['lost_reason' => 'required|string|max:500']);

        $this->dealService->markLost($deal, $request->input('lost_reason'), auth()->user());

        return back()->with('success', 'Deal marked as lost.');
    }
}
