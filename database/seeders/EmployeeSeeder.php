<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all branches and departments
        $branches = Branch::all();
        $departments = Department::all();
        $roles = Role::where('guard_name', 'employees')->get()->keyBy('name');

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Please seed branches first.');

            return;
        }

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please seed departments first.');

            return;
        }

        if ($roles->isEmpty()) {
            $this->command->warn('No roles found. Please seed roles first.');

            return;
        }

        $employeeCount = 0;
        $now = now();
        $hashedPassword = Hash::make('password');

        // Create admin/manager employees first (before other departments)
        $this->command->info('Creating admin and management employees...');
        
        // Get first branch for admin/manager assignment
        $adminBranch = $branches->first();

        // Create Admin employee (one per branch)
        if ($roles->has('Admin')) {
            foreach ($branches as $branch) {
                $admin = $this->createEmployee($faker, $branch, $departments->first(), $hashedPassword, $now, $nigerianNames = [
                    'male' => ['Chukwuemeka', 'Oluwaseun', 'Abubakar', 'Emeka', 'Tunde'],
                    'female' => ['Ngozi', 'Amina', 'Chioma', 'Folake', 'Kemi'],
                ], $surnames = ['Okafor', 'Adebayo', 'Mohammed'], ++$employeeCount);
                $admin->assignRole('Admin');
            }
        }

        // Create HR Manager (one per branch)
        if ($roles->has('HR Manager')) {
            foreach ($branches as $branch) {
                $hrManager = $this->createEmployee($faker, $branch, $departments->where('name', 'Human Resources')->first() ?? $departments->first(), $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $hrManager->assignRole('HR Manager');
            }
        }

        // Create Inventory Manager (one per branch)
        if ($roles->has('Inventory Manager')) {
            foreach ($branches as $branch) {
                $inventoryManager = $this->createEmployee($faker, $branch, $departments->where('name', 'Inventory/Store')->first() ?? $departments->first(), $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $inventoryManager->assignRole('Inventory Manager');
            }
        }

        // Create Sales Manager (one per branch)
        if ($roles->has('Sales Manager')) {
            foreach ($branches as $branch) {
                $salesManager = $this->createEmployee($faker, $branch, $departments->where('name', 'Till')->first() ?? $departments->first(), $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $salesManager->assignRole('Sales Manager');
            }
        }

        // Create Head of Production (one per branch)
        if ($roles->has('Head of Production')) {
            foreach ($branches as $branch) {
                $hOP = $this->createEmployee($faker, $branch, $departments->where('name', 'Kitchen')->first() ?? $departments->first(), $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $hOP->assignRole('Head of Production');
            }
        }

        $nigerianNames = [
            'male' => ['Chukwuemeka', 'Oluwaseun', 'Abubakar', 'Emeka', 'Tunde', 'Chigozie', 'Ibrahim', 'Kunle', 'Obinna', 'Yusuf'],
            'female' => ['Ngozi', 'Amina', 'Chioma', 'Folake', 'Kemi', 'Blessing', 'Hauwa', 'Ada', 'Fatima', 'Nneka'],
        ];
        $surnames = ['Okafor', 'Adebayo', 'Mohammed', 'Nwankwo', 'Ogunleye', 'Chukwu', 'Bello', 'Okoro', 'Aliyu', 'Eze', 'Williams', 'Johnson'];

        foreach ($branches as $branch) {
            $this->command->info("Creating employees for {$branch->name}...");

            // Kitchen department
            $kitchenDept = $departments->where('name', 'Kitchen')->first();
            if ($kitchenDept && $roles->has('Chef')) {
                // 1 Chef per branch
                $chef = $this->createEmployee($faker, $branch, $kitchenDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $chef->assignRole('Chef');

                // 3 Kitchen Staff
                if ($roles->has('Kitchen Staff')) {
                    for ($i = 0; $i < 3; $i++) {
                        $staff = $this->createEmployee($faker, $branch, $kitchenDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                        $staff->assignRole('Kitchen Staff');
                    }
                }
            }

            // Gelato Production
            $gelatoDept = $departments->where('name', 'Gelato Production')->first();
            if ($gelatoDept && $roles->has('Head of Gelato')) {
                // 1 Head of Gelato
                $gelatoHead = $this->createEmployee($faker, $branch, $gelatoDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $gelatoHead->assignRole('Head of Gelato');

                // 2 Gelato Staff
                if ($roles->has('Gelato Production Staff')) {
                    for ($i = 0; $i < 2; $i++) {
                        $staff = $this->createEmployee($faker, $branch, $gelatoDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                        $staff->assignRole('Gelato Production Staff');
                    }
                }
            }

            // Confectionaries Production
            $confectionProdDept = $departments->where('name', 'Confectionaries Production')->first();
            if ($confectionProdDept && $roles->has('Confectionaries Manager')) {
                // 1 Manager
                $manager = $this->createEmployee($faker, $branch, $confectionProdDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $manager->assignRole('Confectionaries Manager');

                // 2 Staff
                if ($roles->has('Confectionaries Production Staff')) {
                    for ($i = 0; $i < 2; $i++) {
                        $staff = $this->createEmployee($faker, $branch, $confectionProdDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                        $staff->assignRole('Confectionaries Production Staff');
                    }
                }
            }

            // Till (Sales)
            $tillDept = $departments->where('name', 'Till')->first();
            if ($tillDept && $roles->has('Till Supervisor')) {
                // 1 Supervisor
                $supervisor = $this->createEmployee($faker, $branch, $tillDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $supervisor->assignRole('Till Supervisor');

                // 3 Cashiers
                if ($roles->has('Cashier')) {
                    for ($i = 0; $i < 3; $i++) {
                        $cashier = $this->createEmployee($faker, $branch, $tillDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                        $cashier->assignRole('Cashier');
                    }
                }
            }

            // Corner Store
            $cornerStoreDept = $departments->where('name', 'Corner Store')->first();
            if ($cornerStoreDept && $roles->has('Corner Store Manager')) {
                // 1 Manager
                $manager = $this->createEmployee($faker, $branch, $cornerStoreDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                $manager->assignRole('Corner Store Manager');

                // 2 Staff
                if ($roles->has('Corner Store Staff')) {
                    for ($i = 0; $i < 2; $i++) {
                        $staff = $this->createEmployee($faker, $branch, $cornerStoreDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                        $staff->assignRole('Corner Store Staff');
                    }
                }
            }

            // Confectionaries Sales
            $confectionSalesDept = $departments->where('name', 'Confectionaries Sales')->first();
            if ($confectionSalesDept && $roles->has('Confectionaries Sales Staff')) {
                // 2 Sales Staff
                for ($i = 0; $i < 2; $i++) {
                    $staff = $this->createEmployee($faker, $branch, $confectionSalesDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    $staff->assignRole('Confectionaries Sales Staff');
                }
            }

            // Inventory/Store
            $inventoryDept = $departments->where('name', 'Inventory/Store')->first();
            if ($inventoryDept) {
                // 1 Store Keeper
                if ($roles->has('Store Keeper')) {
                    $keeper = $this->createEmployee($faker, $branch, $inventoryDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    $keeper->assignRole('Store Keeper');
                }

                // 1 Stock Controller
                if ($roles->has('Stock Controller')) {
                    $controller = $this->createEmployee($faker, $branch, $inventoryDept, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    $controller->assignRole('Stock Controller');
                }
            }
        }

        $this->command->info("✅ {$employeeCount} employees created successfully with roles assigned across all branches.");
        $this->command->info('  Including: Admin, HR Manager, Inventory Manager, Sales Manager, and Head of Production per branch.');
    }

    /**
     * Create a single employee
     */
    private function createEmployee($faker, $branch, $department, $hashedPassword, $now, $nigerianNames, $surnames, $count)
    {
        $gender = $faker->randomElement(['male', 'female']);
        $firstName = $faker->randomElement($nigerianNames[$gender]);
        $lastName = $faker->randomElement($surnames);
        $name = $firstName.' '.$lastName;
        $employeeNumber = 'EMP-'.str_replace(['-', ' '], '', strtoupper($branch->code)).'-'.str_pad($count, 4, '0', STR_PAD_LEFT);

        // Create unique email using employee number to avoid duplicates
        $emailPrefix = strtolower(str_replace(' ', '.', $name)).'.'.$count;

        $hireDate = $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d');
        $status = $faker->randomElement(['active', 'active', 'active', 'on_probation']);

        $probationEndDate = null;
        if ($status === 'on_probation') {
            $probationEndDate = $faker->dateTimeBetween('now', '+3 months')->format('Y-m-d');
        }

        return Employee::create([
            'id' => $faker->uuid(),
            'branch_id' => $branch->id,
            'department_id' => $department->id,
            'manager_id' => null, // Will be set later if needed
            'employee_number' => $employeeNumber,
            'name' => $name,
            'email' => $emailPrefix.'@sweettooth.com',
            'phone' => '+234-'.$faker->numberBetween(800, 909).'-'.$faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000, 9999),
            'address' => $faker->streetAddress().', '.$branch->city.', '.$branch->state.' State, Nigeria',
            'date_of_birth' => $faker->dateTimeBetween('-45 years', '-22 years')->format('Y-m-d'),
            'gender' => $gender,
            'nationality' => 'Nigerian',
            'emergency_contact_name' => $faker->randomElement($nigerianNames[$gender === 'male' ? 'female' : 'male']).' '.$faker->randomElement($surnames),
            'emergency_contact_phone' => '+234-'.$faker->numberBetween(800, 909).'-'.$faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000, 9999),
            'hire_date' => $hireDate,
            'termination_date' => null,
            'status' => $status,
            'probation_end_date' => $probationEndDate,
            'shift_preference' => $faker->randomElement(['morning', 'afternoon', 'rotating', 'flexible']),
            'salary' => $faker->randomFloat(2, 80000, 350000),
            'hourly_rate' => null,
            'tax_id' => 'TIN-'.$faker->numberBetween(10000000, 99999999),
            'bank_account' => $faker->numerify('##########'),
            'allergies' => $faker->boolean(15) ? $faker->randomElement(['None', 'Peanuts', 'Shellfish', 'Lactose']) : null,
            'profile_photo' => null,
            'last_performance_review_date' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'performance_rating' => $faker->randomFloat(1, 3.5, 5.0),
            'password' => $hashedPassword,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
