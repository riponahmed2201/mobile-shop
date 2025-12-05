@extends('app')

@section('title', 'Stock Transfer Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Stock Transfers /</span> Transfer Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock Transfer Information</h5>
                    <div>
                        <span class="badge {{ $stockTransfer->status_badge_class }} fs-6">
                            {{ $stockTransfer->status_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Transfer Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Transfer ID:</td>
                                    <td>#{{ $stockTransfer->id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">From:</td>
                                    <td><strong>{{ $stockTransfer->from_location }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">To:</td>
                                    <td><strong>{{ $stockTransfer->to_location }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Transfer Date:</td>
                                    <td>{{ $stockTransfer->transfer_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge {{ $stockTransfer->status_badge_class }}">
                                            {{ $stockTransfer->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Transferred By:</td>
                                    <td>{{ $stockTransfer->transferredBy ? $stockTransfer->transferredBy->username : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Transfer Summary</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Total Items:</td>
                                    <td><strong>{{ $stockTransfer->total_items }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Quantity:</td>
                                    <td><strong>{{ $stockTransfer->total_quantity }}</strong></td>
                                </tr>
                                @if($stockTransfer->status === 'COMPLETED')
                                <tr>
                                    <td class="text-muted">Received Quantity:</td>
                                    <td><strong>{{ $stockTransfer->total_received_quantity }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Completion:</td>
                                    <td>
                                        @if($stockTransfer->is_fully_received)
                                            <span class="badge bg-success">Fully Received</span>
                                        @else
                                            <span class="badge bg-warning">Partially Received</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Created:</td>
                                    <td>{{ $stockTransfer->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Last Updated:</td>
                                    <td>{{ $stockTransfer->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($stockTransfer->notes)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">Notes</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $stockTransfer->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Transfer Items -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted">Transfer Items</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            @if($stockTransfer->status === 'COMPLETED')
                                            <th>Received</th>
                                            <th>Status</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockTransfer->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->product->product_name }}
                                                @if($item->product->model_name)
                                                    <br><small class="text-muted">{{ $item->product->model_name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            @if($stockTransfer->status === 'COMPLETED')
                                            <td>{{ $item->received_quantity }}</td>
                                            <td>
                                                @if($item->is_fully_received)
                                                    <span class="badge bg-success">Received</span>
                                                @else
                                                    <span class="badge bg-warning">Partial ({{ $item->received_percentage }}%)</span>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        @if($stockTransfer->status === 'PENDING')
                        <a href="{{ route('stock-transfers.edit', $stockTransfer->id) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit
                        </a>
                        <form action="{{ route('stock-transfers.destroy', $stockTransfer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this transfer? This will restore the stock levels.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti tabler-trash me-1"></i> Delete
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('stock-transfers.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to List
                        </a>

                        <!-- Status Update Buttons -->
                        @if($stockTransfer->status === 'PENDING')
                        <div class="ms-auto">
                            <button type="button" class="btn btn-info" onclick="updateStatus({{ $stockTransfer->id }}, 'IN_TRANSIT')">
                                <i class="ti tabler-truck me-1"></i> Mark In Transit
                            </button>
                        </div>
                        @elseif($stockTransfer->status === 'IN_TRANSIT')
                        <div class="ms-auto">
                            <button type="button" class="btn btn-success" onclick="updateStatus({{ $stockTransfer->id }}, 'COMPLETED')">
                                <i class="ti tabler-check me-1"></i> Mark Completed
                            </button>
                        </div>
                        @endif

                        @if(in_array($stockTransfer->status, ['PENDING', 'IN_TRANSIT']))
                        <button type="button" class="btn btn-outline-danger" onclick="updateStatus({{ $stockTransfer->id }}, 'CANCELLED')">
                            <i class="ti tabler-x me-1"></i> Cancel Transfer
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Transfer Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Transfer Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Transfer Created</h6>
                                <small class="text-muted">{{ $stockTransfer->created_at->format('d M Y, H:i') }}</small>
                                @if($stockTransfer->transferredBy)
                                    <p class="mb-0">By: {{ $stockTransfer->transferredBy->username }}</p>
                                @endif
                            </div>
                        </div>

                        @if($stockTransfer->status === 'IN_TRANSIT' || $stockTransfer->status === 'COMPLETED' || $stockTransfer->status === 'CANCELLED')
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $stockTransfer->status === 'CANCELLED' ? 'bg-danger' : 'bg-info' }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">
                                    @if($stockTransfer->status === 'IN_TRANSIT')
                                        Marked In Transit
                                    @elseif($stockTransfer->status === 'COMPLETED')
                                        Completed
                                    @elseif($stockTransfer->status === 'CANCELLED')
                                        Cancelled
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $stockTransfer->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Transfer Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $stockTransfer->total_items }}</h4>
                            <small class="text-muted">Items</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $stockTransfer->total_quantity }}</h4>
                            <small class="text-muted">Quantity</small>
                        </div>
                    </div>
                    @if($stockTransfer->status === 'COMPLETED')
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $stockTransfer->total_received_quantity }}</h4>
                            <small class="text-muted">Received</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $stockTransfer->is_fully_received ? '100' : number_format(($stockTransfer->total_received_quantity / $stockTransfer->total_quantity) * 100, 1) }}%</h4>
                            <small class="text-muted">Complete</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_css')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    margin-bottom: 2px;
    font-size: 0.9rem;
}

.timeline-content small {
    font-size: 0.8rem;
}
</style>
@endpush

@push('page_js')
<script>
function updateStatus(transferId, status) {
    let confirmMessage = '';
    switch(status) {
        case 'IN_TRANSIT':
            confirmMessage = 'Are you sure you want to mark this transfer as "In Transit"?';
            break;
        case 'COMPLETED':
            confirmMessage = 'Are you sure you want to mark this transfer as "Completed"? This will finalize the transfer.';
            break;
        case 'CANCELLED':
            confirmMessage = 'Are you sure you want to cancel this transfer? This will restore the stock levels.';
            break;
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    $.ajax({
        url: `/stock-transfers/${transferId}/status`,
        method: 'PATCH',
        data: {
            status: status,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Reload the page to show updated status
                location.reload();
            } else {
                alert('Failed to update transfer status: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Failed to update transfer status. Please try again.');
        }
    });
}
</script>
@endpush
