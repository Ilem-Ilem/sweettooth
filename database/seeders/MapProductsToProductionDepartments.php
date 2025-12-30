<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Department;
use Illuminate\Database\Seeder;

class MapProductsToProductionDepartments extends Seeder
{
    /**
     * Map products to their production departments
     * 
     * Architecture:
     * - Bakery products (Bread, Croissants, Cookies) → Kitchen
     * - Gelato products → Gelato Production  
     * - Chocolates & Candies → Confectionaries Production
     */
    public function run(): void
    {
        // Get production departments
        $kitchen = Department::where('name', 'Kitchen')->first();
        $gelatoProduction = Department::where('name', 'Gelato Production')->first();
        $confectionariesProduction = Department::where('name', 'Confectionaries Production')->first();

        // Bakery/Kitchen products
        $bakeryProducts = [
            'Almond Danish',
            'Banana Bread',
            'Butter Croissant',
            'Chocolate Cake Slice',
            'Chocolate Chip Cookie',
            'Oatmeal Raisin Cookie',
            'Sourdough Loaf',
        ];

        // Gelato products
        $gelatoProducts = [
            'Chocolate Gelato',
            'Pistachio Gelato',
            'Strawberry Gelato',
            'Vanilla Gelato Base',
        ];

        // Chocolate/Confectionaries products
        $confectioneryProducts = [
            'Dark Chocolate Truffle',
            'Fruit Gummies',
            'Salted Caramel Chocolate',
        ];

        $created = 0;
        $skipped = 0;

        // Map bakery products to Kitchen
        if ($kitchen) {
            foreach ($bakeryProducts as $productName) {
                $product = Product::where('name', $productName)->first();
                if ($product) {
                    $existing = $kitchen->products()
                        ->where('product_id', $product->id)
                        ->exists();

                    if ($existing) {
                        $skipped++;
                    } else {
                        $kitchen->products()->attach($product->id, ['is_available' => 1]);
                        $created++;
                    }
                }
            }
        }

        // Map gelato products to Gelato Production
        if ($gelatoProduction) {
            foreach ($gelatoProducts as $productName) {
                $product = Product::where('name', $productName)->first();
                if ($product) {
                    $existing = $gelatoProduction->products()
                        ->where('product_id', $product->id)
                        ->exists();

                    if ($existing) {
                        $skipped++;
                    } else {
                        $gelatoProduction->products()->attach($product->id, ['is_available' => 1]);
                        $created++;
                    }
                }
            }
        }

        // Map confectionery products to Confectionaries Production
        if ($confectionariesProduction) {
            foreach ($confectioneryProducts as $productName) {
                $product = Product::where('name', $productName)->first();
                if ($product) {
                    $existing = $confectionariesProduction->products()
                        ->where('product_id', $product->id)
                        ->exists();

                    if ($existing) {
                        $skipped++;
                    } else {
                        $confectionariesProduction->products()->attach($product->id, ['is_available' => 1]);
                        $created++;
                    }
                }
            }
        }

        $this->command->info("Mapped $created products to production departments");
        if ($skipped > 0) {
            $this->command->info("Skipped $skipped products (already mapped)");
        }
    }
}
