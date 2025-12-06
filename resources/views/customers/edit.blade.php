@extends('app')

@section('title', 'Edit Customer')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Edit Customer</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Customer</h5>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <h6 class="mb-3">Basic Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $customer->full_name }}" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Code</label>
                        <input type="text" class="form-control bg-light" value="{{ $customer->customer_code }}" readonly disabled />
                        <small class="text-muted">Auto-generated code (cannot be changed)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="mobile_primary">Primary Mobile <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mobile_primary" name="mobile_primary" value="{{ $customer->mobile_primary }}" required maxlength="20" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="mobile_alternative">Alternative Mobile</label>
                        <input type="text" class="form-control" id="mobile_alternative" name="mobile_alternative" value="{{ $customer->mobile_alternative }}" maxlength="20" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $customer->email }}" maxlength="100" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '' }}" />
                    </div>
                </div>

                <!-- Address Information -->
                <h6 class="mb-3 mt-4">Address Information</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2">{{ $customer->address }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ $customer->city }}" maxlength="100" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="postal_code">Postal Code</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ $customer->postal_code }}" maxlength="20" />
                    </div>
                </div>

                <!-- Customer Details -->
                <h6 class="mb-3 mt-4">Customer Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="customer_type">Customer Type</label>
                        <select class="form-select" id="customer_type" name="customer_type">
                            <option value="NEW" {{ $customer->customer_type == 'NEW' ? 'selected' : '' }}>New</option>
                            <option value="REGULAR" {{ $customer->customer_type == 'REGULAR' ? 'selected' : '' }}>Regular</option>
                            <option value="VIP" {{ $customer->customer_type == 'VIP' ? 'selected' : '' }}>VIP</option>
                            <option value="WHOLESALE" {{ $customer->customer_type == 'WHOLESALE' ? 'selected' : '' }}>Wholesale</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="credit_limit">Credit Limit</label>
                        <input type="number" step="0.01" class="form-control" id="credit_limit" name="credit_limit" value="{{ $customer->credit_limit }}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="total_purchases">Total Purchases</label>
                        <input type="number" step="0.01" class="form-control" id="total_purchases" name="total_purchases" value="{{ $customer->total_purchases }}" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="total_repairs">Total Repairs</label>
                        <input type="number" class="form-control" id="total_repairs" name="total_repairs" value="{{ $customer->total_repairs }}" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="loyalty_points">Loyalty Points</label>
                        <input type="number" class="form-control" id="loyalty_points" name="loyalty_points" value="{{ $customer->loyalty_points }}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="outstanding_balance">Outstanding Balance</label>
                        <input type="number" step="0.01" class="form-control" id="outstanding_balance" name="outstanding_balance" value="{{ $customer->outstanding_balance }}" />
                    </div>
                </div>

                <!-- Tags -->
                <h6 class="mb-3 mt-4">Tags</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="tags">Customer Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags[]" value="{{ $customer->tags->pluck('tag_name')->implode(', ') }}" placeholder="Enter tags separated by comma" />
                        <small class="text-muted">You can add multiple tags separated by comma</small>
                    </div>
                </div>

                <!-- Notes -->
                <h6 class="mb-3 mt-4">Additional Information</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $customer->notes }}</textarea>
                    </div>
                </div>

                <!-- Status -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ $customer->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Customer</button>
            </form>
        </div>
    </div>
</div>
@endsection

