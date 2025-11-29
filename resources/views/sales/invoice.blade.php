<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
            font-size: 14px;
            line-height: 24px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .text-right {
            text-align: right !important;
        }
        
        .text-center {
            text-align: center !important;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        @media print {
            .no-print {
                display: none;
            }
            .invoice-box {
                box-shadow: none;
                border: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Print Invoice</button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-left: 10px;">Close</button>
    </div>

    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <!-- <img src="" style="width:100%; max-width:300px;"> -->
                                Mobile Shop
                            </td>

                            <td>
                                Invoice #: {{ $sale->invoice_number }}<br>
                                Created: {{ $sale->sale_date->format('F d, Y') }}<br>
                                Status: {{ $sale->payment_status }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                Mobile Shop Inc.<br>
                                1234 Main Street<br>
                                Dhaka, Bangladesh
                            </td>

                            <td>
                                {{ $sale->customer ? $sale->customer->full_name : 'Walk-in Customer' }}<br>
                                {{ $sale->customer ? $sale->customer->mobile_primary : '' }}<br>
                                {{ $sale->customer ? $sale->customer->email : '' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td class="text-center">Unit Cost</td>
                <td class="text-center">Quantity</td>
                <td class="text-right">Price</td>
            </tr>

            @foreach($sale->items as $item)
            <tr class="item">
                <td>{{ $item->product->product_name }}</td>
                <td class="text-center">৳{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">৳{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @endforeach

            <tr class="total">
                <td colspan="3" class="text-right">Subtotal:</td>
                <td class="text-right">৳{{ number_format($sale->subtotal, 2) }}</td>
            </tr>
            
            @if($sale->discount_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Discount:</td>
                <td class="text-right">-৳{{ number_format($sale->discount_amount, 2) }}</td>
            </tr>
            @endif

            @if($sale->tax_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Tax:</td>
                <td class="text-right">+৳{{ number_format($sale->tax_amount, 2) }}</td>
            </tr>
            @endif

            <tr>
                <td colspan="3" class="text-right" style="font-weight: bold;">Total:</td>
                <td class="text-right" style="font-weight: bold;">৳{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            
            <tr>
                <td colspan="3" class="text-right">Paid:</td>
                <td class="text-right">৳{{ number_format($sale->paid_amount, 2) }}</td>
            </tr>
            
            <tr>
                <td colspan="3" class="text-right">Due:</td>
                <td class="text-right">৳{{ number_format($sale->due_amount, 2) }}</td>
            </tr>
        </table>
        
        <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #777;">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
        </div>
    </div>
    
    <script>
        // Auto print when opened
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
