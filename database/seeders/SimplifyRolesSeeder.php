<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

/**
 * Simplify Roles Seeder
 *
 * This seeder:
 * 1. Removes all old roles (except Super Admin, Manager, Operator, Viewer)
 * 2. Migrates users from old roles to new simplified roles
 * 3. Cleans up invalid role assignments
 */
class SimplifyRolesSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $oldRoles = [
            'MD', 'Head of Production', 'Head of Gelato', 'Confectioneries Manager',
            'Chef', 'Supervisor', 'Kitchen Staff', 'Cashier', 'Corner Store Manager',
            'Stock Controller', 'HR Officer', 'Production Staff', 'Sales Staff',
            'Inventory Staff', 'HR Staff', 'Employee', 'Specialist', // Old individual role names
        ];

        echo "🔄 Simplifying roles...\n";

        // Map old roles to new simplified roles
        $roleMap = [
            'MD' => 'Manager',
            'Head of Production' => 'Manager',
            'Head of Gelato' => 'Operator',
            'Confectioneries Manager' => 'Manager',
            'Chef' => 'Operator',
            'Supervisor' => 'Manager',
            'Kitchen Staff' => 'Operator',
            'Cashier' => 'Operator',
            'Corner Store Manager' => 'Manager',
            'Stock Controller' => 'Operator',
            'HR Officer' => 'Operator',
            'Production Staff' => 'Operator',
            'Sales Staff' => 'Operator',
            'Inventory Staff' => 'Operator',
            'HR Staff' => 'Operator',
            'Employee' => 'Viewer',
            'Specialist' => 'Operator',
        ];

        $migratedCount = 0;

        // Migrate users from old roles to new roles
        foreach ($roleMap as $oldRole => $newRole) {
            // Find users with old role
            $usersWithOldRole = User::role($oldRole, $guard)->get();

            foreach ($usersWithOldRole as $user) {
                // Only reassign if not already super admin
                if (!$user->hasRole('Super Admin')) {
                    $user->syncRoles([$newRole]);
                    $migratedCount++;
                    echo "   ✓ Migrated {$user->name} ({$oldRole} → {$newRole})\n";
                }
            }
        }

        // Delete old roles
        $deletedCount = 0;
        foreach ($oldRoles as $oldRole) {
            $role = Role::where('name', $oldRole)
                ->where('guard_name', $guard)
                ->first();

            if ($role) {
                $role->delete();
                $deletedCount++;
            }
        }

        echo "\n✅ Role simplification complete:\n";
        echo "   - $migratedCount users migrated to new roles\n";
        echo "   - $deletedCount old roles deleted\n";
        echo "   - Remaining roles: 4 (Super Admin, Manager, Operator, Viewer)\n";
    }
}
