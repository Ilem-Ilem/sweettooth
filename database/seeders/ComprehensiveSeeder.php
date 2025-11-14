<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Stock;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Shift;
use App\Models\DailyProduce;
use App\Models\ProductionRecord;
use App\Models\ProductionRequest;
use App\Models\RawMaterialUtilization;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ProductDispatch;
use App\Models\ProductStock;
use App\Models\SalesShift;
use App\Models\Table as DiningTable;
use App\Models\ClockIn;
use App\Models\StockMovement;
use App\Models\StockTake;
use App\Models\StockTakeDetail;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\ItemDispatch;
use App\Models\ApprovedItem;
use App\Models\ExpiryConfirmation;
use App\Models\Receipt;
use App\Models\AuditLog;
use App\Models\ApprovalRequest;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use App\Models\EmployeeLeaveAllocation;
use App\Models\SalaryHistory;
use App\Models\ProbationReview;
use App\Models\EmployeeStepout;
use App\Models\HealthCheck;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ComprehensiveSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('🚀 Starting comprehensive database seeding...');

        // 1. Permissions & Roles (from MDSeeder)
        $this->command->info('📋 Seeding permissions and roles...');
        $this->seedPermissionsAndRoles();

        // 2. Master Data
        $this->command->info('🏢 Seeding master data...');
        $this->seedMasterData();

        // 3. Branches (expand to 20 branches)
        $this->command->info('🏭 Seeding branches...');
        $branches = $this->seedBranches(20);

        // 4. Department Categories & Departments (expand)
        $this->command->info('🏗️ Seeding departments...');
        $departments = $this->seedDepartments($branches);

        // 5. Employees (expand to 200 employees)
        $this->command->info('👥 Seeding employees...');
        $employees = $this->seedEmployees($branches, $departments, 200);

        // 6. Items & Stocks (expand to 200 items per branch)
        $this->command->info('📦 Seeding items and stocks...');
        $items = $this->seedItemsAndStocks($branches, 200);

        // 7. Product Types (expand to 50 types)
        $this->command->info('🏷️ Seeding product types...');
        $productTypes = $this->seedProductTypes($departments, 50);

        // 8. Products (expand to 100 products)
        $this->command->info('🍰 Seeding products...');
        $products = $this->seedProducts($branches, $productTypes, 100);

        // 9. Recipes & Ingredients (expand)
        $this->command->info('📖 Seeding recipes and ingredients...');
        $recipes = $this->seedRecipesAndIngredients($branches, $departments, $employees, $items, 50);

        // 10. Production Data
        $this->command->info('🏭 Seeding production data...');
        $this->seedProductionData($branches, $departments, $employees, $recipes, $items, 50);

        // 11. Sales Data
        $this->command->info('💰 Seeding sales data...');
        $this->seedSalesData($branches, $departments, $employees, $products, 100);

        // 12. Purchase Data
        $this->command->info('🛒 Seeding purchase data...');
        $this->seedPurchaseData($branches, $employees, $items, 50);

        // 13. Inventory Management
        $this->command->info('📊 Seeding inventory management data...');
        $this->seedInventoryManagement($branches, $employees, $items, $products, 50);

        // 14. HR Data
        $this->command->info('👔 Seeding HR data...');
        $this->seedHRData($employees, 50);

        // 15. System Data
        $this->command->info('⚙️ Seeding system data...');
        $this->seedSystemData($branches, $employees, 50);

        $this->command->info('✅ Comprehensive seeding completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info("   - Branches: {$branches->count()}");
        $this->command->info("   - Departments: {$departments->count()}");
        $this->command->info("   - Employees: {$employees->count()}");
        $this->command->info("   - Items: " . count($items));
        $this->command->info("   - Products: {$products->count()}");
        $this->command->info("   - Recipes: {$recipes->count()}");
    }

    private function seedPermissionsAndRoles()
    {
        $role = Role::firstOrCreate(['name' => 'MD']);
        $permissions = [
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'approve employee leave', 'view employee attendance', 'manage payroll',
            'view hr reports', 'assign departments', 'approve recruitment',
            'view employee profile', 'generate employee report', 'terminate employee',
            'view salary structure', 'update employee role', 'view performance reviews',
            'view production batches', 'create production batch', 'edit production batch', 'delete production batch',
            'approve production plan', 'view production schedule', 'manage production line', 'monitor production status',
            'record production output', 'update production cost', 'view quality control report',
            'approve quality control', 'view wastage reports', 'update machinery status', 'log equipment maintenance',
            'view inventory', 'create inventory item', 'edit inventory item', 'delete inventory item',
            'adjust stock levels', 'view stock history', 'transfer stock', 'receive stock',
            'issue raw materials', 'view warehouse report', 'manage warehouse location',
            'view expiry tracking', 'mark damaged goods', 'approve restock', 'monitor inventory alerts',
            'view suppliers', 'create supplier', 'edit supplier', 'delete supplier',
            'approve purchase order', 'create purchase order', 'edit purchase order', 'view purchase history',
            'receive goods', 'approve vendor payment', 'view logistics status', 'schedule delivery',
            'approve transportation', 'track shipment', 'view import/export records', 'manage procurement report',
            'view sales records', 'create sales order', 'edit sales order', 'delete sales order',
            'approve sales invoice', 'process refund', 'view financial dashboard', 'approve expense',
            'view profit and loss', 'manage tax settings', 'generate sales reports',
            'approve discounts', 'manage pricing structure', 'view payment history',
            'view cash flow', 'update financial policy',
            'view audit logs', 'manage users', 'manage roles', 'manage permissions',
            'view system settings', 'update company info', 'backup database', 'restore backup',
            'view activity logs', 'manage notifications', 'view dashboard', 'access API tokens',
            'view analytics', 'view KPI metrics', 'manage departments', 'configure approval workflow',
            'access admin panel', 'view change history', 'enable maintenance mode', 'disable maintenance mode',
            'generate compliance report', 'view supplier compliance', 'approve compliance status',
            'view environmental reports', 'manage food safety records', 'view traceability logs',
            'approve export documents', 'review regulatory submissions', 'view recall reports',
        ];

        foreach (array_unique($permissions) as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        $role->syncPermissions($permissions);

        $user = User::firstOrCreate(
            ['email' => 'md@sweettooth.com'],
            [
                'name' => 'Managing Director',
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole($role);
    }

    private function seedMasterData()
    {
        // Leave Types
        $leaveTypes = [
            ['name' => 'Annual Leave', 'code' => 'AL', 'days_allowed' => 21, 'description' => 'Annual paid leave'],
            ['name' => 'Sick Leave', 'code' => 'SL', 'days_allowed' => 10, 'description' => 'Medical leave'],
            ['name' => 'Maternity Leave', 'code' => 'ML', 'days_allowed' => 84, 'description' => 'Maternity leave'],
            ['name' => 'Paternity Leave', 'code' => 'PL', 'days_allowed' => 7, 'description' => 'Paternity leave'],
            ['name' => 'Emergency Leave', 'code' => 'EL', 'days_allowed' => 5, 'description' => 'Emergency leave'],
        ];

        foreach ($leaveTypes as $type) {
            LeaveType::firstOrCreate($type);
        }
    }

    private function seedBranches($count)
    {
        $branches = [];
        $faker = Faker::create();
        $cities = ['Lagos', 'Abuja', 'Port Harcourt', 'Calabar', 'Enugu', 'Kano', 'Ibadan', 'Benin City', 'Warri', 'Jos'];

        for ($i = 1; $i <= $count; $i++) {
            $city = $faker->randomElement($cities);
            $branches[] = Branch::create([
                'name' => "SweetTooth {$city} Branch {$i}",
                'code' => strtoupper(substr($city, 0, 3)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'location' => $faker->streetAddress() . ', ' . $city,
                'phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
                'email' => strtolower($city) . $i . '@sweettooth.com',
                'description' => "SweetTooth branch in {$city}",
                'country' => 'Nigeria',
                'state' => $city,
                'city' => $city,
                'postal_code' => $faker->numberBetween(100000, 999999),
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ]);
        }

        return collect($branches);
    }

    private function seedDepartments($branches)
    {
        $production = DepartmentCategory::firstOrCreate(['name' => 'Production']);
        $sales = DepartmentCategory::firstOrCreate(['name' => 'Sales']);
        $support = DepartmentCategory::firstOrCreate(['name' => 'Support']);

        $departments = [
            // Production
            ['name' => 'Kitchen', 'category_id' => $production->id],
            ['name' => 'Gelato Production', 'category_id' => $production->id],
            ['name' => 'Confectionaries Production', 'category_id' => $production->id],
            ['name' => 'Bakery', 'category_id' => $production->id],
            ['name' => 'Beverage Production', 'category_id' => $production->id],

            // Sales
            ['name' => 'Till', 'category_id' => $sales->id],
            ['name' => 'Corner Store', 'category_id' => $sales->id],
            ['name' => 'Confectionaries Sales', 'category_id' => $sales->id],
            ['name' => 'Dine-in Service', 'category_id' => $sales->id],
            ['name' => 'Online Orders', 'category_id' => $sales->id],

            // Support
            ['name' => 'Inventory/Store', 'category_id' => $support->id],
            ['name' => 'HR', 'category_id' => $support->id],
            ['name' => 'Finance', 'category_id' => $support->id],
            ['name' => 'IT', 'category_id' => $support->id],
            ['name' => 'Maintenance', 'category_id' => $support->id],
        ];

        $createdDepartments = [];
        foreach ($departments as $dept) {
            $createdDepartments[] = Department::create(array_merge($dept, [
                'description' => ucfirst($dept['name']) . ' department',
                'branch_id' => null, // Global departments
            ]));
        }

        return collect($createdDepartments);
    }

    private function seedEmployees($branches, $departments, $count)
    {
        $faker = Faker::create();
        $roles = Role::where('guard_name', 'employees')->get()->keyBy('name');
        $hashedPassword = Hash::make('password');
        $employees = [];

        $nigerianNames = [
            'male' => ['Chukwuemeka', 'Oluwaseun', 'Abubakar', 'Emeka', 'Tunde', 'Chigozie', 'Ibrahim', 'Kunle', 'Obinna', 'Yusuf'],
            'female' => ['Ngozi', 'Amina', 'Chioma', 'Folake', 'Kemi', 'Blessing', 'Hauwa', 'Ada', 'Fatima', 'Nneka']
        ];
        $surnames = ['Okafor', 'Adebayo', 'Mohammed', 'Nwankwo', 'Ogunleye', 'Chukwu', 'Bello', 'Okoro', 'Aliyu', 'Eze'];

        for ($i = 0; $i < $count; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->randomElement($nigerianNames[$gender]);
            $lastName = $faker->randomElement($surnames);
            $branch = $faker->randomElement($branches);
            $department = $faker->randomElement($departments);

            $employee = Employee::create([
                'id' => $faker->uuid(),
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'employee_number' => 'EMP-' . str_replace(['-', ' '], '', strtoupper($branch->code)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name' => $firstName . ' ' . $lastName,
                'email' => strtolower($firstName . '.' . $lastName . $i) . '@sweettooth.com',
                'phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
                'address' => $faker->streetAddress() . ', ' . $branch->city,
                'date_of_birth' => $faker->dateTimeBetween('-45 years', '-22 years')->format('Y-m-d'),
                'gender' => $gender,
                'nationality' => 'Nigerian',
                'emergency_contact_name' => $faker->randomElement($nigerianNames[$gender === 'male' ? 'female' : 'male']) . ' ' . $faker->randomElement($surnames),
                'emergency_contact_phone' => '+234-' . $faker->numberBetween(800, 909) . '-' . $faker->numberBetween(100, 999) . '-' . $faker->numberBetween(1000, 9999),
                'hire_date' => $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
                'termination_date' => null,
                'status' => $faker->randomElement(['active', 'active', 'active', 'on_probation']),
                'probation_end_date' => null,
                'shift_preference' => $faker->randomElement(['morning', 'afternoon', 'rotating', 'flexible']),
                'salary' => $faker->randomFloat(2, 80000, 350000),
                'hourly_rate' => null,
                'tax_id' => 'TIN-' . $faker->numberBetween(10000000, 99999999),
                'bank_account' => $faker->numerify('##########'),
                'allergies' => $faker->boolean(15) ? $faker->randomElement(['None', 'Peanuts', 'Shellfish', 'Lactose']) : null,
                'profile_photo' => null,
                'last_performance_review_date' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'performance_rating' => $faker->randomFloat(1, 3.5, 5.0),
                'password' => $hashedPassword,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign a random role if available
            if ($roles->isNotEmpty()) {
                $role = $faker->randomElement($roles->values());
                $employee->assignRole($role);
            }

            $employees[] = $employee;
        }

        return collect($employees);
    }

    private function seedItemsAndStocks($branches, $itemsPerBranch)
    {
        $faker = Faker::create();
        $allItems = [];

        $itemTemplates = [
            ['name' => 'Sugar - White Granulated', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 50, 'max_stock_level' => 500],
            ['name' => 'Flour - All Purpose', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Cocoa Powder - Premium Dark', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 20, 'max_stock_level' => 200],
            ['name' => 'Butter - Salted', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 30, 'max_stock_level' => 300],
            ['name' => 'Eggs - Large Grade A', 'category' => 'raw_material', 'uom' => 'cartons', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Vanilla Extract - Pure', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 5, 'max_stock_level' => 30],
            ['name' => 'Chocolate Chips - Dark', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 15, 'max_stock_level' => 150],
            ['name' => 'Milk - Fresh Whole', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 20, 'max_stock_level' => 100],
            ['name' => 'Cream - Heavy Whipping', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Yeast - Active Dry', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 5, 'max_stock_level' => 25],
            ['name' => 'Cake Boxes - 10 inch', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Paper Bags - Brown', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 500, 'max_stock_level' => 5000],
            ['name' => 'Plastic Containers', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 150, 'max_stock_level' => 1500],
            ['name' => 'Dishwashing Liquid', 'category' => 'consumable', 'uom' => 'liters', 'reorder_level' => 20, 'max_stock_level' => 100],
            ['name' => 'Paper Towels', 'category' => 'consumable', 'uom' => 'units', 'reorder_level' => 50, 'max_stock_level' => 200],
        ];

        foreach ($branches as $branch) {
            $branchCode = strtoupper(substr($branch->code, 0, 3));
            $branchItems = [];

            for ($i = 0; $i < $itemsPerBranch; $i++) {
                $template = $faker->randomElement($itemTemplates);
                $sku = sprintf('%s-ITM-%05d', $branchCode, $i + 1);

                $item = Item::create([
                    'branch_id' => $branch->id,
                    'name' => $template['name'] . ' - ' . $faker->randomElement(['Premium', 'Standard', 'Bulk', 'Organic']),
                    'sku' => $sku,
                    'category' => $template['category'],
                    'uom' => $template['uom'],
                    'description' => $faker->sentence,
                    'reorder_level' => $template['reorder_level'],
                    'max_stock_level' => $template['max_stock_level'],
                    'status' => 'active',
                ]);

                // Create stock
                $quantityAvailable = rand(50, 400);
                $quantityReserved = rand(0, (int)($quantityAvailable * 0.1));
                $quantityDamaged = rand(0, (int)($quantityAvailable * 0.05));

                $averageCost = match($item->category) {
                    'raw_material' => rand(500, 5000) / 10,
                    'packaging' => rand(50, 500) / 10,
                    'consumable' => rand(300, 3000) / 10,
                    default => rand(500, 5000) / 10
                };

                $healthStatuses = ['good', 'good', 'good', 'warning', 'critical'];
                $healthStatus = $faker->randomElement($healthStatuses);

                $expiryDate = null;
                if (in_array($item->category, ['raw_material', 'consumable'])) {
                    $daysToExpiry = match($healthStatus) {
                        'good' => rand(90, 365),
                        'warning' => rand(30, 89),
                        'critical' => rand(7, 29),
                        default => rand(60, 180)
                    };
                    $expiryDate = now()->addDays($daysToExpiry);
                }

                Stock::create([
                    'branch_id' => $branch->id,
                    'item_id' => $item->id,
                    'quantity_available' => $quantityAvailable,
                    'quantity_reserved' => $quantityReserved,
                    'quantity_damaged' => $quantityDamaged,
                    'average_cost' => $averageCost,
                    'last_stock_take_date' => now()->subDays(rand(1, 30)),
                    'health_status' => $healthStatus,
                    'expiry_date' => $expiryDate,
                ]);

                $branchItems[] = $item;
            }

            $allItems = array_merge($allItems, $branchItems);
        }

        return $allItems;
    }

    private function seedProductTypes($departments, $count)
    {
        $faker = Faker::create();
        $productTypes = [];

        $productionDepts = $departments->where('category_id', DepartmentCategory::where('name', 'Production')->first()->id);

        $typeTemplates = [
            ['name' => 'Pastries', 'code' => 'PT'],
            ['name' => 'Breads', 'code' => 'BR'],
            ['name' => 'Cakes', 'code' => 'CK'],
            ['name' => 'Cookies', 'code' => 'CO'],
            ['name' => 'Gelato Base', 'code' => 'GB'],
            ['name' => 'Gelato Flavors', 'code' => 'GF'],
            ['name' => 'Chocolates', 'code' => 'CH'],
            ['name' => 'Candies', 'code' => 'CD'],
            ['name' => 'Beverages', 'code' => 'BV'],
            ['name' => 'Desserts', 'code' => 'DS'],
        ];

        for ($i = 0; $i < $count; $i++) {
            $template = $faker->randomElement($typeTemplates);
            $dept = $faker->randomElement($productionDepts);

            $productTypes[] = ProductType::create([
                'department_id' => $dept->id,
                'name' => $template['name'] . ' ' . $faker->randomElement(['Premium', 'Standard', 'Mini', 'Large']),
                'code' => $template['code'] . $i,
                'description' => $faker->sentence,
                'status' => 'active',
                'sort_order' => $i + 1,
            ]);
        }

        return collect($productTypes);
    }

    private function seedProducts($branches, $productTypes, $count)
    {
        $faker = Faker::create();
        $products = [];

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $productType = $faker->randomElement($productTypes);

            $products[] = Product::create([
                'branch_id' => $branch->id,
                'name' => $faker->randomElement(['Chocolate Cake', 'Vanilla Ice Cream', 'Strawberry Pastry', 'Blueberry Muffin', 'Dark Chocolate Truffle']) . ' ' . $faker->randomElement(['Slice', 'Scoop', 'Piece', 'Box']),
                'sku' => 'PRD-' . strtoupper(substr($branch->code, 0, 3)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'product_type_id' => $productType->id,
                'description' => $faker->sentence,
                'price' => $faker->randomFloat(2, 2.50, 25.00),
                'cost' => $faker->randomFloat(2, 0.80, 15.00),
                'shelf_life_days' => $faker->numberBetween(1, 30),
                'uom' => $faker->randomElement(['pcs', 'grams', 'kg', 'liters']),
                'unit_weight' => $faker->numberBetween(50, 1000),
                'is_active' => true,
                'is_available' => $faker->boolean(90), // 90% available
                'allergens' => $faker->randomElements(['gluten', 'dairy', 'eggs', 'nuts'], $faker->numberBetween(0, 3)),
                'tags' => $faker->randomElements(['popular', 'seasonal', 'premium', 'diet'], $faker->numberBetween(0, 2)),
            ]);
        }

        return collect($products);
    }

    private function seedRecipesAndIngredients($branches, $departments, $employees, $items, $count)
    {
        $faker = Faker::create();
        $recipes = [];

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $department = $faker->randomElement($departments->where('category_id', DepartmentCategory::where('name', 'Production')->first()->id));

            $recipe = Recipe::create([
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'product_name' => $faker->randomElement(['Chocolate Cake', 'Vanilla Gelato', 'Butter Croissant', 'Strawberry Ice Cream', 'Blueberry Muffin']),
                'sku' => 'REC-' . strtoupper(substr($branch->code, 0, 3)) . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'product_type' => $faker->randomElement(['gelato_base', 'gelato_flavor', 'pastry', 'hot_kitchen', 'beverage']),
                'cost_per_unit' => $faker->randomFloat(4, 0.5, 50),
                'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
                'yield_quantity' => $faker->randomFloat(2, 1, 100),
                'preparation_time' => $faker->numberBetween(5, 120),
                'instructions' => $faker->paragraph,
                'status' => $faker->randomElement(['active', 'inactive', 'testing']),
                'created_by' => $faker->randomElement($employees)->id,
            ]);

            // Add ingredients (3-8 per recipe)
            $ingredientCount = $faker->numberBetween(3, 8);
            $selectedItems = $faker->randomElements($items, $ingredientCount);

            foreach ($selectedItems as $index => $item) {
                RecipeIngredient::create([
                    'recipe_id' => $recipe->id,
                    'item_id' => $item->id,
                    'quantity' => $faker->randomFloat(4, 0.1, 10),
                    'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
                    'sort_order' => $index + 1,
                    'notes' => $faker->optional()->sentence,
                ]);
            }

            $recipes[] = $recipe;
        }

        return collect($recipes);
    }

    private function seedProductionData($branches, $departments, $employees, $recipes, $items, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $department = $faker->randomElement($departments);
            $employee = $faker->randomElement($employees);

            $shift = Shift::create([
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'employee_id' => $employee->id,
                'shift_number' => 'SHIFT-' . Str::random(6),
                'shift_date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                'shift_type' => $faker->randomElement(['morning', 'afternoon', 'night']),
                'clock_in' => $faker->dateTimeBetween('-1 day', 'now'),
                'clock_out' => $faker->optional()->dateTimeBetween('now', '+1 day'),
                'status' => $faker->randomElement(['active', 'closed', 'submitted']),
                'notes' => $faker->optional()->sentence,
            ]);

            // Daily Produce
            $recipe = $faker->randomElement($recipes);
            DailyProduce::create([
                'shift_id' => $shift->id,
                'recipe_id' => $recipe->id,
                'produce_date' => $shift->shift_date,
                'shift_type' => $faker->randomElement(['morning', 'afternoon']),
                'opening_quantity' => $faker->randomFloat(2, 0, 100),
                'requested_quantity' => $faker->randomFloat(2, 0, 50),
                'produced_quantity' => $faker->randomFloat(2, 0, 50),
                'sent_out_quantity' => $faker->randomFloat(2, 0, 40),
                'order_quantity' => $faker->randomFloat(2, 0, 30),
                'callback_quantity' => $faker->randomFloat(2, 0, 10),
                'closing_quantity' => $faker->randomFloat(2, 0, 50),
                'expected_closing' => $faker->randomFloat(2, 0, 50),
                'variance' => $faker->randomFloat(2, -10, 10),
                'notes' => $faker->optional()->sentence,
            ]);

            // Production Record
            ProductionRecord::create([
                'daily_produce_id' => $shift->id, // Using shift_id as placeholder
                'recipe_id' => $recipe->id,
                'produced_by' => $employee->id,
                'quantity_produced' => $faker->randomFloat(2, 1, 50),
                'quantity_approved' => $faker->randomFloat(2, 0, 50),
                'quantity_rejected' => $faker->randomFloat(2, 0, 10),
                'production_time' => $faker->dateTimeBetween('-1 day', 'now'),
                'quality_status' => $faker->randomElement(['excellent', 'good', 'acceptable', 'rejected']),
                'rejection_reason' => $faker->optional()->sentence,
                'notes' => $faker->optional()->sentence,
            ]);

            // Raw Material Utilization
            $item = $faker->randomElement($items);
            $quantityRequired = $faker->randomFloat(4, 0.1, 10);
            $quantityUsed = $quantityRequired + $faker->randomFloat(4, -0.5, 0.5);

            RawMaterialUtilization::create([
                'shift_id' => $shift->id,
                'recipe_id' => $recipe->id,
                'item_id' => $item->id,
                'quantity_required' => $quantityRequired,
                'quantity_used' => $quantityUsed,
                'units_produced' => $faker->randomFloat(2, 1, 50),
                'variance' => $quantityUsed - $quantityRequired,
                'variance_type' => $faker->randomElement(['within_tolerance', 'over_used', 'under_used']),
                'cost_impact' => $faker->randomFloat(2, 0, 100),
                'notes' => $faker->optional()->sentence,
            ]);
        }
    }

    private function seedSalesData($branches, $departments, $employees, $products, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $employee = $faker->randomElement($employees);

            $sale = Sale::create([
                'branch_id' => $branch->id,
                'employee_id' => $employee->id,
                'sale_number' => 'SALE-' . strtoupper(substr($branch->code, 0, 3)) . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'sale_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'total_amount' => 0, // Will be calculated
                'discount_amount' => $faker->randomFloat(2, 0, 100),
                'tax_amount' => 0, // Will be calculated
                'grand_total' => 0, // Will be calculated
                'payment_status' => $faker->randomElement(['paid', 'pending', 'partial']),
                'sale_type' => $faker->randomElement(['dine_in', 'takeaway', 'delivery']),
                'customer_name' => $faker->optional()->name,
                'customer_phone' => $faker->optional()->phoneNumber,
                'notes' => $faker->optional()->sentence,
            ]);

            // Sale Items (2-5 items per sale)
            $itemCount = $faker->numberBetween(2, 5);
            $selectedProducts = $faker->randomElements($products->toArray(), $itemCount);
            $totalAmount = 0;
            $taxAmount = 0;

            foreach ($selectedProducts as $product) {
                $quantity = $faker->numberBetween(1, 5);
                $unitPrice = $product->price;
                $subtotal = $quantity * $unitPrice;
                $tax = $subtotal * 0.075; // 7.5% tax

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'tax_amount' => $tax,
                    'discount_amount' => $faker->randomFloat(2, 0, $subtotal * 0.1),
                    'total_amount' => $subtotal + $tax,
                    'notes' => $faker->optional()->sentence,
                ]);

                $totalAmount += $subtotal;
                $taxAmount += $tax;
            }

            // Update sale totals
            $sale->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => $totalAmount + $taxAmount - $sale->discount_amount,
            ]);

            // Payment
            if ($sale->payment_status !== 'pending') {
                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_method' => $faker->randomElement(['cash', 'card', 'transfer']),
                    'amount' => $sale->grand_total,
                    'payment_date' => $sale->sale_date,
                    'reference_number' => $faker->optional()->uuid,
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function seedPurchaseData($branches, $employees, $items, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $employee = $faker->randomElement($employees);

            $purchase = Purchase::create([
                'branch_id' => $branch->id,
                'supplier_name' => $faker->company,
                'supplier_contact' => $faker->phoneNumber,
                'purchase_order_number' => 'PO-' . strtoupper(substr($branch->code, 0, 3)) . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'purchase_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'expected_delivery_date' => $faker->dateTimeBetween('now', '+7 days'),
                'total_amount' => 0, // Will be calculated
                'status' => $faker->randomElement(['ordered', 'received', 'cancelled']),
                'approved_by' => $employee->id,
                'received_by' => $faker->optional()->randomElement($employees)->id,
                'notes' => $faker->optional()->sentence,
            ]);

            // Purchase Items (3-8 items per purchase)
            $itemCount = $faker->numberBetween(3, 8);
            $selectedItems = $faker->randomElements($items, $itemCount);
            $totalAmount = 0;

            foreach ($selectedItems as $item) {
                $quantity = $faker->numberBetween(10, 100);
                $unitPrice = $faker->randomFloat(2, 50, 500);
                $subtotal = $quantity * $unitPrice;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'quantity_ordered' => $quantity,
                    'quantity_received' => $purchase->status === 'received' ? $quantity : 0,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'notes' => $faker->optional()->sentence,
                ]);

                $totalAmount += $subtotal;
            }

            $purchase->update(['total_amount' => $totalAmount]);
        }
    }

    private function seedInventoryManagement($branches, $employees, $items, $products, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $employee = $faker->randomElement($employees);

            // Stock Movement
            $item = $faker->randomElement($items);
            StockMovement::create([
                'branch_id' => $branch->id,
                'item_id' => $item->id,
                'movement_type' => $faker->randomElement(['in', 'out', 'adjustment']),
                'quantity' => $faker->numberBetween(-50, 100),
                'reason' => $faker->randomElement(['purchase', 'sale', 'adjustment', 'waste', 'transfer']),
                'reference_number' => $faker->optional()->uuid,
                'performed_by' => $employee->id,
                'movement_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'notes' => $faker->optional()->sentence,
            ]);

            // Stock Take
            StockTake::create([
                'branch_id' => $branch->id,
                'item_id' => $item->id,
                'counted_quantity' => $faker->numberBetween(0, 500),
                'system_quantity' => $faker->numberBetween(0, 500),
                'variance' => $faker->numberBetween(-50, 50),
                'performed_by' => $employee->id,
                'stock_take_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'notes' => $faker->optional()->sentence,
            ]);

            // Product Stock
            $product = $faker->randomElement($products);
            ProductStock::create([
                'branch_id' => $branch->id,
                'product_id' => $product->id,
                'quantity_available' => $faker->numberBetween(0, 200),
                'quantity_reserved' => $faker->numberBetween(0, 20),
                'quantity_damaged' => $faker->numberBetween(0, 10),
                'last_updated' => $faker->dateTimeBetween('-7 days', 'now'),
                'notes' => $faker->optional()->sentence,
            ]);

            // Item Request
            ItemRequest::create([
                'branch_id' => $branch->id,
                'requested_by' => $employee->id,
                'approved_by' => $faker->optional()->randomElement($employees)->id,
                'request_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'required_date' => $faker->dateTimeBetween('now', '+7 days'),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'fulfilled']),
                'priority' => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
                'notes' => $faker->optional()->sentence,
            ]);

            // Expiry Confirmation
            ExpiryConfirmation::create([
                'branch_id' => $branch->id,
                'item_id' => $item->id,
                'expiry_date' => $faker->dateTimeBetween('now', '+365 days'),
                'quantity_affected' => $faker->numberBetween(1, 50),
                'action_taken' => $faker->randomElement(['dispose', 'sell_at_discount', 'return_to_supplier']),
                'confirmed_by' => $employee->id,
                'confirmation_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'notes' => $faker->optional()->sentence,
            ]);
        }
    }

    private function seedHRData($employees, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $employee = $faker->randomElement($employees);

            // Leave Application
            LeaveApplication::create([
                'employee_id' => $employee->id,
                'leave_type_id' => LeaveType::inRandomOrder()->first()->id,
                'start_date' => $faker->dateTimeBetween('now', '+30 days'),
                'end_date' => $faker->dateTimeBetween('+31 days', '+60 days'),
                'days_requested' => $faker->numberBetween(1, 14),
                'reason' => $faker->sentence,
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'approved_by' => $faker->optional()->randomElement($employees)->id,
                'approved_date' => $faker->optional()->dateTimeBetween('-30 days', 'now'),
                'notes' => $faker->optional()->sentence,
            ]);

            // Employee Leave Balance
            EmployeeLeaveBalance::create([
                'employee_id' => $employee->id,
                'leave_type_id' => LeaveType::inRandomOrder()->first()->id,
                'year' => date('Y'),
                'allocated_days' => $faker->numberBetween(10, 30),
                'used_days' => $faker->numberBetween(0, 15),
                'remaining_days' => $faker->numberBetween(5, 25),
                'carried_forward' => $faker->numberBetween(0, 5),
            ]);

            // Salary History
            SalaryHistory::create([
                'employee_id' => $employee->id,
                'old_salary' => $faker->randomFloat(2, 50000, 200000),
                'new_salary' => $faker->randomFloat(2, 60000, 300000),
                'effective_date' => $faker->dateTimeBetween('-365 days', 'now'),
                'reason' => $faker->randomElement(['annual_review', 'promotion', 'cost_of_living', 'performance']),
                'approved_by' => $faker->optional()->randomElement($employees)->id,
                'notes' => $faker->optional()->sentence,
            ]);

            // Probation Review
            ProbationReview::create([
                'employee_id' => $employee->id,
                'review_date' => $faker->dateTimeBetween('-180 days', 'now'),
                'reviewer_id' => $faker->optional()->randomElement($employees)->id,
                'performance_rating' => $faker->randomFloat(1, 1.0, 5.0),
                'comments' => $faker->paragraph,
                'recommendation' => $faker->randomElement(['extend_probation', 'confirm_employment', 'terminate']),
                'next_review_date' => $faker->optional()->dateTimeBetween('now', '+180 days'),
                'status' => $faker->randomElement(['completed', 'pending', 'overdue']),
            ]);

            // Clock In
            ClockIn::create([
                'employee_id' => $employee->id,
                'branch_id' => $employee->branch_id,
                'clock_in_time' => $faker->dateTimeBetween('-8 hours', '-1 hour'),
                'clock_out_time' => $faker->optional()->dateTimeBetween('-1 hour', 'now'),
                'total_hours' => $faker->randomFloat(2, 4, 12),
                'status' => $faker->randomElement(['active', 'completed', 'missed']),
                'notes' => $faker->optional()->sentence,
            ]);
        }
    }

    private function seedSystemData($branches, $employees, $count)
    {
        $faker = Faker::create();

        for ($i = 0; $i < $count; $i++) {
            $branch = $faker->randomElement($branches);
            $employee = $faker->randomElement($employees);

            // Audit Log
            AuditLog::create([
                'user_id' => $employee->id,
                'action' => $faker->randomElement(['create', 'update', 'delete', 'view']),
                'model_type' => $faker->randomElement(['Employee', 'Product', 'Sale', 'Purchase', 'Item']),
                'model_id' => $faker->numberBetween(1, 1000),
                'old_values' => json_encode(['field' => 'old_value']),
                'new_values' => json_encode(['field' => 'new_value']),
                'ip_address' => $faker->ipv4,
                'user_agent' => $faker->userAgent,
                'performed_at' => $faker->dateTimeBetween('-30 days', 'now'),
            ]);

            // Approval Request
            ApprovalRequest::create([
                'request_type' => $faker->randomElement(['leave', 'purchase', 'expense', 'promotion']),
                'request_id' => $faker->numberBetween(1, 1000),
                'requested_by' => $employee->id,
                'approved_by' => $faker->optional()->randomElement($employees)->id,
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'approved_at' => $faker->optional()->dateTimeBetween('-30 days', 'now'),
                'comments' => $faker->optional()->sentence,
                'priority' => $faker->randomElement(['low', 'medium', 'high']),
            ]);

            // Receipt
            Receipt::create([
                'sale_id' => $faker->numberBetween(1, 100), // Assuming sales exist
                'receipt_number' => 'RCP-' . strtoupper(substr($branch->code, 0, 3)) . '-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'printed_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'printed_by' => $employee->id,
                'receipt_data' => json_encode(['total' => $faker->randomFloat(2, 10, 500)]),
            ]);

            // Health Check
            HealthCheck::create([
                'service_name' => $faker->randomElement(['database', 'cache', 'queue', 'storage']),
                'status' => $faker->randomElement(['healthy', 'unhealthy', 'warning']),
                'response_time' => $faker->numberBetween(100, 5000),
                'memory_usage' => $faker->numberBetween(50, 1000),
                'checked_at' => $faker->dateTimeBetween('-1 hour', 'now'),
                'details' => json_encode(['cpu' => $faker->numberBetween(10, 90)]),
            ]);
        }
    }
}
