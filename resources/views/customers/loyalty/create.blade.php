@extends('app')

@section('title', 'Add Loyalty Transaction')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers / Loyalty /</span> Add Transaction</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Loyalty Transaction</h5>
            <a href="{{ route('loyalty.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('loyalty.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="customer_id">Customer <span class="text-danger">*</span></label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-points="{{ $customer->loyalty_points }}">
                                    {{ $customer->full_name }} - {{ $customer->mobile_primary }} ({{ $customer->loyalty_points }} pts)
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" id="current_points"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="transaction_type">Transaction Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="transaction_type" name="transaction_type" required>
                            <option value="EARNED">Earned</option>
                            <option value="REDEEMED">Redeemed</option>
                            <option value="EXPIRED">Expired</option>
                            <option value="ADJUSTED">Adjusted</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="points">Points <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="points" name="points" required min="1" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="expiry_date">Expiry Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="reference_type">Reference Type</label>
                        <input type="text" class="form-control" id="reference_type" name="reference_type" maxlength="50" placeholder="e.g., sale, referral" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="reference_id">Reference ID</label>
                        <input type="number" class="form-control" id="reference_id" name="reference_id" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Transaction</button>
            </form>
        </div>
    </div>
</div>

@push('page_js')
<script>
    $('#customer_id').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const points = selectedOption.data('points') || 0;
        $('#current_points').text('Current Points: ' + points);
    });
</script>
@endpush
@endsection

