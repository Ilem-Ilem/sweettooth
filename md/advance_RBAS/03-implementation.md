# 03 - Implementation Guide

## 🚀 Implementation Strategy

This section covers the complete implementation of the advanced RBAC system, including seeders, middleware, authorization logic, and UI considerations.

## 🌱 Database Seeders

### 1. DepartmentCategorySeeder (Already Exists)

The DepartmentCategorySeeder already exists and creates the core categories. No changes needed.

### 2. DepartmentSeeder Extensions

Update the existing DepartmentSeeder to add slugs and active status:

```php
<?php
// database/seeders/DepartmentSeeder.php (extended)

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get all active branches
        $branches = Branch::where('is_active', true)->get();

        foreach ($branches as $branch) {
            $this->createDepartmentsForBranch($branch);
        }

        $this->command->info('✅ Departments seeded successfully');
    }

    private function createDepartmentsForBranch(Branch $branch): void
    {
        $departments = [
            // Production Departments
            [
                'category_name' => 'Production',
                'name' => 'Main Kitchen',
                'slug' => 'main-kitchen',
                'description' => 'Primary production kitchen for food preparation',
            ],
            [
                'category_name' => 'Production',
                'name' => 'Gelato Production',
                'slug' => 'gelato-production',
                'description' => 'Specialized gelato manufacturing and storage',
            ],
            [
                'category_name' => 'Production',
                'name' => 'Confectionery',
                'slug' => 'confectionery',
                'description' => 'Cake and pastry production department',
            ],

            // Sales Departments
            [
                'category_name' => 'Sales',
                'name' => 'Point of Sale',
                'slug' => 'pos',
                'description' => 'In-store sales and cashier operations',
            ],
            [
                'category_name' => 'Sales',
                'name' => 'Corner Store',
                'slug' => 'corner-store',
                'description' => 'Convenience store operations and retail',
            ],

            // Inventory Departments
            [
                'category_name' => 'Inventory',
                'name' => 'Main Warehouse',
                'slug' => 'main-warehouse',
                'description' => 'Central inventory storage and management',
            ],
            [
                'category_name' => 'Inventory',
                'name' => 'Cold Storage',
                'slug' => 'cold-storage',
                'description' => 'Refrigerated inventory storage facility',
            ],

            // HR Department
            [
                'category_name' => 'Support',
                'name' => 'HR Operations',
                'slug' => 'hr-operations',
                'description' => 'Human resources administration and employee management',
            ],
        ];

        foreach ($departments as $deptData) {
            $category = DepartmentCategory::where('name', $deptData['category_name'])->first();

            if (!$category) {
                $this->command->warn("⚠️ Category '{$deptData['category_name']}' not found, skipping department '{$deptData['name']}'");
                continue;
            }

            Department::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'name' => $deptData['name'], // Use name as unique identifier
                ],
                [
                    'category_id' => $category->id,
                    'slug' => $deptData['slug'],
                    'description' => $deptData['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
```

### 3. RoleCategoryConstraintSeeder (New)

Creates the role constraints that define where roles can be used:

```php
<?php
// database/seeders/RoleCategoryConstraintSeeder.php

namespace Database\Seeders;

use App\Models\DepartmentCategory;
use App\Models\RoleCategoryConstraint;
use Illuminate\Database\Seeder;

class RoleCategoryConstraintSeeder extends Seeder
{
    public function run(): void
    {
        $constraints = [
            // Production Roles
            [
                'role_name' => 'Kitchen Staff',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['main-kitchen'],
            ],
            [
                'role_name' => 'Chef',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['main-kitchen'],
            ],
            [
                'role_name' => 'Head of Production',
                'category_name' => 'Production',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Gelato Production Staff',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['gelato-production'],
            ],
            [
                'role_name' => 'Head of Gelato',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['gelato-production'],
            ],
            [
                'role_name' => 'Confectioneries Production Staff',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['confectionery'],
            ],
            [
                'role_name' => 'Confectioneries Manager',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['confectionery'],
            ],

            // Sales Roles
            [
                'role_name' => 'Cashier',
                'category_name' => 'Sales',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['pos'],
            ],
            [
                'role_name' => 'Sales Associate',
                'category_name' => 'Sales',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['pos'],
            ],
            [
                'role_name' => 'Junior Cashier',
                'category_name' => 'Sales',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['pos'],
            ],
            [
                'role_name' => 'Corner Store Staff',
                'category_name' => 'Sales',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['corner-store'],
            ],
            [
                'role_name' => 'Corner Store Manager',
                'category_name' => 'Sales',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['corner-store'],
            ],
            [
                'role_name' => 'Sales Supervisor',
                'category_name' => 'Sales',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Sales Manager',
                'category_name' => 'Sales',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],

            // Inventory Roles
            [
                'role_name' => 'Stock Controller',
                'category_name' => 'Inventory',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Store Keeper',
                'category_name' => 'Inventory',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Inventory Manager',
                'category_name' => 'Inventory',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],

            // HR Roles
            [
                'role_name' => 'HR Officer',
                'category_name' => 'Support',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['hr-operations'],
            ],
            [
                'role_name' => 'HR Manager',
                'category_name' => 'Support',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
        ];

        foreach ($constraints as $constraintData) {
            $category = DepartmentCategory::where('name', $constraintData['category_name'])->first();

            if (!$category) {
                $this->command->warn("⚠️ Category '{$constraintData['category_name']}' not found, skipping constraint for '{$constraintData['role_name']}'");
                continue;
            }

            RoleCategoryConstraint::updateOrCreate(
                [
                    'role_name' => $constraintData['role_name'],
                    'category_id' => $category->id,
                ],
                [
                    'department_type' => $constraintData['department_type'],
                    'allowed_department_slugs' => $constraintData['allowed_department_slugs'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Role category constraints seeded successfully');
    }
}
```

### 3. RoleDepartmentMappingSeeder

```php
<?php
// database/seeders/RoleDepartmentMappingSeeder.php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Department;
use App\Models\RoleDepartmentMapping;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleDepartmentMappingSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [
            // Production Roles
            [
                'role_name' => 'Kitchen Staff',
                'category_slug' => 'production',
                'department_slugs' => ['main-kitchen'],
            ],
            [
                'role_name' => 'Chef',
                'category_slug' => 'production',
                'department_slugs' => ['main-kitchen'],
            ],
            [
                'role_name' => 'Head of Production',
                'category_slug' => 'production',
                'department_slugs' => null, // Category-wide
            ],
            [
                'role_name' => 'Gelato Production Staff',
                'category_slug' => 'production',
                'department_slugs' => ['gelato-production'],
            ],
            [
                'role_name' => 'Head of Gelato',
                'category_slug' => 'production',
                'department_slugs' => ['gelato-production'],
            ],
            [
                'role_name' => 'Confectioneries Production Staff',
                'category_slug' => 'production',
                'department_slugs' => ['confectionery'],
            ],
            [
                'role_name' => 'Confectioneries Manager',
                'category_slug' => 'production',
                'department_slugs' => ['confectionery'],
            ],

            // Sales Roles
            [
                'role_name' => 'Cashier',
                'category_slug' => 'sales',
                'department_slugs' => ['pos'],
            ],
            [
                'role_name' => 'Sales Associate',
                'category_slug' => 'sales',
                'department_slugs' => ['pos'],
            ],
            [
                'role_name' => 'Junior Cashier',
                'category_slug' => 'sales',
                'department_slugs' => ['pos'],
            ],
            [
                'role_name' => 'Corner Store Staff',
                'category_slug' => 'sales',
                'department_slugs' => ['corner-store'],
            ],
            [
                'role_name' => 'Corner Store Manager',
                'category_slug' => 'sales',
                'department_slugs' => ['corner-store'],
            ],
            [
                'role_name' => 'Sales Supervisor',
                'category_slug' => 'sales',
                'department_slugs' => null, // Can work in any sales department
            ],
            [
                'role_name' => 'Sales Manager',
                'category_slug' => 'sales',
                'department_slugs' => null, // Category-wide
            ],

            // Inventory Roles
            [
                'role_name' => 'Stock Controller',
                'category_slug' => 'inventory',
                'department_slugs' => ['main-warehouse', 'cold-storage'],
            ],
            [
                'role_name' => 'Store Keeper',
                'category_slug' => 'inventory',
                'department_slugs' => ['main-warehouse', 'cold-storage'],
            ],
            [
                'role_name' => 'Inventory Manager',
                'category_slug' => 'inventory',
                'department_slugs' => null, // Category-wide
            ],

            // HR Roles
            [
                'role_name' => 'HR Officer',
                'category_slug' => 'hr',
                'department_slugs' => ['hr-operations'],
            ],
            [
                'role_name' => 'HR Manager',
                'category_slug' => 'hr',
                'department_slugs' => null, // Category-wide
            ],
        ];

        foreach ($mappings as $mapping) {
            $role = Role::where('name', $mapping['role_name'])->first();
            if (!$role) {
                $this->command->warn("⚠️ Role '{$mapping['role_name']}' not found, skipping mapping");
                continue;
            }

            $category = Category::where('slug', $mapping['category_slug'])->first();
            if (!$category) {
                $this->command->warn("⚠️ Category '{$mapping['category_slug']}' not found, skipping mapping");
                continue;
            }

            // Handle department-specific mappings
            if ($mapping['department_slugs']) {
                foreach ($mapping['department_slugs'] as $deptSlug) {
                    $department = Department::where('slug', $deptSlug)->first();
                    if ($department) {
                        RoleDepartmentMapping::updateOrCreate(
                            [
                                'role_id' => $role->id,
                                'department_id' => $department->id,
                            ],
                            [
                                'category_id' => $category->id,
                                'is_active' => true,
                            ]
                        );
                    }
                }
            } else {
                // Category-wide mapping
                RoleDepartmentMapping::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'category_id' => $category->id,
                        'department_id' => null,
                    ],
                    [
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ Role department mappings seeded successfully');
    }
}
```

### 4. UserDepartmentRoleSeeder

```php
<?php
// database/seeders/UserDepartmentRoleSeeder.php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\UserDepartmentRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserDepartmentRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Get super admin user for assignments
        $superAdmin = User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first();

        if (!$superAdmin) {
            $this->command->error('❌ No Super Admin user found. Run SuperAdminUserSeeder first.');
            return;
        }

        // Example assignments - adjust based on your actual users
        $assignments = [
            [
                'email' => 'kitchen.staff@sweettooth.com',
                'role_name' => 'Kitchen Staff',
                'department_slug' => 'main-kitchen',
            ],
            [
                'email' => 'cashier@sweettooth.com',
                'role_name' => 'Cashier',
                'department_slug' => 'pos',
            ],
            [
                'email' => 'sales.manager@sweettooth.com',
                'role_name' => 'Sales Manager',
                'department_slug' => 'pos', // Can assign to any sales department
            ],
        ];

        foreach ($assignments as $assignment) {
            $user = User::where('email', $assignment['email'])->first();
            if (!$user) {
                $this->command->warn("⚠️ User '{$assignment['email']}' not found, skipping assignment");
                continue;
            }

            $department = Department::where('slug', $assignment['department_slug'])->first();
            if (!$department) {
                $this->command->warn("⚠️ Department '{$assignment['department_slug']}' not found, skipping assignment");
                continue;
            }

            try {
                $user->assignToDepartment(
                    $assignment['role_name'],
                    $department->id,
                    $superAdmin->id,
                    'Initial department assignment'
                );
                $this->command->info("✅ Assigned {$assignment['role_name']} to {$assignment['email']} in {$assignment['department_slug']}");
            } catch (\Exception $e) {
                $this->command->error("❌ Failed to assign role: {$e->getMessage()}");
            }
        }

        $this->command->info('✅ User department role assignments completed');
    }
}
```

## 🛡️ Middleware Implementation

### 1. DepartmentAccessMiddleware

```php
<?php
// app/Http/Middleware/DepartmentAccessMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DepartmentAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $categorySlug  The category slug to check access for
     */
    public function handle(Request $request, Closure $next, string $categorySlug): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Super Admin bypass
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user has a department assigned
        if (!$user->department) {
            abort(403, 'Access denied: You are not assigned to any department');
        }

        // Check if user's department belongs to the requested category
        if ($user->department->category->name !== ucfirst($categorySlug)) {
            abort(403, 'Access denied: You do not have permission to access this module');
        }

        // Validate that user's roles are appropriate for their department
        if (!$user->validateRoleForDepartment()) {
            abort(403, 'Access denied: Your role is not valid for your assigned department');
        }

        // Set active department in session for UI context
        session(['active_department_id' => $user->department->id]);

        return $next($request);
    }
}
```

### 2. Updated Kernel Registration

```php
// app/Http/Kernel.php

protected $middlewareAliases = [
    // ... existing middleware ...
    'department.access' => \App\Http\Middleware\DepartmentAccessMiddleware::class,
];
```

### 3. Route Usage Examples

```php
// routes/web.php

// Production routes - require production category access
Route::middleware(['auth', 'department.access:production'])->prefix('production')->group(function () {
    Route::get('/', [ProductionController::class, 'index']);
    Route::get('/orders', [ProductionController::class, 'orders'])->middleware('permission:create-production-order');
});

// Sales routes - require sales category access
Route::middleware(['auth', 'department.access:sales'])->prefix('sales')->group(function () {
    Route::get('/', [SalesController::class, 'index']);
    Route::post('/transaction', [SalesController::class, 'processTransaction'])->middleware('permission:process-sale');
});

// Inventory routes - require inventory category access
Route::middleware(['auth', 'department.access:inventory'])->prefix('inventory')->group(function () {
    Route::get('/stock', [InventoryController::class, 'stockLevels']);
    Route::post('/receive', [InventoryController::class, 'receiveStock'])->middleware('permission:receive-stock');
});
```

## 🔐 Authorization Logic

### 1. Enhanced Authorization Service

```php
<?php
// app/Services/Authorization/RbacService.php

namespace App\Services\Authorization;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class RbacService
{
    /**
     * Check if user can perform action in their department context
     */
    public function can(User $user, string $permission): bool
    {
        // Super Admin bypass
        if ($user->isSuperAdmin()) {
            return true;
        }

        // User must have a department assigned
        if (!$user->department) {
            return false;
        }

        // Validate role is appropriate for department
        if (!$user->validateRoleForDepartment()) {
            return false;
        }

        // Check Spatie permission
        return $user->can($permission);
    }

    /**
     * Get user's permissions in their department context
     */
    public function getDepartmentPermissions(User $user): array
    {
        if ($user->isSuperAdmin()) {
            return ['*']; // All permissions
        }

        if (!$user->department) {
            return [];
        }

        // Validate roles are appropriate, then return permissions
        if (!$user->validateRoleForDepartment()) {
            return [];
        }

        return $user->getPermissionsViaRoles()->pluck('name')->toArray();
    }

    /**
     * Validate role assignment for a user in a department
     */
    public function validateRoleAssignment(User $user, string $roleName, Department $department): bool
    {
        // Check if role is allowed in this department
        return $department->allowsRole($roleName);
    }

    /**
     * Get valid roles for a user in their current department
     */
    public function getValidRolesForUser(User $user): array
    {
        if (!$user->department) {
            return [];
        }

        return $user->getValidRolesForDepartment()->toArray();
    }

    /**
     * Check if user can manage another user's roles in the same department
     */
    public function canManageUserRoles(User $manager, User $targetUser): bool
    {
        // Super Admin can manage anyone
        if ($manager->isSuperAdmin()) {
            return true;
        }

        // Must be in same department
        if ($manager->department_id !== $targetUser->department_id) {
            return false;
        }

        // Check if manager has management role
        return $manager->hasRole(['Manager', 'Supervisor', 'Head']) ||
               str_contains($manager->roles->pluck('name')->implode(' '), 'Manager');
    }
}
```

### 2. Enhanced User Authorization

```php
<?php
// app/Services/Authorization/RbacService.php

namespace App\Services\Authorization;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class RbacService
{
    /**
     * Check if user can perform action in department context
     */
    public function can(User $user, string $permission, ?string $departmentId = null): bool
    {
        // Super Admin bypass
        if ($user->isSuperAdmin()) {
            return true;
        }

        // If department specified, check department access first
        if ($departmentId) {
            if (!$user->canAccessDepartment($departmentId)) {
                return false;
            }

            // Check if user has permission through their department role
            return $user->activeDepartmentRoles()
                ->where('department_id', $departmentId)
                ->whereHas('role.permissions', fn($q) => $q->where('name', $permission))
                ->exists();
        }

        // Global permission check (across all user's departments)
        return $user->activeDepartmentRoles()
            ->whereHas('role.permissions', fn($q) => $q->where('name', $permission))
            ->exists();
    }

    /**
     * Get user's permissions in a department
     */
    public function getDepartmentPermissions(User $user, string $departmentId): array
    {
        if ($user->isSuperAdmin()) {
            return ['*']; // All permissions
        }

        return $user->activeDepartmentRoles()
            ->where('department_id', $departmentId)
            ->with('role.permissions')
            ->get()
            ->pluck('role.permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get departments where user has specific permission
     */
    public function getDepartmentsWithPermission(User $user, string $permission): array
    {
        if ($user->isSuperAdmin()) {
            return Department::where('branch_id', $user->branch_id)->pluck('id')->toArray();
        }

        return $user->activeDepartmentRoles()
            ->whereHas('role.permissions', fn($q) => $q->where('name', $permission))
            ->pluck('department_id')
            ->toArray();
    }
}
```

## 🎨 UI Implementation

### 1. Department Context Component

Since users are assigned to specific departments, the department selector becomes simpler:

```php
<?php
// app/Livewire/Components/DepartmentContext.php

namespace App\Livewire\Components;

use Livewire\Component;

class DepartmentContext extends Component
{
    public function mount()
    {
        // Set the user's department context on mount
        $user = auth()->user();
        if ($user && $user->department) {
            session(['active_department_id' => $user->department->id]);
        }
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.components.department-context', [
            'currentDepartment' => $user?->department,
            'isValidRole' => $user?->validateRoleForDepartment() ?? false,
        ]);
    }
}
```

### Updated Department Context Blade Template

```blade
{{-- resources/views/livewire/components/department-context.blade.php --}}
<div>
    @if($currentDepartment)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-blue-900">
                        Current Department: {{ $currentDepartment->name }}
                    </h3>
                    <p class="text-xs text-blue-700 mt-1">
                        Category: {{ $currentDepartment->category->name }}
                    </p>
                </div>

                @if(!$isValidRole)
                    <div class="text-red-600 text-xs">
                        ⚠️ Role validation issue
                    </div>
                @else
                    <div class="text-green-600 text-xs">
                        ✓ Access validated
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-yellow-800">
                No department assigned. Please contact your administrator.
            </p>
        </div>
    @endif
</div>
```

### 2. Department Selector Blade Template

```blade
{{-- resources/views/livewire/components/department-selector.blade.php --}}
<div>
    @if(count($availableDepartments) > 1)
        <div class="mb-4">
            <label for="department-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Select Department
            </label>
            <select
                id="department-select"
                wire:model.live="selectedDepartmentId"
                wire:change="selectDepartment"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="">Choose a department...</option>
                @foreach($availableDepartments as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    @elseif(count($availableDepartments) === 1)
        <div class="mb-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Active Department: {{ array_values($availableDepartments)[0] }}
            </span>
        </div>
    @else
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
            <p class="text-sm text-yellow-800">
                No departments available. Please contact your administrator.
            </p>
        </div>
    @endif
</div>
```

### 3. User Department Assignment Interface

Since users are assigned to departments (not roles to departments), the interface focuses on department assignment and role validation:

```php
<?php
// app/Livewire/Admin/UserDepartmentManagement/Index.php

namespace App\Livewire\Admin\UserDepartmentManagement;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public ?string $selectedUserId = null;
    public ?string $selectedDepartmentId = null;
    public string $notes = '';

    protected $rules = [
        'selectedUserId' => 'required|exists:users,id',
        'selectedDepartmentId' => 'required|exists:departments,id',
        'notes' => 'nullable|string|max:500',
    ];

    public function assignUserToDepartment()
    {
        $this->validate();

        $user = User::find($this->selectedUserId);
        $department = Department::find($this->selectedDepartmentId);

        // Update user's department
        $user->update([
            'department_id' => $department->id,
        ]);

        // Validate that user's roles are appropriate for new department
        if (!$user->validateRoleForDepartment()) {
            // Get valid roles for the new department
            $validRoles = $user->getValidRolesForDepartment();

            // Remove invalid roles and assign valid ones if available
            $currentRoles = $user->roles->pluck('name');
            $invalidRoles = $currentRoles->diff($validRoles);

            foreach ($invalidRoles as $invalidRole) {
                $user->removeRole($invalidRole);
            }

            if ($validRoles->isNotEmpty()) {
                $user->assignRole($validRoles->first());
            }

            session()->flash('warning', 'User reassigned to department. Some roles were adjusted for compatibility.');
        } else {
            session()->flash('success', 'User assigned to department successfully');
        }

        $this->resetForm();
    }

    public function validateUserRoles()
    {
        $user = User::find($this->selectedUserId);

        if (!$user) {
            session()->flash('error', 'User not found');
            return;
        }

        $isValid = $user->validateRoleForDepartment();
        $validRoles = $user->getValidRolesForDepartment();

        if ($isValid) {
            session()->flash('success', 'User roles are valid for their department');
        } else {
            session()->flash('warning', 'User has roles that are not valid for their department. Valid roles: ' . $validRoles->join(', '));
        }
    }

    private function resetForm()
    {
        $this->selectedUserId = null;
        $this->selectedDepartmentId = null;
        $this->notes = '';
    }

    public function render()
    {
        return view('livewire.admin.user-department-management.index', [
            'users' => User::with(['department.category', 'roles'])
                ->paginate(20),
            'departments' => Department::active()
                ->with('category')
                ->get()
                ->mapWithKeys(fn($dept) => [$dept->id => "{$dept->name} ({$dept->category->name})"])
                ->toArray(),
        ]);
    }
}
```

## 🧪 Testing Examples

### 1. Unit Test for Department-Aware Authorization

```php
<?php
// tests/Unit/DepartmentAuthorizationTest.php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\User;
use App\Models\RoleCategoryConstraint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_department_role_validation()
    {
        // Create test data
        $salesCategory = DepartmentCategory::create(['name' => 'Sales']);
        $posDepartment = Department::create([
            'name' => 'POS',
            'slug' => 'pos',
            'category_id' => $salesCategory->id,
            'branch_id' => 'test-branch',
        ]);

        // Create role constraint
        RoleCategoryConstraint::create([
            'role_name' => 'Cashier',
            'category_id' => $salesCategory->id,
            'department_type' => 'specific',
            'allowed_department_slugs' => ['pos'],
        ]);

        // Create user assigned to POS department
        $user = User::factory()->create([
            'department_id' => $posDepartment->id,
        ]);
        $user->assignRole('Cashier');

        // Test validation
        $this->assertTrue($user->validateRoleForDepartment());
        $this->assertTrue($posDepartment->allowsRole('Cashier'));
    }

    public function test_invalid_role_assignment_detected()
    {
        // Create test data
        $salesCategory = DepartmentCategory::create(['name' => 'Sales']);
        $posDepartment = Department::create([
            'name' => 'POS',
            'slug' => 'pos',
            'category_id' => $salesCategory->id,
            'branch_id' => 'test-branch',
        ]);

        // Create user with wrong role for department
        $user = User::factory()->create([
            'department_id' => $posDepartment->id,
        ]);
        $user->assignRole('Chef'); // Chef not allowed in POS

        // Test validation fails
        $this->assertFalse($user->validateRoleForDepartment());
        $this->assertFalse($posDepartment->allowsRole('Chef'));
    }

    public function test_super_admin_bypasses_validation()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        // Even without department, super admin should pass basic checks
        $this->assertTrue($user->isSuperAdmin());
    }
}
```

## 🎯 Migration Strategy

### Phase 1: Database Extensions
```bash
# Run new migrations to extend existing tables
php artisan migrate

# Seed role constraints (existing categories/departments should already be seeded)
php artisan db:seed --class=RoleCategoryConstraintSeeder
```

### Phase 2: User Department Assignment
```bash
# Assign existing users to appropriate departments
# This may require manual assignment or a data migration script
php artisan tinker --execute="
User::whereNull('department_id')->get()->each(function(\$user) {
    // Assign based on existing roles or business logic
    \$department = Department::where('slug', 'pos')->first();
    if(\$department) {
        \$user->update(['department_id' => \$department->id]);
    }
});
"
```

### Phase 3: Role Validation & Cleanup
```bash
# Validate and fix role assignments
php artisan tinker --execute="
User::with('department')->get()->each(function(\$user) {
    if(\$user->department && !\$user->validateRoleForDepartment()) {
        \$validRoles = \$user->getValidRolesForDepartment();
        // Remove invalid roles and assign valid ones
        \$user->roles()->detach();
        if(\$validRoles->isNotEmpty()) {
            \$user->assignRole(\$validRoles->first());
        }
    }
});
"
```

### Phase 4: Feature Rollout
- Deploy middleware and model changes
- Update routes to use department access control
- Enable department context components
- Monitor for any access issues

## 📊 Monitoring & Auditing

### 1. Access Log Middleware

```php
<?php
// app/Http/Middleware/AccessLogger.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccessLogger
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $departmentId = session('active_department_id');

        Log::info('Department Access', [
            'user_id' => $user?->id,
            'department_id' => $departmentId,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}
```

### 2. Department Access Report

```php
<?php
// app/Console/Commands/DepartmentAccessReport.php

namespace App\Console\Commands;

use App\Models\UserDepartmentRole;
use Illuminate\Console\Command;

class DepartmentAccessReport extends Command
{
    protected $signature = 'report:department-access {--days=30}';
    protected $description = 'Generate department access report';

    public function handle()
    {
        $days = $this->option('days');

        $assignments = UserDepartmentRole::with(['user', 'role', 'department'])
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        $this->table(
            ['User', 'Role', 'Department', 'Assigned At', 'Assigned By'],
            $assignments->map(fn($assignment) => [
                $assignment->user->name,
                $assignment->role->name,
                $assignment->department->name,
                $assignment->assigned_at->format('Y-m-d H:i'),
                $assignment->assignedBy?->name ?? 'System',
            ])
        );
    }
}
```

This implementation provides a complete advanced RBAC system with contextual access control, maintaining backward compatibility while adding powerful new capabilities for department-level security.</content>
<parameter name="filePath">md/advance_RBAS/03-implementation.md