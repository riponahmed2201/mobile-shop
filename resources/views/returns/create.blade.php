@extends('app')

@section('title', 'New Return')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> New Return</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Create Return Request</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('returns.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Sale Invoice # <span class="text-danger">*</span></label>
                        <input type="text" name="sale_invoice" class="form-control" placeholder="Enter invoice number" required>
                        <small class="text-muted">Search and select the original sale</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Return Date <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Return Type <span class="text-danger">*</span></label>
                        <select name="return_type" class="form-select" required>
                            <option value="REFUND">Refund</option>
                            <option value="EXCHANGE">Exchange</option>
                            <option value="STORE_CREDIT">Store Credit</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Restocking Fee</label>
                        <input type="number" name="restocking_fee" class="form-control" step="0.01" min="0" value="0">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Return Reason <span class="text-danger">*</span></label>
                    <textarea name="return_reason" class="form-control" rows="3" required placeholder="Describe the reason for return..."></textarea>
                </div>

                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Note:</strong> Return items will be added after selecting the sale invoice. This return will be in PENDING status and require approval.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('returns.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Return Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
