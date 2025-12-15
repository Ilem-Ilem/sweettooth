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
        .status-pending {
            color: #f59e0b;
            font-weight: bold;
        }
        .status-approved {
            color: #3b82f6;
            font-weight: bold;
        }
        .status-partially_dispatched {
            color: #a855f7;
            font-weight: bold;
        }
        .status-completed {
            color: #10b981;
            font-weight: bold;
        }
        .status-rejected {
            color: #ef4444;
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
        <h1>Item Requests Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Request #</th>
                <th>Department</th>
                <th>Requester</th>
                <th>Request Date</th>
                <th>Status</th>
                <th class="text-right">Items</th>
                <th class="text-right">Total Qty</th>
                <th class="text-right">Approved</th>
                <th class="text-right">Dispatched</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr>
                    <td>{{ $item->request_number ?? $item['request_number'] ?? 'N/A' }}</td>
                    <td>{{ $item->department?->name ?? $item['department'] ?? 'N/A' }}</td>
                    <td>{{ $item->requester?->name ?? $item['requester'] ?? 'N/A' }}</td>
                    <td>{{ isset($item->request_date) ? \Carbon\Carbon::parse($item->request_date)->format('Y-m-d') : ($item['request_date'] ?? 'N/A') }}</td>
                    <td>
                        @php
                            $status = $item->status ?? $item['status'] ?? 'pending';
                            $class = 'status-' . $status;
                        @endphp
                        <span class="{{ $class }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    </td>
                    <td class="text-right">{{ $item->requestDetails?->count() ?? $item['items_count'] ?? 0 }}</td>
                    <td class="text-right">{{ number_format($item->requestDetails?->sum('quantity_requested') ?? $item['total_quantity'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item->requestDetails?->sum('quantity_approved') ?? $item['approved_quantity'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($item->requestDetails?->sum('quantity_dispatched') ?? $item['dispatched_quantity'] ?? 0, 2) }}</td>
                    <td>{{ $item->notes ?? $item['notes'] ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">No item requests found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automatically generated report. For more information, contact your inventory manager.</p>
    </div>
</body>
</html>
