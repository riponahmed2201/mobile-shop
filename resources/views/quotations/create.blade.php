@extends('app')

@section('title', 'New Quotation')

@push('page_css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> New Quotation</h4>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Create New Quotation</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('quotations.store') }}" method="POST" id="quotation-form">
                    @csrf

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" id="customer-select" class="select2 form-select @error('customer_id') is-invalid @enderror">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                            <input type="date" name="quotation_date"
                                class="form-control @error('quotation_date') is-invalid @enderror"
                                value="{{ old('quotation_date', date('Y-m-d')) }}" required>
                            @error('quotation_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Valid Until</label>
                            <input type="date" name="valid_until_date"
                                class="form-control @error('valid_until_date') is-invalid @enderror"
                                value="{{ old('valid_until_date', date('Y-m-d', strtotime('+30 days'))) }}">
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
                            <input type="number" name="discount_amount" class="form-control" step="0.01" min="0"
                                value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tax Amount</label>
                            <input type="number" name="tax_amount" class="form-control" step="0.01" min="0"
                                value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="1"></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Terms & Conditions</label>
                        <textarea name="terms_conditions" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Quotation</button>
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

        function addItem() {
            const container = document.getElementById('items-container');
            const currentIndex = itemIndex;
            const itemHtml = `
        <div class="card mb-2 item-row" data-index="${currentIndex}">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Product</label>
                        <select name="items[${currentIndex}][product_id]" class="form-select product-select" data-index="${currentIndex}" required>
                            <option value="">Select Product</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price}">${p.product_name}</option>`).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="items[${currentIndex}][quantity]" class="form-control" min="1" value="1" required onchange="calculateTotal(${currentIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price</label>
                        <input type="number" name="items[${currentIndex}][unit_price]" class="form-control" step="0.01" min="0" required onchange="calculateTotal(${currentIndex})">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <input type="number" name="items[${currentIndex}][total_price]" class="form-control" step="0.01" readonly>
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

            addItem();
        });
    </script>
@endpush
