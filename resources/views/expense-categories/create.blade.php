@extends('app')

@section('title', isset($expenseCategory) ? 'Edit Expense Category' : 'Create Expense Category')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Finance / Expense Categories /</span> 
        {{ isset($expenseCategory) ? 'Edit' : 'Create' }}
    </h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ isset($expenseCategory) ? 'Edit' : 'New' }} Expense Category</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($expenseCategory) ? route('finance.expense-categories.update', $expenseCategory) : route('finance.expense-categories.store') }}" 
                  method="POST">
                @csrf
                @if(isset($expenseCategory))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('category_name') is-invalid @enderror" 
                               id="category_name" name="category_name" 
                               value="{{ old('category_name', $expenseCategory->category_name ?? '') }}" required>
                        @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $expenseCategory->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $expenseCategory->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti tabler-device-floppy me-1"></i> Save
                    </button>
                    <a href="{{ route('finance.expense-categories.index') }}" class="btn btn-secondary">
                        <i class="ti tabler-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
