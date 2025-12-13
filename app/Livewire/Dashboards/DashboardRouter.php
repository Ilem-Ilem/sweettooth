<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Employee;

#[Layout('components.layouts.app.branch-dashboard')]
class DashboardRouter extends Component
{
    /**
     * Render the appropriate dashboard based on user's role
     * DEPRECATED: Use BranchDashboard\Dashboards\Router instead
     */
    public function render()
    {
        // Get current user
        $user = get_user_auth();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $branchId = request()->query('b_id');

        // WEB GUARD (Super Admin)
        // Web guard users (super admins) should always go to super-admin dashboard
        if (auth()->check() && !auth('employees')->check()) {
            return redirect()->route('branch-dashboard.dashboards.super-admin', ['b_id' => $branchId]);
        }

        // EMPLOYEES GUARD
        $sidebarService = \App\Services\SidebarVisibilityService::class;

        // Check clock-in requirement
        if (auth('employees')->check()) {
            $today = now()->startOfDay();
            $clockedInToday = $user->clockIns()
                ->where('date', '>=', $today)
                ->where('clock_in_time', '!=', null)
                ->exists();

            if (!$clockedInToday) {
                return redirect()->route('branch-dashboard.clock-in-board.today', ['b_id' => $branchId]);
            }
        }

        // Route by permissions/capabilities
        if ($sidebarService::canSeeProduction($user)) {
            return redirect()->route('branch-dashboard.dashboard.production', ['b_id' => $branchId]);
        }

        if ($sidebarService::canSeeSalesManagement($user)) {
            return redirect()->route('branch-dashboard.dashboard.sales', ['b_id' => $branchId]);
        }

        if ($sidebarService::canSeeEmployeeManagement($user)) {
            return redirect()->route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
        }

        if ($sidebarService::canSeeInventory($user)) {
            return redirect()->route('branch-dashboard.dashboard.inventory', ['b_id' => $branchId]);
        }

        if ($sidebarService::canSeeReporting($user)) {
            return redirect()->route('branch-dashboard.dashboard.admin', ['b_id' => $branchId]);
        }

        // Fallback to 403 error instead of redirect loop
        abort(403, 'Your account does not have access to any dashboard. Please contact your administrator.');
    }
}
