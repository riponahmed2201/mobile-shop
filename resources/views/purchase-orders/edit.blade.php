@extends('app')

@section('title', 'Edit Purchase Order')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases / Purchase Orders /</span> Edit Order</h4>

    <!-- Status Warning -->
    @if($purchaseOrder->order_status !== 'DRAFT')
    <div class="alert alert-warning d-flex align-items-center mb-4">
        <i class="ti tabler-alert-triangle fs-2 me-3"></i>
        <div>
            <h6 class="alert-heading mb-1">Order Status: {{ $purchaseOrder->status_label }}</h6>
            <p class="mb-0">This order cannot be edited because it is no longer in draft status.</p>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Purchase Order #{{ $purchaseOrder->po_number }}</h5>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            @if($purchaseOrder->order_status === 'DRAFT')
            <form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST" id="po-form">
                @csrf
                @method('PUT')

                <!-- Order Information -->
                <h6 class="mb-3">Order Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="supplier_id">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                        <input type="date" class="form-control @error('po_date') is-invalid @enderror" id="po_date" name="po_date" value="{{ old('po_date', $purchaseOrder->po_date->format('Y-m-d')) }}" required />
                        @error('po_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="expected_delivery_date">Expected Delivery Date</label>
                        <input type="date" class="form-control @error('expected_delivery_date') is-invalid @enderror" id="expected_delivery_date" name="expected_delivery_date" value="{{ old('expected_delivery_date', $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('Y-m-d') : '') }}" />
                        @error('expected_delivery_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Order Items -->
                <h6 class="mb-3 mt-4">Order Items</h6>
                <div id="items-container">
                    @foreach($purchaseOrder->items as $index => $item)
                    <div class="item-row border rounded p-3 mb-3" data-index="{{ $index }}">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Product <span class="text-danger">*</span></label>
                                <select class="form-select product-select @error('items.' . $index . '.product_id') is-invalid @enderror" name="items[{{ $index }}][product_id]" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}" data-name="{{ $product->product_name }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
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
                                @error('items.' . $index . '.product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control quantity-input @error('items.' . $index . '.quantity') is-invalid @enderror" name="items[{{ $index }}][quantity]" min="1" step="1" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" required placeholder="Qty" />
                                @error('items.' . $index . '.quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control unit-price-input @error('items.' . $index . '.unit_price') is-invalid @enderror" name="items[{{ $index }}][unit_price]" min="0" step="0.01" value="{{ old('items.' . $index . '.unit_price', $item->unit_price) }}" required placeholder="0.00" />
                                @error('items.' . $index . '.unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Total</label>
                                <input type="text" class="form-control total-input" readonly value="৳{{ number_format($item->total_price, 2) }}" />
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item-btn" style="{{ count($purchaseOrder->items) > 1 ? '' : 'display: none;' }}">
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

                <!-- Order Summary -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Order Summary</h6>
                                <div class="row">
                                    <div class="col-6">Total Items:</div>
                                    <div class="col-6 fw-bold" id="total-items">{{ count($purchaseOrder->items) }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Total Quantity:</div>
                                    <div class="col-6 fw-bold" id="total-quantity">{{ $purchaseOrder->total_quantity }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-6">Order Total:</div>
                                    <div class="col-6 fw-bold text-primary" id="order-total">৳{{ number_format($purchaseOrder->total_amount, 2) }}</div>
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
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" maxlength="1000" placeholder="Additional notes about this purchase order...">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Update Purchase Order</button>
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                    </div>
                </div>
            </form>
            @else
            <div class="alert alert-info">
                <i class="ti tabler-info-circle me-2"></i>
                <strong>Order Locked:</strong> This purchase order cannot be edited because it is {{ strtolower($purchaseOrder->status_label) }}.
                @if($purchaseOrder->can_receive)
                    You can mark it as received when goods arrive.
                @endif
            </div>

            <!-- Order Details Display -->
            <div class="row">
                <div class="col-md-6">
                    <h6>Order Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>PO Number:</strong></td><td>{{ $purchaseOrder->po_number }}</td></tr>
                        <tr><td><strong>Supplier:</strong></td><td>{{ $purchaseOrder->supplier->supplier_name }}</td></tr>
                        <tr><td><strong>PO Date:</strong></td><td>{{ $purchaseOrder->po_date->format('d M Y') }}</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge bg-{{ $purchaseOrder->status_badge_class }}">{{ $purchaseOrder->status_label }}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Order Summary</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Total Items:</strong></td><td>{{ $purchaseOrder->total_quantity }}</td></tr>
                        <tr><td><strong>Order Total:</strong></td><td>৳{{ number_format($purchaseOrder->total_amount, 2) }}</td></tr>
                        <tr><td><strong>Payment Status:</strong></td><td><span class="badge bg-{{ $purchaseOrder->payment_status_badge_class }}">{{ $purchaseOrder->payment_status_label }}</span></td></tr>
                        <tr><td><strong>Due Amount:</strong></td><td>৳{{ number_format($purchaseOrder->due_amount, 2) }}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Order Items Display -->
            <div class="mt-4">
                <h6>Order Items</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->product_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                <td>৳{{ number_format($item->total_price, 2) }}</td>
                                <td>
                                    @if($purchaseOrder->order_status === 'RECEIVED')
                                        {{ $item->received_quantity }} ({{ $item->received_percentage }}%)
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@if($purchaseOrder->order_status === 'DRAFT')
@push('page_js')
<script>
$(document).ready(function() {
    let itemIndex = {{ count($purchaseOrder->items) }}; // Start from existing count

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
    $('.product-select').trigger('change');
    updateRemoveButtons();

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
@endif
