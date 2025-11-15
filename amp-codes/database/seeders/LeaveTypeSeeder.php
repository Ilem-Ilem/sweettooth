<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LeaveType::create([
            'name' => 'Annual Leave',
            'code' => 'ANNUAL',
            'description' => 'Paid annual leave entitlement',
            'default_days_per_year' => 20,
            'requires_approval' => true,
            'requires_document' => false,
            'max_consecutive_days' => 10,
            'min_notice_days' => 5,
            'is_paid' => true,
            'is_active' => true,
            'color' => '#3b82f6',
        ]);

        LeaveType::create([
            'name' => 'Sick Leave',
            'code' => 'SICK',
            'description' => 'Paid sick leave',
            'default_days_per_year' => 10,
            'requires_approval' => true,
            'requires_document' => true,
            'max_consecutive_days' => 5,
            'min_notice_days' => 0,
            'is_paid' => true,
            'is_active' => true,
            'color' => '#ef4444',
        ]);

        LeaveType::create([
            'name' => 'Emergency Leave',
            'code' => 'EMERGENCY',
            'description' => 'Unpaid emergency leave',
            'default_days_per_year' => 3,
            'requires_approval' => true,
            'requires_document' => false,
            'max_consecutive_days' => 2,
            'min_notice_days' => 0,
            'is_paid' => false,
            'is_active' => true,
            'color' => '#f97316',
        ]);

        LeaveType::create([
            'name' => 'Maternity Leave',
            'code' => 'MATERNITY',
            'description' => 'Paid maternity leave',
            'default_days_per_year' => 90,
            'requires_approval' => true,
            'requires_document' => true,
            'max_consecutive_days' => 90,
            'min_notice_days' => 30,
            'is_paid' => true,
            'is_active' => true,
            'color' => '#ec4899',
        ]);

        LeaveType::create([
            'name' => 'Paternity Leave',
            'code' => 'PATERNITY',
            'description' => 'Paid paternity leave',
            'default_days_per_year' => 10,
            'requires_approval' => true,
            'requires_document' => true,
            'max_consecutive_days' => 10,
            'min_notice_days' => 30,
            'is_paid' => true,
            'is_active' => true,
            'color' => '#06b6d4',
        ]);

        LeaveType::create([
            'name' => 'Unpaid Leave',
            'code' => 'UNPAID',
            'description' => 'Unpaid leave without limit',
            'default_days_per_year' => 0,
            'requires_approval' => true,
            'requires_document' => false,
            'max_consecutive_days' => null,
            'min_notice_days' => 10,
            'is_paid' => false,
            'is_active' => true,
            'color' => '#6b7280',
        ]);

        LeaveType::create([
            'name' => 'Study Leave',
            'code' => 'STUDY',
            'description' => 'Paid study leave for professional development',
            'default_days_per_year' => 5,
            'requires_approval' => true,
            'requires_document' => true,
            'max_consecutive_days' => 5,
            'min_notice_days' => 10,
            'is_paid' => true,
            'is_active' => true,
            'color' => '#8b5cf6',
        ]);
    }
}
