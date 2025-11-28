@extends('app')

@section('title', 'Edit Quotation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Edit Quotation #{{ $quotation->quotation_number }}</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Quotation</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('quotations.update', $quotation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ (old('customer_id') ?? $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} - {{ $customer->mobile_primary }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quotation Date <span class="text-danger">*</span></label>
                        <input type="date" name="quotation_date" class="form-control @error('quotation_date') is-invalid @enderror" value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" required>
                        @error('quotation_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until_date" class="form-control @error('valid_until_date') is-invalid @enderror" value="{{ old('valid_until_date', $quotation->valid_until_date ? $quotation->valid_until_date->format('Y-m-d') : '') }}">
                        @error('valid_until_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" step="0.01" value="{{ old('discount_amount', $quotation->discount_amount) }}">
                        @error('discount_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control @error('tax_amount') is-invalid @enderror" step="0.01" value="{{ old('tax_amount', $quotation->tax_amount) }}">
                        @error('tax_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="1">{{ $quotation->notes }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms_conditions" class="form-control" rows="3">{{ $quotation->terms_conditions }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
