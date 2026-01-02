@extends('app')

@section('title', 'Profit & Loss Statement')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance /</span> Profit & Loss Statement</h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Profit & Loss Report</h5>
        </div>
        <div class="card-body">
            <!-- Date Filter -->
            <form method="GET" action="{{ route('finance.profit-loss.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-filter me-1"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Report Period -->
            <div class="alert alert-info">
                <strong>Report Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>

            <!-- Revenue Section -->
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">REVENUE</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total Sales Revenue</strong></td>
                            <td class="text-end"><strong>৳ {{ number_format($totalSalesRevenue, 2) }}</strong></td>
                        </tr>
                        @foreach($salesByType as $sale)
                            <tr>
                                <td class="ps-4">{{ $sale->sale_type }} Sales</td>
                                <td class="text-end">৳ {{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <!-- Cost of Goods Sold -->
            <div class="card mb-3">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">COST OF GOODS SOLD (COGS)</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Total COGS</strong></td>
                            <td class="text-end"><strong>৳ {{ number_format($cogs, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Gross Profit -->
            <div class="card mb-3">
                <div class="card-header {{ $grossProfit >= 0 ? 'bg-primary' : 'bg-danger' }} text-white">
                    <h6 class="mb-0">GROSS PROFIT</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Gross Profit (Revenue - COGS)</strong></td>
                            <td class="text-end"><strong>৳ {{ number_format($grossProfit, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Operating Expenses -->
            <div class="card mb-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">OPERATING EXPENSES</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        @foreach($expensesByCategory as $expense)
                            <tr>
                                <td>{{ $expense->category_name }}</td>
                                <td class="text-end">৳ {{ number_format($expense->total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-active">
                            <td><strong>Total Expenses</strong></td>
                            <td class="text-end"><strong>৳ {{ number_format($totalExpenses, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="card mb-3">
                <div class="card-header {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                    <h5 class="mb-0">NET PROFIT / LOSS</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Net Profit (Gross Profit - Expenses)</strong></td>
                            <td class="text-end">
                                <h4 class="mb-0 {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳ {{ number_format($netProfit, 2) }}
                                </h4>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Profit Margin</strong></td>
                            <td class="text-end">
                                <strong class="{{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($profitMargin, 2) }}%
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Summary -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-label-success">
                        <div class="card-body text-center">
                            <p class="mb-1">Total Revenue</p>
                            <h4 class="mb-0">৳ {{ number_format($totalSalesRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-label-danger">
                        <div class="card-body text-center">
                            <p class="mb-1">Total Expenses</p>
                            <h4 class="mb-0">৳ {{ number_format($totalExpenses + $cogs, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-label-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                        <div class="card-body text-center">
                            <p class="mb-1">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</p>
                            <h4 class="mb-0">৳ {{ number_format(abs($netProfit), 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
