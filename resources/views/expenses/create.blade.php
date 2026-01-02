@extends('app')

@section('title', isset($expense) ? 'Edit Expense' : 'Create Expense')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Finance / Expenses /</span> 
        {{ isset($expense) ? 'Edit' : 'Create' }}
    </h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ isset($expense) ? 'Edit' : 'New' }} Expense</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($expense) ? route('finance.expenses.update', $expense) : route('finance.expenses.store') }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($expense))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expense_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('expense_category_id') is-invalid @enderror" 
                                id="expense_category_id" name="expense_category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ old('expense_category_id', $expense->expense_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('expense_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="expense_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('expense_date') is-invalid @enderror" 
                               id="expense_date" name="expense_date" 
                               value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('expense_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                               id="amount" name="amount" 
                               value="{{ old('amount', $expense->amount ?? '') }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" name="payment_method" required>
                            <option value="">Select Method</option>
                            <option value="CASH" {{ old('payment_method', $expense->payment_method ?? '') == 'CASH' ? 'selected' : '' }}>Cash</option>
                            <option value="CARD" {{ old('payment_method', $expense->payment_method ?? '') == 'CARD' ? 'selected' : '' }}>Card</option>
                            <option value="BANK" {{ old('payment_method', $expense->payment_method ?? '') == 'BANK' ? 'selected' : '' }}>Bank</option>
                            <option value="BKASH" {{ old('payment_method', $expense->payment_method ?? '') == 'BKASH' ? 'selected' : '' }}>bKash</option>
                            <option value="NAGAD" {{ old('payment_method', $expense->payment_method ?? '') == 'NAGAD' ? 'selected' : '' }}>Nagad</option>
                            <option value="OTHER" {{ old('payment_method', $expense->payment_method ?? '') == 'OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="reference_number" class="form-label">Reference Number</label>
                        <input type="text" class="form-control @error('reference_number') is-invalid @enderror" 
                               id="reference_number" name="reference_number" 
                               value="{{ old('reference_number', $expense->reference_number ?? '') }}">
                        @error('reference_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="receipt_file" class="form-label">Receipt/Invoice</label>
                        <input type="file" class="form-control @error('receipt_file') is-invalid @enderror" 
                               id="receipt_file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
                        @error('receipt_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if(isset($expense) && $expense->receipt_file_url)
                            <small class="text-muted">Current file: <a href="{{ Storage::url($expense->receipt_file_url) }}" target="_blank">View</a></small>
                        @endif
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $expense->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i> Save
                    </button>
                    <a href="{{ route('finance.expenses.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
