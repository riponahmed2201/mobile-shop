@extends('app')

@section('title', 'Supplier Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases / Suppliers /</span> Supplier Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Supplier Information</h5>
                    <div>
                        <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }} fs-6">
                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Basic Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Supplier Name:</td>
                                    <td><strong>{{ $supplier->supplier_name }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Contact Person:</td>
                                    <td>{{ $supplier->contact_person ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}">
                                            {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Created:</td>
                                    <td>{{ $supplier->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Contact Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Mobile:</td>
                                    <td><strong>{{ $supplier->mobile }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Email:</td>
                                    <td>{{ $supplier->email ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Address:</td>
                                    <td>{{ $supplier->address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">City/Country:</td>
                                    <td>{{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-muted">Financial Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted">Credit Limit:</td>
                                            <td><strong>৳{{ number_format($supplier->credit_limit, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Outstanding Balance:</td>
                                            <td><strong>৳{{ number_format($supplier->outstanding_balance, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Available Credit:</td>
                                            <td>
                                                <span class="badge bg-{{ $supplier->available_credit > 0 ? 'success' : 'danger' }}">
                                                    ৳{{ number_format($supplier->available_credit, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td class="text-muted">Payment Terms:</td>
                                            <td>{{ $supplier->payment_terms ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Total Purchases:</td>
                                            <td><strong>৳{{ number_format($supplier->total_purchases, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Pending Orders:</td>
                                            <td><strong>{{ $supplier->pending_orders_count }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Purchase Orders -->
            @if($supplier->purchaseOrders->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Recent Purchase Orders</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplier->purchaseOrders->take(5) as $po)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="text-decoration-none">
                                            {{ $po->po_number }}
                                        </a>
                                    </td>
                                    <td>{{ $po->po_date->format('d M Y') }}</td>
                                    <td>{{ $po->total_quantity }}</td>
                                    <td>৳{{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $po->status_badge_class }}">
                                            {{ $po->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $po->payment_status_badge_class }}">
                                            {{ $po->payment_status_label }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($supplier->purchaseOrders->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('purchase-orders.index', ['supplier' => $supplier->id]) }}" class="btn btn-sm btn-outline-primary">
                            View All Orders ({{ $supplier->purchaseOrders->count() }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit Supplier
                        </a>
                        <a href="{{ route('purchase-orders.create', ['supplier' => $supplier->id]) }}" class="btn btn-success">
                            <i class="ti tabler-plus me-1"></i> Create PO
                        </a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to List
                        </a>

                        @if($supplier->purchaseOrders->count() == 0)
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="ms-auto" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti tabler-trash me-1"></i> Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="mb-1">{{ $supplier->purchaseOrders->count() }}</h4>
                            <small class="text-muted">Total Orders</small>
                        </div>
                        <div class="col-6">
                            <h4 class="mb-1">৳{{ number_format($supplier->total_purchases, 2) }}</h4>
                            <small class="text-muted">Total Value</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-primary rounded-circle">
                                <i class="ti tabler-phone"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Mobile</h6>
                            <small class="text-muted">{{ $supplier->mobile }}</small>
                        </div>
                    </div>

                    @if($supplier->email)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-info rounded-circle">
                                <i class="ti tabler-mail"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Email</h6>
                            <small class="text-muted">{{ $supplier->email }}</small>
                        </div>
                    </div>
                    @endif

                    @if($supplier->address)
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-success rounded-circle">
                                <i class="ti tabler-map-pin"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Address</h6>
                            <small class="text-muted">{{ $supplier->address }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
