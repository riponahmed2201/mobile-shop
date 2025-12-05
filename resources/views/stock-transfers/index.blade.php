@extends('app')

@section('title', 'Stock Transfers')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Stock Transfers</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-primary rounded">
                                <i class="icon-base ti tabler-package-plus icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Transfers</span>
                    <h3 class="card-title mb-2" id="total-transfers">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-trending-up"></i> All time</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-warning rounded">
                                <i class="icon-base ti tabler-clock icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Pending</span>
                    <h3 class="card-title mb-2" id="pending-count">0</h3>
                    <small class="text-warning fw-semibold"><i class="icon-base ti tabler-clock"></i> Awaiting action</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-info rounded">
                                <i class="icon-base ti tabler-truck icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">In Transit</span>
                    <h3 class="card-title mb-2" id="in-transit-count">0</h3>
                    <small class="text-info fw-semibold"><i class="icon-base ti tabler-truck"></i> On the move</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-success rounded">
                                <i class="icon-base ti tabler-check icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Completed</span>
                    <h3 class="card-title mb-2" id="completed-count">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-check"></i> Successfully delivered</small>
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
                    <h3 class="card-title mb-2" id="cancelled-count">0</h3>
                    <small class="text-danger fw-semibold"><i class="icon-base ti tabler-x"></i> Not completed</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-secondary rounded">
                                <i class="icon-base ti tabler-calendar icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Recent (30 days)</span>
                    <h3 class="card-title mb-2" id="recent-transfers">0</h3>
                    <small class="text-secondary fw-semibold"><i class="icon-base ti tabler-calendar"></i> Last 30 days</small>
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
    $(function () {
      if ($.fn.DataTable.isDataTable('#stock-transfers-table')) {
          $('#stock-transfers-table').DataTable().destroy();
      }

      var table = $('#stock-transfers-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('stock-transfers.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'transfer_details', name: 'transfer_details'},
              {data: 'total_items', name: 'total_items'},
              {data: 'total_quantity', name: 'total_quantity'},
              {data: 'status_badge', name: 'status'},
              {data: 'transfer_date_formatted', name: 'transfer_date'},
              {data: 'transferred_by_name', name: 'transferred_by'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[5, 'desc']], // Sort by transfer date (descending)
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });

      // Load statistics
      loadTransferStatistics();
    });

    function loadTransferStatistics() {
        // This would typically be an AJAX call to get statistics
        // For now, we'll calculate from the table data when it's loaded
        $('#stock-transfers-table').on('draw.dt', function () {
            // You can implement AJAX call here to get statistics
            // For demo purposes, showing placeholder values
        });
    }

    function updateStatus(transferId, status) {
        if (!confirm(`Are you sure you want to change the transfer status to ${status.replace('_', ' ').toLowerCase()}?`)) {
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
