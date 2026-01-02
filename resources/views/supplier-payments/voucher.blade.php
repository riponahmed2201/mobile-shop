<!DOCTYPE html>
<html>
<head>
    <title>Payment Voucher</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .voucher-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f4f4f4; }
        .total { font-weight: bold; font-size: 18px; }
        .signature { margin-top: 60px; }
        .signature div { display: inline-block; width: 45%; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print" style="margin-bottom: 20px; padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer;">Print Voucher</button>

    <div class="header">
        <h2>PAYMENT VOUCHER</h2>
        <p><strong>{{ config('app.name', 'Mobile Shop') }}</strong></p>
    </div>

    <div class="voucher-info">
        <p><strong>Voucher No:</strong> PV-{{ str_pad($supplierPayment->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Date:</strong> {{ $supplierPayment->payment_date->format('d M Y') }}</p>
    </div>

    <table>
        <tr>
            <th>Paid To</th>
            <td>{{ $supplierPayment->supplier->supplier_name }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td class="total">৳ {{ number_format($supplierPayment->amount, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ $supplierPayment->payment_method }}</td>
        </tr>
        <tr>
            <th>Reference Number</th>
            <td>{{ $supplierPayment->reference_number ?? '-' }}</td>
        </tr>
        @if($supplierPayment->purchaseOrder)
        <tr>
            <th>Purchase Order</th>
            <td>{{ $supplierPayment->purchaseOrder->po_number }}</td>
        </tr>
        @endif
        <tr>
            <th>Notes</th>
            <td>{{ $supplierPayment->notes ?? '-' }}</td>
        </tr>
    </table>

    <div class="signature">
        <div>
            <p>_____________________</p>
            <p>Paid By: {{ $supplierPayment->paidBy->username ?? '-' }}</p>
        </div>
        <div style="float: right;">
            <p>_____________________</p>
            <p>Received By</p>
        </div>
    </div>
</body>
</html>
