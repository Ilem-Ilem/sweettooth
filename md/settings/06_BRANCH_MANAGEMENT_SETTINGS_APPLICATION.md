# 06_BRANCH_MANAGEMENT_SETTINGS_APPLICATION.md

## Branch Management Settings Application

### Current Issues with Branch Settings Usage

#### 1. Branch Creation and Management
**Problem Areas:**
- Branch creation doesn't respect global settings
- Branch edit permissions not enforced
- Warehouse creation not checking global settings

**Current Issues:**
```php
// app/Livewire/BranchDashboard/Branches/Create.php
// Line 45: Should check warehouse_add setting
public function create()
{
    $canAddWarehouse = Settings::branchManagement('warehouse_add', false);
    if (!$canAddWarehouse && !is_super_admin()) {
        session()->flash('error', 'Warehouse creation is disabled. Contact system administrator.');
        return;
    }
    
    // Branch creation logic...
}

// app/Livewire/BranchDashboard/Branches/Edit.php
// Line 32: Should check branch_edit setting
public function mount($branchId)
{
    $canEditBranch = Settings::branchManagement('branch_edit', false);
    if (!$canEditBranch && !is_super_admin()) {
        abort(403, 'Branch editing is disabled');
    }
    
    // Edit logic...
}
```

**Required Changes:**
```php
// Branch permission checking trait
// app/Traits/BranchPermissionTrait.php
trait BranchPermissionTrait
{
    protected function canCreateWarehouse(): bool
    {
        return Settings::branchManagement('warehouse_add', false) || is_super_admin();
    }
    
    protected function canEditBranch(): bool
    {
        return Settings::branchManagement('branch_edit', false) || is_super_admin();
    }
    
    protected function canDeleteBranch(): bool
    {
        return Settings::branchManagement('branch_delete', false) || is_super_admin();
    }
    
    protected function canAssignBranchAdmin(): bool
    {
        return Settings::branchManagement('branch_admin_assign', false) || is_super_admin();
    }
    
    protected function canManageBranchHours(): bool
    {
        return Settings::branchManagement('branch_hours', false) || is_super_admin();
    }
    
    protected function canDoInterBranchTransfer(): bool
    {
        return Settings::branchManagement('inter_branch_transfer', false) || is_super_admin();
    }
}
```

#### 2. Inter-Branch Transfers
**Problem Areas:**
- Stock transfers don't check permissions
- Transfer approval workflow not enforced
- Central warehouse settings not applied

**Required Changes:**
```php
// app/Livewire/BranchDashboard/Inventory/ItemDispatches.php
// Line 305: Add permission check
public function createInterBranchTransfer($fromBranch, $toBranch, $items)
{
    if (!$this->canDoInterBranchTransfer()) {
        session()->flash('error', 'Inter-branch transfers are disabled. Contact system administrator.');
        return;
    }
    
    // Check if destination is central warehouse
    $centralWarehouseEnabled = Settings::branchManagement('central_warehouse', false);
    if ($centralWarehouseEnabled && !$this->isCentralWarehouse($toBranch)) {
        session()->flash('error', 'Transfers must go through central warehouse when central warehouse is enabled.');
        return;
    }
    
    // Transfer logic...
}

private function isCentralWarehouse($branchId): bool
{
    $branch = Branch::find($branchId);
    return $branch?->is_central_warehouse ?? false;
}
```

#### 3. Branch Admin Assignment
**Problem Areas:**
- Admin assignment not checking permissions
- Role assignment not enforcing branch management settings
- Admin privileges not properly scoped

**Required Changes:**
```php
// app/Livewire/BranchDashboard/EmployeeModule/RolePermission/AssignRole.php
// Add branch admin permission check
public function assignBranchAdmin($employeeId, $branchId)
{
    if (!$this->canAssignBranchAdmin()) {
        session()->flash('error', 'Branch admin assignment is disabled. Contact system administrator.');
        return;
    }
    
    $employee = Employee::find($employeeId);
    $branch = Branch::find($branchId);
    
    // Assign branch admin role
    $employee->assignRole('Branch Admin');
    
    // Set branch scope for admin privileges
    $employee->update([
        'assigned_branch_id' => $branchId,
        'is_branch_admin' => true,
        'branch_permissions' => $this->getBranchAdminPermissions()
    ]);
    
    session()->flash('message', 'Branch admin assigned successfully!');
}

private function getBranchAdminPermissions(): array
{
    $permissions = [
        'manage_employees' => Settings::branchManagement('branch_admin_assign', false),
        'manage_inventory' => true, // Basic inventory management
        'view_reports' => true, // Report viewing
    ];
    
    // Add conditional permissions based on global settings
    $permissions['manage_branch_hours'] = Settings::branchManagement('branch_hours', false);
    $permissions['edit_branch_info'] = Settings::branchManagement('branch_edit', false);
    
    return $permissions;
}
```

#### 4. Branch Hours Management
**Problem Areas:**
- Branch hours not respecting global settings
- Hour editing not permission-controlled
- Operating hours not applied consistently

**Required Changes:**
```php
// app/Livewire/BranchDashboard/Branches/ManageHours.php
// Add permission checking
public function mount()
{
    if (!$this->canManageBranchHours() && !is_super_admin()) {
        abort(403, 'Branch hours management is disabled');
    }
    
    $this->loadBranchHours();
}

public function updateHours($hoursData)
{
    if (!$this->canManageBranchHours()) {
        session()->flash('error', 'You do not have permission to manage branch hours.');
        return;
    }
    
    $branchId = current_branch_id();
    $branch = Branch::find($branchId);
    
    $branch->update([
        'operating_hours' => $hoursData,
        'hours_updated_by' => auth()->id(),
        'hours_updated_at' => now()
    ]);
    
    session()->flash('message', 'Branch hours updated successfully!');
}

// Apply branch hours in various modules
private function isBranchOpen($branchId = null): bool
{
    $branchId = $branchId ?? current_branch_id();
    $branch = Branch::find($branchId);
    
    if (!$branch || !$branch->operating_hours) {
        return true; // Default to open if no hours set
    }
    
    $currentTime = now()->format('H:i');
    $currentDay = now()->format('l'); // Monday, Tuesday, etc.
    
    $todayHours = $branch->operating_hours[$currentDay] ?? null;
    
    if (!$todayHours || !$todayHours['is_open']) {
        return false;
    }
    
    return $currentTime >= $todayHours['open_time'] && $currentTime <= $todayHours['close_time'];
}
```

#### 5. SaaS Tenant Settings
**Problem Areas:**
- Multi-tenant features not controlled by settings
- Tenant isolation not properly enforced
- SaaS features not properly gated

**Required Changes:**
```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    $saasTenantEnabled = Settings::branchManagement('saas_tenant', false);
    
    if ($saasTenantEnabled) {
        // Apply tenant isolation
        $this->applyTenantIsolation();
        
        // Enable SaaS-specific features
        $this->enableSaaSFeatures();
    }
    
    // Apply branch-wide settings
    $this->applyBranchSettings();
}

private function applyTenantIsolation(): void
{
    // Ensure tenant isolation in queries
    if (auth()->check() && !is_super_admin()) {
        $tenantId = auth()->user()->tenant_id;
        
        // Apply tenant scoping to models
        Branch::addGlobalScope('tenant', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        });
    }
}

private function enableSaaSFeatures(): void
{
    // Enable multi-tenant specific features
    config(['tenancy.enabled' => true]);
    config(['tenancy.database' => true]);
    
    // Enable tenant-specific routes
    Route::pattern('tenant', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
}
```

### Implementation Priority
1. **Permission Checks** - High (security)
2. **Inter-Branch Transfers** - High (business logic)
3. **Branch Hours** - Medium (operational)
4. **Admin Assignment** - Medium (role management)
5. **SaaS Features** - Low (platform-specific)

### Files to Update
- `app/Livewire/BranchDashboard/Branches/Create.php`
- `app/Livewire/BranchDashboard/Branches/Edit.php`
- `app/Livewire/BranchDashboard/Inventory/ItemDispatches.php`
- `app/Livewire/BranchDashboard/EmployeeModule/RolePermission/AssignRole.php`
- `app/Providers/AppServiceProvider.php`
- All branch-related components

### Testing Requirements
- Test branch creation with warehouse settings disabled
- Verify inter-branch transfer permissions
- Test branch admin assignment restrictions
- Validate branch hours management permissions
- Test SaaS tenant isolation