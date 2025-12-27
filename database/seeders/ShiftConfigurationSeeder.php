<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShiftConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = \App\Models\Branch::all();

        foreach ($branches as $branch) {
            $configurations = [
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'morning',
                    'name' => 'Morning Shift',
                    'start_time' => '06:00:00',
                    'end_time' => '14:00:00',
                    'clock_in_start' => '06:00:00', // STRICT: 6 AM
                    'clock_in_end' => '12:00:00',   // STRICT: 12 PM
                    'auto_clock_out_minutes' => 15,
                    'max_overtime_hours' => 2.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'UTC',
                    'is_active' => true,
                ],
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'afternoon',
                    'name' => 'Afternoon Shift',
                    'start_time' => '12:00:00',
                    'end_time' => '20:00:00',
                    'clock_in_start' => '12:00:00', // STRICT: 12 PM
                    'clock_in_end' => '20:00:00',   // STRICT: 8 PM
                    'auto_clock_out_minutes' => 15,
                    'max_overtime_hours' => 2.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'UTC',
                    'is_active' => true,
                ],
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'full_time',
                    'name' => 'Full Time',
                    'start_time' => '00:00:00',
                    'end_time' => '23:59:59',
                    'clock_in_start' => '00:00:00',
                    'clock_in_end' => '23:59:59',
                    'auto_clock_out_minutes' => 480, // 8 hours for full time
                    'max_overtime_hours' => 4.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'UTC',
                    'is_active' => true,
                ],
            ];

            \App\Models\ShiftConfiguration::insert($configurations);
        }
    }
}
