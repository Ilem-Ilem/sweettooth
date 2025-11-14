<?php

namespace App\Observers;

use App\Models\Department;
use Illuminate\Support\Str;
use App\Models\DepartmentPage;

class SalesPageObserver
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
        if ($department->category && $department->category->name === 'Sales') {
            $defaultPages = $this->getDefaultSalesPages($department);

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
    protected function getDefaultSalesPages(Department $department): array
    {
        return [
            [
                'name' => 'POS',
                'slug' => 'pos',
                'route_name' => "branch-dashboard.sales-dashboard.pos.index",
                'icon' => 'shopping-cart',
                'order' => 1,
            ],
            [
                'name' => 'My Sales',
                'slug' => 'my-sales',
                'route_name' => 'branch-dashboard.sales-dashboard.my-sales.index',
                'icon' => 'user-circle',
                'order' => 2,
            ],
            [
                'name'=> 'Sales Analytics',
                'slug'=>'sales-analytics',
                'route_name'=> 'branch-dashboard.sales-dashboard.analytics.index',
                'icon'=>'chart-bar',
                'order'=>3
            ],
            [
                'name' => 'Shift Closing',
                'slug' => 'shift-closing',
                'route_name' => 'branch-dashboard.sales-dashboard.shift-closing.index',
                'icon' => 'clock',
                'order' => 4,
            ]
        ];
    }
}
