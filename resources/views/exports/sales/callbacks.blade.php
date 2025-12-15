@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; line-height: 1.4; color: #333; }
            .header { margin-bottom: 25px; padding-bottom: 15px; border-bottom: 3px solid #9b59b6; }
            .header h1 { font-size: 20pt; color: #9b59b6; margin-bottom: 5px; }
            .meta { font-size: 8pt; color: #666; margin-top: 8px; }
            .section-title { font-size: 12pt; font-weight: bold; color: #9b59b6; margin-top: 20px; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 2px solid #9b59b6; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 8pt; }
            thead { background-color: #9b59b6; color: white; }
            th { padding: 8px 6px; text-align: left; font-weight: bold; border: 1px solid #9b59b6; }
            td { padding: 6px; border: 1px solid #ddd; }
            tbody tr:nth-child(odd) { background-color: #f9f9f9; }
            .status-pending { color: #f39c12; font-weight: bold; }
            .status-approved { color: #3498db; font-weight: bold; }
            .status-received { color: #9b59b6; font-weight: bold; }
            .status-completed { color: #27ae60; font-weight: bold; }
            .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd; text-align: center; font-size: 7pt; color: #999; }
            .summary-box { display: inline-block; width: 23%; margin: 1%; padding: 12px; background-color: #f8f9fa; border-left: 4px solid #9b59b6; vertical-align: top; }
            .summary-label { font-size: 8pt; color: #666; }
            .summary-value { font-size: 14pt; font-weight: bold; color: #2C3E50; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Sales Callbacks Report</h1>
            <div class="meta">
                <p><strong>Period:</strong> {{ $data['period']['from'] ?? 'N/A' }} to {{ $data['period']['to'] ?? 'N/A' }}</p>
                <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                <p><strong>Branch:</strong> {{ $data['branch_name'] ?? 'All Branches' }}</p>
            </div>
        </div>

        <!-- Summary -->
        @if(!empty($data['summary']))
        <div style="margin-bottom: 20px;">
            <div class="summary-box">
                <div class="summary-label">Total Callbacks</div>
                <div class="summary-value">{{ $data['summary']['total'] ?? 0 }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Pending</div>
                <div class="summary-value" style="color: #f39c12;">{{ $data['summary']['pending'] ?? 0 }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Approved</div>
                <div class="summary-value" style="color: #3498db;">{{ $data['summary']['approved'] ?? 0 }}</div>
            </div>
            <div class="summary-box">
                <div class="summary-label">Completed</div>
                <div class="summary-value" style="color: #27ae60;">{{ $data['summary']['completed'] ?? 0 }}</div>
            </div>
        </div>
        @endif

        <!-- Callbacks Table -->
        <div class="section-title">Callback Details</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 20%;">Product</th>
                    <th style="width: 10%;">Quantity</th>
                    <th style="width: 15%;">Reason</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 18%;">Callback Time</th>
                    <th style="width: 15%;">Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['callbacks'] ?? [] as $callback)
                <tr>
                    <td>{{ $callback['id'] ?? 'N/A' }}</td>
                    <td style="font-weight: bold;">{{ $callback['product_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ number_format($callback['quantity'] ?? 0, 2) }} {{ $callback['uom'] ?? '' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $callback['reason'] ?? 'N/A')) }}</td>
                    <td>
                        <span class="status-{{ $callback['status'] ?? 'pending' }}">
                            {{ ucfirst(str_replace('_', ' ', $callback['status'] ?? 'pending')) }}
                        </span>
                    </td>
                    <td>{{ $callback['callback_time'] ?? 'N/A' }}</td>
                    <td>{{ $callback['recorded_by'] ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #999; padding: 20px;">No callbacks found for the selected period</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>SweetTooth Sales Callbacks Report - Generated automatically</p>
        </div>
    </body>
    </html>
@endif

@if($forExcel ?? false)
<table>
    <thead>
        <tr style="background-color: #9b59b6; color: white; font-weight: bold;">
            <th colspan="7" style="text-align: center; padding: 15px; font-size: 16pt;">Sales Callbacks Report</th>
        </tr>
        <tr style="background-color: #8e44ad; color: white;">
            <th colspan="4">Period: {{ $data['period']['from'] ?? 'N/A' }} to {{ $data['period']['to'] ?? 'N/A' }}</th>
            <th colspan="3">Generated: {{ now()->format('d/m/Y H:i:s') }}</th>
        </tr>
    </thead>
</table>

<table><tr><td></td></tr></table>

<table>
    <thead>
        <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
            <th>ID</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>UOM</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Callback Time</th>
            <th>Recorded By</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['callbacks'] ?? [] as $index => $callback)
        <tr style="{{ $index % 2 == 0 ? '' : 'background-color: #f9f9f9;' }}">
            <td>{{ $callback['id'] ?? 'N/A' }}</td>
            <td style="font-weight: bold;">{{ $callback['product_name'] ?? 'N/A' }}</td>
            <td style="text-align: right;">{{ number_format($callback['quantity'] ?? 0, 2) }}</td>
            <td>{{ $callback['uom'] ?? '' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $callback['reason'] ?? 'N/A')) }}</td>
            <td style="{{ ($callback['status'] ?? '') == 'completed' ? 'color: green;' : (($callback['status'] ?? '') == 'pending' ? 'color: orange;' : '') }}">
                {{ ucfirst(str_replace('_', ' ', $callback['status'] ?? 'pending')) }}
            </td>
            <td>{{ $callback['callback_time'] ?? 'N/A' }}</td>
            <td>{{ $callback['recorded_by'] ?? 'N/A' }}</td>
            <td>{{ $callback['notes'] ?? '' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align: center; color: #999;">No callbacks found</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endif
