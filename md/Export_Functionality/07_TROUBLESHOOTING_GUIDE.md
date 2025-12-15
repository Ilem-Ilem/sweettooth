# Export Functionality - Troubleshooting Guide

## Common Issues & Solutions

### Issue 1: "Call to undefined method exportPDF"

**Error:**
```
Call to undefined method App\Livewire\...\exportPDF()
```

**Cause:** Trait not imported in component

**Solution:**
```php
use App\Traits\Exportable;

class YourComponent extends BaseComponent
{
    use Exportable;  // ← Add this
}
```

---

### Issue 2: "View not found"

**Error:**
```
View [exports.template-name] not found.
```

**Cause:** Template path incorrect or file doesn't exist

**Solution:**
1. Check path matches file location
   ```
   exports.inventory.stocks-pdf
   → resources/views/exports/inventory/stocks-pdf.blade.php
   ```

2. Verify folder exists
   ```
   /resources/views/exports/inventory/
   ```

3. Verify file naming
   - Use hyphens: `stocks-pdf.blade.php` ✅
   - Not underscores: `stocks_pdf.blade.php` ❌

**Fix:**
```php
// Component call
'exports.inventory.stocks-pdf'  // ✅ Correct

// File location
resources/views/exports/inventory/stocks-pdf.blade.php  // ✅ Correct
```

---

### Issue 3: Data not displaying in export

**Symptom:** Export file created but data is empty or shows "No data available"

**Causes:**

1. **Data variable wrong name**
   ```blade
   <!-- ❌ Wrong -->
   @foreach($items as $item)
   
   <!-- ✅ Correct -->
   @foreach($data as $item)
   ```

2. **Data not passed correctly**
   ```php
   // ❌ Wrong
   return $this->export('report', $items, 'exports.template', 'pdf');
   
   // ✅ Correct
   return $this->export(
       'report',
       collect($items),  // Must be Collection
       'exports.template',
       'pdf'
   );
   ```

3. **Empty collection**
   ```php
   // ✅ Add check
   if ($data->isEmpty()) {
       session()->flash('error', 'No data to export');
       return;
   }
   ```

**Fix:**
- Verify data preparation method returns data
- Check collection is not empty before export
- Use `collect()` to ensure Collection type

---

### Issue 4: "Unknown or blank mime type"

**Error:**
```
Unknown or blank mime type passed to stream_download. Guessing application/octet-stream.
```

**Cause:** Missing Content-Type header

**Solution:**
```php
return response()->streamDownload(
    function () use ($csv) { echo $csv; },
    $filename,
    [
        'Content-Type' => 'text/csv',  // ← Add this
        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]
);
```

---

### Issue 5: PDF shows garbled text or missing content

**Cause:** 
- Complex HTML/CSS not supported by DomPDF
- External stylesheets not loaded
- Unicode characters not supported

**Solution:**
```blade
<!-- ✅ Use inline styles -->
<table style="width: 100%; border-collapse: collapse;">
    <thead style="background-color: #2C3E50; color: white;">

<!-- ❌ Avoid external stylesheets -->
<link rel="stylesheet" href="...">

<!-- ✅ Use supported fonts -->
<style>
    body { font-family: 'DejaVu Sans', serif, sans-serif; }
</style>
```

**For Unicode (Naira symbol, etc):**
```php
// Configure DomPDF
$pdf->setOption('isPhpEnabled', true);
$pdf->setOption('isRemoteEnabled', true);

// Or use alternatives in template
₦{{ number_format($value, 2) }}  // ✅ Works
```

---

### Issue 6: Excel export "file appears to be corrupted"

**Cause:** Incorrect HTML structure in template

**Solution:**
```blade
<!-- ✅ Simple table structure -->
<table>
    <thead>
        <tr>
            <th>Header</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Data</td>
        </tr>
    </tbody>
</table>

<!-- ❌ Avoid complex nesting -->
<div><table><div>...
```

**Check:**
- [ ] Valid HTML structure
- [ ] No nested tables
- [ ] Proper thead/tbody
- [ ] Inline styles only

---

### Issue 7: CSV data not escaping correctly

**Symptom:** Commas in data break CSV import

**Cause:** Data not properly escaped

**Solution:**
Use PHP's `fputcsv()` which handles escaping:

```php
$handle = fopen('php://temp', 'r+');

// ✅ Correct - uses fputcsv
foreach ($data as $row) {
    fputcsv($handle, $row);
}

// ❌ Wrong - manual concatenation
$csv = implode(',', $row);
```

---

### Issue 8: Export timeout with large datasets

**Symptom:** Export fails silently or shows 500 error with large data

**Cause:** Request timeout or memory limit exceeded

**Solution:**

1. **Enable queuing:**
   ```php
   return $this->export(
       'large-report',
       $data,
       'exports.template',
       'pdf',
       true  // queue=true
   );
   ```

2. **Increase time limits in config:**
   ```php
   set_time_limit(300);  // 5 minutes
   ini_set('memory_limit', '512M');
   ```

3. **Paginate data:**
   ```php
   $data = Model::paginate(1000)->chunk(100);
   ```

---

### Issue 9: "No route to export method"

**Error:**
```
Route for [wire:click="exportPDF"] not found
```

**Cause:** 
- Method not public
- Method not in component
- Wire directive syntax wrong

**Solution:**
```php
// ✅ Must be public
public function exportPDF()
{
    return $this->export(...);
}

// In view - ✅ Correct syntax
<button wire:click="exportPDF">Export</button>

// ❌ Wrong syntax
<button wire:click="this.exportPDF">Export</button>
<button onclick="exportPDF()">Export</button>
```

---

### Issue 10: Exported file has timestamp but shouldn't

**Solution:**
```php
// Remove timestamp
'report-name'  // ✅ No timestamp

// File will be: report-name.pdf
```

---

### Issue 11: Special characters showing as question marks

**Cause:** Character encoding issue

**Solution:**
```blade
<!-- Ensure charset in template -->
<meta charset="UTF-8">

<!-- In component -->
return $this->export(
    'report-' . now()->format('Y-m-d'),
    $data,
    'exports.template',
    'pdf'
);
```

**In CSV:**
```php
header('Content-Charset=UTF-8');
```

---

### Issue 12: Date formatting inconsistent across exports

**Solution:**
Create helper method in component:

```php
private function formatDate($date)
{
    return $date ? $date->format('Y-m-d') : 'N/A';
}

private function formatDateTime($date)
{
    return $date ? $date->format('Y-m-d H:i') : 'N/A';
}

private function formatCurrency($value)
{
    return '₦' . number_format($value, 2);
}
```

Use consistently in templates:
```blade
{{ $this->formatDate($item->created_at) }}
{{ $this->formatCurrency($item->total) }}
```

---

### Issue 13: Buttons not appearing in view

**Cause:** 
- Export buttons not added to view
- Wrong view file modified

**Solution:**
1. Add buttons to correct view file
2. Use correct wire:click method name

```blade
<button wire:click="exportPDF" class="btn">
    <svg><!-- Icon --></svg>
    Export PDF
</button>
```

---

### Issue 14: "Class not found" error

**Error:**
```
Class 'App\Traits\Exportable' not found
```

**Cause:**
- Wrong namespace
- File not in correct location
- Trait not created

**Solution:**
Verify file exists: `app/Traits/Exportable.php`

```php
// In component
use App\Traits\Exportable;

class YourComponent extends BaseComponent
{
    use Exportable;
}
```

---

### Issue 15: Export button shows loading but nothing happens

**Cause:**
- Export method has error
- No return statement
- Data is empty

**Solution:**
```php
public function exportPDF()
{
    $data = $this->getData();
    
    // ✅ Add validation
    if ($data->isEmpty()) {
        $this->toast()->warning('No data to export')->send();
        return;
    }
    
    // ✅ Must return response
    return $this->export(
        'report',
        $data,
        'exports.template',
        'pdf'
    );
}
```

Check browser console for JavaScript errors.

---

## Debugging Steps

### Step 1: Check Method Exists
```php
// In component, verify method exists
public function exportPDF()
{
    dd('Method called');  // Should see this if working
}
```

### Step 2: Verify Data
```php
public function exportPDF()
{
    $data = $this->getData();
    dd($data);  // Check data structure
}
```

### Step 3: Check Template Path
```php
public function exportPDF()
{
    // Verify template exists
    $view = view('exports.template');
    dd($view);
}
```

### Step 4: Test Download
```php
public function exportPDF()
{
    return response()->download(
        public_path('test.pdf'),
        'test.pdf'
    );
}
```

---

## Performance Issues

### Slow Exports

**Check:**
1. Database query performance
   ```php
   // Use eager loading
   Model::with('relationships')->get();
   ```

2. Template rendering
   ```php
   // Minimize loops and calculations in template
   <!-- ✅ Pre-calculate in component -->
   <!-- ❌ Avoid complex calculations in template -->
   ```

3. Enable query logging
   ```php
   DB::enableQueryLog();
   // ... export code ...
   dd(DB::getQueryLog());
   ```

### Large File Sizes

**Reduce by:**
1. Export only needed fields
2. Remove formatting in large datasets
3. Use CSV instead of PDF for large data
4. Implement pagination/chunking

---

## Testing Checklist

After implementing export:
- [ ] Export button visible
- [ ] Button is clickable
- [ ] File downloads
- [ ] File not corrupted
- [ ] File named correctly
- [ ] Data matches UI
- [ ] Formatting correct
- [ ] No console errors
- [ ] Works on all browsers
- [ ] Performance acceptable

---

## Getting Help

If still stuck:
1. Check component has `use Exportable;`
2. Verify template path and file exists
3. Check data is Collection not array
4. Verify method is public
5. Check browser console for errors
6. Review Laravel logs in `storage/logs/`
7. Test with simple data first

Check audit files for real working examples:
- Inventory: `01_INVENTORY_EXPORT_AUDIT.md`
- Analytics: `02_ANALYTICS_EXPORT_AUDIT.md`
- Sales: `03_SALES_EXPORT_AUDIT.md`
