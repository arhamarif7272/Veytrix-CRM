@extends('layouts.app')

@section('title', 'Add Deal')
@section('page-title', 'Create Sales Opportunity')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('deals.index') }}">Deals</a></li>
    <li class="breadcrumb-item active">New Deal</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form action="{{ route('deals.store') }}" method="POST">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Create Sales Deal</h4>
                    <p class="text-muted mb-0">Record potential contract value, customer association, and timeline</p>
                </div>
                <div>
                    <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Deal</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-handshake me-2"></i> Deal Specifics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deal Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Acme Enterprise ERP License - 200 Users" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Associated Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">Select Customer Account</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ (old('customer_id', $selectedCustomer) == $c->id) ? 'selected' : '' }}>{{ $c->name }} ({{ $c->company ?? 'Direct' }})</option>
                                @endforeach
                            </select>
                            @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Initial Stage <span class="text-danger">*</span></label>
                            <select name="stage_id" class="form-select" required>
                                @foreach($stages as $stg)
                                    <option value="{{ $stg->id }}" {{ old('stage_id') == $stg->id ? 'selected' : '' }}>{{ $stg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Deal Value ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}" placeholder="25000" required>
                            @error('value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Win Probability (%)</label>
                            <input type="number" min="0" max="100" name="probability" class="form-control" value="{{ old('probability', 50) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Target Close Date</label>
                            <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', now()->addDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Deal Owner</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Assign to Me</option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ old('assigned_to') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deal Scope & Notes</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Terms discussed, competitors, pricing considerations...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('deals.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Deal</button>
            </div>
        </form>
    </div>
</div>
@endsection
