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
            border-bottom: 2px solid #dc2626;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #b91c1c;
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
            border-radius: 8px;
            padding: 15px;
        }
        .summary-card.critical {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
        }
        .summary-card.warning {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
        }
        .summary-card.info {
            background-color: #dbeafe;
            border: 1px solid #93c5fd;
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
        }
        .summary-card.critical .value { color: #991b1b; }
        .summary-card.warning .value { color: #92400e; }
        .summary-card.info .value { color: #1e40af; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #dc2626;
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
            background-color: #fee2e2;
        }
        .type-critical {
            color: #991b1b;
            font-weight: bold;
        }
        .type-warning {
            color: #92400e;
            font-weight: bold;
        }
        .type-info {
            color: #1e40af;
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
        <h1>Alerts Dashboard Report</h1>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    @php
        $totalAlerts = $data->count();
        $criticalAlerts = $data->where('type', 'critical')->count();
        $warningAlerts = $data->where('type', 'warning')->count();
        $infoAlerts = $data->where('type', 'info')->count();
    @endphp

    <div class="summary">
        <div class="summary-card critical">
            <label>Critical Alerts</label>
            <div class="value">{{ $criticalAlerts }}</div>
        </div>
        <div class="summary-card warning">
            <label>Warning Alerts</label>
            <div class="value">{{ $warningAlerts }}</div>
        </div>
        <div class="summary-card info">
            <label>Info Alerts</label>
            <div class="value">{{ $infoAlerts }}</div>
        </div>
        <div class="summary-card critical">
            <label>Total Alerts</label>
            <div class="value">{{ $totalAlerts }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Category</th>
                <th>Message</th>
                <th>Item</th>
                <th>SKU</th>
                <th>Current Level</th>
                <th>Reorder Level</th>
                <th>Recommended Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $alert)
                <tr>
                    <td>
                        <span class="type-{{ $alert['type'] ?? $alert->type ?? 'info' }}">
                            {{ ucfirst($alert['type'] ?? $alert->type ?? 'Info') }}
                        </span>
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $alert['category'] ?? $alert->category ?? 'N/A')) }}</td>
                    <td>{{ $alert['message'] ?? $alert->message ?? 'N/A' }}</td>
                    <td>{{ $alert['item'] ?? $alert->item ?? '' }}</td>
                    <td>{{ $alert['sku'] ?? $alert->sku ?? '' }}</td>
                    <td>
                        @if(isset($alert['current']))
                            {{ number_format($alert['current'], 2) }}
                        @elseif(isset($alert['damaged_qty']))
                            {{ number_format($alert['damaged_qty'], 2) }}
                        @endif
                    </td>
                    <td>
                        @if(isset($alert['reorder_level']))
                            {{ number_format($alert['reorder_level'], 2) }}
                        @elseif(isset($alert['days_left']))
                            {{ $alert['days_left'] }} days
                        @endif
                    </td>
                    <td>{{ $alert['action'] ?? $alert->action ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">
                        <strong>No alerts found</strong>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is an automatically generated alerts report. For immediate action, please review all critical alerts.</p>
    </div>
</body>
</html>
