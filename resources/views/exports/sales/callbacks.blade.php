@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Callback ID</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>UOM</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Callback Date & Time</th>
                <th>Recorded By</th>
                <th>Approved By</th>
                <th>Received By</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $callback)
                <tr>
                    <td>{{ $callback->id ?? '-' }}</td>
                    <td>{{ $callback->product?->name ?? '-' }}</td>
                    <td>{{ $callback->product?->sku ?? '-' }}</td>
                    <td>{{ $callback->quantity ?? 0 }}</td>
                    <td>{{ $callback->uom ?? 'kg' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $callback->reason ?? '-')) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $callback->status ?? 'pending')) }}</td>
                    <td>{{ $callback->callback_time ? \Carbon\Carbon::parse($callback->callback_time)->format('Y-m-d H:i:s') : '-' }}</td>
                    <td>{{ $callback->recordedBy?->name ?? '-' }}</td>
                    <td>{{ $callback->approvedBy?->name ?? '-' }}</td>
                    <td>{{ $callback->receivedBy?->name ?? '-' }}</td>
                    <td>{{ $callback->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Product Callbacks Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Callback ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Product</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">SKU</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Qty</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Reason</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $callback)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $callback->id ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $callback->product?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $callback->product?->sku ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ $callback->quantity ?? 0 }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst(str_replace('_', ' ', $callback->reason ?? '-')) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <span style="padding: 4px 8px; border-radius: 4px; {{ match($callback->status ?? 'pending') {
                                'pending' => 'background-color: #fff3cd; color: #856404;',
                                'approved_by_production' => 'background-color: #cfe2ff; color: #084298;',
                                'received_by_production' => 'background-color: #d1ecf1; color: #0c5460;',
                                'completed' => 'background-color: #d4edda; color: #155724;',
                                default => 'background-color: #e2e3e5; color: #383d41;'
                            } }}">
                                {{ ucfirst(str_replace('_', ' ', $callback->status ?? 'pending')) }}
                            </span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $callback->recordedBy?->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $statuses = $data->groupBy('status')->map->count();
            $totalQty = $data->sum(fn ($c) => $c->quantity ?? 0);
        @endphp

        <div style="margin-top: 30px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #dc3545;">
            <h3 style="margin-top: 0; color: #333;">Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Callbacks:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $data->count() }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Quantity:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ number_format($totalQty, 2) }}</td>
                </tr>
                @foreach($statuses as $status => $count)
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>{{ ucfirst(str_replace('_', ' ', $status ?? 'pending')) }}:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $count }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endif
