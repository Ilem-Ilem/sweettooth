<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class EnsureSuperAdminSeeder extends Seeder
{
    /**
     * Ensure admin@sweettooth.local has Super Admin role.
     */
    public function run(): void
    {
        $email = 'admin@sweettooth.local';
        $guard = 'web';

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->command->error("User {$email} not found.");
            return;
        }

        $role = Role::where('name', 'Super Admin')
            ->where('guard_name', $guard)
            ->first();

        if (! $role) {
            $this->command->error('Super Admin role not found. Run RoleSeeder first.');
            return;
        }

        $user->syncRoles([$role]);
        $this->command->info("Assigned Super Admin role to {$email}.");
    }
}
