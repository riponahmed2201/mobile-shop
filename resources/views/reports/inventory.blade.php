@extends('app')

@section('title', 'Inventory Report')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><span class="text-muted fw-light">Reports /</span> Inventory Report</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="ti tabler-printer me-1"></i> Print
                </button>
                <form action="{{ route('reports.inventory.export') }}" method="GET" class="d-inline">
                    <input type="hidden" name="date_from" value="{{ $filters['date_from'] }}">
                    <input type="hidden" name="date_to" value="{{ $filters['date_to'] }}">
                    <input type="hidden" name="brand_id" value="{{ $filters['brand_id'] }}">
                    <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
                    <input type="hidden" name="product_type" value="{{ $filters['product_type'] }}">
                    <input type="hidden" name="stock_status" value="{{ $filters['stock_status'] }}">
                    <input type="hidden" name="is_active" value="{{ $filters['is_active'] }}">
                    <button type="submit" class="btn btn-success">
                        <i class="ti tabler-download me-1"></i> Export CSV
                    </button>
                </form>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ti tabler-filter me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reports.inventory') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="brand_id">Brand</label>
                            <select class="form-select select2" id="brand_id" name="brand_id">
                                <option value="">All Brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ $filters['brand_id'] == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->brand_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="category_id">Category</label>
                            <select class="form-select select2" id="category_id" name="category_id">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $filters['category_id'] == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="product_type">Product Type</label>
                            <select class="form-select" id="product_type" name="product_type">
                                <option value="">All Types</option>
                                <option value="MOBILE" {{ $filters['product_type'] == 'MOBILE' ? 'selected' : '' }}>
                                    Mobile</option>
                                <option value="ACCESSORY" {{ $filters['product_type'] == 'ACCESSORY' ? 'selected' : '' }}>
                                    Accessory</option>
                                <option value="PARTS" {{ $filters['product_type'] == 'PARTS' ? 'selected' : '' }}>Parts
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="stock_status">Stock Status</label>
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">All Status</option>
                                <option value="in_stock" {{ $filters['stock_status'] == 'in_stock' ? 'selected' : '' }}>In
                                    Stock</option>
                                <option value="out_of_stock"
                                    {{ $filters['stock_status'] == 'out_of_stock' ? 'selected' : '' }}>Out of Stock
                                </option>
                                <option value="low_stock" {{ $filters['stock_status'] == 'low_stock' ? 'selected' : '' }}>
                                    Low Stock</option>
                                <option value="critical_stock"
                                    {{ $filters['stock_status'] == 'critical_stock' ? 'selected' : '' }}>Critical Stock
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="is_active">Product Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="">All</option>
                                <option value="active" {{ $filters['is_active'] == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ $filters['is_active'] == 'inactive' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="date_from">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ $filters['date_from'] }}">
                            <small class="text-muted">For stock movement</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="date_to">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ $filters['date_to'] }}">
                            <small class="text-muted">For stock movement</small>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-search me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-secondary">
                                <i class="ti tabler-refresh me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti tabler-package"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Products</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['total_products']) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-primary small">
                            <i class="ti tabler-check-circle"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['active_products']) }}</span>
                            active products
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti tabler-box"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Stock Qty</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['total_stock_quantity']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-package-import"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['in_stock_count']) }}</span>
                            products in stock
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti tabler-currency-taka"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Stock Value</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['total_stock_value'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-chart-line"></i>
                            <span
                                class="fw-medium">৳{{ number_format($reportData['statistics']['avg_stock_value_per_product'], 2) }}</span>
                            avg per product
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti tabler-wallet"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Potential Revenue</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['potential_revenue'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-trending-up"></i>
                            <span
                                class="fw-medium">৳{{ number_format($reportData['statistics']['potential_profit'], 2) }}</span>
                            potential profit
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti tabler-alert-triangle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Out of Stock</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['out_of_stock_count']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-alert-circle"></i>
                            <span class="fw-medium">Urgent restocking needed</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti tabler-exclamation-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Critical Stock</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['critical_stock_count']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-arrow-down"></i>
                            <span class="fw-medium">Below minimum level</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti tabler-minus"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Low Stock</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['low_stock_count']) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-package-minus"></i>
                            <span class="fw-medium">Below reorder level</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="ti tabler-refresh"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Turnover Rate</p>
                                <h4 class="mb-0">
                                    {{ number_format($reportData['statistics']['stock_turnover_rate'], 1) }}%
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-secondary small">
                            <i class="ti tabler-calendar"></i>
                            <span class="fw-medium">Last 30 days</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-3 mb-4">
            <!-- Stock by Category -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Stock Value by Category</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <!-- Stock by Brand -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Stock Value by Brand</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="brandChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Stock Value Products -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Products by Stock Value</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Current Stock</th>
                                <th>Purchase Price</th>
                                <th>Stock Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['topStockValue'] as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $product['product_name'] }}
                                        @if (isset($product['model_name']))
                                            <br><small class="text-muted">{{ $product['model_name'] }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $product['brand']['brand_name'] ?? 'N/A' }}</td>
                                    <td>{{ number_format($product['current_stock']) }} {{ $product['unit'] }}</td>
                                    <td>৳{{ number_format($product['purchase_price'], 2) }}</td>
                                    <td><strong>৳{{ number_format($product['stock_value'], 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Low Stock Alert</h5>
                <a href="{{ route('low-stock.index') }}" class="btn btn-sm btn-danger">
                    <i class="ti tabler-alert-triangle me-1"></i>View All Alerts
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Current Stock</th>
                                <th>Min Level</th>
                                <th>Reorder Level</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['lowStockProducts'] as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $product['product_name'] }}
                                        @if (isset($product['model_name']))
                                            <br><small class="text-muted">{{ $product['model_name'] }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $product['brand']['brand_name'] ?? 'N/A' }}</td>
                                    <td><span
                                            class="badge bg-warning">{{ number_format($product['current_stock']) }}</span>
                                    </td>
                                    <td>{{ number_format($product['min_stock_level']) }}</td>
                                    <td>{{ number_format($product['reorder_level']) }}</td>
                                    <td>
                                        @if ($product['current_stock'] <= $product['min_stock_level'])
                                            <span class="badge bg-danger">Critical</span>
                                        @else
                                            <span class="badge bg-warning">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No low stock products</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- All Products Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Products Inventory</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="inventoryTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Type</th>
                                <th>Stock</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Stock Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['products'] as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $product->product_name }}</strong>
                                        @if ($product->model_name)
                                            <br><small class="text-muted">{{ $product->model_name }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $product->brand ? $product->brand->brand_name : '-' }}</td>
                                    <td>{{ $product->category ? $product->category->category_name : '-' }}</td>
                                    <td><span class="badge bg-label-primary">{{ $product->product_type }}</span></td>
                                    <td>
                                        <strong>{{ number_format($product->current_stock) }}</strong>
                                        {{ $product->unit }}
                                        @if ($product->current_stock <= 0)
                                            <br><span class="badge bg-danger">Out</span>
                                        @elseif($product->current_stock <= $product->min_stock_level)
                                            <br><span class="badge bg-danger">Critical</span>
                                        @elseif($product->current_stock <= $product->reorder_level)
                                            <br><span class="badge bg-warning">Low</span>
                                        @else
                                            <br><span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                    <td>৳{{ number_format($product->purchase_price, 2) }}</td>
                                    <td>৳{{ number_format($product->selling_price, 2) }}</td>
                                    <td><strong>৳{{ number_format($product->current_stock * $product->purchase_price, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if ($product->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No products found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_css')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <style>
        @media print {

            .btn,
            .card-header,
            .breadcrumb,
            aside,
            nav,
            .menu {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush

@push('page_js')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select...",
                allowClear: true,
                width: '100%'
            });

            // Stock by Category Chart
            const categoryData = @json($reportData['stockByCategory']);
            const categoryLabels = categoryData.map(item => item.category ? item.category.category_name :
                'Uncategorized');
            const categoryValues = categoryData.map(item => item.total_value);

            new Chart(document.getElementById('categoryChart'), {
                type: 'bar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Stock Value (৳)',
                        data: categoryValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.8)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '৳' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '৳' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Stock by Brand Chart
            const brandData = @json($reportData['stockByBrand']);
            const brandLabels = brandData.map(item => item.brand ? item.brand.brand_name : 'Unknown');
            const brandValues = brandData.map(item => item.total_value);

            new Chart(document.getElementById('brandChart'), {
                type: 'doughnut',
                data: {
                    labels: brandLabels,
                    datasets: [{
                        data: brandValues,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(199, 199, 199, 0.8)',
                            'rgba(83, 102, 255, 0.8)',
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ৳' + context.parsed.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });

        function printReport() {
            window.print();
        }
    </script>
@endpush
