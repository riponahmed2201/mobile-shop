@extends('app')

@section('title', 'Sales Report')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><span class="text-muted fw-light">Reports /</span> Sales Report</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="ti tabler-printer me-1"></i> Print
                </button>
                <form action="{{ route('reports.sales.export') }}" method="GET" class="d-inline">
                    <input type="hidden" name="date_from" value="{{ $filters['date_from'] }}">
                    <input type="hidden" name="date_to" value="{{ $filters['date_to'] }}">
                    <input type="hidden" name="customer_id" value="{{ $filters['customer_id'] }}">
                    <input type="hidden" name="product_id" value="{{ $filters['product_id'] }}">
                    <input type="hidden" name="brand_id" value="{{ $filters['brand_id'] }}">
                    <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
                    <input type="hidden" name="sale_type" value="{{ $filters['sale_type'] }}">
                    <input type="hidden" name="payment_method" value="{{ $filters['payment_method'] }}">
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
                <form action="{{ route('reports.sales') }}" method="GET" id="filterForm">
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
                            <label class="form-label" for="customer_id">Customer</label>
                            <select class="form-select select2" id="customer_id" name="customer_id">
                                <option value="">All Customers</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ $filters['customer_id'] == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->full_name }}
                                        {{ $customer->customer_code ? ' (' . $customer->customer_code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="product_id">Product</label>
                            <select class="form-select select2" id="product_id" name="product_id">
                                <option value="">All Products</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $filters['product_id'] == $product->id ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                        {{ $product->model_name ? ' - ' . $product->model_name : '' }}
                                    </option>
                                @endforeach
                            </select>
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
                        <div class="col-md-3">
                            <label class="form-label" for="sale_type">Sale Type</label>
                            <select class="form-select" id="sale_type" name="sale_type">
                                <option value="">All Types</option>
                                <option value="REGULAR" {{ $filters['sale_type'] == 'REGULAR' ? 'selected' : '' }}>Regular
                                </option>
                                <option value="WHOLESALE" {{ $filters['sale_type'] == 'WHOLESALE' ? 'selected' : '' }}>
                                    Wholesale</option>
                                <option value="B2B" {{ $filters['sale_type'] == 'B2B' ? 'selected' : '' }}>B2B</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="payment_method">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method">
                                <option value="">All Methods</option>
                                <option value="CASH" {{ $filters['payment_method'] == 'CASH' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="CARD" {{ $filters['payment_method'] == 'CARD' ? 'selected' : '' }}>Card
                                </option>
                                <option value="MOBILE_BANKING"
                                    {{ $filters['payment_method'] == 'MOBILE_BANKING' ? 'selected' : '' }}>Mobile Banking
                                </option>
                                <option value="EMI" {{ $filters['payment_method'] == 'EMI' ? 'selected' : '' }}>EMI
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-search me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.sales') }}" class="btn btn-secondary">
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
                                    <i class="ti tabler-currency-taka"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Revenue</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['total_revenue'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <p
                            class="mb-0 small {{ $reportData['statistics']['revenue_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i
                                class="ti {{ $reportData['statistics']['revenue_growth'] >= 0 ? 'tabler-trending-up' : 'tabler-trending-down' }}"></i>
                            <span
                                class="fw-medium">{{ number_format(abs($reportData['statistics']['revenue_growth']), 1) }}%</span>
                            vs previous period
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
                                    <i class="ti tabler-shopping-cart"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Sales</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['total_sales']) }}</h4>
                            </div>
                        </div>
                        <p
                            class="mb-0 small {{ $reportData['statistics']['sales_growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i
                                class="ti {{ $reportData['statistics']['sales_growth'] >= 0 ? 'tabler-trending-up' : 'tabler-trending-down' }}"></i>
                            <span
                                class="fw-medium">{{ number_format(abs($reportData['statistics']['sales_growth']), 1) }}%</span>
                            vs previous period
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
                                    <i class="ti tabler-chart-line"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Avg Order Value</p>
                                <h4 class="mb-0">
                                    ৳{{ number_format($reportData['statistics']['average_order_value'], 2) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-box"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['total_quantity']) }}</span>
                            items sold
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
                                <p class="mb-0 text-muted small">Total Profit</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['total_profit'], 2) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-percentage"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['profit_margin'], 1) }}%</span>
                            profit margin
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
                                    <i class="ti tabler-users"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Unique Customers</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['unique_customers']) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-secondary small">
                            <i class="ti tabler-user-check"></i>
                            <span class="fw-medium">Active buyers</span>
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
                                    <i class="ti tabler-cash"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Cash Sales</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['cash_sales'], 2) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <span class="fw-medium">Payment method</span>
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
                                    <i class="ti tabler-credit-card"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Card Sales</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['card_sales'], 2) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <span class="fw-medium">Payment method</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti tabler-device-mobile"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Mobile Banking</p>
                                <h4 class="mb-0">
                                    ৳{{ number_format($reportData['statistics']['mobile_banking_sales'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-primary small">
                            <span class="fw-medium">Payment method</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-3 mb-4">
            <!-- Daily Sales Trend -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Daily Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyTrendChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Methods</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                                <th>Sales Count</th>
                                <th>Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['topProducts'] as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $item['product']['product_name'] ?? 'N/A' }}
                                        @if (isset($item['product']['model_name']))
                                            <br><small class="text-muted">{{ $item['product']['model_name'] }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item['total_quantity']) }}</td>
                                    <td>{{ number_format($item['sales_count']) }}</td>
                                    <td>৳{{ number_format($item['total_revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sales Details Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Sales Details</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="salesTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Payment</th>
                                <th>Items</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['sales'] as $index => $sale)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $sale->invoice_number }}</strong></td>
                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td>
                                        {{ $sale->customer ? $sale->customer->full_name : 'Walk-in Customer' }}
                                        @if ($sale->customer && $sale->customer->mobile_primary)
                                            <br><small class="text-muted">{{ $sale->customer->mobile_primary }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $sale->sale_type }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $sale->payment_method }}</span>
                                    </td>
                                    <td>{{ $sale->items->count() }}</td>
                                    <td>{{ number_format($sale->total_quantity) }}</td>
                                    <td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown">
                                                <i class="ti tabler-dots-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('sales.show', $sale->id) }}">
                                                    <i class="ti tabler-eye me-2"></i>View
                                                </a>
                                                <a class="dropdown-item" href="{{ route('sales.invoice', $sale->id) }}"
                                                    target="_blank">
                                                    <i class="ti tabler-printer me-2"></i>Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No sales found for the selected filters</td>
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

            // Daily Trend Chart
            const dailyTrendData = @json($reportData['dailyTrend']);
            const dailyLabels = dailyTrendData.map(item => new Date(item.date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short'
            }));
            const dailyRevenue = dailyTrendData.map(item => item.revenue);
            const dailySalesCount = dailyTrendData.map(item => item.sales_count);

            new Chart(document.getElementById('dailyTrendChart'), {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Revenue (৳)',
                        data: dailyRevenue,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        yAxisID: 'y',
                        tension: 0.3
                    }, {
                        label: 'Sales Count',
                        data: dailySalesCount,
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
                                text: 'Sales Count'
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        }
                    }
                }
            });

            // Payment Methods Chart
            const paymentMethodsData = @json($reportData['paymentMethods']);
            const paymentLabels = paymentMethodsData.map(item => item.payment_method);
            const paymentAmounts = paymentMethodsData.map(item => item.total_amount);

            new Chart(document.getElementById('paymentMethodsChart'), {
                type: 'doughnut',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        data: paymentAmounts,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
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
        });

        function printReport() {
            window.print();
        }
    </script>
@endpush
