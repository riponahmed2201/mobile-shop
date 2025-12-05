@extends('app')

@section('title', 'Create Repair Ticket')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Repair Service / Repair Tickets /</span> Create Ticket</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create New Repair Ticket</h5>
            <a href="{{ route('repairs.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('repairs.store') }}" method="POST">
                @csrf

                <!-- Customer Selection -->
                <h6 class="mb-3">Customer Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="customer_id">Customer <span class="text-danger">*</span></label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} - {{ $customer->mobile_primary }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">If customer not found, <a href="{{ route('customers.create') }}" target="_blank">add new customer</a></small>
                        </div>
                    </div>
                </div>

                <!-- Device Information -->
                <h6 class="mb-3 mt-4">Device Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="device_brand">Device Brand</label>
                        <input type="text" class="form-control @error('device_brand') is-invalid @enderror" id="device_brand" name="device_brand" value="{{ old('device_brand') }}" maxlength="100" placeholder="e.g., Samsung, Apple, Xiaomi">
                        @error('device_brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="device_model">Device Model</label>
                        <input type="text" class="form-control @error('device_model') is-invalid @enderror" id="device_model" name="device_model" value="{{ old('device_model') }}" maxlength="100" placeholder="e.g., Galaxy S23, iPhone 14 Pro">
                        @error('device_model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="imei_number">IMEI Number</label>
                        <input type="text" class="form-control @error('imei_number') is-invalid @enderror" id="imei_number" name="imei_number" value="{{ old('imei_number') }}" maxlength="50" placeholder="Enter IMEI number">
                        @error('imei_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">IMEI must be unique across all repair tickets</small>
                        </div>
                    </div>
                </div>

                <!-- Repair Details -->
                <h6 class="mb-3 mt-4">Repair Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="priority">Priority Level <span class="text-danger">*</span></label>
                        <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="LOW" {{ old('priority') === 'LOW' ? 'selected' : '' }}>Low</option>
                            <option value="NORMAL" {{ old('priority') === 'NORMAL' ? 'selected' : '' }}>Normal</option>
                            <option value="HIGH" {{ old('priority') === 'HIGH' ? 'selected' : '' }}>High</option>
                            <option value="URGENT" {{ old('priority') === 'URGENT' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="estimated_delivery_date">Estimated Delivery Date</label>
                        <input type="date" class="form-control @error('estimated_delivery_date') is-invalid @enderror" id="estimated_delivery_date" name="estimated_delivery_date" value="{{ old('estimated_delivery_date') }}" min="{{ now()->addDay()->format('Y-m-d') }}">
                        @error('estimated_delivery_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="estimated_cost">Estimated Cost (৳)</label>
                        <input type="number" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost') }}" min="0" step="0.01" placeholder="0.00">
                        @error('estimated_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="advance_payment">Advance Payment (৳)</label>
                        <input type="number" class="form-control @error('advance_payment') is-invalid @enderror" id="advance_payment" name="advance_payment" value="{{ old('advance_payment') }}" min="0" step="0.01" placeholder="0.00">
                        @error('advance_payment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="problem_description">Problem Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('problem_description') is-invalid @enderror" id="problem_description" name="problem_description" rows="4" maxlength="1000" placeholder="Describe the problem in detail..." required>{{ old('problem_description') }}</textarea>
                        @error('problem_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Additional Options -->
                <h6 class="mb-3 mt-4">Additional Options</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('warranty_repair') is-invalid @enderror" type="checkbox" id="warranty_repair" name="warranty_repair" value="1" {{ old('warranty_repair') ? 'checked' : '' }}>
                            <label class="form-check-label" for="warranty_repair">
                                Warranty Repair
                            </label>
                            @error('warranty_repair')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">
                            <small class="text-muted">Check if this is a warranty repair (no charges apply)</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">Additional Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Any additional notes or special instructions...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Create Repair Ticket</button>
                        <a href="{{ route('repairs.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
    // Auto-format IMEI number
    $('#imei_number').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        // IMEI is typically 15 digits, but allow up to 50 as per DB
        $(this).val(value);
    });

    // Format currency inputs
    $('#estimated_cost, #advance_payment').on('blur', function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });

    // Set minimum date for estimated delivery
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    $('#estimated_delivery_date').attr('min', tomorrow.toISOString().split('T')[0]);

    // Warranty repair logic
    $('#warranty_repair').change(function() {
        const isWarranty = $(this).is(':checked');
        if (isWarranty) {
            $('#estimated_cost, #advance_payment').val('0.00').prop('disabled', true);
        } else {
            $('#estimated_cost, #advance_payment').prop('disabled', false);
        }
    });

    // Form validation
    $('form').submit(function(e) {
        let isValid = true;

        // Check required fields
        $(this).find('input[required], select[required], textarea[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Customer validation
        const customerId = $('#customer_id').val();
        if (!customerId) {
            $('#customer_id').addClass('is-invalid');
            isValid = false;
        }

        // Priority validation
        const priority = $('#priority').val();
        if (!priority) {
            $('#priority').addClass('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
            return false;
        }
    });

    // Live validation feedback
    $('input[required], select[required], textarea[required]').on('blur', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
        } else {
            $(this).addClass('is-invalid');
        }
    });
});
</script>
@endpush
