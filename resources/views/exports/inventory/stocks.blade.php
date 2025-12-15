@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; line-height: 1.4; color: #333; }
            .header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 3px solid #3498db; }
            .header h1 { font-size: 18pt; color: #2C3E50; margin-bottom: 5px; }
            .meta { font-size: 7pt; color: #666; margin-top: 8px; }
            .section-title { font-size: 11pt; font-weight: bold; color: #2C3E50; margin-top: 15px; margin-bottom: 8px; padding-bottom: 3px; border-bottom: 2px solid #3498db; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 7pt; }
            thead { background-color: #2C3E50; color: white; }
            th { padding: 6px 4px; text-align: left; font-weight: bold; border: 1px solid #2C3E50; }
            td { padding: 4px 4px; border: 1px solid #ddd; }
            tbody tr:nth-child(odd) { background-color: #f9f9f9; }
            .status-good { color: #27ae60; font-weight: bold; }
            .status-moderate { color: #f39c12; font-weight: bold; }
            .status-low { color: #e67e22; font-weight: bold; }
            .status-critical { color: #e74c3c; font-weight: bold; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; text-align: center; font-size: 6pt; color: #999; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Inventory Stock Report</h1>
            <div class="meta">
                <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                <p><strong>Total Records:</strong> {{ count($data) }}</p>
            </div>
        </div>

        <!-- Stock Records Table -->
        <div class="section-title">Stock Records</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">SKU</th>
                    <th style="width: 18%;">Item Name</th>
                    <th style="width: 10%;">Category</th>
                    <th style="width: 7%;">Available</th>
                    <th style="width: 7%;">Reserved</th>
                    <th style="width: 7%;">Damaged</th>
                    <th style="width: 7%;">Total</th>
                    <th style="width: 5%;">UOM</th>
                    <th style="width: 8%;">Avg Cost</th>
                    <th style="width: 8%;">Total Value</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 6%;">Expiry</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $stock)
                <tr>
                    <td class="text-center"><strong>{{ $stock['sku'] ?? 'N/A' }}</strong></td>
                    <td>{{ $stock['item_name'] ?? 'N/A' }}</td>
                    <td>{{ $stock['category'] ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($stock['quantity_available'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($stock['quantity_reserved'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($stock['quantity_damaged'] ?? 0, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($stock['quantity_total'] ?? 0, 2) }}</strong></td>
                    <td class="text-center">{{ $stock['uom'] ?? 'units' }}</td>
                    <td class="text-right">₦{{ number_format($stock['average_cost'] ?? 0, 2) }}</td>
                    <td class="text-right"><strong>₦{{ number_format($stock['total_value'] ?? 0, 2) }}</strong></td>
                    <td class="text-center"><span class="status-{{ $stock['health_status'] ? strtolower($stock['health_status']) : 'good' }}">{{ $stock['health_status'] ?? 'Good' }}</span></td>
                    <td>{{ $stock['expiry_date'] ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="12" style="text-align: center; color: #999;">No stocks found</td></tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <div style="margin-top: 20px;">
            <strong>Total Stock Value:</strong> ₦{{ number_format($data->sum(fn($s) => $s['total_value'] ?? 0), 2) }}
        </div>

        <div class="footer">
            <p>SweetTooth Inventory Stock Report - Generated automatically</p>
        </div>
    </body>
    </html>
@endif

@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #3498db; color: white; font-weight: bold;">
            <th colspan="12" style="text-align: center; padding: 15px; font-size: 14pt;">Inventory Stock Report</th>
        </tr>
        <tr style="background-color: #2C3E50; color: white;">
            <th colspan="12" style="padding: 10px;">Generated: {{ now()->format('d/m/Y H:i:s') }} | Total Records: {{ count($data) }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
            <th style="text-align: center;">SKU</th>
            <th>Item Name</th>
            <th>Category</th>
            <th style="text-align: right;">Available</th>
            <th style="text-align: right;">Reserved</th>
            <th style="text-align: right;">Damaged</th>
            <th style="text-align: right;">Total</th>
            <th style="text-align: center;">UOM</th>
            <th style="text-align: right;">Avg Cost</th>
            <th style="text-align: right;">Total Value</th>
            <th style="text-align: center;">Reorder Level</th>
            <th style="text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $stock)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td style="text-align: center; font-weight: bold;">{{ $stock['sku'] ?? 'N/A' }}</td>
            <td>{{ $stock['item_name'] ?? 'N/A' }}</td>
            <td>{{ $stock['category'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($stock['quantity_available'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ number_format($stock['quantity_reserved'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ number_format($stock['quantity_damaged'] ?? 0, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($stock['quantity_total'] ?? 0, 2) }}</td>
            <td style="text-align: center;">{{ $stock['uom'] ?? 'units' }}</td>
            <td style="text-align: right;">{{ number_format($stock['average_cost'] ?? 0, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($stock['total_value'] ?? 0, 2) }}</td>
            <td style="text-align: right;">{{ number_format($stock['reorder_level'] ?? 0, 2) }}</td>
            <td style="text-align: center; {{ strtolower($stock['health_status'] ?? 'good') == 'critical' ? 'color: red; font-weight: bold;' : '' }}">{{ $stock['health_status'] ?? 'Good' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table><tr><td></td></tr></table>

<table>
    <tr style="background-color: #3498db; color: white; font-weight: bold;">
        <td colspan="2" style="padding: 10px;">SUMMARY</td>
    </tr>
    <tr style="background-color: #f9f9f9;">
        <td style="font-weight: bold; padding: 8px;">Total Stock Value</td>
        <td style="text-align: right; padding: 8px;">{{ number_format($data->sum(fn($s) => $s['total_value'] ?? 0), 2) }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold; padding: 8px;">Total Items</td>
        <td style="text-align: right; padding: 8px;">{{ count($data) }}</td>
    </tr>
</table>
@endif
