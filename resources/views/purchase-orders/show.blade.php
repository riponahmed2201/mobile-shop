@extends('app')

@section('title', 'Purchase Order Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Purchases / Purchase Orders /</span> Order Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Purchase Order #{{ $purchaseOrder->po_number }}</h5>
                    <div>
                        <span class="badge bg-{{ $purchaseOrder->status_badge_class }} fs-6 me-2">
                            {{ $purchaseOrder->status_label }}
                        </span>
                        <span class="badge bg-{{ $purchaseOrder->payment_status_badge_class }} fs-6">
                            {{ $purchaseOrder->payment_status_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">PO Number:</td>
                                    <td><strong>{{ $purchaseOrder->po_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Supplier:</td>
                                    <td>
                                        <strong>{{ $purchaseOrder->supplier->supplier_name }}</strong>
                                        <br><small class="text-muted">{{ $purchaseOrder->supplier->mobile }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">PO Date:</td>
                                    <td>{{ $purchaseOrder->po_date->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Expected Delivery:</td>
                                    <td>{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Created By:</td>
                                    <td>{{ $purchaseOrder->createdBy ? $purchaseOrder->createdBy->username : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Order Summary</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Total Items:</td>
                                    <td><strong>{{ $purchaseOrder->total_quantity }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Order Total:</td>
                                    <td><strong>৳{{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Paid Amount:</td>
                                    <td><strong>৳{{ number_format($purchaseOrder->paid_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Due Amount:</td>
                                    <td><strong class="{{ $purchaseOrder->due_amount > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($purchaseOrder->due_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ $purchaseOrder->status_badge_class }}">
                                            {{ $purchaseOrder->status_label }}
                                        </span>
                                        @if($purchaseOrder->order_status === 'RECEIVED')
                                            <br><small class="text-success mt-1">Fully Received</small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($purchaseOrder->notes)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">Notes</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $purchaseOrder->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Order Items</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    @if($purchaseOrder->order_status === 'RECEIVED')
                                    <th>Received</th>
                                    <th>Status</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->product->product_name }}
                                        @if($item->product->model_name)
                                            <br><small class="text-muted">{{ $item->product->model_name }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($item->total_price, 2) }}</td>
                                    @if($purchaseOrder->order_status === 'RECEIVED')
                                    <td>{{ $item->received_quantity }}</td>
                                    <td>
                                        @if($item->is_fully_received)
                                            <span class="badge bg-success">Received</span>
                                        @else
                                            <span class="badge bg-warning">Partial ({{ $item->received_percentage }}%)</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="{{ $purchaseOrder->order_status === 'RECEIVED' ? 3 : 3 }}" class="text-end"><strong>Totals:</strong></td>
                                    <td><strong>{{ $purchaseOrder->total_quantity }}</strong></td>
                                    <td><strong>৳{{ number_format($purchaseOrder->total_amount, 2) }}</strong></td>
                                    @if($purchaseOrder->order_status === 'RECEIVED')
                                    <td><strong>{{ $purchaseOrder->total_received_quantity }}</strong></td>
                                    <td>-</td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        @if($purchaseOrder->can_edit)
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit Order
                        </a>
                        <form action="{{ route('purchase-orders.destroy', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti tabler-trash me-1"></i> Delete Order
                            </button>
                        </form>
                        @endif

                        @if($purchaseOrder->order_status === 'DRAFT')
                        <button type="button" class="btn btn-info" onclick="updateStatus({{ $purchaseOrder->id }}, 'CONFIRMED')">
                            <i class="ti tabler-send me-1"></i> Confirm Order
                        </button>
                        @endif

                        @if($purchaseOrder->can_receive)
                        <button type="button" class="btn btn-success" onclick="updateStatus({{ $purchaseOrder->id }}, 'RECEIVED')">
                            <i class="ti tabler-package me-1"></i> Mark as Received
                        </button>
                        @endif

                        @if(in_array($purchaseOrder->order_status, ['DRAFT', 'CONFIRMED']))
                        <button type="button" class="btn btn-outline-danger" onclick="updateStatus({{ $purchaseOrder->id }}, 'CANCELLED')">
                            <i class="ti tabler-x me-1"></i> Cancel Order
                        </button>
                        @endif

                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Order Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Order Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Order Created</h6>
                                <small class="text-muted">{{ $purchaseOrder->created_at->format('d M Y, H:i') }}</small>
                                @if($purchaseOrder->createdBy)
                                    <p class="mb-0">By: {{ $purchaseOrder->createdBy->username }}</p>
                                @endif
                            </div>
                        </div>

                        @if(in_array($purchaseOrder->order_status, ['CONFIRMED', 'RECEIVED', 'CANCELLED']))
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">
                                    @if($purchaseOrder->order_status === 'CONFIRMED')
                                        Order Confirmed
                                    @elseif($purchaseOrder->order_status === 'RECEIVED')
                                        Order Received
                                    @elseif($purchaseOrder->order_status === 'CANCELLED')
                                        Order Cancelled
                                    @endif
                                </h6>
                                <small class="text-muted">{{ $purchaseOrder->updated_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Supplier Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Supplier Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3">
                            <div class="avatar-initial bg-primary rounded-circle">
                                {{ strtoupper(substr($purchaseOrder->supplier->supplier_name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $purchaseOrder->supplier->supplier_name }}</h6>
                            <small class="text-muted">{{ $purchaseOrder->supplier->mobile }}</small>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="mb-1">{{ $purchaseOrder->supplier->purchaseOrders->count() }}</h6>
                            <small class="text-muted">Total Orders</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">৳{{ number_format($purchaseOrder->supplier->total_purchases, 2) }}</h6>
                            <small class="text-muted">Total Value</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('suppliers.show', $purchaseOrder->supplier->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti tabler-eye me-1"></i> View Supplier
                        </a>
                        <a href="{{ route('purchase-orders.create', ['supplier' => $purchaseOrder->supplier->id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="ti tabler-plus me-1"></i> New Order
                        </a>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="printOrder()">
                            <i class="ti tabler-printer me-1"></i> Print Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_css')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content h6 {
    margin-bottom: 2px;
    font-size: 0.9rem;
}

.timeline-content small {
    font-size: 0.8rem;
}
</style>
@endpush

@push('page_js')
<script>
function updateStatus(orderId, status) {
    let confirmMessage = '';
    switch(status) {
        case 'CONFIRMED':
            confirmMessage = 'Are you sure you want to confirm this purchase order? This will send it to the supplier.';
            break;
        case 'RECEIVED':
            confirmMessage = 'Are you sure you want to mark this order as received? This will update inventory levels.';
            break;
        case 'CANCELLED':
            confirmMessage = 'Are you sure you want to cancel this purchase order? This action cannot be undone.';
            break;
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    $.ajax({
        url: `/purchase-orders/${orderId}/status`,
        method: 'PATCH',
        data: {
            status: status,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Reload the page to show updated status
                location.reload();
            } else {
                alert('Failed to update order status: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Failed to update order status. Please try again.');
        }
    });
}

function printOrder() {
    window.print();
}
</script>
@endpush
