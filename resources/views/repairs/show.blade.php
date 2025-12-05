@extends('app')

@section('title', 'Repair Ticket Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Repair Service / Repair Tickets /</span> Ticket Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Repair Ticket #{{ $repairTicket->ticket_number }}</h5>
                    <div>
                        <span class="badge bg-{{ $repairTicket->status_badge_class }} fs-6 me-2">
                            {{ $repairTicket->status_label }}
                        </span>
                        <span class="badge bg-{{ $repairTicket->priority_badge_class }} fs-6">
                            {{ $repairTicket->priority_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Ticket Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Ticket Number:</td>
                                    <td><strong>{{ $repairTicket->ticket_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $repairTicket->status_badge_class }}">
                                            {{ $repairTicket->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Priority:</td>
                                    <td>
                                        <span class="badge bg-{{ $repairTicket->priority_badge_class }}">
                                            {{ $repairTicket->priority_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Received Date:</td>
                                    <td>{{ $repairTicket->received_date->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Estimated Delivery:</td>
                                    <td>{{ $repairTicket->estimated_delivery_date ? $repairTicket->estimated_delivery_date->format('d M Y') : '-' }}</td>
                                </tr>
                                @if($repairTicket->actual_delivery_date)
                                <tr>
                                    <td class="text-muted">Actual Delivery:</td>
                                    <td>{{ $repairTicket->actual_delivery_date->format('d M Y, H:i') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Created By:</td>
                                    <td>{{ $repairTicket->createdBy ? $repairTicket->createdBy->name : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Device & Cost Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Device:</td>
                                    <td><strong>{{ $repairTicket->device_info ?: 'Not specified' }}</strong></td>
                                </tr>
                                @if($repairTicket->imei_number)
                                <tr>
                                    <td class="text-muted">IMEI:</td>
                                    <td>{{ $repairTicket->imei_number }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Estimated Cost:</td>
                                    <td>{{ $repairTicket->estimated_cost ? '৳' . number_format($repairTicket->estimated_cost, 2) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Final Cost:</td>
                                    <td>{{ $repairTicket->final_cost ? '৳' . number_format($repairTicket->final_cost, 2) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Advance Payment:</td>
                                    <td>{{ $repairTicket->advance_payment ? '৳' . number_format($repairTicket->advance_payment, 2) : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Outstanding Balance:</td>
                                    <td>
                                        <span class="{{ $repairTicket->outstanding_balance > 0 ? 'text-danger' : 'text-success' }}">
                                            ৳{{ number_format($repairTicket->outstanding_balance, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Warranty Repair:</td>
                                    <td>{{ $repairTicket->warranty_repair ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Problem Description -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">Problem Description</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $repairTicket->problem_description }}</p>
                            </div>
                        </div>
                    </div>

                    @if($repairTicket->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Additional Notes</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $repairTicket->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Customer Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-primary rounded-circle">
                                {{ strtoupper(substr($repairTicket->customer->full_name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $repairTicket->customer->full_name }}</h6>
                            <small class="text-muted">{{ $repairTicket->customer->mobile_primary }}</small>
                            @if($repairTicket->customer->email)
                                <br><small class="text-muted">{{ $repairTicket->customer->email }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="mb-1">{{ $repairTicket->customer->repairTickets->count() }}</h6>
                            <small class="text-muted">Total Repairs</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-1">{{ $repairTicket->customer->total_purchases }}</h6>
                            <small class="text-muted">Total Purchases</small>
                        </div>
                        <div class="col-4">
                            <h6 class="mb-1">৳{{ number_format($repairTicket->customer->loyalty_points, 2) }}</h6>
                            <small class="text-muted">Loyalty Points</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technician Assignment -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Technician Assignment</h6>
                </div>
                <div class="card-body">
                    @if($repairTicket->assignedTo)
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3">
                                <div class="avatar-initial bg-info rounded-circle">
                                    {{ strtoupper(substr($repairTicket->assignedTo->name, 0, 1)) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $repairTicket->assignedTo->name }}</h6>
                                <small class="text-muted">Assigned Technician</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="ti tabler-user-x fs-1 text-muted mb-2"></i>
                            <p class="text-muted mb-0">No technician assigned</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($repairTicket->can_edit)
                        <a href="{{ route('repairs.edit', $repairTicket) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit Ticket
                        </a>
                        @endif

                        @if($repairTicket->can_assign)
                        <button type="button" class="btn btn-info" onclick="assignTicket({{ $repairTicket->id }})">
                            <i class="ti tabler-user-plus me-1"></i> Assign Technician
                        </button>
                        @endif

                        @if($repairTicket->status === 'RECEIVED')
                        <button type="button" class="btn btn-warning" onclick="updateStatus({{ $repairTicket->id }}, 'DIAGNOSED')">
                            <i class="ti tabler-stethoscope me-1"></i> Mark Diagnosed
                        </button>
                        @endif

                        @if($repairTicket->status === 'DIAGNOSED')
                        <button type="button" class="btn btn-info" onclick="updateStatus({{ $repairTicket->id }}, 'IN_PROGRESS')">
                            <i class="ti tabler-settings me-1"></i> Start Repair
                        </button>
                        @endif

                        @if($repairTicket->can_mark_ready)
                        <button type="button" class="btn btn-success" onclick="updateStatus({{ $repairTicket->id }}, 'READY')">
                            <i class="ti tabler-check me-1"></i> Mark Ready
                        </button>
                        @endif

                        @if($repairTicket->can_deliver)
                        <button type="button" class="btn btn-primary" onclick="updateStatus({{ $repairTicket->id }}, 'DELIVERED')">
                            <i class="ti tabler-truck-delivery me-1"></i> Mark Delivered
                        </button>
                        @endif

                        @if(in_array($repairTicket->status, ['RECEIVED', 'CANCELLED']))
                        <form action="{{ route('repairs.destroy', $repairTicket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this repair ticket?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti tabler-trash me-1"></i> Delete Ticket
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('repairs.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Status Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Ticket Received</h6>
                                <small class="text-muted">{{ $repairTicket->received_date->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        @foreach($repairTicket->statusHistory->sortBy('changed_at') as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Status Changed to {{ ucfirst(strtolower($history->new_status)) }}</h6>
                                <small class="text-muted">{{ $history->changed_at->format('d M Y, H:i') }}</small>
                                @if($history->changedBy)
                                    <p class="mb-0">By: {{ $history->changedBy->name }}</p>
                                @endif
                                @if($history->notes)
                                    <p class="mb-0 text-muted">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $repairTicket->days_since_received }}</h4>
                            <small class="text-muted">Days Since Received</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $repairTicket->parts->count() }}</h4>
                            <small class="text-muted">Parts Used</small>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <h4 class="mb-1">৳{{ number_format($repairTicket->total_parts_cost, 2) }}</h4>
                            <small class="text-muted">Parts Cost</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $repairTicket->feedbacks->count() }}</h4>
                            <small class="text-muted">Feedbacks</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Customer Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('customers.show', $repairTicket->customer) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti tabler-eye me-1"></i> View Customer Profile
                        </a>
                        <a href="{{ route('repairs.create', ['customer' => $repairTicket->customer->id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="ti tabler-plus me-1"></i> New Repair for Customer
                        </a>
                        <a href="tel:{{ $repairTicket->customer->mobile_primary }}" class="btn btn-outline-info btn-sm">
                            <i class="ti tabler-phone me-1"></i> Call Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Technician Modal -->
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Technician</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assign-form">
                    <div class="mb-3">
                        <label for="technician-select" class="form-label">Select Technician</label>
                        <select class="form-select" id="technician-select" name="assigned_to" required>
                            <option value="">Choose Technician</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignment()">Assign</button>
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
function assignTicket(ticketId) {
    $('#assignModal').modal('show');
}

function submitAssignment() {
    const technicianId = $('#technician-select').val();
    if (!technicianId) {
        alert('Please select a technician.');
        return;
    }

    $.ajax({
        url: `/repairs/${{ $repairTicket->id }}/assign`,
        method: 'PATCH',
        data: {
            assigned_to: technicianId,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#assignModal').modal('hide');
                location.reload();
            } else {
                alert('Failed to assign technician: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to assign technician. Please try again.');
        }
    });
}

function updateStatus(ticketId, status) {
    let confirmMessage = '';
    switch(status) {
        case 'DIAGNOSED':
            confirmMessage = 'Are you sure you want to mark this ticket as diagnosed?';
            break;
        case 'IN_PROGRESS':
            confirmMessage = 'Are you sure you want to start the repair process?';
            break;
        case 'READY':
            confirmMessage = 'Are you sure you want to mark this ticket as ready for delivery?';
            break;
        case 'DELIVERED':
            confirmMessage = 'Are you sure you want to mark this ticket as delivered? This will finalize the repair.';
            break;
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    $.ajax({
        url: `/repairs/${ticketId}/status`,
        method: 'PATCH',
        data: {
            status: status,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Failed to update ticket status: ' + response.message);
            }
        },
        error: function() {
            alert('Failed to update ticket status. Please try again.');
        }
    });
}
</script>
@endpush
