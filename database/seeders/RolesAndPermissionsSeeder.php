<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data in development
        if (app()->environment(['local', 'development'])) {
            $this->command->info('Clearing existing roles and permissions...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('role_has_permissions')->truncate();
            DB::table('model_has_roles')->truncate();
            DB::table('model_has_permissions')->truncate();
            DB::table('permissions')->truncate();
            DB::table('roles')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Create all permissions
        $permissions = $this->getAllPermissions();
        $this->command->info('Creating permissions...');

        $permissionBar = $this->command->getOutput()->createProgressBar(count($permissions));
        $permissionBar->start();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                ['description' => $permission['description'] ?? null]
            );
            $permissionBar->advance();
        }
        $permissionBar->finish();
        $this->command->newLine();

        // Create roles and assign permissions
        $roles = $this->getRolesWithPermissions();
        $this->command->info('Creating roles and assigning permissions...');

        $roleBar = $this->command->getOutput()->createProgressBar(count($roles));
        $roleBar->start();

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate([
                'name' => $roleData['name'],
                'guard_name' => 'web'
            ]);

            if (isset($roleData['permissions'])) {
                // Handle wildcard permissions for super-admin
                if (strtolower($roleData['name']) === 'super-admin' || strtolower($roleData['name']) === 'super admin') {
                    if (in_array('*', $roleData['permissions'])) {
                        // Give all permissions to super-admin
                        $allPermissions = Permission::all();
                        $role->givePermissionTo($allPermissions);
                    }
                } else {
                    $role->givePermissionTo($roleData['permissions']);
                }
            }

            $roleBar->advance();
        }
        $roleBar->finish();
        $this->command->newLine();

        $this->command->info('✅ Roles and permissions seeded successfully!');
    }

    protected function getAllPermissions(): array
    {
        return [
            // User Management
            ['name' => 'view.users', 'description' => 'View user list'],
            ['name' => 'create.users', 'description' => 'Create new users'],
            ['name' => 'edit.users', 'description' => 'Edit user details'],
            ['name' => 'delete.users', 'description' => 'Delete users'],
            ['name' => 'view.all.users', 'description' => 'View users across all branches'],

            // Branch Management
            ['name' => 'view.branches', 'description' => 'View branch list'],
            ['name' => 'create.branches', 'description' => 'Create new branches'],
            ['name' => 'edit.branches', 'description' => 'Edit branch details'],
            ['name' => 'delete.branches', 'description' => 'Delete branches'],

            // Department Management
            ['name' => 'view.departments', 'description' => 'View department list'],
            ['name' => 'create.departments', 'description' => 'Create new departments'],
            ['name' => 'edit.departments', 'description' => 'Edit department details'],
            ['name' => 'delete.departments', 'description' => 'Delete departments'],

            // Sales Management
            ['name' => 'view.sales', 'description' => 'View sales records'],
            ['name' => 'create.sales', 'description' => 'Create sales records'],
            ['name' => 'edit.sales', 'description' => 'Edit sales records'],
            ['name' => 'delete.sales', 'description' => 'Delete sales records'],
            ['name' => 'view.all.sales', 'description' => 'View sales across all branches'],
            ['name' => 'edit.all.sales', 'description' => 'Edit sales across all branches'],
            ['name' => 'delete.all.sales', 'description' => 'Delete sales across all branches'],
            ['name' => 'edit.own.branch.sales', 'description' => 'Edit sales in own branch only'],
            ['name' => 'delete.own.branch.sales', 'description' => 'Delete sales in own branch only'],

            // Inventory Management
            ['name' => 'view.inventory', 'description' => 'View inventory'],
            ['name' => 'create.inventory', 'description' => 'Add inventory items'],
            ['name' => 'edit.inventory', 'description' => 'Edit inventory items'],
            ['name' => 'delete.inventory', 'description' => 'Delete inventory items'],
            ['name' => 'view.all.inventory', 'description' => 'View inventory across all branches'],
            ['name' => 'adjust.inventory', 'description' => 'Adjust inventory levels'],

            // Production Management
            ['name' => 'view.production', 'description' => 'View production records'],
            ['name' => 'create.production', 'description' => 'Create production records'],
            ['name' => 'edit.production', 'description' => 'Edit production records'],
            ['name' => 'delete.production', 'description' => 'Delete production records'],
            ['name' => 'view.all.production', 'description' => 'View production across all branches'],

            // Recipe Management
            ['name' => 'view.recipes', 'description' => 'View recipes'],
            ['name' => 'create.recipes', 'description' => 'Create recipes'],
            ['name' => 'edit.recipes', 'description' => 'Edit recipes'],
            ['name' => 'delete.recipes', 'description' => 'Delete recipes'],

            // Reports
            ['name' => 'view.reports', 'description' => 'View reports'],
            ['name' => 'generate.reports', 'description' => 'Generate new reports'],
            ['name' => 'export.reports', 'description' => 'Export report data'],
            ['name' => 'view.all.reports', 'description' => 'View reports across all branches'],

            // Settings
            ['name' => 'view.settings', 'description' => 'View system settings'],
            ['name' => 'edit.settings', 'description' => 'Edit system settings'],

            // Leave Management
            ['name' => 'view.leave', 'description' => 'View leave applications'],
            ['name' => 'create.leave', 'description' => 'Create leave applications'],
            ['name' => 'approve.leave', 'description' => 'Approve leave applications'],
            ['name' => 'reject.leave', 'description' => 'Reject leave applications'],
            ['name' => 'view.all.leave', 'description' => 'View leave across all branches'],

            // Clock In/Out
            ['name' => 'view.attendance', 'description' => 'View attendance records'],
            ['name' => 'clock.in', 'description' => 'Clock in/out functionality'],
            ['name' => 'view.all.attendance', 'description' => 'View attendance across all branches'],

            // Audit
            ['name' => 'view.audit.logs', 'description' => 'View audit logs'],
            ['name' => 'view.all.audit.logs', 'description' => 'View audit logs across all branches'],

            // Approvals
            ['name' => 'view.approvals', 'description' => 'View approval requests'],
            ['name' => 'approve.requests', 'description' => 'Approve requests'],
            ['name' => 'reject.requests', 'description' => 'Reject requests'],
        ];
    }

    protected function getRolesWithPermissions(): array
    {
        return [
            [
                'name' => 'Super Admin',
                'permissions' => ['*'], // All permissions
            ],
            [
                'name' => 'admin',
                'permissions' => [
                    // User management
                    'view.users', 'create.users', 'edit.users', 'delete.users', 'view.all.users',

                    // Branch & department management
                    'view.branches', 'create.branches', 'edit.branches', 'delete.branches',
                    'view.departments', 'create.departments', 'edit.departments', 'delete.departments',

                    // Sales management
                    'view.sales', 'create.sales', 'edit.sales', 'delete.sales',
                    'view.all.sales', 'edit.all.sales', 'delete.all.sales',

                    // Inventory management
                    'view.inventory', 'create.inventory', 'edit.inventory', 'delete.inventory',
                    'view.all.inventory', 'adjust.inventory',

                    // Production management
                    'view.production', 'create.production', 'edit.production', 'delete.production',
                    'view.all.production',

                    // Recipe management
                    'view.recipes', 'create.recipes', 'edit.recipes', 'delete.recipes',

                    // Reports
                    'view.reports', 'generate.reports', 'export.reports', 'view.all.reports',

                    // Settings
                    'view.settings', 'edit.settings',

                    // Leave management
                    'view.leave', 'create.leave', 'approve.leave', 'reject.leave', 'view.all.leave',

                    // Attendance
                    'view.attendance', 'view.all.attendance',

                    // Audit
                    'view.audit.logs', 'view.all.audit.logs',

                    // Approvals
                    'view.approvals', 'approve.requests', 'reject.requests',
                ],
            ],
            [
                'name' => 'branch-manager',
                'permissions' => [
                    // Limited user management (own branch)
                    'view.users', 'edit.users',

                    // Branch viewing (own branch only)
                    'view.branches',

                    // Department management (own branch)
                    'view.departments', 'create.departments', 'edit.departments',

                    // Sales management (own branch)
                    'view.sales', 'create.sales', 'edit.own.branch.sales', 'delete.own.branch.sales',

                    // Inventory management (own branch)
                    'view.inventory', 'create.inventory', 'edit.inventory', 'adjust.inventory',

                    // Production management (own branch)
                    'view.production', 'create.production', 'edit.production',
                    'view.all.production',

                    // Recipe viewing
                    'view.recipes',

                    // Reports (own branch)
                    'view.reports', 'generate.reports', 'export.reports',

                    // Leave management (own branch)
                    'view.leave', 'create.leave', 'approve.leave', 'reject.leave',

                    // Attendance (own branch)
                    'view.attendance',

                    // Audit (own branch)
                    'view.audit.logs',

                    // Approvals (own branch)
                    'view.approvals', 'approve.requests', 'reject.requests',
                ],
            ],
            [
                'name' => 'supervisor',
                'permissions' => [
                    // Limited user viewing
                    'view.users',

                    // Branch viewing
                    'view.branches',

                    // Department viewing
                    'view.departments',

                    // Sales management (own branch, limited editing)
                    'view.sales', 'create.sales', 'edit.own.branch.sales',

                    // Inventory management (viewing only)
                    'view.inventory', 'edit.inventory',

                    // Production viewing
                    'view.production',

                    // Recipe viewing
                    'view.recipes',

                    // Reports (own branch)
                    'view.reports', 'generate.reports',

                    // Leave management (own team)
                    'view.leave', 'create.leave', 'approve.leave', 'reject.leave',

                    // Attendance (own team)
                    'view.attendance',

                    // Audit (limited)
                    'view.audit.logs',

                    // Approvals (limited)
                    'view.approvals', 'approve.requests', 'reject.requests',
                ],
            ],
            [
                'name' => 'employee',
                'permissions' => [
                    // Basic viewing
                    'view.branches',
                    'view.departments',

                    // Sales (own entries)
                    'view.sales', 'create.sales',

                    // Inventory viewing
                    'view.inventory',

                    // Production viewing
                    'view.production',

                    // Recipe viewing
                    'view.recipes',

                    // Reports (limited)
                    'view.reports',

                    // Leave management (own)
                    'view.leave', 'create.leave',

                    // Clock in/out
                    'clock.in',

                    // Attendance (own)
                    'view.attendance',
                ],
            ],
        ];
    }
}