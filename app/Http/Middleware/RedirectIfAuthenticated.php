<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if super-admin is logged in (auth()->user())
        if (Auth::check()) {
            return redirect($this->getSuperAdminRedirectUrl(Auth::user()));
        }

        // Check if employee is logged in (auth('employees')->user())
        if (Auth::guard('employees')->check()) {
            $employee = Auth::guard('employees')->user();
            if ($employee->branch_id) {
                return redirect()->route('branch-dashboard.index', ['b_id' => $employee->branch_id]);
            }
        }

        return $next($request);
    }

    /**
     * Get the redirect URL for super-admin.
     */
    private function getSuperAdminRedirectUrl($user): string
    {
        // Try to use last accessed branch
        $branch = Branch::where('id', $user->last_accessed_branch_id)
            ->where('is_active', 1)
            ->first();

        // Fall back to first available branch
        if (!$branch) {
            $branch = Branch::where('is_active', 1)
                ->orderBy('created_at')
                ->first();
        }

        // If branch exists, redirect to branch-dashboard with b_id
        if ($branch) {
            return route('branch-dashboard.index', ['b_id' => $branch->id]);
        }

        // Fallback if no branches available
        return route('branch-select');
    }
}
