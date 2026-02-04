<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AlignRolesPermissionsSeeder extends Seeder
{
    /**
     * Align super admin access and role-based permissions.
     */
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure role-permission mappings are up to date (role name -> permissions)
        $this->call(FixRolePermissionsSeeder::class);

        // Ensure Super Admin role exists and has all permissions
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', $guard)
            ->first();

        if ($superAdminRole) {
            $allPermissions = Permission::where('guard_name', $guard)->get();
            $superAdminRole->syncPermissions($allPermissions);

            if (Schema::hasColumn('roles', 'level')) {
                $superAdminRole->level = 5;
                $superAdminRole->save();
            }
        } else {
            $this->command->error('❌ Super Admin role not found. Run RoleSeeder first!');
        }

        // Assign Super Admin role to admin@sweettooth.local
        $superAdminUser = User::where('email', 'admin@sweettooth.local')->first();
        if ($superAdminUser && $superAdminRole) {
            $superAdminUser->syncRoles([$superAdminRole]);
            $this->command->info('✅ admin@sweettooth.local assigned Super Admin role.');
        } elseif (! $superAdminUser) {
            $this->command->warn('⚠️  admin@sweettooth.local not found.');
        }

        // Assign a default role to users without roles
        $defaultRole = Role::where('name', 'Employee')
            ->where('guard_name', $guard)
            ->first();

        if ($defaultRole) {
            User::doesntHave('roles')->each(function (User $user) use ($defaultRole) {
                $user->assignRole($defaultRole);
            });
        }
    }
}
