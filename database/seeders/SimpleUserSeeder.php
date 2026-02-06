<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SimpleUserSeeder extends Seeder
{
    /**
     * Create simplified users with 4 roles
     */
    public function run(): void
    {
        // Get or create default branch
        $defaultBranch = \App\Models\Branch::first();
        if (!$defaultBranch) {
            $defaultBranch = \App\Models\Branch::create([
                'name' => 'Main Branch',
                'code' => 'MB001',
                'location' => 'Main Location',
                'is_active' => true,
            ]);
        }

        // Clear existing users (optional)
        // User::truncate();

        // ===== SUPER ADMIN =====
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sweettooth.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);

        // ===== MANAGERS =====
        User::firstOrCreate(
            ['email' => 'production.manager@sweettooth.local'],
            ['name' => 'Production Manager', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Manager']);

        User::firstOrCreate(
            ['email' => 'sales.manager@sweettooth.local'],
            ['name' => 'Sales Manager', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Manager']);

        User::firstOrCreate(
            ['email' => 'inventory.manager@sweettooth.local'],
            ['name' => 'Inventory Manager', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Manager']);

        User::firstOrCreate(
            ['email' => 'hr.manager@sweettooth.local'],
            ['name' => 'HR Manager', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Manager']);

        // ===== OPERATORS =====
        User::firstOrCreate(
            ['email' => 'kitchen.staff@sweettooth.local'],
            ['name' => 'Kitchen Staff', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Operator']);

        User::firstOrCreate(
            ['email' => 'gelato.operator@sweettooth.local'],
            ['name' => 'Gelato Operator', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Operator']);

        User::firstOrCreate(
            ['email' => 'confectioneries.operator@sweettooth.local'],
            ['name' => 'Confectionaries Operator', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Operator']);

        User::firstOrCreate(
            ['email' => 'cashier@sweettooth.local'],
            ['name' => 'Cashier', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Operator']);

        User::firstOrCreate(
            ['email' => 'store.clerk@sweettooth.local'],
            ['name' => 'Store Clerk', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Operator']);

        User::firstOrCreate(
            ['email' => 'corner.staff@sweettooth.local'],
            ['name' => 'Corner Store Staff', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Operator']);

        // ===== VIEWER =====
        User::firstOrCreate(
            ['email' => 'viewer@sweettooth.local'],
            ['name' => 'Viewer', 'password' => bcrypt('password'), 'branch_id' => $defaultBranch->id]
        )->syncRoles(['Viewer']);

        echo "✅ 12 users created with simplified roles:\n";
        echo "   - 1 Super Admin\n";
        echo "   - 4 Managers\n";
        echo "   - 6 Operators\n";
        echo "   - 1 Viewer\n";
    }
}
