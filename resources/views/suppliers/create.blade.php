@extends('app')

@section('title', 'Add Supplier')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases / Suppliers /</span> Add Supplier</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Supplier</h5>
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <h6 class="mb-3">Basic Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="supplier_name">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required maxlength="200" placeholder="Enter supplier name" />
                        @error('supplier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="contact_person">Contact Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" maxlength="100" placeholder="Enter contact person name" />
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <h6 class="mb-3 mt-4">Contact Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="mobile">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile') }}" required maxlength="20" placeholder="Enter mobile number" />
                        @error('mobile')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">Format: +880xxxxxxxxxx or 01xxxxxxxxx</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" maxlength="100" placeholder="Enter email address" />
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Address Information -->
                <h6 class="mb-3 mt-4">Address Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" maxlength="500" placeholder="Enter full address">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="city">City</label>
                        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" maxlength="100" placeholder="Enter city" />
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="country">Country</label>
                        <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', 'Bangladesh') }}" maxlength="100" placeholder="Enter country" />
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Financial Information -->
                <h6 class="mb-3 mt-4">Financial Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="credit_limit">Credit Limit (৳)</label>
                        <input type="number" class="form-control @error('credit_limit') is-invalid @enderror" id="credit_limit" name="credit_limit" value="{{ old('credit_limit') }}" min="0" step="0.01" placeholder="0.00" />
                        @error('credit_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">Maximum credit amount allowed for this supplier</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="payment_terms">Payment Terms</label>
                        <input type="text" class="form-control @error('payment_terms') is-invalid @enderror" id="payment_terms" name="payment_terms" value="{{ old('payment_terms') }}" maxlength="200" placeholder="e.g., Net 30 days, COD" />
                        @error('payment_terms')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">Payment terms and conditions</small>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <h6 class="mb-3 mt-4">Status</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Supplier
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">
                            <small class="text-muted">Inactive suppliers cannot be selected for new purchase orders</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Create Supplier</button>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script>
$(document).ready(function() {
    // Auto-format mobile number
    $('#mobile').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.startsWith('880')) {
            // Already has country code
            $(this).val('+' + value);
        } else if (value.startsWith('0')) {
            // Local number, convert to international
            $(this).val('+880' + value.substring(1));
        } else if (value.length === 11 && value.startsWith('1')) {
            // Local number without leading 0
            $(this).val('+880' + value);
        }
    });

    // Format credit limit input
    $('#credit_limit').on('blur', function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });

    // Form validation
    $('form').submit(function(e) {
        let isValid = true;

        // Check required fields
        $(this).find('input[required], select[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Email validation
        const email = $('#email').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email').addClass('is-invalid');
            if (!$('#email').next('.invalid-feedback').length) {
                $('#email').after('<div class="invalid-feedback">Please enter a valid email address.</div>');
            }
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        }
    });
});
</script>
@endpush
