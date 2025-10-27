# Role & Permission Helper Guide

## Overview

The `RolePermission` helper provides a comprehensive interface for working with Spatie Laravel Permission package. It includes caching, hierarchical role management, and convenient methods for authorization checks across both `web` (User) and `employees` (Employee) guards.

## Table of Contents

1. [Basic Usage](#basic-usage)
2. [Blade Directives](#blade-directives)
3. [Route Protection](#route-protection)
4. [Livewire Component Examples](#livewire-component-examples)
5. [Advanced Features](#advanced-features)

---

## Basic Usage

### Check if User Has a Role

```php
use App\Helpers\RolePermission;

// Check current authenticated user
if (RolePermission::hasRole('Admin')) {
    // User has Admin role
}

// Check specific guard
if (RolePermission::hasRole('Chef', 'employees')) {
    // Employee has Chef role
}
```

### Check if User Has a Permission

```php
// Check single permission
if (RolePermission::hasPermission('edit-employees')) {
    // User can edit employees
}

// Check if user has ANY of these permissions
if (RolePermission::hasAnyPermission(['edit-employees', 'view-employees'])) {
    // User can edit OR view employees
}

// Check if user has ALL permissions
if (RolePermission::hasAllPermissions(['edit-employees', 'delete-employees'])) {
    // User can both edit AND delete employees
}
```

### Convenience Methods

```php
// Check if super admin
if (RolePermission::isSuperAdmin()) {
    // User is Super Admin
}

// Check if any admin role
if (RolePermission::isAdmin()) {
    // User is Admin or Super Admin
}

// Check if manager
if (RolePermission::isManager()) {
    // User is Managing Director, HR Manager, Sales Manager, etc.
}

// Check if supervisor
if (RolePermission::isSupervisor()) {
    // User is Chef, Head of Gelato, Till Supervisor, etc.
}

// Check if staff level
if (RolePermission::isStaff()) {
    // User is Kitchen Staff, Cashier, etc.
}
```

---

## Blade Directives

### Role-Based Directives

```blade
{{-- Single role check --}}
@role('Admin')
    <a href="/admin/dashboard">Admin Dashboard</a>
@endrole

{{-- Multiple roles (ANY) --}}
@anyrole(['Manager', 'Supervisor'])
    <button>View Reports</button>
@endanyrole

{{-- Multiple roles (ALL) --}}
@allroles(['Admin', 'HR Manager'])
    <button>Manage All Employees</button>
@endallroles

{{-- Unless role (inverse) --}}
@unlessrole('Admin')
    <p>You don't have admin access</p>
@endunlessrole
```

### Permission-Based Directives

```blade
{{-- Single permission check --}}
@permission('edit-employees')
    <button wire:click="editEmployee">Edit</button>
@endpermission

{{-- Multiple permissions (ANY) --}}
@anypermission(['edit-employees', 'view-employees'])
    <a href="/employees">Manage Employees</a>
@endanypermission

{{-- Multiple permissions (ALL) --}}
@allpermissions(['create-products', 'edit-products'])
    <button>Full Product Management</button>
@endallpermissions

{{-- Unless permission (inverse) --}}
@unlesspermission('delete-employees')
    <span class="text-gray-400">Delete (No Permission)</span>
@endunlesspermission
```

### Convenience Directives

```blade
{{-- Super admin check --}}
@superadmin
    <a href="/super-admin/settings">System Settings</a>
@endsuperadmin

{{-- Admin check (Admin or Super Admin) --}}
@admin
    <a href="/admin/panel">Admin Panel</a>
@endadmin

{{-- Manager check --}}
@manager
    <button>Approve Request</button>
@endmanager

{{-- Supervisor check --}}
@supervisor
    <button>View Team Performance</button>
@endsupervisor

{{-- Staff check --}}
@staff
    <p>Welcome to staff portal</p>
@endstaff

{{-- Role level check (hierarchical) --}}
@rolelevel(4)
    {{-- Only managers and above (level 4+) --}}
    <button>Manage Department</button>
@endrolelevel

{{-- Check if can manage another user --}}
@canmanage($employee)
    <button wire:click="editEmployee({{ $employee->id }})">Edit</button>
@endcanmanage

{{-- Module access check --}}
@module('production')
    <a href="/production/queue">Production Queue</a>
@endmodule
```

---

## Route Protection

### Using Spatie Middleware (Already Configured)

```php
// In routes/web.php or routes/super-admin.php

// Require specific role
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
});

// Require specific permission
Route::middleware(['auth', 'permission:edit-employees'])->group(function () {
    Route::post('/employees/{id}/update', [EmployeeController::class, 'update']);
});

// Require role OR permission
Route::middleware(['auth', 'role_or_permission:Admin|edit-employees'])->group(function () {
    Route::get('/employees/manage', [EmployeeController::class, 'manage']);
});
```

### Using Helper in Controllers

```php
use App\Helpers\RolePermission;

class EmployeeController extends Controller
{
    public function edit($id)
    {
        // Check permission
        if (!RolePermission::hasPermission('edit-employees')) {
            abort(403, 'Unauthorized action.');
        }

        $employee = Employee::findOrFail($id);

        // Check if can manage this specific employee
        if (!RolePermission::canManageUser($employee)) {
            abort(403, 'You cannot manage this employee.');
        }

        return view('employees.edit', compact('employee'));
    }

    public function delete($id)
    {
        // Only managers (level 4+) can delete
        if (!RolePermission::hasRoleLevel(4)) {
            abort(403, 'Insufficient permissions.');
        }

        Employee::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Employee deleted');
    }
}
```

---

## Livewire Component Examples

### Basic Permission Checks

```php
<?php

namespace App\Livewire\Employees;

use Livewire\Component;
use App\Helpers\RolePermission;
use App\Models\Employee;

class EmployeeManagement extends Component
{
    public $employees;
    public $canEdit = false;
    public $canDelete = false;

    public function mount()
    {
        // Check permissions on component mount
        $this->canEdit = RolePermission::hasPermission('edit-employees');
        $this->canDelete = RolePermission::hasPermission('delete-employees');

        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        $this->employees = Employee::all();
    }

    public function editEmployee($id)
    {
        // Double-check permission before action
        if (!RolePermission::hasPermission('edit-employees')) {
            $this->toast()->error('You do not have permission to edit employees')->send();
            return;
        }

        // Edit logic here
    }

    public function deleteEmployee($id)
    {
        // Only managers can delete
        if (!RolePermission::hasRoleLevel(4)) {
            $this->toast()->error('Only managers can delete employees')->send();
            return;
        }

        $employee = Employee::findOrFail($id);

        // Check if can manage this employee
        if (!RolePermission::canManageUser($employee)) {
            $this->toast()->error('You cannot delete this employee')->send();
            return;
        }

        $employee->delete();
        $this->toast()->success('Employee deleted successfully')->send();
        $this->loadEmployees();
    }

    public function render()
    {
        return view('livewire.employees.employee-management');
    }
}
```

### Corresponding Blade View

```blade
<div>
    <h2>Employee Management</h2>

    @permission('create-employees')
        <button wire:click="createEmployee" class="btn btn-primary">
            Add New Employee
        </button>
    @endpermission

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                @anypermission(['edit-employees', 'delete-employees'])
                    <th>Actions</th>
                @endanypermission
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->getRoleNames()->implode(', ') }}</td>

                    @anypermission(['edit-employees', 'delete-employees'])
                        <td>
                            @permission('edit-employees')
                                @canmanage($employee)
                                    <button wire:click="editEmployee({{ $employee->id }})">
                                        Edit
                                    </button>
                                @endcanmanage
                            @endpermission

                            @permission('delete-employees')
                                @rolelevel(4)
                                    @canmanage($employee)
                                        <button wire:click="deleteEmployee({{ $employee->id }})">
                                            Delete
                                        </button>
                                    @endcanmanage
                                @endrolelevel
                            @endpermission
                        </td>
                    @endanypermission
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

---

## Advanced Features

### Role Hierarchy

The helper includes a hierarchical role system with 5 levels:

- **Level 5**: Super Admin, Managing Director (Executive)
- **Level 4**: Admin, Department Managers (Management)
- **Level 3**: Department Heads, Supervisors
- **Level 2**: Officers (HR Officer, Stock Controller, etc.)
- **Level 1**: Staff (Cashiers, Production Staff, etc.)

```php
// Check if user has minimum role level
if (RolePermission::hasRoleLevel(4)) {
    // User is level 4 or higher (managers and above)
}

// Get user's role level
$role = 'Sales Manager';
$level = RolePermission::getRoleLevel($role); // Returns 4
```

### Managing Other Users

```php
$currentUser = auth()->user();
$targetEmployee = Employee::find($id);

// Check if current user can manage target employee
if (RolePermission::canManageUser($targetEmployee)) {
    // Current user has higher role level than target
    // Can edit, assign roles, etc.
}
```

### Module Access Control

```php
// Check if user can access a specific module
if (RolePermission::canAccessModule('production')) {
    // User has at least one production permission
}

// Available modules:
// - production
// - sales
// - inventory
// - employees
// - reports
// - settings
```

### Assigning Roles and Permissions

```php
use App\Helpers\RolePermission;

$employee = Employee::find($id);

// Assign a role
RolePermission::assignRole($employee, 'Chef');

// Remove a role
RolePermission::removeRole($employee, 'Kitchen Staff');

// Sync roles (replaces all existing roles)
RolePermission::syncRoles($employee, ['Chef', 'Kitchen Staff']);

// Give direct permission
RolePermission::givePermission($employee, 'view-analytics');

// Revoke permission
RolePermission::revokePermission($employee, 'view-analytics');

// Sync permissions
RolePermission::syncPermissions($employee, ['view-analytics', 'view-reports']);
```

### Getting Role and Permission Lists

```php
// Get all roles for a guard
$roles = RolePermission::getAllRoles('employees');
// Returns: [
//     ['id' => 1, 'name' => 'Admin', 'guard_name' => 'employees', 'permissions_count' => 10],
//     ['id' => 2, 'name' => 'Manager', 'guard_name' => 'employees', 'permissions_count' => 8],
//     ...
// ]

// Get all permissions for a guard
$permissions = RolePermission::getAllPermissions('employees');
// Returns: ['view-employees', 'edit-employees', 'delete-employees', ...]

// Get permissions grouped by category
$grouped = RolePermission::getPermissionsByCategory('employees');
// Returns: [
//     'employees' => ['view-employees', 'edit-employees', 'delete-employees'],
//     'production' => ['view-production-queue', 'start-production'],
//     ...
// ]

// Get current user's roles
$userRoles = RolePermission::getUserRoles();

// Get current user's permissions
$userPermissions = RolePermission::getUserPermissions();
```

### Caching

The helper uses two levels of caching:
1. **Spatie's built-in cache** for role/permission checks
2. **Custom Laravel cache** for role and permission lists (10 minutes TTL)

```php
// Clear all caches
RolePermission::clearCache();

// This is useful after:
// - Creating new roles or permissions
// - Updating role/permission assignments
// - Changing Settings
```

### Role Descriptions

```php
$description = RolePermission::getRoleDescription('Chef');
// Returns: "Leads kitchen operations and production"

// Use in UI to show role descriptions
@foreach($roles as $role)
    <option value="{{ $role }}">
        {{ $role }} - {{ RolePermission::getRoleDescription($role) }}
    </option>
@endforeach
```

### Checking Existence

```php
// Check if role exists
if (RolePermission::roleExists('Chef', 'employees')) {
    // Role exists
}

// Check if permission exists
if (RolePermission::permissionExists('edit-employees', 'employees')) {
    // Permission exists
}

// Get role model
$role = RolePermission::getRole('Chef', 'employees');

// Get permission model
$permission = RolePermission::getPermission('edit-employees', 'employees');
```

### Get Users with Role or Permission

```php
// Get all users with a specific role
$chefs = RolePermission::getUsersWithRole('Chef', 'employees');

// Get all users with a specific permission
$canEditEmployees = RolePermission::getUsersWithPermission('edit-employees', 'employees');
```

---

## Complete Example: Production Queue Access

```php
// Livewire Component
<?php

namespace App\Livewire\Production;

use Livewire\Component;
use App\Helpers\RolePermission;
use App\Models\ProductionQueue;

class QueueManagement extends Component
{
    public $queue;
    public $canView = false;
    public $canStart = false;
    public $canComplete = false;
    public $canApprove = false;

    public function mount()
    {
        // Check module access first
        if (!RolePermission::canAccessModule('production')) {
            abort(403, 'You do not have access to production module');
        }

        // Set specific permissions
        $this->canView = RolePermission::hasPermission('view-production-queue');
        $this->canStart = RolePermission::hasPermission('start-production');
        $this->canComplete = RolePermission::hasPermission('complete-production');
        $this->canApprove = RolePermission::hasPermission('approve-production');

        if (!$this->canView) {
            abort(403, 'Unauthorized');
        }

        $this->loadQueue();
    }

    public function loadQueue()
    {
        $this->queue = ProductionQueue::with('product')->get();
    }

    public function startProduction($id)
    {
        if (!$this->canStart) {
            $this->toast()->error('No permission to start production')->send();
            return;
        }

        // Start production logic
    }

    public function completeProduction($id)
    {
        if (!$this->canComplete) {
            $this->toast()->error('No permission to complete production')->send();
            return;
        }

        // Complete production logic
    }

    public function approveProduction($id)
    {
        // Only managers can approve
        if (!$this->canApprove || !RolePermission::hasRoleLevel(4)) {
            $this->toast()->error('Only managers can approve production')->send();
            return;
        }

        // Approve production logic
    }

    public function render()
    {
        return view('livewire.production.queue-management');
    }
}
```

```blade
{{-- Blade View --}}
<div>
    <h2>Production Queue</h2>

    @manager
        <div class="alert alert-info">
            You have manager access to production operations
        </div>
    @endmanager

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Status</th>
                <th>Quantity</th>
                @anypermission(['start-production', 'complete-production', 'approve-production'])
                    <th>Actions</th>
                @endanypermission
            </tr>
        </thead>
        <tbody>
            @foreach($queue as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->quantity }}</td>

                    <td>
                        @if($item->status === 'pending')
                            @permission('start-production')
                                <button wire:click="startProduction({{ $item->id }})">
                                    Start
                                </button>
                            @endpermission
                        @endif

                        @if($item->status === 'in_progress')
                            @permission('complete-production')
                                <button wire:click="completeProduction({{ $item->id }})">
                                    Complete
                                </button>
                            @endpermission
                        @endif

                        @if($item->status === 'completed')
                            @permission('approve-production')
                                @rolelevel(4)
                                    <button wire:click="approveProduction({{ $item->id }})">
                                        Approve
                                    </button>
                                @endrolelevel
                            @endpermission
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

---

## Summary

The `RolePermission` helper provides:

- ✅ Easy role and permission checking
- ✅ Multiple guard support (web, employees)
- ✅ Hierarchical role management
- ✅ Performance optimization with caching
- ✅ Blade directives for clean views
- ✅ Module-based access control
- ✅ User management capabilities
- ✅ Comprehensive convenience methods

Use it throughout your application for consistent, efficient authorization checks!
