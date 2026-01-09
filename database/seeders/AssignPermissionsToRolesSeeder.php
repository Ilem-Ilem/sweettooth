<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class AssignPermissionsToRolesSeeder extends Seeder
{
    public function run(): void
    {
        // First, create all permissions
        $allPermissions = [
            // Organization
            'view_organization',
            'create_organization',
            'edit_organization',
            'delete_organization',
            
            // Employee Management
            'view_employees',
            'create_employee',
            'edit_employee',
            'delete_employee',
            'manage_employee_roles',
            'manage_employee_shifts',
            'view_employee_details',
            
            // Leave Management
            'view_leave_applications',
            'approve_leave',
            'reject_leave',
            'create_leave_type',
            'edit_leave_type',
            'delete_leave_type',
            'view_leave_balance',
            'allocate_leave',
            
            // Inventory Management
            'view_inventory',
            'create_inventory_item',
            'edit_inventory_item',
            'delete_inventory_item',
            'adjust_inventory',
            'view_stock_levels',
            'receive_stock',
            'transfer_stock',
            'view_inventory_callbacks',
            
            // Accounting
            'view_all_accounting',
            'view_accounting_own_dept',
            'view_gl_accounts',
            'create_gl_entry',
            'edit_gl_entry',
            'delete_gl_entry',
            'view_transactions',
            'view_reports',
            'export_accounting_data',
            'process_invoices',
            'manage_payments',
            
            // Payroll
            'view_payroll',
            'process_payroll',
            'view_salary_history',
            'edit_salary',
            'approve_payroll',
            
            // Production
            'view_production_queue',
            'start_production',
            'complete_production',
            'view_production_callbacks',
            'create_production_callback',
            'view_dispatch_callbacks',
            'create_dispatch_callback',
            'manage_recipes',
            'view_recipes',
            'create_recipe',
            'edit_recipe',
            'delete_recipe',
            
            // Sales
            'process_sale',
            'view_daily_sales',
            'view_sales_reports',
            'manage_prices',
            'view_invoices',
            'create_invoice',
            
            // Reports & Analytics
            'view_reports',
            'generate_reports',
            'view_analytics',
            'export_data',
            'view_branch_metrics',
            
            // System Settings
            'view_settings',
            'edit_settings',
            'view_system_settings',
            'manage_users',
            'manage_roles',
            'manage_permissions',
        ];

        // Create permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web'],
                ['description' => str_replace('_', ' ', ucwords($permission))]
            );
        }

        // Define role permissions
        $rolePermissions = [
            // Super Admin - Everything
            'Super Admin' => $allPermissions,
            
            // Managing Director (MD) - Everything
            'Managing Director' => $allPermissions,
            
            // Admin
            'Admin' => [
                'view_employees', 'manage_employee_roles', 'manage_employee_shifts',
                'view_inventory', 'view_stock_levels',
                'view_payroll',
                'view_production_queue',
                'view_sales_reports',
                'view_reports', 'view_analytics',
                'view_settings',
            ],
            
            // ========== HR DEPARTMENT ==========
            'HR Manager' => [
                // Organization
                'view_organization', 'create_organization', 'edit_organization', 'delete_organization',
                // Employee Management
                'view_employees', 'create_employee', 'edit_employee', 'delete_employee',
                'manage_employee_roles', 'manage_employee_shifts', 'view_employee_details',
                // Leave Management
                'view_leave_applications', 'approve_leave', 'reject_leave',
                'create_leave_type', 'edit_leave_type', 'delete_leave_type',
                'view_leave_balance', 'allocate_leave',
                // Payroll (limited)
                'view_payroll', 'view_salary_history',
            ],
            
            'HR Officer' => [
                // Employee Management (view/create only)
                'view_employees', 'create_employee', 'view_employee_details',
                // Leave Management
                'view_leave_applications', 'view_leave_balance', 'allocate_leave',
            ],
            
            // ========== ACCOUNTING DEPARTMENT ==========
            'Accounting Manager' => [
                // Accounting (ALL - cross-department)
                'view_all_accounting',
                'view_gl_accounts', 'create_gl_entry', 'edit_gl_entry', 'delete_gl_entry',
                'view_transactions', 'view_reports', 'export_accounting_data',
                'process_invoices', 'manage_payments',
                // Payroll
                'view_payroll', 'process_payroll', 'view_salary_history',
                'approve_payroll',
                // Reports
                'generate_reports', 'view_analytics', 'export_data', 'view_branch_metrics',
            ],
            
            // ========== PRODUCTION DEPARTMENT ==========
            'Head of Production' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_production_callbacks', 'create_production_callback',
                'view_dispatch_callbacks', 'create_dispatch_callback',
                'manage_recipes', 'view_recipes', 'create_recipe', 'edit_recipe', 'delete_recipe',
                'view_inventory', 'view_stock_levels',
                'view_sales_reports', 'view_reports',
            ],
            
            'Chef' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_production_callbacks', 'create_production_callback',
                'view_recipes',
                'view_inventory', 'view_stock_levels',
            ],
            
            'Head of Gelato' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_production_callbacks', 'create_production_callback',
                'view_recipes',
                'view_inventory', 'view_stock_levels',
            ],
            
            'Kitchen Staff' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_recipes',
            ],
            
            'Gelato Production Staff' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_recipes',
            ],
            
            'Confectioneries Production Staff' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_recipes',
            ],
            
            'Production Helper' => [
                'view_production_callbacks',
                'view_dispatch_callbacks',
                'view_inventory_callbacks',
            ],
            
            // ========== INVENTORY DEPARTMENT ==========
            'Inventory Manager' => [
                'view_inventory', 'create_inventory_item', 'edit_inventory_item', 'delete_inventory_item',
                'adjust_inventory', 'view_stock_levels',
                'receive_stock', 'transfer_stock',
                'view_inventory_callbacks',
                'view_reports', 'view_analytics', 'export_data',
            ],
            
            'Stock Controller' => [
                'view_inventory', 'adjust_inventory', 'view_stock_levels',
                'receive_stock', 'transfer_stock',
                'view_inventory_callbacks',
                'view_reports',
            ],
            
            'Store Keeper' => [
                'view_inventory', 'view_stock_levels',
                'receive_stock', 'transfer_stock',
            ],
            
            'Inventory Clerk' => [
                'view_inventory', 'view_stock_levels',
            ],
            
            'Warehouse Manager' => [
                'view_inventory', 'create_inventory_item', 'edit_inventory_item',
                'adjust_inventory', 'view_stock_levels',
                'receive_stock', 'transfer_stock',
                'view_inventory_callbacks',
            ],
            
            // ========== SALES DEPARTMENT ==========
            'Sales Manager' => [
                'view_employees', 'manage_employee_shifts',
                'process_sale', 'view_daily_sales', 'view_sales_reports',
                'manage_prices',
                'view_invoices', 'create_invoice',
                'view_inventory', 'view_stock_levels',
                'view_reports', 'view_analytics', 'export_data',
            ],
            
            'Sales Supervisor' => [
                'process_sale', 'view_daily_sales', 'view_sales_reports',
                'manage_prices',
                'view_invoices', 'create_invoice',
            ],
            
            'Sales Associate' => [
                'process_sale', 'view_daily_sales',
            ],
            
            'Cashier' => [
                'process_sale',
            ],
            
            'Junior Cashier' => [
                'process_sale',
            ],
            
            'Till Supervisor' => [
                'process_sale', 'view_daily_sales', 'view_sales_reports',
                'view_invoices',
            ],
            
            // ========== STORE/CORNER STORE ==========
            'Store Manager' => [
                'view_inventory', 'view_stock_levels', 'adjust_inventory',
                'process_sale', 'view_daily_sales', 'view_sales_reports',
                'view_employees', 'manage_employee_shifts',
                'view_reports', 'view_analytics',
            ],
            
            'Store Supervisor' => [
                'view_inventory', 'view_stock_levels',
                'process_sale', 'view_daily_sales',
            ],
            
            'Corner Store Manager' => [
                'view_inventory', 'view_stock_levels', 'adjust_inventory',
                'process_sale', 'view_daily_sales', 'view_sales_reports',
                'view_employees', 'manage_employee_shifts',
                'view_reports',
            ],
            
            'Corner Store Staff' => [
                'view_inventory', 'view_stock_levels',
                'process_sale', 'view_daily_sales',
            ],
            
            'Confectioneries Manager' => [
                'view_production_queue', 'start_production', 'complete_production',
                'view_production_callbacks', 'create_production_callback',
                'view_inventory', 'view_stock_levels', 'adjust_inventory',
                'process_sale', 'view_daily_sales', 'view_sales_reports',
                'view_employees', 'manage_employee_shifts',
                'view_reports',
            ],
            
            'Confectioneries Sales Staff' => [
                'process_sale', 'view_daily_sales',
                'view_inventory', 'view_stock_levels',
            ],
            
            // ========== GENERIC ROLES ==========
            'Supervisor' => [
                'view_production_queue', 'view_daily_sales', 'view_reports',
            ],
            
            'Manager' => [
                'view_production_queue', 'view_daily_sales', 'view_inventory',
                'view_reports', 'view_analytics',
            ],
            
            'Staff' => [
                'view_production_queue', 'view_recipes',
            ],
            
            'Employee' => [
                'view_leave_balance',
            ],
            
            'Viewer' => [
                'view_reports', 'view_analytics',
            ],
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            
            if ($role) {
                // Get permission objects
                $permissionObjects = Permission::whereIn('name', $permissions)->get();
                
                // Sync permissions to role
                $role->syncPermissions($permissionObjects);
                
                echo "✓ Assigned " . count($permissionObjects) . " permissions to {$roleName}\n";
            } else {
                echo "✗ Role '{$roleName}' not found\n";
            }
        }

        echo "\nPermission assignment complete!\n";
    }
}
