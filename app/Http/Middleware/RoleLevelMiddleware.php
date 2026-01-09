<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role Level Middleware
 *
 * Checks if user has minimum role level for an action.
 * Usage: ->middleware('role.level:3') for Manager+ access
 *
 * Levels:
 * 5 = Super Admin
 * 4 = Admin
 * 3 = Manager
 * 2 = Supervisor
 * 1 = Staff
 */
class RoleLevelMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $minLevel): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userLevel = $this->getUserLevel($user);

        if ($userLevel < $minLevel) {
            $levelNames = [
                1 => 'Staff',
                2 => 'Supervisor',
                3 => 'Manager',
                4 => 'Admin',
                5 => 'Super Admin',
            ];

            $requiredRole = $levelNames[$minLevel] ?? "Level $minLevel";
            abort(403, "This action requires {$requiredRole} access or higher.");
        }

        // Add role level to request for use in controllers/views
        $request->attributes->set('user_role_level', $userLevel);

        return $next($request);
    }

    /**
     * Get user's role level
     */
    private function getUserLevel(User $user): int
    {
        // Check by role level column first (new system)
        $role = $user->roles()->orderByDesc('level')->first();

        if ($role && isset($role->level)) {
            return (int) $role->level;
        }

        // Fallback: check by role name (old system compatibility)
        if ($user->hasRole('Super Admin')) return 5;
        if ($user->hasRole('Admin')) return 4;

        $managerRoles = [
            'Manager', 'Head of Production', 'Chef', 'Head of Gelato',
            'Confectioneries Manager', 'Sales Manager', 'HR Manager',
            'Inventory Manager', 'Corner Store Manager', 'MD', 'Managing Director'
        ];
        if ($user->hasAnyRole($managerRoles)) return 3;

        $supervisorRoles = ['Supervisor', 'Till Supervisor', 'Sales Supervisor', 'Stock Controller'];
        if ($user->hasAnyRole($supervisorRoles)) return 2;

        return 1;
    }
}
