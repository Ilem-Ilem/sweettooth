@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Stock Take ID</th>
                <th>Date</th>
                <th>Type</th>
                <th>Status</th>
                <th>Conductor</th>
                <th>Verifier</th>
                <th>Items Counted</th>
                <th>Matched</th>
                <th>Surplus</th>
                <th>Shortage</th>
                <th>Notes</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $take)
                <tr>
                    <td>{{ $take->stock_take_number ?? '-' }}</td>
                    <td>{{ $take->stock_take_date ? \Carbon\Carbon::parse($take->stock_take_date)->format('Y-m-d') : '-' }}</td>
                    <td>{{ ucfirst($take->type ?? 'full') }}</td>
                    <td>{{ ucfirst($take->status ?? 'pending') }}</td>
                    <td>{{ $take->conductor?->name ?? '-' }}</td>
                    <td>{{ $take->verifier?->name ?? '-' }}</td>
                    <td>{{ $take->stockTakeDetails?->count() ?? 0 }}</td>
                    <td>{{ $take->stockTakeDetails?->where('variance_type', 'match')->count() ?? 0 }}</td>
                    <td>{{ $take->stockTakeDetails?->where('variance_type', 'surplus')->count() ?? 0 }}</td>
                    <td>{{ $take->stockTakeDetails?->where('variance_type', 'shortage')->count() ?? 0 }}</td>
                    <td>{{ $take->notes ?? '-' }}</td>
                    <td>{{ $take->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Stock Takes Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Stock Take ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Type</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Conductor</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Items</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $take)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $take->stock_take_number ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $take->stock_take_date ? \Carbon\Carbon::parse($take->stock_take_date)->format('Y-m-d') : '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($take->type ?? 'full') }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <span style="padding: 4px 8px; border-radius: 4px; {{ ($take->status === 'completed') ? 'background-color: #d4edda; color: #155724;' : 'background-color: #fff3cd; color: #856404;' }}">
                                {{ ucfirst($take->status ?? 'pending') }}
                            </span>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $take->conductor?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">{{ $take->stockTakeDetails?->count() ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $totalItems = $data->sum(fn ($take) => $take->stockTakeDetails?->count() ?? 0);
            $totalMatched = $data->sum(fn ($take) => $take->stockTakeDetails?->where('variance_type', 'match')->count() ?? 0);
            $totalSurplus = $data->sum(fn ($take) => $take->stockTakeDetails?->where('variance_type', 'surplus')->count() ?? 0);
            $totalShortage = $data->sum(fn ($take) => $take->stockTakeDetails?->where('variance_type', 'shortage')->count() ?? 0);
        @endphp

        <div style="margin-top: 30px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #007bff;">
            <h3 style="margin-top: 0; color: #333;">Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Stock Takes:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $data->count() }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Items Counted:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $totalItems }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Matched:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $totalMatched }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Surplus:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; text-align: right;">{{ $totalSurplus }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Shortage:</strong></td>
                    <td style="padding: 8px; text-align: right;">{{ $totalShortage }}</td>
                </tr>
            </table>
        </div>
    </div>
@endif
