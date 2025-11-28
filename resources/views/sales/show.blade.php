@extends('app')

@section('title', 'Sale Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Sale #{{ $sale->invoice_number }}</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Sale Details</h5>
                    <div>
                        <span class="badge bg-{{ $sale->payment_status === 'PAID' ? 'success' : ($sale->payment_status === 'PARTIAL' ? 'warning' : 'danger') }}">
                            {{ $sale->payment_status }}
                        </span>
                        <span class="badge bg-{{ $sale->sale_status === 'COMPLETED' ? 'success' : ($sale->sale_status === 'CANCELLED' ? 'danger' : 'warning') }}">
                            {{ $sale->sale_status }}
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
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product->product_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                    <td><strong>৳{{ number_format($sale->subtotal, 2) }}</strong></td>
                                </tr>
                                @if($sale->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Discount:</td>
                                    <td>-৳{{ number_format($sale->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($sale->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Tax:</td>
                                    <td>+৳{{ number_format($sale->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>৳{{ number_format($sale->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Paid Amount:</td>
                                    <td>৳{{ number_format($sale->paid_amount, 2) }}</td>
                                </tr>
                                <tr class="{{ $sale->due_amount > 0 ? 'table-danger' : 'table-success' }}">
                                    <td colspan="3" class="text-end"><strong>Due Amount:</strong></td>
                                    <td><strong>৳{{ number_format($sale->due_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sale Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Invoice #:</dt>
                        <dd class="col-sm-7">{{ $sale->invoice_number }}</dd>

                        <dt class="col-sm-5">Customer:</dt>
                        <dd class="col-sm-7">{{ $sale->customer ? $sale->customer->full_name : 'Walk-in Customer' }}</dd>

                        <dt class="col-sm-5">Sale Date:</dt>
                        <dd class="col-sm-7">{{ $sale->sale_date->format('d M Y') }}</dd>

                        <dt class="col-sm-5">Sale Type:</dt>
                        <dd class="col-sm-7"><span class="badge bg-info">{{ $sale->sale_type }}</span></dd>

                        <dt class="col-sm-5">Payment Method:</dt>
                        <dd class="col-sm-7">{{ $sale->payment_method }}</dd>

                        <dt class="col-sm-5">Sold By:</dt>
                        <dd class="col-sm-7">{{ $sale->soldBy ? $sale->soldBy->name : '-' }}</dd>

                        @if($sale->notes)
                        <dt class="col-sm-5">Notes:</dt>
                        <dd class="col-sm-7">{{ $sale->notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('sales.index') }}" class="btn btn-secondary flex-fill">Back to List</a>
                @if($sale->sale_status === 'COMPLETED')
                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary flex-fill">Edit</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
