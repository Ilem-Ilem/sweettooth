<?php

namespace Database\Seeders;

use App\Models\DepartmentCategory;
use App\Models\RoleCategoryConstraint;
use Illuminate\Database\Seeder;

class RoleCategoryConstraintSeeder extends Seeder
{
    public function run(): void
    {
        $constraints = [
            // ===== SUPER ADMIN (Full Access Everywhere) =====
            [
                'role_name' => 'Super Admin',
                'category_name' => 'Sales', // Can be any category
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Super Admin',
                'category_name' => 'Production',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Super Admin',
                'category_name' => 'Inventory',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Super Admin',
                'category_name' => 'Support',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],

            // ===== MANAGER (Category-wide Access) =====
            [
                'role_name' => 'Manager',
                'category_name' => 'Production',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Manager',
                'category_name' => 'Sales',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Manager',
                'category_name' => 'Inventory',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Manager',
                'category_name' => 'Support',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],

            // ===== SUPERVISOR (Category-wide Access) =====
            [
                'role_name' => 'Supervisor',
                'category_name' => 'Production',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Supervisor',
                'category_name' => 'Sales',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Supervisor',
                'category_name' => 'Inventory',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Supervisor',
                'category_name' => 'Support',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],

            // ===== SPECIALIST (Production-specific) =====
            [
                'role_name' => 'Specialist',
                'category_name' => 'Production',
                'department_type' => 'category_wide', // Can work in any production dept
                'allowed_department_slugs' => null,
            ],

            // ===== PRODUCTION STAFF (Department-specific) =====
            [
                'role_name' => 'Production Staff',
                'category_name' => 'Production',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['main-kitchen', 'gelato-production', 'confectionery'],
            ],

            // ===== SALES STAFF (Department-specific) =====
            [
                'role_name' => 'Sales Staff',
                'category_name' => 'Sales',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['pos', 'corner-store'],
            ],

            // ===== INVENTORY STAFF (Category-wide) =====
            [
                'role_name' => 'Inventory Staff',
                'category_name' => 'Inventory',
                'department_type' => 'category_wide',
                'allowed_department_slugs' => null,
            ],

            // ===== HR STAFF (Department-specific) =====
            [
                'role_name' => 'HR Staff',
                'category_name' => 'Support',
                'department_type' => 'specific',
                'allowed_department_slugs' => ['hr-operations'],
            ],

            // ===== EMPLOYEE (Branch-wide - can work anywhere) =====
            [
                'role_name' => 'Employee',
                'category_name' => 'Sales',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Employee',
                'category_name' => 'Production',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Employee',
                'category_name' => 'Inventory',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Employee',
                'category_name' => 'Support',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],

            // ===== VIEWER (Branch-wide Read-only) =====
            [
                'role_name' => 'Viewer',
                'category_name' => 'Sales',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Viewer',
                'category_name' => 'Production',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Viewer',
                'category_name' => 'Inventory',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
            [
                'role_name' => 'Viewer',
                'category_name' => 'Support',
                'department_type' => 'branch_wide',
                'allowed_department_slugs' => null,
            ],
        ];

        foreach ($constraints as $constraintData) {
            $category = DepartmentCategory::where('name', $constraintData['category_name'])->first();

            if (!$category) {
                $this->command->warn("⚠️ Category '{$constraintData['category_name']}' not found, skipping constraint for '{$constraintData['role_name']}'");
                continue;
            }

            RoleCategoryConstraint::updateOrCreate(
                [
                    'role_name' => $constraintData['role_name'],
                    'category_id' => $category->id,
                ],
                [
                    'department_type' => $constraintData['department_type'],
                    'allowed_department_slugs' => $constraintData['allowed_department_slugs'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Role category constraints seeded successfully for simplified roles');
    }
}