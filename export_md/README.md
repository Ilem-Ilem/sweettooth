# Export & Bulk Action System Documentation

## Overview

This directory contains comprehensive documentation for the SweetTooth application's export and bulk action functionality. It covers the current state, gaps, and implementation plan for all 31 modules.

## Files in This Directory

### 1. **00_EXPORTS_AND_BULK_ACTIONS_OVERVIEW.md**
High-level overview of the system:
- Current infrastructure and existing implementations
- List of 5 components with defined but unimplemented exports
- List of 26 components completely missing export support
- Priority breakdown by category
- Quick reference to detailed files

**Start here** to understand the current state.

### 2. **01_COMPONENTS_NEEDING_EXPORT_IMPLEMENTATION.md**
Detailed breakdown of each component:
- 5 components with defined but unimplemented exports (Phase 1)
- 26 critical components needing new export functionality
- Organized by priority (Priority 1-7)
- Expected data fields for each export
- Common implementation patterns

**Reference this** when starting work on a specific component.

### 3. **02_EXPORT_IMPLEMENTATION_TEMPLATES.md**
Ready-to-use code templates:
- Basic export method template
- Sales export example (full implementation)
- Accounting export example (with calculations)
- Employee/HR export example
- Batch export helper
- Map and transform patterns
- CSV export alternative
- Error handling template

**Copy-paste these** when implementing new exports.

### 4. **03_BULK_ACTIONS_IMPROVEMENTS.md**
Enhancements and advanced features:
- Status update actions (activate, deactivate, archive)
- Bulk assignment (to users, departments, roles)
- Tag/category assignment
- Role assignment patterns
- Send notifications/emails
- Enhanced BaseComponent code
- Blade template examples
- Confirmation dialogs
- Audit logging patterns
- Permission checks

**Use this** to add advanced bulk actions beyond basic delete.

### 4. **04_IMPLEMENTATION_CHECKLIST.md**
Step-by-step implementation plan:
- Phase 1: Fix 5 incomplete exports (highest priority)
- Phase 2: Add exports to 5 critical modules
- Phase 3: Add exports to 10 medium-priority modules
- Phase 4: Add exports to 11 lower-priority modules
- Code quality checklist
- Testing checklist
- Template creation checklist
- Deployment checklist
- Progress tracking

**Follow this** as your implementation guide.

## Quick Start

### For Implementing a New Export

1. **Check current state** → Read `00_EXPORTS_AND_BULK_ACTIONS_OVERVIEW.md`
2. **Find your component** → Look it up in `01_COMPONENTS_NEEDING_EXPORT_IMPLEMENTATION.md`
3. **Use code templates** → Copy relevant patterns from `02_EXPORT_IMPLEMENTATION_TEMPLATES.md`
4. **Create export method**:
   ```php
   protected function exportSelected(): void
   {
       if (empty($this->selectedIds)) {
           session()->flash('info', 'No items selected.');
           return;
       }

       $items = YourModel::whereIn('id', $this->selectedIds)->get();
       
       $this->export(
           'export_name_' . date('Y-m-d'),
           $items,
           'exports.your_template',
           'excel'
       );
   }
   ```
5. **Create Blade template** → Use format from templates file
6. **Test** → Follow testing checklist in `04_IMPLEMENTATION_CHECKLIST.md`
7. **Track progress** → Update checklist

### For Adding Bulk Actions

1. **Review current patterns** → `03_BULK_ACTIONS_IMPROVEMENTS.md`
2. **Choose action type** (activate, assign, tag, notify, etc.)
3. **Copy code pattern** from templates
4. **Add to bulkActions array** in component
5. **Implement handler method**
6. **Add Blade UI** for action controls
7. **Test** thoroughly

## System Architecture

```
App/Livewire/BaseComponent
├── Handles multi-select
├── Routes bulk actions
└── Exports basic scaffold

Various Components (31 total)
├── 5 with export defined but unimplemented
└── 26 with no export support

App/Traits/Exportable
├── Universal export method
├── Handles PDF, Excel, CSV
├── Queues large datasets
└── Manages file generation

Resources/Views/Exports/
└── Templates for each module type

Jobs/
├── ExportExcelJob
└── ExportPdfJob
```

## Key Components

### Exportable Trait
Location: `app/Traits/Exportable.php`

Provides unified export functionality:
```php
// Single export
$this->export('filename', $data, 'view', 'excel');

// Styled export with options
$this->styledExport('filename', $data, [
    'views' => ['pdf' => 'path', 'excel' => 'path'],
    'format' => 'both',
    'orientation' => 'landscape',
]);

// Batch export
$this->batchExport([...]);

// Map and transform
$this->mapAndExport('filename', $data, 'view', $mapping);
```

### BaseComponent
Location: `app/Livewire/BaseComponent.php`

Foundation for bulk operations:
- Multi-select with select-all
- Bulk action routing
- Default delete and export methods
- Extensible for custom actions

## Implementation Status

### Current (Baseline)
- ✅ Exportable trait: Production-ready
- ✅ BaseComponent: Production-ready
- ✅ Export jobs: Production-ready
- ❌ 5 components: Export defined but not implemented
- ❌ 26 components: No export support

### Target State
- ✅ All 31 components: Full export support
- ✅ Advanced bulk actions: Activate, assign, tag, notify
- ✅ Professional templates: PDF and Excel for each
- ✅ Queuing: Automatic for large datasets
- ✅ Audit logging: All exports tracked
- ✅ Permissions: Export access controlled

## Data Formats

### Excel Exports
- Auto-sizing columns
- Frozen header row
- Alternating row colors
- Professional styling
- Support for >500 rows (queued)

### PDF Exports
- A4 or custom paper size
- Portrait or landscape
- Custom margins
- Professional header/footer
- Support for >500 rows (queued)

### CSV Exports
- Lightweight, immediate download
- Suitable for spreadsheets
- Direct streaming (no queue)

## Performance Considerations

- **Small exports (<500 rows)**: Immediate download
- **Large exports (500+ rows)**: Queued jobs
- **Very large exports (5000+ rows)**: Consider pagination or date filters
- **Memory**: Queue jobs run with 512M limit
- **Files**: Generated in `storage/exports/`

## Security & Permissions

- All exports require authentication
- Can add permission checks before export
- Sensitive data should be masked
- Audit trail logs all exports
- File access should be restricted

## Testing

Each export should be tested for:
1. **Functionality**: Data accuracy
2. **Formatting**: Numbers, dates, currency
3. **Edge cases**: Empty data, special characters, large datasets
4. **Performance**: Execution time, memory usage
5. **User experience**: Error messages, success feedback

## Common Issues & Solutions

### Memory Limit Exceeded
- Solution: Lower queue threshold or increase memory limit
- Set `'queue_threshold' => 300` for sooner queuing

### Slow Exports
- Solution: Add database indexes, optimize queries
- Use select() to limit columns fetched
- Pre-calculate summaries instead of computing in view

### File Not Downloading
- Solution: Check browser console, verify MIME types
- Ensure file path has write permissions
- Check for output buffering in code

### Formatting Issues in Excel
- Solution: Review template Blade syntax
- Test in Excel directly, not preview
- Check for HTML tags in data

### Queued Export Not Processing
- Solution: Ensure queue worker is running
- Check job logs: `storage/logs/laravel.log`
- Verify mail driver configured if notifications used

## Next Steps

1. **Start with Phase 1** (5 components)
   - Implement missing `exportSelected()` methods
   - Create basic Blade templates
   - Test thoroughly

2. **Move to Phase 2** (5 critical modules)
   - Sales, Accounting, Analytics, Production, Daily Produce
   - Implement full export functionality
   - Add queuing support

3. **Complete Phase 3 & 4** (21 remaining components)
   - Parallelized by team members
   - Follow established patterns
   - Maintain consistency

4. **Enhance bulk actions**
   - Add activate/deactivate
   - Add assignment operations
   - Add notifications
   - Add permission checks

## References

- Laravel Excel: `maatwebsite/excel`
- DomPDF: `barryvdh/laravel-dompdf`
- Livewire: `livewire/livewire`
- TallStackUI: UI component library

## Questions?

Refer to the detailed files or check existing implementations in:
- `app/Livewire/BranchDashboard/EmployeeModule/Index.php` (has bulkDelete)
- `app/Services/DocumentExportService.php` (specialized exports)
- `app/Http/Controllers/ExportController.php` (controller-based exports)

---

**Last Updated**: 2026-01-02
**Status**: Baseline assessment complete, ready for implementation
