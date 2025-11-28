@extends('app')

@section('title', 'EMI Plans')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> EMI/Installments</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">EMI Plans List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="emi-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Sale Invoice</th>
                            <th>Customer</th>
                            <th>Total Amount</th>
                            <th>Down Payment</th>
                            <th>Installment</th>
                            <th>Progress</th>
                            <th>Remaining</th>
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
      if ($.fn.DataTable.isDataTable('#emi-table')) {
        $('#emi-table').DataTable().destroy();
      }
      var table = $('#emi-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('emi.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'sale_invoice', name: 'sale_invoice'},
              {data: 'customer_name', name: 'customer_name'},
              {data: 'total_amount', name: 'total_amount'},
              {data: 'down_payment', name: 'down_payment'},
              {data: 'installment_amount', name: 'installment_amount'},
              {data: 'progress', name: 'progress', orderable: false},
              {data: 'remaining_amount', name: 'remaining_amount'},
              {data: 'status', name: 'status'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[0, 'desc']],
          pageLength: 25
      });
    });
</script>
@endpush
