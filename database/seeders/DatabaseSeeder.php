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
            AssignPermissionsToRolesSeeder::class,

            // Shared setup
            UomSetupSeeder::class,

            // Port Harcourt-only real data from Excel files
            PortHarcourtRealDataSeeder::class,

            // UI pages for seeded departments
            DepartmentPageSeeder::class,
            SalesPagesSeeder::class,

            // Shift configurations
            ShiftConfigurationSeeder::class,

            // UOM conversions
            CupToGramConversionsSeeder::class,

            // Ensure all existing users have baseline roles
            AssignRolesToExistingUsersSeeder::class,
        ]);
    }
}
