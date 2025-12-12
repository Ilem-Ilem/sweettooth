<?php

namespace App\Livewire\BranchDashboard\Dashboards;

use Livewire\Component;
use App\Models\Employee;
use App\Models\Department;

/**
 * Manager Dashboard - Manager level view
 * 
 * Provides Manager roles with:
 * - Team/department statistics
 * - Employee performance overview
 * - Departmental reports
 * - Team-level operations access
 */
class ManagerDashboard extends Component
{
    public function mount()
    {
        // Check if user has manager role
        $currentUser = get_user_auth();
        if (!$currentUser->hasRole('manager')) {
            abort(403, 'Only Managers can access this dashboard');
        }
    }

    public function render()
    {
        $currentUser = get_user_auth();
        $currentBranchId = request()->query('b_id');
        
        // If employee is logged in, get their department
        $userDepartmentId = $currentUser?->department_id;

        $stats = [
            'team_size' => Employee::when($userDepartmentId, function($q) use ($userDepartmentId) {
                return $q->where('department_id', $userDepartmentId);
            })->when($currentBranchId, function($q) use ($currentBranchId) {
                return $q->where('branch_id', $currentBranchId);
            })->count(),
            'active_team_members' => Employee::where('status', 'active')->when($userDepartmentId, function($q) use ($userDepartmentId) {
                return $q->where('department_id', $userDepartmentId);
            })->when($currentBranchId, function($q) use ($currentBranchId) {
                return $q->where('branch_id', $currentBranchId);
            })->count(),
        ];

        $teamMembers = Employee::when($userDepartmentId, function($q) use ($userDepartmentId) {
            return $q->where('department_id', $userDepartmentId);
        })->when($currentBranchId, function($q) use ($currentBranchId) {
            return $q->where('branch_id', $currentBranchId);
        })->latest()->limit(10)->get();

        return view('livewire.branch-dashboard.dashboards.manager-dashboard', [
            'stats' => $stats,
            'teamMembers' => $teamMembers,
        ])->layout('components.layouts.app.branch-dashboard');
    }
}
