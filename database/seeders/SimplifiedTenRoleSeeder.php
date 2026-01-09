<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

/**
 * Simplified Ten Role Seeder
 * 
 * Creates a clean 10-role hierarchy:
 * 1. Super Admin     - Full system access
 * 2. Manager         - Cross-functional management
 * 3. Supervisor      - Team supervision
 * 4. Specialist      - Advanced role-specific operations
 * 5. Production Staff - Production operations
 * 6. Sales Staff     - Sales operations
 * 7. Inventory Staff - Inventory operations
 * 8. HR Staff        - HR operations
 * 9. Employee        - Basic employee access
 * 10. Viewer         - Read-only access
 */
class SimplifiedTenRoleSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        echo "🔄 Rebuilding roles to 10-role simplified structure...\n";

        // Step 1: Delete existing 4 simplified roles
        Role::where('guard_name', $guard)
            ->whereIn('name', ['Super Admin', 'Manager', 'Operator', 'Viewer'])
            ->delete();

        echo "   ✓ Cleared old simplified roles\n";

        // Step 2: Create the 10 new roles
        $roles = [
            'Super Admin' => 'Full system access - no restrictions',
            'Manager' => 'Cross-functional management and oversight',
            'Supervisor' => 'Team supervision and delegation',
            'Specialist' => 'Advanced role-specific operations',
            'Production Staff' => 'Production operations and batch management',
            'Sales Staff' => 'Point-of-sale and transaction processing',
            'Inventory Staff' => 'Stock and inventory management',
            'HR Staff' => 'Employee and payroll management',
            'Employee' => 'Basic employee access and dashboard viewing',
            'Viewer' => 'Read-only access to reports and dashboards',
        ];

        foreach ($roles as $name => $description) {
            Role::create([
                'name' => $name,
                'guard_name' => $guard,
            ]);
            echo "   ✓ Created role: $name\n";
        }

        // Step 3: Define permissions for each role
        $this->assignRolePermissions($guard);

        echo "\n✅ Role restructuring complete!\n";
        echo "   Total roles: " . count($roles) . "\n";
    }

    private function assignRolePermissions(string $guard): void
    {
        $rolePermissions = [
            'Super Admin' => ['*'],
            
            'Manager' => [
                // Management & Staff
                'view-employees', 'view-departments', 'manage-staff-schedule',
                'manage-leave', 'approve-leave', 'view-hr-reports',
                
                // Production oversight
                'view-production-queue', 'view-production-reports',
                'view-batch-history', 'approve-production',
                
                // Sales oversight
                'view-sales-dashboard', 'view-daily-sales',
                'view-sales-reports', 'view-sales-transactions',
                'view-till-records',
                
                // Inventory oversight
                'view-stock-levels', 'view-inventory-reports',
                'view-stock-valuation',
                
                // Analytics & Reporting
                'view-analytics', 'view-dashboard', 'view-department-reports',
                'view-branch-reports', 'view-kpi-metrics', 'generate-reports',
                'export-reports', 'view-activity-logs', 'view-activity-timeline',
            ],
            
            'Supervisor' => [
                // Team management
                'view-employees', 'manage-staff-schedule', 'manage-leave',
                'approve-leave', 'view-departments',
                
                // Department-specific operations (view)
                'view-production-queue', 'view-production-reports',
                'view-sales-dashboard', 'view-sales-reports',
                'view-stock-levels',
                
                // Analytics
                'view-dashboard', 'view-analytics', 'view-department-reports',
                'view-activity-logs', 'view-activity-timeline',
            ],
            
            'Specialist' => [
                // Production specialization
                'create-production-order', 'start-production', 'complete-production',
                'manage-recipes', 'view-recipes', 'manage-quality-control',
                'view-production-queue', 'view-production-reports', 'view-batch-history',
                'edit-production-order', 'view-production-cost',
                
                // Sales specialization
                'process-sale', 'issue-refund', 'manage-sales-discounts',
                'edit-sales-transactions', 'manage-payment-methods',
                'view-sales-dashboard', 'view-daily-sales', 'view-sales-transactions',
                'close-register',
                
                // Inventory specialization
                'receive-stock', 'transfer-stock', 'adjust-inventory',
                'write-off-stock', 'manage-reorder-levels', 'manage-stock-categories',
                'view-stock-history', 'view-reorder-levels',
                
                // HR specialization
                'create-employees', 'edit-employees', 'view-employee-details',
                'manage-payroll', 'view-payroll',
                
                // Dashboard access
                'view-dashboard', 'view-analytics', 'view-stock-levels',
            ],
            
            'Production Staff' => [
                // Core production operations
                'view-production-queue', 'start-production', 'complete-production',
                'view-recipes', 'view-production-reports', 'view-batch-history',
                
                // Stock viewing
                'view-stock-levels',
                
                // Dashboard access
                'view-dashboard', 'view_production_dashboard',
            ],
            
            'Sales Staff' => [
                // Core sales operations
                'view-sales-dashboard', 'process-sale', 'view-daily-sales',
                'view-sales-reports', 'view-sales-transactions', 'view-till-records',
                
                // Stock viewing
                'view-stock-levels',
                
                // Dashboard access
                'view-dashboard', 'view_sales_dashboard',
            ],
            
            'Inventory Staff' => [
                // Core inventory operations
                'view-stock-levels', 'receive-stock', 'transfer-stock',
                'adjust-inventory', 'view-inventory-reports', 'view-stock-history',
                
                // Dashboard access
                'view-dashboard', 'view_inventory_dashboard',
            ],
            
            'HR Staff' => [
                // HR operations
                'view-employees', 'create-employees', 'edit-employees',
                'view-employee-details', 'manage-staff-schedule',
                'manage-leave', 'approve-leave', 'view-hr-reports',
                'view-payroll',
                
                // Dashboard access
                'view-dashboard', 'view_hr_dashboard',
            ],
            
            'Employee' => [
                // Basic access
                'view-dashboard', 'manage-leave', 'view-activity-timeline',
                'view-employees', 'view-stock-levels',
            ],
            
            'Viewer' => [
                // Read-only reporting
                'view-dashboard', 'view-analytics', 'view-sales-reports',
                'view-inventory-reports', 'view-hr-reports', 'view-department-reports',
                'export-reports', 'view-kpi-metrics', 'view-activity-timeline',
            ],
        ];

        echo "\n📋 Assigning permissions to roles...\n";

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->where('guard_name', $guard)->first();

            if (!$role) {
                echo "   ✗ Role '$roleName' not found\n";
                continue;
            }

            // Handle wildcard (Super Admin)
            if (in_array('*', $permissions)) {
                $allPermissions = Permission::where('guard_name', $guard)->get();
                $role->syncPermissions($allPermissions);
                echo "   ✓ $roleName: * (all permissions)\n";
            } else {
                // Get valid permissions
                $validPermissions = Permission::whereIn('name', $permissions)
                    ->where('guard_name', $guard)
                    ->pluck('id')
                    ->toArray();

                $invalidPerms = array_diff(
                    $permissions,
                    Permission::whereIn('name', $permissions)
                        ->where('guard_name', $guard)
                        ->pluck('name')
                        ->toArray()
                );

                if ($invalidPerms) {
                    echo "   ⚠ $roleName: " . count($invalidPerms) . " invalid permission(s) skipped\n";
                }

                $role->syncPermissions($validPermissions);
                echo "   ✓ $roleName: " . count($validPermissions) . " permissions\n";
            }
        }
    }
}
