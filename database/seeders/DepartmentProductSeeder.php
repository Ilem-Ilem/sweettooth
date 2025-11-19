<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DepartmentProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Department-Product relationship seeding...');

        // Get all sales departments
        $salesDepartments = Department::whereHas('category', function ($q) {
            $q->where('name', 'Sales');
        })->get();

        if ($salesDepartments->isEmpty()) {
            $this->command->error('No sales departments found! Please seed departments first.');

            return;
        }

        // Get all products
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->error('No products found! Please seed products first.');

            return;
        }

        $totalRelationships = 0;

        // Map products to departments based on product type
        foreach ($salesDepartments as $department) {
            $departmentProducts = [];

            switch ($department->name) {
                case 'Till':
                    // Till sells pastries, cookies, breads (ready-made snacks)
                    $departmentProducts = $products->filter(function ($product) {
                        return in_array($product->productType->code, ['PT', 'CO', 'BR']);
                    });
                    break;

                case 'Corner Store':
                    // Corner Store sells hot food items (cakes, pastries, breads)
                    $departmentProducts = $products->filter(function ($product) {
                        return in_array($product->productType->code, ['CK', 'PT', 'BR']);
                    });
                    break;

                case 'Confectionaries Sales':
                    // Confectionaries sells chocolates, candies, gelato
                    $departmentProducts = $products->filter(function ($product) {
                        return in_array($product->productType->code, ['CH', 'CD', 'GF']);
                    });
                    break;

                default:
                    // For any other sales department, add all products
                    $departmentProducts = $products;
                    break;
            }

            // Attach products to department with additional data
            $sortOrder = 0;
            foreach ($departmentProducts as $product) {
                $department->products()->attach($product->id, [
                    'is_available' => true,
                    'department_price' => null, // Use product's base price
                    'sort_order' => $sortOrder++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $totalRelationships++;
            }

            $this->command->info("✓ Linked {$departmentProducts->count()} products to {$department->name}");
        }

        $this->command->info("✅ Total department-product relationships created: {$totalRelationships}");
    }
}
