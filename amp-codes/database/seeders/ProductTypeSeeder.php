<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gelatoDept = Department::where('name', 'Gelato Production')->first();
        $pastryDept = Department::where('name', 'Pastry Production')->first();
        $kitchenDept = Department::where('name', 'Hot Kitchen')->first();

        // Gelato product types
        ProductType::create([
            'department_id' => $gelatoDept->id,
            'name' => 'Gelato Base',
            'code' => 'GB',
            'description' => 'Basic gelato bases',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        ProductType::create([
            'department_id' => $gelatoDept->id,
            'name' => 'Gelato Flavor',
            'code' => 'GF',
            'description' => 'Flavored gelato products',
            'status' => 'active',
            'sort_order' => 2,
        ]);

        // Pastry product types
        ProductType::create([
            'department_id' => $pastryDept->id,
            'name' => 'Pastry',
            'code' => 'PT',
            'description' => 'Pastry and baked goods',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        ProductType::create([
            'department_id' => $pastryDept->id,
            'name' => 'Cake',
            'code' => 'CK',
            'description' => 'Cakes and layered desserts',
            'status' => 'active',
            'sort_order' => 2,
        ]);

        // Hot kitchen product types
        ProductType::create([
            'department_id' => $kitchenDept->id,
            'name' => 'Beverage',
            'code' => 'BEV',
            'description' => 'Hot and cold beverages',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        ProductType::create([
            'department_id' => $kitchenDept->id,
            'name' => 'Hot Food',
            'code' => 'HF',
            'description' => 'Hot prepared foods',
            'status' => 'active',
            'sort_order' => 2,
        ]);
    }
}
