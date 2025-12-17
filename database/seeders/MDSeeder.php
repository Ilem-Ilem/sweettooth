<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find the MD role (unified system uses web guard)
        $role = Role::firstOrCreate(
            ['name' => 'MD', 'guard_name' => 'web'],
            ['is_protected' => true]
        );

        // ===============================
        // 🔹 DEFINE ALL PERMISSIONS
        // ===============================

        $permissions = [

            // 👨‍🏭 Employee & HR (using standardized format)
            'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
            'approve-leave', 'manage-staff-schedule', 'manage-leave',
            'view-departments', 'assign-roles',

            // 🏭 Production & Manufacturing
            'view-production-queue', 'create-production', 'start-production', 'complete-production',
            'approve-production', 'manage-recipes', 'view-production-reports',

            // 📦 Inventory & Warehousing
            'view-stock-levels', 'receive-stock', 'transfer-stock', 'adjust-inventory',
            'view-inventory-reports', 'create-purchase-order', 'approve-purchase-order',

            // 🚚 Procurement & Logistics
            'create-purchase-order', 'approve-purchase-order', 'view-inventory-reports',

            // 💰 Sales & Finance
            'process-sale', 'view-daily-sales', 'issue-refund', 'close-register',
            'manage-customers', 'view-sales-reports', 'view-analytics',

            // ⚙️ System & Administration
            'view-audit-logs', 'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            'manage-settings', 'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
            'view-branches', 'create-branches', 'edit-branches', 'delete-branches',
            'manage-staff-schedule', 'view-department-reports', 'generate-reports', 'export-data',

            // 🧾 Compliance & Reporting
            'view-callbacks', 'create-callback', 'approve-callbacks', 'resolve-callbacks',
        ];

        // Ensure no duplicates (safe check)
        $permissions = array_unique($permissions);

        // ===============================
        // 🔹 CREATE PERMISSIONS
        // ===============================

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web'],
                ['category' => 'general']
            );
        }

        // Assign all permissions to MD
        $role->syncPermissions($permissions);

        // ===============================
        // 🔹 CREATE MD EMPLOYEE
        // ===============================

        // Get or create the first branch (for assigning to MD)
        $branch = Branch::first();
        if (!$branch) {
            $this->command->warn('⚠️ No branches found. Skipping MD employee creation.');
            return;
        }

        // Create MD as User in unified system
        $now = now();
        $user = User::firstOrCreate(
            ['email' => 'md@sweettooth.com'],
            [
                'name' => 'Managing Director',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id, // Assign to first branch
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Assign MD role to user (unified system)
        $user->assignRole($role);

        // Optional: Create legacy Employee record for backward compatibility
        // (Remove this once all Employee references are updated)
        $mdEmployee = Employee::firstOrCreate(
            ['email' => 'md@sweettooth.com'],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'branch_id' => $branch->id,
                'department_id' => null,
                'employee_number' => 'MD-001',
                'name' => 'Managing Director',
                'email' => 'md@sweettooth.com',
                'phone' => '+234-000-000-0000',
                'address' => $branch->address ?? 'HQ',
                'date_of_birth' => now()->subYears(40)->format('Y-m-d'),
                'gender' => 'male',
                'nationality' => 'Nigerian',
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_phone' => '+234-000-000-0000',
                'hire_date' => now()->format('Y-m-d'),
                'status' => 'active',
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $this->command->info('✅ MD role, permissions, and employee created successfully with full system access.');
    }
}
