@extends('app')

@section('title', 'Edit Category')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Categories /</span> Edit Category</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Category</h5>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="category_name">Category Name</label>
                    <input type="text" class="form-control" id="category_name" name="category_name" value="{{ $category->category_name }}" required />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="category_type">Category Type</label>
                    <select class="form-select" id="category_type" name="category_type" required>
                        <option value="MOBILE" {{ $category->category_type == 'MOBILE' ? 'selected' : '' }}>Mobile</option>
                        <option value="ACCESSORY" {{ $category->category_type == 'ACCESSORY' ? 'selected' : '' }}>Accessory</option>
                        <option value="PARTS" {{ $category->category_type == 'PARTS' ? 'selected' : '' }}>Parts</option>
                        <option value="OTHER" {{ $category->category_type == 'OTHER' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="parent_category_id">Parent Category (Optional)</label>
                    <select class="form-select" id="parent_category_id" name="parent_category_id">
                        <option value="">None</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $category->parent_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </form>
        </div>
    </div>
</div>
@endsection
