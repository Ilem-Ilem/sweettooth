<?php

namespace App\Livewire\BranchDashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

/**
 * Dashboard Router Component
 * 
 * Routes users to the correct dashboard based on their role and permission hierarchy
 * Handles 5-level role hierarchy:
 *   Level 5: Super Admin / Managing Director
 *   Level 4: Admin / Head of Production / Sales Manager / HR Manager / Inventory Manager
 *   Level 3: Chef / Head of Gelato / Till Supervisor / Confectionaries Manager
 *   Level 2: HR Officer / Stock Controller / Store Keeper
 *   Level 1: Kitchen Staff / Cashier / Store Staff / Production Staff
 */
class DashboardRouter extends Component
{
    public function mount()
    {
        $currentUser = get_user_auth();
        $branchId = request()->query('b_id');

        // Determine user's highest level role and route accordingly
        
        // Level 5: Super Admin/Managing Director - should not reach here (different auth guard)
        // These users login on web guard and go to /super-admin/dashboard directly
        
        // Level 4: Head of Department/Module Manager
        if ($currentUser->hasAnyRole(['head_of_production', 'production_manager'])) {
            return Redirect::route('branch-dashboard.dashboard.production', ['b_id' => $branchId]);
        }
        
        if ($currentUser->hasAnyRole(['sales_manager'])) {
            return Redirect::route('branch-dashboard.dashboard.sales', ['b_id' => $branchId]);
        }
        
        if ($currentUser->hasAnyRole(['hr_manager', 'employee_manager'])) {
            return Redirect::route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
        }
        
        if ($currentUser->hasAnyRole(['inventory_manager'])) {
            return Redirect::route('branch-dashboard.dashboard.inventory', ['b_id' => $branchId]);
        }

        // Level 4: Admin (web guard)
        if ($currentUser->hasRole('admin')) {
            return Redirect::route('branch-dashboard.dashboard.admin', ['b_id' => $branchId]);
        }

        // Level 3: Supervisors and Team Leads
        if ($currentUser->hasAnyRole(['chef', 'head_of_gelato', 'confectionaries_manager', 'till_supervisor', 'corner_store_manager'])) {
            // Route to their department's dashboard
            $departmentId = $currentUser->department_id;
            if ($departmentId) {
                // Could route to department-specific dashboard in future
                // For now, route to appropriate module
                if ($currentUser->hasAnyRole(['chef', 'head_of_gelato', 'confectionaries_manager'])) {
                    return Redirect::route('branch-dashboard.dashboard.production', ['b_id' => $branchId]);
                }
                if ($currentUser->hasAnyRole(['till_supervisor', 'corner_store_manager'])) {
                    return Redirect::route('branch-dashboard.dashboard.sales', ['b_id' => $branchId]);
                }
            }
        }

        // Level 2: Officers/Controllers
        if ($currentUser->hasAnyRole(['hr_officer', 'stock_controller', 'store_keeper'])) {
            if ($currentUser->hasRole('hr_officer')) {
                return Redirect::route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
            }
            if ($currentUser->hasAnyRole(['stock_controller', 'store_keeper'])) {
                return Redirect::route('branch-dashboard.dashboard.inventory', ['b_id' => $branchId]);
            }
        }

        // Level 1: Regular Staff - route by department
        if ($currentUser->hasAnyRole(['kitchen_staff', 'gelato_staff', 'confectionaries_staff', 'production_staff'])) {
            return Redirect::route('branch-dashboard.dashboard.production', ['b_id' => $branchId]);
        }

        if ($currentUser->hasAnyRole(['cashier', 'corner_store_staff', 'confectionaries_sales_staff'])) {
            return Redirect::route('branch-dashboard.dashboard.sales', ['b_id' => $branchId]);
        }

        // Default fallback: main branch dashboard
        return Redirect::route('branch-dashboard.index', ['b_id' => $branchId]);
    }

    public function render()
    {
        // This component only handles routing in mount(), view is just a loading message
        return view('livewire.branch-dashboard.dashboard-router');
    }
}
