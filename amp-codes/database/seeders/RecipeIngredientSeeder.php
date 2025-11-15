<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Database\Seeder;

class RecipeIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recipes = Recipe::all();

        foreach ($recipes as $recipe) {
            // Get 3-5 random items for each recipe
            $items = Item::where('branch_id', $recipe->branch_id)
                ->where('category', 'raw_material')
                ->inRandomOrder()
                ->limit(rand(3, 5))
                ->get();

            foreach ($items as $index => $item) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'item_id' => $item->id,
                    'quantity' => rand(10, 500) / 10,
                    'uom' => ['grams', 'kg', 'liters', 'ml'][array_rand(['grams', 'kg', 'liters', 'ml'])],
                    'sort_order' => $index + 1,
                    'notes' => 'Essential ingredient for ' . $recipe->product_name,
                ]);
            }
        }
    }
}
