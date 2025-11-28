@extends('app')

@section('title', 'New Sale')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> New Sale</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create New Sale</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sales.store') }}" method="POST" id="sale-form">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }} - {{ $customer->mobile_primary }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Sale Type <span class="text-danger">*</span></label>
                        <select name="sale_type" class="form-select" required>
                            <option value="RETAIL">Retail</option>
                            <option value="WHOLESALE">Wholesale</option>
                            <option value="EMI">EMI</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="CASH">Cash</option>
                            <option value="CARD">Card</option>
                            <option value="BKASH">bKash</option>
                            <option value="NAGAD">Nagad</option>
                            <option value="BANK">Bank Transfer</option>
                            <option value="MIXED">Mixed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Paid Amount <span class="text-danger">*</span></label>
                        <input type="number" name="paid_amount" class="form-control" step="0.01" min="0" value="0" required>
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
                        <input type="number" name="discount_amount" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tax Amount</label>
                        <input type="number" name="tax_amount" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="1"></textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Sale</button>
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

function addItem() {
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="card mb-2 item-row" data-index="${itemIndex}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Product</label>
                        <select name="items[${itemIndex}][product_id]" class="form-select" required onchange="updatePrice(${itemIndex})">
                            <option value="">Select Product</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price}">${p.product_name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" value="1" required onchange="calculateTotal(${itemIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="items[${itemIndex}][unit_price]" class="form-control" step="0.01" min="0" required onchange="calculateTotal(${itemIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <input type="number" name="items[${itemIndex}][total_price]" class="form-control" step="0.01" readonly>
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

// Add first item on load
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endpush
