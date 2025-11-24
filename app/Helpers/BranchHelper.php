<?php

use App\Models\Branch;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\User;

/**
 * Branch Helper Functions
 *
 * These helper functions provide utilities for managing branch context
 * in a multi-branch application with super admin capabilities.
 */
/**
 * Audit Helper - Delegates to AuditService
 *
 * Provides a convenient function-based API for audit logging.
 * For more advanced features, use AuditService directly.
 *
 * @see App\Services\AuditService
 */
if (!function_exists('audit')) {
    function audit(
        $causer,
        $action,
        $auditable = null,
        $description = null,
        $status = 'completed',
        $approvalRequest = null,
        array $metadata = []
    ) {
        return \App\Services\AuditService::log(
            $causer,
            $action,
            $auditable,
            $description,
            $status,
            $approvalRequest,
            $metadata
        );
    }
}

if (!function_exists('current_branch_id')) {
    /**
     * Get the current branch ID based on user context.
     *
     * Returns the branch ID from session (for super admins) or from
     * the authenticated employee's branch assignment.
     *
     * @return int|null
     */
    function current_branch_id(): ?string
    {
        // First check session (for super admins who can switch branches)
        if (session()->has('selected_branch_id')) {
            return session('selected_branch_id');
        }

        // Then check authenticated employee's branch
        if (auth('employees')->check()) {
            return auth('employees')->user()->branch_id;
        }

        // Fallback to null if no branch context available
        return null;
    }
}

if (!function_exists('get_user_auth')) {
    function get_user_auth()
    {
        return auth("employees")->user() ?? auth()->user();
    }
}

if (!function_exists('current_actor')) {
    function current_actor(): User|Employee|null
    {
        if (auth()->check()) {
            return auth()->user();
        }

        if (auth('employees')->check()) {
            return auth('employees')->user();
        }

        return null;
    }
}


if (!function_exists('is_super_admin')) {
    /**
     * Check if the current user is a super admin.
     *
     * A super admin is defined as someone who is authenticated in the
     * default guard (users) but NOT in the employees guard.
     *
     * @return bool
     */
    function is_super_admin(): bool
    {
        return auth()->check() && !auth('employees')->check();
    }
}

if (!function_exists('can_access_all_branches')) {
    /**
     * Check if the current user can access all branches.
     *
     * Returns true for:
     * - Super admins (users table, NOT employees guard)
     * - Users with multi-branch roles (super-admin, md, director, admin)
     *
     * Returns false for:
     * - Regular employees (employees guard users)
     * - Unauthenticated users
     *
     * @return bool
     */
    function can_access_all_branches(): bool
    {
        // Employees can NEVER access all branches
        if (auth('employees')->check()) {
            return false;
        }

        if (!auth()->check()) {
            return false;
        }

        // Check if user is super admin (in users table, not employees guard)
        if (is_super_admin()) {
            return true;
        }

        // Check if user has specific roles (requires spatie/laravel-permission)
        if (method_exists(auth()->user(), 'hasAnyRole')) {
            return auth()->user()->hasAnyRole(['super-admin', 'md', 'director', 'admin']);
        }

        return false;
    }
}

if (!function_exists('get_accessible_branches')) {
    /**
     * Get all branches accessible to the current user.
     *
     * Returns all active branches for super admins, or just the assigned
     * branch for regular employees.
     *
     * @return \Illuminate\Support\Collection
     */
    function get_accessible_branches(): \Illuminate\Support\Collection
    {
        if (can_access_all_branches()) {
            return \App\Models\Branch::where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        if (auth('employees')->check()) {
            $branchId = auth('employees')->user()->branch_id;
            return \App\Models\Branch::where('id', $branchId)->get();
        }

        return collect([]);
    }
}

if (!function_exists('set_current_branch')) {
    /**
     * Set the current branch context.
     *
     * This updates the session with the selected branch ID and
     * optionally updates the user's last accessed branch.
     *
     * @param int $branchId
     * @param bool $updateUserPreference
     * @return void
     */
    function set_current_branch(string $branchId, bool $updateUserPreference = true): void
    {
        session(['selected_branch_id' => $branchId]);

        if ($updateUserPreference && auth()->check() && is_super_admin()) {
            auth()->user()->update(['last_accessed_branch_id' => $branchId]);
        }
    }
}

if (!function_exists('current_branch')) {
    /**
     * Get the current branch model instance.
     *
     * @return \App\Models\Branch|null
     */
    function current_branch(): ?\App\Models\Branch
    {
        $branchId = current_branch_id();

        if (!$branchId) {
            return null;
        }

        return \App\Models\Branch::find($branchId);
    }
}

if (!function_exists('validate_branch_access')) {
    /**
     * Validate that the current user can access the specified branch.
     *
     * @param int $branchId
     * @return bool
     */
    function validate_branch_access(string $branchId): bool
    {
        // Super admins can access all branches
        if (can_access_all_branches()) {
            return true;
        }

        // Regular employees can only access their assigned branch
        if (auth('employees')->check()) {
            return auth('employees')->user()->branch_id === $branchId;
        }

        return false;
    }
}
