@extends('app')

@section('title', 'Customer Report')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><span class="text-muted fw-light">Reports /</span> Customer Report</h4>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="ti tabler-printer me-1"></i> Print
                </button>
                <form action="{{ route('reports.customers.export') }}" method="GET" class="d-inline">
                    <input type="hidden" name="date_from" value="{{ $filters['date_from'] }}">
                    <input type="hidden" name="date_to" value="{{ $filters['date_to'] }}">
                    <input type="hidden" name="customer_type" value="{{ $filters['customer_type'] }}">
                    <input type="hidden" name="is_active" value="{{ $filters['is_active'] }}">
                    <input type="hidden" name="min_purchases" value="{{ $filters['min_purchases'] }}">
                    <input type="hidden" name="city" value="{{ $filters['city'] }}">
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
                <form action="{{ route('reports.customers') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="date_from">Registered From</label>
                            <input type="date" class="form-control" id="date_from" name="date_from"
                                value="{{ $filters['date_from'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="date_to">Registered To</label>
                            <input type="date" class="form-control" id="date_to" name="date_to"
                                value="{{ $filters['date_to'] }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="customer_type">Customer Type</label>
                            <select class="form-select" id="customer_type" name="customer_type">
                                <option value="">All Types</option>
                                <option value="NEW" {{ $filters['customer_type'] == 'NEW' ? 'selected' : '' }}>New
                                </option>
                                <option value="REGULAR" {{ $filters['customer_type'] == 'REGULAR' ? 'selected' : '' }}>
                                    Regular</option>
                                <option value="VIP" {{ $filters['customer_type'] == 'VIP' ? 'selected' : '' }}>VIP
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="is_active">Status</label>
                            <select class="form-select" id="is_active" name="is_active">
                                <option value="">All</option>
                                <option value="active" {{ $filters['is_active'] == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ $filters['is_active'] == 'inactive' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="min_purchases">Min. Purchase Amount</label>
                            <input type="number" class="form-control" id="min_purchases" name="min_purchases"
                                value="{{ $filters['min_purchases'] }}" placeholder="0.00" step="0.01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city"
                                value="{{ $filters['city'] }}" placeholder="Enter city">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-search me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('reports.customers') }}" class="btn btn-secondary">
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
                                    <i class="ti tabler-users"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Customers</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['total_customers']) }}</h4>
                            </div>
                        </div>
                        <p
                            class="mb-0 small {{ $reportData['statistics']['growth_rate'] >= 0 ? 'text-success' : 'text-danger' }}">
                            <i
                                class="ti {{ $reportData['statistics']['growth_rate'] >= 0 ? 'tabler-trending-up' : 'tabler-trending-down' }}"></i>
                            <span
                                class="fw-medium">{{ number_format(abs($reportData['statistics']['growth_rate']), 1) }}%</span>
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
                                    <i class="ti tabler-user-check"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Active Customers</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['active_customers']) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-calendar"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['active_this_month']) }}</span>
                            active this month
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
                                <p class="mb-0 text-muted small">Total Revenue</p>
                                <h4 class="mb-0">৳{{ number_format($reportData['statistics']['total_revenue'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-chart-line"></i>
                            <span
                                class="fw-medium">৳{{ number_format($reportData['statistics']['avg_purchase_per_customer'], 2) }}</span>
                            avg per customer
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
                                    <i class="ti tabler-crown"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Top Customer</p>
                                <h4 class="mb-0">
                                    ৳{{ number_format($reportData['statistics']['top_customer_spending'], 2) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-trophy"></i>
                            <span class="fw-medium">Highest spending</span>
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
                                    <i class="ti tabler-shopping-cart"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">With Purchases</p>
                                <h4 class="mb-0">
                                    {{ number_format($reportData['statistics']['customers_with_purchases']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-primary small">
                            <i class="ti tabler-percentage"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['purchase_rate'], 1) }}%</span>
                            purchase rate
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
                                    <i class="ti tabler-user-plus"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">New Customers</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['new_customers']) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-calendar-plus"></i>
                            <span
                                class="fw-medium">{{ number_format($reportData['statistics']['recent_acquisitions']) }}</span>
                            last 30 days
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
                                    <i class="ti tabler-user-star"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Regular Customers</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['regular_customers']) }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-repeat"></i>
                            <span class="fw-medium">Repeat buyers</span>
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
                                    <i class="ti tabler-diamond"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">VIP Customers</p>
                                <h4 class="mb-0">{{ number_format($reportData['statistics']['vip_customers']) }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-star"></i>
                            <span class="fw-medium">Premium segment</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-3 mb-4">
            <!-- Customer Acquisition Trend -->
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Acquisition Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="acquisitionChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Customers by Type -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Customers by Type</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="customerTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Customers by Purchase Amount</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Type</th>
                                <th>Total Purchases</th>
                                <th>Sales Count</th>
                                <th>Last Purchase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['topCustomers'] as $index => $customer)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $customer['full_name'] }}</strong>
                                        @if (isset($customer['customer_code']))
                                            <br><small class="text-muted">{{ $customer['customer_code'] }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $customer['mobile_primary'] ?? 'N/A' }}
                                        @if (isset($customer['email']))
                                            <br><small class="text-muted">{{ $customer['email'] }}</small>
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge bg-label-{{ $customer['customer_type'] == 'VIP' ? 'warning' : ($customer['customer_type'] == 'REGULAR' ? 'info' : 'primary') }}">{{ $customer['customer_type'] }}</span>
                                    </td>
                                    <td><strong>৳{{ number_format($customer['total_purchases'], 2) }}</strong></td>
                                    <td>{{ number_format($customer['sales_count']) }} sales</td>
                                    <td>
                                        @if (isset($customer['last_purchase_date']) && $customer['last_purchase_date'])
                                            {{ \Carbon\Carbon::parse($customer['last_purchase_date'])->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Customers & Inactive Customers -->
        <div class="row g-3 mb-4">
            <!-- Recent Customers -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Customers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['recentCustomers'] as $customer)
                                        <tr>
                                            <td>
                                                {{ $customer['full_name'] }}<br>
                                                <small
                                                    class="text-muted">{{ $customer['mobile_primary'] ?? 'N/A' }}</small>
                                            </td>
                                            <td><span
                                                    class="badge bg-label-primary">{{ $customer['customer_type'] }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($customer['created_at'])->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No recent customers</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inactive Customers -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Inactive Customers (90+ days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Last Purchase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reportData['inactiveCustomers'] as $customer)
                                        <tr>
                                            <td>
                                                {{ $customer['full_name'] }}<br>
                                                <small
                                                    class="text-muted">{{ $customer['mobile_primary'] ?? 'N/A' }}</small>
                                            </td>
                                            <td><span
                                                    class="badge bg-label-warning">{{ $customer['customer_type'] }}</span>
                                            </td>
                                            <td>
                                                @if (isset($customer['last_purchase_date']) && $customer['last_purchase_date'])
                                                    <span
                                                        class="text-danger">{{ \Carbon\Carbon::parse($customer['last_purchase_date'])->format('d M Y') }}</span>
                                                @else
                                                    <span class="text-muted">Never</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No inactive customers</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Customers Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Customers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="customersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>City</th>
                                <th>Type</th>
                                <th>Total Purchases</th>
                                <th>Loyalty Points</th>
                                <th>Last Purchase</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['customers'] as $index => $customer)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $customer->full_name }}</strong>
                                        @if ($customer->customer_code)
                                            <br><small class="text-muted">{{ $customer->customer_code }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $customer->mobile_primary ?? '-' }}
                                        @if ($customer->email)
                                            <br><small class="text-muted">{{ $customer->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $customer->city ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-label-{{ $customer->customer_type == 'VIP' ? 'warning' : ($customer->customer_type == 'REGULAR' ? 'info' : 'primary') }}">
                                            {{ $customer->customer_type }}
                                        </span>
                                    </td>
                                    <td><strong>৳{{ number_format($customer->total_purchases, 2) }}</strong></td>
                                    <td>{{ number_format($customer->loyalty_points ?? 0) }}</td>
                                    <td>
                                        @if ($customer->last_purchase_date)
                                            {{ $customer->last_purchase_date->format('d M Y') }}
                                        @else
                                            <span class="text-muted">Never</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($customer->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No customers found</td>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Customer Acquisition Trend Chart
            const acquisitionData = @json($reportData['acquisitionTrend']);
            const acquisitionLabels = acquisitionData.map(item => new Date(item.date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short'
            }));
            const acquisitionCounts = acquisitionData.map(item => item.customer_count);

            new Chart(document.getElementById('acquisitionChart'), {
                type: 'line',
                data: {
                    labels: acquisitionLabels,
                    datasets: [{
                        label: 'New Customers',
                        data: acquisitionCounts,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Customer Type Chart
            const typeData = @json($reportData['customersByType']);
            const typeLabels = typeData.map(item => item.customer_type);
            const typeRevenue = typeData.map(item => item.total_revenue);

            new Chart(document.getElementById('customerTypeChart'), {
                type: 'doughnut',
                data: {
                    labels: typeLabels,
                    datasets: [{
                        data: typeRevenue,
                        backgroundColor: [
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
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
