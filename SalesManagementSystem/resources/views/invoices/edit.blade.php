@extends('layouts.app')

@section('title', 'Edit ' . $invoice->number)
@section('page-title', 'Edit Invoice')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">Invoices</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invoices.show', $invoice->id) }}">{{ $invoice->number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Edit: {{ $invoice->number }}</h4>
                    <p class="text-muted mb-0">Update due date, payment status, and customer notes</p>
                </div>
                <div>
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Invoice</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-info-circle me-2"></i> Invoice Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="draft" {{ old('status', $invoice->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ old('status', $invoice->status) === 'sent' ? 'selected' : '' }}>Sent / Unpaid</option>
                                <option value="partial" {{ old('status', $invoice->status) === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="paid" {{ old('status', $invoice->status) === 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="overdue" {{ old('status', $invoice->status) === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="cancelled" {{ old('status', $invoice->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Payment Due Date <span class="text-danger">*</span></label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Customer Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Terms & Conditions</label>
                            <textarea name="terms" class="form-control" rows="3">{{ old('terms', $invoice->terms) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
