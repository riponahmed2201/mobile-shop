@extends('app')

@section('title', 'Brands')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Brands</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Brands List</h5>
            <a href="{{ route('brands.create') }}" class="btn btn-primary">Add Brand</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="brands-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Logo</th>
                            <th>Brand Name</th>
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
      if ($.fn.DataTable.isDataTable('#brands-table')) {
          $('#brands-table').DataTable().destroy();
      }
      
      var table = $('#brands-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('brands.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'logo', name: 'logo', orderable: false, searchable: false},
              {data: 'brand_name', name: 'brand_name'},
              {data: 'is_active', name: 'is_active', render: function(data) {
                  return data ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-danger">Inactive</span>';
              }},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ]
      });
    });
</script>
@endpush

