<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get all branches, departments, and positions
        $branches = Branch::all();
        $departments = Department::all();
        $positions = Position::all();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Please seed branches first.');
            return;
        }

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please seed departments first.');
            return;
        }

        if ($positions->isEmpty()) {
            $this->command->warn('No positions found. Please seed positions first.');
            return;
        }

        $employeeCount = 0;
        $now = now();
        $hashedPassword = Hash::make('password');
        $employeesData = [];

        $statuses = ['active', 'active', 'active', 'active', 'on_probation'];
        $shifts = ['morning', 'afternoon', 'rotating', 'flexible'];
        $nigerianNames = [
            'male' => ['Chukwuemeka', 'Oluwaseun', 'Abubakar', 'Emeka', 'Tunde', 'Chigozie', 'Ibrahim', 'Kunle', 'Obinna', 'Yusuf'],
            'female' => ['Ngozi', 'Amina', 'Chioma', 'Folake', 'Kemi', 'Blessing', 'Hauwa', 'Ada', 'Fatima', 'Nneka']
        ];
        $surnames = ['Okafor', 'Adebayo', 'Mohammed', 'Nwankwo', 'Ogunleye', 'Chukwu', 'Bello', 'Okoro', 'Aliyu', 'Eze', 'Williams', 'Johnson'];

        foreach ($branches as $branch) {
            $this->command->info("Creating employees for {$branch->name}...");

            // Create employees for each department at this branch
            // Kitchen department
            $kitchenDept = $departments->where('name', 'Kitchen')->first();
            $chefPosition = $positions->where('name', 'Chef')->first();
            $kitchenStaffPosition = $positions->where('name', 'Kitchen Staff')->first();

            if ($kitchenDept && $chefPosition) {
                // 1 Chef per branch
                $employeesData[] = $this->createEmployee($faker, $branch, $kitchenDept, $chefPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);

                // 3 Kitchen Staff
                if ($kitchenStaffPosition) {
                    for ($i = 0; $i < 3; $i++) {
                        $employeesData[] = $this->createEmployee($faker, $branch, $kitchenDept, $kitchenStaffPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    }
                }
            }

            // Gelato Production
            $gelatoDept = $departments->where('name', 'Gelato Production')->first();
            $gelatoHeadPosition = $positions->where('name', 'Head of Gelato')->first();
            $gelatoStaffPosition = $positions->where('name', 'Gelato Production Staff')->first();

            if ($gelatoDept && $gelatoHeadPosition) {
                // 1 Head of Gelato
                $employeesData[] = $this->createEmployee($faker, $branch, $gelatoDept, $gelatoHeadPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);

                // 2 Gelato Staff
                if ($gelatoStaffPosition) {
                    for ($i = 0; $i < 2; $i++) {
                        $employeesData[] = $this->createEmployee($faker, $branch, $gelatoDept, $gelatoStaffPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    }
                }
            }

            // Confectionaries Production
            $confectionProdDept = $departments->where('name', 'Confectionaries Production')->first();
            $confectionManagerPosition = $positions->where('name', 'Confectionaries Manager')->first();
            $confectionStaffPosition = $positions->where('name', 'Confectionaries Production Staff')->first();

            if ($confectionProdDept && $confectionManagerPosition) {
                // 1 Manager
                $employeesData[] = $this->createEmployee($faker, $branch, $confectionProdDept, $confectionManagerPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);

                // 2 Staff
                if ($confectionStaffPosition) {
                    for ($i = 0; $i < 2; $i++) {
                        $employeesData[] = $this->createEmployee($faker, $branch, $confectionProdDept, $confectionStaffPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    }
                }
            }

            // Till (Sales)
            $tillDept = $departments->where('name', 'Till')->first();
            $tillSupervisorPosition = $positions->where('name', 'Till Supervisor')->first();
            $cashierPosition = $positions->where('name', 'Cashier')->first();

            if ($tillDept && $tillSupervisorPosition) {
                // 1 Supervisor
                $employeesData[] = $this->createEmployee($faker, $branch, $tillDept, $tillSupervisorPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);

                // 3 Cashiers
                if ($cashierPosition) {
                    for ($i = 0; $i < 3; $i++) {
                        $employeesData[] = $this->createEmployee($faker, $branch, $tillDept, $cashierPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    }
                }
            }

            // Corner Store
            $cornerStoreDept = $departments->where('name', 'Corner Store')->first();
            $cornerManagerPosition = $positions->where('name', 'Corner Store Manager')->first();
            $cornerStaffPosition = $positions->where('name', 'Corner Store Staff')->first();

            if ($cornerStoreDept && $cornerManagerPosition) {
                // 1 Manager
                $employeesData[] = $this->createEmployee($faker, $branch, $cornerStoreDept, $cornerManagerPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);

                // 2 Staff
                if ($cornerStaffPosition) {
                    for ($i = 0; $i < 2; $i++) {
                        $employeesData[] = $this->createEmployee($faker, $branch, $cornerStoreDept, $cornerStaffPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                    }
                }
            }

            // Confectionaries Sales
            $confectionSalesDept = $departments->where('name', 'Confectionaries Sales')->first();
            $confectionSalesStaffPosition = $positions->where('name', 'Confectionaries Sales Staff')->first();

            if ($confectionSalesDept && $confectionSalesStaffPosition) {
                // 2 Sales Staff
                for ($i = 0; $i < 2; $i++) {
                    $employeesData[] = $this->createEmployee($faker, $branch, $confectionSalesDept, $confectionSalesStaffPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                }
            }

            // Inventory/Store
            $inventoryDept = $departments->where('name', 'Inventory/Store')->first();
            $storeKeeperPosition = $positions->where('name', 'Store Keeper')->first();
            $stockControllerPosition = $positions->where('name', 'Stock Controller')->first();

            if ($inventoryDept) {
                // 1 Store Keeper
                if ($storeKeeperPosition) {
                    $employeesData[] = $this->createEmployee($faker, $branch, $inventoryDept, $storeKeeperPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                }

                // 1 Stock Controller
                if ($stockControllerPosition) {
                    $employeesData[] = $this->createEmployee($faker, $branch, $inventoryDept, $stockControllerPosition, $hashedPassword, $now, $nigerianNames, $surnames, ++$employeeCount);
                }
            }
        }

        // Bulk insert in chunks of 50
        foreach (array_chunk($employeesData, 50) as $chunk) {
            Employee::insert($chunk);
        }

        $this->command->info("✅ {$employeeCount} employees created successfully across all branches.");
    }

    /**
     * Create a single employee data array
     */
    private function createEmployee($faker, $branch, $department, $position, $hashedPassword, $now, $nigerianNames, $surnames, $count)
    {
        $gender = $faker->randomElement(['male', 'female']);
        $firstName = $faker->randomElement($nigerianNames[$gender]);
        $lastName = $faker->randomElement($surnames);
        $name = $firstName . ' ' . $lastName;
        $employeeNumber = 'EMP-' . str_replace(['-', ' '], '', strtoupper($branch->code)) . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Create unique email using employee number to avoid duplicates
        $emailPrefix = strtolower(str_replace(' ', '.', $name)) . '.' . $count;

        $hireDate = $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d');
        $status = $faker->randomElement(['active', 'active', 'active', 'on_probation']);

        $probationEndDate = null;
        if ($status === 'on_probation') {
            $probationEndDate = $faker->dateTimeBetween('now', '+3 months')->format('Y-m-d');
        }

        return [
            'id' => $faker->uuid(),
            'branch_id' => $branch->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'manager_id' => null, // Will be set later if needed
            'employee_number' => $employeeNumber,
            'name' => $name,
            'email' => $emailPrefix . '@sweettooth.com',
            'phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
            'address' => $faker->streetAddress() . ', ' . $branch->city . ', ' . $branch->state . ' State, Nigeria',
            'date_of_birth' => $faker->dateTimeBetween('-45 years', '-22 years')->format('Y-m-d'),
            'gender' => $gender,
            'nationality' => 'Nigerian',
            'emergency_contact_name' => $faker->randomElement($nigerianNames[$gender === 'male' ? 'female' : 'male']) . ' ' . $faker->randomElement($surnames),
            'emergency_contact_phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
            'position' => $position->name, // Keep this for backward compatibility
            'hire_date' => $hireDate,
            'termination_date' => null,
            'status' => $status,
            'probation_end_date' => $probationEndDate,
            'shift_preference' => $faker->randomElement(['morning', 'afternoon', 'rotating', 'flexible']),
            'salary' => $faker->randomFloat(2, 80000, 350000),
            'hourly_rate' => null,
            'tax_id' => 'TIN-' . $faker->numberBetween(10000000, 99999999),
            'bank_account' => $faker->numerify('##########'),
            'allergies' => $faker->boolean(15) ? $faker->randomElement(['None', 'Peanuts', 'Shellfish', 'Lactose']) : null,
            'profile_photo' => null,
            'last_performance_review_date' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'performance_rating' => $faker->randomFloat(1, 3.5, 5.0),
            'password' => $hashedPassword,
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
