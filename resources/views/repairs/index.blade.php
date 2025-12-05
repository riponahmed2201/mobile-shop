@extends('app')

@section('title', 'Repair Tickets')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Repair Service /</span> Repair Tickets</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ $statistics['total_tickets'] }}</span>
                            <p class="text-muted mb-0">Total Tickets</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ti tabler-tool ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ $statistics['in_progress'] }}</span>
                            <p class="text-muted mb-0">In Progress</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti tabler-clock ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ $statistics['ready'] }}</span>
                            <p class="text-muted mb-0">Ready for Delivery</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti tabler-check ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">৳{{ number_format($statistics['total_revenue'], 2) }}</span>
                            <p class="text-muted mb-0">Total Revenue</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti tabler-currency-taka ti-26px"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="status-filter">
                        <option value="">All Status</option>
                        <option value="RECEIVED" {{ request('status') === 'RECEIVED' ? 'selected' : '' }}>Received</option>
                        <option value="DIAGNOSED" {{ request('status') === 'DIAGNOSED' ? 'selected' : '' }}>Diagnosed</option>
                        <option value="IN_PROGRESS" {{ request('status') === 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                        <option value="PARTS_PENDING" {{ request('status') === 'PARTS_PENDING' ? 'selected' : '' }}>Parts Pending</option>
                        <option value="READY" {{ request('status') === 'READY' ? 'selected' : '' }}>Ready</option>
                        <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>Delivered</option>
                        <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Priority</label>
                    <select class="form-select" id="priority-filter">
                        <option value="">All Priority</option>
                        <option value="LOW" {{ request('priority') === 'LOW' ? 'selected' : '' }}>Low</option>
                        <option value="NORMAL" {{ request('priority') === 'NORMAL' ? 'selected' : '' }}>Normal</option>
                        <option value="HIGH" {{ request('priority') === 'HIGH' ? 'selected' : '' }}>High</option>
                        <option value="URGENT" {{ request('priority') === 'URGENT' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Technician</label>
                    <select class="form-select" id="technician-filter">
                        <option value="">All Technicians</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" {{ request('assigned_to') == $technician->id ? 'selected' : '' }}>
                                {{ $technician->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Customer</label>
                    <input type="text" class="form-control" id="customer-filter" value="{{ request('customer') }}" placeholder="Search by customer name or phone">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date Range</label>
                    <input type="date" class="form-control" id="date-from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="apply-filters">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Repair Tickets</h5>
            <a href="{{ route('repairs.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-1"></i> New Ticket
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Customer</th>
                        <th>Device</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Technician</th>
                        <th>Estimated Cost</th>
                        <th>Received Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('repairs.show', $ticket) }}" class="text-decoration-none">
                                <strong>{{ $ticket->ticket_number }}</strong>
                            </a>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $ticket->customer->full_name }}</strong><br>
                                <small class="text-muted">{{ $ticket->customer->mobile_primary }}</small>
                            </div>
                        </td>
                        <td>
                            @if($ticket->device_brand || $ticket->device_model)
                                {{ $ticket->device_brand }} {{ $ticket->device_model }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                            @if($ticket->imei_number)
                                <br><small class="text-muted">IMEI: {{ $ticket->imei_number }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $ticket->status_badge_class }}">
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $ticket->priority_badge_class }}">
                                {{ $ticket->priority_label }}
                            </span>
                        </td>
                            <td>
                            {{ $ticket->assignedTo ? $ticket->assignedTo->name : '<span class="text-muted">Unassigned</span>' }}
                        </td>
                        <td>
                            @if($ticket->final_cost)
                                ৳{{ number_format($ticket->final_cost, 2) }}
                            @elseif($ticket->estimated_cost)
                                <span class="text-muted">Est: ৳{{ number_format($ticket->estimated_cost, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $ticket->received_date->format('d M Y') }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('repairs.show', $ticket) }}">
                                        <i class="ti tabler-eye me-1"></i> View Details
                                    </a>
                                    @if($ticket->can_edit)
                                    <a class="dropdown-item" href="{{ route('repairs.edit', $ticket) }}">
                                        <i class="ti tabler-pencil me-1"></i> Edit
                                    </a>
                                    @endif
                                    @if($ticket->can_assign)
                                    <a class="dropdown-item" href="#" onclick="assignTicket({{ $ticket->id }})">
                                        <i class="ti tabler-user-plus me-1"></i> Assign Technician
                                    </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    @if($ticket->can_mark_ready)
                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $ticket->id }}, 'READY')">
                                        <i class="ti tabler-check me-1"></i> Mark Ready
                                    </a>
                                    @endif
                                    @if($ticket->can_deliver)
                                    <a class="dropdown-item" href="#" onclick="updateStatus({{ $ticket->id }}, 'DELIVERED')">
                                        <i class="ti tabler-truck-delivery me-1"></i> Mark Delivered
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $tickets->appends(request()->query())->links() }}
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

@push('page_js')
<script>
let currentTicketId = null;

$(document).ready(function() {
    // Initialize DataTable
    $('.datatables-basic').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        info: false,
        ordering: false
    });

    // Update badge counts on page load
    updateBadgeCounts();

    // Apply filters
    $('#apply-filters').click(function() {
        const params = new URLSearchParams();

        const status = $('#status-filter').val();
        if (status) params.append('status', status);

        const priority = $('#priority-filter').val();
        if (priority) params.append('priority', priority);

        const technician = $('#technician-filter').val();
        if (technician) params.append('assigned_to', technician);

        const customer = $('#customer-filter').val();
        if (customer) params.append('customer', customer);

        const dateFrom = $('#date-from').val();
        if (dateFrom) params.append('date_from', dateFrom);

        window.location.href = '{{ route("repairs.index") }}?' + params.toString();
    });

    // Enter key for customer filter
    $('#customer-filter').keypress(function(e) {
        if (e.which === 13) {
            $('#apply-filters').click();
        }
    });
});

// Update badge counts dynamically
function updateBadgeCounts() {
    // Update sidebar badges
    $.ajax({
        url: '{{ route("repairs.by-status", "IN_PROGRESS") }}',
        method: 'GET',
        success: function(data) {
            $('#in-progress-count').text(data.tickets ? data.tickets.length : 0);
        }
    });

    $.ajax({
        url: '{{ route("repairs.by-status", "READY") }}',
        method: 'GET',
        success: function(data) {
            $('#ready-count').text(data.tickets ? data.tickets.length : 0);
        }
    });
}

function assignTicket(ticketId) {
    currentTicketId = ticketId;
    $('#assignModal').modal('show');
}

function submitAssignment() {
    const technicianId = $('#technician-select').val();
    if (!technicianId) {
        alert('Please select a technician.');
        return;
    }

    $.ajax({
        url: `/repairs/${currentTicketId}/assign`,
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
                updateBadgeCounts(); // Update counts after status change
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
