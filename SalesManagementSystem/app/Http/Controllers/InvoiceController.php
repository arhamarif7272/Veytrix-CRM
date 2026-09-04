<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\AuditService;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function index(Request $request)
    {
        $query = Invoice::query();

        if ($search = $request->input('search')) {
            $query->where('number', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get()->keyBy(fn($c) => (string) $c->id);

        return view('invoices.index', compact('invoices', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $deals = Deal::all();
        $products = Product::where('is_active', true)->get();
        $selectedCustomer = $request->input('customer_id');

        return view('invoices.create', compact('customers', 'deals', 'products', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|string',
            'deal_id'        => 'nullable|string',
            'due_date'       => 'required|date',
            'tax_rate'       => 'nullable|numeric|min:0',
            'discount_type'  => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'notes'          => 'nullable|string',
            'terms'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ($item['quantity'] * $item['unit_price']);
        }

        $discount = 0;
        if (($validated['discount_type'] ?? '') === 'percentage') {
            $discount = $subtotal * (floatval($validated['discount_value'] ?? 0) / 100);
        } else {
            $discount = floatval($validated['discount_value'] ?? 0);
        }

        $taxable = max(0, $subtotal - $discount);
        $tax = $taxable * (floatval($validated['tax_rate'] ?? 0) / 100);
        $total = $taxable + $tax;

        $invoice = Invoice::create([
            'number'          => $this->invoiceService->generateNumber(),
            'customer_id'     => $validated['customer_id'],
            'deal_id'         => $validated['deal_id'] ?? null,
            'created_by'      => auth()->id(),
            'status'          => Invoice::STATUS_SENT,
            'items'           => $validated['items'],
            'subtotal'        => round($subtotal, 2),
            'tax_rate'        => floatval($validated['tax_rate'] ?? 0),
            'tax_amount'      => round($tax, 2),
            'discount_type'   => $validated['discount_type'] ?? 'fixed',
            'discount_value'  => floatval($validated['discount_value'] ?? 0),
            'discount_amount' => round($discount, 2),
            'total'           => round($total, 2),
            'amount_paid'     => 0,
            'amount_due'      => round($total, 2),
            'currency'        => 'USD',
            'due_date'        => $validated['due_date'],
            'notes'           => $validated['notes'] ?? null,
            'terms'           => $validated['terms'] ?? null,
            'sent_at'         => now(),
        ]);

        AuditService::log(
            action: 'invoice.created',
            module: 'invoices',
            entityType: 'Invoice',
            entityId: (string) $invoice->id,
            entityLabel: $invoice->number,
            newValues: $invoice->toArray()
        );

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Invoice #{$invoice->number} created successfully!");
    }

    public function show(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $customer = Customer::find($invoice->customer_id);
        $deal = $invoice->deal_id ? Deal::find($invoice->deal_id) : null;

        return view('invoices.show', compact('invoice', 'customer', 'deal'));
    }

    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $deals = Deal::all();

        return view('invoices.edit', compact('invoice', 'customers', 'deals'));
    }

    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'due_date' => 'required|date',
            'status'   => 'required|in:draft,sent,paid,overdue,partial,cancelled',
            'notes'    => 'nullable|string',
            'terms'    => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Invoice #{$invoice->number} updated successfully!");
    }

    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $number = $invoice->number;

        AuditService::log(
            action: 'invoice.deleted',
            module: 'invoices',
            entityType: 'Invoice',
            entityId: (string) $invoice->id,
            entityLabel: $number,
            oldValues: $invoice->toArray()
        );

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', "Invoice #{$number} deleted.");
    }

    public function send(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'status'  => Invoice::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return back()->with('success', "Invoice #{$invoice->number} marked as SENT to customer!");
    }

    public function recordPayment(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'amount'            => 'required|numeric|min:0.01|max:' . ($invoice->amount_due ?: $invoice->total),
            'payment_method'    => 'required|string|in:credit_card,bank_transfer,cash,stripe,paypal,check',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $this->invoiceService->recordPayment(
            invoice: $invoice,
            amount: floatval($validated['amount']),
            paymentMethod: $validated['payment_method'],
            reference: $validated['payment_reference'] ?? null,
            user: auth()->user()
        );

        return back()->with('success', "Payment of \${$validated['amount']} recorded successfully! 💳");
    }

    public function pdf(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $customer = Customer::find($invoice->customer_id);

        if (class_exists(Pdf::class)) {
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'customer'));
            return $pdf->stream("Invoice-{$invoice->number}.pdf");
        }

        return view('invoices.pdf', compact('invoice', 'customer'));
    }

    public function myIndex()
    {
        $user = auth()->user();
        $customer = Customer::where('email', $user->email)->first();
        $customerIds = array_values(array_unique(array_filter([(string) $user->id, $customer ? (string) $customer->id : null])));
        $invoices = Invoice::whereIn('customer_id', $customerIds)->orderBy('created_at', 'desc')->paginate(10);

        return view('invoices.my', compact('invoices'));
    }

    public function myShow(string $id)
    {
        $user = auth()->user();
        $customer = Customer::where('email', $user->email)->first();
        $customerIds = array_values(array_unique(array_filter([(string) $user->id, $customer ? (string) $customer->id : null])));
        $invoice = Invoice::where('_id', $id)->whereIn('customer_id', $customerIds)->firstOrFail();
        $customerObj = Customer::find($invoice->customer_id) ?? $customer;

        return view('invoices.show', ['invoice' => $invoice, 'customer' => $customerObj, 'deal' => null]);
    }
}
