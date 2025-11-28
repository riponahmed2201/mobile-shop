@extends('app')

@section('title', 'Return Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Return #{{ $return->return_number }}</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Return Details</h5>
                    <div>
                        <span class="badge bg-{{ $return->return_type === 'REFUND' ? 'info' : ($return->return_type === 'EXCHANGE' ? 'warning' : 'primary') }}">
                            {{ $return->return_type }}
                        </span>
                        <span class="badge bg-{{ $return->status === 'PENDING' ? 'warning' : ($return->status === 'APPROVED' ? 'success' : ($return->status === 'REJECTED' ? 'danger' : 'primary')) }}">
                            {{ $return->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->items as $item)
                                <tr>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($item->total_amount, 2) }}</td>
                                </tr>
                                @if($item->condition_notes)
                                <tr>
                                    <td colspan="4" class="text-muted"><small>Condition: {{ $item->condition_notes }}</small></td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>৳{{ number_format($return->total_amount, 2) }}</strong></td>
                                </tr>
                                @if($return->restocking_fee > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Restocking Fee:</td>
                                    <td>-৳{{ number_format($return->restocking_fee, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Refund Amount:</strong></td>
                                    <td><strong>৳{{ number_format($return->refund_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-3">
                        <h6>Return Reason:</h6>
                        <p class="text-muted">{{ $return->return_reason }}</p>
                    </div>

                    @if($return->approval_notes)
                    <div class="mt-3">
                        <h6>Approval Notes:</h6>
                        <p class="text-muted">{{ $return->approval_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Return Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Return #:</dt>
                        <dd class="col-sm-7">{{ $return->return_number }}</dd>

                        <dt class="col-sm-5">Sale Invoice:</dt>
                        <dd class="col-sm-7">
                            <a href="{{ route('sales.show', $return->sale_id) }}">
                                {{ $return->sale->invoice_number }}
                            </a>
                        </dd>

                        <dt class="col-sm-5">Customer:</dt>
                        <dd class="col-sm-7">{{ $return->customer->full_name }}</dd>

                        <dt class="col-sm-5">Return Date:</dt>
                        <dd class="col-sm-7">{{ $return->return_date->format('d M Y') }}</dd>

                        @if($return->approved_by)
                        <dt class="col-sm-5">Approved By:</dt>
                        <dd class="col-sm-7">{{ $return->approver->name }}</dd>
                        @endif

                        @if($return->processed_by)
                        <dt class="col-sm-5">Processed By:</dt>
                        <dd class="col-sm-7">{{ $return->processor->name }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('returns.index') }}" class="btn btn-secondary">Back to List</a>
                
                @if($return->status === 'PENDING')
                <form action="{{ route('returns.approve', $return->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Approve this return?')">
                        <i class="ti ti-check me-1"></i> Approve Return
                    </button>
                </form>
                
                <form action="{{ route('returns.reject', $return->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Reject this return?')">
                        <i class="ti ti-x me-1"></i> Reject Return
                    </button>
                </form>
                @endif

                @if($return->status === 'APPROVED')
                <form action="{{ route('returns.process', $return->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Process this return (complete refund/exchange)?')">
                        <i class="ti ti-package me-1"></i> Process Return
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
