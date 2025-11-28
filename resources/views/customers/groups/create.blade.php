@extends('app')

@section('title', 'Add Customer Group')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers / Groups /</span> Add New Group</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add New Customer Group</h5>
            <a href="{{ route('customer-groups.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('customer-groups.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="group_name">Group Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="group_name" name="group_name" required maxlength="100" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="color">Color</label>
                        <input type="color" class="form-control form-control-color" id="color" name="color" value="#696cff" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="discount_percentage">Discount Percentage</label>
                        <input type="number" step="0.01" class="form-control" id="discount_percentage" name="discount_percentage" value="0.00" min="0" max="100" />
                        <small class="text-muted">Discount percentage for this group</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="min_purchase_amount">Min Purchase Amount</label>
                        <input type="number" step="0.01" class="form-control" id="min_purchase_amount" name="min_purchase_amount" min="0" />
                        <small class="text-muted">Minimum purchase amount to qualify</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="min_purchase_count">Min Purchase Count</label>
                        <input type="number" class="form-control" id="min_purchase_count" name="min_purchase_count" min="0" />
                        <small class="text-muted">Minimum number of purchases to qualify</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Group</button>
            </form>
        </div>
    </div>
</div>
@endsection

