@extends('app')

@section('title', 'Suppliers')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases /</span> Suppliers</h4>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-primary rounded">
                                <i class="icon-base ti tabler-users icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Suppliers</span>
                    <h3 class="card-title mb-2" id="total-suppliers">{{ $statistics['total_suppliers'] }}</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-trending-up"></i> All registered</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-success rounded">
                                <i class="icon-base ti tabler-check-circle icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Active</span>
                    <h3 class="card-title mb-2" id="active-suppliers">{{ $statistics['active_suppliers'] }}</h3>
                    <small class="text-success fw-semibold"><i class="icon-base ti tabler-check-circle"></i> Currently active</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-danger rounded">
                                <i class="icon-base ti tabler-x icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Inactive</span>
                    <h3 class="card-title mb-2" id="inactive-suppliers">{{ $statistics['inactive_suppliers'] }}</h3>
                    <small class="text-danger fw-semibold"><i class="icon-base ti tabler-x"></i> Currently inactive</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-warning rounded">
                                <i class="icon-base ti tabler-alert-triangle icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">With Balance</span>
                    <h3 class="card-title mb-2" id="suppliers-with-balance">{{ $statistics['suppliers_with_balance'] }}</h3>
                    <small class="text-warning fw-semibold"><i class="icon-base ti tabler-alert-triangle"></i> Outstanding balance</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-info rounded">
                                <i class="icon-base ti tabler-currency-dollar icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Outstanding</span>
                    <h3 class="card-title mb-2" id="total-outstanding">৳{{ number_format($statistics['total_outstanding'], 2) }}</h3>
                    <small class="text-info fw-semibold"><i class="icon-base ti tabler-currency-dollar"></i> Amount due</small>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-secondary rounded">
                                <i class="icon-base ti tabler-clock icon-sm"></i>
                            </div>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Pending Orders</span>
                    <h3 class="card-title mb-2" id="pending-orders">{{ $statistics['top_suppliers']->sum('pending_orders') }}</h3>
                    <small class="text-secondary fw-semibold"><i class="icon-base ti tabler-clock"></i> Awaiting action</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Suppliers -->
    @if($statistics['top_suppliers']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Top Suppliers by Purchase Value</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($statistics['top_suppliers'] as $supplier)
                        <div class="col-md-4 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <div class="avatar-initial bg-primary rounded-circle">
                                        {{ strtoupper(substr($supplier['name'], 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $supplier['name'] }}</h6>
                                    <small class="text-muted">
                                        ৳{{ number_format($supplier['total_purchases'], 2) }}
                                        @if($supplier['pending_orders'] > 0)
                                            | {{ $supplier['pending_orders'] }} pending orders
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Suppliers List</h5>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Add Supplier</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="suppliers-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Supplier Name</th>
                            <th>Contact Info</th>
                            <th>Purchase Info</th>
                            <th>Credit Info</th>
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
      if ($.fn.DataTable.isDataTable('#suppliers-table')) {
          $('#suppliers-table').DataTable().destroy();
      }

      var table = $('#suppliers-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('suppliers.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'supplier_name', name: 'supplier_name'},
              {data: 'contact_info', name: 'mobile'},
              {data: 'purchase_info', name: 'supplier_name'},
              {data: 'credit_info', name: 'credit_limit'},
              {data: 'is_active', name: 'is_active'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[1, 'asc']], // Sort by supplier name
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });
    });

    function toggleStatus(supplierId, status) {
        if (!confirm(`Are you sure you want to ${status ? 'activate' : 'deactivate'} this supplier?`)) {
            return;
        }

        $.ajax({
            url: `/suppliers/${supplierId}/toggle-status`,
            method: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Reload the page to show updated status
                location.reload();
            },
            error: function(xhr) {
                alert('Failed to update supplier status. Please try again.');
            }
        });
    }
</script>
@endpush
