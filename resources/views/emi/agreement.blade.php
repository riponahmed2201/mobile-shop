@extends('app')

@section('title', 'EMI Agreement')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="invoice-print p-5">
        <div class="d-flex justify-content-between flex-row mb-4">
            <div class="mb-4">
                <h3 class="mb-2">EMI AGREEMENT</h3>
                <p class="mb-1">Mobile Shop Inc.</p>
                <p class="mb-1">123 Business Street</p>
                <p class="mb-0">Dhaka, Bangladesh</p>
            </div>
            <div>
                <h4 class="mb-2">EMI Plan #{{ $emiPlan->id }}</h4>
                <p class="mb-1">Date: {{ $emiPlan->created_at->format('d M Y') }}</p>
                <p class="mb-1">Sale Invoice: {{ $emiPlan->sale->invoice_number }}</p>
            </div>
        </div>

        <hr class="my-4">

        <div class="row mb-4">
            <div class="col-sm-6">
                <h5>Customer Information:</h5>
                <p class="mb-1"><strong>Name:</strong> {{ $emiPlan->customer->full_name }}</p>
                <p class="mb-1"><strong>Mobile:</strong> {{ $emiPlan->customer->mobile_primary }}</p>
                @if($emiPlan->customer->email)
                <p class="mb-1"><strong>Email:</strong> {{ $emiPlan->customer->email }}</p>
                @endif
                @if($emiPlan->customer->address)
                <p class="mb-0"><strong>Address:</strong> {{ $emiPlan->customer->address }}</p>
                @endif
            </div>
            <div class="col-sm-6">
                <h5>EMI Plan Details:</h5>
                <p class="mb-1"><strong>Total Amount:</strong> ৳{{ number_format($emiPlan->total_amount, 2) }}</p>
                <p class="mb-1"><strong>Down Payment:</strong> ৳{{ number_format($emiPlan->down_payment, 2) }}</p>
                <p class="mb-1"><strong>Remaining:</strong> ৳{{ number_format($emiPlan->total_amount - $emiPlan->down_payment, 2) }}</p>
                <p class="mb-1"><strong>Installments:</strong> {{ $emiPlan->number_of_installments }} months</p>
                <p class="mb-1"><strong>Monthly Payment:</strong> ৳{{ number_format($emiPlan->installment_amount, 2) }}</p>
                <p class="mb-0"><strong>Interest Rate:</strong> {{ $emiPlan->interest_rate }}%</p>
            </div>
        </div>

        <div class="table-responsive mb-4">
            <h5 class="mb-3">Installment Schedule:</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Due Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($emiPlan->installments as $installment)
                    <tr>
                        <td>{{ $installment->installment_number }}</td>
                        <td>{{ $installment->due_date->format('d M Y') }}</td>
                        <td>৳{{ number_format($installment->amount, 2) }}</td>
                        <td>
                            @if($installment->status === 'PAID')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2">Total Installment Amount</th>
                        <th colspan="2">৳{{ number_format($emiPlan->installment_amount * $emiPlan->number_of_installments, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mb-4">
            <h5>Terms & Conditions:</h5>
            <ol class="small">
                <li>The customer agrees to pay {{ $emiPlan->number_of_installments }} monthly installments of ৳{{ number_format($emiPlan->installment_amount, 2) }} each.</li>
                <li>Payment must be made on or before the due date mentioned in the schedule.</li>
                <li>Late payment may result in additional charges and legal action.</li>
                <li>The customer must maintain the product in good condition during the EMI period.</li>
                <li>Failure to pay 3 consecutive installments may result in product repossession.</li>
                <li>Early settlement is allowed with prior notice to the company.</li>
                <li>This agreement is governed by the laws of Bangladesh.</li>
            </ol>
        </div>

        <div class="row mt-5">
            <div class="col-6">
                <p class="mb-0">_______________________</p>
                <p class="mb-0"><strong>Customer Signature</strong></p>
                <p class="mb-0">{{ $emiPlan->customer->full_name }}</p>
                <p class="mb-0">Date: _______________</p>
            </div>
            <div class="col-6 text-end">
                <p class="mb-0">_______________________</p>
                <p class="mb-0"><strong>Authorized Signature</strong></p>
                <p class="mb-0">Mobile Shop Inc.</p>
                <p class="mb-0">Date: {{ now()->format('d M Y') }}</p>
            </div>
        </div>

        <div class="d-print-none mt-4 text-center">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="ti tabler-printer me-1"></i> Print Agreement
            </button>
            <a href="{{ route('emi.show', $emiPlan->id) }}" class="btn btn-secondary">
                <i class="ti tabler-arrow-left me-1"></i> Back
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
    
    /* Remove any shadows or borders */
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
