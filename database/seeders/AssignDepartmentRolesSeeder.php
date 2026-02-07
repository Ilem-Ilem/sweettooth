<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignDepartmentRolesSeeder extends Seeder
{
    /**
     * Assign appropriate roles to users based on their departments.
     */
    public function run(): void
    {
        $this->command->info('Starting department-based role assignment...');

        // Define department to role mapping
        $departmentRoleMapping = [
            'Kitchen' => ['Head of Production', 'Chef', 'Kitchen Staff'],
            'Gelato Production' => ['Head of Gelato', 'Gelato Production Staff'],
            'Confectionaries Production' => ['Confectionaries Manager', 'Confectionaries Production Staff'],
            'Till' => ['Sales Manager', 'Cashier', 'Till Supervisor'],
            'Corner Store' => ['Corner Store Manager', 'Corner Store Staff'],
            'Confectionaries Sales' => ['Confectionaries Sales Staff', 'Sales Manager'],
            'Inventory/Store' => ['Inventory Manager', 'Stock Controller', 'Store Keeper'],
            'HR' => ['HR Manager', 'HR Officer'],
            'Bakery' => ['Head of Production', 'Chef', 'Kitchen Staff'],
            'Beverage Production' => ['Head of Production', 'Chef', 'Kitchen Staff'],
            'Dine-in Service' => ['Sales Manager', 'Sales Associate'],
            'Online Orders' => ['Sales Manager', 'Sales Associate'],
            'Finance' => ['Accounting Manager', 'Accountant'],
            'IT' => ['Admin', 'Supervisor'],
            'Maintenance' => ['Supervisor', 'Employee'],
        ];

        $processedUsers = 0;
        $assignedRoles = 0;

        foreach ($departmentRoleMapping as $departmentName => $possibleRoles) {
            $department = Department::where('name', $departmentName)->first();
            
            if (!$department) {
                $this->command->warn("Department '$departmentName' not found, skipping...");
                continue;
            }

            // Get all users in this department
            $users = User::where('department_id', $department->id)->get();
            
            if ($users->isEmpty()) {
                $this->command->info("No users found in department '$departmentName'");
                continue;
            }

            foreach ($users as $user) {
                // Determine the most appropriate role for this user
                // For now, we'll assign the first role in the list, but in a real scenario,
                // you might want to consider job titles, seniority, etc.
                $roleName = $possibleRoles[0]; // Default to first role
                
                // Check if the role exists
                $role = Role::where('name', $roleName)->first();
                
                if (!$role) {
                    $this->command->error("Role '$roleName' does not exist for department '$departmentName'");
                    continue;
                }

                // Assign the role to the user if they don't already have it
                if (!$user->hasRole($role->name)) {
                    $user->assignRole($role->name);
                    $this->command->info("Assigned role '$roleName' to user '{$user->name}' in department '$departmentName'");
                    $assignedRoles++;
                } else {
                    $this->command->line("User '{$user->name}' already has role '$roleName', skipping...");
                }
                
                $processedUsers++;
            }
        }

        // Special handling for users without departments (assign generic Employee role)
        $usersWithoutDepartment = User::whereNull('department_id')->orWhere('department_id', '')->get();
        
        $employeeRole = Role::where('name', 'Employee')->first();
        if ($employeeRole) {
            foreach ($usersWithoutDepartment as $user) {
                if (!$user->hasRole('Employee')) {
                    $user->assignRole('Employee');
                    $this->command->info("Assigned 'Employee' role to user '{$user->name}' without department");
                    $assignedRoles++;
                }
                $processedUsers++;
            }
        }

        $this->command->info("Completed department-based role assignment.");
        $this->command->info("Processed $processedUsers users and assigned $assignedRoles roles.");
    }
}