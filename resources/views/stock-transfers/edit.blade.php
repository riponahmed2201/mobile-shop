@extends('app')

@section('title', 'Edit Stock Transfer')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Stock Transfers /</span> Edit Transfer</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Stock Transfer</h5>
            <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('stock-transfers.update', $stockTransfer->id) }}" method="POST" id="transfer-form">
                @csrf
                @method('PUT')

                <!-- Transfer Details -->
                <h6 class="mb-3">Transfer Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="from_location">From Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('from_location') is-invalid @enderror" id="from_location" name="from_location" value="{{ old('from_location', $stockTransfer->from_location) }}" required list="locations-list" placeholder="e.g., Main Warehouse, Store A" />
                        @error('from_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="to_location">To Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('to_location') is-invalid @enderror" id="to_location" name="to_location" value="{{ old('to_location', $stockTransfer->to_location) }}" required list="locations-list" placeholder="e.g., Branch Store, Customer Location" />
                        @error('to_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="transfer_date">Transfer Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('transfer_date') is-invalid @enderror" id="transfer_date" name="transfer_date" value="{{ old('transfer_date', $stockTransfer->transfer_date->format('Y-m-d')) }}" required />
                        @error('transfer_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Transfer Items -->
                <h6 class="mb-3 mt-4">Transfer Items</h6>
                <div id="items-container">
                    @foreach($stockTransfer->items as $index => $item)
                    <div class="item-row border rounded p-3 mb-3" data-index="{{ $index }}">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select product-select @error('items.' . $index . '.product_id') is-invalid @enderror" name="items[{{ $index }}][product_id]" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->current_stock + $item->quantity }}" data-name="{{ $product->product_name }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->product_name }}
                                            @if($product->model_name)
                                                ({{ $product->model_name }})
                                            @endif
                                            @if($product->brand)
                                                - {{ $product->brand->brand_name }}
                                            @endif
                                            [Stock: {{ $product->current_stock + $item->quantity }} {{ $product->unit }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.' . $index . '.product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control quantity-input @error('items.' . $index . '.quantity') is-invalid @enderror" name="items[{{ $index }}][quantity]" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" min="1" required placeholder="Enter quantity" />
                                @error('items.' . $index . '.quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <small class="text-muted">Available: <span class="available-stock">{{ $item->product->current_stock + $item->quantity }}</span></small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item-btn" style="{{ count($stockTransfer->items) > 1 ? '' : 'display: none;' }}">
                                    <i class="ti tabler-trash me-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary" id="add-item-btn">
                        <i class="ti tabler-plus me-1"></i> Add Another Item
                    </button>
                </div>

                <!-- Notes -->
                <h6 class="mb-3 mt-4">Additional Information</h6>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Additional notes about this transfer...">{{ old('notes', $stockTransfer->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Warning for stock changes -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="ti tabler-alert-triangle me-2"></i>
                            <strong>Warning:</strong> Editing this transfer will automatically update the product's stock levels to reflect the new quantities.
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update Transfer</button>
                        <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Datalist for locations -->
    <datalist id="locations-list">
        @foreach($locations as $location)
            <option value="{{ $location }}">
        @endforeach
    </datalist>
</div>
@endsection

@push('page_js')
<script>
$(document).ready(function() {
    let itemIndex = {{ count($stockTransfer->items) }}; // Start from existing count

    // Add new item row
    $('#add-item-btn').click(function() {
        const newRow = createItemRow(itemIndex);
        $('#items-container').append(newRow);
        itemIndex++;

        // Show remove buttons if more than one item
        updateRemoveButtons();
    });

    // Remove item row
    $(document).on('click', '.remove-item-btn', function() {
        $(this).closest('.item-row').remove();
        updateRemoveButtons();
    });

    // Update available stock when product changes
    $(document).on('change', '.product-select', function() {
        const selectedOption = $(this).find('option:selected');
        const stock = selectedOption.data('stock') || 0;
        const row = $(this).closest('.item-row');
        row.find('.available-stock').text(stock);

        // Clear quantity if product changed
        row.find('.quantity-input').val('');
    });

    // Validate quantity against available stock
    $(document).on('input', '.quantity-input', function() {
        const quantity = parseInt($(this).val()) || 0;
        const row = $(this).closest('.item-row');
        const availableStock = parseInt(row.find('.available-stock').text()) || 0;

        if (quantity > availableStock) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Quantity exceeds available stock (' + availableStock + ')</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    function createItemRow(index) {
        return `
            <div class="item-row border rounded p-3 mb-3" data-index="${index}">
                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select product-select" name="items[${index}][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-stock="{{ $product->current_stock }}" data-name="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                    @if($product->model_name)
                                        ({{ $product->model_name }})
                                    @endif
                                    @if($product->brand)
                                        - {{ $product->brand->brand_name }}
                                    @endif
                                    [Stock: {{ $product->current_stock }} {{ $product->unit }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control quantity-input" name="items[${index}][quantity]" min="1" required placeholder="Enter quantity" />
                        <div class="form-text">
                            <small class="text-muted">Available: <span class="available-stock">0</span></small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item-btn">
                            <i class="ti tabler-trash me-1"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function updateRemoveButtons() {
        const itemCount = $('.item-row').length;
        if (itemCount > 1) {
            $('.remove-item-btn').show();
        } else {
            $('.remove-item-btn').hide();
        }
    }

    // Initialize remove buttons
    updateRemoveButtons();

    // Form validation before submit
    $('#transfer-form').submit(function(e) {
        let isValid = true;

        // Check if all required fields are filled
        $(this).find('input[required], select[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Check quantity validations
        $('.quantity-input').each(function() {
            const quantity = parseInt($(this).val()) || 0;
            const row = $(this).closest('.item-row');
            const availableStock = parseInt(row.find('.available-stock').text()) || 0;

            if (quantity > availableStock) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please correct the errors in the form before submitting.');
        }
    });
});
</script>
@endpush
