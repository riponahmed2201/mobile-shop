@extends('app')

@section('title', 'Purchase Orders')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases /</span> Purchase Orders</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-primary rounded">
                                <i class="icon-base ti tabler-file-text icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Orders</span>
                    <h3 class="card-title mb-2" id="total-orders">{{ $statistics['total_orders'] }}</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-trending-up"></i> All orders</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-secondary rounded">
                                <i class="icon-base ti tabler-edit icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Draft</span>
                    <h3 class="card-title mb-2" id="draft-count">{{ $statistics['draft_count'] }}</h3>
                    <small class="text-secondary fw-semibold"><i class="icon-base ti tabler-edit"></i> Being prepared</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-info rounded">
                                <i class="icon-base ti tabler-send icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Confirmed</span>
                    <h3 class="card-title mb-2" id="confirmed-count">{{ $statistics['confirmed_count'] }}</h3>
                    <small class="text-info fw-semibold"><i class="icon-base ti tabler-send"></i> Sent to supplier</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-success rounded">
                                <i class="icon-base ti tabler-package icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Received</span>
                    <h3 class="card-title mb-2" id="received-count">{{ $statistics['received_count'] }}</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-package"></i> Goods received</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-danger rounded">
                                <i class="icon-base ti tabler-x icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Cancelled</span>
                    <h3 class="card-title mb-2" id="cancelled-count">{{ $statistics['cancelled_count'] }}</h3>
                    <small class="text-danger fw-semibold"><i class="icon-base ti tabler-x"></i> Not completed</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-warning rounded">
                                <i class="icon-base ti tabler-currency-dollar icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Due</span>
                    <h3 class="card-title mb-2" id="total-due">৳{{ number_format($statistics['total_due'], 2) }}</h3>
                    <small class="text-warning fw-semibold"><i class="icon-base ti tabler-currency-dollar"></i> Amount outstanding</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
                    <i class="ti tabler-plus me-1"></i> Create Purchase Order
                </a>
                <a href="{{ route('suppliers.create') }}" class="btn btn-success">
                    <i class="ti tabler-user-plus me-1"></i> Add New Supplier
                </a>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        <i class="ti tabler-filter me-1"></i> Filter Orders
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('')">All Orders</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('DRAFT')">Draft Orders</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('CONFIRMED')">Confirmed Orders</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('RECEIVED')">Received Orders</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterByStatus('CANCELLED')">Cancelled Orders</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Purchase Orders</h5>
            <div class="d-flex gap-2">
                <input type="text" class="form-control" id="searchInput" placeholder="Search orders..." style="width: 200px;">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown">
                        <i class="ti tabler-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="#" onclick="exportData('csv')">Export as CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportData('excel')">Export as Excel</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="purchase-orders-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Order Info</th>
                            <th>Status</th>
                            <th>Payment Info</th>
                            <th>Delivery Info</th>
                            <th>Created By</th>
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
    let table;
    let currentStatusFilter = '';

    $(function () {
      if ($.fn.DataTable.isDataTable('#purchase-orders-table')) {
          $('#purchase-orders-table').DataTable().destroy();
      }

      table = $('#purchase-orders-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('purchase-orders.index') }}",
              data: function(d) {
                  d.status = currentStatusFilter;
              }
          },
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'po_number', name: 'po_number'},
              {data: 'supplier_name', name: 'supplier_name'},
              {data: 'order_info', name: 'total_amount'},
              {data: 'status_badge', name: 'order_status'},
              {data: 'payment_info', name: 'payment_status'},
              {data: 'delivery_info', name: 'expected_delivery_date'},
              {data: 'created_by_name', name: 'created_by'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[1, 'desc']], // Sort by PO number (descending)
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });

      // Add search functionality
      $('#searchInput').on('keyup', function() {
          table.search($(this).val()).draw();
      });
    });

    function filterByStatus(status) {
        currentStatusFilter = status;
        table.ajax.reload();
    }

    function updateStatus(orderId, status) {
        let confirmMessage = '';
        switch(status) {
            case 'CONFIRMED':
                confirmMessage = 'Are you sure you want to confirm this purchase order? This will send it to the supplier.';
                break;
            case 'RECEIVED':
                confirmMessage = 'Are you sure you want to mark this order as received? This will update inventory levels.';
                break;
            case 'CANCELLED':
                confirmMessage = 'Are you sure you want to cancel this purchase order? This action cannot be undone.';
                break;
        }

        if (!confirm(confirmMessage)) {
            return;
        }

        $.ajax({
            url: `/purchase-orders/${orderId}/status`,
            method: 'PATCH',
            data: {
                status: status,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Reload the table
                    table.ajax.reload();
                    // Show success message
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr) {
                showToast('error', 'Failed to update order status.');
            }
        });
    }

    function exportData(format) {
        // Implement export functionality
        alert('Export functionality will be implemented');
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
