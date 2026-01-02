@extends('app')

@section('title', 'Expense Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance / Expenses /</span> Details</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Expense Details</h5>
            <div>
                <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-primary btn-sm">
                    <i class="ti tabler-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ti tabler-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Category:</strong>
                    <p>{{ $expense->category->category_name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Date:</strong>
                    <p>{{ $expense->expense_date->format('d M Y') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Amount:</strong>
                    <p class="text-danger"><strong>৳ {{ number_format($expense->amount, 2) }}</strong></p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Payment Method:</strong>
                    <p><span class="badge bg-info">{{ $expense->payment_method }}</span></p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Reference Number:</strong>
                    <p>{{ $expense->reference_number ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Created By:</strong>
                    <p>{{ $expense->createdBy->username ?? '-' }}</p>
                </div>
                <div class="col-12 mb-3">
                    <strong>Description:</strong>
                    <p>{{ $expense->description ?? '-' }}</p>
                </div>
                @if($expense->receipt_file_url)
                    <div class="col-12 mb-3">
                        <strong>Receipt/Invoice:</strong>
                        <p>
                            <a href="{{ Storage::url($expense->receipt_file_url) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ti tabler-file me-1"></i> View Receipt
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
