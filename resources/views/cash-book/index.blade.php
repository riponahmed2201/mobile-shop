@extends('app')

@section('title', 'Cash Book')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance /</span> Cash Book</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Cash Transactions</h5>
            <a href="{{ route('finance.cash-book.create') }}" class="btn btn-primary btn-sm">
                <i class="ti tabler-plus me-1"></i> Add Transaction
            </a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('finance.cash-book.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Transaction Type</label>
                        <select name="transaction_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="SALE" {{ request('transaction_type') == 'SALE' ? 'selected' : '' }}>Sale</option>
                            <option value="PURCHASE" {{ request('transaction_type') == 'PURCHASE' ? 'selected' : '' }}>Purchase</option>
                            <option value="EXPENSE" {{ request('transaction_type') == 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                            <option value="PAYMENT_RECEIVED" {{ request('transaction_type') == 'PAYMENT_RECEIVED' ? 'selected' : '' }}>Payment Received</option>
                            <option value="PAYMENT_MADE" {{ request('transaction_type') == 'PAYMENT_MADE' ? 'selected' : '' }}>Payment Made</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-label-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="mb-1">Opening Balance</p>
                                    <h5 class="mb-0">৳ {{ number_format($openingBalance, 2) }}</h5>
                                </div>
                                <i class="ti tabler-wallet fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-label-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="mb-1">Total Inflow</p>
                                    <h5 class="mb-0">৳ {{ number_format($totalInflow, 2) }}</h5>
                                </div>
                                <i class="ti tabler-arrow-down-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-label-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="mb-1">Total Outflow</p>
                                    <h5 class="mb-0">৳ {{ number_format($totalOutflow, 2) }}</h5>
                                </div>
                                <i class="ti tabler-arrow-up-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-label-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="mb-1">Closing Balance</p>
                                    <h5 class="mb-0">৳ {{ number_format($closingBalance, 2) }}</h5>
                                </div>
                                <i class="ti tabler-cash fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Payment Method</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningBalance = $openingBalance; @endphp
                        @forelse($transactions as $transaction)
                            @php
                                $isCredit = in_array($transaction->transaction_type, ['SALE', 'PAYMENT_RECEIVED', 'OPENING_BALANCE']);
                                $runningBalance += $isCredit ? $transaction->amount : -$transaction->amount;
                            @endphp
                            <tr>
                                <td>{{ $transaction->transaction_date->format('d M Y H:i') }}</td>
                                <td><span class="badge bg-{{ $isCredit ? 'success' : 'danger' }}">{{ $transaction->transaction_type }}</span></td>
                                <td>{{ $transaction->description }}</td>
                                <td>{{ $transaction->payment_method ?? '-' }}</td>
                                <td class="text-end">{{ $isCredit ? '' : number_format($transaction->amount, 2) }}</td>
                                <td class="text-end">{{ $isCredit ? number_format($transaction->amount, 2) : '' }}</td>
                                <td class="text-end"><strong>{{ number_format($runningBalance, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No transactions found for the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
