<!DOCTYPE html>
<html>
<head>
    <title>Payment Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .receipt-info { margin-bottom: 20px; }
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
    <button onclick="window.print()" class="no-print" style="margin-bottom: 20px; padding: 10px 20px; background: #28a745; color: white; border: none; cursor: pointer;">Print Receipt</button>

    <div class="header">
        <h2>PAYMENT RECEIPT</h2>
        <p><strong>{{ config('app.name', 'Mobile Shop') }}</strong></p>
    </div>

    <div class="receipt-info">
        <p><strong>Receipt No:</strong> RC-{{ str_pad($paymentCollection->id, 6, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Date:</strong> {{ $paymentCollection->payment_date->format('d M Y') }}</p>
    </div>

    <table>
        <tr>
            <th>Received From</th>
            <td>{{ $paymentCollection->customer->full_name }}</td>
        </tr>
        <tr>
            <th>Amount</th>
            <td class="total">৳ {{ number_format($paymentCollection->amount, 2) }}</td>
        </tr>
        <tr>
            <th>Payment Method</th>
            <td>{{ $paymentCollection->payment_method }}</td>
        </tr>
        <tr>
            <th>Reference Number</th>
            <td>{{ $paymentCollection->reference_number ?? '-' }}</td>
        </tr>
        @if($paymentCollection->sale)
        <tr>
            <th>Against Invoice</th>
            <td>{{ $paymentCollection->sale->invoice_number }}</td>
        </tr>
        @endif
        <tr>
            <th>Notes</th>
            <td>{{ $paymentCollection->notes ?? '-' }}</td>
        </tr>
    </table>

    <div class="signature">
        <div>
            <p>_____________________</p>
            <p>Received By: {{ $paymentCollection->collectedBy->username ?? '-' }}</p>
        </div>
        <div style="float: right;">
            <p>_____________________</p>
            <p>Customer Signature</p>
        </div>
    </div>
</body>
</html>
