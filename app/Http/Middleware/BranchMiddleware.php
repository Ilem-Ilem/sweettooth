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
     */
      public function handle(Request $request, Closure $next): Response
    {
        $b_id = $request->query('b_id');

        // Validate format first
        $validator = Validator::make(['b_id' => $b_id], [
            'b_id' => ['required', 'uuid', 'exists:branches,id'],
        ]);

        if ($validator->fails()) {
            Log::warning('Blocked request with invalid or missing b_id', [
                'ip' => $request->ip(),
                'b_id' => $b_id,
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Invalid or unauthorized branch access.');
        }

        // Optionally, you can set the branch globally
        $branch = Branch::find($b_id);
        app()->instance('currentBranch', $branch);

        // If your app uses multi-branch access control:
        // if (!auth()->user()->branches->contains($branch)) {
        //     abort(403, 'You are not authorized for this branch.');
        // }

        return $next($request);
    }
}
