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
                line-height: 1.5;
                color: #333;
            }
            
            .header {
                margin-bottom: 30px;
                border-bottom: 3px solid #2C3E50;
                padding-bottom: 15px;
            }
            
            .header h1 {
                font-size: 24pt;
                color: #2C3E50;
                margin-bottom: 5px;
            }
            
            .header p {
                font-size: 8pt;
                color: #666;
            }
            
            .export-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
                font-size: 8pt;
                color: #666;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            thead {
                background-color: #2C3E50;
                color: white;
            }
            
            th {
                padding: 10px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #ddd;
                font-size: 9pt;
            }
            
            td {
                padding: 8px;
                border: 1px solid #ddd;
                font-size: 9pt;
            }
            
            tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }
            
            tbody tr:hover {
                background-color: #f0f0f0;
            }
            
            .status {
                display: inline-block;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 8pt;
                font-weight: bold;
            }
            
            .status.active {
                background-color: #d4edda;
                color: #155724;
            }
            
            .status.inactive {
                background-color: #f8d7da;
                color: #721c24;
            }
            
            .status.pending {
                background-color: #fff3cd;
                color: #856404;
            }
            
            .footer {
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                font-size: 8pt;
                color: #999;
            }
            
            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Export Report</h1>
            <p>Generated on {{ now()->format('d/m/Y H:i:s') }} by {{ auth()->user()?->name ?? 'System' }}</p>
        </div>
        
        <div class="export-info">
            <span>Total Records: <strong>{{ $data->count() }}</strong></span>
            <span>Timezone: <strong>{{ config('app.timezone', 'UTC') }}</strong></span>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 25%;">Name</th>
                    <th style="width: 35%;">Email</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 17%;">Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <tr>
                        <td>{{ $item->id ?? $item['id'] ?? '' }}</td>
                        <td>{{ $item->name ?? $item['name'] ?? '' }}</td>
                        <td>{{ $item->email ?? $item['email'] ?? '' }}</td>
                        <td>
                            @php
                                $status = $item->status ?? $item['status'] ?? 'inactive';
                            @endphp
                            <span class="status {{ strtolower($status) }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            @if($item->created_at)
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            @else
                                {{ $item['created_at'] ?? 'N/A' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #999;">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="footer">
            <p>This is an automatically generated report. Please verify data before use.</p>
        </div>
    </body>
    </html>
@endif
