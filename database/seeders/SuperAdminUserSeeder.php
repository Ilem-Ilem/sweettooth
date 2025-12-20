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
            ['email' => 'md@gmail.com'],
            [
                'name' => 'Managing Director',
                'password' => bcrypt('password'), // Change this in production!
            ]
        );

        // Assign Super Admin role from web guard
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first();

        if ($superAdminRole && !$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        $this->command->info('✅ Super Admin user created/verified: admin@sweettooth.local');
        $this->command->warn('⚠️  Default password is "password" - CHANGE THIS IN PRODUCTION!');
    }
}
