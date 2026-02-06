<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EnsureSuperAdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if (!$superAdminRole) {
            $this->command->error('Super Admin role not found!');
            return;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        
        // Sync all permissions to Super Admin role
        $superAdminRole->syncPermissions($allPermissions);
        
        $this->command->info("Super Admin role now has all {$allPermissions->count()} permissions.");
        
        // Also ensure the Super Admin user exists and has the role
        $superAdminUser = User::where('email', 'admin@sweettooth.local')->first();
        
        if ($superAdminUser) {
            if (!$superAdminUser->hasRole('Super Admin')) {
                $superAdminUser->assignRole('Super Admin');
                $this->command->info('Super Admin user assigned Super Admin role.');
            }
        } else {
            $this->command->error('Super Admin user not found!');
        }
    }
}