@extends('app')

@section('title', 'New Return')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> New Return</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create Return Request</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('returns.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Sale Invoice # <span class="text-danger">*</span></label>
                        <select name="sale_id" class="form-select @error('sale_id') is-invalid @enderror" required>
                            <option value="">Select Sale Invoice</option>
                            @foreach($sales as $sale)
                                <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>{{ $sale->invoice_number }} - {{ $sale->customer ? $sale->customer->full_name : 'Walk-in' }} ({{ $sale->sale_date->format('d M Y') }})</option>
                            @endforeach
                        </select>
                        @error('sale_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror" value="{{ old('return_date', date('Y-m-d')) }}" required>
                        @error('return_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Return Type <span class="text-danger">*</span></label>
                        <select name="return_type" class="form-select @error('return_type') is-invalid @enderror" required>
                            <option value="REFUND" {{ old('return_type') == 'REFUND' ? 'selected' : '' }}>Refund</option>
                            <option value="EXCHANGE" {{ old('return_type') == 'EXCHANGE' ? 'selected' : '' }}>Exchange</option>
                            <option value="STORE_CREDIT" {{ old('return_type') == 'STORE_CREDIT' ? 'selected' : '' }}>Store Credit</option>
                        </select>
                        @error('return_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Restocking Fee</label>
                        <input type="number" name="restocking_fee" class="form-control @error('restocking_fee') is-invalid @enderror" step="0.01" min="0" value="{{ old('restocking_fee', 0) }}">
                        @error('restocking_fee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Return Reason <span class="text-danger">*</span></label>
                    <textarea name="return_reason" class="form-control @error('return_reason') is-invalid @enderror" rows="3" required placeholder="Describe the reason for return...">{{ old('return_reason') }}</textarea>
                    @error('return_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <h5 class="mb-3">Return Items <span class="text-danger">*</span></h5>
                <div class="alert alert-info" id="select-sale-alert">
                    <i class="ti ti-info-circle me-2"></i> Please select a sale invoice to view items.
                </div>
                <div id="items-container" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Select</th>
                                    <th>Product</th>
                                    <th>Sold Qty</th>
                                    <th>Return Qty</th>
                                    <th>Unit Price</th>
                                </tr>
                            </thead>
                            <tbody id="items-table-body">
                                <!-- Items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    @error('items')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Return Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saleSelect = document.querySelector('select[name="sale_id"]');
    const itemsContainer = document.getElementById('items-container');
    const itemsTableBody = document.getElementById('items-table-body');
    const selectSaleAlert = document.getElementById('select-sale-alert');

    saleSelect.addEventListener('change', function() {
        const saleId = this.value;
        if (saleId) {
            fetchItems(saleId);
        } else {
            hideItems();
        }
    });

    // If old input exists (validation error), trigger fetch
    if (saleSelect.value) {
        fetchItems(saleSelect.value);
    }

    function fetchItems(saleId) {
        fetch(`/sales/${saleId}/items`)
            .then(response => response.json())
            .then(items => {
                itemsTableBody.innerHTML = '';
                if (items.length > 0) {
                    items.forEach((item, index) => {
                        const row = `
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="items[${index}][selected]" class="form-check-input item-checkbox" value="1" onchange="toggleQty(${index})">
                                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                                </td>
                                <td>${item.product.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>
                                    <input type="number" name="items[${index}][quantity]" class="form-control form-control-sm item-qty" min="1" max="${item.quantity}" value="1" disabled required>
                                </td>
                                <td>৳${item.unit_price}</td>
                            </tr>
                        `;
                        itemsTableBody.insertAdjacentHTML('beforeend', row);
                    });
                    showItems();
                } else {
                    itemsTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No items found for this sale.</td></tr>';
                    showItems();
                }
            })
            .catch(error => {
                console.error('Error fetching items:', error);
                alert('Failed to load sale items.');
            });
    }

    function showItems() {
        itemsContainer.style.display = 'block';
        selectSaleAlert.style.display = 'none';
    }

    function hideItems() {
        itemsContainer.style.display = 'none';
        selectSaleAlert.style.display = 'block';
        itemsTableBody.innerHTML = '';
    }

    window.toggleQty = function(index) {
        const checkbox = document.querySelector(`input[name="items[${index}][selected]"]`);
        const qtyInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
        qtyInput.disabled = !checkbox.checked;
    };
});
</script>
@endpush
