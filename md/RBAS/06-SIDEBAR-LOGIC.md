# Sidebar Logic Specification

## Overview

The sidebar must dynamically show menu items based on:
1. **User's department** - What category they belong to
2. **User's role level** - What actions they can perform
3. **Available departments** - What departments they can access

---

## New SidebarVisibilityService

### File Location
`app/Services/SidebarVisibilityService.php`

### Complete Implementation

```php
<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Collection;

class SidebarVisibilityService
{
    // Role levels
    private const LEVEL_SUPER_ADMIN = 5;
    private const LEVEL_ADMIN = 4;
    private const LEVEL_MANAGER = 3;
    private const LEVEL_SUPERVISOR = 2;
    private const LEVEL_STAFF = 1;

    /**
     * Get user's role level (1-5)
     */
    public static function getRoleLevel(User $user): int
    {
        if ($user->hasRole('Super Admin')) return self::LEVEL_SUPER_ADMIN;
        if ($user->hasRole('Admin')) return self::LEVEL_ADMIN;
        if ($user->hasRole('Manager')) return self::LEVEL_MANAGER;
        if ($user->hasRole('Supervisor')) return self::LEVEL_SUPERVISOR;
        return self::LEVEL_STAFF;
    }

    /**
     * Check if user is Super Admin
     */
    public static function isSuperAdmin(User $user): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_SUPER_ADMIN;
    }

    /**
     * Check if user is Admin or higher
     */
    public static function isAdmin(User $user): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_ADMIN;
    }

    /**
     * Check if user is Manager or higher
     */
    public static function isManager(User $user): bool
    {
        return self::getRoleLevel($user) >= self::LEVEL_MANAGER;
    }

    /**
     * Get user's department category name
     */
    public static function getDepartmentCategory(User $user): ?string
    {
        return $user->department?->category?->name;
    }

    /**
     * Get departments user can access in sidebar
     */
    public static function getAccessibleDepartments(User $user): Collection
    {
        $level = self::getRoleLevel($user);
        $branchId = session('current_branch_id') ?? $user->branch_id;

        // Super Admin sees all departments
        if ($level >= self::LEVEL_SUPER_ADMIN) {
            return Department::where('branch_id', $branchId)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->get();
        }

        // Admin sees all departments in their branch
        if ($level >= self::LEVEL_ADMIN) {
            return Department::where('branch_id', $branchId)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->get();
        }

        // Manager sees all departments in same category
        if ($level >= self::LEVEL_MANAGER) {
            $categoryId = $user->department?->category_id;
            return Department::where('branch_id', $branchId)
                ->where('category_id', $categoryId)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('name')
                ->get();
        }

        // Supervisor/Staff see only their department
        $dept = $user->department;
        return $dept ? collect([$dept->load('category')]) : collect();
    }

    /**
     * Get visible menu sections
     */
    public static function getVisibleSections(User $user): array
    {
        $level = self::getRoleLevel($user);
        $category = self::getDepartmentCategory($user);
        $deptName = $user->department?->name;

        return [
            // Main Sections
            'dashboard' => true, // Everyone sees dashboard

            // Department-specific sections
            'production' => $category === 'Production' || $level >= self::LEVEL_ADMIN,
            'sales' => $category === 'Sales' || $level >= self::LEVEL_ADMIN,
            'inventory' => ($category === 'Support' && $deptName === 'Inventory/Store') || $level >= self::LEVEL_ADMIN,
            'hr' => ($category === 'Support' && $deptName === 'HR') || $level >= self::LEVEL_ADMIN,
            'accounting' => ($category === 'Support' && $deptName === 'Accounting') || $level >= self::LEVEL_ADMIN,

            // Role-level sections
            'reports' => $level >= self::LEVEL_SUPERVISOR,
            'analytics' => $level >= self::LEVEL_SUPERVISOR,
            'staff_schedule' => $level >= self::LEVEL_SUPERVISOR,

            // Admin sections
            'organization' => $level >= self::LEVEL_ADMIN,
            'administration' => $level >= self::LEVEL_ADMIN,
            'user_management' => $level >= self::LEVEL_ADMIN,
            'department_management' => $level >= self::LEVEL_ADMIN,

            // Super Admin only
            'roles_permissions' => $level >= self::LEVEL_SUPER_ADMIN,
            'branch_management' => $level >= self::LEVEL_SUPER_ADMIN,
            'system_settings' => $level >= self::LEVEL_SUPER_ADMIN,
            'audit_logs' => $level >= self::LEVEL_SUPER_ADMIN,
        ];
    }

    /**
     * Get production menu items for user
     */
    public static function getProductionMenuItems(User $user): array
    {
        $level = self::getRoleLevel($user);
        $depts = self::getAccessibleDepartments($user)
            ->filter(fn($d) => $d->category?->name === 'Production');

        if ($depts->isEmpty()) {
            return [];
        }

        $items = [];

        foreach ($depts as $dept) {
            $deptItems = [
                [
                    'name' => 'Daily Produce',
                    'route' => 'production.daily-produce',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'clipboard-list',
                    'minLevel' => self::LEVEL_STAFF,
                ],
                [
                    'name' => 'Recipes',
                    'route' => 'production.recipes',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'book-open',
                    'minLevel' => self::LEVEL_STAFF,
                ],
                [
                    'name' => 'Shift Closing',
                    'route' => 'production.shift-closing',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'clock',
                    'minLevel' => self::LEVEL_STAFF,
                ],
            ];

            // Manager+ items
            if ($level >= self::LEVEL_MANAGER) {
                $deptItems[] = [
                    'name' => 'Products',
                    'route' => 'production.products',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'cube',
                    'minLevel' => self::LEVEL_MANAGER,
                ];
                $deptItems[] = [
                    'name' => 'Requests',
                    'route' => 'production.requests',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'inbox',
                    'minLevel' => self::LEVEL_MANAGER,
                ];
                $deptItems[] = [
                    'name' => 'Reports',
                    'route' => 'production.reports',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'chart-bar',
                    'minLevel' => self::LEVEL_MANAGER,
                ];
            }

            $items[] = [
                'department' => $dept,
                'items' => array_filter($deptItems, fn($item) => $level >= $item['minLevel']),
            ];
        }

        return $items;
    }

    /**
     * Get sales menu items for user
     */
    public static function getSalesMenuItems(User $user): array
    {
        $level = self::getRoleLevel($user);
        $depts = self::getAccessibleDepartments($user)
            ->filter(fn($d) => $d->category?->name === 'Sales');

        if ($depts->isEmpty()) {
            return [];
        }

        $items = [];

        foreach ($depts as $dept) {
            $deptItems = [
                [
                    'name' => 'POS',
                    'route' => 'sales.pos',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'shopping-cart',
                    'minLevel' => self::LEVEL_STAFF,
                ],
                [
                    'name' => 'My Sales',
                    'route' => 'sales.my-sales',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'currency-dollar',
                    'minLevel' => self::LEVEL_STAFF,
                ],
                [
                    'name' => 'Shift Closing',
                    'route' => 'sales.shift-closing',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'clock',
                    'minLevel' => self::LEVEL_STAFF,
                ],
            ];

            // Supervisor+ items
            if ($level >= self::LEVEL_SUPERVISOR) {
                $deptItems[] = [
                    'name' => 'Analytics',
                    'route' => 'sales.analytics',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'chart-pie',
                    'minLevel' => self::LEVEL_SUPERVISOR,
                ];
            }

            // Manager+ items
            if ($level >= self::LEVEL_MANAGER) {
                $deptItems[] = [
                    'name' => 'Stock Opening',
                    'route' => 'sales.stock-opening',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'archive',
                    'minLevel' => self::LEVEL_MANAGER,
                ];
                $deptItems[] = [
                    'name' => 'Reports',
                    'route' => 'sales.reports',
                    'params' => ['deptSlug' => $dept->slug],
                    'icon' => 'document-report',
                    'minLevel' => self::LEVEL_MANAGER,
                ];
            }

            $items[] = [
                'department' => $dept,
                'items' => array_filter($deptItems, fn($item) => $level >= $item['minLevel']),
            ];
        }

        return $items;
    }

    /**
     * Get inventory menu items
     */
    public static function getInventoryMenuItems(User $user): array
    {
        $level = self::getRoleLevel($user);

        $items = [
            [
                'name' => 'Stock Levels',
                'route' => 'inventory.stock-levels',
                'icon' => 'cube',
                'minLevel' => self::LEVEL_STAFF,
            ],
            [
                'name' => 'Purchases',
                'route' => 'inventory.purchases',
                'icon' => 'shopping-bag',
                'minLevel' => self::LEVEL_STAFF,
            ],
        ];

        if ($level >= self::LEVEL_SUPERVISOR) {
            $items[] = [
                'name' => 'Dispatches',
                'route' => 'inventory.dispatches',
                'icon' => 'truck',
                'minLevel' => self::LEVEL_SUPERVISOR,
            ];
            $items[] = [
                'name' => 'Health Check',
                'route' => 'inventory.health-check',
                'icon' => 'shield-check',
                'minLevel' => self::LEVEL_SUPERVISOR,
            ];
        }

        if ($level >= self::LEVEL_MANAGER) {
            $items[] = [
                'name' => 'Reports',
                'route' => 'inventory.reports',
                'icon' => 'chart-bar',
                'minLevel' => self::LEVEL_MANAGER,
            ];
            $items[] = [
                'name' => 'Suppliers',
                'route' => 'inventory.suppliers',
                'icon' => 'users',
                'minLevel' => self::LEVEL_MANAGER,
            ];
        }

        return array_filter($items, fn($item) => $level >= $item['minLevel']);
    }

    /**
     * Get HR menu items
     */
    public static function getHRMenuItems(User $user): array
    {
        $level = self::getRoleLevel($user);

        $items = [
            [
                'name' => 'Employees',
                'route' => 'hr.employees',
                'icon' => 'users',
                'minLevel' => self::LEVEL_STAFF,
            ],
            [
                'name' => 'Leave Requests',
                'route' => 'hr.leave-requests',
                'icon' => 'calendar',
                'minLevel' => self::LEVEL_STAFF,
            ],
        ];

        if ($level >= self::LEVEL_MANAGER) {
            $items[] = [
                'name' => 'Appraisals',
                'route' => 'hr.appraisals',
                'icon' => 'star',
                'minLevel' => self::LEVEL_MANAGER,
            ];
            $items[] = [
                'name' => 'Reports',
                'route' => 'hr.reports',
                'icon' => 'chart-bar',
                'minLevel' => self::LEVEL_MANAGER,
            ];
        }

        return array_filter($items, fn($item) => $level >= $item['minLevel']);
    }

    /**
     * Get admin menu items (Admin+ only)
     */
    public static function getAdminMenuItems(User $user): array
    {
        $level = self::getRoleLevel($user);

        if ($level < self::LEVEL_ADMIN) {
            return [];
        }

        $items = [
            [
                'name' => 'Users',
                'route' => 'admin.users',
                'icon' => 'users',
                'minLevel' => self::LEVEL_ADMIN,
            ],
            [
                'name' => 'Departments',
                'route' => 'admin.departments',
                'icon' => 'office-building',
                'minLevel' => self::LEVEL_ADMIN,
            ],
        ];

        // Super Admin only
        if ($level >= self::LEVEL_SUPER_ADMIN) {
            $items[] = [
                'name' => 'Roles & Permissions',
                'route' => 'admin.roles',
                'icon' => 'shield-check',
                'minLevel' => self::LEVEL_SUPER_ADMIN,
            ];
            $items[] = [
                'name' => 'Branches',
                'route' => 'admin.branches',
                'icon' => 'location-marker',
                'minLevel' => self::LEVEL_SUPER_ADMIN,
            ];
            $items[] = [
                'name' => 'Settings',
                'route' => 'admin.settings',
                'icon' => 'cog',
                'minLevel' => self::LEVEL_SUPER_ADMIN,
            ];
            $items[] = [
                'name' => 'Audit Logs',
                'route' => 'admin.audit-logs',
                'icon' => 'document-search',
                'minLevel' => self::LEVEL_SUPER_ADMIN,
            ];
        }

        return array_filter($items, fn($item) => $level >= $item['minLevel']);
    }
}
```

---

## Blade Template Usage

### Main Sidebar Template

```blade
{{-- resources/views/components/sidebar.blade.php --}}

@php
    use App\Services\SidebarVisibilityService;

    $user = auth()->user();
    $visibility = SidebarVisibilityService::getVisibleSections($user);
    $roleLevel = SidebarVisibilityService::getRoleLevel($user);
@endphp

<flux:sidebar sticky stashable>
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <flux:brand href="{{ route('dashboard') }}" name="SweetTooth" />

    <flux:navlist variant="outline">

        {{-- Dashboard --}}
        <flux:navlist.item icon="home" href="{{ route('dashboard') }}">
            Dashboard
        </flux:navlist.item>

        {{-- PRODUCTION SECTION --}}
        @if($visibility['production'])
            @php $productionMenu = SidebarVisibilityService::getProductionMenuItems($user); @endphp
            @if(count($productionMenu) > 0)
                <flux:navlist.group heading="Production" expandable>
                    @foreach($productionMenu as $deptMenu)
                        <flux:navlist.group heading="{{ $deptMenu['department']->name }}" expandable>
                            @foreach($deptMenu['items'] as $item)
                                <flux:navlist.item
                                    icon="{{ $item['icon'] }}"
                                    href="{{ route($item['route'], $item['params']) }}"
                                    :current="request()->routeIs($item['route'])">
                                    {{ $item['name'] }}
                                </flux:navlist.item>
                            @endforeach
                        </flux:navlist.group>
                    @endforeach
                </flux:navlist.group>
            @endif
        @endif

        {{-- SALES SECTION --}}
        @if($visibility['sales'])
            @php $salesMenu = SidebarVisibilityService::getSalesMenuItems($user); @endphp
            @if(count($salesMenu) > 0)
                <flux:navlist.group heading="Sales" expandable>
                    @foreach($salesMenu as $deptMenu)
                        <flux:navlist.group heading="{{ $deptMenu['department']->name }}" expandable>
                            @foreach($deptMenu['items'] as $item)
                                <flux:navlist.item
                                    icon="{{ $item['icon'] }}"
                                    href="{{ route($item['route'], $item['params']) }}"
                                    :current="request()->routeIs($item['route'])">
                                    {{ $item['name'] }}
                                </flux:navlist.item>
                            @endforeach
                        </flux:navlist.group>
                    @endforeach
                </flux:navlist.group>
            @endif
        @endif

        {{-- INVENTORY SECTION --}}
        @if($visibility['inventory'])
            @php $inventoryMenu = SidebarVisibilityService::getInventoryMenuItems($user); @endphp
            <flux:navlist.group heading="Inventory" expandable>
                @foreach($inventoryMenu as $item)
                    <flux:navlist.item
                        icon="{{ $item['icon'] }}"
                        href="{{ route($item['route']) }}"
                        :current="request()->routeIs($item['route'])">
                        {{ $item['name'] }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
        @endif

        {{-- HR SECTION --}}
        @if($visibility['hr'])
            @php $hrMenu = SidebarVisibilityService::getHRMenuItems($user); @endphp
            <flux:navlist.group heading="Human Resources" expandable>
                @foreach($hrMenu as $item)
                    <flux:navlist.item
                        icon="{{ $item['icon'] }}"
                        href="{{ route($item['route']) }}"
                        :current="request()->routeIs($item['route'])">
                        {{ $item['name'] }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
        @endif

        {{-- ADMINISTRATION (Admin+) --}}
        @if($visibility['administration'])
            @php $adminMenu = SidebarVisibilityService::getAdminMenuItems($user); @endphp
            <flux:navlist.group heading="Administration" expandable>
                @foreach($adminMenu as $item)
                    <flux:navlist.item
                        icon="{{ $item['icon'] }}"
                        href="{{ route($item['route']) }}"
                        :current="request()->routeIs($item['route'])">
                        {{ $item['name'] }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist.group>
        @endif

    </flux:navlist>

    <flux:spacer />

    {{-- User info --}}
    <flux:dropdown position="top" align="start">
        <flux:profile avatar="{{ auth()->user()->avatar_url }}" name="{{ auth()->user()->name }}" />
        <flux:menu>
            <flux:menu.item icon="user" href="{{ route('profile') }}">Profile</flux:menu.item>
            <flux:menu.separator />
            <flux:menu.item icon="logout" href="{{ route('logout') }}">Logout</flux:menu.item>
        </flux:menu>
    </flux:dropdown>

</flux:sidebar>
```

---

## Visual Examples

### Staff in Kitchen Department

```
Dashboard
Production
  └── Kitchen
       ├── Daily Produce
       ├── Recipes
       └── Shift Closing
```

### Manager in Kitchen Department

```
Dashboard
Production
  ├── Kitchen
  │    ├── Daily Produce
  │    ├── Recipes
  │    ├── Products
  │    ├── Requests
  │    ├── Reports
  │    └── Shift Closing
  ├── Gelato Production
  │    └── (same items)
  └── Confectioneries Production
       └── (same items)
```

### Admin (Branch Level)

```
Dashboard
Production
  ├── Kitchen
  ├── Gelato Production
  └── Confectioneries Production
Sales
  ├── Till
  ├── Corner Store
  └── Confectioneries Sales
Inventory
  ├── Stock Levels
  ├── Purchases
  └── ...
Human Resources
  ├── Employees
  └── ...
Administration
  ├── Users
  └── Departments
```

### Super Admin

```
Dashboard
Production (all)
Sales (all)
Inventory (all)
Human Resources (all)
Administration
  ├── Users
  ├── Departments
  ├── Roles & Permissions
  ├── Branches
  ├── Settings
  └── Audit Logs
```

---

## Summary

| Role Level | Production | Sales | Inventory | HR | Admin |
|------------|------------|-------|-----------|-----|-------|
| Staff (1) | Own dept only | Own dept only | If in Inv dept | If in HR dept | NO |
| Supervisor (2) | Own dept + reports | Own dept + analytics | If in Inv dept | If in HR dept | NO |
| Manager (3) | All in category | All in category | If in Inv dept | If in HR dept | NO |
| Admin (4) | All in branch | All in branch | YES | YES | Users, Depts |
| Super Admin (5) | ALL | ALL | ALL | ALL | ALL |
