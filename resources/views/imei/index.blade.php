@extends('app')

@section('title', 'IMEI Tracking')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> IMEI Tracking</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-primary rounded">
                                <i class="icon-base ti tabler-device-mobile icon-sm"></i>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" id="cardOpt1" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-base ti tabler-dots-vertical icon-xs text-muted"></i>
                            </button>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total IMEI</span>
                    <h3 class="card-title mb-2" id="total-imei">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-chevron-up"></i> All tracked devices</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-success rounded">
                                <i class="icon-base ti tabler-package icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">In Stock</span>
                    <h3 class="card-title mb-2" id="in-stock-imei">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-chevron-up"></i> Available for sale</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-info rounded">
                                <i class="icon-base ti tabler-shopping-cart icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Sold</span>
                    <h3 class="card-title mb-2" id="sold-imei">0</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-chevron-up"></i> Devices sold</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-warning rounded">
                                <i class="icon-base ti tabler-alert-triangle icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Warranty Expiring</span>
                    <h3 class="card-title mb-2" id="expiring-imei">0</h3>
                    <small class="text-warning fw-semibold"><i class="icon-base ti tabler-alert-triangle"></i> Within 30 days</small>
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
    $(function () {
      if ($.fn.DataTable.isDataTable('#imei-table')) {
          $('#imei-table').DataTable().destroy();
      }

      var table = $('#imei-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('imei.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'imei_number', name: 'imei_number'},
              {data: 'product_name', name: 'product_name'},
              {data: 'model_name', name: 'model_name'},
              {data: 'brand_name', name: 'brand_name'},
              {data: 'status', name: 'status'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'warranty_status', name: 'warranty_status'},
              {data: 'purchase_date', name: 'purchase_date', render: function (data, type, row) {
                  if (data) {
                      return new Date(data).toLocaleDateString();
                  }
                  return '-';
              }},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[1, 'asc']], // Sort by IMEI number
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });

      // Load statistics
      loadImeiStatistics();
    });

    function loadImeiStatistics() {
        // This would typically be an AJAX call to get statistics
        // For now, we'll calculate from the table data when it's loaded
        $('#imei-table').on('draw.dt', function () {
            // You can implement AJAX call here to get statistics
            // For demo purposes, showing placeholder values
        });
    }
</script>
@endpush
