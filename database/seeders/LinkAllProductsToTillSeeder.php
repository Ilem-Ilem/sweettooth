<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Department;
use Illuminate\Database\Seeder;

class LinkAllProductsToTillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Till department
        $tillDepartment = Department::where('name', 'Till')->first();
        
        if (!$tillDepartment) {
            $this->command->error('Till department not found!');
            return;
        }

        // Get all products
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->error('No products found!');
            return;
        }

        $linked = 0;
        foreach ($products as $product) {
            // Check if product is already linked to Till department
            $existing = $product->departments()->where('department_id', $tillDepartment->id)->first();

            if (!$existing) {
                // Link product to Till department
                $product->departments()->attach($tillDepartment->id, [
                    'is_available' => true,
                    'department_price' => $product->price, // Use product's default price
                    'sort_order' => 0,
                ]);
                $linked++;
            }
        }

        $this->command->info("Linked $linked products to Till department for POS testing.");
    }
}