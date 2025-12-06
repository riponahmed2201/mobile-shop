@extends('app')

@section('title', 'Stock Adjustments')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Stock Adjustments</h4>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti tabler-box-multiple"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Adjustments</p>
                                <h4 class="mb-0" id="total-adjustments">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-trending-up"></i>
                            <span class="fw-medium">All time</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti tabler-plus"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Stock Added</p>
                                <h4 class="mb-0" id="add-count">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-arrow-up"></i>
                            <span class="fw-medium">Positive adjustments</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti tabler-minus"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Stock Removed</p>
                                <h4 class="mb-0" id="remove-count">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-arrow-down"></i>
                            <span class="fw-medium">Negative adjustments</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti tabler-clock"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Recent (30 days)</p>
                                <h4 class="mb-0" id="recent-adjustments">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-calendar"></i>
                            <span class="fw-medium">Last 30 days</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Stock Adjustment History</h5>
                <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">New Adjustment</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="stock-adjustments-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Adjustment Type</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Adjusted By</th>
                                <th>Date</th>
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
            if ($.fn.DataTable.isDataTable('#stock-adjustments-table')) {
                $('#stock-adjustments-table').DataTable().destroy();
            }

            var table = $('#stock-adjustments-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('stock-adjustments.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'adjustment_type_label',
                        name: 'adjustment_type'
                    },
                    {
                        data: 'signed_quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'reason',
                        name: 'reason',
                        render: function(data, type, row) {
                            if (!data) return '-';
                            return data.length > 50 ? data.substring(0, 50) + '...' : data;
                        }
                    },
                    {
                        data: 'adjusted_by_name',
                        name: 'adjusted_by'
                    },
                    {
                        data: 'adjustment_date_formatted',
                        name: 'adjustment_date'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [7, 'desc']
                ], // Sort by adjustment date (descending)
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });

            // Load statistics
            loadAdjustmentStatistics();
        });

        function loadAdjustmentStatistics() {
            // This would typically be an AJAX call to get statistics
            // For now, we'll calculate from the table data when it's loaded
            $('#stock-adjustments-table').on('draw.dt', function() {
                // You can implement AJAX call here to get statistics
                // For demo purposes, showing placeholder values
            });
        }
    </script>
@endpush
