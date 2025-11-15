<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\DepartmentCategory;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $production = DepartmentCategory::where('name', 'Production')->first();
        $sales = DepartmentCategory::where('name', 'Sales')->first();
        $support = DepartmentCategory::where('name', 'Support')->first();

        $departments = [
            // PRODUCTION (makes products)
            [
                'name' => 'Kitchen',
                'category_id' => $production->id,
                'description' => 'Prepares food for Till, Confectionaries, Corner Store'
            ],
            [
                'name' => 'Gelato Production',
                'category_id' => $production->id,
                'description' => 'Makes gelato/ice cream'
            ],
            [
                'name' => 'Confectionaries Production',
                'category_id' => $production->id,
                'description' => 'Makes confectionery items'
            ],

            // SALES (sells products)
            [
                'name' => 'Till',
                'category_id' => $sales->id,
                'description' => 'Sells ready-made snacks'
            ],
            [
                'name' => 'Corner Store',
                'category_id' => $sales->id,
                'description' => 'On-demand food sales'
            ],
            [
                'name' => 'Confectionaries Sales',
                'category_id' => $sales->id,
                'description' => 'Sells confectionery items'
            ],

            // SUPPORT
            [
                'name' => 'Inventory/Store',
                'category_id' => $support->id,
                'description' => 'Manages all stock'
            ],
            [
                'name' => 'HR',
                'category_id' => $support->id,
                'description' => 'Human resources (corporate level)'
            ],
        ];

        foreach ($departments as $dept) {
            Department::create([
                'name' => $dept['name'],
                'category_id' => $dept['category_id'],
                'description' => $dept['description'],
                'branch_id' => null,
            ]);
        }

        $this->command->info("✅ " . count($departments) . " departments created successfully.");
    }
}
