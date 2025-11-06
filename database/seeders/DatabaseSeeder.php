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
            // 1. Permissions & Roles
            PermissionSeeder::class,
            RoleSeeder::class,

            // 2. Master Data
            MDSeeder::class,

            // 3. Branch & Department Setup
            BranchSeeder::class,
            DepartmentCategorySeeder::class,
            DepartmentSeeder::class,

            // 4. Employees (needs branches and departments)
            EmployeeSeeder::class,

            // 5. Inventory Items (needs branches)
            ItemSeeder::class, // Creates 20 items per branch with stocks

            // 6. Products Setup
            ProductTypeSeeder::class,
            ProductSeeder::class, // Creates products with branch_id

            // 7. Department-Product Relationships
            DepartmentProductSeeder::class, // Links products to sales departments

            // 8. Recipes & Ingredients
            ProductRecipeSeeder::class, // Creates recipes linked to products with ingredients

            // 9. Production & UI Pages
            ProductionSeeder::class,
            DepartmentPageSeeder::class,
            SalesPagesSeeder::class,
        ]);
    }
}
