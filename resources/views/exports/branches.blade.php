@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Branch Name</th>
                <th>Code</th>
                <th>Location</th>
                <th>City</th>
                <th>Country</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Manager</th>
                <th>Status</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $branch)
                <tr>
                    <td>{{ $branch->id }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->code }}</td>
                    <td>{{ $branch->location ?? '-' }}</td>
                    <td>{{ $branch->city ?? '-' }}</td>
                    <td>{{ $branch->country ?? '-' }}</td>
                    <td>{{ $branch->email ?? '-' }}</td>
                    <td>{{ $branch->phone ?? '-' }}</td>
                    <td>{{ $branch->manager?->name ?? '-' }}</td>
                    <td>{{ $branch->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $branch->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Branches Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Branch Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Code</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Location</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">City</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Manager</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $branch)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $branch->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $branch->code }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $branch->location ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $branch->city ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $branch->manager?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <span style="padding: 4px 8px; border-radius: 4px; {{ $branch->is_active ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }}">
                                {{ $branch->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
