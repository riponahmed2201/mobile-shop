@extends('app')

@section('title', 'Quotations')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> Quotations</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Quotations List</h5>
            <a href="{{ route('quotations.create') }}" class="btn btn-primary">New Quotation</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="quotations-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Quotation #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Amount</th>
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
      if ($.fn.DataTable.isDataTable('#quotations-table')) {
        $('#quotations-table').DataTable().destroy();
      }
      var table = $('#quotations-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('quotations.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'quotation_number', name: 'quotation_number'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'quotation_date', name: 'quotation_date'},
              {data: 'total_amount', name: 'total_amount'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[3, 'desc']],
          pageLength: 25
      });
    });
</script>
@endpush
