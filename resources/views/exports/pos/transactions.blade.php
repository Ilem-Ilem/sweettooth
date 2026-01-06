@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Receipt #</th>
                <th>Date & Time</th>
                <th>Order Type</th>
                <th>Items Count</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Discount</th>
                <th>Total</th>
                <th>Payment Method</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->sale_number ?? '-' }}</td>
                    <td>{{ $sale->sale_time?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('-', ' ', $sale->order_type ?? '-')) }}</td>
                    <td>{{ $sale->saleItems?->count() ?? 0 }}</td>
                    <td>{{ number_format($sale->subtotal ?? 0, 2) }}</td>
                    <td>{{ number_format($sale->tax ?? 0, 2) }}</td>
                    <td>{{ number_format($sale->discount ?? 0, 2) }}</td>
                    <td>{{ number_format($sale->total ?? 0, 2) }}</td>
                    <td>{{ $sale->payments?->first()?->payment_method ?? '-' }}</td>
                    <td>{{ ucfirst($sale->status ?? '-') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">POS Transactions Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Receipt #</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Time</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Type</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Items</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Subtotal</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Tax</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Total</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $sale)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale->sale_number ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale->sale_time?->format('H:i') ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ substr(ucfirst($sale->order_type ?? '-'), 0, 4) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ $sale->saleItems?->count() ?? 0 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($sale->subtotal ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($sale->tax ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">{{ number_format($sale->total ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $sale->payments?->first()?->payment_method ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->count() > 0)
            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
                <p style="font-weight: bold;">Daily Summary</p>
                <table style="width: 350px;">
                    <tr>
                        <td style="padding: 8px;">Total Transactions:</td>
                        <td style="padding: 8px; text-align: right;">{{ $data->count() }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Sales:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('total'), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Tax:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('tax'), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Average Transaction:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->avg('total'), 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #ddd; font-weight: bold;">
                        <td style="padding: 8px;">Grand Total:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('total'), 2) }}</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
@endif
