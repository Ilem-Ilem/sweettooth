<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuthService;
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
        // Allow Super Admin always
        if (AuthService::isSuperAdmin()) {
            return $next($request);
        }

        // Check permissions
        if (AuthService::user() && AuthService::user()->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403);
    }
}
