@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Movement ID</th>
                <th>Date & Time</th>
                <th>Movement Type</th>
                <th>Stock Item</th>
                <th>Quantity</th>
                <th>Department</th>
                <th>Shift</th>
                <th>Moved By</th>
                <th>Notes</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $movement)
                <tr>
                    <td>{{ $movement->id ?? '-' }}</td>
                    <td>{{ $movement->movement_date ? \Carbon\Carbon::parse($movement->movement_date)->format('Y-m-d H:i:s') : '-' }}</td>
                    <td>{{ ucfirst($movement->type ?? '-') }}</td>
                    <td>{{ $movement->stock?->item?->name ?? '-' }}</td>
                    <td>{{ abs($movement->quantity ?? 0) }}</td>
                    <td>{{ $movement->department_name ?? '-' }}</td>
                    <td>{{ ucfirst($movement->shift ?? '-') }}</td>
                    <td>{{ $movement->mover?->name ?? 'System' }}</td>
                    <td>{{ $movement->notes ?? '-' }}</td>
                    <td>{{ $movement->reference ? 'Complete' : 'Pending' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Stock Movements Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Movement ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date & Time</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Type</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Stock Item</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Quantity</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Moved By</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Dept</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $movement)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $movement->id ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $movement->movement_date ? \Carbon\Carbon::parse($movement->movement_date)->format('Y-m-d H:i') : '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <span style="padding: 4px 8px; border-radius: 4px; {{ $movement->isInbound() ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }}">
                                {{ ucfirst($movement->type ?? '-') }}
                            </span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $movement->stock?->item?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ abs($movement->quantity ?? 0) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $movement->mover?->name ?? 'System' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $movement->department_name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $inbound = $data->filter(fn ($m) => $m->isInbound())->sum(fn ($m) => $m->quantity ?? 0);
            $outbound = abs($data->filter(fn ($m) => !$m->isInbound())->sum(fn ($m) => $m->quantity ?? 0));
            $totalMovements = $data->count();
        @endphp

        <div style="margin-top: 30px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #28a745;">
            <h3 style="margin-top: 0; color: #333;">Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Movements:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $totalMovements }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Stock In:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ number_format($inbound, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Stock Out:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ number_format($outbound, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
@endif
