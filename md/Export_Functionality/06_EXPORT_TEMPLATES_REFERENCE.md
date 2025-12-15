# Export Templates Reference

## Template Structure

All templates use conditional rendering based on format:

```blade
@if($forPdf ?? false)
    <!-- PDF-specific content -->
@endif

@if($forExcel ?? false)
    <!-- Excel-specific content -->
@endif

@if($forCsv ?? false)
    <!-- CSV-specific content (usually plain table) -->
@endif
```

---

## PDF Template Format

### Basic Structure
```blade
@if($forPdf ?? false)
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            /* CSS here */
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Report Title</h1>
            <p>Generated: {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        
        <table>
            <!-- Data here -->
        </table>
        
        <div class="footer">
            <p>Report footer</p>
        </div>
    </body>
    </html>
@endif
```

### CSS Guidelines for PDF
- Use inline styles (preferred)
- Avoid external stylesheets
- Use standard CSS (DomPDF may not support all modern CSS)
- Include font families explicitly
- Set explicit widths on columns
- Use page-break-after for multi-page reports

### Example: Analytics PDF

```blade
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
                font-size: 10pt;
                color: #333;
            }
            
            .header {
                margin-bottom: 30px;
                padding-bottom: 15px;
                border-bottom: 3px solid #2C3E50;
            }
            
            .header h1 {
                font-size: 24pt;
                color: #2C3E50;
                margin-bottom: 5px;
            }
            
            .meta {
                font-size: 8pt;
                color: #666;
                margin-top: 10px;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            
            thead {
                background-color: #2C3E50;
                color: white;
            }
            
            th {
                padding: 12px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #ddd;
                font-size: 10pt;
            }
            
            td {
                padding: 10px;
                border: 1px solid #ddd;
                font-size: 9pt;
            }
            
            tbody tr:nth-child(odd) {
                background-color: #f9f9f9;
            }
            
            .section-title {
                font-size: 14pt;
                font-weight: bold;
                color: #2C3E50;
                margin-top: 20px;
                margin-bottom: 10px;
                border-bottom: 2px solid #2C3E50;
                padding-bottom: 5px;
            }
            
            .metric {
                display: inline-block;
                width: 45%;
                margin: 10px 2%;
                padding: 15px;
                background-color: #f5f5f5;
                border-left: 4px solid #2C3E50;
            }
            
            .metric-label {
                font-size: 9pt;
                color: #666;
                margin-bottom: 5px;
            }
            
            .metric-value {
                font-size: 16pt;
                font-weight: bold;
                color: #2C3E50;
            }
            
            .page-break {
                page-break-after: always;
            }
            
            .footer {
                margin-top: 50px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                font-size: 8pt;
                color: #999;
            }
            
            .status-good { color: #27ae60; font-weight: bold; }
            .status-warning { color: #f39c12; font-weight: bold; }
            .status-critical { color: #e74c3c; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Analytics Report</h1>
            <div class="meta">
                <p><strong>Period:</strong> {{ $data['period']['from'] ?? 'N/A' }} to {{ $data['period']['to'] ?? 'N/A' }}</p>
                <p><strong>Generated:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                <p><strong>By:</strong> {{ auth()->user()?->name ?? 'System' }}</p>
            </div>
        </div>
        
        <!-- Summary Section -->
        <div class="section-title">Summary Metrics</div>
        <div>
            <div class="metric">
                <div class="metric-label">Total Stock Value</div>
                <div class="metric-value">₦{{ number_format($data['summary']['total_stock_value'] ?? 0, 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Total Items</div>
                <div class="metric-value">{{ $data['summary']['total_items'] ?? 0 }}</div>
            </div>
        </div>
        
        <!-- Details Table -->
        <div class="section-title">Stock Health Status</div>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th style="width: 15%;">Stock Level</th>
                    <th style="width: 15%;">Health %</th>
                    <th style="width: 20%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['stock_health'] ?? [] as $stock)
                <tr>
                    <td>{{ $stock['item_name'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">{{ $stock['stock_level'] ?? 0 }}</td>
                    <td style="text-align: right;">{{ $stock['health_percentage'] ?? 0 }}%</td>
                    <td>
                        @if(($stock['status'] ?? '') === 'good')
                            <span class="status-good">✓ Good</span>
                        @elseif(($stock['status'] ?? '') === 'warning')
                            <span class="status-warning">⚠ Warning</span>
                        @else
                            <span class="status-critical">✗ Critical</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">No data available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="footer">
            <p>This is an automatically generated report. Verify data before distribution.</p>
        </div>
    </body>
    </html>
@endif
```

---

## Excel Template Format

### Basic Structure
```blade
@if($forExcel ?? false)
    <table>
        <thead>
            <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
                <th style="border: 1px solid #ddd; padding: 10px;">Column 1</th>
                <th style="border: 1px solid #ddd; padding: 10px;">Column 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr style="background-color: {{ $loop->odd ? '#f9f9f9' : 'white' }};">
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->field1 }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $row->field2 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### Excel-Specific Notes
- Use inline styles
- Avoid complex HTML
- Keep tables simple
- Use standard colors
- Apply borders to cells
- Use padding for spacing
- Alternate row colors for readability

### Example: Inventory Excel

```blade
@if($forExcel ?? false)
    <table>
        <thead>
            <tr style="background-color: #2C3E50; color: white; font-weight: bold;">
                <th style="border: 1px solid #ddd; padding: 10px; width: 25%;">Item Name</th>
                <th style="border: 1px solid #ddd; padding: 10px; width: 15%;">SKU</th>
                <th style="border: 1px solid #ddd; padding: 10px; width: 15%;">Category</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right; width: 12%;">Available</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right; width: 12%;">Reserved</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right; width: 12%;">Total</th>
                <th style="border: 1px solid #ddd; padding: 10px; width: 10%;">UOM</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right; width: 15%;">Avg Cost</th>
                <th style="border: 1px solid #ddd; padding: 10px; text-align: right; width: 15%;">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $stock)
            <tr style="background-color: {{ $loop->odd ? '#f9f9f9' : 'white' }};">
                <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{{ $stock->item->name ?? 'N/A' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; font-family: monospace;">{{ $stock->item->sku ?? 'N/A' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ ucfirst($stock->item->category ?? 'N/A') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($stock->available_quantity, 2) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($stock->reserved_quantity, 2) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">{{ number_format($stock->total_quantity, 2) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $stock->item->uom ?? 'units' }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ number_format($stock->average_cost, 2) }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">{{ number_format($stock->available_quantity * $stock->average_cost, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="border: 1px solid #ddd; padding: 15px; text-align: center; color: #999;">
                    No data available
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endif
```

---

## CSV Template Format

### Basic Structure
```blade
@if($forCsv ?? false)
    <table>
        <thead>
            <tr>
                <th>Column 1</th>
                <th>Column 2</th>
                <th>Column 3</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->field1 }}</td>
                <td>{{ $row->field2 }}</td>
                <td>{{ $row->field3 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
```

### CSV Generation Notes
- No styling needed (plain table)
- Proper escaping handled by PHP
- Use double quotes for text values
- Handle NULL values gracefully
- Date formatting consistent
- Currency values without symbols

---

## Common Template Patterns

### Pattern: Metrics Grid
```blade
<div style="margin: 20px 0;">
    <div style="display: inline-block; width: 45%; margin: 10px 2%; padding: 15px; background-color: #f5f5f5;">
        <div style="font-size: 8pt; color: #666;">Metric Label</div>
        <div style="font-size: 16pt; font-weight: bold; color: #2C3E50;">Value</div>
    </div>
</div>
```

### Pattern: Status Badge
```blade
<span style="padding: 4px 8px; border-radius: 4px; font-weight: bold;
    background-color: @if($status === 'good') #d4edda @elseif($status === 'warning') #fff3cd @else #f8d7da @endif;">
    {{ ucfirst($status) }}
</span>
```

### Pattern: Currency Formatting
```blade
{{ number_format($value, 2, '.', ',') }}  <!-- 1,234,567.89 -->
₦{{ number_format($value, 2) }}           <!-- ₦1234567.89 -->
```

### Pattern: Conditional Display
```blade
{{ $value ?? 'N/A' }}                      <!-- Show N/A if null -->
{{ $date?->format('Y-m-d') ?? 'N/A' }}     <!-- Format date or show N/A -->
```

---

## Color Scheme Reference

Use consistent colors across templates:

```css
Primary: #2C3E50       /* Dark blue-gray */
Success: #27ae60       /* Green */
Warning: #f39c12       /* Orange -->
Danger:  #e74c3c       /* Red -->
Light:   #f5f5f5       /* Light gray -->
Border:  #ddd          /* Border gray -->
Text:    #333          /* Dark text -->
Muted:   #999          /* Muted text -->
```

---

## Directory Organization

Create templates in organized structure:

```
resources/views/exports/
├── analytics/
│   ├── overall-summary-pdf.blade.php
│   └── overall-summary-excel.blade.php
├── inventory/
│   ├── stocks-pdf.blade.php
│   ├── stocks-excel.blade.php
│   ├── items-pdf.blade.php
│   ├── items-excel.blade.php
│   ├── analytics-pdf.blade.php
│   └── analytics-excel.blade.php
├── sales/
│   ├── analytics-pdf.blade.php
│   ├── analytics-excel.blade.php
│   ├── callbacks-csv.blade.php
│   └── my-sales-csv.blade.php
├── example-pdf.blade.php       (reference)
└── example-excel.blade.php     (reference)
```

---

## Variable Naming Convention

In templates, always expect `$data` as collection:

```blade
<!-- ✅ Correct -->
@foreach($data as $item)
    {{ $item->field }}
@endforeach

<!-- ❌ Avoid -->
@foreach($items as $item)
    {{ $item->field }}
@endforeach
```

Pass formatted data from component:

```php
return $this->export(
    'report-name',
    $this->prepareData(),  // <-- Should return Collection
    'exports.template',
    'pdf'
);
```

---

## Testing Templates

Quick checklist:
- [ ] All variables present in template
- [ ] Null values handled gracefully
- [ ] Numbers formatted correctly
- [ ] Dates in consistent format
- [ ] Currency with naira symbol
- [ ] Tables have proper borders
- [ ] Headers visible
- [ ] No blank pages
- [ ] Content fits page width
- [ ] Special characters displayed correctly
