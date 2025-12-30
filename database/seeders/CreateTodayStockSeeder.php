<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CreateTodayStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the Till department
        $till = Department::where('name', 'Till')->first();
        
        if (!$till) {
            $this->command->warn('Till department not found!');
            return;
        }

        // Get all products that are mapped to Till
        $products = Product::whereHas('departments', function ($q) use ($till) {
            $q->where('department_id', $till->id);
        })->active()->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found mapped to Till department!');
            return;
        }

        $today = Carbon::today();
        $created = 0;
        $skipped = 0;

        foreach ($products as $product) {
            // Check if stock already exists for today
            $existing = ProductStock::where('product_id', $product->id)
                ->whereDate('stock_date', $today)
                ->exists();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Create stock record for today with arbitrary quantities
            ProductStock::create([
                'product_id' => $product->id,
                'stock_date' => $today,
                'opening_quantity' => 15,  // Default quantity for testing
                'addition_quantity' => 0,
                'callback_quantity' => 0,
                'redress_quantity' => 0,
                'transfer_quantity' => 0,
                'glovo_quantity' => 0,
                'quantity_sold' => 0,
                'closing_quantity' => 15,  // opening + additions - deductions
            ]);

            $created++;
        }

        $this->command->info("Created $created stock records for today ($today)");
        if ($skipped > 0) {
            $this->command->info("Skipped $skipped products (stock already exists for today)");
        }
    }
}
