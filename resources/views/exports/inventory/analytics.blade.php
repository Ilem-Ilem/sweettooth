@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; line-height: 1.4; color: #333; }
            .header { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #27ae60; }
            .header h1 { font-size: 20pt; color: #27ae60; margin-bottom: 5px; }
            .meta { font-size: 8pt; color: #666; margin-top: 8px; }
            .section-title { font-size: 12pt; font-weight: bold; color: #27ae60; margin-top: 20px; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #27ae60; }
            .metrics-grid { width: 100%; margin-bottom: 20px; }
            .metric-box { display: inline-block; width: 23%; margin: 1%; padding: 12px; background-color: #f8f9fa; border-left: 4px solid #27ae60; vertical-align: top; }
            .metric-label { font-size: 8pt; color: #666; margin-bottom: 3px; }
            .metric-value { font-size: 14pt; font-weight: bold; color: #2C3E50; }
            .metric-change { font-size: 7pt; margin-top: 2px; }
            .metric-change.positive { color: #27ae60; }
            .metric-change.negative { color: #e74c3c; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 8pt; }
            thead { background-color: #27ae60; color: white; }
            th { padding: 8px 6px; text-align: left; font-weight: bold; border: 1px solid #27ae60; }
            td { padding: 6px; border: 1px solid #ddd; }
            tbody tr:nth-child(odd) { background-color: #f9f9f9; }
            .status-good { color: #27ae60; font-weight: bold; }
            .status-moderate { color: #f39c12; font-weight: bold; }
            .status-low { color: #e67e22; font-weight: bold; }
            .status-critical { color: #e74c3c; font-weight: bold; }
            .insight-box { padding: 8px 12px; margin-bottom: 8px; border-left: 4px solid #3498db; background-color: #f8f9fa; font-size: 8pt; }
            .insight-box.positive { border-left-color: #27ae60; }
            .insight-box.negative { border-left-color: #e74c3c; }
            .insight-box.warning { border-left-color: #f39c12; }
            .insight-box.critical { border-left-color: #e74c3c; }
            .insight-box.info { border-left-color: #3498db; }
            .alert-row { padding: 6px 10px; margin-bottom: 5px; background-color: #fff3cd; border-left: 3px solid #f39c12; font-size: 8pt; }
            .alert-row.critical { background-color: #f8d7da; border-left-color: #e74c3c; }
            .alert-row.expired { background-color: #f8d7da; border-left-color: #dc3545; }
            .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 7pt; color: #999; }
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
                <div class="metric-value">N{{ number_format($data['total_stock_value'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Items</div>
                <div class="metric-value">{{ number_format($data['total_items'] ?? 0) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Purchases</div>
                <div class="metric-value">{{ number_format($data['total_purchases'] ?? 0) }}</div>
                <div class="metric-change">N{{ number_format($data['purchase_value'] ?? 0, 2) }} value</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Movements</div>
                <div class="metric-value">{{ number_format($data['total_movements'] ?? 0) }}</div>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-label">Stock In</div>
                <div class="metric-value" style="color: #27ae60;">{{ number_format($data['movements_in'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Stock Out</div>
                <div class="metric-value" style="color: #e74c3c;">{{ number_format($data['movements_out'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Pending Requests</div>
                <div class="metric-value" style="color: #f39c12;">{{ number_format($data['pending_requests'] ?? 0) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Completed Requests</div>
                <div class="metric-value" style="color: #27ae60;">{{ number_format($data['completed_requests'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Alert Summary -->
        <div class="metrics-grid">
            <div class="metric-box" style="border-left-color: #e74c3c;">
                <div class="metric-label">Low Stock Items</div>
                <div class="metric-value" style="color: #e74c3c;">{{ $data['low_stock_items'] ?? 0 }}</div>
            </div>
            <div class="metric-box" style="border-left-color: #dc3545;">
                <div class="metric-label">Critical Items</div>
                <div class="metric-value" style="color: #dc3545;">{{ $data['critical_items'] ?? 0 }}</div>
            </div>
            <div class="metric-box" style="border-left-color: #6c757d;">
                <div class="metric-label">Expired Items</div>
                <div class="metric-value" style="color: #6c757d;">{{ $data['expired_items'] ?? 0 }}</div>
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
        @if(!empty($data['stock_health_data']))
        <div class="section-title">Stock Health Status</div>
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
                @forelse($data['stock_health_data'] as $stock)
                <tr>
                    <td>{{ $stock['item_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($stock['stock_level'] ?? 0, 2) }} {{ $stock['uom'] ?? '' }}</td>
                    <td style="text-align: right;">{{ number_format($stock['reorder_level'] ?? 0, 2) }}</td>
                    <td style="text-align: right;">{{ $stock['health_percentage'] ?? 0 }}%</td>
                    <td><span class="status-{{ $stock['status'] ?? 'good' }}">{{ ucfirst($stock['status'] ?? 'Good') }}</span></td>
                    <td>{{ $stock['last_movement'] ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; color: #999;">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Department Breakdown -->
        @if(!empty($data['department_breakdown']))
        <div class="section-title">Category Breakdown</div>
        <table>
            <thead>
                <tr>
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
                @forelse($data['department_breakdown'] as $dept)
                <tr>
                    <td style="font-weight: bold;">{{ $dept['category'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">N{{ number_format($dept['stock_value'] ?? 0, 2) }}</td>
                    <td style="text-align: center;">{{ $dept['item_count'] ?? 0 }}</td>
                    <td style="text-align: right; color: #27ae60;">{{ number_format($dept['stock_in'] ?? 0, 2) }}</td>
                    <td style="text-align: right; color: #e74c3c;">{{ number_format($dept['stock_out'] ?? 0, 2) }}</td>
                    <td style="text-align: center; {{ ($dept['low_items'] ?? 0) > 0 ? 'color: #e74c3c; font-weight: bold;' : '' }}">{{ $dept['low_items'] ?? 0 }}</td>
                    <td style="text-align: center;">{{ $dept['requests'] ?? 0 }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; color: #999;">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Performance Metrics -->
        @if(!empty($data['performance_metrics']))
        <div class="section-title">Performance Metrics</div>
        <table>
            <thead>
                <tr><th style="width: 50%;">Metric</th><th style="width: 50%;">Value</th></tr>
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
                    <td>{{ $data['performance_metrics']['most_requested']['name'] ?? 'N/A' }} ({{ number_format($data['performance_metrics']['most_requested']['quantity'] ?? 0) }} requests)</td>
                </tr>
            </tbody>
        </table>
        @endif

        <div class="footer">
            <p>SweetTooth Inventory Analytics Report - Generated automatically</p>
        </div>
    </body>
    </html>
@endif

@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #27ae60; color: white; font-weight: bold;">
            <th colspan="6" style="text-align: center; padding: 15px; font-size: 16pt;">Inventory Analytics Report</th>
        </tr>
        <tr style="background-color: #1e8449; color: white;">
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
            <th>Metric</th><th>Value</th><th>Metric</th><th>Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">Total Stock Value</td>
            <td>{{ number_format($data['total_stock_value'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Total Items</td>
            <td>{{ $data['total_items'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Total Purchases</td>
            <td>{{ $data['total_purchases'] ?? 0 }}</td>
            <td style="font-weight: bold;">Purchase Value</td>
            <td>{{ number_format($data['purchase_value'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Stock In</td>
            <td style="color: green;">{{ number_format($data['movements_in'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Stock Out</td>
            <td style="color: red;">{{ number_format($data['movements_out'] ?? 0, 2) }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Low Stock Items</td>
            <td style="color: red; font-weight: bold;">{{ $data['low_stock_items'] ?? 0 }}</td>
            <td style="font-weight: bold;">Critical Items</td>
            <td style="color: red; font-weight: bold;">{{ $data['critical_items'] ?? 0 }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Expired Items</td>
            <td>{{ $data['expired_items'] ?? 0 }}</td>
            <td style="font-weight: bold;">Total Movements</td>
            <td>{{ $data['total_movements'] ?? 0 }}</td>
        </tr>
    </tbody>
</table>

<table><tr><td></td></tr></table>

<!-- Stock Health Section -->
@if(!empty($data['stock_health_data']))
<table>
    <thead>
        <tr style="background-color: #e74c3c; color: white; font-weight: bold;">
            <th colspan="6" style="padding: 10px;">STOCK HEALTH STATUS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th>Item Name</th><th>Stock Level</th><th>Reorder Level</th><th>Health %</th><th>Status</th><th>Last Movement</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['stock_health_data'] as $index => $stock)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold;">{{ $stock['item_name'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($stock['stock_level'] ?? 0, 2) }} {{ $stock['uom'] ?? '' }}</td>
            <td style="text-align: right;">{{ number_format($stock['reorder_level'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ $stock['health_percentage'] ?? 0 }}%</td>
            <td style="{{ ($stock['status'] ?? '') == 'critical' ? 'color: red; font-weight: bold;' : '' }}">{{ ucfirst($stock['status'] ?? 'Good') }}</td>
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
        <tr style="background-color: #9b59b6; color: white; font-weight: bold;">
            <th colspan="7" style="padding: 10px;">CATEGORY BREAKDOWN</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th>Category</th><th>Stock Value</th><th>Items</th><th>Stock In</th><th>Stock Out</th><th>Low Items</th><th>Requests</th>
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
@endif
