<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\SalesShift;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->error('No products found! Please seed products first.');
            return;
        }

        // Get the first branch (or create a default one if needed)
        $branch = Branch::first();
        if (!$branch) {
            $this->command->error('No branch found! Please seed branches first.');
            return;
        }

        // Get the Till department for sales operations
        $tillDepartment = \App\Models\Department::where('name', 'Till')->first();
        if (!$tillDepartment) {
            $this->command->error('Till department not found! Please seed departments first.');
            return;
        }

        // Get the first user to assign as employee
        $user = \App\Models\User::first();
        if (!$user) {
            $this->command->error('No users found! Please seed users first.');
            return;
        }

        // Create a sales shift for today if it doesn't exist
        $salesShift = SalesShift::firstOrCreate([
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'branch_id' => $branch->id,
            'department_id' => $tillDepartment->id,
        ], [
            'shift_number' => 'SHFT-' . Carbon::today()->format('Ymd') . '-001',
            'status' => 'active',
            'employee_id' => $user->id,
        ]);

        $created = 0;
        $updated = 0;

        foreach ($products as $product) {
            // Check if stock already exists for this product today
            $productStock = ProductStock::firstOrCreate([
                'product_id' => $product->id,
                'stock_date' => Carbon::today(),
                'sales_shift_id' => $salesShift->id,
            ], [
                'shift_type' => 'morning',
                'opening_quantity' => $this->getDefaultOpeningQuantity($product),
                'addition_quantity' => 0,
                'production_date' => Carbon::today(),
                'expiry_date' => Carbon::today()->addDays($product->shelf_life_days ?? 7),
                'callback_quantity' => 0,
                'redress_quantity' => 0,
                'total_available' => $this->getDefaultOpeningQuantity($product),
                'transfer_quantity' => 0,
                'glovo_quantity' => 0,
                'quantity_sold' => 0,
                'closing_quantity' => $this->getDefaultOpeningQuantity($product),
                'amount' => 0,
                'notes' => 'Initial stock for POS testing',
            ]);

            if ($productStock->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("Created $created new product stock records and updated $updated existing records for POS testing.");

        // Also create some historical stock data for the past few days
        $this->createHistoricalStockData($products, $salesShift, $branch, $tillDepartment, $user);
    }

    /**
     * Get default opening quantity based on product type
     */
    private function getDefaultOpeningQuantity($product): float
    {
        // Different products have different typical stock levels
        $productName = strtolower($product->name);

        if (str_contains($productName, 'croissant') || str_contains($productName, 'danish')) {
            return 50.0; // Pastries typically have higher stock
        } elseif (str_contains($productName, 'bread')) {
            return 20.0; // Breads have moderate stock
        } elseif (str_contains($productName, 'cake')) {
            return 10.0; // Cakes have lower stock (made fresh)
        } elseif (str_contains($productName, 'cookie')) {
            return 100.0; // Cookies have high stock (longer shelf life)
        } elseif (str_contains($productName, 'gelato')) {
            return 500.0; // Gelato measured in grams, higher quantities
        } elseif (str_contains($productName, 'chocolate') || str_contains($productName, 'candy')) {
            return 200.0; // Confectionery has higher stock
        } else {
            return 30.0; // Default stock level
        }
    }

    /**
     * Create historical stock data for the past few days
     */
    private function createHistoricalStockData($products, $salesShift, $branch, $tillDepartment, $user): void
    {
        $daysBack = 5; // Create data for the past 5 days

        for ($i = 1; $i <= $daysBack; $i++) {
            $date = Carbon::today()->subDays($i);

            // Create a sales shift for this historical date if it doesn't exist
            $historicalShift = SalesShift::firstOrCreate([
                'shift_date' => $date,
                'shift_type' => 'morning', // Use 'morning' which is a valid enum value
                'branch_id' => $branch->id,
                'department_id' => $tillDepartment->id,
            ], [
                'shift_number' => 'SHFT-' . $date->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'status' => 'closed',
                'employee_id' => $user->id,
            ]);

            foreach ($products as $product) {
                // Skip if already exists
                $existing = ProductStock::where('product_id', $product->id)
                    ->whereDate('stock_date', $date)
                    ->exists();

                if ($existing) {
                    continue;
                }

                // Generate random sales data for historical dates
                $openingQty = $this->getDefaultOpeningQuantity($product);
                $soldQty = min(rand(5, (int)($openingQty * 0.8)), $openingQty); // Sell 5-80% of opening stock

                ProductStock::create([
                    'product_id' => $product->id,
                    'stock_date' => $date,
                    'sales_shift_id' => $historicalShift->id,
                    'shift_type' => 'morning',
                    'opening_quantity' => $openingQty,
                    'addition_quantity' => 0,
                    'production_date' => $date->copy()->subDays(1), // Produced yesterday
                    'expiry_date' => $date->copy()->addDays($product->shelf_life_days ?? 7),
                    'callback_quantity' => rand(0, 2), // Random callbacks
                    'redress_quantity' => rand(0, 1), // Random redress
                    'total_available' => $openingQty,
                    'transfer_quantity' => 0,
                    'glovo_quantity' => rand(0, 5), // Random glovo orders
                    'quantity_sold' => $soldQty,
                    'closing_quantity' => $openingQty - $soldQty - rand(0, 2), // Remaining stock
                    'amount' => $soldQty * $product->price, // Revenue from sales
                    'notes' => "Historical stock data for POS testing",
                ]);
            }
        }

        $this->command->info("Created historical stock data for the past $daysBack days.");
    }
}