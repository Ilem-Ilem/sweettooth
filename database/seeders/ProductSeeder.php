<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first branch (or all branches)
        $branch = \App\Models\Branch::first();

        if (!$branch) {
            $this->command->error('No branches found! Please seed branches first.');
            return;
        }

        // Get product types
        $pastries = ProductType::where('code', 'PT')->first();
        $breads = ProductType::where('code', 'BR')->first();
        $cakes = ProductType::where('code', 'CK')->first();
        $cookies = ProductType::where('code', 'CO')->first();
        $gelatoFlavors = ProductType::where('code', 'GF')->first();
        $gelatoBase = ProductType::where('code', 'GB')->first();
        $chocolates = ProductType::where('code', 'CH')->first();
        $candies = ProductType::where('code', 'CD')->first();

        $products = [
            // Pastries
            [
                'branch_id' => $branch->id,
                'name' => 'Butter Croissant',
                'sku' => 'PT-BUT-001',
                'product_type_id' => $pastries->id,
                'description' => 'Classic French butter croissant, flaky and golden',
                'price' => 3.50,
                'cost' => 1.20,
                'shelf_life_days' => 2,
                'uom' => 'pcs',
                'recipe_yield' => 24,
                'recipe_yield_weight' => 2400,
                'unit_weight' => 100,
                'yield_percentage' => 95,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'tags' => ['breakfast', 'french', 'popular'],
            ],
            [
                'branch_id' => $branch->id,
                'name' => 'Almond Danish',
                'sku' => 'PT-ALM-002',
                'product_type_id' => $pastries->id,
                'description' => 'Sweet Danish pastry topped with sliced almonds',
                'price' => 4.00,
                'cost' => 1.50,
                'shelf_life_days' => 2,
                'uom' => 'pcs',
                'recipe_yield' => 18,
                'recipe_yield_weight' => 2700,
                'unit_weight' => 150,
                'yield_percentage' => 93,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten', 'dairy', 'eggs', 'nuts'],
                'tags' => ['breakfast', 'pastry'],
            ],

            // Breads
            [
                'branch_id' => $branch->id,
                'name' => 'Sourdough Loaf',
                'sku' => 'BR-SOU-001',
                'product_type_id' => $breads->id,
                'description' => 'Artisan sourdough bread with tangy flavor',
                'price' => 6.50,
                'cost' => 2.00,
                'shelf_life_days' => 4,
                'uom' => 'pcs',
                'recipe_yield' => 4,
                'recipe_yield_weight' => 3200,
                'unit_weight' => 800,
                'yield_percentage' => 90,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten'],
                'tags' => ['artisan', 'sourdough'],
            ],
            [
                'branch_id' => $branch->id,
                'name' => 'Banana Bread',
                'sku' => 'BR-BAN-002',
                'product_type_id' => $breads->id,
                'description' => 'Moist banana bread with walnuts',
                'price' => 5.99,
                'cost' => 2.50,
                'shelf_life_days' => 7,
                'uom' => 'pcs',
                'recipe_yield' => 2,
                'recipe_yield_weight' => 1800,
                'unit_weight' => 900,
                'yield_percentage' => 92,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten', 'eggs', 'nuts'],
                'tags' => ['sweet', 'popular'],
            ],

            // Cakes
            [
                'branch_id' => $branch->id,
                'name' => 'Chocolate Cake Slice',
                'sku' => 'CK-CHO-001',
                'product_type_id' => $cakes->id,
                'description' => 'Rich chocolate layer cake with chocolate ganache',
                'price' => 7.50,
                'cost' => 2.80,
                'shelf_life_days' => 5,
                'uom' => 'pcs',
                'recipe_yield' => 12,
                'recipe_yield_weight' => 2400,
                'unit_weight' => 200,
                'yield_percentage' => 95,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'tags' => ['chocolate', 'dessert', 'popular'],
            ],

            // Cookies
            [
                'branch_id' => $branch->id,
                'name' => 'Chocolate Chip Cookie',
                'sku' => 'CO-CHI-001',
                'product_type_id' => $cookies->id,
                'description' => 'Classic chocolate chip cookies',
                'price' => 2.50,
                'cost' => 0.80,
                'shelf_life_days' => 10,
                'uom' => 'pcs',
                'recipe_yield' => 36,
                'recipe_yield_weight' => 1800,
                'unit_weight' => 50,
                'yield_percentage' => 96,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'tags' => ['cookie', 'popular'],
            ],
            [
                'branch_id' => $branch->id,
                'name' => 'Oatmeal Raisin Cookie',
                'sku' => 'CO-OAT-002',
                'product_type_id' => $cookies->id,
                'description' => 'Wholesome oatmeal cookies with plump raisins',
                'price' => 2.50,
                'cost' => 0.75,
                'shelf_life_days' => 10,
                'uom' => 'pcs',
                'recipe_yield' => 40,
                'recipe_yield_weight' => 2000,
                'unit_weight' => 50,
                'yield_percentage' => 96,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['gluten', 'dairy', 'eggs'],
                'tags' => ['cookie', 'healthy'],
            ],

            // Gelato Base
            [
                'branch_id' => $branch->id,
                'name' => 'Vanilla Gelato Base',
                'sku' => 'GB-VAN-001',
                'product_type_id' => $gelatoBase->id,
                'description' => 'Premium vanilla gelato base mixture',
                'price' => 0.00,
                'cost' => 8.50,
                'shelf_life_days' => 3,
                'uom' => 'kg',
                'recipe_yield' => 5,
                'recipe_yield_weight' => 5000,
                'unit_weight' => 1000,
                'yield_percentage' => 98,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['dairy'],
                'tags' => ['gelato', 'base'],
            ],

            // Gelato Flavors
            [
                'branch_id' => $branch->id,
                'name' => 'Chocolate Gelato',
                'sku' => 'GF-CHO-001',
                'product_type_id' => $gelatoFlavors->id,
                'description' => 'Rich dark chocolate gelato',
                'price' => 4.50,
                'cost' => 1.80,
                'shelf_life_days' => 30,
                'uom' => 'grams',
                'recipe_yield' => 5000,
                'recipe_yield_weight' => 5000,
                'unit_weight' => 100,
                'yield_percentage' => 97,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['dairy'],
                'tags' => ['gelato', 'chocolate', 'popular'],
            ],
            [
                'branch_id' => $branch->id,
                'name' => 'Strawberry Gelato',
                'sku' => 'GF-STR-002',
                'product_type_id' => $gelatoFlavors->id,
                'description' => 'Fresh strawberry gelato',
                'price' => 4.50,
                'cost' => 1.90,
                'shelf_life_days' => 30,
                'uom' => 'grams',
                'recipe_yield' => 5000,
                'recipe_yield_weight' => 5000,
                'unit_weight' => 100,
                'yield_percentage' => 97,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['dairy'],
                'tags' => ['gelato', 'fruit'],
            ],
            [
                'branch_id' => $branch->id,
                'name' => 'Pistachio Gelato',
                'sku' => 'GF-PIS-003',
                'product_type_id' => $gelatoFlavors->id,
                'description' => 'Authentic pistachio gelato from Sicily',
                'price' => 5.50,
                'cost' => 2.50,
                'shelf_life_days' => 30,
                'uom' => 'grams',
                'recipe_yield' => 5000,
                'recipe_yield_weight' => 5000,
                'unit_weight' => 100,
                'yield_percentage' => 96,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['dairy', 'nuts'],
                'tags' => ['gelato', 'premium', 'nuts'],
            ],

            // Chocolates
            [
                'branch_id' => $branch->id,
                'name' => 'Dark Chocolate Truffle',
                'sku' => 'CH-DAR-001',
                'product_type_id' => $chocolates->id,
                'description' => 'Hand-rolled dark chocolate truffle',
                'price' => 2.50,
                'cost' => 0.90,
                'shelf_life_days' => 21,
                'uom' => 'pcs',
                'recipe_yield' => 50,
                'recipe_yield_weight' => 1000,
                'unit_weight' => 20,
                'yield_percentage' => 94,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['dairy'],
                'tags' => ['chocolate', 'premium', 'truffle'],
            ],
            [
                'branch_id' => $branch->id,
                'name' => 'Salted Caramel Chocolate',
                'sku' => 'CH-SAL-002',
                'product_type_id' => $chocolates->id,
                'description' => 'Milk chocolate with salted caramel filling',
                'price' => 2.75,
                'cost' => 1.00,
                'shelf_life_days' => 21,
                'uom' => 'pcs',
                'recipe_yield' => 48,
                'recipe_yield_weight' => 1200,
                'unit_weight' => 25,
                'yield_percentage' => 93,
                'is_active' => true,
                'is_available' => true,
                'allergens' => ['dairy'],
                'tags' => ['chocolate', 'caramel', 'popular'],
            ],

            // Candies
            [
                'branch_id' => $branch->id,
                'name' => 'Fruit Gummies',
                'sku' => 'CD-FRU-001',
                'product_type_id' => $candies->id,
                'description' => 'Assorted fruit-flavored gummy candies',
                'price' => 1.50,
                'cost' => 0.40,
                'shelf_life_days' => 180,
                'uom' => 'grams',
                'recipe_yield' => 2000,
                'recipe_yield_weight' => 2000,
                'unit_weight' => 100,
                'yield_percentage' => 95,
                'is_active' => true,
                'is_available' => true,
                'allergens' => [],
                'tags' => ['candy', 'fruit', 'kids'],
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info("✅ " . count($products) . " products created successfully with recipe yield data.");
    }
}
