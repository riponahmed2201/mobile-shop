@extends('app')

@section('title', 'Loyalty Program')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Loyalty Program</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Loyalty Transactions</h5>
            <a href="{{ route('loyalty.create') }}" class="btn btn-primary">Add Transaction</a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Customer</label>
                    <select class="form-select" id="customer_filter">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->full_name }} - {{ $customer->mobile_primary }} ({{ $customer->loyalty_points }} pts)</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="loyalty-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Mobile</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Reference</th>
                            <th>Description</th>
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
      // Destroy existing DataTable if it exists
      if ($.fn.DataTable.isDataTable('#loyalty-table')) {
          $('#loyalty-table').DataTable().destroy();
      }
      
      var table = $('#loyalty-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('loyalty.index') }}",
              data: function (d) {
                  d.customer_id = $('#customer_filter').val();
              }
          },
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'customer_mobile', name: 'customer_mobile'},
              {data: 'transaction_type', name: 'transaction_type'},
              {data: 'points', name: 'points'},
              {data: 'reference', name: 'reference'},
              {data: 'description', name: 'description'},
              {data: 'created_at', name: 'created_at'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[7, 'desc']],
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });

      $('#customer_filter').on('change', function() {
          table.ajax.reload();
      });
    });
</script>
@endpush

