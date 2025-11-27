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
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_name">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_code">Product Code</label>
                        <input type="text" class="form-control" id="product_code" name="product_code" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="category_id">Category</label>
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
                        <label class="form-label" for="purchase_price">Purchase Price</label>
                        <input type="number" step="0.01" class="form-control" id="purchase_price" name="purchase_price" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="selling_price">Selling Price</label>
                        <input type="number" step="0.01" class="form-control" id="selling_price" name="selling_price" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="current_stock">Current Stock</label>
                        <input type="number" class="form-control" id="current_stock" name="current_stock" value="0" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="product_type">Type</label>
                        <select class="form-select" id="product_type" name="product_type">
                            <option value="MOBILE">Mobile</option>
                            <option value="ACCESSORY">Accessory</option>
                            <option value="PARTS">Parts</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Product</button>
            </form>
        </div>
    </div>
</div>
@endsection
