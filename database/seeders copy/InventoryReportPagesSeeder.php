<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentPage;
use Illuminate\Database\Seeder;

class InventoryReportPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Inventory department (usually branch-wide, not specific)
        $inventoryDepartments = Department::whereHas('category', function ($query) {
            $query->where('name', 'Inventory');
        })->get();

        $reportPages = $this->getInventoryReportPages();

        foreach ($inventoryDepartments as $department) {
            foreach ($reportPages as $pageData) {
                DepartmentPage::updateOrCreate(
                    [
                        'department_id' => $department->id,
                        'slug' => $pageData['slug'],
                    ],
                    [
                        'name' => $pageData['name'],
                        'route_name' => $pageData['route_name'],
                        'icon' => $pageData['icon'],
                        'order' => $pageData['order'],
                        'is_active' => $pageData['is_active'],
                    ]
                );
            }
        }

        $this->command->info('Inventory report pages seeded successfully for '.$inventoryDepartments->count().' inventory departments.');
    }

    /**
     * Get inventory report pages configuration.
     */
    protected function getInventoryReportPages(): array
    {
        return [
            [
                'name' => 'Stock Level Report',
                'slug' => 'report-stock-levels',
                'route_name' => 'branch-dashboard.inventory.reports.stock-levels',
                'icon' => 'cube',
                'order' => 31,
                'description' => 'Current stock levels, low stock alerts, and expiry tracking',
                'is_active' => true, // Implemented
            ],
            [
                'name' => 'Stock Movement Report',
                'slug' => 'report-stock-movement',
                'route_name' => 'branch-dashboard.inventory.reports.stock-movement',
                'icon' => 'arrows-right-left',
                'order' => 32,
                'description' => 'Track all stock movements - in, out, transfers, and adjustments',
                'is_active' => false, // Not yet implemented
            ],
            [
                'name' => 'Inventory Turnover Report',
                'slug' => 'report-inventory-turnover',
                'route_name' => 'branch-dashboard.inventory.reports.turnover',
                'icon' => 'arrow-path',
                'order' => 33,
                'description' => 'Stock rotation analysis and turnover rates',
                'is_active' => false, // Not yet implemented
            ],
            [
                'name' => 'Reorder Analysis Report',
                'slug' => 'report-reorder-analysis',
                'route_name' => 'branch-dashboard.inventory.reports.reorder',
                'icon' => 'shopping-cart',
                'order' => 34,
                'description' => 'Reorder point analysis and purchase recommendations',
                'is_active' => false, // Not yet implemented
            ],
            [
                'name' => 'Variance Report',
                'slug' => 'report-stock-variance',
                'route_name' => 'branch-dashboard.inventory.reports.variance',
                'icon' => 'exclamation-triangle',
                'order' => 35,
                'description' => 'Stock count variances and discrepancy analysis',
                'is_active' => false, // Not yet implemented
            ],
        ];
    }
}
