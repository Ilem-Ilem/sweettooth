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
                border-bottom: 3px solid #E74C3C;
            }

            .header h1 {
                font-size: 20pt;
                color: #E74C3C;
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
                color: #E74C3C;
                margin-top: 20px;
                margin-bottom: 10px;
                padding-bottom: 5px;
                border-bottom: 2px solid #E74C3C;
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
                border-left: 4px solid #E74C3C;
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
                background-color: #E74C3C;
                color: white;
            }

            th {
                padding: 8px 6px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #E74C3C;
            }

            td {
                padding: 6px;
                border: 1px solid #ddd;
            }

            tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }

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
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Sales Analytics Report</h1>
            <div class="meta">
                <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($data['period']['from'] ?? now())->format('d/m/Y H:i') }} to {{ \Carbon\Carbon::parse($data['period']['to'] ?? now())->format('d/m/Y H:i') }}</p>
                <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <!-- Sales Overview -->
        <div class="section-title">Sales Overview</div>
        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-label">Total Sales</div>
                <div class="metric-value">N{{ number_format($data['overview']['total_sales'] ?? 0, 2) }}</div>
                @if(($data['overview']['growth_rate'] ?? 0) != 0)
                    <div class="metric-change {{ ($data['overview']['growth_rate'] ?? 0) > 0 ? 'positive' : 'negative' }}">
                        {{ ($data['overview']['growth_rate'] ?? 0) > 0 ? '+' : '' }}{{ number_format($data['overview']['growth_rate'] ?? 0, 1) }}% vs previous
                    </div>
                @endif
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Orders</div>
                <div class="metric-value">{{ number_format($data['overview']['total_orders'] ?? 0) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Avg Order Value</div>
                <div class="metric-value">N{{ number_format($data['overview']['avg_order_value'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Net Revenue</div>
                <div class="metric-value">N{{ number_format($data['overview']['net_revenue'] ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-label">Total Discount</div>
                <div class="metric-value" style="color: #e74c3c;">N{{ number_format($data['overview']['total_discount'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Total Tax</div>
                <div class="metric-value">N{{ number_format($data['overview']['total_tax'] ?? 0, 2) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Refunds</div>
                <div class="metric-value" style="color: #e74c3c;">N{{ number_format($data['overview']['total_refunds'] ?? 0, 2) }}</div>
                <div class="metric-change">{{ $data['overview']['refund_count'] ?? 0 }} orders</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Actual Revenue</div>
                <div class="metric-value" style="color: #27ae60;">N{{ number_format($data['overview']['actual_revenue'] ?? 0, 2) }}</div>
            </div>
        </div>

        <!-- Top Selling Products -->
        @if(!empty($data['top_products']))
        <div class="section-title">Top Selling Products</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%;">Product Name</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 25%;">Revenue</th>
                    <th style="width: 20%;">Orders</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['top_products'] as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $product['product']['name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($product['total_quantity'] ?? 0) }}</td>
                    <td style="text-align: right;">N{{ number_format($product['total_revenue'] ?? 0, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($product['order_count'] ?? 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">No product data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Payment Breakdown -->
        @if(!empty($data['payments']))
        <div class="section-title">Payment Methods</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Payment Method</th>
                    <th style="width: 30%;">Count</th>
                    <th style="width: 30%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['payments'] as $payment)
                <tr>
                    <td style="font-weight: bold;">{{ ucfirst($payment['payment_method'] ?? 'N/A') }}</td>
                    <td style="text-align: right;">{{ number_format($payment['count'] ?? 0) }}</td>
                    <td style="text-align: right;">N{{ number_format($payment['total'] ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #999;">No payment data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Category Sales -->
        @if(!empty($data['categories']))
        <div class="section-title">Sales by Category</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Category</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 20%;">Revenue</th>
                    <th style="width: 15%;">Orders</th>
                    <th style="width: 20%;">Products</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['categories'] as $category)
                <tr>
                    <td style="font-weight: bold;">{{ $category['category_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($category['total_quantity'] ?? 0) }}</td>
                    <td style="text-align: right;">N{{ number_format($category['total_revenue'] ?? 0, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($category['order_count'] ?? 0) }}</td>
                    <td style="text-align: right;">{{ number_format($category['product_count'] ?? 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">No category data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Shift Performance -->
        @if(!empty($data['shifts']))
        <div class="section-title">Shift Performance</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 15%;">Shift</th>
                    <th style="width: 20%;">Employee</th>
                    <th style="width: 12%;">Orders</th>
                    <th style="width: 18%;">Sales</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['shifts'] as $shift)
                <tr>
                    <td>{{ $shift['shift_date'] ?? 'N/A' }}</td>
                    <td>{{ ucfirst($shift['shift_type'] ?? 'N/A') }}</td>
                    <td>{{ $shift['employee_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($shift['total_orders'] ?? 0) }}</td>
                    <td style="text-align: right;">N{{ number_format($shift['total_sales'] ?? 0, 2) }}</td>
                    <td>{{ ucfirst($shift['status'] ?? 'N/A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">No shift data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @endif

        <!-- Profit Analysis -->
        @if(!empty($data['profit']))
        <div class="section-title">Profit Analysis</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Metric</th>
                    <th style="width: 50%;">Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">Total Revenue</td>
                    <td style="text-align: right;">N{{ number_format($data['profit']['total_revenue'] ?? 0, 2) }}</td>
                </tr>
                <tr style="background-color: #f9f9f9;">
                    <td style="font-weight: bold;">Total Cost</td>
                    <td style="text-align: right;">N{{ number_format($data['profit']['total_cost'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Gross Profit</td>
                    <td style="text-align: right; color: #27ae60; font-weight: bold;">N{{ number_format($data['profit']['gross_profit'] ?? 0, 2) }}</td>
                </tr>
                <tr style="background-color: #f9f9f9;">
                    <td style="font-weight: bold;">Gross Margin</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($data['profit']['gross_margin'] ?? 0, 2) }}%</td>
                </tr>
            </tbody>
        </table>
        @endif

        <div class="footer">
            <p>SweetTooth Sales Analytics Report - Generated automatically. Please verify data before distribution.</p>
        </div>
    </body>
    </html>
@endif

@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #E74C3C; color: white; font-weight: bold;">
            <th colspan="5" style="text-align: center; padding: 15px; font-size: 16pt;">Sales Analytics Report</th>
        </tr>
        <tr style="background-color: #c0392b; color: white;">
            <th colspan="3">Period: {{ \Carbon\Carbon::parse($data['period']['from'] ?? now())->format('d/m/Y H:i') }} to {{ \Carbon\Carbon::parse($data['period']['to'] ?? now())->format('d/m/Y H:i') }}</th>
            <th colspan="2">Generated: {{ now()->format('d/m/Y H:i:s') }}</th>
        </tr>
    </thead>
</table>

<!-- Overview Section -->
<table>
    <thead>
        <tr style="background-color: #27ae60; color: white; font-weight: bold;">
            <th colspan="4" style="padding: 10px;">SALES OVERVIEW</th>
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
            <td style="font-weight: bold;">Total Sales</td>
            <td>{{ number_format($data['overview']['total_sales'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Total Orders</td>
            <td>{{ $data['overview']['total_orders'] ?? 0 }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Average Order Value</td>
            <td>{{ number_format($data['overview']['avg_order_value'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Net Revenue</td>
            <td>{{ number_format($data['overview']['net_revenue'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Total Discount</td>
            <td style="color: red;">{{ number_format($data['overview']['total_discount'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Total Tax</td>
            <td>{{ number_format($data['overview']['total_tax'] ?? 0, 2) }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Total Refunds</td>
            <td style="color: red;">{{ number_format($data['overview']['total_refunds'] ?? 0, 2) }}</td>
            <td style="font-weight: bold;">Actual Revenue</td>
            <td style="color: green; font-weight: bold;">{{ number_format($data['overview']['actual_revenue'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Growth Rate</td>
            <td>{{ number_format($data['overview']['growth_rate'] ?? 0, 2) }}%</td>
            <td style="font-weight: bold;">Refund Rate</td>
            <td>{{ number_format($data['overview']['refund_rate'] ?? 0, 2) }}%</td>
        </tr>
    </tbody>
</table>

<table><tr><td></td></tr></table>

<!-- Top Products Section -->
@if(!empty($data['top_products']))
<table>
    <thead>
        <tr style="background-color: #3498db; color: white; font-weight: bold;">
            <th colspan="5" style="padding: 10px;">TOP SELLING PRODUCTS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 5%;">#</th>
            <th style="width: 35%;">Product Name</th>
            <th style="width: 20%;">Quantity</th>
            <th style="width: 25%;">Revenue</th>
            <th style="width: 15%;">Orders</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['top_products'] as $index => $product)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td>{{ $index + 1 }}</td>
            <td style="font-weight: bold;">{{ $product['product']['name'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($product['total_quantity'] ?? 0) }}</td>
            <td style="text-align: right;">{{ number_format($product['total_revenue'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ number_format($product['order_count'] ?? 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Payment Methods Section -->
@if(!empty($data['payments']))
<table>
    <thead>
        <tr style="background-color: #9b59b6; color: white; font-weight: bold;">
            <th colspan="3" style="padding: 10px;">PAYMENT METHODS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th>Payment Method</th>
            <th>Count</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['payments'] as $index => $payment)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold;">{{ ucfirst($payment['payment_method'] ?? 'N/A') }}</td>
            <td style="text-align: right;">{{ number_format($payment['count'] ?? 0) }}</td>
            <td style="text-align: right;">{{ number_format($payment['total'] ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Category Sales Section -->
@if(!empty($data['categories']))
<table>
    <thead>
        <tr style="background-color: #f39c12; color: white; font-weight: bold;">
            <th colspan="5" style="padding: 10px;">SALES BY CATEGORY</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th>Category</th>
            <th>Quantity</th>
            <th>Revenue</th>
            <th>Orders</th>
            <th>Products</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['categories'] as $index => $category)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="font-weight: bold;">{{ $category['category_name'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($category['total_quantity'] ?? 0) }}</td>
            <td style="text-align: right;">{{ number_format($category['total_revenue'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ number_format($category['order_count'] ?? 0) }}</td>
            <td style="text-align: right;">{{ number_format($category['product_count'] ?? 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<table><tr><td></td></tr></table>

<!-- Profit Analysis Section -->
@if(!empty($data['profit']))
<table>
    <thead>
        <tr style="background-color: #27ae60; color: white; font-weight: bold;">
            <th colspan="2" style="padding: 10px;">PROFIT ANALYSIS</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 50%;">Metric</th>
            <th style="width: 50%;">Value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">Total Revenue</td>
            <td style="text-align: right;">{{ number_format($data['profit']['total_revenue'] ?? 0, 2) }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Total Cost</td>
            <td style="text-align: right;">{{ number_format($data['profit']['total_cost'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Gross Profit</td>
            <td style="text-align: right; color: green; font-weight: bold;">{{ number_format($data['profit']['gross_profit'] ?? 0, 2) }}</td>
        </tr>
        <tr style="background-color: #f9f9f9;">
            <td style="font-weight: bold;">Gross Margin</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($data['profit']['gross_margin'] ?? 0, 2) }}%</td>
        </tr>
    </tbody>
</table>
@endif
@endif
