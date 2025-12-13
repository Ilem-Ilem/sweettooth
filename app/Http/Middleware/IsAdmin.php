<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuthService;

class IsAdmin
{
    /**
     * Handle an incoming request.
     * Only super admins can pass
     */
    public function handle(Request $request, Closure $next): Response
    {
        AuthService::requireSuperAdmin();
        return $next($request);
    }
}
