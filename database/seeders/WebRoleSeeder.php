<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WebRoleSeeder extends Seeder
{
    /**
     * Create roles for the web guard (Super Admin users)
     */
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create or get Super Admin role for web guard
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => $guard,
        ], [
            'is_protected' => true,
            'description' => 'Full system access with all permissions. Cannot be deleted.',
            'display_order' => 1,
        ]);

        // Create or get MD role for web guard
        $md = Role::firstOrCreate([
            'name' => 'MD',
            'guard_name' => $guard,
        ], [
            'is_protected' => true,
            'description' => 'Managing Director - Executive level with full operational control. Cannot be deleted.',
            'display_order' => 2,
        ]);

        // Create or get Managing Director role for web guard
        $managingDirector = Role::firstOrCreate([
            'name' => 'Managing Director',
            'guard_name' => $guard,
        ], [
            'is_protected' => true,
            'description' => 'Executive level with full operational control. Cannot be deleted.',
            'display_order' => 3,
        ]);

        // Create or get Admin role for web guard
        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => $guard,
        ], [
            'is_protected' => true,
            'description' => 'Administrative access to system settings. Cannot be deleted.',
            'display_order' => 4,
        ]);

        $this->command->info('✅ Web guard roles created/verified successfully.');
    }
}
