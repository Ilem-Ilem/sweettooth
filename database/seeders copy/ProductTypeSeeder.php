<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get production category
        $productionCategory = DepartmentCategory::where('name', 'Production')->first();

        if (!$productionCategory) {
            $this->command->error('❌ Production category not found. Please run DepartmentCategorySeeder first.');
            return;
        }

        // Get production departments
        $kitchen = Department::where('name', 'Kitchen')->where('category_id', $productionCategory->id)->first();
        $gelatoProduction = Department::where('name', 'Gelato Production')->where('category_id', $productionCategory->id)->first();
        $confectionariesProduction = Department::where('name', 'Confectionaries Production')->where('category_id', $productionCategory->id)->first();

        if (!$kitchen || !$gelatoProduction || !$confectionariesProduction) {
            $this->command->error('❌ Production departments not found. Please run DepartmentSeeder first.');
            return;
        }

        $productTypes = [
            // Kitchen Department (8 types)
            [
                'department_id' => $kitchen->id,
                'name' => 'Pastries',
                'code' => 'PT',
                'description' => 'Baked pastries including croissants, danishes, and puff pastries',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Breads',
                'code' => 'BR',
                'description' => 'Fresh baked breads, loaves, and rolls',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Cakes',
                'code' => 'CK',
                'description' => 'Layer cakes, sponge cakes, and celebration cakes',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Cookies',
                'code' => 'CO',
                'description' => 'Baked cookies and biscuits',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Muffins',
                'code' => 'MF',
                'description' => 'Sweet and savory muffins',
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Pies & Tarts',
                'code' => 'PIT',
                'description' => 'Fruit pies, cream pies, and tarts',
                'status' => 'active',
                'sort_order' => 6,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Sandwiches',
                'code' => 'SW',
                'description' => 'Fresh sandwiches and wraps',
                'status' => 'active',
                'sort_order' => 7,
            ],
            [
                'department_id' => $kitchen->id,
                'name' => 'Hot Kitchen',
                'code' => 'HK',
                'description' => 'Cooked meals and hot food items',
                'status' => 'active',
                'sort_order' => 8,
            ],

            // Gelato Production Department (6 types)
            [
                'department_id' => $gelatoProduction->id,
                'name' => 'Gelato Base',
                'code' => 'GB',
                'description' => 'Base gelato mixtures before flavoring',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'department_id' => $gelatoProduction->id,
                'name' => 'Gelato Flavors',
                'code' => 'GF',
                'description' => 'Finished gelato in various flavors',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'department_id' => $gelatoProduction->id,
                'name' => 'Sorbet',
                'code' => 'SB',
                'description' => 'Fruit-based frozen desserts without dairy',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'department_id' => $gelatoProduction->id,
                'name' => 'Ice Cream',
                'code' => 'IC',
                'description' => 'Traditional ice cream products',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'department_id' => $gelatoProduction->id,
                'name' => 'Frozen Yogurt',
                'code' => 'FY',
                'description' => 'Frozen yogurt in various flavors',
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'department_id' => $gelatoProduction->id,
                'name' => 'Gelato Toppings',
                'code' => 'GT',
                'description' => 'House-made toppings and mix-ins for gelato',
                'status' => 'active',
                'sort_order' => 6,
            ],

            // Confectionaries Production Department (6 types)
            [
                'department_id' => $confectionariesProduction->id,
                'name' => 'Chocolates',
                'code' => 'CH',
                'description' => 'Handcrafted chocolates and truffles',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'department_id' => $confectionariesProduction->id,
                'name' => 'Candies',
                'code' => 'CD',
                'description' => 'Hard and soft candies',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'department_id' => $confectionariesProduction->id,
                'name' => 'Fudge',
                'code' => 'FG',
                'description' => 'Traditional and flavored fudge',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'department_id' => $confectionariesProduction->id,
                'name' => 'Caramels',
                'code' => 'CR',
                'description' => 'Soft and hard caramels',
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'department_id' => $confectionariesProduction->id,
                'name' => 'Marshmallows',
                'code' => 'MM',
                'description' => 'Gourmet marshmallows in various flavors',
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'department_id' => $confectionariesProduction->id,
                'name' => 'Nougat',
                'code' => 'NG',
                'description' => 'Traditional nougat confections',
                'status' => 'active',
                'sort_order' => 6,
            ],
        ];

        foreach ($productTypes as $type) {
            ProductType::create($type);
        }

        $this->command->info("✅ " . count($productTypes) . " product types created successfully.");
        $this->command->info("   - Kitchen: 8 types");
        $this->command->info("   - Gelato Production: 6 types");
        $this->command->info("   - Confectionaries Production: 6 types");
    }
}
