@extends('layouts.app')

@section('title', 'Products & Services')
@section('page-title', 'Product Catalog')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Products</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold text-dark">Products & Service Catalog</h4>
        <p class="text-muted mb-0">Manage service pricing, SKU codes, and quotation line items</p>
    </div>
    <div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Product
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4">Product / Service</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Unit Price</th>
                        <th>Unit Type</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold text-dark">{{ $prod->name }}</div>
                            <small class="text-muted">{{ $prod->description ?? '—' }}</small>
                        </td>
                        <td class="small text-muted">{{ $prod->sku ?? '—' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $prod->category ?? 'General' }}</span>
                        </td>
                        <td class="fw-bold text-success fs-6">${{ number_format($prod->unit_price, 2) }}</td>
                        <td class="small text-muted">{{ $prod->unit ?? 'Unit' }}</td>
                        <td>
                            <span class="badge {{ $prod->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $prod->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('products.edit', $prod->id) }}"><i class="far fa-edit text-warning me-2"></i> Edit</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Delete product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="far fa-trash-alt me-2"></i> Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-boxes fa-3x mb-3 text-secondary opacity-50"></i>
                            <h5>No products registered</h5>
                            <p class="small mb-3">Add pricing items to streamline quotation creation</p>
                            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Add Product</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
