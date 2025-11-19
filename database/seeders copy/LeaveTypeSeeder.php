<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'ANNUAL',
                'description' => 'Yearly vacation leave for rest and recreation',
                'default_days_per_year' => 21,
                'requires_approval' => true,
                'requires_document' => false,
                'max_consecutive_days' => 14,
                'min_notice_days' => 7,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#3b82f6',
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SICK',
                'description' => 'Medical leave for illness or injury',
                'default_days_per_year' => 10,
                'requires_approval' => true,
                'requires_document' => true, // Medical certificate required
                'max_consecutive_days' => null,
                'min_notice_days' => 0, // Can be taken immediately
                'is_paid' => true,
                'is_active' => true,
                'color' => '#ef4444',
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EMERGENCY',
                'description' => 'Urgent personal or family emergencies',
                'default_days_per_year' => 5,
                'requires_approval' => true,
                'requires_document' => false,
                'max_consecutive_days' => 3,
                'min_notice_days' => 0,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#f59e0b',
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'MATERNITY',
                'description' => 'Leave for childbirth and post-natal care',
                'default_days_per_year' => 90,
                'requires_approval' => true,
                'requires_document' => true,
                'max_consecutive_days' => null,
                'min_notice_days' => 30,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#ec4899',
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PATERNITY',
                'description' => 'Leave for fathers following childbirth',
                'default_days_per_year' => 7,
                'requires_approval' => true,
                'requires_document' => true,
                'max_consecutive_days' => null,
                'min_notice_days' => 7,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#6366f1',
            ],
            [
                'name' => 'Bereavement Leave',
                'code' => 'BEREAVEMENT',
                'description' => 'Leave for death of a family member',
                'default_days_per_year' => 3,
                'requires_approval' => true,
                'requires_document' => false,
                'max_consecutive_days' => 3,
                'min_notice_days' => 0,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#6b7280',
            ],
            [
                'name' => 'Study Leave',
                'code' => 'STUDY',
                'description' => 'Leave for educational purposes or examinations',
                'default_days_per_year' => 5,
                'requires_approval' => true,
                'requires_document' => true,
                'max_consecutive_days' => 5,
                'min_notice_days' => 14,
                'is_paid' => false,
                'is_active' => true,
                'color' => '#8b5cf6',
            ],
            [
                'name' => 'Unpaid Leave',
                'code' => 'UNPAID',
                'description' => 'Additional leave without pay',
                'default_days_per_year' => 0,
                'requires_approval' => true,
                'requires_document' => false,
                'max_consecutive_days' => null,
                'min_notice_days' => 14,
                'is_paid' => false,
                'is_active' => true,
                'color' => '#64748b',
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::updateOrCreate(
                ['code' => $leaveType['code']],
                $leaveType
            );
        }

        $this->command->info('Leave types seeded successfully!');
    }
}
