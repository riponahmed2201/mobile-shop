@extends('app')

@section('title', 'EMI Plan Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Sales & Orders /</span> EMI Plan Details</h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">Installment Schedule</h5>
                    <span class="badge bg-{{ $emiPlan->status === 'ACTIVE' ? 'success' : ($emiPlan->status === 'COMPLETED' ? 'primary' : ($emiPlan->status === 'DEFAULTED' ? 'danger' : 'warning')) }}">
                        {{ $emiPlan->status }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Payment Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($emiPlan->installments as $installment)
                                <tr class="{{ $installment->status === 'OVERDUE' ? 'table-danger' : ($installment->status === 'PAID' ? 'table-success' : '') }}">
                                    <td>{{ $installment->installment_number }}</td>
                                    <td>{{ $installment->due_date->format('d M Y') }}</td>
                                    <td>৳{{ number_format($installment->amount, 2) }}</td>
                                    <td>৳{{ number_format($installment->paid_amount, 2) }}</td>
                                    <td>{{ $installment->payment_date ? $installment->payment_date->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $installment->status === 'PENDING' ? 'warning' : ($installment->status === 'PAID' ? 'success' : ($installment->status === 'OVERDUE' ? 'danger' : 'secondary')) }}">
                                            {{ $installment->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($installment->status !== 'PAID')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $installment->id }}">
                                            <i class="ti tabler-cash me-1"></i> Pay
                                        </button>

                                        <!-- Payment Modal -->
                                        <div class="modal fade" id="paymentModal{{ $installment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Record Payment - Installment #{{ $installment->installment_number }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('emi.record-payment', $emiPlan->id) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="installment_id" value="{{ $installment->id }}">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Paid Amount <span class="text-danger">*</span></label>
                                                                <input type="number" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" step="0.01" value="{{ old('paid_amount', $installment->amount) }}" required>
                                                                @error('paid_amount')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                                                                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                                                @error('payment_date')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                                                    <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                                                                    <option value="CARD" {{ old('payment_method') == 'CARD' ? 'selected' : '' }}>Card</option>
                                                                    <option value="BKASH" {{ old('payment_method') == 'BKASH' ? 'selected' : '' }}>bKash</option>
                                                                    <option value="NAGAD" {{ old('payment_method') == 'NAGAD' ? 'selected' : '' }}>Nagad</option>
                                                                    <option value="BANK" {{ old('payment_method') == 'BANK' ? 'selected' : '' }}>Bank Transfer</option>
                                                                </select>
                                                                @error('payment_method')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Notes</label>
                                                                <textarea name="notes" class="form-control" rows="2"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Record Payment</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-success"><i class="ti tabler-check"></i> Paid</span>
                                        <a href="{{ route('emi.receipt', $installment->id) }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                            <i class="ti tabler-printer"></i> Receipt
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @if($installment->notes)
                                <tr>
                                    <td colspan="7" class="text-muted"><small>Notes: {{ $installment->notes }}</small></td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">EMI Plan Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Sale Invoice:</dt>
                        <dd class="col-sm-6">
                            <a href="{{ route('sales.show', $emiPlan->sale_id) }}">
                                {{ $emiPlan->sale->invoice_number }}
                            </a>
                        </dd>

                        <dt class="col-sm-6">Customer:</dt>
                        <dd class="col-sm-6">{{ $emiPlan->customer->full_name }}</dd>

                        <dt class="col-sm-6">Total Amount:</dt>
                        <dd class="col-sm-6">৳{{ number_format($emiPlan->total_amount, 2) }}</dd>

                        <dt class="col-sm-6">Down Payment:</dt>
                        <dd class="col-sm-6">৳{{ number_format($emiPlan->down_payment, 2) }}</dd>

                        <dt class="col-sm-6">Installment:</dt>
                        <dd class="col-sm-6">৳{{ number_format($emiPlan->installment_amount, 2) }}</dd>

                        <dt class="col-sm-6">Total Installments:</dt>
                        <dd class="col-sm-6">{{ $emiPlan->number_of_installments }}</dd>

                        <dt class="col-sm-6">Interest Rate:</dt>
                        <dd class="col-sm-6">{{ $emiPlan->interest_rate }}%</dd>

                        <dt class="col-sm-6">Start Date:</dt>
                        <dd class="col-sm-6">{{ $emiPlan->start_date->format('d M Y') }}</dd>

                        <dt class="col-sm-6">Paid Installments:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-info">{{ $emiPlan->paid_installments }} / {{ $emiPlan->number_of_installments }}</span>
                        </dd>

                        <dt class="col-sm-6">Remaining:</dt>
                        <dd class="col-sm-6">
                            <span class="badge bg-{{ $emiPlan->remaining_amount > 0 ? 'danger' : 'success' }}">
                                ৳{{ number_format($emiPlan->remaining_amount, 2) }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Progress Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Progress</h5>
                </div>
                <div class="card-body">
                    @php
                        $percentage = ($emiPlan->paid_installments / $emiPlan->number_of_installments) * 100;
                    @endphp
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                            {{ round($percentage) }}%
                        </div>
                    </div>
                    <p class="text-center mb-0">{{ $emiPlan->paid_installments }} of {{ $emiPlan->number_of_installments }} installments paid</p>
                </div>
            </div>

            <a href="{{ route('emi.agreement', $emiPlan->id) }}" class="btn btn-primary w-100 mb-2" target="_blank">
                <i class="ti tabler-file-text me-1"></i> Print EMI Agreement
            </a>
            <a href="{{ route('emi.index') }}" class="btn btn-secondary w-100">Back to List</a>
        </div>
    </div>
</div>
@endsection
