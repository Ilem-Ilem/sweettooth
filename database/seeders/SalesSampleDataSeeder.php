<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SalesSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $branchId = current_branch_id() ?? \App\Models\Branch::query()->value('id');
        if (!$branchId) {
            $this->command->warn('No branch found. Aborting sales sample data seeding.');
            return;
        }

        $salesCategory = DepartmentCategory::where('name', 'Sales')->first();
        if (!$salesCategory) {
            $this->command->warn('Sales category not found. Aborting sales sample data seeding.');
            return;
        }

        $departments = Department::query()
            ->where('category_id', $salesCategory->id)
            ->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->get();

        if ($departments->isEmpty()) {
            $this->command->warn('No sales departments found. Aborting sales sample data seeding.');
            return;
        }

        $users = User::query()
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            $this->command->warn('No active users found for branch. Aborting sales sample data seeding.');
            return;
        }

        $faker = \Faker\Factory::create();
        $today = Carbon::today();
        $start = $today->copy()->subDays(13);

        $createdSales = 0;

        foreach ($departments as $department) {
            $products = Product::query()
                ->where('branch_id', $branchId)
                ->whereHas('departments', function ($q) use ($department) {
                    $q->where('department_id', $department->id);
                })
                ->get();

            if ($products->isEmpty()) {
                $products = Product::query()
                    ->where('branch_id', $branchId)
                    ->limit(20)
                    ->get();
            }

            if ($products->isEmpty()) {
                continue;
            }

            for ($date = $start->copy(); $date->lte($today); $date->addDay()) {
                $shift = SalesShift::create([
                    'branch_id' => $branchId,
                    'department_id' => $department->id,
                    'employee_id' => $users->random()->id,
                    'shift_number' => Str::upper(Str::random(6)),
                    'shift_date' => $date->toDateString(),
                    'shift_type' => 'morning',
                    'clock_in' => $date->copy()->setTime(9, 0),
                    'clock_out' => $date->copy()->setTime(18, 0),
                    'opening_cash' => 5000,
                    'closing_cash' => 5000,
                    'expected_cash' => 5000,
                    'cash_variance' => 0,
                    'status' => 'closed',
                ]);

                $salesCount = $faker->numberBetween(6, 14);

                for ($i = 0; $i < $salesCount; $i++) {
                    $seller = $users->random();
                    $saleTime = $date->copy()->setTime($faker->numberBetween(9, 20), $faker->numberBetween(0, 59));

                    $sale = Sale::create([
                        'sales_shift_id' => $shift->id,
                        'branch_id' => $branchId,
                        'department_id' => $department->id,
                        'sold_by_id' => $seller->id,
                        'sold_by_type' => get_class($seller),
                        'sale_number' => 'SL-' . $date->format('Ymd') . '-' . Str::upper(Str::random(6)),
                        'sale_time' => $saleTime,
                        'subtotal' => 0,
                        'tax' => 0,
                        'discount' => 0,
                        'total' => 0,
                        'status' => 'completed',
                        'order_type' => $faker->randomElement(['dine_in', 'takeaway', 'delivery']),
                        'notes' => null,
                    ]);

                    $itemsCount = $faker->numberBetween(1, 4);
                    $saleItems = $products->random($itemsCount);

                    foreach ($saleItems as $product) {
                        $quantity = $faker->numberBetween(1, 5);
                        $unitPrice = $product->price ?? $faker->randomFloat(2, 500, 4000);
                        $discount = $faker->randomFloat(2, 0, 200);

                        SaleItem::create([
                            'sale_id' => $sale->id,
                            'department_id' => $department->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                            'discount' => $discount,
                            'notes' => null,
                        ]);
                    }

                    $createdSales++;
                }
            }
        }

        $this->command->info("Seeded {$createdSales} sales across " . $departments->count() . ' sales departments.');
    }
}
