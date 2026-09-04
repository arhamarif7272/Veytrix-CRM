<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Quotation;
use App\Services\AuditService;
use App\Services\QuotationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct(private QuotationService $quotationService) {}

    public function index(Request $request)
    {
        $query = Quotation::query();

        if ($search = $request->input('search')) {
            $query->where('number', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        $quotations = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $customers = Customer::orderBy('name')->get()->keyBy(fn($c) => (string) $c->id);

        return view('quotations.index', compact('quotations', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $deals = Deal::open()->get();
        $products = Product::where('is_active', true)->get();
        $selectedCustomer = $request->input('customer_id');
        $selectedDeal = $request->input('deal_id');

        return view('quotations.create', compact('customers', 'deals', 'products', 'selectedCustomer', 'selectedDeal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|string',
            'deal_id'        => 'nullable|string',
            'valid_until'    => 'nullable|date',
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

        $quotation = $this->quotationService->createQuotation($validated, auth()->user());

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', "Quotation #{$quotation->number} created successfully!");
    }

    public function show(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $customer = Customer::find($quotation->customer_id);
        $deal = $quotation->deal_id ? Deal::find($quotation->deal_id) : null;

        return view('quotations.show', compact('quotation', 'customer', 'deal'));
    }

    public function edit(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $customers = Customer::orderBy('name')->get();
        $deals = Deal::all();
        $products = Product::where('is_active', true)->get();

        return view('quotations.edit', compact('quotation', 'customers', 'deals', 'products'));
    }

    public function update(Request $request, string $id)
    {
        $quotation = Quotation::findOrFail($id);

        $validated = $request->validate([
            'customer_id'    => 'required|string',
            'deal_id'        => 'nullable|string',
            'valid_until'    => 'nullable|date',
            'status'         => 'required|in:draft,sent,accepted,rejected,expired',
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

        $this->quotationService->updateQuotation($quotation, $validated, auth()->user());

        return redirect()->route('quotations.show', $quotation->id)
            ->with('success', "Quotation #{$quotation->number} updated successfully!");
    }

    public function destroy(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $number = $quotation->number;

        AuditService::log(
            action: 'quotation.deleted',
            module: 'quotations',
            entityType: 'Quotation',
            entityId: (string) $quotation->id,
            entityLabel: $number,
            oldValues: $quotation->toArray()
        );

        $quotation->delete();

        return redirect()->route('quotations.index')
            ->with('success', "Quotation #{$number} has been deleted.");
    }

    public function send(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $quotation->update([
            'status'  => Quotation::STATUS_SENT,
            'sent_at' => now(),
        ]);

        return back()->with('success', "Quotation #{$quotation->number} marked as SENT to customer!");
    }

    public function convertToInvoice(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $invoice = $this->quotationService->convertToInvoice($quotation, auth()->user());

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Quotation successfully converted to Invoice #{$invoice->number}!");
    }

    public function pdf(string $id)
    {
        $quotation = Quotation::findOrFail($id);
        $customer = Customer::find($quotation->customer_id);

        if (class_exists(Pdf::class)) {
            $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'customer'));
            return $pdf->stream("Quotation-{$quotation->number}.pdf");
        }

        return view('quotations.pdf', compact('quotation', 'customer'));
    }

    // Customer Portal routes
    public function myIndex()
    {
        $user = auth()->user();
        $customer = Customer::where('email', $user->email)->first();
        $customerIds = array_values(array_unique(array_filter([(string) $user->id, $customer ? (string) $customer->id : null])));
        $quotations = Quotation::whereIn('customer_id', $customerIds)->orderBy('created_at', 'desc')->paginate(10);

        return view('quotations.my', compact('quotations'));
    }

    public function myShow(string $id)
    {
        $user = auth()->user();
        $customer = Customer::where('email', $user->email)->first();
        $customerIds = array_values(array_unique(array_filter([(string) $user->id, $customer ? (string) $customer->id : null])));
        $quotation = Quotation::where('_id', $id)->whereIn('customer_id', $customerIds)->firstOrFail();
        $customerObj = Customer::find($quotation->customer_id) ?? $customer;

        return view('quotations.show', ['quotation' => $quotation, 'customer' => $customerObj, 'deal' => null]);
    }
}
