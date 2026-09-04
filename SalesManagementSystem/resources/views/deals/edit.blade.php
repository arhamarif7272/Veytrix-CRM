@extends('layouts.app')

@section('title', 'Edit ' . $deal->title)
@section('page-title', 'Edit Deal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('deals.index') }}">Deals</a></li>
    <li class="breadcrumb-item"><a href="{{ route('deals.show', $deal->id) }}">{{ $deal->title }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <form action="{{ route('deals.update', $deal->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Edit: {{ $deal->title }}</h4>
                    <p class="text-muted mb-0">Update deal status, value, and expected close timeline</p>
                </div>
                <div>
                    <a href="{{ route('deals.show', $deal->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Update Deal</button>
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
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $deal->title) }}" required>
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Customer Account <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id', $deal->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pipeline Stage <span class="text-danger">*</span></label>
                            <select name="stage_id" class="form-select" required>
                                @foreach($stages as $stg)
                                    <option value="{{ $stg->id }}" {{ old('stage_id', $deal->stage_id) == $stg->id ? 'selected' : '' }}>{{ $stg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Deal Value ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="value" class="form-control" value="{{ old('value', $deal->value) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Win Probability (%)</label>
                            <input type="number" min="0" max="100" name="probability" class="form-control" value="{{ old('probability', $deal->probability) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="open" {{ old('status', $deal->status) === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="won" {{ old('status', $deal->status) === 'won' ? 'selected' : '' }}>Won</option>
                                <option value="lost" {{ old('status', $deal->status) === 'lost' ? 'selected' : '' }}>Lost</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Expected Close Date</label>
                            <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', $deal->expected_close_date ? $deal->expected_close_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Deal Owner</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Select Rep</option>
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}" {{ old('assigned_to', $deal->assigned_to) == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deal Notes</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $deal->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('deals.show', $deal->id) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Update Deal</button>
            </div>
        </form>
    </div>
</div>
@endsection
