<?php

namespace Database\Seeders;

use App\Models\DepartmentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DepartmentCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Production',
            'description' => 'All production related departments',
        ]);

        DepartmentCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Sales & Service',
            'description' => 'Sales counter and customer service departments',
        ]);

        DepartmentCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Inventory',
            'description' => 'Inventory and warehouse management',
        ]);

        DepartmentCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Administration',
            'description' => 'Administrative and management departments',
        ]);

        DepartmentCategory::create([
            'id' => (string) Str::uuid(),
            'name' => 'Finance',
            'description' => 'Finance and accounting departments',
        ]);
    }
}
