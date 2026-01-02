@extends('app')

@section('title', 'Record Supplier Payment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance / Supplier Payments /</span> Record Payment</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">New Supplier Payment</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('finance.supplier-payments.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                id="supplier_id" name="supplier_id" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" 
                                    data-balance="{{ $supplier->outstanding_balance }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }} (Outstanding: ৳{{ number_format($supplier->outstanding_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="supplier-balance"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="purchase_order_id" class="form-label">Purchase Order (Optional)</label>
                        <select class="form-select @error('purchase_order_id') is-invalid @enderror" 
                                id="purchase_order_id" name="purchase_order_id">
                            <option value="">Select PO (Optional)</option>
                            @foreach($purchaseOrders as $po)
                                <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                    {{ $po->po_number }} (Due: ৳{{ number_format($po->due_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('purchase_order_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" name="payment_method" required>
                            <option value="">Select Method</option>
                            <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="CARD" {{ old('payment_method') == 'CARD' ? 'selected' : '' }}>Card</option>
                            <option value="BANK" {{ old('payment_method') == 'BANK' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="BKASH" {{ old('payment_method') == 'BKASH' ? 'selected' : '' }}>bKash</option>
                            <option value="NAGAD" {{ old('payment_method') == 'NAGAD' ? 'selected' : '' }}>Nagad</option>
                            <option value="OTHER" {{ old('payment_method') == 'OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                               id="payment_date" name="payment_date" 
                               value="{{ old('payment_date', date('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                               id="reference_number" name="reference_number" 
                               value="{{ old('reference_number') }}" placeholder="Check/Transaction ID">
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i> Record Payment
                    </button>
                    <a href="{{ route('finance.supplier-payments.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page_js')
<script>
    // Show supplier balance when selected
    document.getElementById('supplier_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const balance = selectedOption.getAttribute('data-balance');
        const balanceDisplay = document.getElementById('supplier-balance');
        
        if (balance) {
            balanceDisplay.textContent = 'Outstanding Balance: ৳' + parseFloat(balance).toFixed(2);
        } else {
            balanceDisplay.textContent = '';
        }
    });
</script>
@endpush
@endsection
