<?php

namespace App\Livewire\Dashboards;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Employee;
use App\Helpers\RolePermission;

#[Layout('components.layouts.app.branch-dashboard')]
class DashboardRouter extends Component
{
    /**
     * Render the appropriate dashboard based on user's role
     */
    public function render()
    {
        // Get current user
        $user = current_actor();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user's primary role (highest priority if multiple roles)
        $role = $this->getUserRole($user);
        
        if (!$role) {
            return view('livewire.dashboards.no-role');
        }

        // Determine which dashboard to route to
        $dashboardRoute = $this->getDashboardRoute($role->name);
        
        // Redirect to appropriate dashboard
        return redirect()->route($dashboardRoute);
    }

    /**
     * Get user's primary role (highest level if multiple roles assigned)
     * 
     * When a user has multiple roles, we select the one with highest priority:
     * - Level 5 (Executive) > Level 4 (Management) > Level 3 > Level 2 > Level 1
     */
    private function getUserRole($user)
    {
        // If user is in employees table (has roles via HasRoles trait)
        if ($user instanceof Employee) {
            $userRoles = $user->roles()->get();
            
            if ($userRoles->isEmpty()) {
                return null;
            }
            
            // If user has only one role, return it
            if ($userRoles->count() === 1) {
                return $userRoles->first();
            }
            
            // User has multiple roles - return the one with highest priority
            return $this->getHighestPriorityRole($userRoles);
        }

        // Super admin (web guard) - has roles via HasRoles trait
        if (auth('web')->check()) {
            $userRoles = $user->roles()->get();
            
            if ($userRoles->isEmpty()) {
                return null;
            }
            
            if ($userRoles->count() === 1) {
                return $userRoles->first();
            }
            
            return $this->getHighestPriorityRole($userRoles);
        }

        return null;
    }

    /**
     * Get the highest priority role from a collection of roles
     * Priority is determined by role level (5 = highest, 1 = lowest)
     */
    private function getHighestPriorityRole($roles)
    {
        $rolePriorities = [
            // Level 5 - Executive (highest priority)
            'super_admin' => 50,
            'managing_director' => 50,
            
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
        
        // Find the role with highest priority value
        $highestPriorityRole = null;
        $highestPriority = 0;
        
        foreach ($roles as $role) {
            $priority = $rolePriorities[$role->name] ?? 0;
            
            if ($priority > $highestPriority) {
                $highestPriority = $priority;
                $highestPriorityRole = $role;
            }
        }
        
        // Return highest priority role, or first role if no match found
        return $highestPriorityRole ?? $roles->first();
    }

    /**
     * Map role to dashboard route name
     */
    private function getDashboardRoute(string $roleName): string
    {
        return match ($roleName) {
            // Executive Level
            'super_admin',
            'managing_director' => 'branch-dashboard.dashboard.super-admin',

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

            // Default - fallback to generic dashboard
            default => 'branch-dashboard.index',
        };
    }
}
