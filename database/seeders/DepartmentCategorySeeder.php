<?php

namespace Database\Seeders;

use App\Models\DepartmentCategory;
use Illuminate\Database\Seeder;

class DepartmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Sales',
                'description' => 'Departments focused on selling products and services, customer acquisition, and revenue generation.',
            ],
            [
                'name' => 'Production',
                'description' => 'Departments responsible for manufacturing, production processes, and quality control.',
            ],
            [
                'name' => 'Support',
                'description' => 'Departments providing assistance, customer service, and technical support services.',
            ],
        ];

        foreach ($categories as $category) {
            DepartmentCategory::create($category);
        }
    }
}
