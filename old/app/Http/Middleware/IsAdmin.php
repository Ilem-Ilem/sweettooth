<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated
        if (! auth()->check()) {
            abort(403, 'Forbidden');
        }

        // Check if user has required role(s)
        if (! auth()->user()->hasAnyRole(['MD', 'Admin'])) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
