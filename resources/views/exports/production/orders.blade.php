@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Number</th>
                <th>Product/Item</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Department</th>
                <th>Start Date</th>
                <th>Due Date</th>
                <th>Progress %</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_number ?? '-' }}</td>
                    <td>{{ $order->name ?? $order->product?->name ?? '-' }}</td>
                    <td>{{ number_format($order->quantity ?? 0, 2) }}</td>
                    <td>{{ $order->unit ?? '-' }}</td>
                    <td>{{ $order->department?->name ?? '-' }}</td>
                    <td>{{ $order->start_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $order->due_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $order->progress ?? 0 }}</td>
                    <td>{{ ucfirst($order->status ?? '-') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Production Orders Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Order #</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Product</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Qty</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Department</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Start Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Due Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Progress</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $order->order_number ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $order->name ?? $order->product?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($order->quantity ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $order->department?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $order->start_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $order->due_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $order->progress ?? 0 }}%</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                            <span style="padding: 4px 8px; border-radius: 4px; {{ 
                                $order->status === 'completed' ? 'background-color: #d4edda; color: #155724;' : 
                                ($order->status === 'in_progress' ? 'background-color: #cfe2ff; color: #084298;' : 
                                'background-color: #fff3cd; color: #856404;') 
                            }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status ?? '-')) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
