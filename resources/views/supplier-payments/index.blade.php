@extends('app')

@section('title', 'Supplier Payments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance /</span> Supplier Payments</h4>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Supplier Payments</h5>
            <a href="{{ route('finance.supplier-payments.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-1"></i> Record Payment
            </a>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" action="{{ route('finance.supplier-payments.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
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
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti tabler-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('finance.supplier-payments.index') }}" class="btn btn-secondary">
                                <i class="ti tabler-x me-1"></i> Clear
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Summary -->
            <div class="alert alert-info">
                <strong>Total Paid:</strong> ৳ {{ number_format($totalPaid, 2) }}
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>PO Number</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                <td><strong>{{ $payment->supplier->supplier_name }}</strong></td>
                                <td>{{ $payment->purchaseOrder->po_number ?? '-' }}</td>
                                <td><strong>৳ {{ number_format($payment->amount, 2) }}</strong></td>
                                <td><span class="badge bg-info">{{ $payment->payment_method }}</span></td>
                                <td>{{ $payment->reference_number ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('finance.supplier-payments.show', $payment) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="ti tabler-eye"></i>
                                    </a>
                                    <a href="{{ route('finance.supplier-payments.voucher', $payment) }}" class="btn btn-sm btn-primary" title="Voucher" target="_blank">
                                        <i class="ti tabler-file-invoice"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No supplier payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
