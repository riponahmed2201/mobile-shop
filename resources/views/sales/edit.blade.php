@extends('app')

@section('title', 'Edit Sale')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Edit Sale #{{ $sale->invoice_number }}</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Sale</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.update', $sale->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ (old('customer_id') ?? $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} - {{ $customer->mobile_primary }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" class="form-control @error('sale_date') is-invalid @enderror" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Sale Type <span class="text-danger">*</span></label>
                        <select name="sale_type" class="form-select @error('sale_type') is-invalid @enderror" required>
                            <option value="RETAIL" {{ (old('sale_type') ?? $sale->sale_type) == 'RETAIL' ? 'selected' : '' }}>Retail</option>
                            <option value="WHOLESALE" {{ (old('sale_type') ?? $sale->sale_type) == 'WHOLESALE' ? 'selected' : '' }}>Wholesale</option>
                            <option value="EMI" {{ (old('sale_type') ?? $sale->sale_type) == 'EMI' ? 'selected' : '' }}>EMI</option>
                        </select>
                        @error('sale_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="CASH" {{ (old('payment_method') ?? $sale->payment_method) == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="CARD" {{ (old('payment_method') ?? $sale->payment_method) == 'CARD' ? 'selected' : '' }}>Card</option>
                            <option value="BKASH" {{ (old('payment_method') ?? $sale->payment_method) == 'BKASH' ? 'selected' : '' }}>bKash</option>
                            <option value="NAGAD" {{ (old('payment_method') ?? $sale->payment_method) == 'NAGAD' ? 'selected' : '' }}>Nagad</option>
                            <option value="BANK" {{ (old('payment_method') ?? $sale->payment_method) == 'BANK' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="MIXED" {{ (old('payment_method') ?? $sale->payment_method) == 'MIXED' ? 'selected' : '' }}>Mixed</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Paid Amount <span class="text-danger">*</span></label>
                        <input type="number" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" step="0.01" value="{{ old('paid_amount', $sale->paid_amount) }}" required>
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Sale Items</h5>
                <div id="items-container">
                    <!-- Items will be added here -->
                </div>
                
                <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addItem()">
                    <i class="ti ti-plus me-1"></i> Add Item
                </button>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Discount Amount</label>
                        <input type="number" name="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" step="0.01" value="{{ old('discount_amount', $sale->discount_amount) }}">
                        @error('discount_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control @error('tax_amount') is-invalid @enderror" step="0.01" value="{{ old('tax_amount', $sale->tax_amount) }}">
                        @error('tax_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="1">{{ $sale->notes }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script>
let itemIndex = 0;
const products = @json($products);
const existingItems = @json($sale->items);

function addItem(item = null) {
    const container = document.getElementById('items-container');
    const isExisting = item !== null;
    
    const productId = isExisting ? item.product_id : '';
    const quantity = isExisting ? item.quantity : 1;
    const unitPrice = isExisting ? item.unit_price : '';
    const totalPrice = isExisting ? item.total_price : '';

    const itemHtml = `
        <div class="card mb-2 item-row" data-index="${itemIndex}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Product</label>
                        <select name="items[${itemIndex}][product_id]" class="form-select" required onchange="updatePrice(${itemIndex})">
                            <option value="">Select Product</option>
                            ${products.map(p => {
                                const selected = p.id == productId ? 'selected' : '';
                                return `<option value="${p.id}" data-price="${p.selling_price}" ${selected}>${p.product_name}</option>`;
                            }).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" value="${quantity}" required onchange="calculateTotal(${itemIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="items[${itemIndex}][unit_price]" class="form-control" step="0.01" min="0" value="${unitPrice}" required onchange="calculateTotal(${itemIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <input type="number" name="items[${itemIndex}][total_price]" class="form-control" step="0.01" value="${totalPrice}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger w-100" onclick="removeItem(${itemIndex})">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
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
    if (existingItems && existingItems.length > 0) {
        existingItems.forEach(item => addItem(item));
    } else {
        addItem();
    }
});
</script>
@endpush
