@extends('app')

@section('title', 'Critical Stock Alerts')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Inventory / Low Stock Alerts /</span>
        Critical Stock Alerts
        <span class="badge bg-danger ms-2">{{ count($criticalProducts) }}</span>
    </h4>

    <!-- Alert Banner -->
    <div class="alert alert-danger d-flex align-items-center mb-4">
        <i class="ti tabler-alert-triangle fs-2 me-3"></i>
        <div>
            <h6 class="alert-heading mb-1">Critical Stock Alert</h6>
            <p class="mb-0">These products are at or below their minimum stock levels. Immediate action is required to prevent stockouts.</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('low-stock.index') }}" class="btn btn-outline-primary">
                    <i class="ti tabler-arrow-left me-1"></i> Back to All Alerts
                </a>
                <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-1"></i> Add Stock Adjustment
                </a>
                <a href="{{ route('purchase.create') }}" class="btn btn-success">
                    <i class="ti tabler-truck-delivery me-1"></i> Create Purchase Order
                </a>
            </div>
        </div>
    </div>

    @if($criticalProducts->count() > 0)
    <div class="row">
        @foreach($criticalProducts as $product)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="card-title mb-1">{{ $product->product_name }}</h6>
                            <small class="text-muted">
                                @if($product->brand)
                                    {{ $product->brand->brand_name }}
                                @endif
                                @if($product->model_name)
                                    | {{ $product->model_name }}
                                @endif
                            </small>
                        </div>
                        <span class="badge bg-danger">Critical</span>
                    </div>

                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="text-danger mb-0">{{ $product->current_stock }}</h4>
                                <small class="text-muted">Current</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="text-primary mb-0">{{ $product->min_stock_level }}</h4>
                                <small class="text-muted">Minimum</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h4 class="text-warning mb-0">{{ $product->reorder_level }}</h4>
                                <small class="text-muted">Reorder</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ min(100, ($product->current_stock / $product->min_stock_level) * 100) }}%"></div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Stock level: {{ round(($product->current_stock / $product->min_stock_level) * 100, 1) }}% of minimum
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti tabler-eye me-1"></i> View
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti tabler-pencil me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showStockDetails({{ $product->id }})">
                            <i class="ti tabler-info-circle me-1"></i> Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="ti tabler-check-circle fs-1 text-success mb-3"></i>
            <h5 class="text-success">All Clear!</h5>
            <p class="text-muted mb-3">No products are currently at critical stock levels.</p>
            <a href="{{ route('low-stock.index') }}" class="btn btn-primary">
                <i class="ti tabler-arrow-left me-1"></i> Back to Low Stock Alerts
            </a>
        </div>
    </div>
    @endif

    <!-- Stock Details Modal -->
    <div class="modal fade" id="stockDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Stock Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="stockDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script>
function showStockDetails(productId) {
    $.get("{{ route('low-stock.details') }}", { product_id: productId })
        .done(function(response) {
            if (response.success) {
                const product = response.data.product;
                const stockDetails = response.data.stock_details;

                let content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Product Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Name:</strong></td><td>${product.product_name}</td></tr>
                                <tr><td><strong>Model:</strong></td><td>${product.model_name || '-'}</td></tr>
                                <tr><td><strong>Brand:</strong></td><td>${product.brand ? product.brand.brand_name : '-'}</td></tr>
                                <tr><td><strong>Category:</strong></td><td>${product.category ? product.category.category_name : '-'}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Stock Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Current Stock:</strong></td><td>${stockDetails.current_stock} ${product.unit}</td></tr>
                                <tr><td><strong>Min Level:</strong></td><td>${stockDetails.min_stock_level} ${product.unit}</td></tr>
                                <tr><td><strong>Reorder Level:</strong></td><td>${stockDetails.reorder_level} ${product.unit}</td></tr>
                                <tr><td><strong>Status:</strong></td><td><span class="badge bg-${stockDetails.severity}">${stockDetails.status.replace('_', ' ')}</span></td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-${stockDetails.severity}">
                                <strong>Alert:</strong> ${stockDetails.message}
                                ${stockDetails.suggested_reorder > 0 ? '<br><strong>Suggested reorder quantity:</strong> ' + stockDetails.suggested_reorder + ' ' + product.unit : ''}
                            </div>
                        </div>
                    </div>
                `;

                $('#stockDetailsContent').html(content);
                $('#stockDetailsModal').modal('show');
            } else {
                alert('Failed to load stock details: ' + response.message);
            }
        })
        .fail(function() {
            alert('Failed to load stock details. Please try again.');
        });
}
</script>
@endpush
