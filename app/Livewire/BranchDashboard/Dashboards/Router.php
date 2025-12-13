<?php

namespace App\Livewire\BranchDashboard\Dashboards;

use Livewire\Component;
use Illuminate\Support\Facades\Redirect;

/**
 * Dashboard Router - Single entry point for role-based dashboard redirects
 * 
 * Routes users to appropriate dashboard based on their role and guard
 * Checks for clock-in requirement first
 * Handles all cases without redirect loops
 */
class Router extends Component
{
    public function mount()
    {
        $branchId = request()->query('b_id');

        // Write to file directly for debugging
        file_put_contents('/tmp/router_debug.log', date('Y-m-d H:i:s') . " - Mount called\n" .
            "b_id: " . ($branchId ?? 'null') . "\n" .
            "web user: " . (auth()->user()?->email ?? 'null') . "\n" .
            "employee user: " . (auth('employees')->user()?->employee_number ?? 'null') . "\n\n", FILE_APPEND);

        // If no user authenticated, redirect to login
        if (!auth()->check() && !auth('employees')->check()) {
            return Redirect::route('login');
        }

        // PRIORITY 1: Check if user is authenticated via web guard (Super Admin/MD)
        // Super Admin users should bypass shift selection entirely
        // Do this check FIRST and ONLY on web guard, not on employees
        $webUser = auth()->user();
        $isWebGuardOnly = auth()->check() && !auth('employees')->check();
        
        \Log::info('Router component called', [
            'has_web_user' => $webUser ? true : false,
            'web_user_id' => $webUser?->id,
            'web_user_email' => $webUser?->email,
            'is_web_guard_only' => $isWebGuardOnly,
            'has_employee_user' => auth('employees')->user() ? true : false,
        ]);
        
        if ($webUser && $isWebGuardOnly) {
            try {
                // Web guard users (super admins) should always go to super-admin dashboard
                // regardless of whether they have explicit roles assigned
                \Log::info('Web guard user detected - redirecting to super-admin dashboard', [
                    'user_id' => $webUser->id,
                    'email' => $webUser->email,
                ]);
                return Redirect::route('branch-dashboard.dashboards.super-admin', ['b_id' => $branchId]);
            } catch (\Exception $e) {
                \Log::error('Error handling web guard user', ['error' => $e->getMessage()]);
            }
        }

        // PRIORITY 2: Get current user (for employee guard)
        $currentUser = get_user_auth();
        
        if (!$currentUser) {
            return Redirect::route('login');
        }

        // PRIORITY 3: For employees (employees guard), check if they have an active shift today
        // Only employees need to clock in
        if (auth('employees')->check() && !auth()->check()) {  // Make sure they're ONLY on employees guard
            $today = now()->startOfDay();
            $hasActiveShift = \App\Models\Shift::where('employee_id', $currentUser->id)
                ->where('shift_date', '>=', $today)
                ->where('status', 'active')
                ->exists();

            if (!$hasActiveShift) {
                // Employee hasn't clocked in today, redirect to clock-in
                return Redirect::route('branch-dashboard.select_shift', ['b_id' => $branchId]);
            }
        }

        // EMPLOYEES GUARD (Employee Users)
        // Now check employee roles using SidebarVisibilityService for comprehensive role checking
        $sidebarService = \App\Services\SidebarVisibilityService::class;

        // Check for Admin role first (admins should go to HR/Admin, not department dashboards)
        if ($currentUser->hasRole('Admin')) {
            return Redirect::route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
        }

        // Production roles
        if ($sidebarService::canSeeProduction($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.production', ['b_id' => $branchId]);
        }

        // Sales roles
        if ($sidebarService::canSeeSalesManagement($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.sales', ['b_id' => $branchId]);
        }

        // Inventory roles (check before HR to prioritize inventory-specific roles)
        if ($sidebarService::canSeeInventory($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.inventory', ['b_id' => $branchId]);
        }

        // HR roles (check after inventory since inventory roles may have view-employees permission)
        if ($sidebarService::canSeeEmployeeManagement($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
        }

        // Reporting roles
        if ($sidebarService::canSeeReporting($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.admin', ['b_id' => $branchId]);
        }

        // Organization/General access
        if ($sidebarService::canSeeOrganization($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.admin', ['b_id' => $branchId]);
        }

        // FALLBACK: If employee has no recognized dashboard access, show 403 error instead of redirect loop
        // Log detailed information to help debug permission issues
        \Log::warning('Employee denied dashboard access - no permissions matched', [
            'user_id' => $currentUser?->id,
            'user_email' => $currentUser?->email ?? 'unknown',
            'employee_number' => $currentUser?->employee_number ?? 'unknown',
            'web_guard' => auth()->check(),
            'employee_guard' => auth('employees')->check(),
            'accessible_dashboards' => [
                'production' => $sidebarService::canSeeProduction($currentUser),
                'sales' => $sidebarService::canSeeSalesManagement($currentUser),
                'inventory' => $sidebarService::canSeeInventory($currentUser),
                'hr' => $sidebarService::canSeeEmployeeManagement($currentUser),
                'reporting' => $sidebarService::canSeeReporting($currentUser),
                'organization' => $sidebarService::canSeeOrganization($currentUser),
            ],
        ]);
        abort(403, 'Your account does not have access to any dashboard. Please contact your administrator.');
    }

    public function render()
    {
        return view('livewire.branch-dashboard.dashboards.router');
    }
}
