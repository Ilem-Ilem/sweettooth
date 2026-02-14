<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ShiftConfiguration;
use Illuminate\Database\Seeder;

class ShiftConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all branches
        $branches = Branch::all();

        foreach ($branches as $branch) {
            // Morning Shift
            ShiftConfiguration::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'morning',
                ],
                [
                    'name' => 'Morning Shift',
                    'start_time' => '06:00:00',
                    'end_time' => '14:00:00',
                    'clock_in_start' => '05:30:00',
                    'clock_in_end' => '06:30:00',
                    'auto_clock_out_minutes' => 15,
                    'max_overtime_hours' => 2.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'Africa/Lagos',
                    'is_active' => true,
                ]
            );

            // Afternoon Shift
            ShiftConfiguration::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'afternoon',
                ],
                [
                    'name' => 'Afternoon Shift',
                    'start_time' => '14:00:00',
                    'end_time' => '22:00:00',
                    'clock_in_start' => '13:30:00',
                    'clock_in_end' => '14:30:00',
                    'auto_clock_out_minutes' => 15,
                    'max_overtime_hours' => 2.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'Africa/Lagos',
                    'is_active' => true,
                ]
            );

            // Night Shift
            ShiftConfiguration::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'night',
                ],
                [
                    'name' => 'Night Shift',
                    'start_time' => '22:00:00',
                    'end_time' => '06:00:00',
                    'clock_in_start' => '21:30:00',
                    'clock_in_end' => '22:30:00',
                    'auto_clock_out_minutes' => 15,
                    'max_overtime_hours' => 2.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'Africa/Lagos',
                    'is_active' => true,
                ]
            );

            // Full Time Shift
            ShiftConfiguration::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'shift_type' => 'full_time',
                ],
                [
                    'name' => 'Full Time Shift',
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                    'clock_in_start' => '07:30:00',
                    'clock_in_end' => '08:30:00',
                    'auto_clock_out_minutes' => 480, // 8 hours
                    'max_overtime_hours' => 2.00,
                    'break_duration_minutes' => 60,
                    'timezone' => $branch->timezone ?? 'Africa/Lagos',
                    'is_active' => true,
                ]
            );
        }
    }
}