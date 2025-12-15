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

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
                font-size: 9pt;
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

            .footer {
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                font-size: 7pt;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Inventory Items Report</h1>
            <div class="meta">
                <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="section-title">All Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">SKU</th>
                    <th style="width: 30%;">Name</th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 12%;">Reorder Level</th>
                    <th style="width: 13%;">Status</th>
                    <th style="width: 10%;">UOM</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                <tr>
                    <td>{{ $item['sku'] ?? 'N/A' }}</td>
                    <td>{{ $item['name'] ?? 'N/A' }}</td>
                    <td>{{ $item['category'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ $item['reorder_level'] ?? 0 }}</td>
                    <td>{{ ucfirst($item['status'] ?? 'N/A') }}</td>
                    <td>{{ $item['uom'] ?? 'units' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">No items found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>SweetTooth Inventory Items Report - Generated automatically.</p>
        </div>
    </body>
    </html>
@endif

@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
            <th colspan="6" style="text-align: center; padding: 15px; font-size: 14pt;">Inventory Items Report</th>
        </tr>
        <tr style="background-color: #34495e; color: white;">
            <th colspan="6" style="padding: 10px;">Generated: {{ now()->format('d/m/Y H:i:s') }}</th>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr style="background-color: #2C3E50; color: white;">
            <th style="width: 15%;">SKU</th>
            <th style="width: 30%;">Name</th>
            <th style="width: 20%;">Category</th>
            <th style="width: 12%;">Reorder Level</th>
            <th style="width: 13%;">Status</th>
            <th style="width: 10%;">UOM</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $item)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td>{{ $item['sku'] ?? 'N/A' }}</td>
            <td style="font-weight: bold;">{{ $item['name'] ?? 'N/A' }}</td>
            <td>{{ $item['category'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ $item['reorder_level'] ?? 0 }}</td>
            <td>{{ ucfirst($item['status'] ?? 'N/A') }}</td>
            <td>{{ $item['uom'] ?? 'units' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
