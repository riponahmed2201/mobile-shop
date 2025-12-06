@extends('app')

@section('title', 'Product Performance Report')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><span class="text-muted fw-light">Reports /</span> Product Performance</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="ti tabler-printer me-1"></i> Print
                </button>
                <form action="{{ route('reports.product-performance.export') }}" method="GET" class="d-inline">
                    <input type="hidden" name="date_from" value="{{ $filters['date_from'] }}">
                    <input type="hidden" name="date_to" value="{{ $filters['date_to'] }}">
                    <input type="hidden" name="brand_id" value="{{ $filters['brand_id'] }}">
                    <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
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
                <form action="{{ route('reports.product-performance') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="date_from">Date From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ $filters['date_from'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="date_to">Date To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ $filters['date_to'] }}">
                        </div>
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
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-search me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.product-performance') }}" class="btn btn-secondary">
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
                                    <i class="ti tabler-shopping-cart"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Products Sold</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['total_products_sold']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-primary small">
                            <i class="ti tabler-box"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['total_quantity_sold']) }}</span>
                            total units
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
                                    <i class="ti tabler-currency-taka"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Revenue</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['total_revenue'], 2) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-chart-line"></i>
                            <span
                                class="fw-medium">৳{{ number_format($reportData['statistics']['avg_revenue_per_product'], 2) }}</span>
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
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti tabler-wallet"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Profit</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['total_profit'], 2) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-percentage"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['avg_profit_margin'], 1) }}%</span>
                            avg margin
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
                                    <i class="ti tabler-package"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Unique Products</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['unique_products_sold']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-chart-dots"></i>
                            <span class="fw-medium">of
                                {{ number_format($reportData['statistics']['total_active_products']) }}</span>
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
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti tabler-alert-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Not Sold</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['products_not_sold']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-package-off"></i>
                            <span class="fw-medium">Products with no sales</span>
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
                                    <i class="ti tabler-chart-bar"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Avg Sales/Product</p>
                                <h4 class="mb-0">
                                    {{ number_format($reportData['statistics']['avg_sales_per_product'], 1) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-secondary small">
                            <i class="ti tabler-trending-up"></i>
                            <span class="fw-medium">Average performance</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-3 mb-4">
            <!-- Performance Trend -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Sales Performance Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceTrendChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance by Category -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Revenue by Category</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance by Brand -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Performance by Brand</h5>
            </div>
            <div class="card-body">
                <canvas id="brandChart" height="80"></canvas>
            </div>
        </div>

        <!-- Best Sellers & Most Profitable -->
        <div class="row g-3 mb-4">
            <!-- Best Selling Products -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Best Selling Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Qty Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['bestSellers'] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item['product']['product_name'] ?? 'N/A' }}<br>
                                                <small
                                                    class="text-muted">{{ $item['product']['model_name'] ?? '' }}</small>
                                            </td>
                                            <td><strong>{{ number_format($item['total_quantity']) }}</strong></td>
                                            <td>৳{{ number_format($item['total_revenue'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Profitable Products -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Most Profitable Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Profit</th>
                                        <th>Margin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['mostProfitable'] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item['product']['product_name'] ?? 'N/A' }}<br>
                                                <small
                                                    class="text-muted">{{ $item['product']['model_name'] ?? '' }}</small>
                                            </td>
                                            <td><strong>৳{{ number_format($item['total_profit'], 2) }}</strong></td>
                                            <td><span
                                                    class="badge bg-label-success">{{ number_format($item['profit_margin'], 1) }}%</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Worst Performers & Low Turnover -->
        <div class="row g-3 mb-4">
            <!-- Worst Performing Products -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Worst Performing Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Qty Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['worstPerformers'] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item['product']['product_name'] ?? 'N/A' }}<br>
                                                <small
                                                    class="text-muted">{{ $item['product']['model_name'] ?? '' }}</small>
                                            </td>
                                            <td><span
                                                    class="badge bg-label-danger">{{ number_format($item['total_quantity']) }}</span>
                                            </td>
                                            <td>৳{{ number_format($item['total_revenue'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Turnover Products -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Low Turnover Products (30 days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Sales</th>
                                        <th>Turnover</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['lowTurnover'] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                {{ $item['product']['product_name'] ?? 'N/A' }}<br>
                                                <small class="text-muted">Stock:
                                                    {{ $item['product']['current_stock'] ?? 0 }}</small>
                                            </td>
                                            <td>{{ number_format($item['sales_last_30_days']) }}</td>
                                            <td><span
                                                    class="badge bg-label-warning">{{ number_format($item['turnover_rate'], 1) }}%</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Products Performance Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Products Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="performanceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Qty Sold</th>
                                <th>Sales Count</th>
                                <th>Revenue</th>
                                <th>Profit</th>
                                <th>Margin %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['productsPerformance'] as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item['product']['product_name'] ?? 'N/A' }}</strong>
                                        @if (isset($item['product']['model_name']))
                                            <br><small class="text-muted">{{ $item['product']['model_name'] }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item['product']['brand']['brand_name'] ?? '-' }}</td>
                                    <td>{{ $item['product']['category']['category_name'] ?? '-' }}</td>
                                    <td><strong>{{ number_format($item['total_quantity']) }}</strong></td>
                                    <td>{{ number_format($item['sales_count']) }}</td>
                                    <td>৳{{ number_format($item['total_revenue'], 2) }}</td>
                                    <td>
                                        <strong
                                            class="{{ isset($item['total_profit']) && $item['total_profit'] > 0 ? 'text-success' : 'text-danger' }}">
                                            ৳{{ number_format($item['total_profit'] ?? 0, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ isset($item['profit_margin']) && $item['profit_margin'] > 20 ? 'success' : ($item['profit_margin'] > 10 ? 'info' : 'warning') }}">
                                            {{ number_format($item['profit_margin'] ?? 0, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No products sold in this period</td>
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

            // Performance Trend Chart
            const trendData = @json($reportData['performanceTrend']);
            const trendLabels = trendData.map(item => new Date(item.date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short'
            }));
            const trendRevenue = trendData.map(item => item.revenue);
            const trendQuantity = trendData.map(item => item.quantity);

            new Chart(document.getElementById('performanceTrendChart'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Revenue (৳)',
                        data: trendRevenue,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        yAxisID: 'y',
                        tension: 0.3
                    }, {
                        label: 'Quantity',
                        data: trendQuantity,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        yAxisID: 'y1',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Revenue (৳)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Quantity'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryData = @json($reportData['performanceByCategory']);
            const categoryLabels = categoryData.map(item => item.category_name);
            const categoryRevenue = categoryData.map(item => item.total_revenue);

            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryRevenue,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
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

            // Brand Chart
            const brandData = @json($reportData['performanceByBrand']);
            const brandLabels = brandData.map(item => item.brand_name);
            const brandRevenue = brandData.map(item => item.total_revenue);

            new Chart(document.getElementById('brandChart'), {
                type: 'bar',
                data: {
                    labels: brandLabels,
                    datasets: [{
                        label: 'Revenue (৳)',
                        data: brandRevenue,
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
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
                                    return '৳' + context.parsed.x.toLocaleString();
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
