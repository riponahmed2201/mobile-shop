@extends('app')

@section('title', 'Add Cash Transaction')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Finance / Cash Book /</span> Add Transaction</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Manual Cash Transaction</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('finance.cash-book.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="transaction_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" 
                               id="transaction_date" name="transaction_date" 
                               value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="transaction_type" class="form-label">Transaction Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('transaction_type') is-invalid @enderror" 
                                id="transaction_type" name="transaction_type" required>
                            <option value="">Select Type</option>
                            <option value="OPENING_BALANCE" {{ old('transaction_type') == 'OPENING_BALANCE' ? 'selected' : '' }}>Opening Balance</option>
                            <option value="ADJUSTMENT" {{ old('transaction_type') == 'ADJUSTMENT' ? 'selected' : '' }}>Adjustment</option>
                        </select>
                        @error('transaction_type')
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
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" name="payment_method">
                            <option value="">Select Method</option>
                            <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="BANK" {{ old('payment_method') == 'BANK' ? 'selected' : '' }}>Bank</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i> Save
                    </button>
                    <a href="{{ route('finance.cash-book.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
