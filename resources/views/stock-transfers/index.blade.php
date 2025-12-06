@extends('app')

@section('title', 'Stock Transfers')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Stock Transfers</h4>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti tabler-truck-delivery"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Transfers</p>
                                <h4 class="mb-0" id="total-transfers">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-trending-up"></i>
                            <span class="fw-medium">All time</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti tabler-clock"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Pending</p>
                                <h4 class="mb-0" id="pending-count">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-hourglass"></i>
                            <span class="fw-medium">Awaiting action</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti tabler-truck"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">In Transit</p>
                                <h4 class="mb-0" id="in-transit-count">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-route"></i>
                            <span class="fw-medium">On the move</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti tabler-check"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Completed</p>
                                <h4 class="mb-0" id="completed-count">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-circle-check"></i>
                            <span class="fw-medium">Successfully delivered</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti tabler-x"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Cancelled</p>
                                <h4 class="mb-0" id="cancelled-count">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-circle-x"></i>
                            <span class="fw-medium">Not completed</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="ti tabler-calendar"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Recent (30 days)</p>
                                <h4 class="mb-0" id="recent-transfers">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-secondary small">
                            <i class="ti tabler-clock-hour-3"></i>
                            <span class="fw-medium">Last 30 days</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Stock Transfer History</h5>
                <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary">New Transfer</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="stock-transfers-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Transfer Route</th>
                                <th>Items</th>
                                <th>Total Qty</th>
                                <th>Status</th>
                                <th>Transfer Date</th>
                                <th>Transferred By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" />
@endpush

@push('page_js')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script type="text/javascript">
        $(function() {
            if ($.fn.DataTable.isDataTable('#stock-transfers-table')) {
                $('#stock-transfers-table').DataTable().destroy();
            }

            var table = $('#stock-transfers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock-transfers.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'transfer_details',
                        name: 'transfer_details'
                    },
                    {
                        data: 'total_items',
                        name: 'total_items'
                    },
                    {
                        data: 'total_quantity',
                        name: 'total_quantity'
                    },
                    {
                        data: 'status_badge',
                        name: 'status'
                    },
                    {
                        data: 'transfer_date_formatted',
                        name: 'transfer_date'
                    },
                    {
                        data: 'transferred_by_name',
                        name: 'transferred_by'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [5, 'desc']
                ], // Sort by transfer date (descending)
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });

            // Load statistics
            loadTransferStatistics();
        });

        function loadTransferStatistics() {
            // This would typically be an AJAX call to get statistics
            // For now, we'll calculate from the table data when it's loaded
            $('#stock-transfers-table').on('draw.dt', function() {
                // You can implement AJAX call here to get statistics
                // For demo purposes, showing placeholder values
            });
        }

        function updateStatus(transferId, status) {
            if (!confirm(
                    `Are you sure you want to change the transfer status to ${status.replace('_', ' ').toLowerCase()}?`)) {
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
                        // Reload the table
                        $('#stock-transfers-table').DataTable().ajax.reload();
                        // Show success message
                        showToast('success', response.message);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr) {
                    showToast('error', 'Failed to update transfer status.');
                }
            });
        }

        function showToast(type, message) {
            // Simple toast implementation
            const toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const toastHtml = `<div class="alert ${toastClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;

            $('body').append(toastHtml);

            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
@endpush
