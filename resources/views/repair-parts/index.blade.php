@extends('app')

@section('title', 'Repair Parts Catalog')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Repair Service /</span> Repair Parts Catalog</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-heading">{{ $statistics['total_parts'] }}</span>
                            <p class="text-muted mb-0">Total Parts</p>
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
                            <span class="text-heading">{{ $statistics['active_parts'] }}</span>
                            <p class="text-muted mb-0">Active Parts</p>
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
                            <span class="text-heading">{{ $statistics['low_stock_parts'] }}</span>
                            <p class="text-muted mb-0">Low Stock</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti tabler-alert-triangle ti-26px"></i>
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
                            <span class="text-heading">৳{{ number_format($statistics['total_value'], 2) }}</span>
                            <p class="text-muted mb-0">Total Value</p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
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
                    <label class="form-label">Category</label>
                    <select class="form-select" id="category-filter">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Brand</label>
                    <select class="form-select" id="brand-filter">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') === $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Stock Status</label>
                    <select class="form-select" id="stock-status-filter">
                        <option value="">All Stock Levels</option>
                        <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="reorder" {{ request('stock_status') === 'reorder' ? 'selected' : '' }}>Reorder Soon</option>
                        <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Supplier</label>
                    <select class="form-select" id="supplier-filter">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" id="search-filter" value="{{ request('search') }}" placeholder="Search by name, code, or description">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100" id="apply-filters">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Parts Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Repair Parts Catalog</h5>
            <a href="{{ route('repair-parts.create') }}" class="btn btn-primary">
                <i class="ti tabler-plus me-1"></i> Add Part
            </a>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-basic table">
                <thead>
                    <tr>
                        <th>Part Code</th>
                        <th>Part Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Stock</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($parts as $part)
                    <tr>
                        <td>
                            <a href="{{ route('repair-parts.show', $part) }}" class="text-decoration-none">
                                <strong>{{ $part->part_code }}</strong>
                            </a>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $part->part_name }}</strong>
                                @if($part->description)
                                    <br><small class="text-muted">{{ Str::limit($part->description, 50) }}</small>
                                @endif
                            </div>
                        </td>
                        <td>{{ $part->category ?: '-' }}</td>
                        <td>{{ $part->brand ?: '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $part->stock_status_badge_class }}">
                                {{ $part->current_stock }} {{ $part->unit }}
                            </span>
                            @if($part->needs_reorder)
                                <br><small class="text-warning">Reorder at {{ $part->reorder_level }}</small>
                            @endif
                        </td>
                        <td>{{ $part->purchase_price ? '৳' . number_format($part->purchase_price, 2) : '-' }}</td>
                        <td>{{ $part->selling_price ? '৳' . number_format($part->selling_price, 2) : '-' }}</td>
                        <td>{{ $part->primarySupplier ? $part->primarySupplier->supplier_name : '-' }}</td>
                        <td>
                            @if($part->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                            @if($part->is_discontinued)
                                <br><small class="text-danger">Discontinued</small>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Actions
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('repair-parts.show', $part) }}">
                                        <i class="ti tabler-eye me-1"></i> View Details
                                    </a>
                                    <a class="dropdown-item" href="{{ route('repair-parts.edit', $part) }}">
                                        <i class="ti tabler-pencil me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item" href="#" onclick="adjustStock({{ $part->id }}, '{{ $part->part_name }}', {{ $part->current_stock }})">
                                        <i class="ti tabler-package me-1"></i> Adjust Stock
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    @if($part->repairParts->isEmpty())
                                    <form action="{{ route('repair-parts.destroy', $part) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this part?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="ti tabler-trash me-1"></i> Delete
                                        </button>
                                    </form>
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
            {{ $parts->appends(request()->query())->links() }}
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
let currentPartId = null;

$(document).ready(function() {
    // Initialize DataTable
    $('.datatables-basic').DataTable({
        responsive: true,
        paging: false,
        searching: false,
        info: false,
        ordering: false
    });

    // Apply filters
    $('#apply-filters').click(function() {
        const params = new URLSearchParams();

        const category = $('#category-filter').val();
        if (category) params.append('category', category);

        const brand = $('#brand-filter').val();
        if (brand) params.append('brand', brand);

        const stockStatus = $('#stock-status-filter').val();
        if (stockStatus) params.append('stock_status', stockStatus);

        const supplier = $('#supplier-filter').val();
        if (supplier) params.append('supplier', supplier);

        const search = $('#search-filter').val();
        if (search) params.append('search', search);

        window.location.href = '{{ route("repair-parts.index") }}?' + params.toString();
    });

    // Enter key for search
    $('#search-filter').keypress(function(e) {
        if (e.which === 13) {
            $('#apply-filters').click();
        }
    });
});

function adjustStock(partId, partName, currentStock) {
    currentPartId = partId;
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
        url: `/repair-parts/${currentPartId}/stock`,
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
