<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SnackRecipeSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = DB::table('branches')->value('id'); // Use first branch
        $departmentId = DB::table('departments')->value('id');
        $employeeId = DB::table('employees')->value('id');

        $recipes = [
            [
                'product_name' => 'Chicken Pie',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Butter', 250, 'grams'], ['Chicken (shredded)', 300, 'grams'],
                    ['Onion', 1, 'pcs'], ['Carrot', 1, 'pcs'], ['Seasoning cube', 1, 'pcs'],
                    ['Salt', 1, 'tsp'], ['Water', 100, 'ml'],
                ],
            ],
            [
                'product_name' => 'Beef Roll',
                'ingredients' => [
                    ['Flour', 400, 'grams'], ['Butter', 200, 'grams'], ['Ground beef', 250, 'grams'],
                    ['Onion', 1, 'pcs'], ['Salt', 0.5, 'tsp'], ['Black pepper', 0.25, 'tsp'], ['Egg', 1, 'pcs'],
                ],
            ],
            [
                'product_name' => 'Sausage Roll',
                'ingredients' => [
                    ['Flour', 450, 'grams'], ['Butter', 220, 'grams'], ['Sausage meat', 250, 'grams'],
                    ['Salt', 0.5, 'tsp'], ['Egg (for brushing)', 1, 'pcs'], ['Water', 80, 'ml'],
                ],
            ],
            [
                'product_name' => 'Puff Puff',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Sugar', 100, 'grams'], ['Yeast', 10, 'grams'],
                    ['Water', 350, 'ml'], ['Salt', 0.5, 'tsp'], ['Oil (for frying)', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Doughnut',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Yeast', 7, 'grams'], ['Sugar', 80, 'grams'],
                    ['Butter', 60, 'grams'], ['Milk', 150, 'ml'], ['Egg', 1, 'pcs'], ['Oil', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Meat Pie',
                'ingredients' => [
                    ['Flour', 600, 'grams'], ['Butter', 300, 'grams'], ['Minced beef', 300, 'grams'],
                    ['Potato', 1, 'pcs'], ['Carrot', 1, 'pcs'], ['Onion', 1, 'pcs'],
                    ['Salt', 1, 'tsp'], ['Water', 100, 'ml'],
                ],
            ],
            [
                'product_name' => 'Fish Pie',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Butter', 250, 'grams'], ['Canned tuna', 200, 'grams'],
                    ['Onion', 1, 'pcs'], ['Pepper', 0.5, 'tsp'], ['Salt', 0.5, 'tsp'], ['Egg', 1, 'pcs'],
                ],
            ],
            [
                'product_name' => 'Egg Roll',
                'ingredients' => [
                    ['Flour', 400, 'grams'], ['Sugar', 50, 'grams'], ['Baking powder', 1, 'tsp'],
                    ['Egg (boiled)', 4, 'pcs'], ['Water', 100, 'ml'], ['Oil', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Scotch Egg',
                'ingredients' => [
                    ['Egg (boiled)', 4, 'pcs'], ['Sausage meat', 300, 'grams'],
                    ['Flour', 50, 'grams'], ['Bread crumbs', 100, 'grams'], ['Oil', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Chin Chin',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Sugar', 100, 'grams'], ['Butter', 100, 'grams'],
                    ['Milk', 100, 'ml'], ['Egg', 1, 'pcs'], ['Baking powder', 1, 'tsp'], ['Oil', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Plantain Chips',
                'ingredients' => [
                    ['Plantain', 5, 'pcs'], ['Salt', 1, 'tsp'], ['Oil', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Popcorn',
                'ingredients' => [
                    ['Corn kernels', 200, 'grams'], ['Oil', 2, 'tbsp'], ['Sugar', 50, 'grams'], ['Salt', 0.5, 'tsp'],
                ],
            ],
            [
                'product_name' => 'Peanut Burger',
                'ingredients' => [
                    ['Groundnuts', 300, 'grams'], ['Flour', 200, 'grams'], ['Sugar', 50, 'grams'],
                    ['Egg', 1, 'pcs'], ['Milk powder', 2, 'tbsp'], ['Oil', 1000, 'ml'],
                ],
            ],
            [
                'product_name' => 'Cake Slice',
                'ingredients' => [
                    ['Flour', 250, 'grams'], ['Sugar', 150, 'grams'], ['Butter', 150, 'grams'],
                    ['Egg', 3, 'pcs'], ['Baking powder', 1, 'tsp'], ['Milk', 100, 'ml'], ['Vanilla essence', 1, 'tsp'],
                ],
            ],
            [
                'product_name' => 'Cookies',
                'ingredients' => [
                    ['Flour', 300, 'grams'], ['Sugar', 120, 'grams'], ['Butter', 200, 'grams'],
                    ['Egg', 1, 'pcs'], ['Chocolate chips', 100, 'grams'], ['Baking soda', 0.5, 'tsp'],
                ],
            ],
            [
                'product_name' => 'Cupcake',
                'ingredients' => [
                    ['Flour', 250, 'grams'], ['Butter', 125, 'grams'], ['Sugar', 120, 'grams'],
                    ['Egg', 2, 'pcs'], ['Baking powder', 1, 'tsp'], ['Milk', 80, 'ml'],
                ],
            ],
            [
                'product_name' => 'Sandwich',
                'ingredients' => [
                    ['Bread slices', 4, 'pcs'], ['Lettuce', 2, 'pcs'], ['Tomato', 2, 'slices'],
                    ['Cucumber', 2, 'slices'], ['Egg', 1, 'pcs'], ['Mayonnaise', 1, 'tbsp'],
                ],
            ],
            [
                'product_name' => 'Burger Bun',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Yeast', 7, 'grams'], ['Sugar', 60, 'grams'],
                    ['Butter', 50, 'grams'], ['Milk', 100, 'ml'], ['Egg', 1, 'pcs'], ['Water', 150, 'ml'],
                ],
            ],
            [
                'product_name' => 'Hotdog Bun',
                'ingredients' => [
                    ['Flour', 500, 'grams'], ['Yeast', 7, 'grams'], ['Sugar', 60, 'grams'],
                    ['Butter', 50, 'grams'], ['Milk', 100, 'ml'], ['Egg', 1, 'pcs'], ['Water', 150, 'ml'],
                ],
            ],
            [
                'product_name' => 'Pizza Slice',
                'ingredients' => [
                    ['Flour', 400, 'grams'], ['Yeast', 7, 'grams'], ['Tomato paste', 100, 'grams'],
                    ['Mozzarella cheese', 150, 'grams'], ['Sausage', 100, 'grams'],
                    ['Onion', 1, 'pcs'], ['Pepper', 0.5, 'tsp'], ['Oil', 2, 'tbsp'],
                ],
            ],
        ];

        foreach ($recipes as $r) {
            $recipeId = DB::table('recipes')->insertGetId([
                'branch_id' => $branchId,
                'department_id' => $departmentId,
                'product_name' => $r['product_name'],
                'sku' => strtoupper(Str::slug($r['product_name'])) . '-' . Str::random(4),
                'product_type' => 'pastry',
                'cost_per_unit' => rand(50, 500) / 10,
                'uom' => 'pcs',
                'yield_quantity' => rand(5, 20),
                'preparation_time' => rand(15, 90),
                'instructions' => "Prepare {$r['product_name']} according to standard recipe steps.",
                'status' => 'active',
                'created_by' => $employeeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $i = 1;
            $item = \App\Models\Item::get()->toArray();
            foreach ($r['ingredients'] as $key => $ing) {
                DB::table('recipe_ingredients')->insert([
                    'recipe_id' => $recipeId,
                    'item_id' => $item[$key]['id'], // optional if you don't have matching items
                    'quantity' => $ing[1],
                    'uom' => 'grams',
                    'sort_order' => $i++,
                    'notes' => $ing[0],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
