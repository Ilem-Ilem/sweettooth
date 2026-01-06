@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Department Name</th>
                <th>Category</th>
                <th>Branch</th>
                <th>Description</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $department)
                <tr>
                    <td>{{ $department->id }}</td>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->category?->name ?? '-' }}</td>
                    <td>{{ $department->branch?->name ?? 'Global' }}</td>
                    <td>{{ $department->description ?? '-' }}</td>
                    <td>{{ $department->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Departments Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Department Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Category</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Branch</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $department)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $department->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $department->category?->name ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $department->branch?->name ?? 'Global' }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $department->description ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
