<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentCategory;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $downBranch = Branch::where('code', 'DB002')->first();
        $airportBranch = Branch::where('code', 'AB003')->first();

        $productionCat = DepartmentCategory::where('name', 'Production')->first();
        $salesCat = DepartmentCategory::where('name', 'Sales & Service')->first();
        $inventoryCat = DepartmentCategory::where('name', 'Inventory')->first();

        // Production departments
        Department::create([
            'branch_id' => $mainBranch->id,
            'category_id' => $productionCat->id,
            'name' => 'Gelato Production',
            'description' => 'Gelato manufacturing department',
        ]);

        Department::create([
            'branch_id' => $mainBranch->id,
            'category_id' => $productionCat->id,
            'name' => 'Pastry Production',
            'description' => 'Pastry and baked goods production',
        ]);

        Department::create([
            'branch_id' => $mainBranch->id,
            'category_id' => $productionCat->id,
            'name' => 'Hot Kitchen',
            'description' => 'Hot food preparation',
        ]);

        // Sales departments
        Department::create([
            'branch_id' => $mainBranch->id,
            'category_id' => $salesCat->id,
            'name' => 'Sales Counter',
            'description' => 'Main sales counter',
        ]);

        Department::create([
            'branch_id' => $mainBranch->id,
            'category_id' => $salesCat->id,
            'name' => 'Delivery Service',
            'description' => 'Delivery and logistics',
        ]);

        // Inventory department
        Department::create([
            'branch_id' => $mainBranch->id,
            'category_id' => $inventoryCat->id,
            'name' => 'Warehouse',
            'description' => 'Inventory and warehouse management',
        ]);

        // Downtown branch departments
        Department::create([
            'branch_id' => $downBranch->id,
            'category_id' => $productionCat->id,
            'name' => 'Gelato Production',
            'description' => 'Gelato manufacturing department',
        ]);

        Department::create([
            'branch_id' => $downBranch->id,
            'category_id' => $salesCat->id,
            'name' => 'Sales Counter',
            'description' => 'Sales counter for downtown branch',
        ]);

        // Airport branch departments
        Department::create([
            'branch_id' => $airportBranch->id,
            'category_id' => $salesCat->id,
            'name' => 'Sales Counter',
            'description' => 'Airport location sales counter',
        ]);

        Department::create([
            'branch_id' => $airportBranch->id,
            'category_id' => $inventoryCat->id,
            'name' => 'Mini Warehouse',
            'description' => 'Small warehouse for airport branch',
        ]);
    }
}
