<?php

namespace Database\Seeders;

use App\Models\DailyProduce;
use App\Models\Employee;
use App\Models\ProductionRecord;
use App\Models\ProductionRequest;
use App\Models\ProductType;
use App\Models\RawMaterialUtilization;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Shift;
use App\Models\UnitOfMeasure;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
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

        // Ensure related tables have data
        if (empty($branchIds) || empty($departmentIds) || empty($employeeIds) || empty($itemIds)) {
            throw new \Exception('Related tables (branches, departments, employees, items) must be seeded first.');
        }

        // Build ProductType lookup
        $productTypeIds = ProductType::all()->pluck('id')->toArray();

        // Build UOM lookup
        $uomMap = [
            'grams' => UnitOfMeasure::where('code', 'g')->first()?->id,
            'kg' => UnitOfMeasure::where('code', 'kg')->first()?->id,
            'liters' => UnitOfMeasure::where('code', 'l')->first()?->id,
            'ml' => UnitOfMeasure::where('code', 'ml')->first()?->id,
            'pcs' => UnitOfMeasure::where('code', 'pcs')->first()?->id,
            'units' => UnitOfMeasure::where('code', 'unit')->first()?->id,
        ];

        // 1. Seed Recipes (20 records)
        $recipes = [];
        for ($i = 0; $i < 20; $i++) {
            $uomKey = $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']);
            $recipes[] = Recipe::create([
                'branch_id' => $faker->randomElement($branchIds),
                'department_id' => $faker->randomElement($departmentIds),
                'product_name' => $faker->word.' '.$faker->randomElement(['Gelato', 'Pastry', 'Beverage']),
                'sku' => 'SKU-'.Str::random(8),
                // 'category_id' => $faker->randomElement($categoryIds),
                'product_type_id' => $faker->randomElement($productTypeIds) ?? ProductType::first()->id,
                'cost_per_unit' => $faker->randomFloat(4, 0.5, 50),
                'uom_id' => $uomMap[$uomKey] ?? UnitOfMeasure::first()->id,
                'yield_quantity' => $faker->randomFloat(2, 1, 100),
                'preparation_time' => $faker->numberBetween(5, 120),
                'instructions' => $faker->paragraph,
                'status' => $faker->randomElement(['active', 'inactive', 'testing']),
                'created_by_id' => $faker->randomElement($employeeIds),
                'created_by_type' => Employee::class,
            ]);
        }

        // 2. Seed Recipe Ingredients (20 records)
        foreach ($recipes as $recipe) {
            for ($i = 0; $i < 20; $i++) {
                $uomKey = $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']);
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'item_id' => $faker->randomElement($itemIds),
                    'quantity' => $faker->randomFloat(4, 0.1, 10),
                    'uom_id' => $uomMap[$uomKey] ?? UnitOfMeasure::first()->id,
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
                'shift_number' => 'SHIFT-'.Str::random(6),
                'shift_date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'shift_type' => $faker->randomElement(['morning', 'afternoon', 'night']),
                'clock_in' => $faker->dateTimeBetween('-1 day', 'now'),
                'clock_out' => $faker->optional()->dateTimeBetween('now', '+1 day'),
                'status' => $faker->randomElement(['active', 'closed', 'submitted']),
                'notes' => $faker->optional()->sentence,
            ]);
        }

        // 4. Seed Daily Produces (limit to avoid unique constraint violation on shift_id + recipe_id)
        $dailyProduces = [];
        foreach ($shifts as $shift) {
            // Use only 3-5 recipes per shift to avoid unique constraint violation
            $recipesForShift = $faker->randomElements($recipes, min(5, count($recipes)));
            foreach ($recipesForShift as $recipe) {
                // Skip if this combination already exists
                if (DailyProduce::where('shift_id', $shift->id)->where('recipe_id', $recipe->id)->exists()) {
                    continue;
                }
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
                    'produced_by_id' => $faker->randomElement($employeeIds),
                    'produced_by_type' => Employee::class,
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
                    'item_request_id' => !empty($itemRequestIds) ? $faker->randomElement($itemRequestIds) : null,
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
