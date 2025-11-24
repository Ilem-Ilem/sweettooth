@if($forExcel ?? false)
    <table>
        <thead>
            <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
                <th style="border: 1px solid #ddd; padding: 10px;">ID</th>
                <th style="border: 1px solid #ddd; padding: 10px;">Name</th>
                <th style="border: 1px solid #ddd; padding: 10px;">Email</th>
                <th style="border: 1px solid #ddd; padding: 10px;">Status</th>
                <th style="border: 1px solid #ddd; padding: 10px;">Created At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
                <tr style="background-color: {{ $loop->odd ? '#f9f9f9' : 'white' }};">
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->id ?? $item['id'] ?? '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->name ?? $item['name'] ?? '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $item->email ?? $item['email'] ?? '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <span style="padding: 4px 8px; border-radius: 4px; 
                            background-color: {{ ($item->status ?? $item['status'] ?? 'inactive') === 'active' ? '#d4edda' : '#f8d7da' }};">
                            {{ ucfirst($item->status ?? $item['status'] ?? 'N/A') }}
                        </span>
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : ($item['created_at'] ?? 'N/A') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px; color: #999;">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endif
