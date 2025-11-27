@extends('app')

@section('title', 'Edit Brand')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Brands /</span> Edit Brand</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Brand</h5>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="brand_name">Brand Name</label>
                    <input type="text" class="form-control" id="brand_name" name="brand_name" value="{{ $brand->brand_name }}" required />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="brand_logo">Brand Logo (Optional)</label>
                    @if($brand->brand_logo_url)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $brand->brand_logo_url) }}" alt="Current Logo" style="max-height: 100px;">
                            <p class="text-muted small">Current Logo</p>
                        </div>
                    @endif
                    <input type="file" class="form-control" id="brand_logo" name="brand_logo" accept="image/*" />
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB. Leave empty to keep current logo.</small>
                </div>
                <button type="submit" class="btn btn-primary">Update Brand</button>
            </form>
        </div>
    </div>
</div>
@endsection
