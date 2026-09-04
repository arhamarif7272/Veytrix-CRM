@extends('layouts.app')

@section('title', 'Edit ' . $quotation->number)
@section('page-title', 'Edit Quotation')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
    <li class="breadcrumb-item"><a href="{{ route('quotations.show', $quotation->id) }}">{{ $quotation->number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<form action="{{ route('quotations.update', $quotation->id) }}" method="POST" id="quotationForm">
    @csrf
    @method('PUT')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Edit: {{ $quotation->number }}</h4>
            <p class="text-muted mb-0">Modify line items, discount, tax rate, and validity</p>
        </div>
        <div>
            <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Quotation</button>
        </div>
    </div>

    <!-- Meta Details Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0">
            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-info-circle me-2"></i> Quotation Metadata</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Customer Account <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $quotation->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Associated Deal (Optional)</label>
                    <select name="deal_id" class="form-select">
                        <option value="">None / Standalone</option>
                        @foreach($deals as $d)
                            <option value="{{ $d->id }}" {{ old('deal_id', $quotation->deal_id) == $d->id ? 'selected' : '' }}>{{ $d->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status', $quotation->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ old('status', $quotation->status) === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="accepted" {{ old('status', $quotation->status) === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ old('status', $quotation->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="expired" {{ old('status', $quotation->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Valid Until</label>
                    <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quotation->valid_until ? $quotation->valid_until->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tax Rate (%)</label>
                    <input type="number" step="0.1" name="tax_rate" id="tax_rate" class="form-control" value="{{ old('tax_rate', $quotation->tax_rate) }}" oninput="calculateTotals()">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Discount Type & Value</label>
                    <div class="input-group">
                        <select name="discount_type" id="discount_type" class="form-select" style="max-width: 130px;" onchange="calculateTotals()">
                            <option value="percentage" {{ old('discount_type', $quotation->discount_type) === 'percentage' ? 'selected' : '' }}>% Percent</option>
                            <option value="fixed" {{ old('discount_type', $quotation->discount_type) === 'fixed' ? 'selected' : '' }}>$ Fixed</option>
                        </select>
                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control" value="{{ old('discount_value', $quotation->discount_value) }}" oninput="calculateTotals()">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-list-ol me-2"></i> Line Items</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                <i class="fas fa-plus me-1"></i> Add Line Item
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="bg-light small text-muted text-uppercase">
                        <tr>
                            <th style="width: 35%;">Item / Service</th>
                            <th style="width: 25%;">Description</th>
                            <th style="width: 12%;">Qty</th>
                            <th style="width: 15%;">Unit Price ($)</th>
                            <th style="width: 13%;">Total ($)</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @foreach($quotation->items ?? [['name' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0]] as $index => $item)
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[{{ $index }}][name]" class="form-control" value="{{ $item['name'] ?? '' }}" required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item['description'] ?? '' }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[{{ $index }}][quantity]" class="form-control item-qty" value="{{ $item['quantity'] ?? 1 }}" required oninput="calculateTotals()">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control item-price" value="{{ $item['unit_price'] ?? 0 }}" required oninput="calculateTotals()">
                            </td>
                            <td>
                                <input type="text" class="form-control item-total fw-bold bg-light" value="{{ number_format(($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0), 2) }}" readonly>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm text-danger p-0" onclick="removeRow(this)"><i class="far fa-trash-alt"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-bold" id="subtotalDisplay">${{ number_format($quotation->subtotal, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-muted">Discount:</span>
                            <span class="text-danger fw-semibold" id="discountDisplay">-${{ number_format($quotation->discount_amount, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-muted">Tax Amount:</span>
                            <span class="fw-semibold" id="taxDisplay">${{ number_format($quotation->tax_amount, 2) }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-top fs-5">
                            <span class="fw-bold text-dark">Grand Total:</span>
                            <span class="fw-bold text-primary" id="totalDisplay">${{ number_format($quotation->total, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes & Terms -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Client Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $quotation->notes) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="3">{{ old('terms', $quotation->terms) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Quotation</button>
    </div>
</form>

@push('scripts')
<script>
let rowIndex = {{ count($quotation->items ?? [1]) }};

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td><input type="text" name="items[${rowIndex}][name]" class="form-control" placeholder="Product or Service Name" required></td>
        <td><input type="text" name="items[${rowIndex}][description]" class="form-control" placeholder="Details/Specs"></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][quantity]" class="form-control item-qty" value="1" min="0.01" required oninput="calculateTotals()"></td>
        <td><input type="number" step="0.01" name="items[${rowIndex}][unit_price]" class="form-control item-price" value="0.00" min="0" required oninput="calculateTotals()"></td>
        <td><input type="text" class="form-control item-total fw-bold bg-light" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-0" onclick="removeRow(this)"><i class="far fa-trash-alt"></i></button></td>
    `;
    tbody.appendChild(tr);
    rowIndex++;
}

function removeRow(button) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        button.closest('tr').remove();
        calculateTotals();
    } else {
        alert('Quotation must contain at least one line item.');
    }
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const total = qty * price;
        row.querySelector('.item-total').value = total.toFixed(2);
        subtotal += total;
    });

    const discountType = document.getElementById('discount_type').value;
    const discountVal = parseFloat(document.getElementById('discount_value').value) || 0;
    let discountAmount = discountType === 'percentage' ? (subtotal * (discountVal / 100)) : discountVal;
    discountAmount = Math.min(discountAmount, subtotal);

    const taxable = Math.max(0, subtotal - discountAmount);
    const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
    const taxAmount = taxable * (taxRate / 100);

    const total = taxable + taxAmount;

    document.getElementById('subtotalDisplay').innerText = '$' + subtotal.toFixed(2);
    document.getElementById('discountDisplay').innerText = '-$' + discountAmount.toFixed(2);
    document.getElementById('taxDisplay').innerText = '$' + taxAmount.toFixed(2);
    document.getElementById('totalDisplay').innerText = '$' + total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endpush
@endsection
