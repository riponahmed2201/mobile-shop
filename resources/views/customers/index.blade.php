@extends('app')

@section('title', 'Customers')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers /</span> All Customers</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customers List</h5>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">Add New Customer</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="customers-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Full Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Total Purchases</th>
                            <th>Outstanding</th>
                            <th>Loyalty Points</th>
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
      if ($.fn.DataTable.isDataTable('#customers-table')) {
          $('#customers-table').DataTable().destroy();
      }
      
      var table = $('#customers-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('customers.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'customer_code', name: 'customer_code'},
              {data: 'full_name', name: 'full_name'},
              {data: 'mobile_primary', name: 'mobile_primary'},
              {data: 'email', name: 'email'},
              {data: 'customer_type', name: 'customer_type'},
              {data: 'total_purchases', name: 'total_purchases'},
              {data: 'outstanding_balance', name: 'outstanding_balance'},
              {data: 'loyalty_points', name: 'loyalty_points'},
              {data: 'is_active', name: 'is_active'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[2, 'asc']], // Sort by full name
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });
    });
</script>
@endpush

