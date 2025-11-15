<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $downBranch = Branch::where('code', 'DB002')->first();

        $gelatoDept = Department::where('name', 'Gelato Production')
            ->where('branch_id', $mainBranch->id)
            ->first();
        $pastryDept = Department::where('name', 'Pastry Production')->first();
        $salesDept = Department::where('name', 'Sales Counter')
            ->where('branch_id', $mainBranch->id)
            ->first();
        $warehouseDept = Department::where('name', 'Warehouse')->first();

        // Manager
        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $salesDept->id,
            'employee_number' => 'EMP001',
            'name' => 'John Manager',
            'email' => 'john.manager@sweettooth.local',
            'phone' => '+1234567800',
            'address' => '100 Manager Street',
            'date_of_birth' => '1985-05-15',
            'gender' => 'male',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Jane Manager',
            'emergency_contact_phone' => '+1234567801',
            'hire_date' => '2023-01-01',
            'status' => 'active',
            'salary' => 5000.00,
            'hourly_rate' => 25.00,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        // Gelato Production Employees
        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $gelatoDept->id,
            'employee_number' => 'EMP002',
            'name' => 'Maria Gelato',
            'email' => 'maria.gelato@sweettooth.local',
            'phone' => '+1234567802',
            'address' => '200 Gelato Lane',
            'date_of_birth' => '1990-03-20',
            'gender' => 'female',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Joseph Gelato',
            'emergency_contact_phone' => '+1234567803',
            'hire_date' => '2023-02-01',
            'status' => 'active',
            'salary' => 2500.00,
            'hourly_rate' => 12.50,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $gelatoDept->id,
            'employee_number' => 'EMP003',
            'name' => 'Giuseppe Production',
            'email' => 'giuseppe@sweettooth.local',
            'phone' => '+1234567804',
            'address' => '201 Gelato Lane',
            'date_of_birth' => '1988-07-10',
            'gender' => 'male',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Rosa Production',
            'emergency_contact_phone' => '+1234567805',
            'hire_date' => '2023-02-15',
            'status' => 'active',
            'salary' => 2300.00,
            'hourly_rate' => 11.50,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        // Pastry Production Employees
        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $pastryDept->id,
            'employee_number' => 'EMP004',
            'name' => 'Sophie Pastry',
            'email' => 'sophie.pastry@sweettooth.local',
            'phone' => '+1234567806',
            'address' => '300 Pastry Way',
            'date_of_birth' => '1992-11-05',
            'gender' => 'female',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Pierre Pastry',
            'emergency_contact_phone' => '+1234567807',
            'hire_date' => '2023-03-01',
            'status' => 'active',
            'salary' => 2400.00,
            'hourly_rate' => 12.00,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        // Sales Counter Employees
        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $salesDept->id,
            'employee_number' => 'EMP005',
            'name' => 'Alex Sales',
            'email' => 'alex.sales@sweettooth.local',
            'phone' => '+1234567808',
            'address' => '400 Sales Street',
            'date_of_birth' => '1995-06-12',
            'gender' => 'male',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Lisa Sales',
            'emergency_contact_phone' => '+1234567809',
            'hire_date' => '2023-04-01',
            'status' => 'active',
            'salary' => 1800.00,
            'hourly_rate' => 9.00,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $salesDept->id,
            'employee_number' => 'EMP006',
            'name' => 'Emma Customer Service',
            'email' => 'emma.cs@sweettooth.local',
            'phone' => '+1234567810',
            'address' => '401 Sales Street',
            'date_of_birth' => '1994-01-30',
            'gender' => 'female',
            'nationality' => 'Local',
            'emergency_contact_name' => 'David CS',
            'emergency_contact_phone' => '+1234567811',
            'hire_date' => '2023-04-15',
            'status' => 'active',
            'salary' => 1900.00,
            'hourly_rate' => 9.50,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        // Warehouse Employee
        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $mainBranch->id,
            'department_id' => $warehouseDept->id,
            'employee_number' => 'EMP007',
            'name' => 'Frank Warehouse',
            'email' => 'frank.warehouse@sweettooth.local',
            'phone' => '+1234567812',
            'address' => '500 Warehouse Ave',
            'date_of_birth' => '1989-09-22',
            'gender' => 'male',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Mary Warehouse',
            'emergency_contact_phone' => '+1234567813',
            'hire_date' => '2023-01-15',
            'status' => 'active',
            'salary' => 2000.00,
            'hourly_rate' => 10.00,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);

        // Downtown branch employee
        $downGelato = Department::where('name', 'Gelato Production')
            ->where('branch_id', $downBranch->id)
            ->first();

        Employee::create([
            'id' => (string) Str::uuid(),
            'branch_id' => $downBranch->id,
            'department_id' => $downGelato->id,
            'employee_number' => 'EMP008',
            'name' => 'Antonio Downtown',
            'email' => 'antonio.down@sweettooth.local',
            'phone' => '+1234567814',
            'address' => '600 Downtown Road',
            'date_of_birth' => '1991-04-18',
            'gender' => 'male',
            'nationality' => 'Local',
            'emergency_contact_name' => 'Lucia Downtown',
            'emergency_contact_phone' => '+1234567815',
            'hire_date' => '2023-05-01',
            'status' => 'active',
            'salary' => 2300.00,
            'hourly_rate' => 11.50,
            'password' => Hash::make('password123'),
            'api_token' => Str::random(80),
        ]);
    }
}
