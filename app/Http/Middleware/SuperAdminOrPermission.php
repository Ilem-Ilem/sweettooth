<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOrPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,  ...$permissions): Response
    {
        $user = $request->user();

        // Allow Super Admin always
        if ($user && is_super_admin()) {
            return $next($request);
        }

        // Check permissions
        if ($user && $user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403);
        return $next($request);
    }
}
