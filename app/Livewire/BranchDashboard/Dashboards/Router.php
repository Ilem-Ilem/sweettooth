<?php

namespace App\Livewire\BranchDashboard\Dashboards;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
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

        // Get authenticated user - use direct Auth::user() instead of helper to avoid serialization issues
        $currentUser = Auth::user();

        if (!$currentUser || !($currentUser instanceof \App\Models\User)) {
            // User not authenticated or serialized - force re-auth
            Auth::logout();
            return Redirect::route('login');
        }

        \Log::info('Router component called', [
            'user_id' => $currentUser->id,
            'user_email' => $currentUser->email,
            'user_type' => $currentUser->user_type ?? 'unknown',
            'roles' => method_exists($currentUser, 'getRoleNames') ? $currentUser->getRoleNames()->toArray() : [],
            'branch_id' => $branchId,
            'has_active_shift_check' => $currentUser->user_type === 'employee' ? 'yes' : 'no',
        ]);

        // PRIORITY 1: Super Admin users go to super-admin dashboard
        if (is_super_admin()) {
            return Redirect::route('branch-dashboard.dashboards.super-admin', ['b_id' => $branchId]);
        }

        // PRIORITY 2: Regular employees (non-admin)
        // Check if they have an active shift today
        if ($currentUser->user_type === 'employee') {
            $today = now()->startOfDay();
            $hasActiveShift = \App\Models\Shift::where('employee_id', $currentUser->id)
                ->where('shift_date', '>=', $today)
                ->where('status', 'active')
                ->exists();

            \Log::info('Employee shift check', [
                'user_id' => $currentUser->id,
                'has_active_shift' => $hasActiveShift,
                'user_type' => $currentUser->user_type,
            ]);

            if (!$hasActiveShift) {
                // Employee hasn't clocked in today, redirect to clock-in
                return Redirect::route('branch-dashboard.select_shift', ['b_id' => $branchId]);
            }
        }

        // Check employee roles using SidebarVisibilityService for comprehensive role checking
        $sidebarService = \App\Services\SidebarVisibilityService::class;

        // Check for Admin role first (admins should go to HR/Admin, not department dashboards)
        $isAdmin = $currentUser->hasRole('Admin') || $currentUser->hasRole('admin');
        \Log::info('Admin check', ['is_admin' => $isAdmin]);
        if ($isAdmin) {
            return Redirect::route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
        }

        // HR roles (check early to prioritize HR over other departments)
        $isHR = $currentUser->hasRole('HR Manager') || $currentUser->hasRole('HR Officer') || $currentUser->hasRole('Hr') || $sidebarService::canSeeEmployeeManagement($currentUser);
        \Log::info('HR check', ['is_hr' => $isHR, 'roles' => $currentUser->getRoleNames()->toArray(), 'can_see_employee_management' => $sidebarService::canSeeEmployeeManagement($currentUser)]);
        if ($isHR) {
            return Redirect::route('branch-dashboard.dashboard.hr', ['b_id' => $branchId]);
        }

        // Production roles
        if ($sidebarService::canSeeProduction($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.production', ['b_id' => $branchId]);
        }

        // Corner Store Staff/Manager - dedicated dashboard
        if ($currentUser->hasRole('Corner Store Staff') || $currentUser->hasRole('Corner Store Manager')) {
            return Redirect::route('branch-dashboard.dashboard.corner-store', ['b_id' => $branchId]);
        }

        // Sales roles (check after HR to prevent HR users being redirected to sales)
        if ($sidebarService::canSeeSalesManagement($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.sales', ['b_id' => $branchId]);
        }

        // Inventory roles (check after sales)
        if ($sidebarService::canSeeInventory($currentUser)) {
            return Redirect::route('branch-dashboard.dashboard.inventory', ['b_id' => $branchId]);
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
            'web_guard' => Auth::check(),
            'employee_guard' => Auth::check(),
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
