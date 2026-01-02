@extends('app')

@section('title', 'Collect Payment')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance / Payment Collections /</span> Collect Payment</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">New Payment Collection</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('finance.payment-collections.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" 
                                id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    data-balance="{{ $customer->outstanding_balance }}"
                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->full_name }} (Outstanding: ৳{{ number_format($customer->outstanding_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="customer-balance"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="sale_id" class="form-label">Sale Invoice (Optional)</label>
                        <select class="form-select @error('sale_id') is-invalid @enderror" 
                                id="sale_id" name="sale_id">
                            <option value="">Select Invoice (Optional)</option>
                            @foreach($sales as $sale)
                                <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                    {{ $sale->invoice_number }} (Due: ৳{{ number_format($sale->due_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('sale_id')
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
                        <i class="ti tabler-device-floppy me-1"></i> Collect Payment
                    </button>
                    <a href="{{ route('finance.payment-collections.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page_js')
<script>
    // Show customer balance when selected
    document.getElementById('customer_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const balance = selectedOption.getAttribute('data-balance');
        const balanceDisplay = document.getElementById('customer-balance');
        
        if (balance) {
            balanceDisplay.textContent = 'Outstanding Balance: ৳' + parseFloat(balance).toFixed(2);
        } else {
            balanceDisplay.textContent = '';
        }
    });
</script>
@endpush
@endsection
