@extends('app')

@section('title', 'Expenses')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance /</span> Expenses</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Expense List</h5>
            <a href="{{ route('finance.expenses.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-1"></i> Add Expense
            </a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('finance.expenses.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <option value="CASH" {{ request('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="CARD" {{ request('payment_method') == 'CARD' ? 'selected' : '' }}>Card</option>
                            <option value="BANK" {{ request('payment_method') == 'BANK' ? 'selected' : '' }}>Bank</option>
                            <option value="BKASH" {{ request('payment_method') == 'BKASH' ? 'selected' : '' }}>bKash</option>
                            <option value="NAGAD" {{ request('payment_method') == 'NAGAD' ? 'selected' : '' }}>Nagad</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti tabler-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('finance.expenses.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-x me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>

            <!-- Summary -->
            <div class="alert alert-info">
                <strong>Total Expenses:</strong> ৳ {{ number_format($totalExpenses, 2) }}
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                <td>{{ $expense->category->category_name }}</td>
                                <td><strong>৳ {{ number_format($expense->amount, 2) }}</strong></td>
                                <td><span class="badge bg-info">{{ $expense->payment_method }}</span></td>
                                <td>{{ $expense->reference_number ?? '-' }}</td>
                                <td>{{ Str::limit($expense->description, 50) }}</td>
                                <td>
                                    <a href="{{ route('finance.expenses.show', $expense) }}" class="btn btn-sm btn-info">
                                        <i class="ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-sm btn-primary">
                                        <i class="ti tabler-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $expenses->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
