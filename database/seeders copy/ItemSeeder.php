<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Item;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting item seeding for all branches...');

        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found! Please seed branches first.');

            return;
        }

        $totalItemsCreated = 0;
        $totalStocksCreated = 0;

        foreach ($branches as $branch) {
            $this->command->info("Creating 20 items for branch: {$branch->name}");

            $items = $this->createItemsForBranch($branch);
            $totalItemsCreated += count($items);
            $itemCount = count($items);

            $this->command->info("Created {$itemCount} items for {$branch->name}");

            // Create stock records for each item
            $stocks = $this->createStocksForItems($items, $branch);
            $totalStocksCreated += count($stocks);
            $stockCount = count($stocks);

            $this->command->info("Created {$stockCount} stock records for {$branch->name}");
        }

        $this->command->info('✓ Seeding completed!');
        $this->command->info("✓ Total items created: {$totalItemsCreated}");
        $this->command->info("✓ Total stock records created: {$totalStocksCreated}");
        $this->command->info("✓ Branches processed: {$branches->count()}");
    }

    /**
     * Create 20 diverse items for a specific branch
     */
    private function createItemsForBranch(Branch $branch): array
    {
        $branchCode = str_replace(' ', '', substr($branch->code ?? $branch->name, 0, 3));
        $branchCode = strtoupper($branchCode);

        $itemsData = [
            // RAW MATERIALS (12 items)
            [
                'name' => 'Sugar - White Granulated',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 50,
                'max_stock_level' => 500,
                'description' => 'Premium white granulated sugar for baking and cooking',
            ],
            [
                'name' => 'Flour - All Purpose',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 100,
                'max_stock_level' => 1000,
                'description' => 'High-quality all-purpose flour for general baking',
            ],
            [
                'name' => 'Cocoa Powder - Premium Dark',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 20,
                'max_stock_level' => 200,
                'description' => 'Premium Dutch-processed cocoa powder',
            ],
            [
                'name' => 'Butter - Salted',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 30,
                'max_stock_level' => 300,
                'description' => 'Fresh salted butter for baking and cooking',
            ],
            [
                'name' => 'Eggs - Large Grade A',
                'category' => 'raw_material',
                'uom' => 'cartons',
                'reorder_level' => 10,
                'max_stock_level' => 50,
                'description' => 'Fresh large Grade A eggs (30 pieces per carton)',
            ],
            [
                'name' => 'Vanilla Extract - Pure',
                'category' => 'raw_material',
                'uom' => 'liters',
                'reorder_level' => 5,
                'max_stock_level' => 30,
                'description' => 'Pure vanilla extract for flavoring',
            ],
            [
                'name' => 'Chocolate Chips - Dark',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 15,
                'max_stock_level' => 150,
                'description' => '70% dark chocolate chips for baking',
            ],
            [
                'name' => 'Milk - Fresh Whole',
                'category' => 'raw_material',
                'uom' => 'liters',
                'reorder_level' => 20,
                'max_stock_level' => 100,
                'description' => 'Fresh whole milk, refrigerated',
            ],
            [
                'name' => 'Cream - Heavy Whipping',
                'category' => 'raw_material',
                'uom' => 'liters',
                'reorder_level' => 10,
                'max_stock_level' => 50,
                'description' => 'Heavy whipping cream, 35% fat content',
            ],
            [
                'name' => 'Yeast - Active Dry',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 5,
                'max_stock_level' => 25,
                'description' => 'Active dry yeast for bread making',
            ],
            [
                'name' => 'Vegetable Oil - Cooking',
                'category' => 'raw_material',
                'uom' => 'liters',
                'reorder_level' => 25,
                'max_stock_level' => 200,
                'description' => 'Refined vegetable oil for cooking and frying',
            ],
            [
                'name' => 'Salt - Table Salt',
                'category' => 'raw_material',
                'uom' => 'kg',
                'reorder_level' => 10,
                'max_stock_level' => 100,
                'description' => 'Iodized table salt for cooking and seasoning',
            ],

            // PACKAGING (4 items)
            [
                'name' => 'Cake Boxes - 10 inch',
                'category' => 'packaging',
                'uom' => 'pcs',
                'reorder_level' => 100,
                'max_stock_level' => 1000,
                'description' => 'White cardboard boxes for 10-inch cakes',
            ],
            [
                'name' => 'Pastry Boxes - Small',
                'category' => 'packaging',
                'uom' => 'pcs',
                'reorder_level' => 200,
                'max_stock_level' => 2000,
                'description' => 'Small boxes for pastries and individual servings',
            ],
            [
                'name' => 'Paper Bags - Brown',
                'category' => 'packaging',
                'uom' => 'pcs',
                'reorder_level' => 500,
                'max_stock_level' => 5000,
                'description' => 'Eco-friendly brown paper bags',
            ],
            [
                'name' => 'Plastic Food Containers',
                'category' => 'packaging',
                'uom' => 'pcs',
                'reorder_level' => 150,
                'max_stock_level' => 1500,
                'description' => 'Clear plastic containers with lids for takeaway',
            ],

            // CONSUMABLES (3 items)
            [
                'name' => 'Dishwashing Liquid - Industrial',
                'category' => 'consumable',
                'uom' => 'liters',
                'reorder_level' => 20,
                'max_stock_level' => 100,
                'description' => 'Heavy-duty dishwashing liquid for commercial use',
            ],
            [
                'name' => 'Paper Towels - Kitchen Roll',
                'category' => 'consumable',
                'uom' => 'units',
                'reorder_level' => 50,
                'max_stock_level' => 200,
                'description' => 'Absorbent paper towels for kitchen use',
            ],
            [
                'name' => 'Garbage Bags - Large',
                'category' => 'consumable',
                'uom' => 'pcs',
                'reorder_level' => 100,
                'max_stock_level' => 500,
                'description' => 'Heavy-duty large garbage bags',
            ],

            // EQUIPMENT (1 item)
            [
                'name' => 'Mixing Bowls - Stainless Steel',
                'category' => 'equipment',
                'uom' => 'pcs',
                'reorder_level' => 5,
                'max_stock_level' => 30,
                'description' => 'Professional stainless steel mixing bowls, various sizes',
            ],
        ];

        $items = [];
        $skuCounter = 1;

        foreach ($itemsData as $itemData) {
            // Create unique SKU per branch
            $sku = sprintf('%s-ITM-%05d', $branchCode, $skuCounter);

            $items[] = Item::create([
                'branch_id' => $branch->id,
                'name' => $itemData['name'],
                'sku' => $sku,
                'category' => $itemData['category'],
                'uom' => $itemData['uom'],
                'description' => $itemData['description'] ?? null,
                'reorder_level' => $itemData['reorder_level'],
                'max_stock_level' => $itemData['max_stock_level'],
                'status' => 'active',
            ]);

            $skuCounter++;
        }

        return $items;
    }

    /**
     * Create stock records for items
     */
    private function createStocksForItems(array $items, Branch $branch): array
    {
        $stocks = [];

        foreach ($items as $item) {
            // Generate realistic stock quantities based on category
            $quantityAvailable = match ($item->category) {
                'raw_material' => rand(50, 400),
                'packaging' => rand(100, 800),
                'consumable' => rand(20, 150),
                'equipment' => rand(5, 25),
                default => rand(50, 200)
            };

            $quantityReserved = rand(0, (int) ($quantityAvailable * 0.1)); // Max 10% reserved
            $quantityDamaged = rand(0, (int) ($quantityAvailable * 0.05)); // Max 5% damaged

            // Calculate average cost based on category
            $averageCost = match ($item->category) {
                'raw_material' => rand(500, 5000) / 10,
                'packaging' => rand(50, 500) / 10,
                'consumable' => rand(300, 3000) / 10,
                'equipment' => rand(2000, 20000) / 10,
                default => rand(500, 5000) / 10
            };

            // Determine health status
            $healthStatuses = ['good', 'good', 'good', 'warning', 'critical'];
            $healthStatus = $healthStatuses[array_rand($healthStatuses)];

            // Set expiry date for perishable items
            $expiryDate = null;
            if (in_array($item->category, ['raw_material', 'consumable'])) {
                $daysToExpiry = match ($healthStatus) {
                    'good' => rand(90, 365),
                    'warning' => rand(30, 89),
                    'critical' => rand(7, 29),
                    default => rand(60, 180)
                };
                $expiryDate = now()->addDays($daysToExpiry);
            }

            $stocks[] = Stock::create([
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
        }

        return $stocks;
    }
}
