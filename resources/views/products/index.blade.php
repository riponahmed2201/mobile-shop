@extends('app')

@section('title', 'Products')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory /</span> Products</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Products List</h5>
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
        </div>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="products-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Code</th>
                            <th>Product Name</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>MRP</th>
                            <th>Stock</th>
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
      if ($.fn.DataTable.isDataTable('#products-table')) {
          $('#products-table').DataTable().destroy();
      }
      
      var table = $('#products-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: "{{ route('products.index') }}",
          columns: [
              {data: 'id', name: 'id', render: function (data, type, row, meta) {
                  return meta.row + meta.settings._iDisplayStart + 1;
              }},
              {data: 'product_code', name: 'product_code'},
              {data: 'product_name', name: 'product_name'},
              {data: 'brand_name', name: 'brand_name'},
              {data: 'category_name', name: 'category_name'},
              {data: 'product_type', name: 'product_type'},
              {data: 'purchase_price', name: 'purchase_price'},
              {data: 'selling_price', name: 'selling_price'},
              {data: 'mrp', name: 'mrp'},
              {data: 'current_stock', name: 'current_stock'},
              {data: 'is_active', name: 'is_active'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
          ],
          order: [[2, 'asc']], // Sort by product name
          pageLength: 25,
          lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
      });
    });
</script>
@endpush
