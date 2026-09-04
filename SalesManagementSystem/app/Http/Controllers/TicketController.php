<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\AuditService;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService) {}

    public function index(Request $request)
    {
        $query = Ticket::query();
        $user = auth()->user();

        if ($user->isCustomer()) {
            $customer = Customer::where('email', $user->email)->first();
            $customerIds = array_values(array_unique(array_filter([(string) $user->id, $customer ? (string) $customer->id : null])));
            $query->whereIn('customer_id', $customerIds);
        } elseif ($user->isSupportAgent() && $request->has('my_tickets')) {
            $query->where('assigned_to', (string) $user->id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($assignedTo = $request->input('assigned_to')) {
            $query->where('assigned_to', $assignedTo);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $agents = User::whereIn('role', [User::ROLE_SUPPORT_AGENT, User::ROLE_ADMIN, User::ROLE_MANAGER])->get();

        return view('tickets.index', compact('tickets', 'agents'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $agents = User::whereIn('role', [User::ROLE_SUPPORT_AGENT, User::ROLE_ADMIN])->get();

        return view('tickets.create', compact('customers', 'agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'customer_id' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,critical',
            'category'    => 'required|in:billing,technical,general,feature_request,other',
            'assigned_to' => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);

        $ticket = $this->ticketService->createTicket($validated, auth()->user());

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', "Ticket {$ticket->ticket_number} created successfully!");
    }

    public function show(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $customer = Customer::find($ticket->customer_id);
        $agent = User::find($ticket->assigned_to);
        $creator = User::find($ticket->created_by);
        $agents = User::whereIn('role', [User::ROLE_SUPPORT_AGENT, User::ROLE_ADMIN, User::ROLE_MANAGER])->get();

        $messages = TicketMessage::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();
        $senders = User::all()->keyBy(fn($u) => (string) $u->id);

        return view('tickets.show', compact('ticket', 'customer', 'agent', 'creator', 'agents', 'messages', 'senders'));
    }

    public function edit(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $agents = User::whereIn('role', [User::ROLE_SUPPORT_AGENT, User::ROLE_ADMIN])->get();

        return view('tickets.edit', compact('ticket', 'customers', 'agents'));
    }

    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'priority'    => 'required|in:low,medium,high,critical',
            'category'    => 'required|in:billing,technical,general,feature_request,other',
            'assigned_to' => 'nullable|string',
            'status'      => 'required|in:open,in_progress,waiting_customer,resolved,closed',
            'due_date'    => 'nullable|date',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $num = $ticket->ticket_number;

        TicketMessage::where('ticket_id', $id)->delete();
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', "Ticket {$num} deleted.");
    }

    public function assign(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $request->validate(['assigned_to' => 'required|string']);

        $ticket->update(['assigned_to' => $request->input('assigned_to')]);

        return back()->with('success', 'Ticket assigned successfully!');
    }

    public function updateStatus(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $request->validate(['status' => 'required|in:open,in_progress,waiting_customer,resolved,closed']);

        $this->ticketService->updateStatus($ticket, $request->input('status'), auth()->user());

        return back()->with('success', 'Ticket status updated!');
    }

    public function addMessage(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $this->ticketService->addMessage($ticket, $validated['message'], auth()->user());

        return back()->with('success', 'Reply posted successfully!');
    }

    public function getMessages(string $id)
    {
        $messages = TicketMessage::where('ticket_id', $id)->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
}
