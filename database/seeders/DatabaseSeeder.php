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
            FixRolePermissionsSeeder::class, // Ensure all roles have complete permissions

            // 11. Currency Localization (should come after basic setup)
            CurrencyLocalizationSeeder::class,

            // 12. UI Pages
            DepartmentPageSeeder::class,
            SalesPagesSeeder::class,

            // 13. Accounting Setup (after basic business entities)
            AccountingPeriodSeeder::class,
            ChartOfAccountsSeeder::class,
            GlAccountSeeder::class,

            // 14. Banking Setup (after General Ledger accounts are created)
            BankAccountSeeder::class,

            // 15. Sample General Ledger Entries (after periods and accounts are created)
            GlEntrySeeder::class,  // Note: Still refers to GlEntry but represents General Ledger entries

            // 16. Global Business Configuration
            GlobalBusinessConfigurationSeeder::class,

            // 17. Comprehensive Dummy Data
            AdditionalDummyDataSeeder::class,

            // 18. Product Stock Data for POS Testing
            ProductStockSeeder::class,

            // 19. Link all products to Till for POS availability
            LinkAllProductsToTillSeeder::class,

            // 20. Assign POS access roles to users
            AssignPosAccessRoleSeeder::class,

            // 21. Ensure Super Admin has all permissions
            EnsureSuperAdminPermissionsSeeder::class,
        ]);
    }
}
