@extends('app')

@section('title', 'Edit Sale')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Edit Sale #{{ $sale->invoice_number }}</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Sale</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ (old('customer_id') ?? $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} - {{ $customer->mobile_primary }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="CASH" {{ (old('payment_method') ?? $sale->payment_method) == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="CARD" {{ (old('payment_method') ?? $sale->payment_method) == 'CARD' ? 'selected' : '' }}>Card</option>
                            <option value="BKASH" {{ (old('payment_method') ?? $sale->payment_method) == 'BKASH' ? 'selected' : '' }}>bKash</option>
                            <option value="NAGAD" {{ (old('payment_method') ?? $sale->payment_method) == 'NAGAD' ? 'selected' : '' }}>Nagad</option>
                            <option value="BANK" {{ (old('payment_method') ?? $sale->payment_method) == 'BANK' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="MIXED" {{ (old('payment_method') ?? $sale->payment_method) == 'MIXED' ? 'selected' : '' }}>Mixed</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Paid Amount <span class="text-danger">*</span></label>
                        <input type="number" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" step="0.01" value="{{ old('paid_amount', $sale->paid_amount) }}" required>
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" step="0.01" value="{{ old('discount_amount', $sale->discount_amount) }}">
                        @error('discount_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control @error('tax_amount') is-invalid @enderror" step="0.01" value="{{ old('tax_amount', $sale->tax_amount) }}">
                        @error('tax_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="1">{{ $sale->notes }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
