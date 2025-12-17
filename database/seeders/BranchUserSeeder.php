<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class BranchUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles are seeded first, specifically the 'Employee' role
        $this->call(RoleSeeder::class);

        // Get or create the 'Employee' role
        $employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);

        $branches = Branch::all();

        // If no branches exist, create some default ones for seeding purposes
        if ($branches->isEmpty()) {
            $this->command->info('No branches found. Creating default branches for user seeding.');
            $defaultBranchesData = [
                [
                    'name' => 'SweetTooth North',
                    'code' => 'NTH-001',
                    'location' => 'North Location',
                    'phone' => '+111-222-3333',
                    'email' => 'north@sweettooth.com',
                    'description' => 'Default North Branch',
                    'country' => 'Country',
                    'state' => 'State',
                    'city' => 'City',
                    'postal_code' => '00001',
                    'timezone' => 'Africa/Lagos',
                    'is_active' => true,
                ],
                [
                    'name' => 'SweetTooth South',
                    'code' => 'STH-002',
                    'location' => 'South Location',
                    'phone' => '+444-555-6666',
                    'email' => 'south@sweettooth.com',
                    'description' => 'Default South Branch',
                    'country' => 'Country',
                    'state' => 'State',
                    'city' => 'City',
                    'postal_code' => '00002',
                    'timezone' => 'Africa/Lagos',
                    'is_active' => true,
                ],
            ];
            foreach ($defaultBranchesData as $branchData) {
                $branches->push(Branch::create($branchData));
            }
        }

        foreach ($branches as $branch) {
            $this->seedBranchUsers($branch, $employeeRole);
        }

        $this->command->info('✅ ' . User::count() . ' total users created/updated across all branches.');
    }

    /**
     * Seed users for a specific branch.
     */
    protected function seedBranchUsers(Branch $branch, Role $role): void
    {
        $this->command->info("Seeding users for Branch: {$branch->name}");

        User::factory()->count(30)->create([
            'branch_id' => $branch->id,
            'is_active' => true,
            'user_type' => 'employee',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ])->each(function (User $user) use ($role) {
            $user->assignRole($role);
        });

        $this->command->info("   - Created 30 employees for {$branch->name}.");
    }
}
