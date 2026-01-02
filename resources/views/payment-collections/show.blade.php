@extends('app')

@section('title', 'Payment Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance / Payment Collections /</span> Details</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment Details</h5>
            <div>
                <a href="{{ route('finance.payment-collections.receipt', $paymentCollection) }}" class="btn btn-primary btn-sm" target="_blank">
                    <i class="ti tabler-file-invoice me-1"></i> Print Receipt
                </a>
                <a href="{{ route('finance.payment-collections.index') }}" class="btn btn-secondary btn-sm">
                    <i class="ti tabler-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Customer:</strong>
                    <p>{{ $paymentCollection->customer->full_name }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Payment Date:</strong>
                    <p>{{ $paymentCollection->payment_date->format('d M Y') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Amount:</strong>
                    <p class="text-success"><strong>৳ {{ number_format($paymentCollection->amount, 2) }}</strong></p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Payment Method:</strong>
                    <p><span class="badge bg-success">{{ $paymentCollection->payment_method }}</span></p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Sale Invoice:</strong>
                    <p>{{ $paymentCollection->sale->invoice_number ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Reference Number:</strong>
                    <p>{{ $paymentCollection->reference_number ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Collected By:</strong>
                    <p>{{ $paymentCollection->collectedBy->username ?? '-' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Created At:</strong>
                    <p>{{ $paymentCollection->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="col-12 mb-3">
                    <strong>Notes:</strong>
                    <p>{{ $paymentCollection->notes ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
