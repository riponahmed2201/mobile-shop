@extends('app')

@section('title', 'Out of Stock Products')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Inventory / Low Stock Alerts /</span>
        Out of Stock Products
        <span class="badge bg-danger ms-2">{{ count($outOfStockProducts) }}</span>
    </h4>

    <!-- Alert Banner -->
    <div class="alert alert-danger d-flex align-items-center mb-4">
        <i class="ti tabler-x fs-2 me-3"></i>
        <div>
            <h6 class="alert-heading mb-1">Out of Stock Alert</h6>
            <p class="mb-0">These products have zero stock. They cannot be sold until restocked. Immediate action is required.</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('low-stock.index') }}" class="btn btn-outline-primary">
                    <i class="ti tabler-arrow-left me-1"></i> Back to All Alerts
                </a>
                <a href="{{ route('stock-adjustments.create') }}" class="btn btn-danger">
                    <i class="ti tabler-plus me-1"></i> Add Stock (Urgent)
                </a>
                <a href="{{ route('purchase.create') }}" class="btn btn-success">
                    <i class="ti tabler-truck-delivery me-1"></i> Create Purchase Order
                </a>
            </div>
        </div>
    </div>

    @if($outOfStockProducts->count() > 0)
    <div class="row">
        @foreach($outOfStockProducts as $product)
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
                        <span class="badge bg-danger">Out of Stock</span>
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
                            <div class="progress-bar bg-danger" style="width: 0%"></div>
                        </div>
                        <small class="text-danger mt-1 d-block fw-bold">
                            <i class="ti tabler-alert-triangle me-1"></i> Product is completely out of stock
                        </small>
                    </div>

                    <div class="alert alert-danger py-2 mb-3">
                        <small class="mb-0">
                            <strong>Last Sale:</strong> Unable to sell this product<br>
                            <strong>Status:</strong> Sales blocked until restocked
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti tabler-eye me-1"></i> View
                        </a>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ti tabler-pencil me-1"></i> Edit
                        </a>
                        <a href="{{ route('stock-adjustments.create') }}?product_id={{ $product->id }}" class="btn btn-sm btn-danger">
                            <i class="ti tabler-plus me-1"></i> Add Stock
                        </a>
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
            <h5 class="text-success">All Restocked!</h5>
            <p class="text-muted mb-3">No products are currently out of stock.</p>
            <a href="{{ route('low-stock.index') }}" class="btn btn-primary">
                <i class="ti tabler-arrow-left me-1"></i> Back to Low Stock Alerts
            </a>
        </div>
    </div>
    @endif

    <!-- Summary Card -->
    @if($outOfStockProducts->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-danger bg-opacity-10 border-danger">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-danger mb-2">Business Impact Summary</h6>
                            <ul class="text-danger mb-0 small">
                                <li>{{ $outOfStockProducts->count() }} products cannot be sold</li>
                                <li>Potential revenue loss from unavailable inventory</li>
                                <li>Customer dissatisfaction from stockouts</li>
                                <li>Urgent restocking required to resume sales</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <h3 class="text-danger mb-2">{{ $outOfStockProducts->count() }}</h3>
                            <p class="text-danger mb-0">Products Affected</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
