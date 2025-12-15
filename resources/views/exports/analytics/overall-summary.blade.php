@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'DejaVu Sans', sans-serif;
                font-size: 9pt;
                line-height: 1.4;
                color: #333;
            }

            .header {
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 3px solid #2C3E50;
            }

            .header h1 {
                font-size: 20pt;
                color: #2C3E50;
                margin-bottom: 5px;
            }

            .meta {
                font-size: 8pt;
                color: #666;
                margin-top: 8px;
            }

            .section-title {
                font-size: 12pt;
                font-weight: bold;
                color: #2C3E50;
                margin-top: 20px;
                margin-bottom: 10px;
                padding-bottom: 5px;
                border-bottom: 2px solid #3498db;
            }

            .metrics-grid {
                width: 100%;
                margin-bottom: 20px;
            }

            .metric-box {
                display: inline-block;
                width: 23%;
                margin: 1%;
                padding: 12px;
                background-color: #f8f9fa;
                border-left: 4px solid #2C3E50;
                vertical-align: top;
            }

            .metric-label {
                font-size: 8pt;
                color: #666;
                margin-bottom: 3px;
            }

            .metric-value {
                font-size: 14pt;
                font-weight: bold;
                color: #2C3E50;
            }

            .metric-change {
                font-size: 7pt;
                margin-top: 2px;
            }

            .metric-change.positive { color: #27ae60; }
            .metric-change.negative { color: #e74c3c; }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                font-size: 8pt;
            }

            thead {
                background-color: #2C3E50;
                color: white;
            }

            th {
                padding: 8px 6px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #2C3E50;
            }

            td {
                padding: 6px;
                border: 1px solid #ddd;
            }

            tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }

            .status-good { color: #27ae60; font-weight: bold; }
            .status-moderate { color: #f39c12; font-weight: bold; }
            .status-low { color: #e67e22; font-weight: bold; }
            .status-critical { color: #e74c3c; font-weight: bold; }

            .insight-box {
                padding: 8px 12px;
                margin-bottom: 8px;
                border-left: 4px solid #3498db;
                background-color: #f8f9fa;
                font-size: 8pt;
            }

            .insight-box.positive { border-left-color: #27ae60; }
            .insight-box.negative { border-left-color: #e74c3c; }
            .insight-box.warning { border-left-color: #f39c12; }
            .insight-box.critical { border-left-color: #e74c3c; }
            .insight-box.info { border-left-color: #3498db; }

            .alert-row {
                padding: 6px 10px;
                margin-bottom: 5px;
                background-color: #fff3cd;
                border-left: 3px solid #f39c12;
                font-size: 8pt;
            }

            .alert-row.critical { background-color: #f8d7da; border-left-color: #e74c3c; }
            .alert-row.expired { background-color: #f8d7da; border-left-color: #dc3545; }
            .alert-row.warning { background-color: #fff3cd; border-left-color: #f39c12; }

            .footer {
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                font-size: 7pt;
                color: #999;
            }

            .page-break {
                page-break-after: always;
            }

            .two-column {
                width: 100%;
            }

            .two-column td {
                width: 50%;
                vertical-align: top;
                padding: 10px;
                border: none;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Inventory Analytics Report</h1>
            <div class="meta">
                <p><strong>Period:</strong> {{ $data['period']['from'] ?? 'N/A' }} to {{ $data['period']['to'] ?? 'N/A' }}</p>
                <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                <p><strong>Branch:</strong> {{ $data['branch_name'] ?? 'All Branches' }}</p>
            </div>
        </div>

        <!-- Summary Metrics -->
        <div class="section-title">Summary Overview</div>
        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-label">Total Stock Value</div>
                <div class="metric-value">N{{ number_format($data['summary']['total_stock_value'] ?? 0, 2) }}</div>
                @if(($data['summary']['stock_value_change_percentage'] ?? 0) != 0)
                    <div class="metric-change {{ ($data['summary']['stock_value_change_percentage'] ?? 0) > 0 ? 'positive' : 'negative' }}">
                        {{ ($data['summary']['stock_value_change_percentage'] ?? 0) > 0 ? '+' : '' }}{{ number_format($data['summary']['stock_value_change_percentage'] ?? 0, 1) }}% from previous period
                    </div>
                @endif
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Items</div>
                <div class="metric-value">{{ number_format($data['summary']['total_items'] ?? 0) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Purchases</div>
                <div class="metric-value">{{ number_format($data['summary']['total_purchases'] ?? 0) }}</div>
                <div class="metric-change">N{{ number_format($data['summary']['total_purchase_value'] ?? 0, 2) }} value</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Movements</div>
                <div class="metric-value">{{ number_format($data['summary']['total_movements'] ?? 0) }}</div>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-label">Stock In</div>
                <div class="metric-value" style="color: #27ae60;">{{ number_format($data['summary']['stock_in'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Stock Out</div>
                <div class="metric-value" style="color: #e74c3c;">{{ number_format($data['summary']['stock_out'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Pending Requests</div>
                <div class="metric-value" style="color: #f39c12;">{{ number_format($data['summary']['pending_requests'] ?? 0) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Completed Requests</div>
                <div class="metric-value" style="color: #27ae60;">{{ number_format($data['summary']['completed_requests'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Alert Summary -->
        <div class="metrics-grid">
            <div class="metric-box" style="border-left-color: #e74c3c;">
                <div class="metric-label">Low Stock Items</div>
                <div class="metric-value" style="color: #e74c3c;">{{ $data['summary']['low_stock_items'] ?? 0 }}</div>
            </div>
            <div class="metric-box" style="border-left-color: #dc3545;">
                <div class="metric-label">Critical Items</div>
                <div class="metric-value" style="color: #dc3545;">{{ $data['summary']['critical_items'] ?? 0 }}</div>
            </div>
            <div class="metric-box" style="border-left-color: #6c757d;">
                <div class="metric-label">Expired Items</div>
                <div class="metric-value" style="color: #6c757d;">{{ $data['summary']['expired_items'] ?? 0 }}</div>
            </div>
            <div class="metric-box" style="border-left-color: #3498db;">
                <div class="metric-label">Total Requests</div>
                <div class="metric-value">{{ $data['summary']['total_requests'] ?? 0 }}</div>
            </div>
        </div>

        <!-- Key Insights -->
        @if(!empty($data['insights']))
        <div class="section-title">Key Insights</div>
        @foreach($data['insights'] as $insight)
            <div class="insight-box {{ $insight['type'] ?? 'info' }}">
                {{ $insight['icon'] ?? '' }} {{ $insight['message'] ?? '' }}
            </div>
        @endforeach
        @endif

        <!-- Stock Health Table -->
        @if(!empty($data['stock_health']))
        <div class="section-title">Stock Health Status (Top 15 Items Needing Attention)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Item Name</th>
                    <th style="width: 15%;">Stock Level</th>
                    <th style="width: 15%;">Reorder Level</th>
                    <th style="width: 12%;">Health %</th>
                    <th style="width: 13%;">Status</th>
                    <th style="width: 15%;">Last Movement</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['stock_health'] as $stock)
                <tr>
                    <td>{{ $stock['item_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($stock['stock_level'] ?? 0, 2) }} {{ $stock['uom'] ?? '' }}</td>
                    <td style="text-align: right;">{{ number_format($stock['reorder_level'] ?? 0, 2) }}</td>
                    <td style="text-align: right;">{{ $stock['health_percentage'] ?? 0 }}%</td>
                    <td>
                        <span class="status-{{ $stock['status'] ?? 'good' }}">
                            {{ $stock['status_icon'] ?? '' }} {{ ucfirst($stock['status'] ?? 'Good') }}
                        </span>
                    </td>
                    <td>{{ $stock['last_movement'] ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">No stock health data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Department/Category Breakdown -->
        @if(!empty($data['department_breakdown']))
        <div class="section-title">Category Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Category</th>
                    <th style="width: 18%;">Stock Value</th>
                    <th style="width: 12%;">Items</th>
                    <th style="width: 12%;">Stock In</th>
                    <th style="width: 12%;">Stock Out</th>
                    <th style="width: 10%;">Low Items</th>
                    <th style="width: 11%;">Requests</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['department_breakdown'] as $dept)
                <tr>
                    <td>{{ $dept['category'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">N{{ number_format($dept['stock_value'] ?? 0, 2) }}</td>
                    <td style="text-align: center;">{{ $dept['item_count'] ?? 0 }}</td>
                    <td style="text-align: right;">{{ number_format($dept['stock_in'] ?? 0, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($dept['stock_out'] ?? 0, 2) }}</td>
                    <td style="text-align: center; {{ ($dept['low_items'] ?? 0) > 0 ? 'color: #e74c3c; font-weight: bold;' : '' }}">{{ $dept['low_items'] ?? 0 }}</td>
                    <td style="text-align: center;">{{ $dept['requests'] ?? 0 }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #999;">No category data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Top Alerts -->
        @if(!empty($data['top_alerts']) && count($data['top_alerts']) > 0)
        <div class="section-title">Top Alerts</div>
        @foreach(array_slice($data['top_alerts'], 0, 10) as $alert)
            <div class="alert-row {{ $alert['type'] ?? 'warning' }}">
                {{ $alert['icon'] ?? '' }} <strong>{{ $alert['item'] ?? '' }}</strong> - {{ $alert['message'] ?? '' }}
                <span style="float: right; font-size: 7pt;">Action: {{ $alert['action'] ?? 'View' }}</span>
            </div>
        @endforeach
        @endif

        <!-- Performance Metrics -->
        @if(!empty($data['performance_metrics']))
        <div class="section-title">Performance Metrics</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Metric</th>
                    <th style="width: 50%;">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Average Turnover Rate</td>
                    <td>{{ number_format($data['performance_metrics']['average_turnover_rate'] ?? 0, 2) }} units/day</td>
                </tr>
                <tr>
                    <td>Fastest Moving Item</td>
                    <td>{{ $data['performance_metrics']['fastest_moving']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['fastest_moving']['quantity'] ?? 0, 2) }} units)</td>
                </tr>
                <tr>
                    <td>Slowest Moving Item</td>
                    <td>{{ $data['performance_metrics']['slowest_moving']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['slowest_moving']['quantity'] ?? 0, 2) }} units)</td>
                </tr>
                <tr>
                    <td>Most Requested Item</td>
                    <td>{{ $data['performance_metrics']['most_requested']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['most_requested']['quantity'] ?? 0, 2) }} requests)</td>
                </tr>
                <tr>
                    <td>Expired vs Active Items</td>
                    <td>{{ number_format($data['performance_metrics']['expired_vs_active_percentage'] ?? 0, 2) }}% expired</td>
                </tr>
            </tbody>
        </table>
        @endif

        <div class="footer">
            <p>SweetTooth Inventory Analytics Report - Generated automatically. Please verify data before distribution.</p>
        </div>
    </body>
    </html>
@endif

@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
            <th colspan="6" style="text-align: center; padding: 15px; font-size: 16pt;">Inventory Analytics Report</th>
        </tr>
        <tr style="background-color: #34495e; color: white;">
            <th colspan="3">Period: {{ $data['period']['from'] ?? 'N/A' }} to {{ $data['period']['to'] ?? 'N/A' }}</th>
            <th colspan="3">Generated: {{ now()->format('d/m/Y H:i:s') }}</th>
        </tr>
    </thead>
</table>

<!-- Summary Section -->
<table>
    <thead>
        <tr style="background-color: #3498db; color: white; font-weight: bold;">
            <th colspan="4" style="padding: 10px;">SUMMARY METRICS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th>Metric</th>
            <th>Value</th>
            <th>Metric</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">Total Stock Value</td>
            <td>{{ number_format($data['summary']['total_stock_value'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Total Items</td>
            <td>{{ $data['summary']['total_items'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Total Purchases</td>
            <td>{{ $data['summary']['total_purchases'] ?? 0 }}</td>
            <td style="font-weight: bold;">Purchase Value</td>
            <td>{{ number_format($data['summary']['total_purchase_value'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Stock In</td>
            <td style="color: green;">{{ number_format($data['summary']['stock_in'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Stock Out</td>
            <td style="color: red;">{{ number_format($data['summary']['stock_out'] ?? 0, 2) }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Total Movements</td>
            <td>{{ $data['summary']['total_movements'] ?? 0 }}</td>
            <td style="font-weight: bold;">Total Requests</td>
            <td>{{ $data['summary']['total_requests'] ?? 0 }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Pending Requests</td>
            <td style="color: orange;">{{ $data['summary']['pending_requests'] ?? 0 }}</td>
            <td style="font-weight: bold;">Completed Requests</td>
            <td style="color: green;">{{ $data['summary']['completed_requests'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Low Stock Items</td>
            <td style="color: red; font-weight: bold;">{{ $data['summary']['low_stock_items'] ?? 0 }}</td>
            <td style="font-weight: bold;">Critical Items</td>
            <td style="color: red; font-weight: bold;">{{ $data['summary']['critical_items'] ?? 0 }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Expired Items</td>
            <td style="color: gray;">{{ $data['summary']['expired_items'] ?? 0 }}</td>
            <td style="font-weight: bold;">Value Change %</td>
            <td>{{ number_format($data['summary']['stock_value_change_percentage'] ?? 0, 2) }}%</td>
        </tr>
    </tbody>
</table>

<table><tr><td></td></tr></table>

<!-- Stock Health Overview Section -->
@if(!empty($data['health_overview']))
<table>
    <thead>
        <tr style="background-color: #3498db; color: white; font-weight: bold;">
            <th colspan="2" style="padding: 10px;">STOCK HEALTH OVERVIEW</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 50%;">Status</th>
            <th style="width: 50%;">Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['health_overview']['labels'] as $index => $label)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold;">{{ $label }}</td>
            <td style="text-align: center;">{{ $data['health_overview']['series'][$index] ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Stock Health Section -->
@if(!empty($data['stock_health']))
<table>
    <thead>
        <tr style="background-color: #e74c3c; color: white; font-weight: bold;">
            <th colspan="6" style="padding: 10px;">STOCK HEALTH STATUS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 30%;">Item Name</th>
            <th style="width: 15%;">Stock Level</th>
            <th style="width: 15%;">Reorder Level</th>
            <th style="width: 12%;">Health %</th>
            <th style="width: 13%;">Status</th>
            <th style="width: 15%;">Last Movement</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['stock_health'] as $index => $stock)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold;">{{ $stock['item_name'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($stock['stock_level'] ?? 0, 2) }} {{ $stock['uom'] ?? '' }}</td>
            <td style="text-align: right;">{{ number_format($stock['reorder_level'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ $stock['health_percentage'] ?? 0 }}%</td>
            <td style="text-align: center; {{ ($stock['status'] ?? '') == 'critical' ? 'color: red; font-weight: bold;' : (($stock['status'] ?? '') == 'low' ? 'color: orange;' : '') }}">{{ ucfirst($stock['status'] ?? 'Good') }}</td>
            <td>{{ $stock['last_movement'] ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Category Breakdown Section -->
@if(!empty($data['department_breakdown']))
<table>
    <thead>
        <tr style="background-color: #27ae60; color: white; font-weight: bold;">
            <th colspan="7" style="padding: 10px;">CATEGORY BREAKDOWN</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th>Category</th>
            <th>Stock Value</th>
            <th>Items</th>
            <th>Stock In</th>
            <th>Stock Out</th>
            <th>Low Items</th>
            <th>Requests</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['department_breakdown'] as $index => $dept)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold;">{{ $dept['category'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($dept['stock_value'] ?? 0, 2) }}</td>
            <td style="text-align: center;">{{ $dept['item_count'] ?? 0 }}</td>
            <td style="text-align: right; color: green;">{{ number_format($dept['stock_in'] ?? 0, 2) }}</td>
            <td style="text-align: right; color: red;">{{ number_format($dept['stock_out'] ?? 0, 2) }}</td>
            <td style="text-align: center; {{ ($dept['low_items'] ?? 0) > 0 ? 'color: red; font-weight: bold;' : '' }}">{{ $dept['low_items'] ?? 0 }}</td>
            <td style="text-align: center;">{{ $dept['requests'] ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Recent Stock Activity Section -->
@if(!empty($data['recent_activity']))
<table>
    <thead>
        <tr style="background-color: #16a085; color: white; font-weight: bold;">
            <th colspan="7" style="padding: 10px;">RECENT STOCK ACTIVITY (Last 10 Movements)</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 15%;">Date</th>
            <th style="width: 25%;">Item</th>
            <th style="width: 10%;">Type</th>
            <th style="width: 10%;">Qty</th>
            <th style="width: 15%;">Reference</th>
            <th style="width: 15%;">Mover</th>
            <th style="width: 10%;">Notes</th>
        </tr>
    </thead>
    <tbody>
        @foreach(array_slice($data['recent_activity'], 0, 10) as $index => $activity)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-size: 8pt;">{{ $activity['date'] }}</td>
            <td style="font-weight: bold;">{{ $activity['item'] }}</td>
            <td style="text-align: center; {{ $activity['type'] == 'in' ? 'color: green;' : 'color: red;' }} font-weight: bold;">{{ strtoupper($activity['type']) }}</td>
            <td style="text-align: right;">{{ number_format($activity['quantity'], 2) }}</td>
            <td style="font-size: 8pt;">{{ $activity['reference'] }}</td>
            <td style="font-size: 8pt;">{{ $activity['mover'] }}</td>
            <td style="font-size: 7pt;">{{ substr($activity['notes'], 0, 20) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Key Insights Section -->
@if(!empty($data['insights']))
<table>
    <thead>
        <tr style="background-color: #f39c12; color: white; font-weight: bold;">
            <th colspan="2" style="padding: 10px;">KEY INSIGHTS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 10%;">Type</th>
            <th style="width: 90%;">Message</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['insights'] as $index => $insight)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold; text-align: center;">{{ $insight['icon'] ?? '' }}</td>
            <td>{{ $insight['message'] ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Top Alerts Section -->
@if(!empty($data['top_alerts']))
<table>
    <thead>
        <tr style="background-color: #e74c3c; color: white; font-weight: bold;">
            <th colspan="4" style="padding: 10px;">TOP ALERTS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 10%;">Type</th>
            <th style="width: 40%;">Item</th>
            <th style="width: 40%;">Message</th>
            <th style="width: 10%;">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach(array_slice($data['top_alerts'], 0, 10) as $index => $alert)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold; text-align: center; {{ ($alert['type'] ?? '') == 'critical' ? 'color: red;' : (($alert['type'] ?? '') == 'expired' ? 'color: darkred;' : (($alert['type'] ?? '') == 'warning' ? 'color: orange;' : '')) }}">{{ $alert['icon'] ?? '' }}</td>
            <td style="font-weight: bold;">{{ $alert['item'] ?? 'N/A' }}</td>
            <td>{{ $alert['message'] ?? 'N/A' }}</td>
            <td style="text-align: center; font-size: 9pt;">{{ $alert['action'] ?? 'View' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Performance Metrics Section -->
@if(!empty($data['performance_metrics']))
<table>
    <thead>
        <tr style="background-color: #9b59b6; color: white; font-weight: bold;">
            <th colspan="2" style="padding: 10px;">PERFORMANCE METRICS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 50%;">Metric</th>
            <th style="width: 50%;">Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">Average Turnover Rate</td>
            <td>{{ number_format($data['performance_metrics']['average_turnover_rate'] ?? 0, 2) }} units/day</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Fastest Moving Item</td>
            <td>{{ $data['performance_metrics']['fastest_moving']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['fastest_moving']['quantity'] ?? 0, 2) }} units)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Slowest Moving Item</td>
            <td>{{ $data['performance_metrics']['slowest_moving']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['slowest_moving']['quantity'] ?? 0, 2) }} units)</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Most Requested Item</td>
            <td>{{ $data['performance_metrics']['most_requested']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['most_requested']['quantity'] ?? 0, 2) }} requests)</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Expired vs Active Items</td>
            <td>{{ number_format($data['performance_metrics']['expired_vs_active_percentage'] ?? 0, 2) }}%</td>
        </tr>
    </tbody>
</table>
@endif
@endif
