@extends('app')

@section('title', 'Create Purchase Order')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases / Purchase Orders /</span> Create Order</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create New Purchase Order</h5>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST" id="po-form">
                @csrf

                <!-- Order Information -->
                <h6 class="mb-3">Order Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="supplier_id">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                    @if($supplier->available_credit > 0)
                                        (Credit: ৳{{ number_format($supplier->available_credit, 2) }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="po_date">PO Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('po_date') is-invalid @enderror" id="po_date" name="po_date" value="{{ old('po_date', now()->format('Y-m-d')) }}" required />
                        @error('po_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="expected_delivery_date">Expected Delivery Date</label>
                        <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date') }}" />
                        @error('expected_delivery_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Order Items -->
                <h6 class="mb-3 mt-4">Order Items</h6>
                <div id="items-container">
                    <div class="item-row border rounded p-3 mb-3" data-index="0">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select product-select @error('items.0.product_id') is-invalid @enderror" name="items[0][product_id]" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}" data-name="{{ $product->product_name }}">
                                            {{ $product->product_name }}
                                            @if($product->model_name)
                                                ({{ $product->model_name }})
                                            @endif
                                            @if($product->brand)
                                                - {{ $product->brand->brand_name }}
                                            @endif
                                            [Current: ৳{{ number_format($product->purchase_price, 2) }}]
                                        </option>
                                    @endforeach
                                </select>
                                @error('items.0.product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control quantity-input @error('items.0.quantity') is-invalid @enderror" name="items[0][quantity]" min="1" step="1" required placeholder="Qty" />
                                @error('items.0.quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control unit-price-input @error('items.0.unit_price') is-invalid @enderror" name="items[0][unit_price]" min="0" step="0.01" required placeholder="0.00" />
                                @error('items.0.unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control total-input" readonly value="৳0.00" />
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item-btn" style="display: none;">
                                    <i class="ti tabler-trash me-1"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary" id="add-item-btn">
                        <i class="ti tabler-plus me-1"></i> Add Another Item
                    </button>
                </div>

                <!-- Order Summary -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Order Summary</h6>
                                <div class="row">
                                    <div class="col-6">Total Items:</div>
                                    <div class="col-6 fw-bold" id="total-items">1</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Total Quantity:</div>
                                    <div class="col-6 fw-bold" id="total-quantity">0</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Order Total:</div>
                                    <div class="col-6 fw-bold text-primary" id="order-total">৳0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <h6 class="mb-3 mt-4">Additional Information</h6>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Additional notes about this purchase order...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Create Purchase Order</button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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
    let itemIndex = 1; // Start from 1 since we have one item already

    // Add new item row
    $('#add-item-btn').click(function() {
        const newRow = createItemRow(itemIndex);
        $('#items-container').append(newRow);
        itemIndex++;

        // Show remove buttons if more than one item
        updateRemoveButtons();
        updateOrderSummary();
    });

    // Remove item row
    $(document).on('click', '.remove-item-btn', function() {
        $(this).closest('.item-row').remove();
        updateRemoveButtons();
        updateOrderSummary();
    });

    // Update unit price when product changes
    $(document).on('change', '.product-select', function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price') || 0;
        const row = $(this).closest('.item-row');
        row.find('.unit-price-input').val(price.toFixed(2));
        calculateRowTotal(row);
        updateOrderSummary();
    });

    // Calculate total when quantity or unit price changes
    $(document).on('input', '.quantity-input, .unit-price-input', function() {
        const row = $(this).closest('.item-row');
        calculateRowTotal(row);
        updateOrderSummary();
    });

    function createItemRow(index) {
        return `
            <div class="item-row border rounded p-3 mb-3" data-index="${index}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select product-select" name="items[${index}][product_id]" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}" data-name="{{ $product->product_name }}">
                                    {{ $product->product_name }}
                                    @if($product->model_name)
                                        ({{ $product->model_name }})
                                    @endif
                                    @if($product->brand)
                                        - {{ $product->brand->brand_name }}
                                    @endif
                                    [Current: ৳{{ number_format($product->purchase_price, 2) }}]
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control quantity-input" name="items[${index}][quantity]" min="1" step="1" required placeholder="Qty" />
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" class="form-control unit-price-input" name="items[${index}][unit_price]" min="0" step="0.01" required placeholder="0.00" />
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control total-input" readonly value="৳0.00" />
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item-btn">
                            <i class="ti tabler-trash me-1"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function calculateRowTotal(row) {
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const unitPrice = parseFloat(row.find('.unit-price-input').val()) || 0;
        const total = quantity * unitPrice;
        row.find('.total-input').val('৳' + total.toFixed(2));
        return total;
    }

    function updateOrderSummary() {
        let totalItems = $('.item-row').length;
        let totalQuantity = 0;
        let orderTotal = 0;

        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.quantity-input').val()) || 0;
            const unitPrice = parseFloat($(this).find('.unit-price-input').val()) || 0;
            totalQuantity += quantity;
            orderTotal += quantity * unitPrice;
        });

        $('#total-items').text(totalItems);
        $('#total-quantity').text(totalQuantity);
        $('#order-total').text('৳' + orderTotal.toFixed(2));
    }

    function updateRemoveButtons() {
        const itemCount = $('.item-row').length;
        if (itemCount > 1) {
            $('.remove-item-btn').show();
        } else {
            $('.remove-item-btn').hide();
        }
    }

    // Initialize calculations
    updateRemoveButtons();
    $('.product-select').first().trigger('change');

    // Form validation before submit
    $('#po-form').submit(function(e) {
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

        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endpush
