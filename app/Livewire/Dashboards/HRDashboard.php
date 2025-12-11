<?php

namespace App\Livewire\Dashboards;

use App\Models\Employee;
use Livewire\Attributes\Layout;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app.branch-dashboard')]
class HRDashboard extends BaseDashboard
{
    public function mount()
    {
        parent::mount();
        $this->verifyAccess();
    }

    private function verifyAccess(): void
    {
        $role = $this->getUserRoleName();
        $allowedRoles = [
            'hr_manager',
            'hr_officer',
        ];

        // Allow access if user has allowed role OR is super admin
        $isAllowed = in_array($role, $allowedRoles) || is_super_admin();
        
        if (!$isAllowed) {
            abort(403, 'Unauthorized access to HR dashboard');
        }
    }

    public function getTotalEmployees(): int
    {
        return $this->remember('total_employees', function () {
            return Employee::where('branch_id', $this->getBranchId())
                ->where('status', 'active')
                ->count();
        });
    }

    public function getOnDutyToday(): int
    {
        return $this->remember('on_duty_today', function () {
            return DB::table('clock_in_outs')
                ->where('branch_id', $this->getBranchId())
                ->whereDate('clock_in_time', Carbon::today())
                ->distinct('employee_id')
                ->count('employee_id');
        });
    }

    public function getOnLeaveToday(): int
    {
        return $this->remember('on_leave_today', function () {
            return DB::table('leaves')
                ->where('branch_id', $this->getBranchId())
                ->where('status', 'approved')
                ->whereDate('date', Carbon::today())
                ->distinct('employee_id')
                ->count('employee_id');
        });
    }

    public function getPendingLeaveRequests(): int
    {
        return $this->remember('pending_leave_requests', function () {
            return DB::table('leaves')
                ->where('branch_id', $this->getBranchId())
                ->where('status', 'pending')
                ->count();
        });
    }

    public function getAttendancePercentage(): float
    {
        return $this->remember('attendance_percentage', function () {
            $totalEmployees = $this->getTotalEmployees();
            if ($totalEmployees === 0) {
                return 0;
            }

            $onDuty = $this->getOnDutyToday();
            return ($onDuty / $totalEmployees) * 100;
        });
    }

    public function getEmployeesByDepartment()
    {
        return $this->remember('employees_by_department', function () {
            return DB::table('employees')
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->where('employees.branch_id', $this->getBranchId())
                ->groupBy('departments.id', 'departments.name')
                ->selectRaw('departments.name, COUNT(employees.id) as count')
                ->get();
        });
    }

    public function getPendingLeaves()
    {
        return $this->remember('pending_leaves', function () {
            return DB::table('leaves')
                ->join('employees', 'leaves.employee_id', '=', 'employees.id')
                ->where('leaves.branch_id', $this->getBranchId())
                ->where('leaves.status', 'pending')
                ->selectRaw('leaves.*, employees.name as employee_name')
                ->orderBy('leaves.created_at', 'desc')
                ->limit(10)
                ->get();
        });
    }

    public function getHRAlerts()
    {
        $alerts = [];
        $pendingLeaves = $this->getPendingLeaveRequests();

        if ($pendingLeaves > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pending Leave Requests',
                'message' => "{$pendingLeaves} leave request(s) awaiting approval",
            ];
        }

        return $alerts;
    }

    public function render()
    {
        try {
            return view('livewire.dashboards.hr.dashboard', [
                'totalEmployees' => $this->getTotalEmployees(),
                'onDutyToday' => $this->getOnDutyToday(),
                'onLeaveToday' => $this->getOnLeaveToday(),
                'pendingLeaveRequests' => $this->getPendingLeaveRequests(),
                'attendancePercentage' => $this->getAttendancePercentage(),
                'employeesByDepartment' => $this->getEmployeesByDepartment(),
                'pendingLeaves' => $this->getPendingLeaves(),
                'hrAlerts' => $this->getHRAlerts(),
            ]);
        } catch (\Exception $e) {
            $this->handleError('loading HR dashboard', $e);
            return view('livewire.dashboards.error');
        }
    }
}
