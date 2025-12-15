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
            border-bottom: 2px solid #2563eb;
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
            background-color: #2563eb;
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
        .status-good {
            color: #16a34a;
            font-weight: bold;
        }
        .status-warning {
            color: #ea580c;
            font-weight: bold;
        }
        .status-critical {
            color: #dc2626;
            font-weight: bold;
        }
        .status-expired {
            color: #6b7280;
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
        <h1>Stock Level Analytics Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th class="text-right">Available</th>
                <th class="text-right">Reserved</th>
                <th class="text-right">Damaged</th>
                <th class="text-right">Reorder Level</th>
                <th>Health Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $item->item?->name ?? $item['name'] ?? 'N/A' }}</td>
                    <td>{{ $item->item?->sku ?? $item['sku'] ?? 'N/A' }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($item->item?->category ?? $item['category'] ?? 'N/A')) }}</td>
                    <td class="text-right">{{ number_format($item->quantity_available ?? $item['available'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item->quantity_reserved ?? $item['reserved'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item->quantity_damaged ?? $item['damaged'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ $item->item?->reorder_level ? number_format($item->item->reorder_level, 2) : 'Not set' }}</td>
                    <td>
                        @php
                            $status = $item->health_status ?? $item['status'] ?? 'unknown';
                            $class = 'status-' . $status;
                        @endphp
                        <span class="{{ $class }}">{{ ucfirst($status) }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No stock items found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automatically generated report. For more information, contact your inventory manager.</p>
    </div>
</body>
</html>
