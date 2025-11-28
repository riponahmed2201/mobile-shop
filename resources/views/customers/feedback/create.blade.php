@extends('app')

@section('title', 'Add Customer Feedback')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers / Feedback /</span> Add Feedback</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Add Customer Feedback</h5>
            <a href="{{ route('feedback.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="customer_id">Customer</label>
                        <select class="form-select" id="customer_id" name="customer_id">
                            <option value="">Select Customer (Optional)</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }} - {{ $customer->mobile_primary }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="feedback_type">Feedback Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="feedback_type" name="feedback_type" required>
                            <option value="SALE">Sale</option>
                            <option value="REPAIR">Repair</option>
                            <option value="SERVICE">Service</option>
                            <option value="GENERAL">General</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="rating">Rating <span class="text-danger">*</span></label>
                        <select class="form-select" id="rating" name="rating" required>
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5" selected>5 Stars</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1">
                            <label class="form-check-label" for="is_public">Make Public</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="feedback_text">Feedback Text</label>
                        <textarea class="form-control" id="feedback_text" name="feedback_text" rows="4"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Feedback</button>
            </form>
        </div>
    </div>
</div>
@endsection

