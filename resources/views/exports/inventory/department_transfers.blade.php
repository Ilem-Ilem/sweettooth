<table>
    <thead>
        <tr>
            <th>Transfer Number</th>
            <th>Status</th>
            <th>From Department</th>
            <th>To Department</th>
            <th>Receiver</th>
            <th>Items</th>
            <th>Notes</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $transfer)
            <tr>
                <td>{{ $transfer->transfer_number }}</td>
                <td>{{ $transfer->status }}</td>
                <td>{{ $transfer->fromDepartment?->name ?? 'N/A' }}</td>
                <td>{{ $transfer->toDepartment?->name ?? 'N/A' }}</td>
                <td>{{ $transfer->receiver?->name ?? 'N/A' }}</td>
                <td>
                    @foreach($transfer->items as $item)
                        {{ $item->item?->name }}: {{ $item->quantity }} {{ $item->unitOfMeasure?->symbol ?? $item->item?->uomSymbol }};
                    @endforeach
                </td>
                <td>{{ $transfer->notes }}</td>
                <td>{{ $transfer->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
