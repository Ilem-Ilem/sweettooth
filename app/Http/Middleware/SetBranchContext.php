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
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Super-admin (regular auth, NOT employee guard)
        if (auth()->check() && !auth('employees')->check()) {
            $this->setSuperAdminBranchContext();           // <-- **keeps session logic**
            $redirect = $this->ensureBranchSlugInUrl($request);
            if ($redirect) {
                return $redirect;
            }
        }
        // 2. Regular employee – unchanged
        elseif (auth('employees')->check()) {
            $this->setEmployeeBranchContext();
        }

        return $next($request);
    }

    /* --------------------------------------------------------------------- *
     *  ORIGINAL SESSION-BASED LOGIC (unchanged – you asked to keep it)
     * --------------------------------------------------------------------- */
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
                $firstBranch = Branch::where('is_active', 1)
                    ->orderBy('name')
                    ->first();

                session(['selected_branch_id' => $firstBranch?->id]);
            }
        }
    }

    /* --------------------------------------------------------------------- *
     *  NEW: Redirect to URL with branch slug if missing
     * --------------------------------------------------------------------- */
    protected function ensureBranchSlugInUrl(Request $request): ?Response
    {
        $branchId = session('selected_branch_id');

        // No branch selected yet – nothing to redirect to
        if (!$branchId) {
            return null;
        }

        $branch = Branch::select('id')
            ->where('id', $branchId)
            ->where('is_active', 1)
            ->first();

        // Fallback if the stored branch disappeared
        if (!$branch) {
            $branch = Branch::where('is_active', 1)
                ->orderBy('name')
                ->first();

            if ($branch) {
                session(['selected_branch_id' => $branch->id]);
            } else {
                return null;
            }
        }

        // Check if the current route already contains the correct slug
        $currentSlug = $request->route('branch_slug'); // <-- adjust to your route param name

        if ($currentSlug === $branch->slug) {
            return null; // URL already correct
        }

        // Build URL with the correct slug
        $newUrl = $this->buildUrlWithBranchSlug($request, $branch->slug);

        return redirect($newUrl, 302);
    }

    /* --------------------------------------------------------------------- *
     *  Helper: rebuild URL with branch slug
     * --------------------------------------------------------------------- */
    protected function buildUrlWithBranchSlug(Request $request, string $slug): string
    {
        $routeName = $request->route()?->getName();

        if ($routeName) {
            // Preserve all existing route parameters
            $params = $request->route()->parameters();
            $params['branch_slug'] = $slug;

            return route($routeName, $params);
        }

        // Fallback for non-named routes – replace first segment
        $uri    = $request->getPathInfo();
        $query  = $request->server('QUERY_STRING');
        $segments = explode('/', trim($uri, '/'));
        $segments[0] = $slug;
        $newPath = '/' . implode('/', $segments);

        return $query ? $newPath . '?' . $query : $newPath;
    }

    /* --------------------------------------------------------------------- *
     *  EMPLOYEE (unchanged)
     * --------------------------------------------------------------------- */
    protected function setEmployeeBranchContext(): void
    {
        $employee = auth('employees')->user();

        if ($employee && $employee->branch_id) {
            session(['selected_branch_id' => $employee->branch_id]);

            if ($employee->department_id) {
                session(['selected_department_id' => $employee->department_id]);
            }
        }
    }
}