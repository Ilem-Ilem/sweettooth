<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $employees = Employee::where('branch_id', $mainBranch->id)->get();
        $departments = Department::where('branch_id', $mainBranch->id)->get();

        $shiftTypes = ['morning', 'afternoon', 'night'];
        $shiftNumber = 1;
        $baseDate = now()->subDays(30);

        foreach ($employees as $employee) {
            $dept = $departments->first();
            
            // Create 15 shifts per employee for the last 30 days
            for ($i = 0; $i < 15; $i++) {
                $shiftDate = $baseDate->copy()->addDays($i * 2);
                $shiftType = $shiftTypes[array_rand($shiftTypes)];
                
                $clockIn = $shiftDate->copy()->setHour(
                    $shiftType === 'morning' ? 6 : ($shiftType === 'afternoon' ? 14 : 22)
                );
                $clockOut = $clockIn->copy()->addHours(8);

                Shift::create([
                    'branch_id' => $mainBranch->id,
                    'department_id' => $dept->id,
                    'employee_id' => $employee->id,
                    'shift_number' => 'SHIFT-' . str_pad($shiftNumber++, 5, '0', STR_PAD_LEFT),
                    'shift_date' => $shiftDate,
                    'shift_type' => $shiftType,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'status' => $shiftDate->isPast() ? 'closed' : 'active',
                    'notes' => ucfirst($shiftType) . ' shift for ' . $employee->name,
                ]);
            }
        }
    }
}
