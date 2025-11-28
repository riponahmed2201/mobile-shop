@extends('app')

@section('title', 'Add Product')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Products /</span> Add Product</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Product</h5>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Information -->
                <h6 class="mb-3">Basic Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_name">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code" name="product_code" maxlength="50" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="category_id">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="brand_id">Brand</label>
                        <select class="form-select" id="brand_id" name="brand_id">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_type">Product Type</label>
                        <select class="form-select" id="product_type" name="product_type">
                            <option value="MOBILE">Mobile</option>
                            <option value="ACCESSORY">Accessory</option>
                            <option value="PARTS">Parts</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="barcode">Barcode</label>
                        <input type="text" class="form-control" id="barcode" name="barcode" maxlength="100" />
                    </div>
                </div>

                <!-- Product Details -->
                <h6 class="mb-3 mt-4">Product Details</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="model_name">Model Name</label>
                        <input type="text" class="form-control" id="model_name" name="model_name" maxlength="100" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="color">Color</label>
                        <input type="text" class="form-control" id="color" name="color" maxlength="50" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="storage">Storage</label>
                        <input type="text" class="form-control" id="storage" name="storage" maxlength="50" placeholder="e.g., 128GB, 256GB" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="ram">RAM</label>
                        <input type="text" class="form-control" id="ram" name="ram" maxlength="50" placeholder="e.g., 4GB, 8GB" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="specifications">Specifications (JSON)</label>
                        <textarea class="form-control" id="specifications" name="specifications" rows="3" placeholder='{"screen": "6.1 inch", "camera": "48MP"}'></textarea>
                        <small class="text-muted">Enter JSON format specifications</small>
                    </div>
                </div>

                <!-- Pricing -->
                <h6 class="mb-3 mt-4">Pricing</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="purchase_price">Purchase Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="selling_price">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="mrp">MRP</label>
                        <input type="number" step="0.01" class="form-control" id="mrp" name="mrp" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="wholesale_price">Wholesale Price</label>
                        <input type="number" step="0.01" class="form-control" id="wholesale_price" name="wholesale_price" />
                    </div>
                </div>

                <!-- Stock Management -->
                <h6 class="mb-3 mt-4">Stock Management</h6>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="current_stock">Current Stock</label>
                        <input type="number" class="form-control" id="current_stock" name="current_stock" value="0" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="min_stock_level">Min Stock Level</label>
                        <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" value="5" />
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="reorder_level">Reorder Level</label>
                        <input type="number" class="form-control" id="reorder_level" name="reorder_level" value="10" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="unit">Unit</label>
                        <select class="form-select" id="unit" name="unit">
                            <option value="PCS" selected>PCS</option>
                            <option value="BOX">BOX</option>
                            <option value="SET">SET</option>
                            <option value="PAIR">PAIR</option>
                            <option value="KG">KG</option>
                            <option value="LITER">LITER</option>
                        </select>
                    </div>
                </div>

                <!-- Warranty -->
                <h6 class="mb-3 mt-4">Warranty</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="warranty_period">Warranty Period (Months)</label>
                        <input type="number" class="form-control" id="warranty_period" name="warranty_period" value="0" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="warranty_type">Warranty Type</label>
                        <input type="text" class="form-control" id="warranty_type" name="warranty_type" maxlength="100" placeholder="e.g., Manufacturer, Seller" />
                    </div>
                </div>

                <!-- Image -->
                <h6 class="mb-3 mt-4">Product Image</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_image">Product Image (Optional)</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" />
                        <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                        <div id="imagePreview" class="mt-3" style="display: none;">
                            <img id="previewImg" src="" alt="Preview" style="max-height: 150px; max-width: 150px; border: 1px solid #ddd; border-radius: 4px; padding: 5px;">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Product</button>
            </form>
        </div>
    </div>
</div>

@push('page_js')
<script>
    document.getElementById('product_image').addEventListener('change', function(e) {
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
@endsection
