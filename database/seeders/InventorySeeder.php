<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HealthCheck;
use App\Models\Item;
use App\Models\ItemDispatch;
use App\Models\ItemRequest;
use App\Models\ItemRequestDetail;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockTake;
use App\Models\StockTakeDetail;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first branch, employee, and department for relationships
        $branch = Branch::first();
        $employee = Employee::first();
        $department = Department::first();

        if (!$branch || !$employee || !$department) {
            $this->command->error('Please ensure you have at least one branch, employee, and department in the database.');
            return;
        }

        $this->command->info('Starting inventory seeding...');

        // Create 25 diverse items
        $items = $this->createItems($branch);
        $this->command->info('Created ' . count($items) . ' items');

        // Create stocks for all items
        $stocks = $this->createStocks($items, $branch);
        $this->command->info('Created ' . count($stocks) . ' stock records');

        // Create purchases
        $purchases = $this->createPurchases($items, $branch, $employee);
        $this->command->info('Created ' . count($purchases) . ' purchases');

        // Create stock movements
        $stockMovements = $this->createStockMovements($stocks, $employee);
        $this->command->info('Created ' . count($stockMovements) . ' stock movements');

        // Create item requests
        $itemRequests = $this->createItemRequests($items, $branch, $department, $employee);
        $this->command->info('Created ' . count($itemRequests) . ' item requests');

        // Create item dispatches
        $itemDispatches = $this->createItemDispatches($itemRequests, $items, $employee);
        $this->command->info('Created ' . count($itemDispatches) . ' item dispatches');

        // Create stock takes
        $stockTakes = $this->createStockTakes($items, $branch, $employee);
        $this->command->info('Created ' . count($stockTakes) . ' stock takes');

        // Create health checks
        $healthChecks = $this->createHealthChecks($stocks, $employee);
        $this->command->info('Created ' . count($healthChecks) . ' health checks');

        $this->command->info('Inventory seeding completed successfully!');
    }

    /**
     * Create diverse inventory items
     */
    private function createItems($branch): array
    {
        $itemsData = [
            // Kitchen supplies
            ['name' => 'Sugar - White Granulated', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 50, 'max_stock_level' => 500],
            ['name' => 'Flour - All Purpose', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Cocoa Powder - Premium', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 20, 'max_stock_level' => 200],
            ['name' => 'Butter - Salted', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 30, 'max_stock_level' => 300],
            ['name' => 'Eggs - Large Grade A', 'category' => 'raw_material', 'uom' => 'cartons', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Vanilla Extract - Pure', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 5, 'max_stock_level' => 30],
            ['name' => 'Chocolate Chips - Dark', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 15, 'max_stock_level' => 150],
            ['name' => 'Milk - Fresh Whole', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 20, 'max_stock_level' => 100],
            ['name' => 'Cream - Heavy', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Yeast - Active Dry', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 5, 'max_stock_level' => 25],

            // Packaging materials
            ['name' => 'Cake Boxes - 10 inch', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Pastry Boxes - Small', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 200, 'max_stock_level' => 2000],
            ['name' => 'Paper Bags - Brown', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 500, 'max_stock_level' => 5000],
            ['name' => 'Plastic Food Containers', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 150, 'max_stock_level' => 1500],
            ['name' => 'Aluminum Foil - Heavy Duty', 'category' => 'packaging', 'uom' => 'units', 'reorder_level' => 10, 'max_stock_level' => 50],

            // Cleaning supplies
            ['name' => 'Dishwashing Liquid - Industrial', 'category' => 'consumable', 'uom' => 'liters', 'reorder_level' => 20, 'max_stock_level' => 100],
            ['name' => 'Floor Cleaner - Disinfectant', 'category' => 'consumable', 'uom' => 'liters', 'reorder_level' => 15, 'max_stock_level' => 75],
            ['name' => 'Hand Soap - Antibacterial', 'category' => 'consumable', 'uom' => 'liters', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Paper Towels - Kitchen Roll', 'category' => 'consumable', 'uom' => 'units', 'reorder_level' => 50, 'max_stock_level' => 200],
            ['name' => 'Garbage Bags - Large', 'category' => 'consumable', 'uom' => 'pcs', 'reorder_level' => 100, 'max_stock_level' => 500],

            // Equipment and tools
            ['name' => 'Mixing Bowls - Stainless Steel', 'category' => 'equipment', 'uom' => 'pcs', 'reorder_level' => 5, 'max_stock_level' => 30],
            ['name' => 'Baking Trays - Professional', 'category' => 'equipment', 'uom' => 'pcs', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Measuring Cups Set', 'category' => 'equipment', 'uom' => 'units', 'reorder_level' => 5, 'max_stock_level' => 20],
            ['name' => 'Piping Bags - Disposable', 'category' => 'consumable', 'uom' => 'pcs', 'reorder_level' => 100, 'max_stock_level' => 500],
            ['name' => 'Chef Knives - Professional', 'category' => 'equipment', 'uom' => 'pcs', 'reorder_level' => 5, 'max_stock_level' => 25],
        ];

        $items = [];
        foreach ($itemsData as $index => $itemData) {
            $items[] = Item::create([
                'branch_id' => $branch->id,
                'name' => $itemData['name'],
                'sku' => 'SKU-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'category' => $itemData['category'],
                'uom' => $itemData['uom'],
                'reorder_level' => $itemData['reorder_level'],
                'max_stock_level' => $itemData['max_stock_level'],
                'status' => 'active',
            ]);
        }

        return $items;
    }

    /**
     * Create stock records for items
     */
    private function createStocks($items, $branch): array
    {
        $stocks = [];
        foreach ($items as $item) {
            $quantityAvailable = rand(10, 500);
            $quantityReserved = rand(0, 50);
            $quantityDamaged = rand(0, 10);
            $averageCost = rand(100, 5000) / 10;

            $stocks[] = Stock::create([
                'branch_id' => $branch->id,
                'item_id' => $item->id,
                'quantity_available' => $quantityAvailable,
                'quantity_reserved' => $quantityReserved,
                'quantity_damaged' => $quantityDamaged,
                'average_cost' => $averageCost,
                'last_stock_take_date' => now()->subDays(rand(1, 30)),
                'health_status' => collect(['good', 'warning', 'critical', 'expired'])->random(),
                'expiry_date' => in_array($item->category, ['raw_material', 'consumable']) ? now()->addDays(rand(30, 180)) : null,
            ]);
        }

        return $stocks;
    }

    /**
     * Create purchase records
     */
    private function createPurchases($items, $branch, $employee): array
    {
        $purchases = [];
        $branchCode = 'BR001';

        for ($i = 0; $i < 8; $i++) {
            $purchaseDate = now()->subDays(rand(1, 60));
            $currency = collect(['USD', 'EUR', 'GBP', 'NGN'])->random();
            $exchangeRate = $currency === 'NGN' ? 1 : rand(800, 1600);

            $purchase = Purchase::create([
                'branch_id' => $branch->id,
                'recorded_by' => $employee->id,
                'purchase_number' => 'PUR-' . $branchCode . '-' . $purchaseDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'purchase_date' => $purchaseDate,
                'supplier_name' => collect(['Global Supplies Ltd', 'Premium Ingredients Co', 'Quality Foods Inc', 'Industrial Distributors', 'Fresh Market Suppliers'])->random(),
                'supplier_contact' => '+234' . rand(8000000000, 8999999999),
                'total_fob_fc' => $fobFc = rand(5000, 50000) / 10,
                'total_fob_ngn' => $fobFc * $exchangeRate,
                'other_costs' => $otherCosts = rand(1000, 10000) / 10,
                'landing_cost' => ($fobFc * $exchangeRate) + $otherCosts,
                'currency' => $currency,
                'exchange_rate' => $exchangeRate,
                'payment_status' => collect(['pending', 'partial', 'paid'])->random(),
                'notes' => 'Purchase order #' . ($i + 1),
            ]);

            // Create purchase items
            $selectedItems = collect($items)->random(rand(3, 8));
            foreach ($selectedItems as $item) {
                $quantity = rand(10, 200);
                $fobFcUnit = rand(50, 500) / 10;
                $otherCostsItem = rand(10, 100) / 10;
                $fobFcTotal = $quantity * $fobFcUnit;
                $fobNgnTotal = $fobFcTotal * $exchangeRate;
                $landingCostTotal = $fobNgnTotal + $otherCostsItem;
                $totalCost = $landingCostTotal;
                $costPerUnit = $totalCost / $quantity;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'uom' => $item->uom,
                    'fob_fc' => $fobFcTotal,
                    'fob_ngn' => $fobNgnTotal,
                    'other_costs' => $otherCostsItem,
                    'landing_cost' => $landingCostTotal,
                    'total_cost' => $totalCost,
                    'cost_per_unit' => $costPerUnit,
                ]);
            }

            $purchases[] = $purchase;
        }

        return $purchases;
    }

    /**
     * Create stock movements
     */
    private function createStockMovements($stocks, $employee): array
    {
        $movements = [];
        $movementTypes = ['in', 'out', 'damaged', 'return', 'transfer'];

        foreach (collect($stocks)->random(min(30, count($stocks))) as $stock) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                $type = collect($movementTypes)->random();
                $quantity = rand(5, 50);
                $quantityBefore = rand(100, 500);

                $quantityAfter = in_array($type, ['in', 'return'])
                    ? $quantityBefore + $quantity
                    : $quantityBefore - $quantity;

                $movements[] = StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => $type,
                    'quantity' => $quantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reference_type' => null,
                    'reference_id' => null,
                    'moved_by' => $employee->id,
                    'movement_date' => now()->subDays(rand(1, 45)),
                    'notes' => 'Stock ' . $type . ' movement',
                ]);
            }
        }

        return $movements;
    }

    /**
     * Create item requests
     */
    private function createItemRequests($items, $branch, $department, $employee): array
    {
        $requests = [];
        $statuses = ['pending', 'approved', 'partially_dispatched', 'completed', 'cancelled'];
        $branchCode = 'BR001';
        $departmentCode = 'DP001';
        $shifts = ['morning', 'afternoon'];

        for ($i = 0; $i < 12; $i++) {
            $requestDate = now()->subDays(rand(1, 40));
            $status = collect($statuses)->random();

            $request = ItemRequest::create([
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'request_number' => 'REQ-' . $branchCode . '-' . $departmentCode . '-' . $requestDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'requested_by' => $employee->id,
                'request_date' => $requestDate,
                'shift' => collect($shifts)->random(),
                'status' => $status,
                'approved_by' => in_array($status, ['approved', 'partially_dispatched', 'completed']) ? $employee->id : null,
                'approved_at' => in_array($status, ['approved', 'partially_dispatched', 'completed']) ? $requestDate->copy()->addHours(rand(2, 48)) : null,
                'notes' => 'Request for ' . $department->name ?? 'department',
            ]);

            // Create request details
            $selectedItems = collect($items)->random(rand(2, 6));
            foreach ($selectedItems as $item) {
                $quantityRequested = rand(5, 100);
                $quantityApproved = in_array($status, ['approved', 'partially_dispatched', 'completed'])
                    ? $quantityRequested
                    : 0;
                $quantityDispatched = $status === 'completed'
                    ? $quantityApproved
                    : ($status === 'partially_dispatched' ? rand(1, $quantityApproved) : 0);

                ItemRequestDetail::create([
                    'request_id' => $request->id,
                    'item_id' => $item->id,
                    'quantity_requested' => $quantityRequested,
                    'quantity_approved' => $quantityApproved,
                    'quantity_dispatched' => $quantityDispatched,
                    'uom' => $item->uom,
                    'notes' => 'Request for ' . $item->name,
                ]);
            }

            $requests[] = $request;
        }

        return $requests;
    }

    /**
     * Create item dispatches
     */
    private function createItemDispatches($itemRequests, $items, $employee): array
    {
        $dispatches = [];
        $shifts = ['morning', 'afternoon'];

        foreach ($itemRequests as $request) {
            if (in_array($request->status, ['partially_dispatched', 'completed'])) {
                $requestDetails = ItemRequestDetail::where('request_id', $request->id)
                    ->where('quantity_dispatched', '>', 0)
                    ->get();

                foreach ($requestDetails as $detail) {
                    $dispatchTime = $request->approved_at->copy()->addHours(rand(1, 24));
                    $receivedTime = rand(0, 1) ? $dispatchTime->copy()->addHours(rand(1, 6)) : null;

                    $dispatches[] = ItemDispatch::create([
                        'request_id' => $request->id,
                        'item_id' => $detail->item_id,
                        'dispatched_by' => $employee->id,
                        'branch_id'=>$request->branch_id,
                        'received_by' => $employee->id,
                        'quantity' => $detail->quantity_dispatched,
                        'uom' => $detail->uom,
                        'dispatch_time' => $dispatchTime,
                        'received_time' => $receivedTime,
                        'shift' => collect($shifts)->random(),
                        'notes' => 'Dispatch for request ' . $request->request_number,
                    ]);
                }
            }
        }

        return $dispatches;
    }

    /**
     * Create stock takes
     */
    private function createStockTakes($items, $branch, $employee): array
    {
        $stockTakes = [];
        $branchCode = 'BR001';
        $types = ['daily', 'weekly', 'monthly', 'annual', 'ad_hoc'];
        $statuses = ['in_progress', 'completed', 'verified'];

        for ($i = 0; $i < 5; $i++) {
            $stockTakeDate = now()->subDays(rand(1, 50));
            $status = collect($statuses)->random();

            $stockTake = StockTake::create([
                'branch_id' => $branch->id,
                'stock_take_number' => 'ST-' . $branchCode . '-' . $stockTakeDate->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'stock_take_date' => $stockTakeDate,
                'type' => collect($types)->random(),
                'conducted_by' => $employee->id,
                'status' => $status,
                'verified_by' => $status === 'verified' ? $employee->id : null,
                'verified_at' => $status === 'verified' ? $stockTakeDate->copy()->addDays(rand(1, 3)) : null,
                'notes' => 'Stock take #' . ($i + 1),
            ]);

            // Create stock take details
            $selectedItems = collect($items)->random(rand(5, 15));
            foreach ($selectedItems as $item) {
                $systemQuantity = rand(50, 500);
                $physicalQuantity = $systemQuantity + rand(-20, 20);
                $variance = $physicalQuantity - $systemQuantity;

                $varianceType = $variance > 0 ? 'surplus' : ($variance < 0 ? 'shortage' : 'match');

                StockTakeDetail::create([
                    'stock_take_id' => $stockTake->id,
                    'item_id' => $item->id,
                    'system_quantity' => $systemQuantity,
                    'physical_quantity' => $physicalQuantity,
                    'variance' => abs($variance),
                    'variance_type' => $varianceType,
                    'notes' => $varianceType !== 'match' ? 'Variance found: ' . $variance : 'Quantities match',
                ]);
            }

            $stockTakes[] = $stockTake;
        }

        return $stockTakes;
    }

    /**
     * Create health checks
     */
    private function createHealthChecks($stocks, $employee): array
    {
        $healthChecks = [];
        $conditions = ['excellent', 'good', 'fair', 'poor', 'damaged', 'expired'];
        $actions = [
            'excellent' => 'No action required',
            'good' => 'Continue monitoring',
            'fair' => 'Monitor closely, use soon',
            'poor' => 'Marked for quick disposal',
            'damaged' => 'Removed from inventory',
            'expired' => 'Disposed as per protocol',
        ];

        foreach (collect($stocks)->random(min(40, count($stocks))) as $stock) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $condition = collect($conditions)->random();
                $quantityAffected = $condition === 'excellent' ? 0 : rand(1, 20);

                $healthChecks[] = HealthCheck::create([
                    'stock_id' => $stock->id,
                    'checked_by' => $employee->id,
                    'check_date' => now()->subDays(rand(1, 35)),
                    'condition' => $condition,
                    'quantity_affected' => $quantityAffected,
                    'observations' => 'Routine health check - condition: ' . $condition,
                    'action_taken' => $actions[$condition],
                ]);
            }
        }

        return $healthChecks;
    }
}
