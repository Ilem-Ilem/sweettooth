# 02 - Export Implementation Templates & Code Patterns

## Basic Export Method Template

```php
/**
 * Export selected items as Excel/PDF/CSV
 */
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No items selected for export.');
        return;
    }

    // Fetch selected items with relationships
    $items = YourModel::whereIn('id', $this->selectedIds)
        ->with('relationships')
        ->get();

    // Call the unified export method from Exportable trait
    $this->export(
        filename: 'export_name_' . date('Y-m-d_His'),
        data: $items,
        views: 'exports.your_template',
        format: 'excel',  // 'pdf', 'excel', or 'both'
        queue: false,     // true if items.count() > 500
        exportOptions: [
            'paper' => 'A4',
            'orientation' => 'portrait',
            'include_timestamp' => true,
        ]
    );
}
```

## Sales Export Template

**File**: `app/Livewire/BranchDashboard/Sales/MySales.php`

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No sales selected.');
        return;
    }

    $sales = Sale::whereIn('id', $this->selectedIds)
        ->with(['customer', 'branch', 'items', 'payments'])
        ->get()
        ->map(fn($sale) => [
            'id' => $sale->id,
            'date' => $sale->sale_date->format('Y-m-d'),
            'time' => $sale->sale_date->format('H:i:s'),
            'customer' => $sale->customer?->name ?? 'Walk-in',
            'branch' => $sale->branch->name,
            'items_count' => $sale->items->count(),
            'subtotal' => $sale->subtotal,
            'tax' => $sale->tax,
            'total' => $sale->total,
            'status' => $sale->status,
            'payment_method' => $sale->payments->first()?->method,
        ]);

    $this->export(
        'sales_export_' . date('Y-m-d'),
        $sales,
        'exports.sales.transactions',
        'excel',
        $sales->count() > 500
    );
}
```

**Blade View**: `resources/views/exports/sales/transactions.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Customer</th>
                <th>Branch</th>
                <th>Items</th>
                <th>Subtotal</th>
                <th>Tax</th>
                <th>Total</th>
                <th>Status</th>
                <th>Payment Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $sale)
                <tr>
                    <td>{{ $sale['id'] }}</td>
                    <td>{{ $sale['date'] }}</td>
                    <td>{{ $sale['time'] }}</td>
                    <td>{{ $sale['customer'] }}</td>
                    <td>{{ $sale['branch'] }}</td>
                    <td>{{ $sale['items_count'] }}</td>
                    <td>{{ number_format($sale['subtotal'], 2) }}</td>
                    <td>{{ number_format($sale['tax'], 2) }}</td>
                    <td><strong>{{ number_format($sale['total'], 2) }}</strong></td>
                    <td>{{ ucfirst($sale['status']) }}</td>
                    <td>{{ $sale['payment_method'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif;">
        <h2 style="text-align: center; margin-bottom: 20px;">Sales Report</h2>
        <p style="text-align: center; color: #666; font-size: 12px;">
            Generated on {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background-color: #2C3E50; color: white;">
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Sale ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Customer</th>
                    <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $sale)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['id'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['date'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $sale['customer'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">{{ number_format($sale['total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
```

## Accounting Export Template

**File**: `app/Livewire/BranchDashboard/Accounting/Index.php`

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No records selected.');
        return;
    }

    $records = JournalEntry::whereIn('id', $this->selectedIds)
        ->with(['account', 'category'])
        ->get()
        ->map(fn($entry) => [
            'date' => $entry->entry_date->format('Y-m-d'),
            'account' => $entry->account->name,
            'description' => $entry->description,
            'debit' => $entry->debit,
            'credit' => $entry->credit,
            'category' => $entry->category->name,
            'reference' => $entry->reference_number,
            'status' => $entry->status,
        ]);

    $this->styledExport(
        'accounting_export_' . date('Y-m-d'),
        $records,
        [
            'views' => [
                'pdf' => 'exports.accounting.journal',
                'excel' => 'exports.accounting.journal'
            ],
            'format' => 'both',
            'queue' => $records->count() > 500,
            'orientation' => 'landscape',
            'paper' => 'A4',
        ]
    );
}
```

**Blade View**: `resources/views/exports/accounting/journal.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
                <th>Date</th>
                <th>Account</th>
                <th>Description</th>
                <th style="text-align: right;">Debit</th>
                <th style="text-align: right;">Credit</th>
                <th>Category</th>
                <th>Reference</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDebit = 0; $totalCredit = 0; @endphp
            @foreach($data as $entry)
                <tr>
                    <td>{{ $entry['date'] }}</td>
                    <td>{{ $entry['account'] }}</td>
                    <td>{{ $entry['description'] }}</td>
                    <td style="text-align: right;">{{ number_format($entry['debit'], 2) }}</td>
                    <td style="text-align: right;">{{ number_format($entry['credit'], 2) }}</td>
                    <td>{{ $entry['category'] }}</td>
                    <td>{{ $entry['reference'] }}</td>
                    <td>{{ ucfirst($entry['status']) }}</td>
                </tr>
                @php $totalDebit += $entry['debit']; $totalCredit += $entry['credit']; @endphp
            @endforeach
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="3">TOTALS</td>
                <td style="text-align: right;">{{ number_format($totalDebit, 2) }}</td>
                <td style="text-align: right;">{{ number_format($totalCredit, 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif; font-size: 10px;">
        <h2 style="text-align: center; margin: 0 0 5px 0; font-size: 14px;">Journal Entries Report</h2>
        <p style="text-align: center; margin: 0 0 15px 0; color: #666; font-size: 10px;">
            Generated: {{ now()->format('Y-m-d H:i:s') }} | Page 1
        </p>
        
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #2C3E50; color: white;">
                    <th style="border: 1px solid #ccc; padding: 4px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ccc; padding: 4px; text-align: left;">Account</th>
                    <th style="border: 1px solid #ccc; padding: 4px; text-align: left;">Description</th>
                    <th style="border: 1px solid #ccc; padding: 4px; text-align: right;">Debit</th>
                    <th style="border: 1px solid #ccc; padding: 4px; text-align: right;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @php $totalDebit = 0; $totalCredit = 0; @endphp
                @foreach($data as $entry)
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 4px;">{{ $entry['date'] }}</td>
                        <td style="border: 1px solid #ccc; padding: 4px;">{{ $entry['account'] }}</td>
                        <td style="border: 1px solid #ccc; padding: 4px;">{{ substr($entry['description'], 0, 30) }}</td>
                        <td style="border: 1px solid #ccc; padding: 4px; text-align: right;">{{ number_format($entry['debit'], 2) }}</td>
                        <td style="border: 1px solid #ccc; padding: 4px; text-align: right;">{{ number_format($entry['credit'], 2) }}</td>
                    </tr>
                    @php $totalDebit += $entry['debit']; $totalCredit += $entry['credit']; @endphp
                @endforeach
                <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <td colspan="3" style="border: 1px solid #ccc; padding: 4px; text-align: right;">TOTALS:</td>
                    <td style="border: 1px solid #ccc; padding: 4px; text-align: right;">{{ number_format($totalDebit, 2) }}</td>
                    <td style="border: 1px solid #ccc; padding: 4px; text-align: right;">{{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
@endif
```

## Employee/HR Export Template

**File**: `app/Livewire/BranchDashboard/EmployeeModule/Index.php`

```php
protected function exportSelected(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No employees selected.');
        return;
    }

    $employees = Employee::whereIn('id', $this->selectedIds)
        ->with(['department', 'position', 'branch'])
        ->get()
        ->map(fn($emp) => [
            'id' => $emp->employee_code,
            'name' => $emp->first_name . ' ' . $emp->last_name,
            'email' => $emp->email,
            'phone' => $emp->phone,
            'department' => $emp->department?->name,
            'position' => $emp->position?->name,
            'branch' => $emp->branch->name,
            'join_date' => $emp->join_date->format('Y-m-d'),
            'status' => $emp->status,
            'salary' => $emp->salary,
        ]);

    $this->export(
        'employees_export_' . date('Y-m-d'),
        $employees,
        'exports.employees.roster',
        'excel'
    );
}
```

**Blade View**: `resources/views/exports/employees/roster.blade.php`

```blade
@if($forExcel)
    <table>
        <thead>
            <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Department</th>
                <th>Position</th>
                <th>Branch</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $emp)
                <tr>
                    <td>{{ $emp['id'] }}</td>
                    <td>{{ $emp['name'] }}</td>
                    <td>{{ $emp['email'] }}</td>
                    <td>{{ $emp['phone'] }}</td>
                    <td>{{ $emp['department'] }}</td>
                    <td>{{ $emp['position'] }}</td>
                    <td>{{ $emp['branch'] }}</td>
                    <td>{{ $emp['join_date'] }}</td>
                    <td>{{ ucfirst($emp['status']) }}</td>
                    <td style="text-align: right;">{{ number_format($emp['salary'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@elseif($forPdf)
    <div style="font-family: Arial, sans-serif;">
        <h2 style="text-align: center;">Employee Roster</h2>
        <p style="text-align: center; color: #666; font-size: 12px;">
            Generated: {{ now()->format('Y-m-d H:i:s') }}
        </p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background-color: #2C3E50; color: white;">
                    <th style="border: 1px solid #ddd; padding: 8px;">ID</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Department</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Position</th>
                    <th style="border: 1px solid #ddd; padding: 8px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $emp)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $emp['id'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $emp['name'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $emp['department'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">{{ $emp['position'] }}</td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <span style="padding: 2px 6px; background-color: {{ $emp['status'] == 'active' ? '#d4edda' : '#f8d7da' }}; border-radius: 3px;">
                                {{ ucfirst($emp['status']) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
```

## Batch Export Helper

For exporting multiple datasets at once:

```php
protected function batchExportAllData(): void
{
    $configs = [
        [
            'filename' => 'employees_' . date('Y-m-d'),
            'data' => Employee::all(),
            'view' => 'exports.employees.roster'
        ],
        [
            'filename' => 'departments_' . date('Y-m-d'),
            'data' => Department::all(),
            'view' => 'exports.departments'
        ],
        [
            'filename' => 'roles_' . date('Y-m-d'),
            'data' => Role::all(),
            'view' => 'exports.roles'
        ]
    ];

    $this->batchExport($configs, 'excel', [
        'paper' => 'A4',
        'orientation' => 'portrait'
    ]);
}
```

## Map and Transform Before Export

For complex data transformations:

```php
protected function exportWithMapping(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No items selected.');
        return;
    }

    $mapping = [
        'ID' => fn($item) => $item->id,
        'Name' => fn($item) => $item->name,
        'Created' => fn($item) => $item->created_at->format('Y-m-d'),
        'Status' => fn($item) => strtoupper($item->status),
        'Total' => fn($item) => number_format($item->total, 2),
    ];

    $items = YourModel::whereIn('id', $this->selectedIds)->get();

    $this->mapAndExport(
        'export_' . date('Y-m-d'),
        $items,
        'exports.generic_table',
        $mapping,
        'excel'
    );
}
```

## CSV Export (Simple Alternative)

For lightweight CSV exports without queue:

```php
protected function exportAsCSV(): void
{
    if (empty($this->selectedIds)) {
        session()->flash('info', 'No items selected.');
        return;
    }

    $items = YourModel::whereIn('id', $this->selectedIds)->get();

    return response()->streamDownload(function () use ($items) {
        $csv = fopen('php://output', 'w');
        
        // Headers
        fputcsv($csv, ['ID', 'Name', 'Value', 'Date']);
        
        // Data
        foreach ($items as $item) {
            fputcsv($csv, [
                $item->id,
                $item->name,
                $item->value,
                $item->created_at->format('Y-m-d'),
            ]);
        }
        
        fclose($csv);
    }, 'export_' . date('Y-m-d_His') . '.csv');
}
```

## Error Handling Template

```php
protected function exportSelected(): void
{
    try {
        if (empty($this->selectedIds)) {
            session()->flash('info', 'No items selected for export.');
            return;
        }

        $items = YourModel::whereIn('id', $this->selectedIds)->get();

        if ($items->isEmpty()) {
            session()->flash('warning', 'Selected items could not be found.');
            return;
        }

        $this->export(
            'export_' . date('Y-m-d'),
            $items,
            'exports.your_template',
            'excel'
        );

    } catch (\Exception $e) {
        \Log::error('Export failed: ' . $e->getMessage());
        session()->flash('error', 'Export failed: ' . $e->getMessage());
    }
}
```
