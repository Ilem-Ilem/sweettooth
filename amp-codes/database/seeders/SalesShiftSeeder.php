<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\SalesShift;
use Illuminate\Database\Seeder;

class SalesShiftSeeder extends Seeder
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

        foreach ($branches as $branch) {
            $baseDate = now()->subDays(30);

            for ($i = 0; $i < 30; $i++) {
                $shiftDate = $baseDate->copy()->addDays($i);

                // Morning shift
                SalesShift::create([
                    'branch_id' => $branch->id,
                    'shift_date' => $shiftDate,
                    'shift_type' => 'morning',
                    'opening_cash' => 500.00,
                    'closing_cash' => 0,
                    'total_sales' => 0,
                    'opening_time' => $shiftDate->copy()->setHour(8)->setMinute(0),
                    'closing_time' => $shiftDate->copy()->setHour(14)->setMinute(0),
                    'status' => 'closed',
                    'notes' => 'Morning shift for ' . $shiftDate->format('Y-m-d'),
                ]);

                // Afternoon shift
                SalesShift::create([
                    'branch_id' => $branch->id,
                    'shift_date' => $shiftDate,
                    'shift_type' => 'afternoon',
                    'opening_cash' => 600.00,
                    'closing_cash' => 0,
                    'total_sales' => 0,
                    'opening_time' => $shiftDate->copy()->setHour(14)->setMinute(0),
                    'closing_time' => $shiftDate->copy()->setHour(20)->setMinute(0),
                    'status' => 'closed',
                    'notes' => 'Afternoon shift for ' . $shiftDate->format('Y-m-d'),
                ]);

                // Evening shift
                SalesShift::create([
                    'branch_id' => $branch->id,
                    'shift_date' => $shiftDate,
                    'shift_type' => 'night',
                    'opening_cash' => 400.00,
                    'closing_cash' => 0,
                    'total_sales' => 0,
                    'opening_time' => $shiftDate->copy()->setHour(20)->setMinute(0),
                    'closing_time' => $shiftDate->copy()->addDay()->setHour(8)->setMinute(0),
                    'status' => 'closed',
                    'notes' => 'Evening shift for ' . $shiftDate->format('Y-m-d'),
                ]);
            }
        }
    }
}
