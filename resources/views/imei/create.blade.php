@extends('app')

@section('title', 'Add IMEI')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / IMEI Tracking /</span> Add IMEI</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New IMEI Record</h5>
            <a href="{{ route('imei.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('imei.store') }}" method="POST">
                @csrf

                <!-- Basic Information -->
                <h6 class="mb-3">Basic Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_id">Product <span class="text-danger">*</span></label>
                        <select class="form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->product_name }}
                                    @if($product->model_name)
                                        ({{ $product->model_name }})
                                    @endif
                                    @if($product->brand)
                                        - {{ $product->brand->brand_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="imei_number">IMEI Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('imei_number') is-invalid @enderror" id="imei_number" name="imei_number" value="{{ old('imei_number') }}" required maxlength="50" placeholder="Enter IMEI number" />
                        @error('imei_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="serial_number">Serial Number</label>
                        <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" maxlength="100" placeholder="Enter serial number (optional)" />
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="IN_STOCK" {{ old('status', 'IN_STOCK') == 'IN_STOCK' ? 'selected' : '' }}>In Stock</option>
                            <option value="DEFECTIVE" {{ old('status') == 'DEFECTIVE' ? 'selected' : '' }}>Defective</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Purchase & Warranty Information -->
                <h6 class="mb-3 mt-4">Purchase & Warranty Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="purchase_date">Purchase Date</label>
                        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" />
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="warranty_expiry_date">Warranty Expiry Date</label>
                        <input type="date" class="form-control @error('warranty_expiry_date') is-invalid @enderror" id="warranty_expiry_date" name="warranty_expiry_date" value="{{ old('warranty_expiry_date') }}" />
                        @error('warranty_expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Additional Information -->
                <h6 class="mb-3 mt-4">Additional Information</h6>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Additional notes about this IMEI record">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save IMEI Record</button>
                        <a href="{{ route('imei.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
    // Set purchase date to today if not set
    if (!$('#purchase_date').val()) {
        $('#purchase_date').val(new Date().toISOString().split('T')[0]);
    }

    // Auto-calculate warranty expiry date based on product warranty period
    $('#product_id').change(function() {
        const productId = $(this).val();
        if (productId) {
            // You can implement AJAX call here to get product warranty period
            // For now, we'll set a default 1 year warranty
            const purchaseDate = new Date($('#purchase_date').val());
            if (purchaseDate && !isNaN(purchaseDate)) {
                const warrantyDate = new Date(purchaseDate);
                warrantyDate.setFullYear(warrantyDate.getFullYear() + 1); // Default 1 year
                $('#warranty_expiry_date').val(warrantyDate.toISOString().split('T')[0]);
            }
        }
    });

    // Update warranty expiry when purchase date changes
    $('#purchase_date').change(function() {
        const productId = $('#product_id').val();
        if (productId) {
            $('#product_id').trigger('change');
        }
    });
});
</script>
@endpush
