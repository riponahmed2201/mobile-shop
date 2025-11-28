@extends('app')

@section('title', 'Quotation Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Quotation #{{ $quotation->quotation_number }}</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Quotation Details</h5>
                    <span class="badge bg-{{ $quotation->status === 'DRAFT' ? 'secondary' : ($quotation->status === 'SENT' ? 'info' : ($quotation->status === 'ACCEPTED' ? 'success' : ($quotation->status === 'REJECTED' ? 'danger' : ($quotation->status === 'EXPIRED' ? 'warning' : 'primary')))) }}">
                        {{ $quotation->status }}
                    </span>
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
                                @foreach($quotation->items as $item)
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
                                    <td><strong>৳{{ number_format($quotation->subtotal, 2) }}</strong></td>
                                </tr>
                                @if($quotation->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Discount:</td>
                                    <td>-৳{{ number_format($quotation->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($quotation->tax_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end">Tax:</td>
                                    <td>+৳{{ number_format($quotation->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Total Amount:</strong></td>
                                    <td><strong>৳{{ number_format($quotation->total_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($quotation->terms_conditions)
                    <div class="mt-3">
                        <h6>Terms & Conditions:</h6>
                        <p class="text-muted">{{ $quotation->terms_conditions }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Quotation Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Quotation #:</dt>
                        <dd class="col-sm-7">{{ $quotation->quotation_number }}</dd>

                        <dt class="col-sm-5">Customer:</dt>
                        <dd class="col-sm-7">{{ $quotation->customer ? $quotation->customer->full_name : '-' }}</dd>

                        <dt class="col-sm-5">Date:</dt>
                        <dd class="col-sm-7">{{ $quotation->quotation_date->format('d M Y') }}</dd>

                        @if($quotation->valid_until_date)
                        <dt class="col-sm-5">Valid Until:</dt>
                        <dd class="col-sm-7">{{ $quotation->valid_until_date->format('d M Y') }}</dd>
                        @endif

                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $quotation->creator ? $quotation->creator->name : '-' }}</dd>

                        @if($quotation->notes)
                        <dt class="col-sm-5">Notes:</dt>
                        <dd class="col-sm-7">{{ $quotation->notes }}</dd>
                        @endif

                        @if($quotation->status === 'CONVERTED')
                        <dt class="col-sm-5">Converted to:</dt>
                        <dd class="col-sm-7">
                            <a href="{{ route('sales.show', $quotation->converted_to_sale_id) }}">
                                Sale #{{ $quotation->convertedSale->invoice_number }}
                            </a>
                        </dd>
                        @endif
                    </dl>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Back to List</a>
                
                @if($quotation->status !== 'CONVERTED')
                <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-primary">Edit</a>
                
                <form action="{{ route('quotations.convert', $quotation->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Convert this quotation to a sale?')">
                        <i class="ti ti-check me-1"></i> Convert to Sale
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
