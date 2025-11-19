<?php

namespace App\Providers;

use App\Helpers\RolePermission;
use App\Models\Department;
use App\Observers\DepartmentObserver;
use App\Observers\SalesPageObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Department::observe(DepartmentObserver::class);
        Department::observe(SalesPageObserver::class);

        Auth::macro('employee', function () {
            return Auth::guard('employees')->user();
        });

        // Register custom Blade directives for role/permission checks
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // Role directives
        Blade::if('role', fn (string $role, ?string $guard = null) => RolePermission::hasRole($role, $guard));
        Blade::if('anyrole', fn (array $roles, ?string $guard = null) => RolePermission::hasAnyRole($roles, $guard));
        Blade::if('allroles', fn (array $roles, ?string $guard = null) => RolePermission::hasAllRoles($roles, $guard));
        Blade::if('unlessrole', fn (string $role, ?string $guard = null) => ! RolePermission::hasRole($role, $guard));

        // Permission directives
        Blade::if('permission', fn (string $permission, ?string $guard = null) => RolePermission::hasPermission($permission, $guard));
        Blade::if('anypermission', fn (array $permissions, ?string $guard = null) => RolePermission::hasAnyPermission($permissions, $guard));
        Blade::if('allpermissions', fn (array $permissions, ?string $guard = null) => RolePermission::hasAllPermissions($permissions, $guard));
        Blade::if('unlesspermission', fn (string $permission, ?string $guard = null) => ! RolePermission::hasPermission($permission, $guard));

        // Convenience directives
        Blade::if('superadmin', fn (?string $guard = null) => RolePermission::isSuperAdmin($guard));
        Blade::if('admin', fn (?string $guard = null) => RolePermission::isAdmin($guard));
        Blade::if('manager', fn (?string $guard = null) => RolePermission::isManager($guard));
        Blade::if('supervisor', fn (?string $guard = null) => RolePermission::isSupervisor($guard));
        Blade::if('staff', fn (?string $guard = null) => RolePermission::isStaff($guard));
        Blade::if('rolelevel', fn (int $minLevel, ?string $guard = null) => RolePermission::hasRoleLevel($minLevel, $guard));
        Blade::if('canmanage', fn ($targetUser, ?string $guard = null) => RolePermission::canManageUser($targetUser, $guard));
        Blade::if('module', fn (string $module, ?string $guard = null) => RolePermission::canAccessModule($module, $guard));
    }
}
