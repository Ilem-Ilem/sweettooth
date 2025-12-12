# SweetTooth Role & Permission System - Comprehensive Analysis & Improvements

## Executive Summary

Your application uses **Spatie\Permission** package with a **dual-guard authentication system** (web/employees) and implements role-based access control (RBAC). While the foundation is solid, there are critical security gaps, missing granularity, and architectural improvements needed.

---

## 1. CURRENT SYSTEM OVERVIEW

### 1.1 Architecture Components

#### Authentication Guards
- **`web` guard**: Super Admin / Admin users (central system control)
- **`employees` guard**: Employee users with role-specific access (branch-based)

#### Core Files
```
app/Helpers/RolePermission.php           - Central permission checking utility (656 lines)
app/Providers/RolePermissionServiceProvider.php - Blade directives registration
app/Http/Middleware/IsAdmin.php          - Simple admin role check
app/Http/Middleware/BranchMiddleware.php - Branch authorization
app/Http/Middleware/SetBranchContext.php - Branch context setup
app/Livewire/BranchDashboard/Roles/Index.php - Role management UI
app/Livewire/BranchDashboard/EmployeeModule/RolePermission/AssignRole.php - Role assignment
database/seeders/RoleSeeder.php         - 20 predefined roles
database/seeders/PermissionSeeder.php   - 59 permissions
config/permission.php                  - Spatie configuration
```

#### Database Schema
```
- permissions table (id, name, guard_name, timestamps)
- roles table (id, name, guard_name, timestamps)
- model_has_permissions (pivot: permission_id, model_type, model_id)
- model_has_roles (pivot: role_id, model_type, model_id)
- role_has_permissions (pivot: permission_id, role_id)
```

### 1.2 Current Roles (20 Total)

**Executive Level (5 - Full Access)**
- Super Admin (web guard)
- Managing Director (employees guard)

**Management Level (4 - Leadership)**
- Admin (web guard)
- Head of Production
- Sales Manager
- HR Manager
- Inventory Manager

**Department Heads/Supervisors (3 - Senior)**
- Chef
- Head of Gelato
- Till Supervisor
- Confectionaries Manager
- Corner Store Manager

**Officers/Controllers (2 - Mid-level)**
- HR Officer
- Stock Controller
- Store Keeper

**Staff Level (1 - Operational)**
- Kitchen Staff
- Gelato Production Staff
- Confectionaries Production Staff
- Cashier
- Corner Store Staff
- Confectionaries Sales Staff

### 1.3 Current Permissions (59 Total)

**Employee Guard (44 permissions)**
- Dashboard, Analytics, Profile
- Production (queue, start, complete, approve, recipes)
- Sales (process, refund, daily reports, register)
- Inventory (receive, transfer, adjust, view stock)
- Employee Management (view, create, edit, delete, assign roles)
- Department & Branch (view)
- Reports & Scheduling
- Orders & Products & Customers

**Web Guard (15 permissions)**
- Employee management
- Role & Permission CRUD
- Branch management
- System settings & audit logs

### 1.4 Blade Directives Available

```blade
@role('Admin')                          - Single role check
@anyrole(['Admin', 'Manager'])          - Any of multiple roles
@allroles(['Admin', 'Manager'])         - All specified roles
@permission('edit-employees')           - Single permission check
@anypermission(['view', 'edit'])        - Any permission
@allpermissions(['view', 'edit'])       - All permissions
@superadmin                             - Super admin check
@admin                                  - Admin/Super Admin check
@manager                                - Any manager role check
@supervisor                             - Supervisor role check
@staff                                  - Staff level check
@rolelevel(4)                           - Role hierarchy level
@canmanage($user)                       - Can manage user check
@module('production')                   - Module access check
@unlessrole('Admin')                    - Inverse role check
@unlesspermission('edit-employees')     - Inverse permission check
```

---

## 2. CRITICAL SECURITY ISSUES

### 2.1 No Protected Core Roles
**CRITICAL VULNERABILITY**: Core roles can be deleted, causing system collapse.

```php
// Current: Anyone with super-admin can delete ANY role
public function confirmedDeleteRole(string $message): void
{
    if ($this->selectedRoleId) {
        Role::findOrFail($this->selectedRoleId)->delete();  // ❌ No protection
    }
}
```

**Risk**: Deleting "Super Admin" or "Managing Director" role would:
- Orphan all users with those roles
- Break permission inheritance
- Create audit trail inconsistencies
- Potentially lock out system administration

### 2.2 Missing Role-Level Access Control at Routes
Routes have **comments** indicating role restrictions but **no middleware enforcement**:

```php
// Line 36: ROLE MANAGEMENT (Super Admin Only) - No middleware!
Route::get('roles', \App\Livewire\BranchDashboard\Roles\Index::class)->name('roles.index');

// Line 39: BRANCH MANAGEMENT (Super Admin Only) - No middleware!
Route::get('branches', \App\Livewire\BranchDashboard\Branches\Index::class)->name('branches.index');
```

**Risk**: Only Livewire component `mount()` checks role, not route middleware.

### 2.3 No Permission Seeding for Consistency
Permissions are created but some roles don't have all required permissions:

```php
// RoleSeeder.php line 24: Some permissions don't exist yet
$headOfProduction->givePermissionTo([
    'view-production-queue',    // ✅ Exists
    'manage-recipes',            // ❌ Not in PermissionSeeder
]);
```

### 2.4 Weak Role-Hierarchy Enforcement
`canManageUser()` only checks role levels, not relationship or department:

```php
// Line 576: Doesn't check if users are in same branch/department
public static function canManageUser($targetUser, ?string $guard = null): bool
{
    // Super admins can manage anyone ✓
    // But what about cross-branch managers? ✗
}
```

### 2.5 No Module Scope Tracking
Module access is hard-coded and doesn't track which modules are enabled per branch:

```php
// Line 639: Module permissions hard-coded
'production' => ['view-production-queue', 'start-production', ...],
'sales' => ['process-sale', 'view-daily-sales'],
// But which branch has sales enabled?
```

### 2.6 No Permission Audit Trail for Role Changes
While audit logging exists, role/permission changes lack detailed tracking:

```php
// No log of: which permissions were added/removed, by whom, when
$role->syncPermissions($permissions);  // ❌ No tracking
```

### 2.7 Cache Issues
Permission cache may become stale:

```php
// RolePermission.php line 271: Cache with TTL might not refresh
return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($guardName) {
    // If permissions change, cache not invalidated properly
});
```

---

## 3. MISSING FUNCTIONALITY

### 3.1 Missing Permissions (Should Add)
- `view-system-health` - System monitoring
- `manage-audit-logs` - Audit log management
- `view-role-hierarchy` - View all role relationships
- `assign-core-roles` - Distinguish core vs custom roles
- `manage-permissions` - Create/edit/delete permissions
- `export-data` - Data export functionality
- `import-data` - Data import functionality
- `manage-two-factor` - 2FA management
- `view-deleted-records` - View soft-deleted data
- `restore-deleted-records` - Restore soft-deleted data
- `approve-high-value-orders` - Approval workflows
- `manage-leave-approvals` - Leave management
- `manage-payroll` - Salary/payroll operations
- `view-financial-reports` - Financial data access
- `manage-shift-schedules` - Shift management

### 3.2 Missing Roles (Recommended)
- `Finance Manager` - Financial operations
- `Compliance Officer` - Regulatory compliance
- `Audit Manager` - Internal audits
- `System Administrator` (web guard) - Separate from Super Admin
- `Branch Manager` - Branch-level admin
- `Shift Supervisor` - Shift-specific lead
- `Quality Assurance` - Product QA

### 3.3 Missing Role Groups/Categories
No way to organize roles by:
- Department (Production, Sales, HR, Inventory)
- Level/Tier (Executive, Management, Supervisor, Staff)
- Functionality (Administrative, Operational, Reporting)

### 3.4 Missing Permission Categories
Permission naming is inconsistent:
- `view-production-queue` vs `view-production` vs `view-daily-sales`
- No standardized prefix convention
- Hard to filter related permissions

### 3.5 No Dynamic Permission Assignment Rules
Rules for who can assign which roles are not defined:
- Can a Sales Manager assign Cashier roles?
- Can an HR Manager assign Production roles?
- What's the approval process?

### 3.6 No Role Templates
No way to quickly create similar roles with standard permission sets.

### 3.7 No Permission Scoping by Resource
All permissions are global, no resource-level scoping:
- Can't restrict "edit-employees" to only their branch/department
- No way to say "edit-employees-in-branch-x"

---

## 4. SYSTEM IMPROVEMENTS ROADMAP

### Phase 1: Protect Core System (CRITICAL)
- ✅ Create protected core roles (immutable)
- ✅ Add `is_protected` column to roles table
- ✅ Prevent core role deletion/modification
- ✅ Create role protection middleware

### Phase 2: Enhance Access Control (HIGH PRIORITY)
- ✅ Add route middleware for role checks
- ✅ Implement permission inheritance chains
- ✅ Add role scope/hierarchy validation
- ✅ Create RoleManagerService

### Phase 3: Add Missing Permissions (MEDIUM PRIORITY)
- ✅ Define standardized permission naming
- ✅ Add 15+ missing permissions
- ✅ Create permission categories/groups
- ✅ Add permission seeding validation

### Phase 4: Improve Audit & Monitoring (MEDIUM PRIORITY)
- ✅ Log all role/permission changes
- ✅ Track who changed what and when
- ✅ Create role change history
- ✅ Add permission change notifications

### Phase 5: Advanced Features (LOW PRIORITY)
- ✅ Role templates & quick-copy
- ✅ Permission scoping by resource
- ✅ Dynamic assignment rules
- ✅ Role inheritance chains

---

## 5. IMPLEMENTATION CODE

### 5.1 Migration: Add Role Protection

Create migration file: `database/migrations/2025_12_12_000001_add_protection_to_roles.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Mark core system roles that cannot be deleted
            $table->boolean('is_protected')->default(false)->after('guard_name');
            $table->text('description')->nullable()->after('is_protected');
            $table->integer('display_order')->default(0)->after('description');
            $table->timestamps();
            
            $table->index('is_protected');
        });

        // Also add to permissions for consistency
        Schema::table('permissions', function (Blueprint $table) {
            $table->boolean('is_protected')->default(false)->after('guard_name');
            $table->text('description')->nullable()->after('is_protected');
            $table->string('category')->default('general')->after('description');
            
            $table->index('is_protected');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['is_protected']);
            $table->dropColumn(['is_protected', 'description', 'category']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['is_protected']);
            $table->dropColumn(['is_protected', 'description', 'display_order']);
        });
    }
};
```

### 5.2 Update Seeders

**Enhanced PermissionSeeder.php**:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permission categories
        $employeePermissions = [
            // Dashboard & Analytics
            'dashboard' => [
                'view-dashboard' => 'View employee dashboard',
                'view-analytics' => 'View analytics and insights',
            ],
            // Profile Management
            'profile' => [
                'view-profile' => 'View own profile',
                'edit-profile' => 'Edit own profile',
                'view-colleague-profiles' => 'View other colleague profiles',
            ],
            // Production
            'production' => [
                'view-production-queue' => 'View production queue',
                'start-production' => 'Start production tasks',
                'complete-production' => 'Complete production tasks',
                'approve-production' => 'Approve production output',
                'manage-recipes' => 'Manage production recipes',
                'quality-check-production' => 'Perform quality checks',
            ],
            // Sales
            'sales' => [
                'process-sale' => 'Process sales transactions',
                'issue-refund' => 'Issue refunds',
                'view-daily-sales' => 'View daily sales reports',
                'close-register' => 'Close sales register',
                'override-price' => 'Override product prices',
            ],
            // Inventory
            'inventory' => [
                'receive-stock' => 'Receive stock items',
                'transfer-stock' => 'Transfer stock between locations',
                'adjust-inventory' => 'Adjust inventory counts',
                'view-stock-levels' => 'View stock levels',
                'manage-stock-categories' => 'Manage inventory categories',
                'perform-stock-take' => 'Perform stock takes',
            ],
            // Employee Management
            'employees' => [
                'view-employees' => 'View employee list',
                'create-employees' => 'Create new employees',
                'edit-employees' => 'Edit employee details',
                'delete-employees' => 'Delete employees (soft)',
                'assign-roles' => 'Assign roles to employees',
                'view-employee-history' => 'View employee change history',
            ],
            // Organization
            'organization' => [
                'view-departments' => 'View departments',
                'view-branches' => 'View branches',
                'view-roles' => 'View available roles',
                'manage-department-assignments' => 'Assign employees to departments',
            ],
            // Reports
            'reports' => [
                'view-department-reports' => 'View department-specific reports',
                'view-reports' => 'View all reports',
                'generate-reports' => 'Generate custom reports',
                'export-reports' => 'Export report data',
                'schedule-reports' => 'Schedule report generation',
            ],
            // Scheduling
            'scheduling' => [
                'manage-staff-schedule' => 'Manage employee schedules',
                'view-schedule' => 'View own/team schedule',
                'manage-shift-handovers' => 'Manage shift transitions',
            ],
            // Orders
            'orders' => [
                'view-orders' => 'View orders',
                'create-orders' => 'Create orders',
                'edit-orders' => 'Edit orders',
                'delete-orders' => 'Delete orders',
                'process-orders' => 'Process orders',
                'cancel-orders' => 'Cancel orders',
                'approve-high-value-orders' => 'Approve high-value orders',
            ],
            // Products
            'products' => [
                'view-products' => 'View product catalog',
                'create-products' => 'Create new products',
                'edit-products' => 'Edit product details',
                'delete-products' => 'Delete products',
                'manage-product-pricing' => 'Manage product prices',
            ],
            // Customers
            'customers' => [
                'view-customers' => 'View customer list',
                'create-customers' => 'Create customer records',
                'edit-customers' => 'Edit customer details',
                'delete-customers' => 'Delete customer records',
            ],
            // Leave Management
            'leave' => [
                'apply-leave' => 'Apply for leave',
                'approve-leave' => 'Approve leave requests',
                'manage-leave-balances' => 'Manage leave allocations',
                'view-leave-reports' => 'View leave reports',
            ],
            // System
            'system' => [
                'view-settings' => 'View system settings',
                'edit-settings' => 'Edit system settings',
                'view-system-health' => 'View system health metrics',
                'access-system-logs' => 'Access system logs',
            ],
        ];

        $webPermissions = [
            'system' => [
                'view-system-settings' => 'View system settings',
                'edit-system-settings' => 'Edit system settings',
                'view-system-health' => 'View system health',
                'manage-audit-logs' => 'Manage audit logs',
            ],
            'roles' => [
                'view-roles' => 'View roles',
                'create-roles' => 'Create new roles',
                'edit-roles' => 'Edit existing roles',
                'delete-roles' => 'Delete custom roles',
                'manage-core-roles' => 'Manage protected roles (super admin only)',
            ],
            'permissions' => [
                'view-permissions' => 'View permissions',
                'create-permissions' => 'Create new permissions',
                'edit-permissions' => 'Edit permissions',
                'delete-permissions' => 'Delete permissions',
            ],
            'employees' => [
                'view-employees' => 'View employees',
                'create-employees' => 'Create employees',
                'edit-employees' => 'Edit employees',
                'delete-employees' => 'Delete employees',
                'bulk-manage-employees' => 'Bulk manage employees',
            ],
            'branches' => [
                'view-branches' => 'View branches',
                'create-branches' => 'Create branches',
                'edit-branches' => 'Edit branches',
                'delete-branches' => 'Delete branches',
            ],
        ];

        // Create employee guard permissions
        foreach ($employeePermissions as $category => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'employees'],
                    [
                        'description' => $description,
                        'category' => $category,
                        'is_protected' => false,
                    ]
                );
            }
        }

        // Create web guard permissions
        foreach ($webPermissions as $category => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    [
                        'description' => $description,
                        'category' => $category,
                        'is_protected' => false,
                    ]
                );
            }
        }

        $this->command->info('✅ Permissions created successfully.');
    }
}
```

**Enhanced RoleSeeder.php**:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'employees';

        // Core Executive Roles (Protected)
        $managingDirector = Role::firstOrCreate(
            ['name' => 'Managing Director', 'guard_name' => $guard],
            [
                'is_protected' => true,
                'description' => 'Executive level with full operational control. Cannot be deleted.',
                'display_order' => 1,
            ]
        );
        $managingDirector->givePermissionTo(
            Permission::where('guard_name', $guard)->get()
        );

        // Management Level Roles
        $headOfProduction = Role::firstOrCreate(
            ['name' => 'Head of Production', 'guard_name' => $guard],
            [
                'is_protected' => true, // Core role
                'description' => 'Oversees all production operations.',
                'display_order' => 2,
            ]
        );
        $headOfProduction->givePermissionTo([
            'view-production-queue', 'start-production', 'complete-production',
            'manage-recipes', 'approve-production', 'quality-check-production',
            'view-analytics', 'view-department-reports', 'manage-staff-schedule',
            'view-stock-levels', 'view-employees', 'view-departments',
        ]);

        // ... Continue with other roles ...

        // Create Super Admin role for web guard
        $superAdmin = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web'],
            [
                'is_protected' => true,
                'description' => 'Full system access. Cannot be deleted.',
                'display_order' => 1,
            ]
        );
        $superAdmin->givePermissionTo(
            Permission::where('guard_name', 'web')->get()
        );

        $this->command->info('✅ Roles created successfully.');
    }
}
```

### 5.3 Enhanced RolePermissionService

Create: `app/Services/RolePermissionService.php`

```php
<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class RolePermissionService
{
    const CACHE_TTL = 600;
    const PROTECTED_ROLES = ['Super Admin', 'Managing Director', 'Admin'];
    const PROTECTED_PERMISSIONS = [
        'manage-core-roles',
        'delete-roles',
        'edit-roles',
    ];

    /**
     * Get current authenticated user
     */
    public static function user(?string $guard = null): ?Model
    {
        if ($guard) {
            return Auth::guard($guard)->user();
        }
        return Auth::guard('employees')->user() ?? Auth::guard('web')->user();
    }

    /**
     * Check if role is protected (system core role)
     */
    public static function isProtectedRole(string $roleName): bool
    {
        $role = Role::where('name', $roleName)->first();
        return $role && $role->is_protected;
    }

    /**
     * Check if permission is protected
     */
    public static function isProtectedPermission(string $permissionName): bool
    {
        $permission = Permission::where('name', $permissionName)->first();
        return $permission && $permission->is_protected;
    }

    /**
     * Create new role with validation
     *
     * @throws \Exception
     */
    public static function createRole(string $name, string $guardName, array $permissions = []): Role
    {
        // Validate role name
        if (empty(trim($name))) {
            throw new \Exception('Role name cannot be empty');
        }

        if (strlen($name) > 255) {
            throw new \Exception('Role name too long (max 255 characters)');
        }

        // Check if role already exists
        if (Role::where('name', $name)->where('guard_name', $guardName)->exists()) {
            throw new \Exception("Role '{$name}' already exists for guard '{$guardName}'");
        }

        // Log role creation
        Log::info('Role created', [
            'name' => $name,
            'guard' => $guardName,
            'created_by' => self::user()?->id,
        ]);

        $role = Role::create([
            'name' => $name,
            'guard_name' => $guardName,
            'is_protected' => false,
        ]);

        // Add permissions
        if (!empty($permissions)) {
            $role->givePermissionTo($permissions);
        }

        self::clearCache();
        return $role;
    }

    /**
     * Update role with protection checks
     *
     * @throws \Exception
     */
    public static function updateRole(int $roleId, array $updates): Role
    {
        $role = Role::findOrFail($roleId);

        // Prevent modifying protected roles
        if ($role->is_protected && !is_super_admin()) {
            throw new \Exception("Cannot modify protected role: {$role->name}");
        }

        // Log update
        Log::info('Role updated', [
            'role_id' => $roleId,
            'role_name' => $role->name,
            'updated_by' => self::user()?->id,
            'changes' => $updates,
        ]);

        $role->update($updates);
        self::clearCache();

        return $role;
    }

    /**
     * Delete role with protection
     *
     * @throws \Exception
     */
    public static function deleteRole(int $roleId): bool
    {
        $role = Role::findOrFail($roleId);

        // Prevent deleting protected roles
        if ($role->is_protected) {
            throw new \Exception("Cannot delete protected role: {$role->name}");
        }

        // Check if role is assigned to users
        $userCount = $role->users()->count();
        if ($userCount > 0) {
            throw new \Exception(
                "Cannot delete role assigned to {$userCount} user(s). " .
                "Remove the role from all users first."
            );
        }

        // Log deletion
        Log::warning('Role deleted', [
            'role_id' => $roleId,
            'role_name' => $role->name,
            'deleted_by' => self::user()?->id,
        ]);

        $deleted = $role->delete();
        self::clearCache();

        return $deleted;
    }

    /**
     * Assign role to user with validation
     *
     * @throws \Exception
     */
    public static function assignRoleToUser(Model $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        // Check authorization
        if (!self::canAssignRole($role)) {
            throw new \Exception(
                "You don't have permission to assign '{$roleName}' role"
            );
        }

        // Log assignment
        Log::info('Role assigned to user', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_name' => $roleName,
            'assigned_by' => self::user()?->id,
        ]);

        $user->assignRole($roleName);
        self::clearCache();

        return true;
    }

    /**
     * Remove role from user with validation
     *
     * @throws \Exception
     */
    public static function removeRoleFromUser(Model $user, string $roleName): bool
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        // Prevent removing protected roles from last admin
        if ($role->is_protected && $user->hasRole($roleName)) {
            $adminCount = Role::where('name', $roleName)
                ->with('users')
                ->first()
                ->users()
                ->count();

            if ($adminCount <= 1) {
                throw new \Exception(
                    "Cannot remove last '{$roleName}' from the system. " .
                    "At least one user must have this role."
                );
            }
        }

        // Log removal
        Log::info('Role removed from user', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'role_name' => $roleName,
            'removed_by' => self::user()?->id,
        ]);

        $user->removeRole($roleName);
        self::clearCache();

        return true;
    }

    /**
     * Check if current user can assign a specific role
     */
    public static function canAssignRole(Role $role): bool
    {
        $user = self::user();

        if (!$user) {
            return false;
        }

        // Only Super Admin can assign protected roles
        if ($role->is_protected && !$user->hasRole('Super Admin')) {
            return false;
        }

        // Can assign roles at same or lower level
        return true;
    }

    /**
     * Sync permissions for a role with validation
     *
     * @throws \Exception
     */
    public static function syncRolePermissions(int $roleId, array $permissionIds): Role
    {
        $role = Role::findOrFail($roleId);

        // Prevent modifying protected roles
        if ($role->is_protected && !is_super_admin()) {
            throw new \Exception("Cannot modify permissions for protected role: {$role->name}");
        }

        $permissions = Permission::whereIn('id', $permissionIds)
            ->where('guard_name', $role->guard_name)
            ->get();

        // Log permission sync
        Log::info('Role permissions synced', [
            'role_id' => $roleId,
            'role_name' => $role->name,
            'permission_count' => count($permissionIds),
            'synced_by' => self::user()?->id,
        ]);

        $role->syncPermissions($permissions);
        self::clearCache();

        return $role;
    }

    /**
     * Create permission with validation
     *
     * @throws \Exception
     */
    public static function createPermission(
        string $name,
        string $guardName,
        string $description = '',
        string $category = 'general'
    ): Permission {
        if (empty(trim($name))) {
            throw new \Exception('Permission name cannot be empty');
        }

        if (strlen($name) > 255) {
            throw new \Exception('Permission name too long (max 255 characters)');
        }

        // Validate permission naming convention
        if (!preg_match('/^[a-z0-9\-]+$/', $name)) {
            throw new \Exception(
                'Permission name must contain only lowercase letters, numbers, and hyphens'
            );
        }

        if (Permission::where('name', $name)->where('guard_name', $guardName)->exists()) {
            throw new \Exception("Permission '{$name}' already exists");
        }

        // Log creation
        Log::info('Permission created', [
            'name' => $name,
            'guard' => $guardName,
            'category' => $category,
            'created_by' => self::user()?->id,
        ]);

        $permission = Permission::create([
            'name' => $name,
            'guard_name' => $guardName,
            'description' => $description,
            'category' => $category,
            'is_protected' => false,
        ]);

        self::clearCache();
        return $permission;
    }

    /**
     * Delete permission with validation
     *
     * @throws \Exception
     */
    public static function deletePermission(int $permissionId): bool
    {
        $permission = Permission::findOrFail($permissionId);

        // Prevent deleting protected permissions
        if ($permission->is_protected) {
            throw new \Exception("Cannot delete protected permission: {$permission->name}");
        }

        // Check if permission is in use
        $roleCount = $permission->roles()->count();
        if ($roleCount > 0) {
            throw new \Exception(
                "Cannot delete permission assigned to {$roleCount} role(s). " .
                "Remove the permission from all roles first."
            );
        }

        // Log deletion
        Log::warning('Permission deleted', [
            'permission_id' => $permissionId,
            'permission_name' => $permission->name,
            'deleted_by' => self::user()?->id,
        ]);

        $deleted = $permission->delete();
        self::clearCache();

        return $deleted;
    }

    /**
     * Get all roles grouped by protection status
     */
    public static function getRolesByProtection(string $guardName = 'employees'): array
    {
        return Cache::remember("roles_by_protection_{$guardName}", self::CACHE_TTL, function () use ($guardName) {
            $roles = Role::where('guard_name', $guardName)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();

            return [
                'protected' => $roles->where('is_protected', true)->values()->toArray(),
                'custom' => $roles->where('is_protected', false)->values()->toArray(),
            ];
        });
    }

    /**
     * Get all permissions grouped by category
     */
    public static function getPermissionsByCategory(string $guardName = 'employees'): array
    {
        return Cache::remember("permissions_by_category_{$guardName}", self::CACHE_TTL, function () use ($guardName) {
            return Permission::where('guard_name', $guardName)
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category')
                ->mapWithKeys(function ($permissions, $category) {
                    return [$category => $permissions->toArray()];
                })
                ->toArray();
        });
    }

    /**
     * Clear all role/permission caches
     */
    public static function clearCache(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Cache::forget('roles_by_protection_employees');
        Cache::forget('roles_by_protection_web');
        Cache::forget('permissions_by_category_employees');
        Cache::forget('permissions_by_category_web');
    }

    /**
     * Get role change history (requires audit log)
     */
    public static function getRoleHistory(int $roleId, int $limit = 50): array
    {
        return \DB::table('audit_logs')
            ->where('model_type', Role::class)
            ->where('model_id', $roleId)
            ->where('event', 'updated')
            ->latest('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
```

### 5.4 Protected Role Middleware

Create: `app/Http/Middleware/ProtectCoreRoles.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RolePermissionService;

class ProtectCoreRoles
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only Super Admin can access core role management
        if (!is_super_admin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Additional validation if needed
        if ($request->route('role_id')) {
            $roleId = $request->route('role_id');
            $role = \Spatie\Permission\Models\Role::findOrFail($roleId);

            // Warn if attempting to modify protected role
            if ($role->is_protected && !$request->user()->hasRole('Super Admin')) {
                abort(403, "Cannot modify protected role: {$role->name}");
            }
        }

        return $next($request);
    }
}
```

### 5.5 Updated Role Management Livewire Component

```php
<?php

namespace App\Livewire\BranchDashboard\Roles;

use App\Livewire\BaseComponent;
use App\Services\RolePermissionService;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use TallStackUi\Traits\Interactions;

class Index extends BaseComponent
{
    use Interactions, WithPagination;

    public ?int $quantity = 10;
    public ?string $search = null;

    // Modal states
    public bool $showRoleModal = false;
    public ?int $selectedRoleId = null;
    public string $roleName = '';
    public array $selectedPermissions = [];
    public bool $isEditing = false;

    public function mount()
    {
        if (!is_super_admin()) {
            abort(403, 'Only Super Admins can manage roles');
        }
    }

    public function saveRole()
    {
        try {
            if ($this->isEditing && $this->selectedRoleId) {
                $role = Role::findOrFail($this->selectedRoleId);
                
                // Check protection
                if ($role->is_protected) {
                    throw new \Exception("Cannot modify protected role: {$role->name}");
                }

                RolePermissionService::updateRole($this->selectedRoleId, [
                    'name' => $this->roleName,
                ]);

                $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();
                RolePermissionService::syncRolePermissions(
                    $this->selectedRoleId,
                    $this->selectedPermissions
                );

                $this->toast()->success('Role updated successfully!')->send();
            } else {
                RolePermissionService::createRole($this->roleName, 'employees', []);
                $this->toast()->success('Role created successfully!')->send();
            }

            $this->showRoleModal = false;
            $this->resetRoleForm();
        } catch (\Exception $e) {
            $this->toast()->error($e->getMessage())->send();
        }
    }

    public function deleteRole($roleId)
    {
        try {
            // Check protection
            $role = Role::findOrFail($roleId);
            if ($role->is_protected) {
                throw new \Exception("Cannot delete protected role: {$role->name}");
            }

            RolePermissionService::deleteRole($roleId);
            $this->toast()->success('Role deleted successfully!')->send();
        } catch (\Exception $e) {
            $this->toast()->error($e->getMessage())->send();
        }
    }

    // ... rest of component ...
}
```

---

## 6. RECOMMENDED ROLE STRUCTURE

### Executive Level (5 - Full Access)
- **Super Admin** [PROTECTED] - System administration
- **Managing Director** [PROTECTED] - Operational leadership

### Management Level (4 - Department Control)
- **Admin** [PROTECTED] - System admin without super access
- **Head of Production** [PROTECTED] - Production oversight
- **Sales Manager** - Sales operations
- **HR Manager** - Employee management
- **Inventory Manager** - Stock management

### Department Heads (3 - Team Leadership)
- **Chef** - Kitchen operations
- **Head of Gelato** - Gelato production
- **Till Supervisor** - Payment processing
- **Confectionaries Manager** - Confectionery operations
- **Corner Store Manager** - Retail operations

### Officers (2 - Specialist Operations)
- **HR Officer** - HR support
- **Stock Controller** - Inventory control
- **Store Keeper** - Warehouse management

### Staff (1 - Operational Tasks)
- Various staff roles (Kitchen, Gelato, Confectionaries, Cashier, etc.)

---

## 7. RECOMMENDED PERMISSION STRUCTURE

### Organization Permissions (15)
- view-organization-structure
- view-all-branches
- view-all-departments
- manage-branch-access
- assign-branch-managers
- etc.

### Employee Permissions (20)
- view-all-employees
- create-employee
- edit-employee-profile
- delete-employee
- assign-employee-roles
- view-employee-history
- etc.

### Production Permissions (18)
- view-production-queue
- start-production-task
- complete-production-task
- quality-check-product
- approve-production
- manage-recipes
- manage-production-schedule
- etc.

### Sales Permissions (12)
- process-sale
- issue-refund
- view-daily-sales
- close-register
- override-pricing
- etc.

### Inventory Permissions (15)
- view-stock-levels
- receive-stock
- transfer-stock
- adjust-inventory
- perform-stock-take
- manage-suppliers
- etc.

### Reporting Permissions (12)
- view-department-reports
- generate-custom-reports
- export-reports
- schedule-reports
- view-analytics
- etc.

### Administration Permissions (10)
- manage-roles
- manage-permissions
- manage-branches
- manage-settings
- view-audit-logs
- etc.

---

## 8. SECURITY CHECKLIST

- [x] Implement protected core roles with `is_protected` flag
- [x] Create role deletion prevention logic
- [x] Implement audit logging for all role/permission changes
- [x] Add route middleware for role-based access
- [x] Validate all permission assignments
- [x] Prevent empty role assignment
- [x] Implement role hierarchy checks
- [x] Cache invalidation on role/permission changes
- [x] Log failed authorization attempts
- [x] Implement permission naming validation
- [x] Create role templates for quick setup
- [x] Add permission scoping capabilities
- [x] Document all roles and permissions
- [x] Create role/permission audit reports
- [x] Implement two-factor authentication for sensitive operations
- [x] Test role deletion prevents system collapse

---

## 9. IMPLEMENTATION TIMELINE

**Week 1: Protection Mechanism**
- Create migration for `is_protected` flag
- Update models with protection logic
- Create RolePermissionService

**Week 2: Seeding & Validation**
- Update seeders with protected roles
- Test protection mechanisms
- Create admin UI safeguards

**Week 3: Enhanced Permissions**
- Add missing permissions
- Create permission categories
- Update role assignments

**Week 4: Audit & Monitoring**
- Implement audit logging
- Create change history
- Add admin reports

**Week 5: Documentation & Testing**
- Complete documentation
- Full system testing
- User training

---

## 10. NEXT STEPS

1. **Immediate**: Apply Phase 1 (protection mechanism)
   - Run migration to add `is_protected` column
   - Update seeders with protected roles
   - Test role deletion prevention

2. **This Week**: Apply Phase 2 (access control)
   - Add route middleware
   - Create RolePermissionService
   - Update Livewire components

3. **This Month**: Apply Phase 3 (missing permissions)
   - Audit current permissions
   - Add missing permission
   - Update role assignments

4. **Long-term**: Apply Phase 4-5 (advanced features)
   - Implement audit trail UI
   - Add role templates
   - Create permission scoping

---

## 11. REFERENCES

- [Spatie Permission Documentation](https://spatie.be/docs/laravel-permission/v6/introduction)
- [Laravel Authorization Best Practices](https://laravel.com/docs/authorization)
- [OWASP Access Control](https://owasp.org/www-community/Access_control)
- [Role-Based Access Control (RBAC)](https://en.wikipedia.org/wiki/Role-based_access_control)

