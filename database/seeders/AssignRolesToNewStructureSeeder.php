<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Assign Roles to New 10-Role Structure
 * 
 * Maps existing users to the new simplified role hierarchy
 */
class AssignRolesToNewStructureSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        echo "🔄 Assigning users to new role structure...\n";

        // Role mapping based on user name/type
        $roleAssignments = [
            'Super Admin' => 'Super Admin',
            'Production Manager' => 'Manager',
            'Sales Manager' => 'Manager',
            'Inventory Manager' => 'Manager',
            'HR Manager' => 'Manager',
            'Kitchen Staff' => 'Production Staff',
            'Gelato Operator' => 'Specialist',
            'Confectioneries Operator' => 'Specialist',
            'Cashier' => 'Sales Staff',
            'Store Clerk' => 'Inventory Staff',
            'Corner Store Staff' => 'Sales Staff',
            'Viewer' => 'Viewer',
        ];

        $assignedCount = 0;

        foreach ($roleAssignments as $userName => $roleName) {
            $user = User::where('name', $userName)->first();

            if (!$user) {
                echo "   ✗ User '$userName' not found\n";
                continue;
            }

            $role = Role::where('name', $roleName)
                ->where('guard_name', $guard)
                ->first();

            if (!$role) {
                echo "   ✗ Role '$roleName' not found\n";
                continue;
            }

            $user->syncRoles([$role]);
            $assignedCount++;
            echo "   ✓ Assigned $userName → $roleName\n";
        }

        echo "\n✅ Role assignment complete!\n";
        echo "   - $assignedCount users assigned to new roles\n";
        echo "\n📋 Current User Roles:\n";
        echo str_repeat('-', 80) . "\n";

        User::with('roles')->get()->each(function ($user) {
            $roles = $user->roles->pluck('name')->join(', ') ?: 'No role';
            printf("%-30s | %-35s | %s\n", $user->name, $user->email, $roles);
        });
    }
}
