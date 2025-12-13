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
            WebRoleSeeder::class, // Create web guard roles for super admin
            ProtectedRoleSeeder::class,

            // 2. Accounting Setup (Before any other seeders)
            ChartOfAccountsSeeder::class,
            AccountingAccessControlSeeder::class,

            // 3. Super Admin User (Before branch setup)
            SuperAdminUserSeeder::class,

            // 4. Branch & Department Setup
            BranchSeeder::class,
            MDSeeder::class,
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

            // 10. Production & UI Pages
            // ProductionSeeder::class, // Skipped due to unique constraint issues
            DepartmentPageSeeder::class,
            SalesPagesSeeder::class,
        ]);
    }
}
