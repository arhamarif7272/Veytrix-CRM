@extends('layouts.app')

@section('title', 'Add Product')
@section('page-title', 'Create Product / Service')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">New Product</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Add Catalog Item</h4>
                    <p class="text-muted mb-0">Record software licenses, subscription plans, and consulting rates</p>
                </div>
                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Product</button>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-box me-2"></i> Item Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Product / Service Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Cloud Infrastructure Audit" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SKU / Item Code</label>
                            <input type="text" name="sku" class="form-control" placeholder="SRV-AUDIT-01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <input type="text" name="category" class="form-control" placeholder="e.g. Consulting, SaaS, Support">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="2500.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit Type</label>
                            <input type="text" name="unit" class="form-control" placeholder="e.g. Monthly, Per Seat, Flat">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Detailed Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Deliverables included in this item..."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch" checked>
                                <label class="form-check-label fw-medium" for="activeSwitch">Active in catalog</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Save Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
