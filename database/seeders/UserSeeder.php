<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create default branch
        $defaultBranch = Branch::first() ?? Branch::create([
            'name' => 'Main Branch',
            'code' => 'MB001',
            'location' => 'Main Location',
            'is_active' => true,
        ]);

        // Create Super Admin users
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@sweettooth.local'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['Super Admin']);

        // Create MD user
        $md = User::firstOrCreate(
            ['email' => 'md@sweettooth.com'],
            [
                'name' => 'Managing Director',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        $md->syncRoles(['MD']);

        // Create Head of Production
        $headProd = User::firstOrCreate(
            ['email' => 'head.production@sweettooth.local'],
            [
                'name' => 'John Production',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $headProd->syncRoles(['Head of Production']);

        // Create Sales Manager
        $salesMgr = User::firstOrCreate(
            ['email' => 'sales.manager@sweettooth.local'],
            [
                'name' => 'Sarah Sales',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $salesMgr->syncRoles(['Sales Manager']);

        // Create HR Manager
        $hrMgr = User::firstOrCreate(
            ['email' => 'hr.manager@sweettooth.local'],
            [
                'name' => 'Emma HR',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $hrMgr->syncRoles(['HR Manager']);

        // Create Inventory Manager
        $invMgr = User::firstOrCreate(
            ['email' => 'inventory.manager@sweettooth.local'],
            [
                'name' => 'Mike Inventory',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $invMgr->syncRoles(['Inventory Manager']);

        // Create Supervisors
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@sweettooth.local'],
            [
                'name' => 'David Supervisor',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $supervisor->syncRoles(['Supervisor']);

        // Create Chef
        $chef = User::firstOrCreate(
            ['email' => 'chef@sweettooth.local'],
            [
                'name' => 'Gordon Chef',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $chef->syncRoles(['Chef']);

        // Create Head of Gelato
        $headGelato = User::firstOrCreate(
            ['email' => 'head.gelato@sweettooth.local'],
            [
                'name' => 'Isabella Gelato',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $headGelato->syncRoles(['Head of Gelato']);

        // Create Confectioneries Manager
        $confMgr = User::firstOrCreate(
            ['email' => 'confectioneries.manager@sweettooth.local'],
            [
                'name' => 'Sophie Confectioneries',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $confMgr->syncRoles(['Confectioneries Manager']);

        // Create Production Staff
        $prodStaff = User::firstOrCreate(
            ['email' => 'kitchen.staff@sweettooth.local'],
            [
                'name' => 'Tommy Kitchen Staff',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $prodStaff->syncRoles(['Kitchen Staff']);

        // Create Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@sweettooth.local'],
            [
                'name' => 'Lisa Cashier',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $cashier->syncRoles(['Cashier']);

        // Create Corner Store Manager
        $cornerMgr = User::firstOrCreate(
            ['email' => 'corner.manager@sweettooth.local'],
            [
                'name' => 'Robert Corner Store',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $cornerMgr->syncRoles(['Corner Store Manager']);

        // Create Stock Controller
        $stockCtrl = User::firstOrCreate(
            ['email' => 'stock.controller@sweettooth.local'],
            [
                'name' => 'Peter Stock Controller',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $stockCtrl->syncRoles(['Stock Controller']);

        // Create HR Officer
        $hrOfficer = User::firstOrCreate(
            ['email' => 'hr.officer@sweettooth.local'],
            [
                'name' => 'Patricia HR Officer',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $hrOfficer->syncRoles(['HR Officer']);

        // Create regular Employee
        $employee = User::firstOrCreate(
            ['email' => 'employee@sweettooth.local'],
            [
                'name' => 'Alex Employee',
                'password' => bcrypt('password'),
                'branch_id' => $defaultBranch->id,
                'is_active' => true,
                'user_type' => 'employee',
                'email_verified_at' => now(),
            ]
        );
        $employee->syncRoles(['Employee']);

        echo "✅ " . User::count() . " users created/updated with roles assigned.\n";
    }
}
