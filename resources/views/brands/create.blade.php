@extends('app')

@section('title', 'Add Brand')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Brands /</span> Add Brand</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Brand</h5>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="brand_name">Brand Name</label>
                    <input type="text" class="form-control" id="brand_name" name="brand_name" placeholder="Samsung" required />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="brand_logo">Brand Logo (Optional)</label>
                    <input type="file" class="form-control" id="brand_logo" name="brand_logo" accept="image/*" />
                    <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                    <div id="imagePreview" class="mt-3" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-height: 150px; max-width: 150px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Brand</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script>
    document.getElementById('brand_logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const previewDiv = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.style.display = 'none';
        }
    });
</script>
@endpush
