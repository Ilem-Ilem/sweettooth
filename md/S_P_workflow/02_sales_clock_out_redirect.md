# Sales Clock Out Redirect to Shift Closing

## How It Works

When a sales employee clicks "Clock Out", they are **automatically redirected** to the shift closing page instead of having their shift closed immediately.

## Key File

**File**: `app/Livewire/BranchDashboard/HeaderClockInOut.php`

## Clock Out Logic (Lines 71-136)

```php
public function clockOut()
{
    // Check if this is a sales employee
    $isSalesEmployee = $this->checkIfSalesEmployee();

    // Update shift with clock out time
    $this->currentShift->clock_out = Carbon::now();

    // For sales employees, set to 'active' to trigger shift closing workflow
    // For other employees, complete the shift immediately
    if ($isSalesEmployee && !is_super_admin() && !can_access_all_branches()) {
        // KEEP SHIFT ACTIVE - don't close it yet!
        $this->currentShift->status = 'active';
        $this->currentShift->workflow_state = 'shift_closing';

        // Update metadata with clock out timestamp
        $metadata = $this->currentShift->metadata ?? [];
        $metadata['clock_out_at'] = now()->toIso8601String();
        $this->currentShift->metadata = $metadata;
    } else {
        // Non-sales employees: close immediately
        $this->currentShift->status = 'closed';
        $this->currentShift->workflow_state = 'completed';
    }

    $this->currentShift->save();

    // REDIRECT: Sales employees go to shift closing page
    if ($isSalesEmployee && !is_super_admin() && !can_access_all_branches()) {
        $branchId = $this->b_id ?: current_branch_id();
        $departmentSlug = $this->getEmployeeDepartmentSlug();

        $this->toast()->success("Clocked out! Please complete shift closing.")->send();

        return redirect()->route('branch-dashboard.sales-dashboard.shift-closing.index', [
            'salesDeptSlug' => $departmentSlug,
            'b_id' => $branchId
        ]);
    }
}
```

## Key Points

### 1. Shift Status Remains 'active'
```php
$this->currentShift->status = 'active'; // NOT 'closed'!
```
This keeps the shift "open" until shift closing is completed.

### 2. Workflow State Set to 'shift_closing'
```php
$this->currentShift->workflow_state = 'shift_closing';
```
This tells the system the user needs to complete shift closing.

### 3. Automatic Redirect
```php
return redirect()->route('branch-dashboard.sales-dashboard.shift-closing.index', [...]);
```
User is immediately sent to the shift closing page.

### 4. Sales Employee Detection
```php
protected function checkIfSalesEmployee(): bool
{
    $employee = auth()->user();
    $department = Department::with('category')->find($employee->department_id);
    return strtolower($department->category->name) === 'sales';
}
```
Only employees in departments with category "sales" are redirected.

## Flow Diagram

```
User Clicks "Clock Out"
         ↓
Is Sales Employee? ──No──→ Close shift immediately (status='closed')
         │
        Yes
         ↓
Set workflow_state = 'shift_closing'
Keep status = 'active'
         ↓
Redirect to Shift Closing Page
         ↓
User MUST complete shift closing
         ↓
Only then: status='closed', workflow_state='completed'
```

## Super Admin Bypass

Super admins and users with `can_access_all_branches()` bypass this workflow:
```php
if ($isSalesEmployee && !is_super_admin() && !can_access_all_branches()) {
    // Workflow enforcement
} else {
    // Direct close
}
```
