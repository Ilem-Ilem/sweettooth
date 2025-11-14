<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Shift;
use App\Models\DailyProduce;
use App\Models\ProductionRecord;
use App\Models\ProductionRequest;
use App\Models\RawMaterialUtilization;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Assumed IDs for related tables (replace with actual IDs or seed these tables first)
        $branchIds = \App\Models\Branch::all()->pluck('id')->toArray();
        $departmentIds = \App\Models\Department::all()->pluck('id')->toArray();
        $employeeIds = \App\Models\Employee::all()->pluck('id')->toArray();
        $itemIds = \App\Models\Item::all()->pluck('id')->toArray();
        $itemRequestIds = \App\Models\ItemRequest::all()->pluck('id')->toArray();

        // dd(
        //     [
        //         'branch_id'=>$branchIds,
        //         'departments'=>$departmentIds,
        //         'employees'=>$employeeIds,
        //         'items'=>$itemIds,
        //         'itemRequestIds'=>$itemRequestIds
        //     ]
        // );
        // Ensure related tables have data
        if (empty($branchIds) || empty($departmentIds) || empty($employeeIds) || empty($itemIds)) {
            throw new \Exception('Related tables (branches, departments, employees, categories, items, item_requests) must be seeded first.');
        }

        // 1. Seed Recipes (20 records)
        $recipes = [];
        for ($i = 0; $i < 20; $i++) {
            $recipes[] = Recipe::create([
                'branch_id' => $faker->randomElement($branchIds),
                'department_id' => $faker->randomElement($departmentIds),
                'product_name' => $faker->word . ' ' . $faker->randomElement(['Gelato', 'Pastry', 'Beverage']),
                'sku' => 'SKU-' . Str::random(8),
                // 'category_id' => $faker->randomElement($categoryIds),
                'product_type' => $faker->randomElement(['gelato_base', 'gelato_flavor', 'pastry', 'hot_kitchen', 'beverage']),
                'cost_per_unit' => $faker->randomFloat(4, 0.5, 50),
                'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
                'yield_quantity' => $faker->randomFloat(2, 1, 100),
                'preparation_time' => $faker->numberBetween(5, 120),
                'instructions' => $faker->paragraph,
                'status' => $faker->randomElement(['active', 'inactive', 'testing']),
                'created_by' => $faker->randomElement($employeeIds),
            ]);
        }

        // 2. Seed Recipe Ingredients (20 records)
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < 20; $i++) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'item_id' => $faker->randomElement($itemIds),
                    'quantity' => $faker->randomFloat(4, 0.1, 10),
                    'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
                    'sort_order' => $i,
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }

        // 3. Seed Shifts (20 records)
        $shifts = [];
        for ($i = 0; $i < 20; $i++) {
            $shifts[] = Shift::create([
                'branch_id' => $faker->randomElement($branchIds),
                'department_id' => $faker->randomElement($departmentIds),
                'employee_id' => $faker->randomElement($employeeIds),
                'shift_number' => 'SHIFT-' . Str::random(6),
                'shift_date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'shift_type' => $faker->randomElement(['morning', 'afternoon', 'night']),
                'clock_in' => $faker->dateTimeBetween('-1 day', 'now'),
                'clock_out' => $faker->optional()->dateTimeBetween('now', '+1 day'),
                'status' => $faker->randomElement(['active', 'closed', 'submitted']),
                'notes' => $faker->optional()->sentence,
            ]);
        }

        // 4. Seed Daily Produces (20 records)
        $dailyProduces = [];
        foreach ($shifts as $shift) {
            for ($i = 0; $i < 20; $i++) {
                $recipe = $faker->randomElement($recipes);
                $dailyProduces[] = DailyProduce::create([
                    'shift_id' => $shift->id,
                    'recipe_id' => $recipe->id,
                    'produce_date' => $shift->shift_date,
                    'shift_type' => $faker->randomElement(['morning', 'afternoon']),
                    'opening_quantity' => $faker->randomFloat(2, 0, 100),
                    'requested_quantity' => $faker->randomFloat(2, 0, 50),
                    'produced_quantity' => $faker->randomFloat(2, 0, 50),
                    'sent_out_quantity' => $faker->randomFloat(2, 0, 40),
                    'order_quantity' => $faker->randomFloat(2, 0, 30),
                    'callback_quantity' => $faker->randomFloat(2, 0, 10),
                    'closing_quantity' => $faker->randomFloat(2, 0, 50),
                    'expected_closing' => $faker->randomFloat(2, 0, 50),
                    'variance' => $faker->randomFloat(2, -10, 10),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }

        // 5. Seed Production Records (20 records)
        foreach ($dailyProduces as $dailyProduce) {
            for ($i = 0; $i < 20; $i++) {
                ProductionRecord::create([
                    'daily_produce_id' => $dailyProduce->id,
                    'recipe_id' => $dailyProduce->recipe_id,
                    'produced_by' => $faker->randomElement($employeeIds),
                    'quantity_produced' => $faker->randomFloat(2, 1, 50),
                    'quantity_approved' => $faker->randomFloat(2, 0, 50),
                    'quantity_rejected' => $faker->randomFloat(2, 0, 10),
                    'production_time' => $faker->dateTimeBetween('-1 day', 'now'),
                    'quality_status' => $faker->randomElement(['excellent', 'good', 'acceptable', 'rejected']),
                    'rejection_reason' => $faker->optional()->sentence,
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }

        // 6. Seed Production Requests (20 records)
        foreach ($shifts as $shift) {
            for ($i = 0; $i < 20; $i++) {
                ProductionRequest::create([
                    'shift_id' => $shift->id,
                    'item_request_id' => $faker->randomElement($itemRequestIds),
                    'recipe_id' => $faker->optional()->randomElement($recipes)->id ?? null,
                    'planned_production_quantity' => $faker->optional()->randomFloat(2, 1, 100),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }

        // 7. Seed Call Backs - DISABLED (obsolete CallBack model replaced by ProductionCallback and ProductDispatchCallback)
        // foreach ($shifts as $shift) {
        //     for ($i = 0; $i < 20; $i++) {
        //         $callbackType = $faker->randomElement(['inventory_item', 'produced_item']);
        //         CallBack::create([
        //             'shift_id' => $shift->id,
        //             'callback_type' => $callbackType,
        //             'reference_id' => $callbackType === 'inventory_item'
        //                 ? $faker->randomElement($itemIds)
        //                 : $faker->randomElement($recipes)->id,
        //             'quantity' => $faker->randomFloat(2, 0, 20),
        //             'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
        //             'reason' => $faker->randomElement(['expired', 'damaged', 'quality_issue', 'contaminated', 'other']),
        //             'description' => $faker->optional()->sentence,
        //             'reported_by' => $faker->randomElement($employeeIds),
        //             'callback_time' => $faker->dateTimeBetween('-1 day', 'now'),
        //             'action_taken' => $faker->randomElement(['disposed', 'returned_to_supplier', 'reprocessed', 'pending']),
        //         ]);
        //     }
        // }

        // 8. Seed Raw Material Utilizations (20 records)
        foreach ($shifts as $shift) {
            for ($i = 0; $i < 20; $i++) {
                $recipe = $faker->randomElement($recipes);
                $quantityRequired = $faker->randomFloat(4, 0.1, 10);
                $quantityUsed = $quantityRequired + $faker->randomFloat(4, -0.5, 0.5);
                RawMaterialUtilization::create([
                    'shift_id' => $shift->id,
                    'recipe_id' => $recipe->id,
                    'item_id' => $faker->randomElement($itemIds),
                    'quantity_required' => $quantityRequired,
                    'quantity_used' => $quantityUsed,
                    'units_produced' => $faker->randomFloat(2, 1, 50),
                    'variance' => $quantityUsed - $quantityRequired,
                    'variance_type' => $faker->randomElement(['within_tolerance', 'over_used', 'under_used']),
                    'cost_impact' => $faker->randomFloat(2, 0, 100),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }
}
