# Audit Management Dashboard Implementation

**Created:** November 23, 2025  
**Status:** ✅ Complete  
**Location:** `Livewire/BranchDashboard/AuditManagement/`

---

## Overview

The Audit Management Dashboard is a comprehensive interface for viewing and managing audit logs and approval requests within the branch dashboard. It provides full visibility into all system actions, sensitive operations, and approval workflows with light/dark mode support.

### Key Features

- **Two-Tab Interface:** Audit Logs & Approval Requests
- **Advanced Filtering:** Search, action, status, and date range filters
- **Approval Management:** Approve/Reject pending requests directly from dashboard
- **Light & Dark Mode:** Full theme support using Tailwind CSS
- **Responsive Design:** Works seamlessly on desktop and mobile
- **Real-time Updates:** Auto-refresh every 120 seconds
- **Sorting:** Click column headers to sort (Audit Logs tab)

---

## File Structure

```
app/Livewire/BranchDashboard/AuditManagement/
└── Index.php                          (Component logic)

resources/views/livewire/branch-dashboard/audit-management/
└── index.blade.php                    (View template)
```

---

## Component: `AuditManagement\Index`

### Properties

```php
public $branchId;           // Current branch ID
public $tab = 'logs';       // Active tab: 'logs' or 'approvals'
public $search = '';        // Search query
public $filterAction = '';  // Filter by action type
public $filterStatus = '';  // Filter by status
public $filterDateFrom = '';// From date filter
public $filterDateTo = '';  // To date filter
public $sortBy = 'logged_at';    // Sort column
public $sortDirection = 'desc';  // Sort direction
```

### Methods

#### `mount($b_id)`
Initialize component with branch ID.

```php
public function mount($b_id)
{
    $this->branchId = $b_id;
}
```

#### `updatingSearch()` / `updatingFilter*()` / `updatingSort*()`
Auto-reset pagination when filters change.

#### `sort($column)`
Toggle sort direction or change sort column.

```php
public function sort($column)
{
    if ($this->sortBy === $column) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortBy = $column;
        $this->sortDirection = 'desc';
    }
}
```

#### `approveRequest($requestId)`
Approve a pending approval request.

```php
public function approveRequest($requestId)
{
    $request = ApprovalRequest::find($requestId);
    if ($request && $request->status === 'pending') {
        $request->approve(auth()->user() ?? auth('employees')->user());
        $this->dispatch('toast', message: 'Request approved', type: 'success');
    }
}
```

#### `rejectRequest($requestId)`
Reject a pending approval request.

```php
public function rejectRequest($requestId)
{
    $request = ApprovalRequest::find($requestId);
    if ($request && $request->status === 'pending') {
        $request->reject(
            auth()->user() ?? auth('employees')->user(),
            'Rejected from audit dashboard'
        );
        $this->dispatch('toast', message: 'Request rejected', type: 'info');
    }
}
```

#### `clearFilters()`
Reset all filters to default values.

```php
public function clearFilters()
{
    $this->search = '';
    $this->filterAction = '';
    $this->filterStatus = '';
    $this->filterDateFrom = '';
    $this->filterDateTo = '';
    $this->resetPage();
}
```

#### `render()`
Build query and return appropriate view based on active tab.

---

## View: `audit-management/index.blade.php`

### Layout Structure

```
├── Breadcrumb Navigation
├── Header Section (with Refresh button)
├── Tab Navigation (Logs / Approvals)
├── Filter Bar
│   ├── Search Input
│   ├── Action Filter
│   ├── Status Filter
│   ├── Date Range Filters
│   └── Clear Filters Button
└── Content Area
    ├── Audit Logs Table (or)
    └── Approval Requests Table + Stats Cards
```

### Styling Details

#### Colors
- **Headers:** Indigo gradient (`from-indigo-600 to-indigo-700`)
- **Success/Green:** Approval states
- **Warning/Yellow:** Pending states
- **Error/Red:** Rejected/critical states
- **Info/Purple:** Info states

#### Dark Mode Support
All elements use `dark:` prefix for dark mode:
```blade
<!-- Example -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
```

### Audit Logs Tab

Shows all audit log entries with:
- **Timestamp** (sortable)
- **Action** (with badge)
- **Actor** (who performed action)
- **Target** (what was affected)
- **Status** (pending/completed/rejected)
- **Description** (action details)

#### Status Badges
```
⏳ Pending    - Yellow badge
✓ Completed  - Green badge
✗ Rejected   - Red badge
```

### Approval Requests Tab

Shows approval workflow with:
- **Summary Cards** (Pending/Approved/Rejected counts)
- **Request Table** with columns:
  - Requested (timestamp)
  - Action (type)
  - Requester (who requested)
  - Reason (why)
  - Status (pending/approved/rejected/executed)
  - Actions (Approve/Reject buttons for pending)

#### Status Indicators
```
⏳ Pending   - Yellow badge (actionable)
✓ Approved  - Green badge
✗ Rejected  - Red badge
→ Executed  - Blue badge
```

---

## Integration Points

### Route Registration

```php
// In routes/branch-route.php
Route::prefix('audit')->name('audit.')->group(function () {
    Route::get('/', \App\Livewire\BranchDashboard\AuditManagement\Index::class)->name('index');
});
```

### Navigation Menu

Added to "Organization" section in sidebar:
```blade
<flux:navlist.item icon="document-text" 
    :href="branch_route('branch-dashboard.audit.index')"
    :current="request()->routeIs('branch-dashboard.audit.*')" wire:navigate>
    {{ __('Audit Management') }}
</flux:navlist.item>
```

### Access URL
```
/branch-dashboard/audit?b_id={branchId}
```

---

## Styling & Theme Support

### Light Mode (Default)
- White backgrounds (`bg-white`)
- Dark text (`text-gray-900`)
- Gray borders (`border-gray-200`)

### Dark Mode
- Dark gray backgrounds (`dark:bg-gray-800`)
- Light text (`dark:text-white`)
- Darker borders (`dark:border-gray-700`)

### Gradient Headers
- Light: Blue-to-white hover effects
- Dark: Darker shade with opacity

### Cards & Sections
```blade
<!-- Standard card styling -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border dark:border-gray-700">
    <div class="px-4 py-3 text-gray-900 dark:text-white">
        Content here
    </div>
</div>
```

### Tables
- Headers: `bg-gray-100 dark:bg-gray-700`
- Rows: Hover state `hover:bg-gray-50 dark:hover:bg-gray-700/50`
- Alternating row colors via borders

### Buttons
- Primary: Indigo (`bg-indigo-600 hover:bg-indigo-700`)
- Success: Green (`bg-green-600 hover:bg-green-700`)
- Danger: Red (`bg-red-600 hover:bg-red-700`)
- Secondary: Gray/White with borders

---

## Filter & Search Usage

### Search
- Searches in:
  - Audit Logs: `description` and `action` columns
  - Approval Requests: `reason` and `action` columns
- **Debounced:** 500ms delay before filtering
- **Live:** Updates as you type

### Action Filter
- Dropdown of all available actions
- Supports custom action names
- Example values: `create`, `update`, `delete`, `approve`, etc.

### Status Filter
**Audit Logs Statuses:**
- `pending` - Awaiting approval
- `completed` - Action completed
- `rejected` - Action rejected

**Approval Requests Statuses:**
- `pending` - Awaiting approval
- `approved` - Approved by supervisor
- `rejected` - Rejected by supervisor
- `executed` - Action was executed

### Date Range Filter
- **From Date:** Filter entries after this date
- **To Date:** Filter entries before this date
- Both are optional
- Format: YYYY-MM-DD

---

## Permissions & Access Control

### Current Implementation
**No RBA (Role-Based Access) - Open to All**

The dashboard is currently accessible to all authenticated users with branch access.

### Future RBA Considerations
To add role-based restrictions, add to `mount()`:

```php
public function mount($b_id)
{
    $this->branchId = $b_id;
    
    // Future: Role-based access
    if (!auth('employees')->user()?->hasPermission('view_audit_logs')) {
        abort(403, 'Unauthorized');
    }
}
```

---

## Real-time Features

### Auto-Refresh
```blade
<div class="p-3 space-y-4" wire:poll.120s="$refresh">
```
- Refreshes every 120 seconds
- Maintains filter and pagination state
- Can be adjusted via `wire:poll.{seconds}s`

### Live Filtering
- All filters use `wire:model.live` or `.live.debounce`
- Pagination resets when filters change
- Smooth loading transitions

### Loading State
Shows "Updating..." message when Livewire is processing:
```blade
<div wire:loading class="...">
    Updating...
</div>
```

---

## Approval Workflow Integration

### From Audit Dashboard

1. **View Pending Requests**
   - Switch to "Approval Requests" tab
   - See all pending approvals with counts

2. **Approve Request**
   - Click "Approve" button
   - Automatically records approver as current user
   - Toast notification confirms action

3. **Reject Request**
   - Click "Reject" button
   - Records rejection reason
   - Updates status immediately

### Connected Models
- Uses `ApprovalRequest::approve()` method
- Uses `ApprovalRequest::reject()` method
- Both methods maintain audit trail

---

## Responsive Design

### Breakpoints
- **Mobile (< 768px):** Single column, compact headers
- **Tablet (768px - 1024px):** Simplified layout
- **Desktop (> 1024px):** Full multi-column layout

### Mobile Considerations
- Filters stack vertically
- Tables scroll horizontally
- Buttons stack for approval actions
- Badges display on separate lines if needed

---

## Performance Optimization

### Pagination
- **Per Page:** 15 items (configurable)
- **Theme:** Tailwind
- **Lazy Pagination:** Uses Livewire's built-in pagination

### Queries
- Filters applied before pagination
- Date filtering uses `whereDate()` for performance
- Distinct queries for action/status dropdowns

### Optimization Tips
```php
// For large datasets, consider:
// 1. Add database indexes:
//    - audit_logs: (action, status, logged_at)
//    - approval_requests: (status, action, created_at)
//
// 2. Archive old logs monthly
// 3. Use eager loading for relationships
```

---

## Troubleshooting

### Filter Not Working
**Problem:** Dropdown shows no options
**Solution:** Check if data exists in database
```php
// Verify actions exist:
AuditLog::distinct('action')->pluck('action');
ApprovalRequest::distinct('action')->pluck('action');
```

### Approval Buttons Not Appearing
**Problem:** No action buttons for pending requests
**Solution:** Verify request status is 'pending'
```php
ApprovalRequest::where('status', 'pending')->get();
```

### Dark Mode Not Applying
**Problem:** Styles still showing light mode in dark mode
**Solution:** Check `<html class="dark">` in layout
```blade
<!-- resources/views/components/layouts/app/branch-dashboard.blade.php -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
```

### Auto-refresh Not Working
**Problem:** Dashboard doesn't update automatically
**Solution:** Check Livewire JavaScript bundle is loaded
```blade
@fluxScripts
```

---

## Examples

### Access Audit Dashboard
```
https://yourdomain.com/branch-dashboard/audit?b_id=1
```

### Filter by Action
1. Select "create" from Action dropdown
2. Dashboard shows only creation logs

### Find Pending Approvals
1. Switch to "Approval Requests" tab
2. Status filter defaults to showing all
3. Click "Pending" count card to see summary

### Sort Audit Logs
1. Click "Timestamp" header
2. First click: sort newest first (desc)
3. Second click: sort oldest first (asc)
4. Arrow indicator shows current direction

---

## Maintenance

### Log Cleanup
To prevent database bloat, archive old logs:

```php
// Schedule in console kernel
$schedule->daily(function () {
    // Keep last 90 days, archive older
    AuditLog::where('logged_at', '<', now()->subDays(90))
        ->delete();
});
```

### Performance Monitoring
Monitor these metrics:
- Query time: Should be < 500ms
- Page load: Should be < 2s
- Memory: Watch pagination with large datasets

### Updates
When updating component:
1. Update both `.php` file and `.blade.php`
2. Test in both light and dark modes
3. Verify filters still work
4. Check mobile responsiveness

---

## Future Enhancements

### Planned Features
- [ ] Export audit logs (CSV/PDF)
- [ ] Audit log analytics & charts
- [ ] Advanced search with operators
- [ ] User activity heatmaps
- [ ] Audit trail visualization
- [ ] Integration with notifications
- [ ] Compliance report generation
- [ ] Bulk approval actions

### Suggested Improvements
- Add avatar for actor display
- Show IP address for audit entries
- Add audit log comparison view
- Create audit report templates
- Add webhook notifications for approvals

---

## Related Documentation

- **Auditflow.md** - Complete audit system documentation
- **Auditflow-QuickRef.md** - Quick reference for common patterns
- **AUDIT_SYSTEM_MIGRATION.md** - System architecture & migration guide
- **IMPLEMENTATION_CHECKLIST.md** - Phase tracking

---

## Support

For issues or questions:
1. Check Auditflow.md for system details
2. Review component docblocks
3. Test filters with sample data
4. Verify database has required tables

---

**Last Updated:** November 23, 2025  
**Status:** ✅ Production Ready  
**Theme Support:** ✅ Light & Dark Mode  
**Responsive:** ✅ Mobile-Friendly
