<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountingMiddleware
{
    /**
     * Handle an incoming request for accounting access
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has accounting role
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has accounting-related roles
        if (!$this->hasAccountingAccess(auth()->user())) {
            abort(403, 'Unauthorized access to accounting system');
        }

        // Log access
        \Log::info('Accounting system access', [
            'user_id' => auth()->id(),
            'path' => $request->path(),
            'method' => $request->method(),
        ]);

        return $next($request);
    }

    /**
     * Check if user has accounting access
     */
    private function hasAccountingAccess($user): bool
    {
        // Check if user has any accounting role
        $accountingRoles = [
            'Accountant',
            'Accounting Manager',
            'accountant',
            'accounting_manager',
            'finance_director',
            'cfo',
        ];

        // If using Spatie Roles
        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($accountingRoles);
        }

        // Fallback check
        return true; // Allow all authenticated users (customize as needed)
    }
}
