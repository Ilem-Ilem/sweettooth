<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Department;

class ValidateSalesDepartmentContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $salesDeptSlug = $request->route('salesDeptSlug');
        $branchId = $request->query('b_id') ?? current_branch_id();

        if ($salesDeptSlug) {
            $department = Department::where('slug', $salesDeptSlug)
                ->where(function($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                      ->orWhereNull('branch_id');
                })
                ->first();

            if (!$department) {
                Log::warning('Invalid department access attempt', [
                    'slug' => $salesDeptSlug,
                    'branch_id' => $branchId,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);

                abort(404, 'Sales department not found.');
            }

            // Super admins can access any department
            if (is_super_admin() || can_access_all_branches()) {
                $request->merge(['current_department' => $department]);
                return $next($request);
            }

            // Validate employee has access to this department
            $employee = auth()->user();
            if ($employee && $employee->department_id !== $department->id) {
                Log::warning('Unauthorized department access attempt', [
                    'employee_id' => $employee->id,
                    'employee_department' => $employee->department_id,
                    'requested_department' => $department->id,
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl()
                ]);

                abort(403, 'You are not authorized to access this sales department.');
            }

            // Store department context in request for use in controllers/components
            $request->merge(['current_department' => $department]);
        }

        return $next($request);
    }
}
