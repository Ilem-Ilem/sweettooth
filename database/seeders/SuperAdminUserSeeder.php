<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Create a super admin user for the web guard
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sweettooth.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // Change this in production!
                'is_active' => true,
            ]
        );

        // Assign Super Admin role from web guard
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first();

        if ($superAdminRole) {
            $superAdmin->syncRoles([$superAdminRole]);
            $this->command->info('✅ Super Admin user created/verified: '.$superAdmin->email);
            $this->command->warn('⚠️  Default password is "password" - CHANGE THIS IN PRODUCTION!');
        } else {
            $this->command->error('❌ Super Admin role not found. Run RoleSeeder first!');
        }
    }
}
