# Database Changes

## Overview

This document outlines all database changes needed for the simplified RBAC system.

---

## 1. New Simplified Roles

### Migration: `create_simplified_roles_table.php`

```php
public function up()
{
    // Add level column to roles if not exists
    if (!Schema::hasColumn('roles', 'level')) {
        Schema::table('roles', function (Blueprint $table) {
            $table->tinyInteger('level')->default(1)->after('guard_name');
        });
    }

    // Create the 5 new simplified roles
    $roles = [
        ['name' => 'Super Admin', 'guard_name' => 'web', 'level' => 5, 'description' => 'Full system access, all branches'],
        ['name' => 'Admin', 'guard_name' => 'web', 'level' => 4, 'description' => 'Branch-wide access'],
        ['name' => 'Manager', 'guard_name' => 'web', 'level' => 3, 'description' => 'Department manager'],
        ['name' => 'Supervisor', 'guard_name' => 'web', 'level' => 2, 'description' => 'Department supervisor'],
        ['name' => 'Staff', 'guard_name' => 'web', 'level' => 1, 'description' => 'Department worker'],
    ];

    foreach ($roles as $role) {
        \Spatie\Permission\Models\Role::updateOrCreate(
            ['name' => $role['name'], 'guard_name' => $role['guard_name']],
            $role
        );
    }
}
```

---

## 2. Users Table Changes

### Current State:
```sql
users
- id (UUID)
- name
- email
- branch_id (UUID, nullable)
- department_id (UUID, nullable)  -- THIS NEEDS TO BE REQUIRED
- ...
```

### Required Change:
```php
// Migration: enforce_user_department.php
public function up()
{
    // First, ensure all users have a department
    // Super Admins can have NULL department

    Schema::table('users', function (Blueprint $table) {
        // Add index for faster queries
        $table->index('department_id');
    });
}
```

### Validation Rule (in User model):
```php
// Users must have department unless Super Admin
public static function rules($userId = null)
{
    return [
        'department_id' => [
            'required_unless:role,Super Admin',
            'exists:departments,id'
        ],
    ];
}
```

---

## 3. Departments Table (Already Exists)

### Current Structure:
```sql
departments
- id (UUID)
- branch_id (UUID)
- category_id (UUID)  -- Links to department_categories
- name (string)
- slug (string)
- description (text, nullable)
- is_active (boolean, default true)
- manager_user_id (UUID, nullable)
```

### No changes needed - structure is good.

---

## 4. Department Categories (Already Exists)

### Current Structure:
```sql
department_categories
- id (UUID)
- name (string)  -- 'Production', 'Sales', 'Support'
- description (text, nullable)
```

### No changes needed.

---

## 5. Role Mapping Table (New - Optional)

For tracking old role to new role conversion:

```php
// Migration: create_role_migration_log.php
public function up()
{
    Schema::create('role_migration_log', function (Blueprint $table) {
        $table->id();
        $table->uuid('user_id');
        $table->string('old_role');
        $table->string('new_role');
        $table->timestamp('migrated_at');

        $table->foreign('user_id')->references('id')->on('users');
    });
}
```

---

## 6. Simplified Permissions

### Current State: 50+ permissions

### New State: ~15 core permissions

```php
$permissions = [
    // Basic Operations
    'view-dashboard',
    'perform-operations',      // Generic: sales, production, etc based on dept

    // Department-Specific (determined by department category)
    'view-reports',
    'manage-staff-schedule',
    'approve-requests',
    'edit-department-settings',

    // Admin-Level
    'view-all-departments',
    'manage-users',
    'manage-departments',

    // Super Admin Only
    'manage-roles',
    'manage-branches',
    'system-settings',
    'view-audit-logs',
];
```

### Permission Assignment by Role Level:

```php
$rolePermissions = [
    'Staff' => [
        'view-dashboard',
        'perform-operations',
    ],
    'Supervisor' => [
        'view-dashboard',
        'perform-operations',
        'view-reports',
        'manage-staff-schedule',
    ],
    'Manager' => [
        'view-dashboard',
        'perform-operations',
        'view-reports',
        'manage-staff-schedule',
        'approve-requests',
        'edit-department-settings',
    ],
    'Admin' => [
        // All Manager permissions plus:
        'view-all-departments',
        'manage-users',
        'manage-departments',
    ],
    'Super Admin' => [
        // Everything
        '*',
    ],
];
```

---

## 7. Migration Script: Convert Existing Users

```php
// Command: php artisan roles:migrate-to-simplified

class MigrateToSimplifiedRoles extends Command
{
    protected $signature = 'roles:migrate-to-simplified {--dry-run}';

    private array $roleMapping = [
        // Level 5 - Super Admin
        'Super Admin' => ['role' => 'Super Admin', 'dept' => null],
        'MD' => ['role' => 'Super Admin', 'dept' => null],
        'Managing Director' => ['role' => 'Super Admin', 'dept' => null],

        // Level 4 - Admin
        'Admin' => ['role' => 'Admin', 'dept' => null],

        // Level 3 - Manager (Production)
        'Head of Production' => ['role' => 'Manager', 'dept' => 'Kitchen'],
        'Chef' => ['role' => 'Manager', 'dept' => 'Kitchen'],
        'Head of Gelato' => ['role' => 'Manager', 'dept' => 'Gelato Production'],
        'Confectioneries Manager' => ['role' => 'Manager', 'dept' => 'Confectioneries Production'],

        // Level 3 - Manager (Sales)
        'Sales Manager' => ['role' => 'Manager', 'dept' => 'Till'],
        'Corner Store Manager' => ['role' => 'Manager', 'dept' => 'Corner Store'],

        // Level 3 - Manager (Support)
        'HR Manager' => ['role' => 'Manager', 'dept' => 'HR'],
        'Inventory Manager' => ['role' => 'Manager', 'dept' => 'Inventory/Store'],

        // Level 2 - Supervisor
        'Till Supervisor' => ['role' => 'Supervisor', 'dept' => 'Till'],
        'Sales Supervisor' => ['role' => 'Supervisor', 'dept' => 'Till'],
        'Stock Controller' => ['role' => 'Supervisor', 'dept' => 'Inventory/Store'],
        'Supervisor' => ['role' => 'Supervisor', 'dept' => null], // Keep existing dept

        // Level 1 - Staff (Production)
        'Kitchen Staff' => ['role' => 'Staff', 'dept' => 'Kitchen'],
        'Gelato Production Staff' => ['role' => 'Staff', 'dept' => 'Gelato Production'],
        'Confectioneries Production Staff' => ['role' => 'Staff', 'dept' => 'Confectioneries Production'],

        // Level 1 - Staff (Sales)
        'Cashier' => ['role' => 'Staff', 'dept' => 'Till'],
        'Junior Cashier' => ['role' => 'Staff', 'dept' => 'Till'],
        'Corner Store Staff' => ['role' => 'Staff', 'dept' => 'Corner Store'],
        'Sales Associate' => ['role' => 'Staff', 'dept' => 'Till'],
        'Confectioneries Sales Staff' => ['role' => 'Staff', 'dept' => 'Confectioneries Sales'],

        // Level 1 - Staff (Support)
        'HR Officer' => ['role' => 'Staff', 'dept' => 'HR'],
        'Store Keeper' => ['role' => 'Staff', 'dept' => 'Inventory/Store'],

        // Generic
        'Employee' => ['role' => 'Staff', 'dept' => null],
        'Viewer' => ['role' => 'Staff', 'dept' => null],
        'Accountant' => ['role' => 'Staff', 'dept' => 'Accounting'],
    ];

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        User::with('roles', 'department')->chunk(100, function ($users) use ($dryRun) {
            foreach ($users as $user) {
                $oldRole = $user->roles->first()?->name;
                $mapping = $this->roleMapping[$oldRole] ?? ['role' => 'Staff', 'dept' => null];

                $newRole = $mapping['role'];
                $newDept = $mapping['dept'];

                // Determine department
                $departmentId = $user->department_id;
                if ($newDept && !$departmentId) {
                    $dept = Department::where('name', $newDept)->first();
                    $departmentId = $dept?->id;
                }

                $this->info("User: {$user->email}");
                $this->info("  Old Role: {$oldRole}");
                $this->info("  New Role: {$newRole}");
                $this->info("  Department: {$departmentId}");

                if (!$dryRun) {
                    $user->syncRoles([$newRole]);
                    if ($departmentId) {
                        $user->update(['department_id' => $departmentId]);
                    }

                    // Log the migration
                    DB::table('role_migration_log')->insert([
                        'user_id' => $user->id,
                        'old_role' => $oldRole ?? 'none',
                        'new_role' => $newRole,
                        'migrated_at' => now(),
                    ]);
                }
            }
        });

        $this->info($dryRun ? 'Dry run complete.' : 'Migration complete.');
    }
}
```

---

## 8. Cleanup: Remove Old Roles (After Testing)

```php
// Run ONLY after confirming new system works
// Command: php artisan roles:cleanup-old

public function handle()
{
    $keepRoles = ['Super Admin', 'Admin', 'Manager', 'Supervisor', 'Staff'];

    Role::whereNotIn('name', $keepRoles)->delete();

    $this->info('Old roles removed.');
}
```

---

## Summary of Database Changes

| Change | Type | Risk |
|--------|------|------|
| Add `level` to roles | ALTER TABLE | Low |
| Create 5 new roles | INSERT | Low |
| Add index on users.department_id | ALTER TABLE | Low |
| Create role_migration_log | CREATE TABLE | Low |
| Migrate user roles | UPDATE | Medium |
| Delete old roles | DELETE | High (do last) |
