@extends('app')

@section('title', 'IMEI Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Inventory / IMEI Tracking /</span> IMEI Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">IMEI Information</h5>
                    <div>
                        <span class="badge bg-{{ match($imei->status) {
                            'IN_STOCK' => 'success',
                            'SOLD' => 'primary',
                            'DEFECTIVE' => 'danger',
                            'RETURNED' => 'warning',
                            default => 'secondary'
                        } }}">
                            {{ str_replace('_', ' ', $imei->status) }}
                        </span>
                        @if($imei->warranty_expiry_date)
                            @php
                                $daysLeft = now()->diffInDays($imei->warranty_expiry_date, false);
                            @endphp
                            @if($daysLeft < 0)
                                <span class="badge bg-danger">Warranty Expired</span>
                            @elseif($daysLeft <= 30)
                                <span class="badge bg-warning">Warranty Expires Soon ({{ $daysLeft }} days)</span>
                            @else
                                <span class="badge bg-success">Under Warranty</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Basic Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">IMEI Number:</td>
                                    <td><strong>{{ $imei->imei_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Serial Number:</td>
                                    <td>{{ $imei->serial_number ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Product:</td>
                                    <td>
                                        <strong>{{ $imei->product->product_name }}</strong>
                                        @if($imei->product->model_name)
                                            <br><small class="text-muted">{{ $imei->product->model_name }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Brand:</td>
                                    <td>{{ $imei->product->brand ? $imei->product->brand->brand_name : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status:</td>
                                    <td>
                                        <span class="badge bg-{{ match($imei->status) {
                                            'IN_STOCK' => 'success',
                                            'SOLD' => 'primary',
                                            'DEFECTIVE' => 'danger',
                                            'RETURNED' => 'warning',
                                            default => 'secondary'
                                        } }}">
                                            {{ str_replace('_', ' ', $imei->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Purchase & Warranty</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Purchase Date:</td>
                                    <td>{{ $imei->purchase_date ? $imei->purchase_date->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Sale Date:</td>
                                    <td>{{ $imei->sale_date ? $imei->sale_date->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Warranty Expiry:</td>
                                    <td>
                                        @if($imei->warranty_expiry_date)
                                            {{ $imei->warranty_expiry_date->format('d M Y') }}
                                            @php
                                                $daysLeft = now()->diffInDays($imei->warranty_expiry_date, false);
                                            @endphp
                                            @if($daysLeft < 0)
                                                <span class="text-danger">(Expired)</span>
                                            @elseif($daysLeft <= 30)
                                                <span class="text-warning">({{ $daysLeft }} days left)</span>
                                            @else
                                                <span class="text-success">(Active)</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @if($imei->customer)
                                <tr>
                                    <td class="text-muted">Sold To:</td>
                                    <td>
                                        <strong>{{ $imei->customer->full_name }}</strong>
                                        <br><small class="text-muted">{{ $imei->customer->mobile_primary }}</small>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($imei->notes)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted">Notes</h6>
                            <p class="mb-0">{{ $imei->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <a href="{{ route('imei.edit', $imei->id) }}" class="btn btn-primary">
                            <i class="ti tabler-pencil me-1"></i> Edit
                        </a>
                        <a href="{{ route('imei.index') }}" class="btn btn-secondary">
                            <i class="ti tabler-arrow-left me-1"></i> Back to List
                        </a>
                        @if($imei->status !== 'SOLD')
                        <form action="{{ route('imei.destroy', $imei->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this IMEI record?');">
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
            <!-- Product Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Product Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Name:</td>
                            <td>{{ $imei->product->product_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Model:</td>
                            <td>{{ $imei->product->model_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Brand:</td>
                            <td>{{ $imei->product->brand ? $imei->product->brand->brand_name : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Category:</td>
                            <td>{{ $imei->product->category ? $imei->product->category->category_name : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Selling Price:</td>
                            <td>৳{{ number_format($imei->product->selling_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Stock:</td>
                            <td>
                                <span class="badge bg-{{ $imei->product->current_stock <= $imei->product->min_stock_level ? 'danger' : 'success' }}">
                                    {{ $imei->product->current_stock }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Timeline/Activity -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Activity Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">IMEI Created</h6>
                                <small class="text-muted">{{ $imei->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>

                        @if($imei->sale_date)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Sold</h6>
                                <small class="text-muted">{{ $imei->sale_date->format('d M Y, H:i') }}</small>
                                @if($imei->customer)
                                    <p class="mb-0">To: {{ $imei->customer->full_name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($imei->warranty_expiry_date && now() > $imei->warranty_expiry_date)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Warranty Expired</h6>
                                <small class="text-muted">{{ $imei->warranty_expiry_date->format('d M Y') }}</small>
                            </div>
                        </div>
                        @elseif($imei->warranty_expiry_date)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-0">Warranty Expires</h6>
                                <small class="text-muted">{{ $imei->warranty_expiry_date->format('d M Y') }}</small>
                            </div>
                        </div>
                        @endif
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
