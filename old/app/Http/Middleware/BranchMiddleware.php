<?php
namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $branchId = $request->query('b_id');

        // If no b_id param
        if (! $branchId) {
            abort(404, 'Page not found');
        }

        // Validate branch by UUID
        $branch = Branch::where('id', $branchId)->first();

        if (! $branch) {
            abort(404, 'Page not found');
        }


        return $next($request);
    }
}
