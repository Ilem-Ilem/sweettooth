<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
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

        // Get all branches and departments
        $branches = Branch::all();
        $departments = Department::all();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Please seed branches first.');
            return;
        }

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please seed departments first.');
            return;
        }

        $employeeCount = 0;
        $positions = [
            'Production Manager', 'Production Supervisor', 'Quality Control Officer', 'Machine Operator',
            'Packaging Specialist', 'Warehouse Manager', 'Logistics Coordinator', 'Inventory Clerk',
            'Sales Manager', 'Sales Representative', 'Account Executive', 'Business Development Officer',
            'Customer Service Representative', 'Marketing Officer', 'HR Manager', 'HR Officer',
            'Finance Officer', 'Accountant', 'IT Support Specialist', 'Admin Officer'
        ];

        $statuses = ['active', 'active', 'active', 'active', 'on_probation', 'on_leave', 'inactive'];
        $shifts = ['morning', 'afternoon', 'night', 'rotating', 'flexible'];
        $nigerianStates = ['Lagos', 'Kano', 'Oyo', 'Rivers', 'FCT', 'Kaduna', 'Ogun', 'Edo', 'Delta', 'Imo'];

        foreach ($branches as $branch) {
            // Get departments for this branch (both branch-specific and general)
            $branchDepartments = $departments->filter(function ($dept) use ($branch) {
                return $dept->branch_id === $branch->id || $dept->branch_id === null;
            });

            if ($branchDepartments->isEmpty()) {
                continue;
            }

            // Create 20 employees per branch
            for ($i = 1; $i <= 20; $i++) {
                $gender = $faker->randomElement(['male', 'female']);
                $firstName = $gender === 'male' ? $faker->firstNameMale() : $faker->firstNameFemale();
                $lastName = $faker->lastName();
                $name = $firstName . ' ' . $lastName;
                $employeeNumber = 'EMP-' . strtoupper($branch->code) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);

                // Randomly assign a department
                $department = $branchDepartments->random();

                $hireDate = $faker->dateTimeBetween('-5 years', 'now');
                $status = $faker->randomElement($statuses);
                $terminationDate = null;

                if ($status === 'terminated') {
                    $terminationDate = $faker->dateTimeBetween($hireDate, 'now');
                }

                $probationEndDate = null;
                if ($status === 'on_probation') {
                    $probationEndDate = $faker->dateTimeBetween('now', '+6 months');
                }

                Employee::create([
                    'branch_id' => $branch->id,
                    'department_id' => $department->id,
                    'employee_number' => $employeeNumber,
                    'name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)) . '@foodcompany.com',
                    'phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
                    'address' => $faker->streetAddress() . ', ' . $faker->randomElement($nigerianStates) . ' State, Nigeria',
                    'date_of_birth' => $faker->dateTimeBetween('-50 years', '-22 years'),
                    'gender' => $gender,
                    'nationality' => 'Nigerian',
                    'emergency_contact_name' => $faker->name(),
                    'emergency_contact_phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
                    'position' => $faker->randomElement($positions),
                    'hire_date' => $hireDate,
                    'termination_date' => $terminationDate,
                    'status' => $status,
                    'probation_end_date' => $probationEndDate,
                    'shift_preference' => $faker->randomElement($shifts),
                    'salary' => $faker->randomFloat(2, 50000, 500000), // NGN 50k - 500k
                    'hourly_rate' => null,
                    'tax_id' => 'TIN-' . $faker->numberBetween(10000000, 99999999),
                    'bank_account' => $faker->numerify('##########'),
                    'allergies' => $faker->boolean(20) ? $faker->randomElement(['Peanuts', 'Shellfish', 'None', 'Lactose', 'Gluten']) : null,
                    'profile_photo' => null,
                    'last_performance_review_date' => $faker->dateTimeBetween('-1 year', 'now'),
                    'performance_rating' => $faker->randomFloat(1, 3.0, 5.0),
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);

                $employeeCount++;
            }
        }

        $this->command->info("✅ {$employeeCount} employees created successfully (20 per branch).");
    }
}
