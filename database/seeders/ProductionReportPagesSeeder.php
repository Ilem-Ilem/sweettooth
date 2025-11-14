<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentPage;
use Illuminate\Database\Seeder;

class ProductionReportPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all production departments
        $productionDepartments = Department::whereHas('category', function ($query) {
            $query->where('name', 'Production');
        })->get();

        $reportPages = $this->getProductionReportPages();

        foreach ($productionDepartments as $department) {
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
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Production report pages seeded successfully for ' . $productionDepartments->count() . ' production departments.');
    }

    /**
     * Get production report pages configuration.
     */
    protected function getProductionReportPages(): array
    {
        return [
            [
                'name' => 'Production Efficiency Report',
                'slug' => 'report-efficiency',
                'route_name' => 'branch-dashboard.production.reports.efficiency',
                'icon' => 'chart-bar',
                'order' => 11,
                'description' => 'Track production output, efficiency rates, and variance analysis',
            ],
            [
                'name' => 'Quality Metrics Report',
                'slug' => 'report-quality',
                'route_name' => 'branch-dashboard.production.reports.quality',
                'icon' => 'shield-check',
                'order' => 12,
                'description' => 'Monitor quality control, rejection rates, and callback analysis',
            ],
            [
                'name' => 'Waste Analysis Report',
                'slug' => 'report-waste',
                'route_name' => 'branch-dashboard.production.reports.waste',
                'icon' => 'trash',
                'order' => 13,
                'description' => 'Analyze production waste, callbacks, and cost impact',
            ],
            [
                'name' => 'Cost Analysis Report',
                'slug' => 'report-cost',
                'route_name' => 'branch-dashboard.production.reports.cost',
                'icon' => 'currency-dollar',
                'order' => 14,
            ],
            [
                'name' => 'Recipe Performance Report',
                'slug' => 'report-recipe-performance',
                'route_name' => 'branch-dashboard.production.reports.recipe-performance',
                'icon' => 'star',
                'order' => 15,
            ],
            [
                'name' => 'Shift Summary Report',
                'slug' => 'report-shift-summary',
                'route_name' => 'branch-dashboard.production.reports.shift-summary',
                'icon' => 'clock',
                'order' => 16,
            ],
            [
                'name' => 'Ingredient Utilization Report',
                'slug' => 'report-ingredient-utilization',
                'route_name' => 'branch-dashboard.production.reports.ingredient-utilization',
                'icon' => 'beaker',
                'order' => 17,
            ],
            [
                'name' => 'Pipeline Status Report',
                'slug' => 'report-pipeline',
                'route_name' => 'branch-dashboard.production.reports.pipeline',
                'icon' => 'arrow-right-circle',
                'order' => 18,
            ],
            [
                'name' => 'Capacity Planning Report',
                'slug' => 'report-capacity',
                'route_name' => 'branch-dashboard.production.reports.capacity',
                'icon' => 'server',
                'order' => 19,
            ],
        ];
    }
}
