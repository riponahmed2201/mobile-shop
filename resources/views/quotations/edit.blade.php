@extends('app')

@section('title', 'Edit Quotation')

@push('page_css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Edit Quotation #{{ $quotation->quotation_number }}</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Quotation</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('quotations.update', $quotation->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" id="customer-select" class="select2 form-select @error('customer_id') is-invalid @enderror">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ (old('customer_id') ?? $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }}
                                    @if($customer->mobile_primary)
                                        ({{ $customer->mobile_primary }})
                                    @endif
                                    @if($customer->customer_code)
                                        | {{ $customer->customer_code }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Quotation Date <span class="text-danger">*</span></label>
                        <input type="date" name="quotation_date" class="form-control @error('quotation_date') is-invalid @enderror" value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" required>
                        @error('quotation_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until_date" class="form-control @error('valid_until_date') is-invalid @enderror" value="{{ old('valid_until_date', $quotation->valid_until_date ? $quotation->valid_until_date->format('Y-m-d') : '') }}">
                        @error('valid_until_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Quotation Items</h5>
                <div id="items-container"></div>
                
                <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addItem()">
                    <i class="ti tabler-plus me-1"></i> Add Item
                </button>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" step="0.01" value="{{ old('discount_amount', $quotation->discount_amount) }}">
                        @error('discount_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control @error('tax_amount') is-invalid @enderror" step="0.01" value="{{ old('tax_amount', $quotation->tax_amount) }}">
                        @error('tax_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="1">{{ $quotation->notes }}</textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms_conditions" class="form-control" rows="3">{{ $quotation->terms_conditions }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
let itemIndex = 0;
const products = @json($products);
const existingItems = @json($quotation->items);

function addItem(item = null) {
    const container = document.getElementById('items-container');
    const isExisting = item !== null;
    const currentIndex = itemIndex;
    const productId = isExisting ? item.product_id : '';
    const quantity = isExisting ? item.quantity : 1;
    const unitPrice = isExisting ? item.unit_price : '';
    const totalPrice = isExisting ? item.total_price : '';

    const itemHtml = `
        <div class="card mb-2 item-row" data-index="${currentIndex}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Product</label>
                        <select name="items[${currentIndex}][product_id]" class="form-select product-select" data-index="${currentIndex}" required>
                            <option value="">Select Product</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price}" ${p.id == productId ? 'selected' : ''}>${p.product_name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${currentIndex}][quantity]" class="form-control" min="1" value="${quantity}" required onchange="calculateTotal(${currentIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="items[${currentIndex}][unit_price]" class="form-control" step="0.01" min="0" value="${unitPrice}" required onchange="calculateTotal(${currentIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <input type="number" name="items[${currentIndex}][total_price]" class="form-control" step="0.01" value="${totalPrice}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger w-100" onclick="removeItem(${currentIndex})">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    
    // Initialize select2 on the newly added product select
    const $select = $(`select[name="items[${currentIndex}][product_id]"]`);
    $select.wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Product',
        dropdownParent: $select.parent(),
        allowClear: true,
        width: '100%'
    }).on('select2:select', function(e) {
        updatePrice(currentIndex);
    });
    
    itemIndex++;
}

function removeItem(index) {
    document.querySelector(`[data-index="${index}"]`).remove();
}

function updatePrice(index) {
    const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
    const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.dataset.price) {
        priceInput.value = selectedOption.dataset.price;
        calculateTotal(index);
    }
}

function calculateTotal(index) {
    const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
    const unitPrice = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
    const totalInput = document.querySelector(`input[name="items[${index}][total_price]"]`);
    totalInput.value = (quantity * unitPrice).toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for customer selection
    $('#customer-select').wrap('<div class="position-relative"></div>').select2({
        placeholder: 'Select Customer',
        dropdownParent: $('#customer-select').parent(),
        allowClear: true,
        width: '100%'
    });
    
    if (existingItems && existingItems.length > 0) {
        existingItems.forEach(item => addItem(item));
    } else {
        addItem();
    }
});
</script>
@endpush
