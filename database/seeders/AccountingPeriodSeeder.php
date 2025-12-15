<?php

namespace Database\Seeders;

use App\Models\AccountingPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AccountingPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing periods
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        AccountingPeriod::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Create periods for current year and some future months
        for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                // Skip months before last year
                if ($year === $currentYear - 1 && $month < 1) {
                    continue;
                }

                $startDate = Carbon::createFromDate($year, $month, 1);
                $endDate = $startDate->clone()->endOfMonth();

                // Determine status
                $today = Carbon::now();
                if ($endDate < $today) {
                    $status = 'locked'; // Past periods are locked
                } elseif ($startDate <= $today && $endDate >= $today) {
                    $status = 'open'; // Current month is open
                } else {
                    $status = 'open'; // Future months are open
                }

                AccountingPeriod::create([
                    'year' => $year,
                    'month' => $month,
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'status' => $status,
                ]);
            }
        }

        $this->command->info('Accounting periods created successfully!');
    }
}
