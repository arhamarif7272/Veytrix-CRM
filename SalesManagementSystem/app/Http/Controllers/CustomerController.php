<?php

namespace App\Services;

// In CustomerController we'll use models
namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Ticket;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($assignedTo = $request->input('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        // Role restriction for sales executive: only view their assigned or all if permitted
        $user = auth()->user();
        if ($user->isSalesExecutive() && !$request->has('view_all')) {
            $query->where('assigned_to', (string) $user->id);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();

        return view('customers.index', compact('customers', 'salesReps'));
    }

    public function create()
    {
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();
        return view('customers.create', compact('salesReps'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:255',
            'website'        => 'nullable|url|max:255',
            'industry'       => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'assigned_to'    => 'nullable|string',
            'status'         => 'required|in:active,inactive,prospect,churned',
            'source'         => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'annual_revenue' => 'nullable|numeric',
            'employee_count' => 'nullable|integer',
        ]);

        $validated['created_by'] = auth()->id();
        if (empty($validated['assigned_to'])) {
            $validated['assigned_to'] = auth()->id();
        }

        $customer = Customer::create($validated);

        Activity::create([
            'type'              => Activity::TYPE_CREATED,
            'subject'           => 'Customer Created',
            'description'       => "Customer '{$customer->name}' was added to the system",
            'related_type'      => 'customer',
            'related_id'        => (string) $customer->id,
            'performed_by'      => auth()->id(),
            'performed_by_name' => auth()->user()->name,
            'occurred_at'       => now(),
        ]);

        AuditService::log(
            action: 'customer.created',
            module: 'customers',
            entityType: 'Customer',
            entityId: (string) $customer->id,
            entityLabel: $customer->name,
            newValues: $customer->toArray()
        );

        return redirect()->route('customers.show', $customer->id)
            ->with('success', "Customer '{$customer->name}' created successfully!");
    }

    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);
        $assignedUser = User::find($customer->assigned_to);

        $contacts = Contact::where('customer_id', $id)->get();
        $deals = Deal::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        $quotations = Quotation::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        $invoices = Invoice::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        $tickets = Ticket::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        $activities = Activity::where('related_type', 'customer')->where('related_id', $id)->orderBy('occurred_at', 'desc')->get();

        return view('customers.show', compact('customer', 'assignedUser', 'contacts', 'deals', 'quotations', 'invoices', 'tickets', 'activities'));
    }

    public function edit(string $id)
    {
        $customer = Customer::findOrFail($id);
        $salesReps = User::whereIn('role', [User::ROLE_SALES_EXECUTIVE, User::ROLE_MANAGER, User::ROLE_ADMIN])->get();

        return view('customers.edit', compact('customer', 'salesReps'));
    }

    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:50',
            'company'        => 'nullable|string|max:255',
            'website'        => 'nullable|url|max:255',
            'industry'       => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'country'        => 'nullable|string|max:100',
            'assigned_to'    => 'nullable|string',
            'status'         => 'required|in:active,inactive,prospect,churned',
            'source'         => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'annual_revenue' => 'nullable|numeric',
            'employee_count' => 'nullable|integer',
        ]);

        $old = $customer->toArray();
        $customer->update($validated);

        AuditService::log(
            action: 'customer.updated',
            module: 'customers',
            entityType: 'Customer',
            entityId: (string) $customer->id,
            entityLabel: $customer->name,
            oldValues: $old,
            newValues: $customer->toArray()
        );

        return redirect()->route('customers.show', $customer->id)
            ->with('success', "Customer '{$customer->name}' updated successfully!");
    }

    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;

        AuditService::log(
            action: 'customer.deleted',
            module: 'customers',
            entityType: 'Customer',
            entityId: (string) $customer->id,
            entityLabel: $name,
            oldValues: $customer->toArray()
        );

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Customer '{$name}' has been deleted.");
    }

    public function storeContact(Request $request, string $customer)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:50',
            'position'   => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
        ]);

        $validated['customer_id'] = $customer;
        if (!empty($validated['is_primary'])) {
            Contact::where('customer_id', $customer)->update(['is_primary' => false]);
        }

        Contact::create($validated);

        return back()->with('success', 'Contact person added successfully!');
    }

    public function destroyContact(string $customer, string $contact)
    {
        Contact::where('customer_id', $customer)->where('_id', $contact)->delete();
        return back()->with('success', 'Contact removed successfully!');
    }

    public function activity(Request $request, string $customer)
    {
        $validated = $request->validate([
            'type'        => 'required|string',
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Activity::create([
            'type'              => $validated['type'],
            'subject'           => $validated['subject'],
            'description'       => $validated['description'],
            'related_type'      => 'customer',
            'related_id'        => $customer,
            'performed_by'      => auth()->id(),
            'performed_by_name' => auth()->user()->name,
            'occurred_at'       => now(),
        ]);

        return back()->with('success', 'Activity logged successfully!');
    }
}
