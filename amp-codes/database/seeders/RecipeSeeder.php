<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $gelatoDept = Department::where('name', 'Gelato Production')
            ->where('branch_id', $mainBranch->id)
            ->first();
        
        $creator = Employee::where('employee_number', 'EMP002')->first();

        $recipes = [
            [
                'product_name' => 'Vanilla Gelato',
                'sku' => 'REC-VAN-001',
                'product_type' => 'gelato_base',
                'cost_per_unit' => 2.50,
                'yield_quantity' => 10,
                'preparation_time' => 45,
                'instructions' => 'Heat milk and cream, add vanilla extract, churn in gelato machine for 30 minutes',
            ],
            [
                'product_name' => 'Chocolate Gelato',
                'sku' => 'REC-CHO-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 3.00,
                'yield_quantity' => 10,
                'preparation_time' => 50,
                'instructions' => 'Dissolve cocoa powder in hot milk, add sugar and cream, churn for 30 minutes',
            ],
            [
                'product_name' => 'Strawberry Gelato',
                'sku' => 'REC-STR-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 2.80,
                'yield_quantity' => 10,
                'preparation_time' => 40,
                'instructions' => 'Mix strawberry puree with base, add sugar, churn for 25 minutes',
            ],
            [
                'product_name' => 'Pistachio Gelato',
                'sku' => 'REC-PIS-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 3.50,
                'yield_quantity' => 8,
                'preparation_time' => 55,
                'instructions' => 'Toast pistachio, make paste, mix with cream base, churn slowly for 40 minutes',
            ],
            [
                'product_name' => 'Coffee Gelato',
                'sku' => 'REC-COF-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 2.70,
                'yield_quantity' => 10,
                'preparation_time' => 42,
                'instructions' => 'Brew strong espresso, add to cream base with sugar, churn for 30 minutes',
            ],
            [
                'product_name' => 'Hazelnut Gelato',
                'sku' => 'REC-HAZ-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 3.20,
                'yield_quantity' => 9,
                'preparation_time' => 48,
                'instructions' => 'Roast hazelnuts, make paste, fold into base cream, churn for 35 minutes',
            ],
            [
                'product_name' => 'Mint Chocolate Gelato',
                'sku' => 'REC-MNT-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 3.10,
                'yield_quantity' => 10,
                'preparation_time' => 45,
                'instructions' => 'Infuse cream with mint, add cocoa, combine with base, churn for 30 minutes',
            ],
            [
                'product_name' => 'Lemon Gelato',
                'sku' => 'REC-LEM-001',
                'product_type' => 'gelato_flavor',
                'cost_per_unit' => 2.40,
                'yield_quantity' => 10,
                'preparation_time' => 38,
                'instructions' => 'Zest and juice lemons, mix with sugar and cream base, churn for 25 minutes',
            ],
        ];

        foreach ($recipes as $recipe) {
            Recipe::create(array_merge($recipe, [
                'branch_id' => $mainBranch->id,
                'department_id' => $gelatoDept->id,
                'uom' => 'liters',
                'status' => 'active',
                'created_by' => $creator->id,
            ]));
        }
    }
}
