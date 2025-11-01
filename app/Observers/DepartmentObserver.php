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

            foreach ($defaultPages as $page) {
                DepartmentPage::create([
                    'department_id' => $department->id,
                    'name' => $page['name'],
                    'slug' => $page['slug'],
                    'route_name' => $page['route_name'],
                    'icon' => $page['icon'],
                    'order' => $page['order'],
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Get default production pages configuration
     */
    protected function getDefaultProductionPages(Department $department): array
    {
        $deptSlug = $department->slug;

        return [
            // Products Management
            [
                'name' => 'Products',
                'slug' => 'products',
                'route_name' => "branch-dashboard.production.{$deptSlug}.products",
                'icon' => 'heroicon-o-cube',
                'order' => 1,
            ],
            [
                'name' => 'Product Types',
                'slug' => 'product-types',
                'route_name' => "branch-dashboard.production.{$deptSlug}.product-types",
                'icon' => 'heroicon-o-tag',
                'order' => 2,
            ],

            // Recipe Management
            [
                'name' => 'Recipes',
                'slug' => 'recipes',
                'route_name' => "branch-dashboard.production.{$deptSlug}.recipes.index",
                'icon' => 'heroicon-o-book-open',
                'order' => 3,
            ],
            [
                'name' => 'Add Recipe',
                'slug' => 'recipes-add',
                'route_name' => "branch-dashboard.production.{$deptSlug}.recipes.add",
                'icon' => 'heroicon-o-plus-circle',
                'order' => 4,
            ],
            [
                'name' => 'Edit Recipe',
                'slug' => 'recipes-edit',
                'route_name' => "branch-dashboard.production.{$deptSlug}.recipes.edit",
                'icon' => 'heroicon-o-pencil',
                'order' => 5,
            ],
            [
                'name' => 'Recipe Detail',
                'slug' => 'recipes-detail',
                'route_name' => "branch-dashboard.production.{$deptSlug}.recipes.detail",
                'icon' => 'heroicon-o-document-text',
                'order' => 6,
            ],

            // Production Operations
            [
                'name' => 'Production Requests',
                'slug' => 'production-requests',
                'route_name' => "branch-dashboard.production.{$deptSlug}.production-requests",
                'icon' => 'heroicon-o-clipboard-list',
                'order' => 7,
            ],
            [
                'name' => 'Daily Produce',
                'slug' => 'daily-produce',
                'route_name' => "branch-dashboard.production.{$deptSlug}.daily-produce",
                'icon' => 'heroicon-o-calendar',
                'order' => 8,
            ],

            // Inventory & Tracking
            [
                'name' => 'Raw Material Tracking',
                'slug' => 'raw-material-tracking',
                'route_name' => "branch-dashboard.production.{$deptSlug}.raw-material-tracking",
                'icon' => 'heroicon-o-chart-bar',
                'order' => 9,
            ],

            // Module & Stock Monitor
            [
                'name' => 'Module Index',
                'slug' => 'module-index',
                'route_name' => "branch-dashboard.production.{$deptSlug}.module.index",
                'icon' => 'heroicon-o-view-grid',
                'order' => 10,
            ],
            [
                'name' => 'Stock Monitor',
                'slug' => 'stock-monitor',
                'route_name' => "branch-dashboard.production.{$deptSlug}.module.stock-monitor",
                'icon' => 'heroicon-o-eye',
                'order' => 11,
            ],

            // Request Management
            [
                'name' => 'Create Request',
                'slug' => 'request-create',
                'route_name' => "branch-dashboard.production.{$deptSlug}.request.create",
                'icon' => 'heroicon-o-plus',
                'order' => 12,
            ],
            [
                'name' => 'View Requests',
                'slug' => 'request-index',
                'route_name' => "branch-dashboard.production.{$deptSlug}.request.index",
                'icon' => 'heroicon-o-inbox',
                'order' => 13,
            ],
        ];
    }
}
