@extends('app')

@section('title', 'Sales')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> All Sales</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sales List</h5>
            <a href="{{ route('sales.create') }}" class="btn btn-primary">New Sale</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="sales-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Sale Date</th>
                            <th>Total Amount</th>
                            <th>Payment Status</th>
                            <th>Sale Status</th>
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
      if ($.fn.DataTable.isDataTable('#sales-table')) {
          $('#sales-table').DataTable().destroy();
      }
      
      var table = $('#sales-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('sales.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'invoice_number', name: 'invoice_number'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'sale_date', name: 'sale_date'},
              {data: 'total_amount', name: 'total_amount'},
              {data: 'payment_status', name: 'payment_status'},
              {data: 'sale_status', name: 'sale_status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[3, 'desc']], // Sort by sale date
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });
    });
</script>
@endpush
