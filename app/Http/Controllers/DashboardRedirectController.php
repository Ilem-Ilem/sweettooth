<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect to the appropriate dashboard based on user's role
     * This controller handles the dashboard routing logic
     * 
     * Flow:
     * 1. Super admin -> redirect to role-based dashboard
     * 2. Regular employee -> check if clocked in today
     *    - If not clocked in -> redirect to shift selection (clock-in page)
     *    - If clocked in -> redirect to role-based dashboard
     */
    public function redirect(Request $request)
    {
        // Get current authenticated user (from either guard)
        $user = current_actor();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Not authenticated');
        }

        // Check if super admin (users table, NOT employees guard)
        $isSuperAdmin = is_super_admin();
        
        // Get branch ID for URL parameter
        $branchId = current_branch_id();
        
        // For regular employees (not super admin), check clock-in status
        if (!$isSuperAdmin && auth('employees')->check()) {
            if (!$this->hasClockInToday($user)) {
                // Employee not clocked in - redirect to shift selection
                if ($branchId) {
                    return redirect()->route('branch-dashboard.select_shift', ['b_id' => $branchId]);
                }
                return redirect()->route('branch-dashboard.select_shift');
            }
        }

        // Get user's roles
        $roles = $user->roles()->get();
        
        if ($roles->isEmpty()) {
            return view('livewire.dashboards.no-role');
        }

        // Determine primary role (highest priority if multiple roles)
        $primaryRole = $this->getPrimaryRole($roles);
        
        if (!$primaryRole) {
            return view('livewire.dashboards.no-role');
        }

        // Get the dashboard route for this role
        $dashboardRoute = $this->getDashboardRoute($primaryRole->name);
        
        // Get department slug if user is department-assigned
        $deptSlug = null;
        if ($user instanceof Employee && $user->department) {
            $deptSlug = $user->department->slug;
        }
        
        // Log the routing decision
        \Log::info('Dashboard redirect', [
            'user_id' => $user->id ?? 'unknown',
            'primary_role' => $primaryRole->name,
            'target_route' => $dashboardRoute,
            'branch_id' => $branchId,
            'dept_slug' => $deptSlug,
            'is_super_admin' => is_super_admin(),
        ]);
        
        // Super admin without explicit branch - show all branches or redirect to super-admin dashboard
        if (!$branchId && in_array($dashboardRoute, ['branch-dashboard.dashboard.super-admin'])) {
            return redirect()->route($dashboardRoute);
        }
        
        // Redirect to the appropriate dashboard with branch context
        if ($branchId) {
            // For production and sales dashboards, include department slug if available
            if (in_array($dashboardRoute, ['branch-dashboard.dashboard.production', 'branch-dashboard.dashboard.sales']) && $deptSlug) {
                return redirect()->route($dashboardRoute, ['deptSlug' => $deptSlug, 'b_id' => $branchId]);
            }
            return redirect()->route($dashboardRoute, ['b_id' => $branchId]);
        }
        
        return redirect()->route($dashboardRoute);
    }

    /**
     * Check if employee has clocked in today
     */
    private function hasClockInToday(Employee|User $user): bool
    {
        if (!auth('employees')->check()) {
            return true; // Super admin doesn't need clock-in
        }

        $employee = auth('employees')->user();
        
        if (!$employee) {
            return false;
        }

        // Check if there's an active shift for today
        $hasShift = Shift::where('employee_id', $employee->id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->exists();

        return $hasShift;
    }

    /**
     * Get the primary role (highest priority if user has multiple roles)
     */
    private function getPrimaryRole($roles)
    {
        if ($roles->count() === 1) {
            return $roles->first();
        }

        // Multiple roles - return the one with highest priority
        return $this->getHighestPriorityRole($roles);
    }

    /**
     * Get the highest priority role from a collection
     * Handles both snake_case and Title Case role names
     */
    private function getHighestPriorityRole($roles)
    {
        $rolePriorities = [
            // Level 5 - Executive (highest priority)
            'super_admin' => 50,
            'managing_director' => 50,
            'md' => 50,
            
            // Level 4 - Management
            'admin' => 40,
            'branch_admin' => 40,
            'head_of_production' => 40,
            'sales_manager' => 40,
            'hr_manager' => 40,
            'inventory_manager' => 40,
            
            // Level 3 - Supervisor
            'chef' => 30,
            'head_of_gelato' => 30,
            'confectionaries_manager' => 30,
            'till_supervisor' => 30,
            'corner_store_manager' => 30,
            
            // Level 2 - Officer
            'hr_officer' => 20,
            'stock_controller' => 20,
            'store_keeper' => 20,
            
            // Level 1 - Staff (lowest priority)
            'kitchen_staff' => 10,
            'gelato_production_staff' => 10,
            'confectionaries_production_staff' => 10,
            'cashier' => 10,
            'corner_store_staff' => 10,
            'confectionaries_sales_staff' => 10,
        ];
        
        $highestPriorityRole = null;
        $highestPriority = 0;
        
        foreach ($roles as $role) {
            // Normalize role name to snake_case for lookup
            $normalizedRoleName = strtolower(str_replace(' ', '_', $role->name));
            $priority = $rolePriorities[$normalizedRoleName] ?? 0;
            
            if ($priority > $highestPriority) {
                $highestPriority = $priority;
                $highestPriorityRole = $role;
            }
        }
        
        return $highestPriorityRole ?? $roles->first();
    }

    /**
     * Map role to dashboard route
     * Handles both snake_case and Title Case role names
     */
    private function getDashboardRoute(string $roleName): string
    {
        // Normalize role name to snake_case for matching
        $normalizedRole = strtolower(str_replace(' ', '_', $roleName));
        
        return match ($normalizedRole) {
            // Executive Level
            'super_admin',
            'managing_director',
            'md' => 'branch-dashboard.dashboard.super-admin',

            // Management Level
            'admin',
            'branch_admin' => 'branch-dashboard.dashboard.admin',

            // Module Managers
            'head_of_production' => 'branch-dashboard.dashboard.production',
            'sales_manager' => 'branch-dashboard.dashboard.sales',
            'hr_manager' => 'branch-dashboard.dashboard.hr',
            'inventory_manager' => 'branch-dashboard.dashboard.inventory',

            // Supervisors - Route to their module dashboard
            'chef' => 'branch-dashboard.dashboard.production',
            'head_of_gelato' => 'branch-dashboard.dashboard.production',
            'confectionaries_manager' => 'branch-dashboard.dashboard.production',
            'till_supervisor' => 'branch-dashboard.dashboard.sales',
            'corner_store_manager' => 'branch-dashboard.dashboard.sales',

            // Officers - Route to their module dashboard
            'hr_officer' => 'branch-dashboard.dashboard.hr',
            'stock_controller' => 'branch-dashboard.dashboard.inventory',
            'store_keeper' => 'branch-dashboard.dashboard.inventory',

            // Staff - Route to their module dashboard
            'kitchen_staff' => 'branch-dashboard.dashboard.production',
            'gelato_production_staff' => 'branch-dashboard.dashboard.production',
            'confectionaries_production_staff' => 'branch-dashboard.dashboard.production',
            'cashier' => 'branch-dashboard.dashboard.sales',
            'corner_store_staff' => 'branch-dashboard.dashboard.sales',
            'confectionaries_sales_staff' => 'branch-dashboard.dashboard.sales',

            // Default fallback
            default => 'branch-dashboard.index',
        };
    }
}
