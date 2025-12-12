<?php

namespace App\Livewire\BranchDashboard\Dashboards;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Employee;
use App\Models\Inventory\Item;

/**
 * Super Admin Dashboard - System-wide administrative view
 * 
 * Provides Super Admins with:
 * - System statistics and metrics
 * - Branch management overview
 * - Role and permission management access
 * - User and employee management
 * - System settings access
 */
class SuperAdminDashboard extends Component
{
    public function mount()
    {
        // Check if user is super admin
        if (!is_super_admin()) {
            abort(403, 'Only Super Admins can access this dashboard');
        }
    }

    public function render()
    {
        $stats = [
            'total_branches' => Branch::count(),
            'total_roles' => Role::count(),
            'total_employees' => Employee::count(),
            'active_branches' => Branch::where('is_active', true)->count(),
            'total_items' => Item::count(),
        ];

        $recentBranches = Branch::latest()->limit(5)->get();
        $recentRoles = Role::latest()->limit(5)->get();

        return view('livewire.branch-dashboard.dashboards.super-admin-dashboard', [
            'stats' => $stats,
            'recentBranches' => $recentBranches,
            'recentRoles' => $recentRoles,
        ])->layout('components.layouts.app.branch-dashboard');
    }
}
