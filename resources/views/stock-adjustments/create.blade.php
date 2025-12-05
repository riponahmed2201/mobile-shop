@extends('app')

@section('title', 'Create Stock Adjustment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Stock Adjustments /</span> Create Adjustment</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create New Stock Adjustment</h5>
            <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('stock-adjustments.store') }}" method="POST">
                @csrf

                <!-- Product Selection -->
                <h6 class="mb-3">Product Information</h6>
                <div class="row">
                    <div class="col-md-12 mb-3">
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
                                    [Current Stock: {{ $product->current_stock }} {{ $product->unit }}]
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Adjustment Details -->
                <h6 class="mb-3 mt-4">Adjustment Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="adjustment_type">Adjustment Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('adjustment_type') is-invalid @enderror" id="adjustment_type" name="adjustment_type" required>
                            <option value="">Select Type</option>
                            <option value="ADD" {{ old('adjustment_type') == 'ADD' ? 'selected' : '' }}>Stock Added</option>
                            <option value="REMOVE" {{ old('adjustment_type') == 'REMOVE' ? 'selected' : '' }}>Stock Removed</option>
                            <option value="DAMAGED" {{ old('adjustment_type') == 'DAMAGED' ? 'selected' : '' }}>Damaged</option>
                            <option value="LOST" {{ old('adjustment_type') == 'LOST' ? 'selected' : '' }}>Lost</option>
                            <option value="FOUND" {{ old('adjustment_type') == 'FOUND' ? 'selected' : '' }}>Found</option>
                            <option value="RETURN" {{ old('adjustment_type') == 'RETURN' ? 'selected' : '' }}>Returned</option>
                        </select>
                        @error('adjustment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">
                                <strong>Note:</strong> ADD, FOUND, and RETURN will increase stock. REMOVE, DAMAGED, and LOST will decrease stock.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="quantity">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required />
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="adjustment_date">Adjustment Date</label>
                        <input type="datetime-local" class="form-control @error('adjustment_date') is-invalid @enderror" id="adjustment_date" name="adjustment_date" value="{{ old('adjustment_date', now()->format('Y-m-d\TH:i')) }}" />
                        @error('adjustment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="reference_number">Reference Number</label>
                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" maxlength="100" placeholder="Optional reference (e.g., invoice number)" />
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Reason -->
                <h6 class="mb-3 mt-4">Reason for Adjustment</h6>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="reason">Reason <span class="text-warning">(Recommended)</span></label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" maxlength="1000" placeholder="Please provide a detailed reason for this stock adjustment...">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <small class="text-muted">
                                Providing a clear reason helps maintain accurate inventory records and audit trails.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Stock Impact Preview -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info" id="stock-preview" style="display: none;">
                            <strong>Stock Impact Preview:</strong>
                            <span id="preview-text"></span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Create Adjustment</button>
                        <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
    // Update stock preview when adjustment type or quantity changes
    function updateStockPreview() {
        const adjustmentType = $('#adjustment_type').val();
        const quantity = parseInt($('#quantity').val()) || 0;
        const productSelect = $('#product_id option:selected');
        const currentStock = productSelect.data('current-stock') || 0;

        if (adjustmentType && quantity > 0) {
            let newStock;
            let impact;

            switch(adjustmentType) {
                case 'ADD':
                case 'FOUND':
                case 'RETURN':
                    newStock = currentStock + quantity;
                    impact = `Stock will increase from ${currentStock} to ${newStock}`;
                    break;
                case 'REMOVE':
                case 'DAMAGED':
                case 'LOST':
                    newStock = currentStock - quantity;
                    impact = `Stock will decrease from ${currentStock} to ${newStock}`;
                    break;
            }

            $('#preview-text').text(impact);
            $('#stock-preview').show();
        } else {
            $('#stock-preview').hide();
        }
    }

    $('#adjustment_type, #quantity').on('change input', updateStockPreview);
    $('#product_id').change(function() {
        // Store current stock as data attribute for preview
        const selectedOption = $(this).find('option:selected');
        const stockText = selectedOption.text();
        const currentStockMatch = stockText.match(/\[Current Stock: (\d+)/);
        const currentStock = currentStockMatch ? parseInt(currentStockMatch[1]) : 0;
        $(this).data('current-stock', currentStock);
        updateStockPreview();
    });

    // Set current stock for initially selected product
    $('#product_id').trigger('change');
});
</script>
@endpush
