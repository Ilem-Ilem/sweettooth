<?php

namespace App\Observers;

use App\Models\Department;
use App\Models\DepartmentPage;
use Illuminate\Support\Str;

class DepartmentObserver
{
    /**
     * Handle the Department "created" event.
     */
    public function created(Department $department): void
    {
        // Auto-generate slug if not set
        if (empty($department->slug)) {
            $department->slug = Str::slug($department->name);
            $department->saveQuietly(); // Save without triggering events again
        }

        // Auto-seed default pages for production departments
        $this->seedDefaultPages($department);
    }

    /**
     * Handle the Department "updating" event.
     */
    public function updating(Department $department): void
    {
        // Auto-generate slug when name changes
        if ($department->isDirty('name') && empty($department->slug)) {
            $department->slug = Str::slug($department->name);
        }
    }

    /**
     * Seed default pages for a department
     */
    protected function seedDefaultPages(Department $department): void
    {
        // Check if this is a production department
        if ($department->category && $department->category->name === 'Production') {
            $defaultPages = $this->getDefaultProductionPages($department);

            foreach ($defaultPages as $pageData) {
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
    }

    /**
     * Get default production pages configuration
     */
    protected function getDefaultProductionPages(Department $department): array
    {
        return [
            // Products Management
            [
                'name' => 'Products',
                'slug' => 'products',
                'route_name' => "branch-dashboard.production.products",
                'icon' => 'cube',
                'order' => 1,
            ],
            [
                'name' => 'Product Types',
                'slug' => 'product-types',
                'route_name' => "branch-dashboard.production.product-types",
                'icon' => 'tag',
                'order' => 2,
            ],

            // Recipe Management
            [
                'name' => 'Recipes',
                'slug' => 'recipes',
                'route_name' => "branch-dashboard.production.recipes.index",
                'icon' => 'book-open',
                'order' => 3,
            ],
            [
                'name' => 'Add Recipe',
                'slug' => 'recipes-add',
                'route_name' => "branch-dashboard.production.recipes.add",
                'icon' => 'plus-circle',
                'order' => 4,
            ],
            [
                'name' => 'Edit Recipe',
                'slug' => 'recipes-edit',
                'route_name' => "branch-dashboard.production.recipes.edit",
                'icon' => 'pencil',
                'order' => 5,
            ],
            [
                'name' => 'Recipe Detail',
                'slug' => 'recipes-detail',
                'route_name' => "branch-dashboard.production.recipes.detail",
                'icon' => 'document-text',
                'order' => 6,
            ],

            // Production Operations
            [
                'name' => 'Production Requests',
                'slug' => 'production-requests',
                'route_name' => "branch-dashboard.production.request.index",
                'icon' => 'clipboard',
                'order' => 7,
            ],
            [
                'name' => 'Daily Produce',
                'slug' => 'daily-produce',
                'route_name' => "branch-dashboard.production.daily-produce.index",
                'icon' => 'calendar',
                'order' => 8,
            ],

            // Inventory & Tracking
            [
                'name' => 'Raw Material Tracking',
                'slug' => 'raw-material-tracking',
                'route_name' => "branch-dashboard.production.raw-material-tracking",
                'icon' => 'chart-bar',
                'order' => 9,
            ],

            // Shift Closing
            [
                'name' => 'Shift Closing',
                'slug' => 'shift-closing',
                'route_name' => "branch-dashboard.production.shift-closing.index",
                'icon' => 'clock',
                'order' => 10,
            ],

            // Callbacks Section
            [
                'name' => 'View Callbacks',
                'slug' => 'callbacks-index',
                'route_name' => "branch-dashboard.production.callbacks.index",
                'icon' => 'arrow-path',
                'order' => 11,
            ],
            [
                'name' => 'Create Inventory Callback',
                'slug' => 'callbacks-create',
                'route_name' => "branch-dashboard.production.callbacks.create-inventory",
                'icon' => 'plus-circle',
                'order' => 12,
            ],
            [
                'name' => 'Approve Sales Callbacks',
                'slug' => 'callbacks-approve',
                'route_name' => "branch-dashboard.production.callbacks.approve-sales-callbacks",
                'icon' => 'check-circle',
                'order' => 13,
            ],

            // Kitchen Module
            [
                'name' => 'Kitchen Dashboard',
                'slug' => 'kitchen-module',
                'route_name' => "branch-dashboard.production.module.index",
                'icon' => 'home',
                'order' => 14,
            ],
            [
                'name' => 'Stock Monitor',
                'slug' => 'stock-monitor',
                'route_name' => "branch-dashboard.production.module.stock-monitor",
                'icon' => 'chart-bar',
                'order' => 15,
            ],

            // Production Reports Section
            [
                'name' => 'Production Efficiency Report',
                'slug' => 'report-efficiency',
                'route_name' => "branch-dashboard.production.reports.efficiency",
                'icon' => 'chart-bar',
                'order' => 16,
            ],
            [
                'name' => 'Quality Metrics Report',
                'slug' => 'report-quality',
                'route_name' => "branch-dashboard.production.reports.quality",
                'icon' => 'shield-check',
                'order' => 17,
            ],
            [
                'name' => 'Waste Analysis Report',
                'slug' => 'report-waste',
                'route_name' => "branch-dashboard.production.reports.waste",
                'icon' => 'trash',
                'order' => 18,
            ],
            [
                'name' => 'Cost Analysis Report',
                'slug' => 'report-cost',
                'route_name' => "branch-dashboard.production.reports.cost",
                'icon' => 'currency-dollar',
                'order' => 19,
            ],
            [
                'name' => 'Recipe Performance Report',
                'slug' => 'report-recipe-performance',
                'route_name' => "branch-dashboard.production.reports.recipe-performance",
                'icon' => 'star',
                'order' => 20,
            ],
            [
                'name' => 'Shift Summary Report',
                'slug' => 'report-shift-summary',
                'route_name' => "branch-dashboard.production.reports.shift-summary",
                'icon' => 'clock',
                'order' => 21,
            ],
            [
                'name' => 'Ingredient Utilization Report',
                'slug' => 'report-ingredient-utilization',
                'route_name' => "branch-dashboard.production.reports.ingredient-utilization",
                'icon' => 'beaker',
                'order' => 22,
            ],
            [
                'name' => 'Pipeline Status Report',
                'slug' => 'report-pipeline',
                'route_name' => "branch-dashboard.production.reports.pipeline",
                'icon' => 'arrow-right-circle',
                'order' => 23,
            ],
            [
                'name' => 'Capacity Planning Report',
                'slug' => 'report-capacity',
                'route_name' => "branch-dashboard.production.reports.capacity",
                'icon' => 'server',
                'order' => 24,
            ],
        ];
    }
}
