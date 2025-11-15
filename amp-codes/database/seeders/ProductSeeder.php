<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $downBranch = Branch::where('code', 'DB002')->first();
        $airportBranch = Branch::where('code', 'AB003')->first();

        $gelatoBase = ProductType::where('code', 'GB')->first();
        $gelatoFlavor = ProductType::where('code', 'GF')->first();
        $pastry = ProductType::where('code', 'PT')->first();
        $cake = ProductType::where('code', 'CK')->first();
        $beverage = ProductType::where('code', 'BEV')->first();

        // Gelato Base Products
        $products = [
            [
                'name' => 'Classic Vanilla Base',
                'sku' => 'GEL-VAN-001',
                'product_type_id' => $gelatoBase->id,
                'description' => 'Traditional vanilla gelato base',
                'price' => 5.50,
                'cost' => 2.00,
                'shelf_life_days' => 30,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Chocolate Gelato',
                'sku' => 'GEL-CHO-001',
                'product_type_id' => $gelatoFlavor->id,
                'description' => 'Rich dark chocolate gelato',
                'price' => 6.50,
                'cost' => 2.50,
                'shelf_life_days' => 30,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Strawberry Gelato',
                'sku' => 'GEL-STR-001',
                'product_type_id' => $gelatoFlavor->id,
                'description' => 'Fresh strawberry gelato',
                'price' => 6.00,
                'cost' => 2.20,
                'shelf_life_days' => 30,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Pistachio Gelato',
                'sku' => 'GEL-PIS-001',
                'product_type_id' => $gelatoFlavor->id,
                'description' => 'Sicilian pistachio gelato',
                'price' => 7.50,
                'cost' => 3.00,
                'shelf_life_days' => 30,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Croissant',
                'sku' => 'PAS-CRO-001',
                'product_type_id' => $pastry->id,
                'description' => 'Butter croissant',
                'price' => 3.50,
                'cost' => 1.20,
                'shelf_life_days' => 2,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Danish Pastry',
                'sku' => 'PAS-DAN-001',
                'product_type_id' => $pastry->id,
                'description' => 'Danish with fruit filling',
                'price' => 4.00,
                'cost' => 1.50,
                'shelf_life_days' => 2,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Chocolate Cake',
                'sku' => 'CAK-CHO-001',
                'product_type_id' => $cake->id,
                'description' => 'Decadent chocolate cake',
                'price' => 25.00,
                'cost' => 8.00,
                'shelf_life_days' => 3,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Cheesecake',
                'sku' => 'CAK-CHE-001',
                'product_type_id' => $cake->id,
                'description' => 'New York style cheesecake',
                'price' => 28.00,
                'cost' => 9.00,
                'shelf_life_days' => 4,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Espresso',
                'sku' => 'BEV-ESP-001',
                'product_type_id' => $beverage->id,
                'description' => 'Single shot espresso',
                'price' => 2.50,
                'cost' => 0.50,
                'shelf_life_days' => 1,
                'uom' => 'pcs',
            ],
            [
                'name' => 'Cappuccino',
                'sku' => 'BEV-CAP-001',
                'product_type_id' => $beverage->id,
                'description' => 'Cappuccino with foam',
                'price' => 4.00,
                'cost' => 1.00,
                'shelf_life_days' => 1,
                'uom' => 'pcs',
            ],
        ];

        // Create products for each branch
        foreach ($products as $product) {
            // Main branch
            Product::create(array_merge($product, [
                'id' => (string) Str::uuid(),
                'branch_id' => $mainBranch->id,
                'is_active' => true,
                'is_available' => true,
                'recipe_yield' => 1,
                'yield_percentage' => 100,
                'allergens' => ['gluten', 'dairy'],
                'tags' => ['popular', 'bestseller'],
            ]));

            // Downtown branch
            Product::create(array_merge($product, [
                'id' => (string) Str::uuid(),
                'branch_id' => $downBranch->id,
                'is_active' => true,
                'is_available' => true,
                'recipe_yield' => 1,
                'yield_percentage' => 100,
                'allergens' => ['gluten', 'dairy'],
                'tags' => ['popular', 'bestseller'],
            ]));

            // Airport branch
            Product::create(array_merge($product, [
                'id' => (string) Str::uuid(),
                'branch_id' => $airportBranch->id,
                'is_active' => true,
                'is_available' => true,
                'recipe_yield' => 1,
                'yield_percentage' => 100,
                'allergens' => ['gluten', 'dairy'],
                'tags' => ['popular', 'bestseller'],
            ]));
        }
    }
}
