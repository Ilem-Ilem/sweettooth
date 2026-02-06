<?php

namespace App\Services\Reports\Definitions;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HRWorkforceOverviewDefinition implements ReportDefinition
{
    public function meta(): array
    {
        return [
            'name' => 'HR Workforce Overview',
            'type' => 'workforce_overview',
            'category' => 'hr',
            'requires_department' => false,
            'permissions' => [
                'manage_organization',
                'manage-hr',
                'manage_hr',
                'manage-employees',
                'manage_employees',
                'view-hr-reports',
                'view_hr_reports',
            ],
            'order' => 1,
        ];
    }

    public function query(array $context): array
    {
        $branchId = $context['branch_id'] ?? null;
        $from = $context['period_from'] ?? null;
        $to = $context['period_to'] ?? null;

        $employeeBase = Employee::query()
            ->where('user_type', 'employee')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId));

        $totalEmployees = (clone $employeeBase)->count();
        $activeEmployees = (clone $employeeBase)->where('is_active', true)->count();
        $inactiveEmployees = max($totalEmployees - $activeEmployees, 0);

        $newHires = (clone $employeeBase)
            ->when($from && $to, fn ($q) => $q->whereBetween('hire_date', [$from, $to]))
            ->count();

        $terminations = (clone $employeeBase)
            ->whereNotNull('termination_date')
            ->when($from && $to, fn ($q) => $q->whereBetween('termination_date', [$from, $to]))
            ->count();

        $departmentHeadcount = Employee::query()
            ->where('user_type', 'employee')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->select('department_id', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active'))
            ->groupBy('department_id')
            ->get()
            ->map(function ($row) {
                $departmentName = Department::where('id', $row->department_id)->value('name');
                return [
                    'department_id' => $row->department_id,
                    'department' => $departmentName ?? 'Unassigned',
                    'total' => (int) $row->total,
                    'active' => (int) $row->active,
                    'inactive' => max(((int) $row->total - (int) $row->active), 0),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->all();

        $leaveQuery = LeaveApplication::query()
            ->with(['employee', 'leaveType'])
            ->when($branchId, function ($q) use ($branchId) {
                $q->whereHas('employee', fn ($employee) => $employee->where('branch_id', $branchId));
            })
            ->when($from && $to, function ($q) use ($from, $to) {
                $q->whereDate('start_date', '<=', $to)
                    ->whereDate('end_date', '>=', $from);
            });

        $leaveRequests = (clone $leaveQuery)->get();

        $leaveSummary = [
            'total_requests' => $leaveRequests->count(),
            'pending' => $leaveRequests->where('status', 'pending')->count(),
            'approved' => $leaveRequests->where('status', 'approved')->count(),
            'rejected' => $leaveRequests->where('status', 'rejected')->count(),
            'cancelled' => $leaveRequests->where('status', 'cancelled')->count(),
        ];

        $leaveTypes = $leaveRequests
            ->groupBy('leave_type_id')
            ->map(function ($items) {
                $leaveType = $items->first()?->leaveType?->name ?? 'Unspecified';
                return [
                    'leave_type' => $leaveType,
                    'requests' => $items->count(),
                    'approved' => $items->where('status', 'approved')->count(),
                    'pending' => $items->where('status', 'pending')->count(),
                ];
            })
            ->sortByDesc('requests')
            ->values()
            ->all();

        $recentLeaves = $leaveRequests
            ->sortByDesc('created_at')
            ->take(10)
            ->map(function ($leave) {
                return [
                    'employee' => $leave->employee?->name ?? 'Unknown',
                    'leave_type' => $leave->leaveType?->name ?? 'Unspecified',
                    'start_date' => optional($leave->start_date)->format('Y-m-d'),
                    'end_date' => optional($leave->end_date)->format('Y-m-d'),
                    'status' => $leave->status,
                    'days' => $leave->total_days ?? 0,
                ];
            })
            ->values()
            ->all();

        $recentHires = (clone $employeeBase)
            ->when($from && $to, fn ($q) => $q->whereBetween('hire_date', [$from, $to]))
            ->orderByDesc('hire_date')
            ->take(10)
            ->get()
            ->map(function ($employee) {
                return [
                    'employee' => $employee->name,
                    'department' => $employee->department?->name ?? 'Unassigned',
                    'hire_date' => optional($employee->hire_date)->format('Y-m-d'),
                    'status' => $employee->employment_status ?? ($employee->is_active ? 'Active' : 'Inactive'),
                ];
            })
            ->values()
            ->all();

        $recentTerminations = (clone $employeeBase)
            ->whereNotNull('termination_date')
            ->when($from && $to, fn ($q) => $q->whereBetween('termination_date', [$from, $to]))
            ->orderByDesc('termination_date')
            ->take(10)
            ->get()
            ->map(function ($employee) {
                return [
                    'employee' => $employee->name,
                    'department' => $employee->department?->name ?? 'Unassigned',
                    'termination_date' => optional($employee->termination_date)->format('Y-m-d'),
                    'status' => $employee->employment_status ?? 'Terminated',
                ];
            })
            ->values()
            ->all();

        $monthlyHires = $this->groupMonthly((clone $employeeBase), 'hire_date', $from, $to);
        $monthlyTerminations = $this->groupMonthly((clone $employeeBase)->whereNotNull('termination_date'), 'termination_date', $from, $to);

        return [
            'headcount' => [
                'total' => $totalEmployees,
                'active' => $activeEmployees,
                'inactive' => $inactiveEmployees,
                'new_hires' => $newHires,
                'terminations' => $terminations,
            ],
            'departments' => $departmentHeadcount,
            'leave_summary' => $leaveSummary,
            'leave_types' => $leaveTypes,
            'recent_leaves' => $recentLeaves,
            'recent_hires' => $recentHires,
            'recent_terminations' => $recentTerminations,
            'monthly_hires' => $monthlyHires,
            'monthly_terminations' => $monthlyTerminations,
            'period_info' => [
                'from' => $from,
                'to' => $to,
                'total_days' => $from && $to ? Carbon::parse($from)->diffInDays(Carbon::parse($to)) + 1 : 0,
            ],
        ];
    }

    public function summary(array $data, array $context): array
    {
        $headcount = $data['headcount'] ?? [];
        $leave = $data['leave_summary'] ?? [];

        return [
            'total_employees' => $headcount['total'] ?? 0,
            'active_employees' => $headcount['active'] ?? 0,
            'inactive_employees' => $headcount['inactive'] ?? 0,
            'new_hires' => $headcount['new_hires'] ?? 0,
            'terminations' => $headcount['terminations'] ?? 0,
            'leave_requests' => $leave['total_requests'] ?? 0,
            'pending_leaves' => $leave['pending'] ?? 0,
            'approved_leaves' => $leave['approved'] ?? 0,
        ];
    }

    public function charts(array $data, array $context): array
    {
        $departments = $data['departments'] ?? [];
        $leaveSummary = $data['leave_summary'] ?? [];
        $monthlyHires = $data['monthly_hires'] ?? [];
        $monthlyTerminations = $data['monthly_terminations'] ?? [];

        return [
            'department_headcount' => [
                'type' => 'bar',
                'labels' => array_column($departments, 'department'),
                'datasets' => [
                    [
                        'label' => 'Active Employees',
                        'data' => array_column($departments, 'active'),
                        'color' => '#3b82f6',
                    ],
                    [
                        'label' => 'Total Employees',
                        'data' => array_column($departments, 'total'),
                        'color' => '#94a3b8',
                    ],
                ],
            ],
            'leave_status' => [
                'type' => 'pie',
                'labels' => ['Pending', 'Approved', 'Rejected', 'Cancelled'],
                'data' => [
                    $leaveSummary['pending'] ?? 0,
                    $leaveSummary['approved'] ?? 0,
                    $leaveSummary['rejected'] ?? 0,
                    $leaveSummary['cancelled'] ?? 0,
                ],
                'colors' => ['#f59e0b', '#10b981', '#ef4444', '#6b7280'],
            ],
            'hire_vs_termination' => [
                'type' => 'line',
                'labels' => array_column($monthlyHires, 'month'),
                'datasets' => [
                    [
                        'label' => 'New Hires',
                        'data' => array_column($monthlyHires, 'count'),
                        'color' => '#10b981',
                    ],
                    [
                        'label' => 'Terminations',
                        'data' => array_column($monthlyTerminations, 'count'),
                        'color' => '#ef4444',
                    ],
                ],
            ],
        ];
    }

    public function tables(array $data, array $summary, array $context): array
    {
        return [
            'department_headcount' => [
                'headers' => ['Department', 'Active', 'Inactive', 'Total'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['department'],
                        $row['active'],
                        $row['inactive'],
                        $row['total'],
                    ];
                }, $data['departments'] ?? []),
            ],
            'leave_requests' => [
                'headers' => ['Employee', 'Leave Type', 'Start Date', 'End Date', 'Days', 'Status'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['employee'],
                        $row['leave_type'],
                        $row['start_date'],
                        $row['end_date'],
                        $row['days'],
                        ucfirst($row['status']),
                    ];
                }, $data['recent_leaves'] ?? []),
            ],
            'leave_types' => [
                'headers' => ['Leave Type', 'Requests', 'Approved', 'Pending'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['leave_type'],
                        $row['requests'],
                        $row['approved'],
                        $row['pending'],
                    ];
                }, $data['leave_types'] ?? []),
            ],
            'recent_hires' => [
                'headers' => ['Employee', 'Department', 'Hire Date', 'Status'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['employee'],
                        $row['department'],
                        $row['hire_date'],
                        $row['status'],
                    ];
                }, $data['recent_hires'] ?? []),
            ],
            'recent_terminations' => [
                'headers' => ['Employee', 'Department', 'Termination Date', 'Status'],
                'rows' => array_map(function ($row) {
                    return [
                        $row['employee'],
                        $row['department'],
                        $row['termination_date'],
                        $row['status'],
                    ];
                }, $data['recent_terminations'] ?? []),
            ],
        ];
    }

    public function narrative(array $data, array $summary, array $context): array
    {
        $highlights = [];
        $concerns = [];
        $recommendations = [];

        $netChange = ($summary['new_hires'] ?? 0) - ($summary['terminations'] ?? 0);
        $highlights[] = "Net headcount change: {$netChange} (Hires: {$summary['new_hires']}, Terminations: {$summary['terminations']}).";

        if (($summary['total_employees'] ?? 0) === 0) {
            $concerns[] = 'No employees found for the selected branch and period.';
        }

        if (($summary['pending_leaves'] ?? 0) > 10) {
            $concerns[] = 'High number of pending leave requests. Review approval backlog.';
            $recommendations[] = 'Prioritize leave approvals to reduce pending queue.';
        }

        if (($summary['terminations'] ?? 0) > ($summary['new_hires'] ?? 0)) {
            $concerns[] = 'Terminations exceed new hires in the selected period.';
            $recommendations[] = 'Assess retention initiatives and staffing gaps.';
        }

        return [
            'overview' => "Workforce overview for the selected period: {$summary['total_employees']} total employees with {$summary['active_employees']} active.",
            'highlights' => $highlights,
            'concerns' => $concerns,
            'recommendations' => $recommendations,
        ];
    }

    private function groupMonthly($query, string $dateColumn, $from, $to): array
    {
        if (!$from || !$to) {
            return [];
        }

        $start = Carbon::parse($from)->startOfMonth();
        $end = Carbon::parse($to)->endOfMonth();
        $months = [];

        while ($start->lte($end)) {
            $monthLabel = $start->format('Y-m');
            $months[$monthLabel] = 0;
            $start->addMonth();
        }

        $records = $query
            ->whereBetween($dateColumn, [$from, $to])
            ->get([$dateColumn]);

        foreach ($records as $record) {
            $month = Carbon::parse($record->{$dateColumn})->format('Y-m');
            if (array_key_exists($month, $months)) {
                $months[$month]++;
            }
        }

        $result = [];
        foreach ($months as $month => $count) {
            $result[] = [
                'month' => $month,
                'count' => $count,
            ];
        }

        return $result;
    }
}
