@extends('app')

@section('title', 'Customer Groups')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Customer Groups</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customer Groups List</h5>
            <a href="{{ route('customer-groups.create') }}" class="btn btn-primary">Add New Group</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="groups-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Group Name</th>
                            <th>Description</th>
                            <th>Members</th>
                            <th>Discount</th>
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
      if ($.fn.DataTable.isDataTable('#groups-table')) {
          $('#groups-table').DataTable().destroy();
      }
      
      var table = $('#groups-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('customer-groups.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'color_badge', name: 'group_name'},
              {data: 'description', name: 'description'},
              {data: 'members_count', name: 'customers_count'},
              {data: 'discount_percentage', name: 'discount_percentage'},
              {data: 'is_active', name: 'is_active'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[1, 'asc']],
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });
    });
</script>
@endpush

