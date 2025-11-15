<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetBranchContext
{
    /**
     * Handle an incoming request.
     *
     * Set branch context based on user type:
     * - Super Admin (auth()->user() but NOT auth('employees')): Use session or last accessed branch
     * - Regular Employee (auth('employees')): Use their assigned branch
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is a super admin (not an employee)
        if (auth()->check() && !auth('employees')->check()) {
            $this->setSuperAdminBranchContext();
        }
        // Regular employee - use their branch
        elseif (auth('employees')->check()) {
            $this->setEmployeeBranchContext();
        }

        return $next($request);
    }

    /**
     * Set branch context for super admin users.
     */
    protected function setSuperAdminBranchContext(): void
    {
        // If no branch selected in session, set default
        if (!session()->has('selected_branch_id')) {
            $user = auth()->user();

            // Try to use last accessed branch
            $defaultBranch = $user->last_accessed_branch_id;

            // If no last accessed branch, use first active branch
            if (!$defaultBranch) {
                $firstBranch = Branch::where('is_active', 1)
                    ->orderBy('name')
                    ->first();

                $defaultBranch = $firstBranch?->id;
            }

            if ($defaultBranch) {
                session(['selected_branch_id' => $defaultBranch]);
            }
        }

        // Validate that the selected branch still exists and is active
        $selectedBranch = session('selected_branch_id');
        if ($selectedBranch) {
            $branchExists = Branch::where('id', $selectedBranch)
                ->where('is_active', 1)
                ->exists();

            // If branch no longer exists or is inactive, reset to first available
            if (!$branchExists) {
                $firstBranch = Branch::where('status', 'active')
                    ->orderBy('name')
                    ->first();

                session(['selected_branch_id' => $firstBranch?->id]);
            }
        }
    }

    /**
     * Set branch context for regular employee users.
     */
    protected function setEmployeeBranchContext(): void
    {
        $employee = auth('employees')->user();

        if ($employee && $employee->branch_id) {
            session(['selected_branch_id' => $employee->branch_id]);

            // Also set the selected department if exists
            if ($employee->department_id) {
                session(['selected_department_id' => $employee->department_id]);
            }
        }
    }
}
