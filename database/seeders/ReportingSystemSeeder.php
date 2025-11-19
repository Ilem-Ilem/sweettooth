<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentReport;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Reporting System Seeder...');

        // Get a branch that has departments
        $departmentBranchIds = Department::pluck('branch_id')->unique()->filter();
        if ($departmentBranchIds->isNotEmpty()) {
            $branch = Branch::find($departmentBranchIds->first());
        } else {
            $branch = Branch::first();
        }

        if (! $branch) {
            $this->command->error('No branch found. Please create a branch first.');

            return;
        }

        $this->command->info("Using branch: {$branch->branch_name} (ID: {$branch->id})");

        // Get production departments (include NULL branch_id as they might be global)
        $productionDepartments = Department::where(function ($q) use ($branch) {
            $q->where('branch_id', $branch->id)
                ->orWhereNull('branch_id');
        })
            ->whereHas('category', fn ($q) => $q->where('name', 'Production'))
            ->get();

        // If no production departments, use any departments
        if ($productionDepartments->isEmpty()) {
            $this->command->warn('No production departments found. Using all departments...');
            $productionDepartments = Department::where(function ($q) use ($branch) {
                $q->where('branch_id', $branch->id)
                    ->orWhereNull('branch_id');
            })
                ->get();
        }

        if ($productionDepartments->isEmpty()) {
            $this->command->error('No departments found for this branch. Trying any departments...');
            $productionDepartments = Department::all();
        }

        if ($productionDepartments->isEmpty()) {
            $this->command->error('No departments found at all. Please create departments first.');

            return;
        }

        $this->command->info("Found {$productionDepartments->count()} departments");

        // Get an employee to use as report generator
        $employee = Employee::where('branch_id', $branch->id)->first();
        if (! $employee) {
            $this->command->error('No employee found for this branch. Please create employees first.');

            return;
        }

        $this->command->info("Using employee: {$employee->name}");

        // Create Production Efficiency Reports
        $this->createProductionEfficiencyReports($branch, $productionDepartments, $employee);

        // Create Quality Metrics Reports
        $this->createQualityMetricsReports($branch, $productionDepartments, $employee);

        // Create some reviewed reports ready for compilation
        $this->createReviewedReports($branch, $productionDepartments, $employee);

        $this->command->info('Reporting System Seeder completed successfully!');
    }

    private function createProductionEfficiencyReports($branch, $departments, $employee)
    {
        $this->command->info('Creating Production Efficiency Reports...');

        $today = Carbon::today();
        $statuses = ['draft', 'pending_review', 'reviewed'];

        foreach ($departments->take(3) as $index => $department) {
            $reportDate = $today->copy()->subDays($index);

            $report = DepartmentReport::create([
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'generated_by' => $employee->id,
                'report_type' => 'production_efficiency',
                'report_category' => 'production',
                'report_name' => "Production Efficiency Report - {$department->name}",
                'report_date' => $reportDate,
                'period_from' => $reportDate->copy()->startOfDay(),
                'period_to' => $reportDate->copy()->endOfDay(),
                'report_data' => [
                    'daily_summary' => [
                        'total_planned' => 100,
                        'total_actual' => 95,
                        'efficiency_percentage' => 95,
                        'variance' => -5,
                    ],
                    'product_efficiency' => [
                        ['product' => 'Product A', 'planned' => 50, 'actual' => 48, 'efficiency' => 96],
                        ['product' => 'Product B', 'planned' => 30, 'actual' => 28, 'efficiency' => 93.3],
                        ['product' => 'Product C', 'planned' => 20, 'actual' => 19, 'efficiency' => 95],
                    ],
                ],
                'summary_metrics' => [
                    'overall_efficiency' => 95,
                    'total_planned' => 100,
                    'total_actual' => 95,
                    'on_target_batches' => 2,
                    'over_produced_batches' => 0,
                    'under_produced_batches' => 1,
                ],
                'charts_data' => [
                    [
                        'type' => 'line',
                        'title' => 'Daily Efficiency Trend',
                        'data' => [
                            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                            'values' => [92, 94, 95, 93, 95],
                        ],
                    ],
                ],
                'status' => $statuses[$index % 3],
                'reviewed_by' => $statuses[$index % 3] === 'reviewed' ? $employee->id : null,
                'reviewed_at' => $statuses[$index % 3] === 'reviewed' ? now() : null,
            ]);

            $this->command->info("  ✓ Created efficiency report for {$department->name} ({$statuses[$index % 3]})");
        }
    }

    private function createQualityMetricsReports($branch, $departments, $employee)
    {
        $this->command->info('Creating Quality Metrics Reports...');

        $today = Carbon::today();
        $statuses = ['pending_review', 'reviewed', 'reviewed'];

        foreach ($departments->take(3) as $index => $department) {
            $reportDate = $today->copy()->subDays($index + 3);

            $report = DepartmentReport::create([
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'generated_by' => $employee->id,
                'report_type' => 'quality_metrics',
                'report_category' => 'production',
                'report_name' => "Quality Metrics Report - {$department->name}",
                'report_date' => $reportDate,
                'period_from' => $reportDate->copy()->startOfDay(),
                'period_to' => $reportDate->copy()->endOfDay(),
                'report_data' => [
                    'quality_overview' => [
                        'total_batches' => 20,
                        'approved_batches' => 19,
                        'rejected_batches' => 1,
                        'approval_rate' => 95,
                        'rejection_rate' => 5,
                    ],
                    'rejection_reasons' => [
                        ['reason' => 'Quality Issue', 'count' => 1],
                    ],
                ],
                'summary_metrics' => [
                    'overall_approval_rate' => 95,
                    'overall_rejection_rate' => 5,
                    'total_batches' => 20,
                    'approved_batches' => 19,
                    'rejected_batches' => 1,
                ],
                'charts_data' => [
                    [
                        'type' => 'pie',
                        'title' => 'Quality Distribution',
                        'data' => [
                            'labels' => ['Approved', 'Rejected'],
                            'values' => [19, 1],
                        ],
                    ],
                ],
                'status' => $statuses[$index % 3],
                'reviewed_by' => $statuses[$index % 3] === 'reviewed' ? $employee->id : null,
                'reviewed_at' => $statuses[$index % 3] === 'reviewed' ? now() : null,
            ]);

            $this->command->info("  ✓ Created quality report for {$department->name} ({$statuses[$index % 3]})");
        }
    }

    private function createReviewedReports($branch, $departments, $employee)
    {
        $this->command->info('Creating additional reviewed reports for compilation testing...');

        if ($departments->isEmpty()) {
            $this->command->warn('No departments available for creating reviewed reports.');

            return;
        }

        $today = Carbon::today();
        $reportTypes = ['production_efficiency', 'quality_metrics'];

        // Create 5 more reviewed reports spread across departments
        $numReports = min(5, $departments->count() * 2); // Limit based on available departments
        for ($i = 0; $i < $numReports; $i++) {
            $department = $departments->random();
            $reportType = $reportTypes[$i % 2];
            $reportDate = $today->copy()->subDays($i + 6);

            $report = DepartmentReport::create([
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'generated_by' => $employee->id,
                'report_type' => $reportType,
                'report_category' => 'production',
                'report_name' => ucfirst(str_replace('_', ' ', $reportType))." - {$department->name}",
                'report_date' => $reportDate,
                'period_from' => $reportDate->copy()->startOfWeek(),
                'period_to' => $reportDate->copy()->endOfWeek(),
                'report_data' => [
                    'summary' => 'Sample report data for testing',
                    'metrics' => ['efficiency' => rand(85, 98)],
                ],
                'summary_metrics' => [
                    'overall_efficiency' => rand(85, 98),
                    'total_planned' => rand(80, 120),
                    'total_actual' => rand(75, 115),
                    'overall_approval_rate' => rand(90, 99),
                    'overall_rejection_rate' => rand(1, 10),
                ],
                'charts_data' => [],
                'status' => 'reviewed',
                'reviewed_by' => $employee->id,
                'reviewed_at' => now()->subDays(rand(1, 3)),
                'review_notes' => 'Reviewed and approved for compilation',
            ]);

            $this->command->info("  ✓ Created reviewed {$reportType} report for {$department->name}");
        }
    }
}
