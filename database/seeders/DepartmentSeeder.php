<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Branch;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all branches with their codes
        $branches = Branch::select('id', 'code', 'name')->get();

        // General departments (branch_id = null) - these are non-branch dependent
        $generalDepartments = [
            ['name' => 'Human Resources', 'type' => 'sales', 'description' => 'Company-wide HR management and employee relations'],
            ['name' => 'Finance & Accounting', 'type' => 'sales', 'description' => 'Central financial management and accounting'],
            ['name' => 'Information Technology', 'type' => 'sales', 'description' => 'IT infrastructure and software development'],
            ['name' => 'Legal & Compliance', 'type' => 'sales', 'description' => 'Legal affairs and regulatory compliance'],
            ['name' => 'Corporate Strategy', 'type' => 'sales', 'description' => 'Strategic planning and business development'],
            ['name' => 'Research & Development', 'type' => 'production', 'description' => 'Product innovation and development'],
            ['name' => 'Quality Assurance', 'type' => 'production', 'description' => 'Company-wide quality control and standards'],
            ['name' => 'Procurement', 'type' => 'sales', 'description' => 'Central procurement and supplier management'],
            ['name' => 'Marketing & Communications', 'type' => 'sales', 'description' => 'Brand management and corporate communications'],
            ['name' => 'Customer Service', 'type' => 'sales', 'description' => 'Customer support and relations'],
        ];

        // Branch-specific production departments
        $productionDepartments = [
            ['name' => 'Production Line A', 'type' => 'production', 'description' => 'Primary production line for food processing'],
            ['name' => 'Production Line B', 'type' => 'production', 'description' => 'Secondary production line for food processing'],
            ['name' => 'Packaging Department', 'type' => 'production', 'description' => 'Product packaging and labeling'],
            ['name' => 'Quality Control Lab', 'type' => 'production', 'description' => 'Quality testing and control'],
            ['name' => 'Warehouse & Logistics', 'type' => 'production', 'description' => 'Inventory and distribution management'],
            ['name' => 'Maintenance & Engineering', 'type' => 'production', 'description' => 'Equipment maintenance and repair'],
            ['name' => 'Cold Storage Operations', 'type' => 'production', 'description' => 'Temperature-controlled storage management'],
        ];

        // Branch-specific sales departments
        $salesDepartments = [
            ['name' => 'Sales & Distribution', 'type' => 'sales', 'description' => 'Regional sales and product distribution'],
            ['name' => 'Customer Relations', 'type' => 'sales', 'description' => 'Customer engagement and support'],
            ['name' => 'Regional Marketing', 'type' => 'sales', 'description' => 'Local marketing and promotional activities'],
        ];

        // Insert general departments (no branch_id)
        foreach ($generalDepartments as $dept) {
            Department::create([
                'name' => $dept['name'],
                'type' => $dept['type'],
                'description' => $dept['description'],
                'branch_id' => null,
            ]);
        }

        // Insert branch-specific departments for each branch
        if ($branches->isNotEmpty()) {
            // Assign production departments to first 10 branches (processing/production facilities)
            foreach ($branches->take(10) as $branch) {
                foreach ($productionDepartments as $dept) {
                    Department::create([
                        'name' => $dept['name'] . ' - ' . $branch->code,
                        'type' => $dept['type'],
                        'description' => $dept['description'] . ' at ' . $branch->name,
                        'branch_id' => $branch->id,
                    ]);
                }
            }

            // Assign sales departments to all branches
            foreach ($branches as $branch) {
                foreach ($salesDepartments as $dept) {
                    Department::create([
                        'name' => $dept['name'] . ' - ' . $branch->code,
                        'type' => $dept['type'],
                        'description' => $dept['description'] . ' at ' . $branch->name,
                        'branch_id' => $branch->id,
                    ]);
                }
            }
        }

        $totalDepartments = Department::count();
        $this->command->info("✅ {$totalDepartments} departments created successfully.");
        $this->command->info("   - General (non-branch) departments: " . count($generalDepartments));
        $this->command->info("   - Branch-specific departments: " . ($totalDepartments - count($generalDepartments)));
    }
}
