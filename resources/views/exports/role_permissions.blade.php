@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Role Name</th>
                <th>Guard</th>
                <th>Permissions</th>
                <th>Permissions Count</th>
                <th>Created Date</th>
                <th>Updated Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->guard_name }}</td>
                    <td>{{ $role->permissions->pluck('name')->join(', ') }}</td>
                    <td>{{ $role->permissions->count() }}</td>
                    <td>{{ $role->created_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $role->updated_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Role Permissions Report</h2>
        <p style="text-align: center; color: #666; margin-bottom: 20px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Role Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Guard</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Permissions Count</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Created Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $role)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $role->name }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $role->guard_name }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $role->permissions->count() }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $role->created_at?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                    @if($role->permissions->count() > 0)
                        <tr>
                            <td colspan="4" style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9;">
                                <strong>Permissions:</strong> {{ $role->permissions->pluck('name')->join(', ') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@endif