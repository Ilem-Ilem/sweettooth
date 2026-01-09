# Implementation Plan

## Phase 1: Database Changes

### 1.1 Create New Simplified Roles
```sql
-- New roles table will have only 5 roles
INSERT INTO roles (name, guard_name, description, level) VALUES
('Super Admin', 'web', 'Full system access', 5),
('Admin', 'web', 'Branch-wide access', 4),
('Manager', 'web', 'Department manager', 3),
('Supervisor', 'web', 'Department supervisor', 2),
('Staff', 'web', 'Department worker', 1);
```

### 1.2 Ensure Users Have department_id
```sql
-- All users MUST have a department_id (except Super Admin)
ALTER TABLE users
MODIFY COLUMN department_id UUID NOT NULL;

-- Super Admin exception handled in code
```

### 1.3 Create Role Level Column (if not exists)
```sql
ALTER TABLE roles ADD COLUMN level TINYINT DEFAULT 1;
```

---

## Phase 2: Create Department Scope Middleware

### 2.1 DepartmentScopeMiddleware.php
```php
class DepartmentScopeMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $deptSlug = $request->route('deptSlug')
                 ?? $request->route('salesDeptSlug');

        // Super Admin bypass
        if ($user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Admin can access all departments in their branch
        if ($user->hasRole('Admin')) {
            // Validate department belongs to user's branch
            $dept = Department::where('slug', $deptSlug)
                ->where('branch_id', $user->branch_id)
                ->first();

            if (!$dept) {
                abort(403, 'Department not in your branch');
            }
            return $next($request);
        }

        // Manager/Supervisor/Staff - must match their department
        $userDept = $user->department;

        if (!$userDept || $userDept->slug !== $deptSlug) {
            abort(403, 'You can only access your own department');
        }

        return $next($request);
    }
}
```

### 2.2 Register Middleware
```php
// bootstrap/app.php or Kernel.php
'department.scope' => \App\Http\Middleware\DepartmentScopeMiddleware::class,
```

---

## Phase 3: Update Routes

### 3.1 Production Routes
```php
Route::middleware(['auth', 'department.scope'])
    ->prefix('production/{deptSlug}')
    ->group(function () {
        Route::get('/daily-produce', [DailyProduceController::class, 'index']);
        Route::get('/recipes', [RecipeController::class, 'index']);
        Route::get('/shift-closing', [ShiftClosingController::class, 'index']);
        // ... etc
    });
```

### 3.2 Sales Routes
```php
Route::middleware(['auth', 'department.scope'])
    ->prefix('sales/{deptSlug}')
    ->group(function () {
        Route::get('/pos', [POSController::class, 'index']);
        Route::get('/my-sales', [MySalesController::class, 'index']);
        Route::get('/shift-closing', [ShiftClosingController::class, 'index']);
        // ... etc
    });
```

---

## Phase 4: Update Sidebar Logic

### 4.1 New SidebarVisibilityService.php
```php
class SidebarVisibilityService
{
    public static function getVisibleSections(User $user): array
    {
        $dept = $user->department;
        $category = $dept?->category?->name;
        $roleLevel = self::getRoleLevel($user);

        return [
            'production' => $category === 'Production' || $roleLevel >= 4,
            'sales' => $category === 'Sales' || $roleLevel >= 4,
            'inventory' => $category === 'Support' && $dept?->name === 'Inventory/Store' || $roleLevel >= 4,
            'hr' => $category === 'Support' && $dept?->name === 'HR' || $roleLevel >= 4,
            'administration' => $roleLevel >= 4,
            'reports' => $roleLevel >= 3,
        ];
    }

    public static function getRoleLevel(User $user): int
    {
        if ($user->hasRole('Super Admin')) return 5;
        if ($user->hasRole('Admin')) return 4;
        if ($user->hasRole('Manager')) return 3;
        if ($user->hasRole('Supervisor')) return 2;
        return 1; // Staff
    }

    public static function getUserDepartments(User $user): Collection
    {
        $roleLevel = self::getRoleLevel($user);

        // Super Admin sees all
        if ($roleLevel >= 5) {
            return Department::all();
        }

        // Admin sees all in branch
        if ($roleLevel >= 4) {
            return Department::where('branch_id', $user->branch_id)->get();
        }

        // Manager sees all in same category
        if ($roleLevel >= 3) {
            $category = $user->department?->category_id;
            return Department::where('category_id', $category)
                ->where('branch_id', $user->branch_id)
                ->get();
        }

        // Supervisor/Staff see only their department
        return collect([$user->department]);
    }
}
```

---

## Phase 5: Update Dashboard Router

### 5.1 New Router Logic
```php
class Router extends Component
{
    public function mount()
    {
        $user = Auth::user();
        $dept = $user->department;
        $category = $dept?->category?->name;

        // Super Admin -> admin dashboard
        if ($user->hasRole('Super Admin')) {
            return Redirect::route('admin.dashboard');
        }

        // Route by department category
        return match($category) {
            'Production' => Redirect::route('production.dashboard', [
                'deptSlug' => $dept->slug
            ]),
            'Sales' => Redirect::route('sales.dashboard', [
                'deptSlug' => $dept->slug
            ]),
            'Support' => $this->routeSupportDepartment($user, $dept),
            default => abort(403, 'No department assigned')
        };
    }

    private function routeSupportDepartment($user, $dept)
    {
        return match($dept->name) {
            'HR' => Redirect::route('hr.dashboard'),
            'Inventory/Store' => Redirect::route('inventory.dashboard'),
            'Accounting' => Redirect::route('accounting.dashboard'),
            default => Redirect::route('general.dashboard')
        };
    }
}
```

---

## Phase 6: Migration Script for Existing Users

### 6.1 Map Old Roles to New
```php
class MigrateRolesToSimplified extends Command
{
    protected $roleMapping = [
        // Old Role => New Role
        'Super Admin' => 'Super Admin',
        'MD' => 'Super Admin',
        'Managing Director' => 'Super Admin',
        'Admin' => 'Admin',

        // Production Managers
        'Head of Production' => 'Manager',
        'Chef' => 'Manager',
        'Head of Gelato' => 'Manager',
        'Confectioneries Manager' => 'Manager',

        // Production Staff
        'Kitchen Staff' => 'Staff',
        'Gelato Production Staff' => 'Staff',
        'Confectioneries Production Staff' => 'Staff',

        // Sales Managers
        'Sales Manager' => 'Manager',
        'Corner Store Manager' => 'Manager',

        // Sales Supervisors
        'Till Supervisor' => 'Supervisor',
        'Sales Supervisor' => 'Supervisor',

        // Sales Staff
        'Cashier' => 'Staff',
        'Junior Cashier' => 'Staff',
        'Corner Store Staff' => 'Staff',
        'Sales Associate' => 'Staff',

        // HR
        'HR Manager' => 'Manager',
        'HR Officer' => 'Staff',

        // Inventory
        'Inventory Manager' => 'Manager',
        'Stock Controller' => 'Supervisor',
        'Store Keeper' => 'Staff',

        // Generic
        'Supervisor' => 'Supervisor',
        'Employee' => 'Staff',
        'Viewer' => 'Staff',
    ];

    public function handle()
    {
        foreach (User::all() as $user) {
            $oldRole = $user->roles->first()?->name;
            $newRole = $this->roleMapping[$oldRole] ?? 'Staff';

            $user->syncRoles([$newRole]);
            $this->info("User {$user->email}: {$oldRole} -> {$newRole}");
        }
    }
}
```

---

## Phase 7: Update Blade Templates

### 7.1 Sidebar Template Changes
```blade
@php
    $visibility = SidebarVisibilityService::getVisibleSections(auth()->user());
    $userDepts = SidebarVisibilityService::getUserDepartments(auth()->user());
    $currentDept = auth()->user()->department;
@endphp

{{-- Production Section --}}
@if($visibility['production'])
    <flux:navlist.group heading="Production">
        @foreach($userDepts->where('category.name', 'Production') as $dept)
            <flux:navlist.item
                href="{{ route('production.daily-produce', ['deptSlug' => $dept->slug]) }}"
                :current="request()->is('*/production/'.$dept->slug.'/*')">
                {{ $dept->name }}
            </flux:navlist.item>
        @endforeach
    </flux:navlist.group>
@endif

{{-- Sales Section --}}
@if($visibility['sales'])
    <flux:navlist.group heading="Sales">
        @foreach($userDepts->where('category.name', 'Sales') as $dept)
            <flux:navlist.item
                href="{{ route('sales.pos', ['deptSlug' => $dept->slug]) }}"
                :current="request()->is('*/sales/'.$dept->slug.'/*')">
                {{ $dept->name }}
            </flux:navlist.item>
        @endforeach
    </flux:navlist.group>
@endif
```

---

## Implementation Order

1. **Create new roles** (keep old ones temporarily)
2. **Create DepartmentScopeMiddleware**
3. **Update routes** to use middleware
4. **Update SidebarVisibilityService**
5. **Update Dashboard Router**
6. **Run migration script** to convert users
7. **Update Blade templates**
8. **Test thoroughly**
9. **Delete old roles**

---

## Rollback Plan

If issues arise:
1. Old roles are kept until final cleanup
2. Users can be reverted to old roles
3. Middleware can be disabled per-route
4. Feature flag for new vs old system
