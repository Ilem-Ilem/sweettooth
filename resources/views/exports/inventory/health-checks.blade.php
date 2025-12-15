<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #1e40af;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #3b82f6;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tr:hover {
            background-color: #eff6ff;
        }
        .text-right {
            text-align: right;
        }
        .condition-good {
            color: #16a34a;
            background-color: #dcfce7;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .condition-fair {
            color: #2563eb;
            background-color: #dbeafe;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .condition-poor {
            color: #ca8a04;
            background-color: #fef3c7;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .condition-damaged {
            color: #ea580c;
            background-color: #fed7aa;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .condition-expired {
            color: #991b1b;
            background-color: #fee2e2;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Health Checks Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Check Date</th>
                <th>Condition</th>
                <th class="text-right">Qty Affected</th>
                <th>UOM</th>
                <th>Observations</th>
                <th>Action Taken</th>
                <th>Checked By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $check)
                <tr>
                    <td>{{ $check->stock?->item?->name ?? $check['name'] ?? 'N/A' }}</td>
                    <td>{{ $check->stock?->item?->sku ?? $check['sku'] ?? 'N/A' }}</td>
                    <td>{{ $check->check_date ? $check->check_date->format('Y-m-d') : ($check['check_date'] ?? 'N/A') }}</td>
                    <td>
                        @php
                            $condition = $check->condition ?? $check['condition'] ?? 'unknown';
                            $class = 'condition-' . strtolower($condition);
                        @endphp
                        <span class="{{ $class }}">{{ ucfirst($condition) }}</span>
                    </td>
                    <td class="text-right">{{ $check->quantity_affected ? number_format($check->quantity_affected, 2) : ($check['quantity_affected'] ?? '0') }}</td>
                    <td>{{ $check->stock?->item?->unitOfMeasure?->symbol ?? ($check['uom'] ?? 'units') }}</td>
                    <td>{{ $check->observations ?? $check['observations'] ?? 'N/A' }}</td>
                    <td>{{ $check->action_taken ?? $check['action_taken'] ?? 'N/A' }}</td>
                    <td>{{ $check->checker?->name ?? $check['checker'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">No health checks found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automatically generated report. For more information, contact your inventory manager.</p>
    </div>
</body>
</html>
