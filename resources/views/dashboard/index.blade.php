@extends('app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <!-- Total Sales -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Sales</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">৳{{ number_format($totalSales, 2) }}</h4>
                            </div>
                            <small class="text-success">(Lifetime)</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti tabler-currency-taka ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Sales -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Today's Sales</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">৳{{ number_format($todaySales, 2) }}</h4>
                            </div>
                            <small class="text-success">({{ \Carbon\Carbon::now()->format('d M') }})</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti tabler-chart-bar ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Orders</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format($totalOrders) }}</h4>
                            </div>
                            <small class="text-muted">Invoices generated</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti tabler-shopping-cart ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Total Customers</span>
                            <div class="d-flex align-items-end mt-2">
                                <h4 class="mb-0 me-2">{{ number_format($totalCustomers) }}</h4>
                            </div>
                            <small class="text-muted">Registered customers</small>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti tabler-users ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Sales Chart -->
        <div class="col-xl-8 col-12">
            <div class="card h-100">
                <div class="card-header header-elements">
                    <h5 class="card-title mb-0">Sales Overview</h5>
                    <div class="card-action-element ms-auto py-0">
                        <div class="dropdown">
                            <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical text-muted"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="javascript:void(0);">Last 7 Days</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="salesChart"></div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-xl-4 col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Top Selling Products</h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @forelse($topProducts as $product)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-device-mobile"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $product->product_name }}</h6>
                                    <small class="text-muted">Mobile</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $product->total_sold }} Sold</small>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted">No sales data yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Sales -->
        <div class="col-xl-8 col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Sales</h5>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale->id) }}">#{{ $sale->invoice_number }}</a></td>
                                <td>{{ $sale->customer ? $sale->customer->full_name : 'Walk-in Customer' }}</td>
                                <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                <td>৳{{ number_format($sale->total_amount, 2) }}</td>
                                <td>
                                    @if($sale->payment_status == 'PAID')
                                        <span class="badge bg-label-success">Paid</span>
                                    @elseif($sale->payment_status == 'PARTIAL')
                                        <span class="badge bg-label-warning">Partial</span>
                                    @else
                                        <span class="badge bg-label-danger">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No recent sales found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="col-xl-4 col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Low Stock Alerts</h5>
                    <span class="badge bg-danger">{{ $lowStockProducts->count() }} Items</span>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @forelse($lowStockProducts as $product)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger"><i class="ti ti-alert-triangle"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $product->product_name }}</h6>
                                    <small class="text-muted">Stock: {{ $product->current_stock }}</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold text-danger">Alert: {{ $product->min_stock_level }}</small>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted">No low stock items.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const salesChartEl = document.querySelector('#salesChart');
    if (salesChartEl) {
        const salesChartConfig = {
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            series: [{
                name: 'Sales',
                data: @json($salesChartData)
            }],
            xaxis: {
                categories: @json($dates),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return '৳' + val;
                    }
                }
            },
            colors: ['#7367F0'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                padding: { top: -20, bottom: -10 }
            }
        };
        const salesChart = new ApexCharts(salesChartEl, salesChartConfig);
        salesChart.render();
    }
});
</script>
@endpush
