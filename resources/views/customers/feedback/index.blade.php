@extends('app')

@section('title', 'Customer Feedback')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Customers /</span> Customer Feedback</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Customer Feedback List</h5>
            <a href="{{ route('feedback.create') }}" class="btn btn-primary">Add Feedback</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="feedback-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Customer</th>
                            <th>Mobile</th>
                            <th>Type</th>
                            <th>Rating</th>
                            <th>Feedback</th>
                            <th>Status</th>
                            <th>Response</th>
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
      if ($.fn.DataTable.isDataTable('#feedback-table')) {
          $('#feedback-table').DataTable().destroy();
      }
      
      var table = $('#feedback-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('feedback.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'customer_mobile', name: 'customer_mobile'},
              {data: 'feedback_type', name: 'feedback_type'},
              {data: 'rating_stars', name: 'rating'},
              {data: 'feedback_text', name: 'feedback_text'},
              {data: 'is_public', name: 'is_public'},
              {data: 'has_response', name: 'has_response'},
              {data: 'created_at', name: 'created_at'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[8, 'desc']],
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });
    });
</script>
@endpush

