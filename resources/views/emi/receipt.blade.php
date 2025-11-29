@extends('app')

@section('title', 'Installment Receipt')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="invoice-print p-5">
        <div class="d-flex justify-content-between flex-row mb-4">
            <div class="mb-4">
                <h3 class="mb-2">INSTALLMENT RECEIPT</h3>
                <p class="mb-1">Mobile Shop Inc.</p>
                <p class="mb-1">123 Business Street</p>
                <p class="mb-0">Dhaka, Bangladesh</p>
            </div>
            <div>
                <h4 class="mb-2">Receipt #{{ $installment->id }}</h4>
                <p class="mb-1">Date: {{ $installment->payment_date ? $installment->payment_date->format('d M Y') : now()->format('d M Y') }}</p>
                <p class="mb-1">EMI Plan: #{{ $installment->emiPlan->id }}</p>
            </div>
        </div>

        <hr class="my-4">

        <div class="row mb-4">
            <div class="col-sm-6">
                <h5>Customer Information:</h5>
                <p class="mb-1"><strong>Name:</strong> {{ $installment->emiPlan->customer->full_name }}</p>
                <p class="mb-1"><strong>Mobile:</strong> {{ $installment->emiPlan->customer->mobile_primary }}</p>
                @if($installment->emiPlan->customer->email)
                <p class="mb-0"><strong>Email:</strong> {{ $installment->emiPlan->customer->email }}</p>
                @endif
            </div>
            <div class="col-sm-6">
                <h5>Payment Details:</h5>
                <p class="mb-1"><strong>Installment #:</strong> {{ $installment->installment_number }} of {{ $installment->emiPlan->number_of_installments }}</p>
                <p class="mb-1"><strong>Due Date:</strong> {{ $installment->due_date->format('d M Y') }}</p>
                <p class="mb-1"><strong>Payment Date:</strong> {{ $installment->payment_date ? $installment->payment_date->format('d M Y') : 'N/A' }}</p>
                <p class="mb-0"><strong>Payment Method:</strong> {{ $installment->payment_method ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Installment Amount ({{ $installment->installment_number }}/{{ $installment->emiPlan->number_of_installments }})</td>
                        <td class="text-end">৳{{ number_format($installment->amount, 2) }}</td>
                    </tr>
                    @if($installment->paid_amount != $installment->amount)
                    <tr>
                        <td>Adjustment</td>
                        <td class="text-end">৳{{ number_format($installment->paid_amount - $installment->amount, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total Paid</th>
                        <th class="text-end">৳{{ number_format($installment->paid_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mb-4">
            <div class="col-sm-6">
                <h5>EMI Summary:</h5>
                <p class="mb-1"><strong>Total Amount:</strong> ৳{{ number_format($installment->emiPlan->total_amount, 2) }}</p>
                <p class="mb-1"><strong>Paid Installments:</strong> {{ $installment->emiPlan->paid_installments }} / {{ $installment->emiPlan->number_of_installments }}</p>
                <p class="mb-0"><strong>Remaining Amount:</strong> ৳{{ number_format($installment->emiPlan->remaining_amount, 2) }}</p>
            </div>
            <div class="col-sm-6">
                @if($installment->notes)
                <h5>Notes:</h5>
                <p class="mb-0">{{ $installment->notes }}</p>
                @endif
            </div>
        </div>

        @if($installment->emiPlan->remaining_amount > 0)
        <div class="alert alert-info">
            <strong>Next Payment Due:</strong>
            @php
                $nextInstallment = $installment->emiPlan->installments->where('status', 'PENDING')->first();
            @endphp
            @if($nextInstallment)
                Installment #{{ $nextInstallment->installment_number }} - ৳{{ number_format($nextInstallment->amount, 2) }} due on {{ $nextInstallment->due_date->format('d M Y') }}
            @else
                No pending installments
            @endif
        </div>
        @else
        <div class="alert alert-success">
            <strong>Congratulations!</strong> All installments have been paid. EMI plan completed.
        </div>
        @endif

        <div class="row mt-5">
            <div class="col-12 text-center">
                <p class="mb-0"><strong>Thank you for your payment!</strong></p>
                <p class="mb-0 small">This is a computer-generated receipt and does not require a signature.</p>
            </div>
        </div>

        <div class="d-print-none mt-4 text-center">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="ti tabler-printer me-1"></i> Print Receipt
            </button>
            <a href="{{ route('emi.show', $installment->emiPlan->id) }}" class="btn btn-secondary">
                <i class="ti tabler-arrow-left me-1"></i> Back to EMI Plan
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    /* Hide everything except the invoice content */
    body * {
        visibility: hidden;
    }
    
    .invoice-print, .invoice-print * {
        visibility: visible;
    }
    
    .invoice-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 20px !important;
        margin: 0 !important;
    }
    
    /* Hide buttons and non-printable elements */
    .d-print-none {
        display: none !important;
        visibility: hidden !important;
    }
    
    /* Clean background */
    body {
        background: white !important;
    }
    
    /* Prevent page breaks inside tables */
    .table {
        page-break-inside: avoid;
    }
    
    /* Remove any shadows or borders that might look bad in print */
    .card, .card-body {
        box-shadow: none !important;
        border: none !important;
    }
    
    /* Ensure proper spacing */
    @page {
        margin: 1cm;
    }
}
</style>
@endsection
