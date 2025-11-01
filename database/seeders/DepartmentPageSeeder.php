<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\DepartmentPage;
use App\Models\DepartmentCategory;
use Illuminate\Support\Str;

class DepartmentPageSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the Production category
        $productionCategory = DepartmentCategory::where('name', '=', 'Production')->first();

        if (!$productionCategory) {
            $this->command->warn('Production category not found. Please create a Production category first.');
            return;
        }

        $this->command->info("Found category: {$productionCategory->name} (ID: {$productionCategory->id})");

        $departments = Department::where('category_id', $productionCategory->id)->get();

        if ($departments->isEmpty()) {
            $this->command->warn('No production departments found. Please create production departments first.');
            return;
        }

        $this->command->info("Found {$departments->count()} production departments");

        foreach ($departments as $department) {
            // Generate slug if not exists
            if (empty($department->slug)) {
                $department->slug = Str::slug($department->name);
                $department->save();
                $this->command->info("Generated slug '{$department->slug}' for department: {$department->name}");
            }

            // Seed default pages for this department
            $this->seedDepartmentPages($department);

            $this->command->info("✓ Seeded pages for department: {$department->name}");
        }

        $this->command->info('✓ Department pages seeded successfully!');
    }

    /**
     * Seed pages for a specific department
     */
    protected function seedDepartmentPages(Department $department): void
    {
        $deptSlug = $department->slug;

        $pages = [
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
                'route_name' => "branch-dashboard.production.{$deptSlug}.daily-produce.index",
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

        foreach ($pages as $pageData) {
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
