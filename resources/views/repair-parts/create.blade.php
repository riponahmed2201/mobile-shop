@extends('app')

@section('title', 'Add Repair Part')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Repair Service / Repair Parts /</span> Add Part</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Repair Part</h5>
            <a href="{{ route('repair-parts.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('repair-parts.store') }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <h6 class="mb-3">Basic Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="part_name">Part Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('part_name') is-invalid @enderror" id="part_name" name="part_name" value="{{ old('part_name') }}" required maxlength="200" placeholder="Enter part name">
                        @error('part_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="brand">Brand</label>
                        <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" value="{{ old('brand') }}" maxlength="100" placeholder="e.g., Samsung, Apple, Xiaomi">
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="category">Category</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                            <option value="">Select Category</option>
                            @foreach($categories as $categoryOption)
                                <option value="{{ $categoryOption }}" {{ old('category') === $categoryOption ? 'selected' : '' }}>
                                    {{ $categoryOption }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="subcategory">Subcategory</label>
                        <input type="text" class="form-control @error('subcategory') is-invalid @enderror" id="subcategory" name="subcategory" value="{{ old('subcategory') }}" maxlength="100" placeholder="e.g., Screen, Battery, Camera">
                        @error('subcategory')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" maxlength="1000" placeholder="Detailed description of the part...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Compatibility -->
                <h6 class="mb-3 mt-4">Device Compatibility</h6>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Compatible Devices</label>
                        <div id="compatible-devices">
                            @if(old('compatible_devices'))
                                @foreach(old('compatible_devices') as $index => $device)
                                <div class="input-group mb-2 device-input-group">
                                    <input type="text" class="form-control" name="compatible_devices[{{ $index }}]" value="{{ $device }}" placeholder="e.g., Samsung Galaxy S23, iPhone 14">
                                    <button type="button" class="btn btn-outline-danger remove-device">Remove</button>
                                </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-device">Add Compatible Device</button>
                        <div class="form-text">
                            <small class="text-muted">Add device models that this part is compatible with</small>
                        </div>
                    </div>
                </div>

                <!-- Inventory Management -->
                <h6 class="mb-3 mt-4">Inventory Management</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="current_stock">Current Stock</label>
                        <input type="number" class="form-control @error('current_stock') is-invalid @enderror" id="current_stock" name="current_stock" value="{{ old('current_stock', 0) }}" min="0" step="1">
                        @error('current_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="unit">Unit</label>
                        <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit">
                            <option value="pcs" {{ old('unit', 'pcs') === 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                            <option value="set" {{ old('unit') === 'set' ? 'selected' : '' }}>Set</option>
                            <option value="kit" {{ old('unit') === 'kit' ? 'selected' : '' }}>Kit</option>
                            <option value="pair" {{ old('unit') === 'pair' ? 'selected' : '' }}>Pair</option>
                        </select>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="min_stock_level">Minimum Stock Level</label>
                        <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" min="0" step="1">
                        @error('min_stock_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">Low stock warning threshold</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="reorder_level">Reorder Level</label>
                        <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 10) }}" min="0" step="1">
                        @error('reorder_level')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">Level at which reordering should be considered</small>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <h6 class="mb-3 mt-4">Pricing Information</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="purchase_price">Purchase Price (৳)</label>
                        <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="0.01" placeholder="0.00">
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="selling_price">Selling Price (৳)</label>
                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" min="0" step="0.01" placeholder="0.00">
                        @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="mrp">MRP (৳)</label>
                        <input type="number" class="form-control @error('mrp') is-invalid @enderror" id="mrp" name="mrp" value="{{ old('mrp') }}" min="0" step="0.01" placeholder="0.00">
                        @error('mrp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Supplier Information -->
                <h6 class="mb-3 mt-4">Supplier Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="primary_supplier_id">Primary Supplier</label>
                        <select class="form-select @error('primary_supplier_id') is-invalid @enderror" id="primary_supplier_id" name="primary_supplier_id">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('primary_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('primary_supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="supplier_part_code">Supplier Part Code</label>
                        <input type="text" class="form-control @error('supplier_part_code') is-invalid @enderror" id="supplier_part_code" name="supplier_part_code" value="{{ old('supplier_part_code') }}" maxlength="100" placeholder="Part code from supplier">
                        @error('supplier_part_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Location & Status -->
                <h6 class="mb-3 mt-4">Location & Status</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="location">Storage Location</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" maxlength="100" placeholder="e.g., Warehouse A, Shelf 3">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="bin_location">Bin Location</label>
                        <input type="text" class="form-control @error('bin_location') is-invalid @enderror" id="bin_location" name="bin_location" value="{{ old('bin_location') }}" maxlength="50" placeholder="e.g., A-01-05">
                        @error('bin_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Part
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">
                            <small class="text-muted">Inactive parts won't be available for new repairs</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input @error('is_discontinued') is-invalid @enderror" type="checkbox" id="is_discontinued" name="is_discontinued" value="1" {{ old('is_discontinued') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_discontinued">
                                Discontinued
                            </label>
                            @error('is_discontinued')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">
                            <small class="text-muted">Check if this part is discontinued by manufacturer</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Add Repair Part</button>
                        <a href="{{ route('repair-parts.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
    let deviceIndex = {{ count(old('compatible_devices', [])) ?: 0 }};

    // Add compatible device input
    $('#add-device').click(function() {
        const deviceHtml = `
            <div class="input-group mb-2 device-input-group">
                <input type="text" class="form-control" name="compatible_devices[${deviceIndex}]" placeholder="e.g., Samsung Galaxy S23, iPhone 14">
                <button type="button" class="btn btn-outline-danger remove-device">Remove</button>
            </div>
        `;
        $('#compatible-devices').append(deviceHtml);
        deviceIndex++;
    });

    // Remove compatible device input
    $(document).on('click', '.remove-device', function() {
        $(this).closest('.device-input-group').remove();
    });

    // Format currency inputs
    $('#purchase_price, #selling_price, #mrp').on('blur', function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });

    // Auto-fill reorder level based on min stock level
    $('#min_stock_level').on('input', function() {
        const minStock = parseInt($(this).val()) || 0;
        if (minStock > 0 && !$('#reorder_level').val()) {
            $('#reorder_level').val(Math.max(minStock * 2, 10));
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

        // Validate pricing logic
        const purchasePrice = parseFloat($('#purchase_price').val()) || 0;
        const sellingPrice = parseFloat($('#selling_price').val()) || 0;

        if (sellingPrice > 0 && purchasePrice > sellingPrice) {
            $('#selling_price').addClass('is-invalid');
            if (!$('#selling_price').next('.invalid-feedback').length) {
                $('#selling_price').after('<div class="invalid-feedback">Selling price should be higher than purchase price.</div>');
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

    // Live validation feedback
    $('input[required], select[required], textarea[required]').on('blur', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('is-invalid');
        }
    });

    // Remove custom error messages on input
    $('#selling_price').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
});
</script>
@endpush
