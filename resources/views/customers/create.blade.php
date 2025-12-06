@extends('app')

@section('title', 'Add Customer')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Add New Customer</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Customer</h5>
            <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <h6 class="mb-3">Basic Information</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="full_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="mobile_primary">Primary Mobile <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mobile_primary" name="mobile_primary" required maxlength="20" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="mobile_alternative">Alternative Mobile</label>
                        <input type="text" class="form-control" id="mobile_alternative" name="mobile_alternative" maxlength="20" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="100" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" />
                    </div>
                </div>

                <!-- Address Information -->
                <h6 class="mb-3 mt-4">Address Information</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" maxlength="100" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="postal_code">Postal Code</label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="20" />
                    </div>
                </div>

                <!-- Customer Details -->
                <h6 class="mb-3 mt-4">Customer Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="customer_type">Customer Type</label>
                        <select class="form-select" id="customer_type" name="customer_type">
                            <option value="NEW">New</option>
                            <option value="REGULAR">Regular</option>
                            <option value="VIP">VIP</option>
                            <option value="WHOLESALE">Wholesale</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="credit_limit">Credit Limit</label>
                        <input type="number" step="0.01" class="form-control" id="credit_limit" name="credit_limit" value="0.00" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="total_purchases">Total Purchases</label>
                        <input type="number" step="0.01" class="form-control" id="total_purchases" name="total_purchases" value="0.00" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="total_repairs">Total Repairs</label>
                        <input type="number" class="form-control" id="total_repairs" name="total_repairs" value="0" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="loyalty_points">Loyalty Points</label>
                        <input type="number" class="form-control" id="loyalty_points" name="loyalty_points" value="0" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="outstanding_balance">Outstanding Balance</label>
                        <input type="number" step="0.01" class="form-control" id="outstanding_balance" name="outstanding_balance" value="0.00" />
                    </div>
                </div>

                <!-- Tags -->
                <h6 class="mb-3 mt-4">Tags</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="tags">Customer Tags</label>
                        <input type="text" class="form-control" id="tags" name="tags[]" placeholder="Enter tags separated by comma" />
                        <small class="text-muted">You can add multiple tags separated by comma</small>
                    </div>
                </div>

                <!-- Notes -->
                <h6 class="mb-3 mt-4">Additional Information</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>

                <!-- Status -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Customer</button>
            </form>
        </div>
    </div>
</div>
@endsection

