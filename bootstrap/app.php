<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\BranchMiddleware;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetBranchContext;
use App\Http\Middleware\ProtectCoreRoles;
use App\Http\Middleware\RedirectSuperAdminToDashboard;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'isAdmin' => IsAdmin::class,
            'branch'  => BranchMiddleware::class,
            'setBranchContext' => SetBranchContext::class,
            'guest' => RedirectIfAuthenticated::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \App\Http\Middleware\SuperAdminOrPermission::class,
            'protect-roles' => ProtectCoreRoles::class,
            'redirect-super-admin' => RedirectSuperAdminToDashboard::class,
        ]);

        // Apply SetBranchContext to web middleware group
        $middleware->web(append: [
            SetBranchContext::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
