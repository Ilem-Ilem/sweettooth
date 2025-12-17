<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: white;
            margin: 0;
            padding: 10px;
        }
        .receipt {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 4px 0;
            font-size: 12px;
            color: #666;
        }
        .receipt-info {
            font-size: 12px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }
        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
        }
        .items {
            font-size: 11px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #333;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .item-name {
            flex: 1;
        }
        .item-qty {
            width: 40px;
            text-align: center;
        }
        .item-price {
            width: 60px;
            text-align: right;
        }
        .totals {
            font-size: 12px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
        }
        .total-row.grand-total {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: 15px;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h1>SALES RECEIPT</h1>
            <p>{{ $businessName }}</p>
        </div>

        <div class="receipt-info">
            <div>
                <span>Receipt #:</span>
                <span><strong>{{ $receipt->receipt_number }}</strong></span>
            </div>
            <div>
                <span>Date:</span>
                <span>{{ $receipt->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <div>
                <span>Sale ID:</span>
                <span>{{ $receipt->sale_id }}</span>
            </div>
        </div>

        <div class="items">
            @foreach($receipt->sale->saleItems as $item)
            <div class="item">
                <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                <div class="item-qty">{{ number_format($item->quantity, 0) }}</div>
                <div class="item-price">{{ $currencySymbol }}{{ $currencyService->formatAmount($item->total) }}</div>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ $currencySymbol }}{{ $currencyService->formatAmount($receipt->subtotal) }}</span>
            </div>
            @if($receipt->discount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>-{{ $currencySymbol }}{{ $currencyService->formatAmount($receipt->discount) }}</span>
            </div>
            @endif
            @if($receipt->tax > 0)
            <div class="total-row">
                <span>Tax:</span>
                <span>{{ $currencySymbol }}{{ $currencyService->formatAmount($receipt->tax) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>{{ $currencySymbol }}{{ $currencyService->formatAmount($receipt->total) }}</span>
            </div>
        </div>

        @if(!empty($receipt->payments))
        <div class="receipt-info">
            <strong>Payments:</strong>
            @foreach($receipt->payments as $payment)
            <div style="margin-top: 4px;">
                <span>{{ ucfirst($payment['method'] ?? 'Unknown') }}:</span>
                <span style="float: right;">${{ number_format($payment['amount'] ?? 0, 2) }}</span>
            </div>
            @endforeach
        </div>
        @endif

        @if($receipt->change_due > 0)
        <div class="receipt-info">
            <div>
                <span>Change Due:</span>
                <span>${{ number_format($receipt->change_due, 2) }}</span>
            </div>
        </div>
        @endif

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Please come again</p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
