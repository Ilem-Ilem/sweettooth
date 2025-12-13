<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProtectedRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder marks critical system roles as protected to prevent accidental deletion.
     * Should be run after initial RoleSeeder to update protection flags.
     */
    public function run(): void
    {
        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Mark critical system roles as protected
        $protectedRoles = [
            'Super Admin' => 'Full system access with all permissions. Cannot be deleted.',
            'MD' => 'Managing Director - Executive level with full operational control. Cannot be deleted.',
            'Managing Director' => 'Executive level with full operational control. Cannot be deleted.',
            'Admin' => 'Administrative access to system settings. Cannot be deleted.',
        ];

        foreach ($protectedRoles as $roleName => $description) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                $role->update([
                    'is_protected' => true,
                    'description' => $description,
                    'display_order' => $this->getRoleOrder($roleName),
                ]);

                $this->command->info("✅ Marked '{$roleName}' as protected");
            } else {
                $this->command->warn("⚠️ Role '{$roleName}' not found - skipping");
            }
        }

        // Mark critical system permissions as protected
        $protectedPermissions = [
            'delete-roles',
            'delete-permissions',
            'edit-roles',
            'edit-permissions',
        ];

        foreach ($protectedPermissions as $permName) {
            $permission = Permission::where('name', $permName)->first();

            if ($permission) {
                $permission->update([
                    'is_protected' => true,
                    'category' => 'system',
                ]);

                $this->command->info("✅ Marked permission '{$permName}' as protected");
            }
        }

        $this->command->info('✅ Protected roles and permissions marked successfully');
    }

    /**
     * Get display order for a role
     */
    private function getRoleOrder(string $roleName): int
    {
        $order = [
            'Super Admin' => 1,
            'MD' => 2,
            'Managing Director' => 3,
            'Admin' => 4,
        ];

        return $order[$roleName] ?? 0;
    }
}
