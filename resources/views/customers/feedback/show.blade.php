@extends('app')

@section('title', 'View Feedback')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers / Feedback /</span> View Feedback</h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Feedback Details</h5>
            <a href="{{ route('feedback.index') }}" class="btn btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Customer:</strong> {{ $feedback->customer ? $feedback->customer->full_name : 'Anonymous' }}
                </div>
                <div class="col-md-6">
                    <strong>Mobile:</strong> {{ $feedback->customer ? $feedback->customer->mobile_primary : '-' }}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Feedback Type:</strong> 
                    <span class="badge bg-primary">{{ $feedback->feedback_type }}</span>
                </div>
                <div class="col-md-6">
                    <strong>Rating:</strong>
                    @for($i = 1; $i <= 5; $i++)
                        <i class="ti tabler-star{{ $i <= $feedback->rating ? '-filled text-warning' : ' text-muted' }}"></i>
                    @endfor
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Feedback:</strong>
                    <p>{{ $feedback->feedback_text ?: 'No feedback text provided.' }}</p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Status:</strong>
                    <span class="badge {{ $feedback->is_public ? 'bg-success' : 'bg-secondary' }}">
                        {{ $feedback->is_public ? 'Public' : 'Private' }}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Date:</strong> {{ $feedback->created_at->format('d M Y, h:i A') }}
                </div>
            </div>

            @if($feedback->response_text)
                <div class="alert alert-info">
                    <strong>Response:</strong>
                    <p>{{ $feedback->response_text }}</p>
                    <small>Responded by: {{ $feedback->responder ? $feedback->responder->username : 'N/A' }} 
                    on {{ $feedback->responded_at ? $feedback->responded_at->format('d M Y, h:i A') : '' }}</small>
                </div>
            @else
                <form action="{{ route('feedback.respond', $feedback->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="response_text">Add Response</label>
                        <textarea class="form-control" id="response_text" name="response_text" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Response</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

