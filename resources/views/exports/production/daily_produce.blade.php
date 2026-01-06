@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Production ID</th>
                <th>Date</th>
                <th>Product</th>
                <th>Opening Qty</th>
                <th>Produced</th>
                <th>Approved</th>
                <th>Rejected</th>
                <th>Quality %</th>
                <th>Closing Qty</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->produce_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $record->recipe?->product_name ?? '-' }}</td>
                    <td>{{ number_format($record->opening_quantity ?? 0, 2) }}</td>
                    <td>{{ number_format($record->quantity_produced ?? 0, 2) }}</td>
                    <td>{{ number_format($record->quantity_approved ?? 0, 2) }}</td>
                    <td>{{ number_format($record->quantity_rejected ?? 0, 2) }}</td>
                    <td>{{ number_format($record->quality_percentage ?? 0, 2) }}</td>
                    <td>{{ number_format($record->closing_quantity ?? 0, 2) }}</td>
                    <td>{{ ucfirst($record->status ?? '-') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Daily Production Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Product</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Opening</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Produced</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Approved</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Rejected</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Quality %</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Closing</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $record)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $record->produce_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $record->recipe?->product_name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($record->opening_quantity ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($record->quantity_produced ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($record->quantity_approved ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($record->quantity_rejected ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                            <span style="background-color: {{ ($record->quality_percentage ?? 0) >= 90 ? '#d4edda' : ($record->quality_percentage ?? 0) >= 70 ? '#fff3cd' : '#f8d7da' }}; padding: 4px 8px; border-radius: 4px;">
                                {{ number_format($record->quality_percentage ?? 0, 1) }}%
                            </span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($record->closing_quantity ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->count() > 0)
            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
                <p style="font-weight: bold;">Summary</p>
                <table style="width: 400px;">
                    <tr>
                        <td style="padding: 8px;">Total Produced:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('quantity_produced'), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Approved:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('quantity_approved'), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Rejected:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('quantity_rejected'), 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #ddd; font-weight: bold;">
                        <td style="padding: 8px;">Average Quality %:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->avg('quality_percentage'), 1) }}%</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
@endif
