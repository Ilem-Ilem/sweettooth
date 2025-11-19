<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentPage;
use Illuminate\Database\Seeder;

class SalesReportPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all sales departments
        $salesDepartments = Department::whereHas('category', function ($query) {
            $query->where('name', 'Sales');
        })->get();

        $reportPages = $this->getSalesReportPages();

        foreach ($salesDepartments as $department) {
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

        $this->command->info('Sales report pages seeded successfully for '.$salesDepartments->count().' sales departments.');
    }

    /**
     * Get sales report pages configuration.
     */
    protected function getSalesReportPages(): array
    {
        return [
            [
                'name' => 'Sales Performance Report',
                'slug' => 'report-sales-performance',
                'route_name' => 'branch-dashboard.sales.reports.performance',
                'icon' => 'chart-line',
                'order' => 21,
                'description' => 'Revenue analysis, product performance, and sales trends',
                'is_active' => true, // Implemented
            ],
            [
                'name' => 'Sales by Employee Report',
                'slug' => 'report-sales-employee',
                'route_name' => 'branch-dashboard.sales.reports.employee',
                'icon' => 'user-group',
                'order' => 22,
                'description' => 'Individual salesperson performance metrics',
                'is_active' => false, // Not yet implemented
            ],
            [
                'name' => 'Customer Analysis Report',
                'slug' => 'report-customer-analysis',
                'route_name' => 'branch-dashboard.sales.reports.customer',
                'icon' => 'users',
                'order' => 23,
                'description' => 'Customer behavior, preferences, and retention analysis',
                'is_active' => false, // Not yet implemented
            ],
            [
                'name' => 'Payment Method Report',
                'slug' => 'report-payment-method',
                'route_name' => 'branch-dashboard.sales.reports.payment',
                'icon' => 'credit-card',
                'order' => 24,
                'description' => 'Payment method breakdown and reconciliation',
                'is_active' => false, // Not yet implemented
            ],
        ];
    }
}
