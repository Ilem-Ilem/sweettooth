<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $downBranch = Branch::where('code', 'DB002')->first();
        $airportBranch = Branch::where('code', 'AB003')->first();

        $branches = [$mainBranch, $downBranch, $airportBranch];
        $baseDate = now()->subDays(30);

        foreach ($branches as $branch) {
            $salesShifts = SalesShift::where('branch_id', $branch->id)
                ->where('status', 'closed')
                ->get();
            
            $products = Product::where('branch_id', $branch->id)->get();
            $salesDept = Department::where('name', 'Sales Counter')
                ->where('branch_id', $branch->id)
                ->first();

            foreach ($salesShifts as $shift) {
                // Create 5-15 sales per shift
                for ($i = 0; $i < rand(5, 15); $i++) {
                    $sale = Sale::create([
                        'id' => (string) Str::uuid(),
                        'branch_id' => $branch->id,
                        'sales_shift_id' => $shift->id,
                        'department_id' => $salesDept->id,
                        'sale_number' => 'SALE-' . $shift->id . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                        'customer_name' => 'Customer ' . rand(1, 1000),
                        'total_amount' => 0,
                        'tax_amount' => 0,
                        'service_charge' => 0,
                        'discount_amount' => 0,
                        'net_amount' => 0,
                        'payment_method' => ['cash', 'card', 'digital_wallet'][array_rand(['cash', 'card', 'digital_wallet'])],
                        'status' => 'completed',
                        'notes' => 'Sale from ' . $shift->shift_type . ' shift',
                    ]);

                    // Add 2-5 items to each sale
                    $itemCount = rand(2, 5);
                    $selectedProducts = $products->random($itemCount);
                    $totalAmount = 0;

                    foreach ($selectedProducts as $product) {
                        $quantity = rand(1, 3);
                        $unitPrice = $product->price;
                        $amount = $quantity * $unitPrice;
                        $totalAmount += $amount;

                        SaleItem::create([
                            'id' => (string) Str::uuid(),
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'department_id' => $salesDept->id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'amount' => $amount,
                            'notes' => 'Sold ' . $quantity . ' unit(s) of ' . $product->name,
                        ]);
                    }

                    // Calculate totals
                    $tax = round($totalAmount * 0.10, 2);
                    $serviceCharge = round($totalAmount * 0.05, 2);
                    $discount = rand(0, 1) ? round($totalAmount * 0.02, 2) : 0;
                    $netAmount = $totalAmount + $tax + $serviceCharge - $discount;

                    $sale->update([
                        'total_amount' => $totalAmount,
                        'tax_amount' => $tax,
                        'service_charge' => $serviceCharge,
                        'discount_amount' => $discount,
                        'net_amount' => $netAmount,
                    ]);

                    // Update shift totals
                    $shift->total_sales += $netAmount;
                    $shift->save();
                }
            }
        }
    }
}
