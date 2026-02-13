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
        $this->call([
            // Core authorization
            PermissionSeeder::class,
            RoleSeeder::class,

            // Shared setup
            UnitOfMeasureSeeder::class,
            UomConversionSeeder::class,

            // Port Harcourt-only real data from Excel files
            PortHarcourtRealDataSeeder::class,

            // UI pages for seeded departments
            DepartmentPageSeeder::class,
            SalesPagesSeeder::class,
        ]);
    }
}
