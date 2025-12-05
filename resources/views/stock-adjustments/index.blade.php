@extends('app')

@section('title', 'Stock Adjustments')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Stock Adjustments</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-primary rounded">
                                <i class="icon-base ti tabler-package-plus icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Adjustments</span>
                    <h3 class="card-title mb-2" id="total-adjustments">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-trending-up"></i> All time</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-success rounded">
                                <i class="icon-base ti tabler-plus icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Stock Added</span>
                    <h3 class="card-title mb-2" id="add-count">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-plus"></i> Positive adjustments</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-danger rounded">
                                <i class="icon-base ti tabler-minus icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Stock Removed</span>
                    <h3 class="card-title mb-2" id="remove-count">0</h3>
                    <small class="text-danger fw-semibold"><i class="icon-base ti tabler-minus"></i> Negative adjustments</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-warning rounded">
                                <i class="icon-base ti tabler-clock icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Recent (30 days)</span>
                    <h3 class="card-title mb-2" id="recent-adjustments">0</h3>
                    <small class="text-warning fw-semibold"><i class="icon-base ti tabler-calendar"></i> Last 30 days</small>
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
    $(function () {
      if ($.fn.DataTable.isDataTable('#stock-adjustments-table')) {
          $('#stock-adjustments-table').DataTable().destroy();
      }

      var table = $('#stock-adjustments-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('stock-adjustments.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'product_name', name: 'product_name'},
              {data: 'brand_name', name: 'brand_name'},
              {data: 'adjustment_type_label', name: 'adjustment_type'},
              {data: 'signed_quantity', name: 'quantity'},
              {data: 'reason', name: 'reason', render: function (data, type, row) {
                  if (!data) return '-';
                  return data.length > 50 ? data.substring(0, 50) + '...' : data;
              }},
              {data: 'adjusted_by_name', name: 'adjusted_by'},
              {data: 'adjustment_date_formatted', name: 'adjustment_date'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[7, 'desc']], // Sort by adjustment date (descending)
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });

      // Load statistics
      loadAdjustmentStatistics();
    });

    function loadAdjustmentStatistics() {
        // This would typically be an AJAX call to get statistics
        // For now, we'll calculate from the table data when it's loaded
        $('#stock-adjustments-table').on('draw.dt', function () {
            // You can implement AJAX call here to get statistics
            // For demo purposes, showing placeholder values
        });
    }
</script>
@endpush
