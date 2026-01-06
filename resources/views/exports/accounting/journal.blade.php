@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Entry ID</th>
                <th>Date</th>
                <th>Account</th>
                <th>Account Code</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Reference</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->entry_date?->format('Y-m-d') ?? '-' }}</td>
                    <td>{{ $entry->glAccount?->account_name ?? '-' }}</td>
                    <td>{{ $entry->glAccount?->account_code ?? '-' }}</td>
                    <td>{{ $entry->description ?? '-' }}</td>
                    <td>{{ number_format($entry->debit ?? 0, 2) }}</td>
                    <td>{{ number_format($entry->credit ?? 0, 2) }}</td>
                    <td>{{ $entry->reference_number ?? '-' }}</td>
                    <td>{{ ucfirst($entry->status ?? '-') }}</td>
                </tr>
            @endforeach
            <tr style="border-top: 2px solid #000; font-weight: bold;">
                <td colspan="5" style="text-align: right;">TOTALS:</td>
                <td>{{ number_format($data->sum('debit'), 2) }}</td>
                <td>{{ number_format($data->sum('credit'), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">General Ledger Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Account</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Description</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Debit</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Credit</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Reference</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $entry)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $entry->entry_date?->format('Y-m-d') ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $entry->glAccount?->account_code ?? '-' }} - {{ $entry->glAccount?->account_name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $entry->description ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($entry->debit ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($entry->credit ?? 0, 2) }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $entry->reference_number ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <span style="padding: 4px 8px; border-radius: 4px; {{ $entry->status === 'posted' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #fff3cd; color: #856404;' }}">
                                {{ ucfirst($entry->status ?? '-') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->count() > 0)
            <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
                <p style="font-weight: bold;">Summary</p>
                <table style="width: 300px;">
                    <tr>
                        <td style="padding: 8px;">Total Debits:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('debit'), 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;">Total Credits:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('credit'), 2) }}</td>
                    </tr>
                    <tr style="border-top: 1px solid #ddd; font-weight: bold;">
                        <td style="padding: 8px;">Balance:</td>
                        <td style="padding: 8px; text-align: right;">{{ number_format($data->sum('debit') - $data->sum('credit'), 2) }}</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
@endif
