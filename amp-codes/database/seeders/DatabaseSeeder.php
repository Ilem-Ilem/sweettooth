<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in order of dependencies
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,
            DepartmentCategorySeeder::class,
            DepartmentSeeder::class,
            EmployeeSeeder::class,
            PermissionSeeder::class,
            ItemSeeder::class,
            ProductTypeSeeder::class,
            ProductSeeder::class,
            RecipeSeeder::class,
            RecipeIngredientSeeder::class,
            ShiftSeeder::class,
            LeaveTypeSeeder::class,
            GlobalBusinessConfigurationSeeder::class,
            SalesShiftSeeder::class,
            SalesSeeder::class,
        ]);
    }
}
