@extends('app')

@section('title', 'IMEI Tracking')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> IMEI Tracking</h4>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti tabler-device-mobile"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total IMEI</p>
                                <h4 class="mb-0" id="total-imei">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-arrow-up"></i>
                            <span class="fw-medium">All tracked devices</span>
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
                                    <i class="ti tabler-package"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">In Stock</p>
                                <h4 class="mb-0" id="in-stock-imei">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-check"></i>
                            <span class="fw-medium">Available for sale</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti tabler-shopping-cart"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Sold</p>
                                <h4 class="mb-0" id="sold-imei">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-trending-up"></i>
                            <span class="fw-medium">Devices sold</span>
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
                                    <i class="ti tabler-alert-triangle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Warranty Expiring</p>
                                <h4 class="mb-0" id="expiring-imei">0</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-clock"></i>
                            <span class="fw-medium">Within 30 days</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">IMEI Records</h5>
                <a href="{{ route('imei.create') }}" class="btn btn-primary">Add IMEI</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="imei-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>IMEI Number</th>
                                <th>Product Name</th>
                                <th>Model</th>
                                <th>Brand</th>
                                <th>Status</th>
                                <th>Customer</th>
                                <th>Warranty Status</th>
                                <th>Purchase Date</th>
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
            if ($.fn.DataTable.isDataTable('#imei-table')) {
                $('#imei-table').DataTable().destroy();
            }

            var table = $('#imei-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('imei.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'imei_number',
                        name: 'imei_number'
                    },
                    {
                        data: 'product_name',
                        name: 'product_name'
                    },
                    {
                        data: 'model_name',
                        name: 'model_name'
                    },
                    {
                        data: 'brand_name',
                        name: 'brand_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'warranty_status',
                        name: 'warranty_status'
                    },
                    {
                        data: 'purchase_date',
                        name: 'purchase_date',
                        render: function(data, type, row) {
                            if (data) {
                                return new Date(data).toLocaleDateString();
                            }
                            return '-';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [1, 'asc']
                ], // Sort by IMEI number
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });

            // Load statistics
            loadImeiStatistics();
        });

        function loadImeiStatistics() {
            // This would typically be an AJAX call to get statistics
            // For now, we'll calculate from the table data when it's loaded
            $('#imei-table').on('draw.dt', function() {
                // You can implement AJAX call here to get statistics
                // For demo purposes, showing placeholder values
            });
        }
    </script>
@endpush
