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
            border-bottom: 2px solid #059669;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #047857;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-card {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 15px;
        }
        .summary-card label {
            display: block;
            color: #666;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .summary-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #059669;
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
            background-color: #f0fdf4;
        }
        .text-right {
            text-align: right;
        }
        .currency {
            font-weight: bold;
            color: #047857;
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
        <h1>Stock Valuation Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <label>Total Valuation</label>
            <div class="value currency">₦{{ number_format($data->sum(fn($s) => ($s->quantity_available + $s->quantity_reserved) * ($s->average_cost ?? 0)), 2) }}</div>
        </div>
        <div class="summary-card">
            <label>Available Value</label>
            <div class="value currency">₦{{ number_format($data->sum(fn($s) => $s->quantity_available * ($s->average_cost ?? 0)), 2) }}</div>
        </div>
        <div class="summary-card">
            <label>Reserved Value</label>
            <div class="value currency">₦{{ number_format($data->sum(fn($s) => $s->quantity_reserved * ($s->average_cost ?? 0)), 2) }}</div>
        </div>
        <div class="summary-card">
            <label>Total Items</label>
            <div class="value">{{ $data->count() }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th class="text-right">Qty Available</th>
                <th class="text-right">Qty Reserved</th>
                <th class="text-right">Avg Cost</th>
                <th class="text-right">Available Value</th>
                <th class="text-right">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                @php
                    $totalValue = ($item->quantity_available + $item->quantity_reserved) * ($item->average_cost ?? 0);
                    $availableValue = $item->quantity_available * ($item->average_cost ?? 0);
                @endphp
                <tr>
                    <td>{{ $item->item?->name ?? $item['name'] ?? 'N/A' }}</td>
                    <td>{{ $item->item?->sku ?? $item['sku'] ?? 'N/A' }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($item->item?->category ?? $item['category'] ?? 'N/A')) }}</td>
                    <td class="text-right">{{ number_format($item->quantity_available ?? $item['available'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item->quantity_reserved ?? $item['reserved'] ?? 0, 2) }}</td>
                    <td class="text-right currency">₦{{ number_format($item->average_cost ?? 0, 2) }}</td>
                    <td class="text-right currency">₦{{ number_format($availableValue, 2) }}</td>
                    <td class="text-right currency">₦{{ number_format($totalValue, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No items found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automatically generated valuation report. For more information, contact your finance department.</p>
    </div>
</body>
</html>
