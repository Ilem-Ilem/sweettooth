<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first branch, department, and employee
        $branch = Branch::first();
        $department = Department::whereHas('category', function ($q) {
            $q->where('name', 'Production');
        })->first();

        if (!$department) {
            $department = Department::first();
        }

        $employee = Employee::first();

        // Get items for ingredients
        $items = Item::take(10)->get();

        if ($items->count() < 3) {
            $this->command->error('Not enough items in database. Please seed items first.');
            return;
        }

        // Create sample recipes
        $recipes = [
            [
                'product_name' => 'Classic Vanilla Gelato',
                'product_type' => 'gelato_flavor',
                'uom' => 'kg',
                'yield_quantity' => 2,
                'preparation_time' => 45,
                'instructions' => [
                    'Heat milk and cream to 85°C',
                    'Mix sugar and egg yolks until smooth',
                    'Combine hot milk mixture with egg mixture',
                    'Add vanilla extract and mix well',
                    'Cool the mixture to 4°C',
                    'Process in gelato machine for 20-25 minutes',
                    'Store at -18°C'
                ],
                'ingredients' => [
                    ['item_index' => 0, 'quantity' => 500, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 2, 'notes' => 'Use whole milk', 'preparation_notes' => 'Heat gently'],
                    ['item_index' => 1, 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 0, 'notes' => 'Heavy cream', 'preparation_notes' => 'Mix with milk'],
                    ['item_index' => 2, 'quantity' => 150, 'uom' => 'grams', 'cost_per_unit' => 0.003, 'waste_percentage' => 5, 'notes' => 'White granulated', 'preparation_notes' => 'Dissolve completely'],
                ]
            ],
            [
                'product_name' => 'Chocolate Fudge Brownie',
                'product_type' => 'pastry',
                'uom' => 'pcs',
                'yield_quantity' => 24,
                'preparation_time' => 60,
                'instructions' => [
                    'Preheat oven to 180°C',
                    'Melt butter and chocolate together',
                    'Beat eggs and sugar until fluffy',
                    'Combine chocolate mixture with egg mixture',
                    'Add flour and cocoa powder',
                    'Pour into greased pan',
                    'Bake for 25-30 minutes',
                    'Cool completely before cutting'
                ],
                'ingredients' => [
                    ['item_index' => 3, 'quantity' => 250, 'uom' => 'grams', 'cost_per_unit' => 0.012, 'waste_percentage' => 3, 'notes' => 'Dark chocolate', 'preparation_notes' => 'Melt slowly'],
                    ['item_index' => 4, 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.006, 'waste_percentage' => 0, 'notes' => 'Unsalted butter', 'preparation_notes' => 'Room temperature'],
                    ['item_index' => 0, 'quantity' => 300, 'uom' => 'grams', 'cost_per_unit' => 0.003, 'waste_percentage' => 5, 'notes' => 'Caster sugar', 'preparation_notes' => 'Sift before use'],
                    ['item_index' => 1, 'quantity' => 150, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 10, 'notes' => 'All purpose flour', 'preparation_notes' => 'Sift twice'],
                ]
            ],
            [
                'product_name' => 'Strawberry Sorbet',
                'product_type' => 'gelato_flavor',
                'uom' => 'liters',
                'yield_quantity' => 1.5,
                'preparation_time' => 30,
                'instructions' => [
                    'Wash and hull fresh strawberries',
                    'Blend strawberries until smooth',
                    'Heat water and sugar to make syrup',
                    'Cool syrup completely',
                    'Mix strawberry puree with syrup and lemon juice',
                    'Chill mixture for 2 hours',
                    'Process in gelato machine for 15-20 minutes'
                ],
                'ingredients' => [
                    ['item_index' => 5, 'quantity' => 800, 'uom' => 'grams', 'cost_per_unit' => 0.008, 'waste_percentage' => 15, 'notes' => 'Fresh strawberries', 'preparation_notes' => 'Remove stems and wash'],
                    ['item_index' => 2, 'quantity' => 200, 'uom' => 'grams', 'cost_per_unit' => 0.003, 'waste_percentage' => 5, 'notes' => 'Fine sugar', 'preparation_notes' => 'Dissolve in water'],
                    ['item_index' => 6, 'quantity' => 300, 'uom' => 'ml', 'cost_per_unit' => 0.001, 'waste_percentage' => 0, 'notes' => 'Filtered water', 'preparation_notes' => 'Use for syrup'],
                ]
            ],
            [
                'product_name' => 'Tiramisu Gelato',
                'product_type' => 'gelato_flavor',
                'uom' => 'kg',
                'yield_quantity' => 2.5,
                'preparation_time' => 90,
                'instructions' => [
                    'Brew strong espresso and cool',
                    'Mix mascarpone cheese until smooth',
                    'Prepare gelato base with milk and cream',
                    'Add coffee extract and cocoa powder',
                    'Fold in mascarpone mixture',
                    'Chill for 4 hours',
                    'Process in gelato machine',
                    'Dust with cocoa powder before serving'
                ],
                'ingredients' => [
                    ['item_index' => 0, 'quantity' => 600, 'uom' => 'grams', 'cost_per_unit' => 0.002, 'waste_percentage' => 2, 'notes' => 'Whole milk', 'preparation_notes' => 'Heat to 85°C'],
                    ['item_index' => 7, 'quantity' => 250, 'uom' => 'grams', 'cost_per_unit' => 0.015, 'waste_percentage' => 5, 'notes' => 'Mascarpone cheese', 'preparation_notes' => 'Room temperature'],
                    ['item_index' => 8, 'quantity' => 100, 'uom' => 'ml', 'cost_per_unit' => 0.02, 'waste_percentage' => 0, 'notes' => 'Strong espresso', 'preparation_notes' => 'Cool before adding'],
                    ['item_index' => 2, 'quantity' => 180, 'uom' => 'grams', 'cost_per_unit' => 0.003, 'waste_percentage' => 5, 'notes' => 'Sugar', 'preparation_notes' => 'Dissolve completely'],
                ]
            ],
            [
                'product_name' => 'Mango Smoothie Bowl',
                'product_type' => 'beverage',
                'uom' => 'pcs',
                'yield_quantity' => 10,
                'preparation_time' => 20,
                'instructions' => [
                    'Peel and dice fresh mangoes',
                    'Freeze mango pieces for 2 hours',
                    'Blend frozen mango with yogurt',
                    'Add honey and blend until smooth',
                    'Pour into bowls',
                    'Top with granola and fresh fruit'
                ],
                'ingredients' => [
                    ['item_index' => 9, 'quantity' => 1200, 'uom' => 'grams', 'cost_per_unit' => 0.005, 'waste_percentage' => 20, 'notes' => 'Ripe mangoes', 'preparation_notes' => 'Peel and remove pit'],
                    ['item_index' => 0, 'quantity' => 500, 'uom' => 'grams', 'cost_per_unit' => 0.004, 'waste_percentage' => 0, 'notes' => 'Greek yogurt', 'preparation_notes' => 'Use cold'],
                    ['item_index' => 1, 'quantity' => 100, 'uom' => 'grams', 'cost_per_unit' => 0.01, 'waste_percentage' => 0, 'notes' => 'Pure honey', 'preparation_notes' => 'Add for sweetness'],
                ]
            ]
        ];

        DB::transaction(function () use ($branch, $department, $employee, $items, $recipes) {
            foreach ($recipes as $index => $recipeData) {
                // Calculate total cost from ingredients
                $totalCost = 0;
                foreach ($recipeData['ingredients'] as $ing) {
                    $itemIndex = min($ing['item_index'], $items->count() - 1);
                    $quantity = (float) $ing['quantity'];
                    $costPerUnit = (float) $ing['cost_per_unit'];
                    $wastePercent = (float) $ing['waste_percentage'];

                    $actualQuantity = $quantity * (1 + ($wastePercent / 100));
                    $totalCost += $actualQuantity * $costPerUnit;
                }

                $costPerUnit = $totalCost / max((float) $recipeData['yield_quantity'], 1);

                // Generate SKU
                $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $recipeData['product_name']), 0, 4));
                $randomCode = str_pad($index + 100, 3, '0', STR_PAD_LEFT);
                $sku = 'RCP-' . $branch->id . '-' . $nameCode . '-' . $randomCode;

                // Create recipe
                $recipe = Recipe::create([
                    'branch_id' => $branch->id,
                    'department_id' => $department->id,
                    'product_name' => $recipeData['product_name'],
                    'sku' => $sku,
                    'product_type' => $recipeData['product_type'],
                    'cost_per_unit' => $costPerUnit,
                    'uom' => $recipeData['uom'],
                    'yield_quantity' => $recipeData['yield_quantity'],
                    'preparation_time' => $recipeData['preparation_time'],
                    'instructions' => json_encode($recipeData['instructions']),
                    'status' => 'active',
                    'created_by' => $employee->id,
                ]);

                // Create recipe ingredients
                foreach ($recipeData['ingredients'] as $sortOrder => $ingredientData) {
                    // Make sure we don't exceed the items array
                    $itemIndex = min($ingredientData['item_index'], $items->count() - 1);
                    $item = $items[$itemIndex];

                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'item_id' => $item->id, // IMPORTANT: Always use a valid item ID
                        'quantity' => $ingredientData['quantity'],
                        'uom' => $ingredientData['uom'],
                        'cost_per_unit' => $ingredientData['cost_per_unit'],
                        'waste_percentage' => $ingredientData['waste_percentage'],
                        'sort_order' => $sortOrder + 1,
                        'notes' => $ingredientData['notes'] ?? null,
                        'preparation_notes' => $ingredientData['preparation_notes'] ?? null,
                    ]);
                }

                $this->command->info("Created recipe: {$recipeData['product_name']} (SKU: {$sku})");
            }
        });

        $this->command->info('Recipe seeding completed successfully!');
    }
}
