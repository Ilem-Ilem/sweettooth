<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdvancedDepartmentRoleAssignmentSeeder extends Seeder
{
    /**
     * Assign appropriate roles to users based on their departments and positions.
     * This seeder considers user seniority, position, and responsibilities to assign roles more accurately.
     */
    public function run(): void
    {
        $this->command->info('Starting advanced department-based role assignment...');

        // Define department to role mapping with priority (senior positions first)
        $departmentRoleMapping = [
            'Kitchen' => [
                ['keywords' => ['manager', 'head', 'lead'], 'role' => 'Head of Production'],
                ['keywords' => ['chef', 'senior'], 'role' => 'Chef'],
                ['keywords' => ['staff', 'assistant', 'associate'], 'role' => 'Kitchen Staff'],
                ['default' => 'Kitchen Staff']
            ],
            'Gelato Production' => [
                ['keywords' => ['manager', 'head', 'lead'], 'role' => 'Head of Gelato'],
                ['keywords' => ['senior'], 'role' => 'Gelato Production Staff'],
                ['keywords' => ['staff', 'assistant', 'associate'], 'role' => 'Gelato Production Staff'],
                ['default' => 'Gelato Production Staff']
            ],
            'Confectionaries Production' => [
                ['keywords' => ['manager', 'head', 'lead'], 'role' => 'Confectionaries Manager'],
                ['keywords' => ['senior'], 'role' => 'Confectionaries Production Staff'],
                ['keywords' => ['staff', 'assistant', 'associate'], 'role' => 'Confectionaries Production Staff'],
                ['default' => 'Confectionaries Production Staff']
            ],
            'Till' => [
                ['keywords' => ['manager', 'head', 'supervisor'], 'role' => 'Sales Manager'],
                ['keywords' => ['supervisor'], 'role' => 'Till Supervisor'],
                ['keywords' => ['cashier', 'staff'], 'role' => 'Cashier'],
                ['default' => 'Cashier']
            ],
            'Corner Store' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Corner Store Manager'],
                ['keywords' => ['staff', 'assistant'], 'role' => 'Corner Store Staff'],
                ['default' => 'Corner Store Staff']
            ],
            'Confectionaries Sales' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Sales Manager'],
                ['keywords' => ['supervisor'], 'role' => 'Sales Supervisor'],
                ['keywords' => ['staff', 'associate'], 'role' => 'Confectionaries Sales Staff'],
                ['default' => 'Confectionaries Sales Staff']
            ],
            'Inventory/Store' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Inventory Manager'],
                ['keywords' => ['controller', 'supervisor'], 'role' => 'Stock Controller'],
                ['keywords' => ['keeper', 'staff'], 'role' => 'Store Keeper'],
                ['default' => 'Store Keeper']
            ],
            'HR' => [
                ['keywords' => ['manager', 'head'], 'role' => 'HR Manager'],
                ['keywords' => ['officer', 'assistant'], 'role' => 'HR Officer'],
                ['default' => 'HR Officer']
            ],
            'Bakery' => [
                ['keywords' => ['manager', 'head', 'lead'], 'role' => 'Head of Production'],
                ['keywords' => ['chef', 'senior'], 'role' => 'Chef'],
                ['keywords' => ['staff', 'assistant', 'associate'], 'role' => 'Kitchen Staff'],
                ['default' => 'Kitchen Staff']
            ],
            'Beverage Production' => [
                ['keywords' => ['manager', 'head', 'lead'], 'role' => 'Head of Production'],
                ['keywords' => ['chef', 'senior'], 'role' => 'Chef'],
                ['keywords' => ['staff', 'assistant', 'associate'], 'role' => 'Kitchen Staff'],
                ['default' => 'Kitchen Staff']
            ],
            'Dine-in Service' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Sales Manager'],
                ['keywords' => ['supervisor'], 'role' => 'Sales Supervisor'],
                ['keywords' => ['staff', 'server', 'associate'], 'role' => 'Sales Associate'],
                ['default' => 'Sales Associate']
            ],
            'Online Orders' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Sales Manager'],
                ['keywords' => ['supervisor'], 'role' => 'Sales Supervisor'],
                ['keywords' => ['staff', 'associate'], 'role' => 'Sales Associate'],
                ['default' => 'Sales Associate']
            ],
            'Finance' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Accounting Manager'],
                ['keywords' => ['accountant', 'assistant'], 'role' => 'Accountant'],
                ['default' => 'Accountant']
            ],
            'IT' => [
                ['keywords' => ['manager', 'head'], 'role' => 'Admin'],
                ['keywords' => ['supervisor', 'senior'], 'role' => 'Supervisor'],
                ['keywords' => ['staff', 'assistant'], 'role' => 'Employee'],
                ['default' => 'Employee']
            ],
            'Maintenance' => [
                ['keywords' => ['supervisor', 'senior'], 'role' => 'Supervisor'],
                ['keywords' => ['staff', 'technician'], 'role' => 'Employee'],
                ['default' => 'Employee']
            ],
        ];

        $processedUsers = 0;
        $assignedRoles = 0;
        $updatedRoles = 0;

        foreach ($departmentRoleMapping as $departmentName => $roleRules) {
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
                // Skip the super admin user as they already have the correct role
                if ($user->email === 'admin@sweettooth.local') {
                    continue;
                }

                // Determine the most appropriate role based on user's position/title
                $userTitle = strtolower($user->name . ' ' . ($user->employee_number ?? '') . ' ' . ($user->user_type ?? ''));
                
                $roleName = $this->determineRoleFromTitle($userTitle, $roleRules);
                
                if (!$roleName) {
                    // If no role could be determined, use the default
                    $roleName = $roleRules[count($roleRules)-1]['default'] ?? 'Employee';
                }

                // Check if the role exists
                $role = Role::where('name', $roleName)->first();
                
                if (!$role) {
                    $this->command->error("Role '$roleName' does not exist for department '$departmentName'");
                    continue;
                }

                // Check if user already has this role
                if ($user->hasRole($role->name)) {
                    $this->command->line("User '{$user->name}' already has role '$roleName', skipping...");
                    $processedUsers++;
                    continue;
                }

                // Remove any existing roles from this user (for clean assignment)
                $existingRoles = $user->roles;
                if ($existingRoles->count() > 0) {
                    $user->removeRole($existingRoles->first()); // Simplified removal
                    $updatedRoles++;
                }

                // Assign the new role to the user
                $user->assignRole($role->name);
                $this->command->info("Assigned role '$roleName' to user '{$user->name}' in department '$departmentName'");
                $assignedRoles++;
                $processedUsers++;
            }
        }

        // Special handling for users without departments (assign generic Employee role)
        $usersWithoutDepartment = User::whereNull('department_id')->orWhere('department_id', '')->get();
        
        $employeeRole = Role::where('name', 'Employee')->first();
        if ($employeeRole) {
            foreach ($usersWithoutDepartment as $user) {
                // Skip super admin
                if ($user->email === 'admin@sweettooth.local') {
                    continue;
                }
                
                if (!$user->hasRole('Employee')) {
                    $user->assignRole('Employee');
                    $this->command->info("Assigned 'Employee' role to user '{$user->name}' without department");
                    $assignedRoles++;
                }
                $processedUsers++;
            }
        }

        $this->command->info("Completed advanced department-based role assignment.");
        $this->command->info("Processed $processedUsers users, assigned $assignedRoles roles, and updated $updatedRoles existing role assignments.");
    }

    /**
     * Determine the appropriate role based on user title and department rules
     */
    private function determineRoleFromTitle(string $title, array $rules): ?string
    {
        foreach ($rules as $rule) {
            if (!isset($rule['keywords'])) {
                continue; // Skip default rule for now
            }
            
            foreach ($rule['keywords'] as $keyword) {
                if (str_contains($title, $keyword)) {
                    return $rule['role'];
                }
            }
        }
        
        // Return default if no match found
        return $rules[count($rules)-1]['default'] ?? null;
    }
}