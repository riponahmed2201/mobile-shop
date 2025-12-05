@extends('app')

@section('title', 'Repair Part Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Repair Service / Repair Parts /</span> Part Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $repairPart->part_name }}</h5>
                    <div>
                        <span class="badge bg-{{ $repairPart->stock_status_badge_class }} fs-6 me-2">
                            {{ $repairPart->stock_status_label }}
                        </span>
                        @if($repairPart->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Part Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Part Code:</td>
                                    <td><strong>{{ $repairPart->part_code }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Part Name:</td>
                                    <td><strong>{{ $repairPart->part_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Category:</td>
                                    <td>{{ $repairPart->category ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Subcategory:</td>
                                    <td>{{ $repairPart->subcategory ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Brand:</td>
                                    <td>{{ $repairPart->brand ?: '-' }}</td>
                                </tr>
                                @if($repairPart->description)
                                <tr>
                                    <td class="text-muted">Description:</td>
                                    <td>{{ $repairPart->description }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Inventory & Pricing</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Current Stock:</td>
                                    <td>
                                        <span class="badge bg-{{ $repairPart->stock_status_badge_class }}">
                                            {{ $repairPart->current_stock }} {{ $repairPart->unit }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Min Stock Level:</td>
                                    <td>{{ $repairPart->min_stock_level }} {{ $repairPart->unit }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Reorder Level:</td>
                                    <td>{{ $repairPart->reorder_level }} {{ $repairPart->unit }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Stock Value:</td>
                                    <td>৳{{ number_format($repairPart->stock_value, 2) }}</td>
                                </tr>
                                @if($repairPart->profit_margin !== null)
                                <tr>
                                    <td class="text-muted">Profit Margin:</td>
                                    <td>{{ number_format($repairPart->profit_margin, 1) }}%</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Pricing Details -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Pricing Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Purchase Price</h6>
                                            <h4 class="text-primary mb-0">
                                                {{ $repairPart->purchase_price ? '৳' . number_format($repairPart->purchase_price, 2) : '-' }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Selling Price</h6>
                                            <h4 class="text-success mb-0">
                                                {{ $repairPart->selling_price ? '৳' . number_format($repairPart->selling_price, 2) : '-' }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">MRP</h6>
                                            <h4 class="text-info mb-0">
                                                {{ $repairPart->mrp ? '৳' . number_format($repairPart->mrp, 2) : '-' }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compatible Devices -->
                    @if($repairPart->compatible_devices && count($repairPart->compatible_devices) > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Compatible Devices</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($repairPart->compatible_devices as $device)
                                    <span class="badge bg-primary">{{ $device }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Supplier Information -->
                    @if($repairPart->primarySupplier)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Primary Supplier</h6>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <div class="avatar-initial bg-success rounded-circle">
                                        {{ strtoupper(substr($repairPart->primarySupplier->supplier_name, 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $repairPart->primarySupplier->supplier_name }}</h6>
                                    @if($repairPart->supplier_part_code)
                                        <small class="text-muted">Part Code: {{ $repairPart->supplier_part_code }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Location Information -->
                    @if($repairPart->location || $repairPart->bin_location)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Storage Location</h6>
                            <div class="row">
                                @if($repairPart->location)
                                <div class="col-md-6">
                                    <strong>Location:</strong> {{ $repairPart->location }}
                                </div>
                                @endif
                                @if($repairPart->bin_location)
                                <div class="col-md-6">
                                    <strong>Bin:</strong> {{ $repairPart->bin_location }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Usage Statistics -->
            @if($usageStats['total_used'] > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Usage Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4 class="mb-1">{{ $usageStats['total_used'] }}</h4>
                            <small class="text-muted">Total Used</small>
                        </div>
                        <div class="col-md-4">
                            <h4 class="mb-1">{{ $usageStats['repairs_count'] }}</h4>
                            <small class="text-muted">Repair Tickets</small>
                        </div>
                        <div class="col-md-4">
                            <h4 class="mb-1">{{ $usageStats['recent_repairs']->count() }}</h4>
                            <small class="text-muted">Recent Repairs</small>
                        </div>
                    </div>

                    @if($usageStats['recent_repairs']->count() > 0)
                    <div class="mt-4">
                        <h6>Recent Usage</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Customer</th>
                                        <th>Quantity Used</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usageStats['recent_repairs'] as $repair)
                                    <tr>
                                        <td>
                                            <a href="{{ route('repairs.show', $repair['ticket_number']) }}" class="text-decoration-none">
                                                {{ $repair['ticket_number'] }}
                                            </a>
                                        </td>
                                        <td>{{ $repair['customer'] }}</td>
                                        <td>{{ $repair['quantity'] }}</td>
                                        <td>{{ $repair['date'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('repair-parts.edit', $repairPart) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit Part
                        </a>
                        <button type="button" class="btn btn-info" onclick="adjustStock({{ $repairPart->id }}, '{{ $repairPart->part_name }}', {{ $repairPart->current_stock }})">
                            <i class="ti tabler-package me-1"></i> Adjust Stock
                        </button>
                        <a href="{{ route('repair-parts.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to Catalog
                        </a>

                        @if($repairPart->repairParts->isEmpty())
                        <form action="{{ route('repair-parts.destroy', $repairPart) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this part?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti tabler-trash me-1"></i> Delete Part
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $repairPart->current_stock }}</h4>
                            <small class="text-muted">Current Stock</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">{{ $repairPart->repairParts->count() }}</h4>
                            <small class="text-muted">Times Used</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Stock Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            @if($repairPart->stock_status === 'out_of_stock')
                                <span class="avatar-initial rounded bg-danger">!</span>
                            @elseif($repairPart->stock_status === 'low_stock')
                                <span class="avatar-initial rounded bg-warning">⚠</span>
                            @elseif($repairPart->stock_status === 'reorder')
                                <span class="avatar-initial rounded bg-info">↻</span>
                            @else
                                <span class="avatar-initial rounded bg-success">✓</span>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $repairPart->stock_status_label }}</h6>
                            <small class="text-muted">
                                @if($repairPart->stock_status === 'out_of_stock')
                                    Out of stock - needs immediate restocking
                                @elseif($repairPart->stock_status === 'low_stock')
                                    Below minimum stock level
                                @elseif($repairPart->stock_status === 'reorder')
                                    Consider reordering soon
                                @else
                                    Stock levels are good
                                @endif
                            </small>
                        </div>
                    </div>

                    @if($repairPart->needs_reorder)
                    <div class="alert alert-info">
                        <i class="ti tabler-info-circle me-1"></i>
                        <strong>Reorder Recommended:</strong> Current stock ({{ $repairPart->current_stock }}) is below reorder level ({{ $repairPart->reorder_level }})
                    </div>
                    @endif
                </div>
            </div>

            <!-- Part Details -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Part Details</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <span class="badge bg-secondary">{{ $repairPart->unit }}</span>
                            <br><small class="text-muted">Unit</small>
                        </div>
                        <div class="col-6">
                            @if($repairPart->is_discontinued)
                                <span class="badge bg-danger">Discontinued</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                            <br><small class="text-muted">Status</small>
                        </div>
                    </div>

                    @if($repairPart->created_at)
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            Created: {{ $repairPart->created_at->format('d M Y') }}<br>
                            @if($repairPart->updated_at && $repairPart->updated_at != $repairPart->created_at)
                                Updated: {{ $repairPart->updated_at->format('d M Y') }}
                            @endif
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock Level</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 id="part-name-display"></h6>
                    <p class="text-muted mb-0">Current Stock: <span id="current-stock-display"></span></p>
                </div>
                <form id="stock-form">
                    <div class="mb-3">
                        <label for="adjustment" class="form-label">Adjustment Amount</label>
                        <input type="number" class="form-control" id="adjustment" name="adjustment" required>
                        <div class="form-text">Use positive numbers to add stock, negative to reduce</div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <select class="form-select" id="reason" name="reason" required>
                            <option value="">Select Reason</option>
                            <option value="Purchase">Purchase</option>
                            <option value="Return">Return</option>
                            <option value="Damage">Damage</option>
                            <option value="Lost">Lost</option>
                            <option value="Correction">Correction</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" maxlength="500"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitStockAdjustment()">Update Stock</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_js')
<script>
function adjustStock(partId, partName, currentStock) {
    $('#part-name-display').text(partName);
    $('#current-stock-display').text(currentStock);
    $('#adjustment').val('');
    $('#reason').val('');
    $('#notes').val('');
    $('#stockModal').modal('show');
}

function submitStockAdjustment() {
    const adjustment = parseInt($('#adjustment').val());
    const reason = $('#reason').val();
    const notes = $('#notes').val();

    if (!adjustment || !reason) {
        alert('Please fill in all required fields.');
        return;
    }

    $.ajax({
        url: `/repair-parts/{{ $repairPart->id }}/stock`,
        method: 'PATCH',
        data: {
            adjustment: adjustment,
            reason: reason,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#stockModal').modal('hide');
                location.reload();
            } else {
                alert('Failed to update stock: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Failed to update stock: ' + (response?.message || 'Unknown error'));
        }
    });
}
</script>
@endpush
