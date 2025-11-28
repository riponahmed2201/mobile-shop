@extends('app')

@section('title', 'Returns & Refunds')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Returns & Refunds</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Returns List</h5>
            <a href="{{ route('returns.create') }}" class="btn btn-primary">New Return</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="returns-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Return #</th>
                            <th>Sale Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Status</th>
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
      if ($.fn.DataTable.isDataTable('#returns-table')) {
        $('#returns-table').DataTable().destroy();
      }
      var table = $('#returns-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('returns.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'return_number', name: 'return_number'},
              {data: 'sale_invoice', name: 'sale_invoice'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'return_date', name: 'return_date'},
              {data: 'total_amount', name: 'total_amount'},
              {data: 'return_type', name: 'return_type'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[4, 'desc']],
          pageLength: 25
      });
    });
</script>
@endpush
