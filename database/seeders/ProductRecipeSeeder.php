<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\UnitOfMeasure;
use Illuminate\Database\Seeder;

class ProductRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Product Recipe and Ingredients seeding...');

        // Get dependencies
        $branch = Branch::first();
        $productionDept = Department::whereHas('category', function ($q) {
            $q->where('name', 'Production');
        })->first();
        $employee = Employee::first();

        if (! $branch || ! $productionDept || ! $employee) {
            $this->command->error('Missing dependencies! Please seed branches, departments, and employees first.');

            return;
        }

        // Get items for ingredients (use branch-specific items)
        $items = Item::where('branch_id', $branch->id)->get();
        if ($items->isEmpty()) {
            $this->command->error('No items found! Please seed items first.');

            return;
        }

        // Map item names to items for easy lookup
        $itemMap = [];
        foreach ($items as $item) {
            $itemMap[$item->name] = $item;
        }

        // Get products
        $products = Product::where('branch_id', $branch->id)->get();
        if ($products->isEmpty()) {
            $this->command->error('No products found! Please seed products first.');

            return;
        }

        $totalRecipes = 0;
        $totalIngredients = 0;

        // Build ProductType lookup map
        $productTypeMap = [
            'pastry' => ProductType::where('name', 'Pastry')->first()?->id,
            'gelato_flavor' => ProductType::where('name', 'Gelato Flavor')->first()?->id,
            'hot_kitchen' => ProductType::where('name', 'Hot Kitchen')->first()?->id,
        ];

        // Build UOM lookup map
        $uomMap = [
            'grams' => UnitOfMeasure::where('code', 'g')->first()?->id,
            'kg' => UnitOfMeasure::where('code', 'kg')->first()?->id,
            'liters' => UnitOfMeasure::where('code', 'l')->first()?->id,
            'ml' => UnitOfMeasure::where('code', 'ml')->first()?->id,
            'pcs' => UnitOfMeasure::where('code', 'pcs')->first()?->id,
            'units' => UnitOfMeasure::where('code', 'unit')->first()?->id,
        ];

        // Recipe data with ingredients
        $recipesData = [
            'Butter Croissant' => [
                'product_type' => 'pastry',
                'yield_quantity' => 24,
                'preparation_time' => 240, // 4 hours
                'instructions' => json_encode([
                    'Mix flour, sugar, salt, and yeast in a large bowl',
                    'Add cold butter pieces and work into the dough',
                    'Knead until smooth and elastic',
                    'Rest dough in refrigerator for 2 hours',
                    'Roll out and fold dough multiple times (lamination)',
                    'Cut into triangles and roll into croissant shape',
                    'Proof for 1-2 hours until doubled',
                    'Brush with egg wash',
                    'Bake at 200°C for 15-18 minutes until golden',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 500, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Butter - Salted', 'quantity' => 300, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 50, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Milk - Fresh Whole', 'quantity' => 150, 'uom' => 'ml', 'cost_per_unit' => 0.003, 'waste_percentage' => 1],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.5, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                    ['item' => 'Yeast - Active Dry', 'quantity' => 15, 'uom' => 'grams', 'cost_per_unit' => 0.02, 'waste_percentage' => 0],
                    ['item' => 'Salt - Table Salt', 'quantity' => 10, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 0],
                ],
            ],
            'Chocolate Chip Cookie' => [
                'product_type' => 'pastry',
                'yield_quantity' => 36,
                'preparation_time' => 45,
                'instructions' => json_encode([
                    'Cream together butter and sugars',
                    'Beat in eggs and vanilla extract',
                    'Mix in flour, baking soda, and salt',
                    'Fold in chocolate chips',
                    'Scoop dough onto baking sheets',
                    'Bake at 180°C for 10-12 minutes',
                    'Cool on baking sheet for 5 minutes before transferring',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 280, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Butter - Salted', 'quantity' => 225, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.5, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                    ['item' => 'Vanilla Extract - Pure', 'quantity' => 0.01, 'uom' => 'liters', 'cost_per_unit' => 50, 'waste_percentage' => 0],
                    ['item' => 'Chocolate Chips - Dark', 'quantity' => 300, 'uom' => 'grams', 'cost_per_unit' => 0.015, 'waste_percentage' => 1],
                ],
            ],
            'Chocolate Cake Slice' => [
                'product_type' => 'pastry',
                'yield_quantity' => 12,
                'preparation_time' => 90,
                'instructions' => json_encode([
                    'Preheat oven to 175°C',
                    'Mix dry ingredients: flour, cocoa powder, baking soda, salt',
                    'Beat together eggs, sugar, oil, and vanilla',
                    'Add hot water and mix until smooth',
                    'Pour into greased cake pans',
                    'Bake for 30-35 minutes',
                    'Cool completely before frosting',
                    'Prepare chocolate ganache and frost the cake',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 250, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Cocoa Powder - Premium Dark', 'quantity' => 75, 'uom' => 'grams', 'cost_per_unit' => 0.025, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 400, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.67, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                    ['item' => 'Vegetable Oil - Cooking', 'quantity' => 125, 'uom' => 'ml', 'cost_per_unit' => 0.005, 'waste_percentage' => 1],
                    ['item' => 'Vanilla Extract - Pure', 'quantity' => 0.01, 'uom' => 'liters', 'cost_per_unit' => 50, 'waste_percentage' => 0],
                    ['item' => 'Cream - Heavy Whipping', 'quantity' => 200, 'uom' => 'ml', 'cost_per_unit' => 0.01, 'waste_percentage' => 1],
                    ['item' => 'Chocolate Chips - Dark', 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.015, 'waste_percentage' => 1],
                ],
            ],
            'Chocolate Gelato' => [
                'product_type' => 'gelato_flavor',
                'yield_quantity' => 50, // 50 servings of 100g each = 5kg
                'preparation_time' => 60,
                'instructions' => json_encode([
                    'Heat milk and cream to 85°C',
                    'Whisk cocoa powder with some warm milk',
                    'Mix sugar with egg yolks',
                    'Combine hot milk with egg mixture',
                    'Add cocoa mixture and mix well',
                    'Cool to 4°C',
                    'Process in gelato machine for 25 minutes',
                    'Store at -18°C',
                ]),
                'ingredients' => [
                    ['item' => 'Milk - Fresh Whole', 'quantity' => 2, 'uom' => 'liters', 'cost_per_unit' => 3, 'waste_percentage' => 2],
                    ['item' => 'Cream - Heavy Whipping', 'quantity' => 1.5, 'uom' => 'liters', 'cost_per_unit' => 10, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 600, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Cocoa Powder - Premium Dark', 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.025, 'waste_percentage' => 2],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 1, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                ],
            ],
            'Strawberry Gelato' => [
                'product_type' => 'gelato_flavor',
                'yield_quantity' => 50,
                'preparation_time' => 60,
                'instructions' => json_encode([
                    'Blend fresh strawberries to puree',
                    'Heat milk, cream, and sugar to 85°C',
                    'Mix with egg yolks',
                    'Cool completely',
                    'Add strawberry puree',
                    'Process in gelato machine for 25 minutes',
                    'Store at -18°C',
                ]),
                'ingredients' => [
                    ['item' => 'Milk - Fresh Whole', 'quantity' => 1.8, 'uom' => 'liters', 'cost_per_unit' => 3, 'waste_percentage' => 2],
                    ['item' => 'Cream - Heavy Whipping', 'quantity' => 1.2, 'uom' => 'liters', 'cost_per_unit' => 10, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 550, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.8, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                ],
            ],
            'Pistachio Gelato' => [
                'product_type' => 'gelato_flavor',
                'yield_quantity' => 50,
                'preparation_time' => 70,
                'instructions' => json_encode([
                    'Grind pistachios into fine paste',
                    'Heat milk and cream to 85°C',
                    'Mix sugar with egg yolks',
                    'Combine hot milk with egg mixture',
                    'Add pistachio paste and mix well',
                    'Cool to 4°C',
                    'Process in gelato machine for 25 minutes',
                    'Store at -18°C',
                ]),
                'ingredients' => [
                    ['item' => 'Milk - Fresh Whole', 'quantity' => 1.8, 'uom' => 'liters', 'cost_per_unit' => 3, 'waste_percentage' => 2],
                    ['item' => 'Cream - Heavy Whipping', 'quantity' => 1.3, 'uom' => 'liters', 'cost_per_unit' => 10, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 580, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.9, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                ],
            ],
            'Almond Danish' => [
                'product_type' => 'pastry',
                'yield_quantity' => 18,
                'preparation_time' => 180,
                'instructions' => json_encode([
                    'Prepare puff pastry dough',
                    'Roll and fold dough multiple times',
                    'Cut into squares',
                    'Add almond cream filling',
                    'Top with sliced almonds',
                    'Proof for 1 hour',
                    'Brush with egg wash',
                    'Bake at 200°C for 15-18 minutes',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 450, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Butter - Salted', 'quantity' => 280, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 120, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.6, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                    ['item' => 'Milk - Fresh Whole', 'quantity' => 100, 'uom' => 'ml', 'cost_per_unit' => 0.003, 'waste_percentage' => 1],
                    ['item' => 'Vanilla Extract - Pure', 'quantity' => 0.005, 'uom' => 'liters', 'cost_per_unit' => 50, 'waste_percentage' => 0],
                ],
            ],
            'Oatmeal Raisin Cookie' => [
                'product_type' => 'pastry',
                'yield_quantity' => 40,
                'preparation_time' => 40,
                'instructions' => json_encode([
                    'Cream butter and sugars together',
                    'Beat in eggs and vanilla',
                    'Mix in flour, oats, and spices',
                    'Fold in raisins',
                    'Scoop dough onto baking sheets',
                    'Bake at 180°C for 12-14 minutes',
                    'Cool before serving',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 220, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Butter - Salted', 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 180, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.5, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                    ['item' => 'Vanilla Extract - Pure', 'quantity' => 0.008, 'uom' => 'liters', 'cost_per_unit' => 50, 'waste_percentage' => 0],
                ],
            ],
            'Sourdough Loaf' => [
                'product_type' => 'hot_kitchen',
                'yield_quantity' => 4,
                'preparation_time' => 1440, // 24 hours (includes fermentation)
                'instructions' => json_encode([
                    'Feed sourdough starter 12 hours before',
                    'Mix flour, water, salt, and starter',
                    'Autolyse for 30 minutes',
                    'Stretch and fold every 30 minutes (4 times)',
                    'Bulk ferment for 4-6 hours',
                    'Shape into loaves',
                    'Cold ferment overnight (12 hours)',
                    'Score and bake at 230°C for 35-40 minutes',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 1000, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Salt - Table Salt', 'quantity' => 20, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 0],
                    ['item' => 'Yeast - Active Dry', 'quantity' => 10, 'uom' => 'grams', 'cost_per_unit' => 0.02, 'waste_percentage' => 0],
                ],
            ],
            'Banana Bread' => [
                'product_type' => 'hot_kitchen',
                'yield_quantity' => 2,
                'preparation_time' => 90,
                'instructions' => json_encode([
                    'Preheat oven to 175°C',
                    'Mash ripe bananas',
                    'Mix butter, sugar, and eggs',
                    'Add mashed bananas',
                    'Mix in flour, baking soda, and salt',
                    'Pour into greased loaf pans',
                    'Bake for 55-60 minutes',
                    'Cool before slicing',
                ]),
                'ingredients' => [
                    ['item' => 'Flour - All Purpose', 'quantity' => 280, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 5],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Butter - Salted', 'quantity' => 120, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 2],
                    ['item' => 'Eggs - Large Grade A', 'quantity' => 0.5, 'uom' => 'units', 'cost_per_unit' => 15, 'waste_percentage' => 0],
                    ['item' => 'Vegetable Oil - Cooking', 'quantity' => 80, 'uom' => 'ml', 'cost_per_unit' => 0.005, 'waste_percentage' => 1],
                    ['item' => 'Vanilla Extract - Pure', 'quantity' => 0.005, 'uom' => 'liters', 'cost_per_unit' => 50, 'waste_percentage' => 0],
                ],
            ],
            'Dark Chocolate Truffle' => [
                'product_type' => 'hot_kitchen',
                'yield_quantity' => 50,
                'preparation_time' => 120,
                'instructions' => json_encode([
                    'Chop dark chocolate finely',
                    'Heat cream to simmering',
                    'Pour over chocolate and let sit',
                    'Stir until smooth ganache forms',
                    'Cool and refrigerate for 2 hours',
                    'Roll into balls',
                    'Coat with cocoa powder',
                    'Store in refrigerator',
                ]),
                'ingredients' => [
                    ['item' => 'Chocolate Chips - Dark', 'quantity' => 500, 'uom' => 'grams', 'cost_per_unit' => 0.015, 'waste_percentage' => 2],
                    ['item' => 'Cream - Heavy Whipping', 'quantity' => 300, 'uom' => 'ml', 'cost_per_unit' => 0.01, 'waste_percentage' => 1],
                    ['item' => 'Butter - Salted', 'quantity' => 50, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 1],
                    ['item' => 'Cocoa Powder - Premium Dark', 'quantity' => 100, 'uom' => 'grams', 'cost_per_unit' => 0.025, 'waste_percentage' => 2],
                    ['item' => 'Vanilla Extract - Pure', 'quantity' => 0.005, 'uom' => 'liters', 'cost_per_unit' => 50, 'waste_percentage' => 0],
                ],
            ],
            'Salted Caramel Chocolate' => [
                'product_type' => 'hot_kitchen',
                'yield_quantity' => 48,
                'preparation_time' => 150,
                'instructions' => json_encode([
                    'Make caramel with sugar and cream',
                    'Add salt to caramel and cool',
                    'Temper milk chocolate',
                    'Fill molds halfway with chocolate',
                    'Add caramel filling',
                    'Top with more chocolate',
                    'Cool and unmold',
                    'Store in cool place',
                ]),
                'ingredients' => [
                    ['item' => 'Chocolate Chips - Dark', 'quantity' => 600, 'uom' => 'grams', 'cost_per_unit' => 0.015, 'waste_percentage' => 2],
                    ['item' => 'Sugar - White Granulated', 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 3],
                    ['item' => 'Cream - Heavy Whipping', 'quantity' => 200, 'uom' => 'ml', 'cost_per_unit' => 0.01, 'waste_percentage' => 1],
                    ['item' => 'Butter - Salted', 'quantity' => 80, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 1],
                    ['item' => 'Salt - Table Salt', 'quantity' => 5, 'uom' => 'grams', 'cost_per_unit' => 0.001, 'waste_percentage' => 0],
                ],
            ],
        ];

        // Create recipes for products
        foreach ($recipesData as $productName => $recipeData) {
            $product = $products->firstWhere('name', $productName);
            if (! $product) {
                $this->command->warn("Product '{$productName}' not found, skipping...");

                continue;
            }

            // Create the recipe
            $recipe = Recipe::create([
                'branch_id' => $branch->id,
                'department_id' => $productionDept->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku.'-RCP',
                'product_type_id' => $productTypeMap[$recipeData['product_type']] ?? ProductType::first()->id,
                'cost_per_unit' => 0, // Will be calculated
                'uom_id' => $product->uom_id,
                'yield_quantity' => $recipeData['yield_quantity'],
                'preparation_time' => $recipeData['preparation_time'],
                'instructions' => $recipeData['instructions'],
                'status' => 'active',
                'created_by_id' => $employee->id,
                'created_by_type' => Employee::class,
            ]);

            $totalRecipes++;

            // Create recipe ingredients
            $sortOrder = 0;
            foreach ($recipeData['ingredients'] as $ingredientData) {
                $item = $itemMap[$ingredientData['item']] ?? null;
                if (! $item) {
                    $this->command->warn("Item '{$ingredientData['item']}' not found, skipping ingredient...");

                    continue;
                }

                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'item_id' => $item->id,
                    'quantity' => $ingredientData['quantity'],
                    'uom_id' => $uomMap[$ingredientData['uom']] ?? UnitOfMeasure::first()->id,
                    'cost_per_unit' => $ingredientData['cost_per_unit'],
                    'waste_percentage' => $ingredientData['waste_percentage'],
                    'sort_order' => $sortOrder++,
                    'notes' => "Required for {$product->name}",
                    'preparation_notes' => 'Standard preparation',
                ]);

                $totalIngredients++;
            }

            // Update recipe cost
            $recipe->cost_per_unit = $recipe->calculateCostPerUnit();
            $recipe->save();

            $this->command->info("✓ Created recipe for {$product->name} with ".count($recipeData['ingredients']).' ingredients');
        }

        $this->command->info("✅ Total recipes created: {$totalRecipes}");
        $this->command->info("✅ Total recipe ingredients created: {$totalIngredients}");
    }
}
