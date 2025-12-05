@extends('app')

@section('title', 'Stock Adjustment Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / Stock Adjustments /</span> Adjustment Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Stock Adjustment Information</h5>
                    <div>
                        <span class="badge bg-{{ match($stockAdjustment->adjustment_type) {
                            'ADD' => 'success',
                            'REMOVE' => 'danger',
                            'DAMAGED' => 'warning',
                            'LOST' => 'danger',
                            'FOUND' => 'info',
                            'RETURN' => 'primary',
                            default => 'secondary'
                        } }} fs-6">
                            {{ $stockAdjustment->adjustment_type_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Product Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Product:</td>
                                    <td>
                                        <strong>{{ $stockAdjustment->product->product_name }}</strong>
                                        @if($stockAdjustment->product->model_name)
                                            <br><small class="text-muted">{{ $stockAdjustment->product->model_name }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Brand:</td>
                                    <td>{{ $stockAdjustment->product->brand ? $stockAdjustment->product->brand->brand_name : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Category:</td>
                                    <td>{{ $stockAdjustment->product->category ? $stockAdjustment->product->category->category_name : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Adjustment Details</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Type:</td>
                                    <td>
                                        <span class="badge bg-{{ match($stockAdjustment->adjustment_type) {
                                            'ADD' => 'success',
                                            'REMOVE' => 'danger',
                                            'DAMAGED' => 'warning',
                                            'LOST' => 'danger',
                                            'FOUND' => 'info',
                                            'RETURN' => 'primary',
                                            default => 'secondary'
                                        } }}">
                                            {{ $stockAdjustment->adjustment_type_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Quantity:</td>
                                    <td>
                                        <span class="{{ str_starts_with($stockAdjustment->signed_quantity, '+') ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $stockAdjustment->signed_quantity }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Date:</td>
                                    <td>{{ $stockAdjustment->adjustment_date->format('d M Y, H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Reference:</td>
                                    <td>{{ $stockAdjustment->reference_number ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($stockAdjustment->reason)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">Reason</h6>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $stockAdjustment->reason }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Stock Impact -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-muted">Stock Impact</h6>
                            <div class="alert alert-{{ str_starts_with($stockAdjustment->signed_quantity, '+') ? 'success' : 'warning' }}">
                                <strong>Stock Change:</strong> {{ $stockAdjustment->signed_quantity }} {{ $stockAdjustment->product->unit }}
                                @if($stockAdjustment->product->current_stock !== null)
                                    <br><small>Current stock level: {{ $stockAdjustment->product->current_stock }} {{ $stockAdjustment->product->unit }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('stock-adjustments.edit', $stockAdjustment->id) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit
                        </a>
                        <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to List
                        </a>
                        <form action="{{ route('stock-adjustments.destroy', $stockAdjustment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this stock adjustment? This will reverse the stock change.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti tabler-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Adjustment Metadata -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Adjustment Metadata</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">ID:</td>
                            <td>#{{ $stockAdjustment->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Created:</td>
                            <td>{{ $stockAdjustment->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Updated:</td>
                            <td>{{ $stockAdjustment->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Adjusted By:</td>
                            <td>{{ $stockAdjustment->adjustedBy ? $stockAdjustment->adjustedBy->username : 'System' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Related Information -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Related Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.show', $stockAdjustment->product->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti tabler-package me-1"></i> View Product
                        </a>
                        <a href="{{ route('stock-adjustments.index', ['product' => $stockAdjustment->product_id]) }}" class="btn btn-outline-info btn-sm">
                            <i class="ti tabler-history me-1"></i> Other Adjustments
                        </a>
                    </div>

                    @if($stockAdjustment->reference_number)
                    <hr>
                    <small class="text-muted">
                        <strong>Reference:</strong> {{ $stockAdjustment->reference_number }}
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
