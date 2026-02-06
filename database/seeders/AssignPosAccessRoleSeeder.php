<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignPosAccessRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a user to assign the role to (preferably one of the named users)
        $user = User::where('email', 'amina.okafor.5@sweettooth.com')
            ->orWhere('name', 'Amina Okafor')
            ->orWhere('email', 'admin@sweettooth.local') // In case we want to ensure Super Admin has all permissions
            ->first();

        if (!$user) {
            $this->command->error('No suitable user found to assign POS access role.');
            return;
        }

        // Define roles that should have POS access
        $posRoles = ['Super Admin', 'Admin', 'Sales Manager', 'Till Supervisor', 'Cashier', 'Junior Cashier'];

        // Check if user already has one of the POS roles
        $hasPosRole = false;
        foreach ($posRoles as $roleName) {
            if ($user->hasRole($roleName)) {
                $hasPosRole = true;
                $this->command->info("User {$user->name} already has role: {$roleName}");
                break;
            }
        }

        if (!$hasPosRole) {
            // Assign Cashier role which should have POS access
            $role = Role::where('name', 'Cashier')->first();
            if ($role) {
                $user->assignRole($role);
                $this->command->info("Assigned 'Cashier' role to user {$user->name} ({$user->email}).");
            } else {
                $this->command->error("Role 'Cashier' not found.");
            }
        }

        // Also assign 'Sales Manager' role for broader access
        $salesManagerRole = Role::where('name', 'Sales Manager')->first();
        if ($salesManagerRole && !$user->hasRole('Sales Manager')) {
            $user->assignRole($salesManagerRole);
            $this->command->info("Assigned 'Sales Manager' role to user {$user->name} ({$user->email}).");
        }
    }
}