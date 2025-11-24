<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Branch;

class BranchMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Validates that:
     * 1. The b_id parameter is a valid UUID that exists in the database
     * 2. The current user is authorized to access this branch
     *    - Super admins can access any branch
     *    - Employees can ONLY access their assigned branch
     */
    public function handle(Request $request, Closure $next): Response
    {
        // CRITICAL: Different rules for super admins vs employees
        
        // EMPLOYEES: MUST have b_id in URL query parameter, NO session fallback
        if (auth('employees')->check()) {
            $b_id = $request->query('b_id');
            
            // Employees MUST provide b_id parameter - no exceptions
            if (empty($b_id)) {
                Log::warning('Employee attempted to access branch route without b_id parameter', [
                    'ip' => $request->ip(),
                    'employee_id' => auth('employees')->id(),
                    'url' => $request->fullUrl(),
                ]);
                abort(403, 'Branch parameter required.');
            }
        } else {
            // SUPER ADMINS: Can use URL parameter or fall back to session
            $b_id = $request->query('b_id') ?? current_branch_id();
        }

        // Validate format and existence
        $validator = Validator::make(['b_id' => $b_id], [
            'b_id' => ['required', 'uuid', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            Log::warning('Blocked request with invalid or missing b_id', [
                'ip' => $request->ip(),
                'b_id' => $b_id,
                'user_type' => auth('employees')->check() ? 'employee' : 'super_admin',
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Invalid branch access.');
        }

        // Validate that the user is authorized to access this branch
        if (!validate_branch_access($b_id)) {
            Log::warning('Blocked unauthorized branch access attempt', [
                'ip' => $request->ip(),
                'user_id' => auth()->id() ?? auth('employees')->id(),
                'user_type' => auth('employees')->check() ? 'employee' : 'super_admin',
                'requested_branch' => $b_id,
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'You are not authorized to access this branch.');
        }

        // Set the branch globally for the request
        $branch = Branch::find($b_id);
        app()->instance('currentBranch', $branch);

        return $next($request);
    }
}
