<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateEmployeesForNewRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create the default branch
        $branch = Branch::first();
        if (!$branch) {
            echo "❌ No branch found. Please create a branch first.\n";
            return;
        }

        // Get departments
        $hrDept = Department::where('branch_id', $branch->id)
            ->where('slug', 'hr')
            ->first();
        
        $accountingDept = Department::where('branch_id', $branch->id)
            ->where('slug', 'accounting')
            ->first();
        
        // If departments don't exist, create them
        if (!$hrDept) {
            $hrDept = Department::create([
                'branch_id' => $branch->id,
                'category_id' => 1,
                'name' => 'Human Resources',
                'slug' => 'hr',
                'description' => 'Manages employee records, payroll, leave management, and organizational structure',
            ]);
        }

        if (!$accountingDept) {
            $accountingDept = Department::create([
                'branch_id' => $branch->id,
                'category_id' => 1,
                'name' => 'Accounting',
                'slug' => 'accounting',
                'description' => 'Manages financial records and accounting across all departments',
            ]);
        }

        // Get a production department for Production Helper
        $productionDept = Department::where('branch_id', $branch->id)
            ->where('slug', 'kitchen')
            ->first();
        
        if (!$productionDept) {
            $productionDept = Department::where('branch_id', $branch->id)
                ->whereIn('slug', ['production', 'gelato'])
                ->first();
        }

        if (!$productionDept) {
            echo "⚠️ No production department found for Production Helper.\n";
            $productionDept = Department::where('branch_id', $branch->id)->first();
        }

        // ========== 1. HR Manager ==========
        $hrManager = User::firstOrCreate(
            ['email' => 'hr.manager@sweettooth.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sarah Johnson',
                'email' => 'hr.manager@sweettooth.com',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'department_id' => $hrDept->id,
                'phone' => '08123456789',
                'is_active' => true,
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $hrManager->assignRole('HR Manager');
        echo "✓ Created HR Manager: Sarah Johnson ({$hrManager->email})\n";

        // ========== 2. HR Officer ==========
        $hrOfficer = User::firstOrCreate(
            ['email' => 'hr.officer@sweettooth.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'David Okafor',
                'email' => 'hr.officer@sweettooth.com',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'department_id' => $hrDept->id,
                'phone' => '08198765432',
                'is_active' => true,
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $hrOfficer->assignRole('HR Officer');
        echo "✓ Created HR Officer: David Okafor ({$hrOfficer->email})\n";

        // ========== 3. Accounting Manager ==========
        $accountingManager = User::firstOrCreate(
            ['email' => 'accounting.manager@sweettooth.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Chioma Adeyemi',
                'email' => 'accounting.manager@sweettooth.com',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'department_id' => $accountingDept->id,
                'phone' => '08145678901',
                'is_active' => true,
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $accountingManager->assignRole('Accounting Manager');
        echo "✓ Created Accounting Manager: Chioma Adeyemi ({$accountingManager->email})\n";

        // ========== 4. Accounting Officer/Clerk ==========
        $accountingClerk = User::firstOrCreate(
            ['email' => 'accounting.clerk@sweettooth.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Tunde Oluwaseun',
                'email' => 'accounting.clerk@sweettooth.com',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'department_id' => $accountingDept->id,
                'phone' => '08176543210',
                'is_active' => true,
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $accountingClerk->assignRole('Admin'); // Basic accounting staff
        echo "✓ Created Accounting Clerk: Tunde Oluwaseun ({$accountingClerk->email})\n";

        // ========== 5. Production Helper (Kitchen) ==========
        $productionHelper1 = User::firstOrCreate(
            ['email' => 'production.helper1@sweettooth.com'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Amara Nwosu',
                'email' => 'production.helper1@sweettooth.com',
                'password' => Hash::make('password123'),
                'branch_id' => $branch->id,
                'department_id' => $productionDept->id,
                'phone' => '08187654321',
                'is_active' => true,
                'employment_status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $productionHelper1->assignRole('Production Helper');
        echo "✓ Created Production Helper 1: Amara Nwosu ({$productionHelper1->email})\n";

        // ========== 6. Production Helper (Gelato) ==========
        $gelatoDept = Department::where('branch_id', $branch->id)
            ->where('slug', 'gelato')
            ->first();
        
        if ($gelatoDept) {
            $productionHelper2 = User::firstOrCreate(
                ['email' => 'production.helper2@sweettooth.com'],
                [
                    'id' => (string) Str::uuid(),
                    'name' => 'Chidi Ezeobi',
                    'email' => 'production.helper2@sweettooth.com',
                    'password' => Hash::make('password123'),
                    'branch_id' => $branch->id,
                    'department_id' => $gelatoDept->id,
                    'phone' => '08165432109',
                    'is_active' => true,
                    'employment_status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $productionHelper2->assignRole('Production Helper');
            echo "✓ Created Production Helper 2: Chidi Ezeobi ({$productionHelper2->email})\n";
        }

        echo "\n=== Employee Creation Summary ===\n";
        echo "✅ Created employees for new roles:\n";
        echo "   • HR Manager: Sarah Johnson\n";
        echo "   • HR Officer: David Okafor\n";
        echo "   • Accounting Manager: Chioma Adeyemi\n";
        echo "   • Accounting Clerk: Tunde Oluwaseun\n";
        echo "   • Production Helper: Amara Nwosu\n";
        if ($gelatoDept) {
            echo "   • Production Helper: Chidi Ezeobi\n";
        }
        
        echo "\n=== Default Login Credentials ===\n";
        echo "Password: password123\n\n";
        echo "Employees created:\n";
        echo "1. hr.manager@sweettooth.com (HR Manager - Full HR access)\n";
        echo "2. hr.officer@sweettooth.com (HR Officer - HR support staff)\n";
        echo "3. accounting.manager@sweettooth.com (Accounting Manager - Cross-dept access)\n";
        echo "4. accounting.clerk@sweettooth.com (Accounting Clerk)\n";
        echo "5. production.helper1@sweettooth.com (Production Helper - Kitchen)\n";
        if ($gelatoDept) {
            echo "6. production.helper2@sweettooth.com (Production Helper - Gelato)\n";
        }
        echo "\n";
    }
}
