<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentCategory;
use Illuminate\Database\Seeder;

class CreateHRAndAccountingDepartmentsSeeder extends Seeder
{
    public function run(): void
    {
        // Get the Support category
        $supportCategory = DepartmentCategory::where('name', 'Support')->first();
        if (!$supportCategory) {
            $supportCategory = DepartmentCategory::create([
                'name' => 'Support',
                'description' => 'Support departments',
            ]);
        }

        // Get all branches
        $branches = Branch::all();

        foreach ($branches as $branch) {
            // Create HR Department
            $hrDept = Department::firstOrCreate(
                [
                    'branch_id' => $branch->id,
                    'slug' => 'hr',
                ],
                [
                    'category_id' => $supportCategory->id,
                    'name' => 'Human Resources',
                    'description' => 'Manages employee records, payroll, leave management, and organizational structure',
                    'enable_table_management' => false,
                ]
            );

            // Create Accounting Department
            $accountingDept = Department::firstOrCreate(
                [
                    'branch_id' => $branch->id,
                    'slug' => 'accounting',
                ],
                [
                    'category_id' => $supportCategory->id,
                    'name' => 'Accounting',
                    'description' => 'Manages financial records and accounting across all departments',
                    'enable_table_management' => false,
                ]
            );

            echo "Created HR and Accounting departments for branch: {$branch->name}\n";
        }
    }
}
