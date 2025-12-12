<?php

namespace App\Livewire\BranchDashboard\Dashboards;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

/**
 * Dashboard Router - Single entry point for role-based dashboard redirects
 * 
 * This component routes users to the appropriate dashboard based on their role:
 * - Super Admin → SuperAdminDashboard
 * - MD/Admin → AdminDashboard
 * - Manager → ManagerDashboard
 * - Supervisor → SupervisorDashboard
 * - Staff → DepartmentDashboard
 */
class Router extends Component
{
    public function mount()
    {
        $currentUser = get_user_auth();
        $isSuperAdmin = is_super_admin();

        // Route based on user role
        if ($isSuperAdmin) {
            // Super Admin goes to super admin dashboard
            return Redirect::route('branch-dashboard.dashboards.super-admin', ['b_id' => request()->query('b_id')]);
        }

        if ($currentUser->hasRole('admin') || $currentUser->hasRole('md')) {
            // Admin/MD goes to admin dashboard
            return Redirect::route('branch-dashboard.dashboards.admin', ['b_id' => request()->query('b_id')]);
        }

        if ($currentUser->hasRole('manager')) {
            // Manager goes to manager dashboard
            return Redirect::route('branch-dashboard.dashboards.manager', ['b_id' => request()->query('b_id')]);
        }

        if ($currentUser->hasRole('supervisor')) {
            // Supervisor goes to supervisor dashboard
            return Redirect::route('branch-dashboard.dashboards.supervisor', ['b_id' => request()->query('b_id')]);
        }

        // Default: staff/employees go to main branch dashboard
        return Redirect::route('branch-dashboard.index', ['b_id' => request()->query('b_id')]);
    }

    public function render()
    {
        return view('livewire.branch-dashboard.dashboards.router');
    }
}
