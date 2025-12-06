@extends('app')

@section('title', 'Low Stock Alerts')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Low Stock Alerts</h4>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti tabler-alert-triangle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Out of Stock</p>
                                <h4 class="mb-0" id="out-of-stock-count">{{ $statistics['out_of_stock'] }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-urgent"></i>
                            <span class="fw-medium">Urgent action needed</span>
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
                                    <i class="ti tabler-exclamation-circle"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Critical</p>
                                <h4 class="mb-0" id="critical-count">{{ $statistics['critical_stock'] }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-danger small">
                            <i class="ti tabler-alert-square"></i>
                            <span class="fw-medium">Below minimum level</span>
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
                                    <i class="ti tabler-minus"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Low Stock</p>
                                <h4 class="mb-0" id="low-stock-count">
                                    {{ $statistics['total_low_stock'] - $statistics['critical_stock'] - $statistics['out_of_stock'] }}
                                </h4>
                            </div>
                        </div>
                        <p class="mb-0 text-warning small">
                            <i class="ti tabler-arrow-down"></i>
                            <span class="fw-medium">Below reorder level</span>
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
                                    <i class="ti tabler-package"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Total Products</p>
                                <h4 class="mb-0" id="total-products">{{ $statistics['total_products'] }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-info small">
                            <i class="ti tabler-cube"></i>
                            <span class="fw-medium">Active products</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-lg-4 col-md-6 col-sm-6">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti tabler-percentage"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Low Stock %</p>
                                <h4 class="mb-0" id="low-stock-percentage">{{ $statistics['low_stock_percentage'] }}%</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-primary small">
                            <i class="ti tabler-chart-pie"></i>
                            <span class="fw-medium">Of total inventory</span>
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
                                    <i class="ti tabler-circle-check"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 text-muted small">Healthy Stock</p>
                                <h4 class="mb-0" id="healthy-stock">
                                    {{ $statistics['total_products'] - $statistics['total_low_stock'] }}</h4>
                            </div>
                        </div>
                        <p class="mb-0 text-success small">
                            <i class="ti tabler-arrow-up"></i>
                            <span class="fw-medium">Above reorder level</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('low-stock.critical') }}" class="btn btn-danger">
                        <i class="ti tabler-exclamation me-1"></i> View Critical Stock ({{ $statistics['critical_stock'] }})
                    </a>
                    <a href="{{ route('low-stock.out-of-stock') }}" class="btn btn-danger">
                        <i class="ti tabler-alert-triangle me-1"></i> View Out of Stock ({{ $statistics['out_of_stock'] }})
                    </a>
                    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-primary">
                        <i class="ti tabler-plus me-1"></i> Add Stock Adjustment
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-success">
                        <i class="ti tabler-square-plus me-1"></i> Add New Product
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Low Stock Products</h5>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown"
                        data-bs-toggle="dropdown">
                        <i class="ti tabler-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('all')">All Low Stock</a>
                        </li>
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('critical')">Critical
                                Only</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('out_of_stock')">Out of Stock
                                Only</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="low-stock-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Stock Status</th>
                                <th>Suggested Action</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Stock Details Modal -->
        <div class="modal fade" id="stockDetailsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Stock Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="stockDetailsContent">
                        <!-- Content will be loaded here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
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
        let table;
        let currentFilter = 'all';

        $(function() {
            if ($.fn.DataTable.isDataTable('#low-stock-table')) {
                $('#low-stock-table').DataTable().destroy();
            }

            table = $('#low-stock-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('low-stock.index') }}",
                    data: function(d) {
                        d.filter = currentFilter;
                    }
                },
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
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'stock_info',
                        name: 'current_stock'
                    },
                    {
                        data: 'suggested_action',
                        name: 'current_stock'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [
                    [4, 'asc']
                ], // Sort by stock status (most critical first)
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });

            // Load statistics
            loadStatistics();
        });

        function filterByStatus(status) {
            currentFilter = status;
            table.ajax.reload();
        }

        function loadStatistics() {
            $.get("{{ route('low-stock.statistics') }}")
                .done(function(response) {
                    if (response.success) {
                        const stats = response.statistics;
                        $('#out-of-stock-count').text(stats.out_of_stock);
                        $('#critical-count').text(stats.critical_stock);
                        $('#low-stock-count').text(stats.total_low_stock - stats.critical_stock - stats.out_of_stock);
                        $('#total-products').text(stats.total_products);
                        $('#low-stock-percentage').text(stats.low_stock_percentage + '%');
                        $('#healthy-stock').text(stats.total_products - stats.total_low_stock);

                        // Update sidebar badge
                        $('#low-stock-badge').text(stats.urgent_products);
                        if (stats.urgent_products > 0) {
                            $('#low-stock-badge').show();
                        } else {
                            $('#low-stock-badge').hide();
                        }
                    }
                })
                .fail(function() {
                    console.error('Failed to load statistics');
                });
        }

        function showStockDetails(productId) {
            $.get("{{ route('low-stock.details') }}", {
                    product_id: productId
                })
                .done(function(response) {
                    if (response.success) {
                        const product = response.data.product;
                        const stockDetails = response.data.stock_details;

                        let content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Product Information</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Name:</strong></td><td>${product.product_name}</td></tr>
                                    <tr><td><strong>Model:</strong></td><td>${product.model_name || '-'}</td></tr>
                                    <tr><td><strong>Brand:</strong></td><td>${product.brand ? product.brand.brand_name : '-'}</td></tr>
                                    <tr><td><strong>Category:</strong></td><td>${product.category ? product.category.category_name : '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Stock Information</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Current Stock:</strong></td><td>${stockDetails.current_stock} ${product.unit}</td></tr>
                                    <tr><td><strong>Min Level:</strong></td><td>${stockDetails.min_stock_level} ${product.unit}</td></tr>
                                    <tr><td><strong>Reorder Level:</strong></td><td>${stockDetails.reorder_level} ${product.unit}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-${stockDetails.severity}">${stockDetails.status.replace('_', ' ')}</span></td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-${stockDetails.severity}">
                                    <strong>Alert:</strong> ${stockDetails.message}
                                    ${stockDetails.suggested_reorder > 0 ? '<br><strong>Suggested reorder quantity:</strong> ' + stockDetails.suggested_reorder + ' ' + product.unit : ''}
                                </div>
                            </div>
                        </div>
                    `;

                        $('#stockDetailsContent').html(content);
                        $('#stockDetailsModal').modal('show');
                    } else {
                        alert('Failed to load stock details: ' + response.message);
                    }
                })
                .fail(function() {
                    alert('Failed to load stock details. Please try again.');
                });
        }

        // Auto-refresh statistics every 5 minutes
        setInterval(loadStatistics, 300000);
    </script>
@endpush
