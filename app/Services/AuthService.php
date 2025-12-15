<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Unified Authentication Service
 * Single source of truth for all user authentication and authorization checks
 * 
 * ALWAYS use this service instead of calling hasAnyRole() or auth() guards directly
 */
class AuthService
{
    /**
     * Get current authenticated user (from any guard)
     */
    public static function user()
    {
        return Auth::guard('web')->user() ?? Auth::guard('employees')->user();
    }

    /**
     * Check if user is authenticated
     */
    public static function check(): bool
    {
        return Auth::guard('web')->check() || Auth::guard('employees')->check();
    }

    /**
     * Check if user is super admin (web guard only)
     * Super admins are ONLY authenticated via web guard, NOT employees guard
     */
    public static function isSuperAdmin(): bool
    {
        // Super admin = web guard authenticated AND NOT employees guard
        return Auth::guard('web')->check() && !Auth::guard('employees')->check();
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole(string|array $roles): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        return $user->hasAnyRole($roles);
    }

    /**
     * Check if user has specific permission
     */
    public static function hasPermission(string|array $permissions): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        return $user->hasAnyPermission($permissions);
    }

    /**
     * Get current user's roles
     */
    public static function getRoles(): array
    {
        $user = self::user();
        if (!$user) {
            return [];
        }

        return $user->roles()->pluck('name')->toArray();
    }

    /**
     * Get current guard name
     */
    public static function guard(): string
    {
        if (Auth::guard('web')->check()) {
            return 'web';
        }
        if (Auth::guard('employees')->check()) {
            return 'employees';
        }
        return 'none';
    }

    /**
     * Check if user is employee (employees guard)
     */
    public static function isEmployee(): bool
    {
        return Auth::guard('employees')->check() && !Auth::guard('web')->check();
    }

    /**
     * Ensure user is authenticated, throw 401 if not
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            abort(401, 'Unauthorized');
        }
    }

    /**
     * Ensure user is super admin, throw 403 if not
     */
    public static function requireSuperAdmin(): void
    {
        if (!self::isSuperAdmin()) {
            abort(403, 'Only Super Admins can access this resource');
        }
    }

    /**
     * Ensure user has role, throw 403 if not
     */
    public static function requireRole(string|array $roles): void
    {
        if (!self::hasRole($roles)) {
            abort(403, 'Insufficient permissions');
        }
    }

    /**
     * Ensure user has permission, throw 403 if not
     */
    public static function requirePermission(string|array $permissions): void
    {
        if (!self::hasPermission($permissions)) {
            abort(403, 'Insufficient permissions');
        }
    }
}
