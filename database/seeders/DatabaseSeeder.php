<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            // 1. Permissions & Roles (Critical - must be first)
            PermissionSeeder::class,
            RoleSeeder::class,
            FixRolePermissionsSeeder::class, // Ensure all roles have complete permissions

            // 2. Leave Types
            LeaveTypeSeeder::class,

            // 3. Super Admin & Users (Before branch setup)
            SuperAdminUserSeeder::class,

            // 4. Branch & Department Setup
            BranchSeeder::class,
            BranchUserSeeder::class,
            UnitOfMeasureSeeder::class,

            DepartmentCategorySeeder::class,
            DepartmentSeeder::class,

            // 5. Employees (needs branches and departments)
            EmployeeSeeder::class,

            // 6. Inventory Items (needs branches)
            ItemSeeder::class, // Creates 20 items per branch with stocks

            // 7. Products Setup
            ProductTypeSeeder::class,
            ProductSeeder::class, // Creates products with branch_id

            // 8. Department-Product Relationships
            DepartmentProductSeeder::class, // Links products to sales departments

            // 9. Recipes & Ingredients
            ProductRecipeSeeder::class, // Creates recipes linked to products with ingredients

            // 10. Additional Roles
            InventoryStoreRolesSeeder::class, // Additional inventory and store management roles

            // 11. Currency Localization (should come after basic setup)
            CurrencyLocalizationSeeder::class,

            // 12. UI Pages
            DepartmentPageSeeder::class,
            SalesPagesSeeder::class,
        ]);
    }
}
