<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

/**
 * Employee With Roles Seeder
 * 
 * Creates test employees with roles and permissions
 * Maps employees to departments and branches
 */
class EmployeeWithRolesSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';
        
        echo "🔄 Creating employees with roles...\n";

        // Get available branches
        $branches = Branch::where('is_active', 1)->get();
        if ($branches->isEmpty()) {
            echo "   ✗ No active branches found. Run BranchSeeder first.\n";
            return;
        }

        $calabarBranch = $branches->firstWhere('code', 'CAL-001') ?? $branches->first();
        $lagoBranch = $branches->firstWhere('code', 'LAG-003') ?? $branches->skip(1)->first();
        $phcBranch = $branches->firstWhere('code', 'PHC-002') ?? $branches->skip(2)->first();

        // Define employees with roles
        $employeesData = [
            // Managers
            [
                'name' => 'Production Manager',
                'email' => 'production.manager@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Manager',
                'password' => 'password',
            ],
            [
                'name' => 'Sales Manager',
                'email' => 'sales.manager@sweettooth.local',
                'branch' => $lagoBranch,
                'role' => 'Manager',
                'password' => 'password',
            ],
            [
                'name' => 'Inventory Manager',
                'email' => 'inventory.manager@sweettooth.local',
                'branch' => $phcBranch,
                'role' => 'Manager',
                'password' => 'password',
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr.manager@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Manager',
                'password' => 'password',
            ],

            // Supervisors
            [
                'name' => 'Kitchen Supervisor',
                'email' => 'kitchen.supervisor@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Supervisor',
                'password' => 'password',
            ],
            [
                'name' => 'Sales Supervisor',
                'email' => 'sales.supervisor@sweettooth.local',
                'branch' => $lagoBranch,
                'role' => 'Supervisor',
                'password' => 'password',
            ],

            // Specialists
            [
                'name' => 'Gelato Operator',
                'email' => 'gelato.operator@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Specialist',
                'password' => 'password',
            ],
            [
                'name' => 'Confectionaries Operator',
                'email' => 'confectioneries.operator@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Specialist',
                'password' => 'password',
            ],

            // Production Staff
            [
                'name' => 'Kitchen Staff',
                'email' => 'kitchen.staff@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Production Staff',
                'password' => 'password',
            ],
            [
                'name' => 'Pastry Chef',
                'email' => 'pastry.chef@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Production Staff',
                'password' => 'password',
            ],

            // Sales Staff
            [
                'name' => 'Cashier',
                'email' => 'cashier@sweettooth.local',
                'branch' => $lagoBranch,
                'role' => 'Sales Staff',
                'password' => 'password',
            ],
            [
                'name' => 'Sales Associate',
                'email' => 'sales.associate@sweettooth.local',
                'branch' => $lagoBranch,
                'role' => 'Sales Staff',
                'password' => 'password',
            ],
            [
                'name' => 'Corner Store Staff',
                'email' => 'corner.staff@sweettooth.local',
                'branch' => $phcBranch,
                'role' => 'Sales Staff',
                'password' => 'password',
            ],

            // Inventory Staff
            [
                'name' => 'Store Clerk',
                'email' => 'store.clerk@sweettooth.local',
                'branch' => $phcBranch,
                'role' => 'Inventory Staff',
                'password' => 'password',
            ],
            [
                'name' => 'Warehouse Assistant',
                'email' => 'warehouse.assistant@sweettooth.local',
                'branch' => $phcBranch,
                'role' => 'Inventory Staff',
                'password' => 'password',
            ],

            // HR Staff
            [
                'name' => 'HR Officer',
                'email' => 'hr.officer@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'HR Staff',
                'password' => 'password',
            ],

            // Regular Employees
            [
                'name' => 'General Employee',
                'email' => 'general.employee@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Employee',
                'password' => 'password',
            ],
            [
                'name' => 'Junior Staff',
                'email' => 'junior.staff@sweettooth.local',
                'branch' => $lagoBranch,
                'role' => 'Employee',
                'password' => 'password',
            ],

            // Viewer
            [
                'name' => 'Report Viewer',
                'email' => 'viewer@sweettooth.local',
                'branch' => $calabarBranch,
                'role' => 'Viewer',
                'password' => 'password',
            ],
        ];

        $createdCount = 0;
        $failedCount = 0;

        foreach ($employeesData as $data) {
            try {
                // Check if user already exists
                $existingUser = User::where('email', $data['email'])->withTrashed()->first();
                
                if ($existingUser && !$existingUser->trashed()) {
                    echo "   ⟳ User exists: {$data['name']}\n";
                    continue;
                }

                if ($existingUser && $existingUser->trashed()) {
                    // Restore soft-deleted user
                    $existingUser->restore();
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => Hash::make($data['password']),
                        'branch_id' => $data['branch']->id,
                        'is_active' => true,
                    ]);
                }

                // Assign role
                $role = Role::where('name', $data['role'])
                    ->where('guard_name', $guard)
                    ->first();

                if ($role) {
                    $user->syncRoles([$role]);
                    echo "   ✓ {$data['name']} → {$data['role']}\n";
                    $createdCount++;
                } else {
                    echo "   ✗ Role '{$data['role']}' not found for {$data['name']}\n";
                    $failedCount++;
                }
            } catch (\Exception $e) {
                echo "   ✗ Error creating {$data['name']}: {$e->getMessage()}\n";
                $failedCount++;
            }
        }

        echo "\n✅ Employee creation complete!\n";
        echo "   - $createdCount employees created/updated with roles\n";
        echo "   - $failedCount failed\n";

        // Display summary
        $this->displaySummary();
    }

    private function displaySummary(): void
    {
        echo "\n📋 User & Role Summary:\n";
        echo str_repeat('=', 80) . "\n";

        $users = User::with('roles', 'branch')->orderBy('branch_id')->get();

        foreach ($users as $user) {
            $role = $user->roles->first()?->name ?? 'No role';
            $branch = $user->branch?->name ?? 'No branch';
            printf("%-30s | %-30s | %s\n", $user->name, $user->email, "$role ($branch)");
        }

        echo str_repeat('=', 80) . "\n";

        // Roles summary
        echo "\n📊 Roles Summary:\n";
        $roles = \Spatie\Permission\Models\Role::withCount('users')->orderByDesc('users_count')->get();
        foreach ($roles as $role) {
            echo "   " . str_pad($role->name, 20) . " → " . $role->users_count . " users\n";
        }
    }
}
