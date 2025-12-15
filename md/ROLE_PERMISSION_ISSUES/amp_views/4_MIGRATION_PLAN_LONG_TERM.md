# Long-Term Migration Plan - Unified Authentication System

## Executive Summary

Consolidate the dual-guard authentication system (`web` + `employees`) into a single `users` table with role-based access control using `spatie/laravel-permission`.

**Timeline:** 5-6 weeks  
**Effort:** High  
**Risk:** Medium  
**Benefit:** Significantly improved maintainability, security, and flexibility

---

## Pre-Migration Checklist

- [ ] Immediate fix deployed and stable (minimum 1 week)
- [ ] All tests passing
- [ ] Database backups created
- [ ] Team aligned on timeline
- [ ] Feature freeze period scheduled
- [ ] Rollback plan documented

---

## Phase 1: Database Preparation (Week 1)

### 1.1 Backup Current State

```bash
# Create full backup
mysqldump -u root -p sweettooth > backup_$(date +%Y%m%d_%H%M%S).sql

# Also create migrations backup
git checkout -b pre-migration-backup
git add database/migrations/
git commit -m "Backup migrations before user consolidation"
```

### 1.2 Create Migration: Add Columns to Users Table

**File:** `database/migrations/[timestamp]_add_columns_to_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Link user to a branch (nullable for super admins)
            $table->uuid('branch_id')->nullable()->after('id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');

            // Track if user is active
            $table->boolean('is_active')->default(true)->after('branch_id');

            // Employee-specific fields (moved from employees table)
            $table->string('employee_id')->nullable()->unique()->after('is_active');
            $table->timestamp('hired_at')->nullable()->after('employee_id');
            $table->enum('employment_status', ['active', 'suspended', 'terminated', 'on_leave'])
                ->default('active')
                ->after('hired_at');

            // Track user type for data integrity during transition
            $table->enum('user_type', ['admin', 'employee'])->default('admin')->after('employment_status');

            // Soft deletes for safety
            $table->softDeletes()->after('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['branch_id']);
            $table->dropColumn([
                'branch_id',
                'is_active',
                'employee_id',
                'hired_at',
                'employment_status',
                'user_type',
                'deleted_at',
            ]);
        });
    }
};
```

### 1.3 Create Migration: Create Role-Permission Assignment Tables

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Note: spatie/laravel-permission package should handle this,
     * but if migrating manually, use these schemas.
     */
    public function up(): void
    {
        // Already created by spatie, but shown here for reference
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->morphs('model');
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->morphs('model');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
```

---

## Phase 2: Data Migration (Week 2) - CRITICAL: Authentication System Handling

### 2.1 Create Migration: Migrate Employee Data to Users

**File:** `database/migrations/[timestamp]_migrate_employees_to_users.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // Copy employee data into users table
            DB::statement("
                INSERT INTO users (
                    id,
                    name,
                    email,
                    password,
                    branch_id,
                    employee_id,
                    hired_at,
                    employment_status,
                    user_type,
                    is_active,
                    email_verified_at,
                    created_at,
                    updated_at
                )
                SELECT
                    e.id,
                    e.name,
                    e.email,
                    e.password,
                    e.branch_id,
                    e.employee_id,
                    e.hired_at,
                    e.employment_status,
                    'employee' as user_type,
                    e.is_active,
                    e.email_verified_at,
                    e.created_at,
                    e.updated_at
                FROM employees e
                WHERE NOT EXISTS (SELECT 1 FROM users u WHERE u.id = e.id)
            ");

            // Mark existing users as admins
            DB::table('users')
                ->whereNull('user_type')
                ->update(['user_type' => 'admin']);
        });
    }

    public function down(): void
    {
        // Remove all migrated employees from users table
        DB::table('users')
            ->where('user_type', 'employee')
            ->delete();
    }
};
```

### 2.2 Create Migration: Assign Default Roles

**File:** `database/migrations/[timestamp]_assign_default_roles.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // Create roles
            $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
            $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
            $branchManager = Role::firstOrCreate(['name' => 'branch-manager', 'guard_name' => 'web']);
            $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);
            $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

            // Assign roles based on user_type and current permissions
            // This is example logic - adapt to your actual structure

            // All admins → super-admin role
            DB::table('users')
                ->where('user_type', 'admin')
                ->get()
                ->each(function ($user) use ($superAdmin) {
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id' => $superAdmin->id,
                        'model_id' => $user->id,
                        'model_type' => 'App\Models\User',
                    ]);
                });

            // All employees → employee role (with potential branch-manager based on existing permissions)
            DB::table('users')
                ->where('user_type', 'employee')
                ->get()
                ->each(function ($user) use ($employee, $branchManager) {
                    // Start with basic employee role
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id' => $employee->id,
                        'model_id' => $user->id,
                        'model_type' => 'App\Models\User',
                    ]);

                    // Check if they had manager permissions in old system
                    // This logic depends on how old roles were stored
                    // if ($user->is_manager) { ... assign branch-manager role ... }
                });
        });
    }

    public function down(): void
    {
        DB::table('model_has_roles')->delete();
        Role::truncate();
        Permission::truncate();
    }
};
```

### 2.3 CRITICAL: Understand Dual Authentication During This Phase

**IMPORTANT:** After this phase, BOTH authentication systems coexist and work simultaneously.

#### Current Architecture (Before Migration)
```
Admin Login Form
  └─ Checks: users table
     └─ Guard: 'web'
     └─ Routes to: Super Admin Dashboard

Staff Login Form
  └─ Checks: employees table
     └─ Guard: 'employees'
     └─ Routes to: Branch Dashboard
```

#### During Migration (Week 2)
```
Admin Login Form
  └─ Still checks: users table ✓
     └─ Guard: 'web' ✓
     └─ Works as before ✓

Staff Login Form
  └─ Can check EITHER:
     A) employees table (old - still exists)
     B) users table (new - newly populated)
     └─ Both paths work ✓

Result: Both authentication systems fully functional
```

#### Why This Matters
- Users don't get locked out
- Can test new system while old one runs
- Easy rollback if problems
- Gradual switchover possible
- No forced migration date

### 2.4 Verify Data Integrity - CRITICAL CHECKS

Before running migrations, perform these checks:

**Step 1: Check for Email Conflicts**
```bash
php artisan tinker

# Check if any employee emails already exist in users table
> $conflicts = DB::table('users')
    ->whereIn('email', DB::table('employees')->pluck('email'))
    ->get();
> $conflicts->count()  // Should be 0 (no conflicts)
> if ($conflicts->count() > 0) { $conflicts->pluck('email')->dump(); }
```

**Step 2: Check Password Integrity**
```bash
# Ensure no NULL passwords
> App\Models\Employee::whereNull('password')->count()  // Should be 0
> App\Models\User::whereNull('password')->count()  // Should be 0
```

**Step 3: Run Migration**
```bash
php artisan migrate
```

**Step 4: Verify After Migration**
```bash
php artisan tinker

# Verify counts match
> $oldEmployees = App\Models\Employee::count();
> $newEmployees = App\Models\User::where('user_type', 'employee')->count();
> echo "Old table: $oldEmployees, New table: $newEmployees";
> $oldEmployees === $newEmployees  // Should be true

# Verify passwords match (spot check)
> $oldEmp = App\Models\Employee::first();
> $newEmp = App\Models\User::find($oldEmp->id);
> $oldEmp->password === $newEmp->password  // Should be true

# Verify all emails migrated
> $oldEmails = App\Models\Employee::pluck('email')->sort();
> $newEmails = App\Models\User::where('user_type', 'employee')->pluck('email')->sort();
> $oldEmails->equals($newEmails)  // Should be true

# Verify branch assignments
> $branchlesEmployees = App\Models\User::where('user_type', 'employee')->whereNull('branch_id')->count();
> echo "Employees without branch: $branchlesEmployees";  // Should be 0
```

**Step 5: Both Login Systems Still Work**
```bash
# Test admin login (uses users table)
php artisan tinker
> auth()->attempt(['email' => 'admin@example.com', 'password' => 'password'])
> auth()->user()  // Should return User instance
> auth()->user()->user_type  // Should be 'admin'

# Test employee login (uses employees table - still works)
> auth('employees')->attempt(['email' => 'staff@example.com', 'password' => 'password'])
> auth('employees')->user()  // Should return Employee instance

# Test employee login via users table (new path - also works)
> auth()->attempt(['email' => 'staff@example.com', 'password' => 'password'])
> auth()->user()  // Should return User instance (the migrated copy)
> auth()->user()->user_type  // Should be 'employee'
```

**Step 6: Create Verification Report**
```bash
# Generate report for safety
php artisan tinker

$report = [
    'old_employees_count' => App\Models\Employee::count(),
    'new_employees_count' => App\Models\User::where('user_type', 'employee')->count(),
    'admins_in_users' => App\Models\User::where('user_type', 'admin')->count(),
    'null_passwords' => App\Models\User::whereNull('password')->count(),
    'employees_without_branch' => App\Models\User::where('user_type', 'employee')->whereNull('branch_id')->count(),
    'timestamp' => now(),
];

\Log::info('Migration Verification Report', $report);
\File::put(storage_path('migration_report.json'), json_encode($report, JSON_PRETTY_PRINT));

echo "Report saved to: storage/migration_report.json";
```

### 2.5 Document Both Authentication Paths

Create documentation showing that both still work:

**File:** Create `docs/MIGRATION_PHASE2_DUAL_AUTH.md`

```markdown
# Phase 2: Dual Authentication System (Week 2)

## Current Status After Phase 2 Migration

Both authentication systems are fully operational:

### Path 1: Admin Login (Original)
```
users table (web guard)
  └─ Admin users with user_type='admin'
  └─ /login (admin route)
  └─ auth()->check()
  └─ is_super_admin() still works
```

### Path 2: Staff Login (Original)
```
employees table (employees guard)
  └─ Staff with credentials in employees table
  └─ /login (staff route)
  └─ auth('employees')->check()
  └─ is_employee() still works
```

### Path 3: Staff Login (New)
```
users table (web guard)
  └─ Staff users with user_type='employee'
  └─ /login (staff route)
  └─ Can authenticate against users table
  └─ auth()->check() works for employees too
```

## Both Systems Coexist

During Week 2-3, you have THREE authentication paths available:

| Path | Source | Guard | Status | Route To |
|------|--------|-------|--------|----------|
| Admin Login | users (admin) | web | Original ✓ | Super Admin Dashboard |
| Staff Login (Old) | employees | employees | Original ✓ | Branch Dashboard |
| Staff Login (New) | users (employee) | web | New ✓ | Branch Dashboard |

All three work simultaneously. Staff can use either old or new login.

## Testing Both Paths

```bash
# Test Admin (original)
curl -X POST /login -d "email=admin@test.com&password=123"

# Test Staff with Old Auth (original)
curl -X POST /login -d "email=staff@test.com&password=123" # Uses employees table

# Test Staff with New Auth (new)
curl -X POST /login -d "email=staff@test.com&password=123" # Uses users table
```

## Rollback Procedure

If issues found in Week 2-3, simply:
1. Delete migrated employees from users table: `DELETE FROM users WHERE user_type='employee'`
2. Old employees table still has all data
3. Old authentication paths resume: Back to normal
4. No data loss, complete rollback capability
```

### 2.6 Verify Migration

```bash
# Run migrations
php artisan migrate

# Run the verification steps from 2.4 above
# Follow the detailed checklist

# Verify both login paths work
php artisan test tests/Feature/AuthenticationMigrationTest.php
```

---

## Phase 3: Guard & Model Updates (Week 2-3) - Login System Transition

### CRITICAL: During This Phase, Update Login System

**Timeline:**
- **Week 2 (Start):** Both authentication systems fully operational
- **Week 2-3:** Gradually update login controller to use unified path
- **Week 3 (End):** Remove employees guard, single guard only

#### 3.0 Update Login Controller - Transition Path

**File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

**Current Implementation (Before Migration):**
```php
<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * 
     * Currently uses dual guards:
     * - Admin login: users table (web guard)
     * - Staff login: employees table (employees guard)
     */
    public function store(Request $request)
    {
        // Try admin login first (users table, web guard)
        if (auth()->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            return redirect('super-admin');
        }

        // Try staff login (employees table, employees guard)
        if (auth('employees')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            return redirect('branch-dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }
}
```

**Transition Implementation (During Phase 3, Week 2-3):**
```php
<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * 
     * TRANSITION PHASE: Supports both old and new authentication paths
     * - Admin login: users table (web guard) - ORIGINAL
     * - Staff login OLD: employees table (employees guard) - ORIGINAL
     * - Staff login NEW: users table (web guard) - NEW
     */
    public function store(Request $request)
    {
        // PRIMARY: Try unified users table (works for both admin and staff)
        if (auth()->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            
            // Route based on user_type (new system)
            $user = auth()->user();
            if ($user->user_type === 'admin') {
                return redirect('super-admin');
            } else {
                return redirect('branch-dashboard');
            }
        }

        // FALLBACK: Try employees table for backwards compatibility
        // Remove this after all staff migrated and confirmed working
        if (auth('employees')->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            return redirect('branch-dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }
}
```

**Final Implementation (After Phase 3, Week 4+):**
```php
<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     * 
     * FINAL SYSTEM: Single guard, role-based routing
     * - All users authenticate via 'web' guard (users table)
     * - Routing based on roles/user_type
     */
    public function store(Request $request)
    {
        if (auth()->attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();
            
            // Route based on role (proper RBAC)
            if (auth()->user()->hasRole('super-admin')) {
                return redirect('super-admin');
            } else {
                return redirect('branch-dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ]);
    }
}
```

#### 3.0b: Update Logout Similarly

**File:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

**Current:**
```php
public function destroy(Request $request)
{
    // Logout from all guards
    auth()->logout();
    auth('employees')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

**Transition:**
```php
public function destroy(Request $request)
{
    // Logout from both guards (for backwards compatibility)
    auth()->logout();
    auth('employees')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

**Final:**
```php
public function destroy(Request $request)
{
    // Only one guard now
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
```

### 3.1 Update config/auth.php

**Before:**
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'employees' => [
        'driver' => 'session',
        'provider' => 'employees',
    ],
],
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'employees' => [
        'driver' => 'eloquent',
        'model' => App\Models\Employee::class,
    ],
],
```

**After:**
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    // Remove 'employees' guard entirely
],
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    // Remove 'employees' provider
],
```

### 3.2 Update User Model

**File:** `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'employee_id',
        'hired_at',
        'employment_status',
        'user_type',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'branch_id' => 'string',
        'hired_at' => 'datetime',
    ];

    // Set guard for spatie/laravel-permission
    protected $guard_name = 'web';

    /**
     * Get the branch this user belongs to
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Check if user is an employee (not admin)
     */
    public function isEmployee(): bool
    {
        return $this->user_type === 'employee';
    }

    /**
     * Get user's accessible branches
     */
    public function getAccessibleBranches()
    {
        if ($this->isSuperAdmin()) {
            return Branch::where('is_active', 1)->get();
        }
        if ($this->branch_id) {
            return Branch::where('id', $this->branch_id)->get();
        }
        return collect([]);
    }

    /**
     * Check if user can access a branch
     */
    public function canAccessBranch(string $branchId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->branch_id === $branchId;
    }
}
```

### 3.3 Create Employee Facade (Optional)

For backwards compatibility, optionally create:

**File:** `app/Models/Employee.php`

```php
<?php

namespace App\Models;

/**
 * Employee Model - Facade for backwards compatibility
 * 
 * After migration, employees are stored in the users table.
 * This class provides a backwards-compatible interface.
 */
class Employee extends User
{
    protected $table = 'users';

    protected static function boot()
    {
        parent::boot();
        
        // Filter to only employees
        static::addGlobalScope('employees', function ($query) {
            $query->where('user_type', 'employee');
        });
    }

    public static function create(array $attributes = [])
    {
        $attributes['user_type'] = 'employee';
        return parent::create($attributes);
    }
}
```

---

## Phase 4: Code Refactoring (Weeks 3-4)

### 4.1 Update Authentication Helpers

Create new helpers that use single guard:

**File:** `app/Helpers/AuthHelper.php`

```php
<?php

use Illuminate\Support\Facades\Auth;

/**
 * Unified Authentication Helper Functions
 * 
 * After migration to single-guard system with RBAC
 */

if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }
}

if (!function_exists('is_employee')) {
    function is_employee(): bool
    {
        return auth()->check() && auth()->user()->isEmployee();
    }
}

if (!function_exists('get_current_user')) {
    function get_current_user()
    {
        return auth()->user();
    }
}

if (!function_exists('get_user_branch_id')) {
    function get_user_branch_id(): ?string
    {
        return auth()->user()?->branch_id;
    }
}

if (!function_exists('validate_branch_access')) {
    function validate_branch_access(string $branchId): bool
    {
        return auth()->user()?->canAccessBranch($branchId) ?? false;
    }
}
```

### 4.2 Update Middleware

**IsAdmin.php:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()?->isSuperAdmin()) {
            abort(403, 'Super admin access required');
        }
        return $next($request);
    }
}
```

**SuperAdminOrPermission.php:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminOrPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (auth()->user()?->isSuperAdmin()) {
            return $next($request);
        }

        if (auth()->user()?->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403);
    }
}
```

**BranchMiddleware.php:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Branch;

class BranchMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $b_id = $request->query('b_id');

        // Super admins: default to first branch if none specified
        if (auth()->user()?->isSuperAdmin() && empty($b_id)) {
            $b_id = Branch::where('is_active', 1)->first()?->id;
        }

        // Employees must provide branch
        if (!auth()->user()?->isSuperAdmin() && empty($b_id)) {
            abort(403, 'Branch parameter required');
        }

        // Validate existence and access
        if (!auth()->user()?->canAccessBranch($b_id)) {
            abort(403, 'Branch access denied');
        }

        // Set branch context
        app()->instance('currentBranch', Branch::find($b_id));

        return $next($request);
    }
}
```

### 4.3 Search & Replace in Codebase

Replace all occurrences:

```bash
# Find all uses of auth('employees')
grep -r "auth('employees')" app/ --include="*.php" | wc -l

# Replace auth('employees')->check() with auth()->check() && auth()->user()->isEmployee()
find app/ -name "*.php" -type f -exec sed -i "s/auth('employees')->check()/auth()->check() && auth()->user()->isEmployee()/g" {} \;

# Replace auth('employees')->user() with auth()->user()
find app/ -name "*.php" -type f -exec sed -i "s/auth('employees')->user()/auth()->user()/g" {} \;

# Remove Auth::guard('employees') usages
grep -r "Auth::guard('employees')" app/ --include="*.php"
```

### 4.4 Update Blade Templates

```blade
{{-- Before --}}
@if(auth('employees')->check())
    <p>Employee view</p>
@endif

{{-- After --}}
@if(auth()->user()?->isEmployee())
    <p>Employee view</p>
@endif

{{-- Before --}}
@can('view-inventory')
    <button>View Inventory</button>
@endcan

{{-- After (no change - spatie directive works the same) --}}
@can('view-inventory')
    <button>View Inventory</button>
@endcan
```

---

## Phase 5: Testing & Deprecation (Week 5)

### 5.1 Create Comprehensive Test Suite

**File:** `tests/Feature/AuthenticationTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_all_branches()
    {
        $admin = User::factory()->create(['user_type' => 'admin']);
        $admin->assignRole('super-admin');
        $branch = Branch::factory()->create();

        $this->actingAs($admin)
            ->get("/branch-dashboard?b_id={$branch->id}")
            ->assertSuccessful();
    }

    public function test_employee_can_only_access_assigned_branch()
    {
        $employee = User::factory()->create([
            'user_type' => 'employee',
            'branch_id' => $branchA = Branch::factory()->create()->id,
        ]);
        $employee->assignRole('employee');
        
        $branchB = Branch::factory()->create();

        // Can access assigned branch
        $this->actingAs($employee)
            ->get("/branch-dashboard?b_id={$branchA}")
            ->assertSuccessful();

        // Cannot access other branch
        $this->actingAs($employee)
            ->get("/branch-dashboard?b_id={$branchB->id}")
            ->assertForbidden();
    }

    public function test_is_super_admin_helper_works()
    {
        $admin = User::factory()->create()->assignRole('super-admin');
        $employee = User::factory()->create()->assignRole('employee');

        $this->actingAs($admin);
        $this->assertTrue(is_super_admin());

        $this->actingAs($employee);
        $this->assertFalse(is_super_admin());
    }
}
```

### 5.2 Run Full Test Suite

```bash
php artisan test

# If issues found, rollback
git revert HEAD~5
```

### 5.3 Gradual Deprecation

1. Deploy with both systems working
2. Monitor logs for old guard usage
3. Send deprecation notices to developers
4. Remove old Employee model references gradually
5. Remove old guards after 2-3 weeks

---

## Rollback Plan - Phase-Specific Procedures

### Why Rollbacks Are Safe

Because both authentication systems coexist during migration, you have multiple safe rollback points:

```
Phase 2 (Data Migrated):  Both systems work ✓ → Easy rollback
Phase 3 (Guard Updated):  Both systems work ✓ → Medium rollback
Phase 4 (Code Changed):   Single system only → Harder rollback
```

### Rollback During Phase 2 (Data Migration)
**Status:** Both authentication systems fully operational  
**Difficulty:** EASY

```bash
# Simply delete migrated employees from users table
php artisan tinker

> DB::table('users')->where('user_type', 'employee')->delete();

# Verify old employees table still has all data
> App\Models\Employee::count()

# Resume using employees table (employees guard)
# Old authentication paths fully operational
# No data loss - everything in employees table
```

### Rollback During Phase 3 (Guard Updates)
**Status:** Both guards still in config, but login controller updated  
**Difficulty:** MEDIUM

```bash
# Revert authentication controller
git checkout HEAD~N app/Http/Controllers/Auth/AuthenticatedSessionController.php

# Revert config (restore employees guard)
git checkout HEAD~N config/auth.php

# Revert User model
git checkout HEAD~N app/Models/User.php

# Both authentication systems resume
php artisan test tests/Feature/AuthenticationTest.php
```

### Rollback During Phase 4 (Code Refactoring)
**Status:** Code updated, single guard expected  
**Difficulty:** MEDIUM-HARD

```bash
# Revert all code changes
git revert --no-edit [Phase3-commit] [Phase4-commits...]

# Restore config/auth.php (employees guard comes back)
# This reverts:
# - Guard configuration
# - Login controller
# - Middleware
# - Helpers and services

# Verify both systems work again
php artisan test tests/Feature/AuthenticationTest.php
```

### Complete Rollback (Nuclear Option)
**Use if:** Multiple issues or data corruption  
**Difficulty:** MEDIUM

```bash
# Backup current data first
mysqldump -u root -p sweettooth > backup_failed_migration.sql

# Restore full system
mysql -u root -p sweettooth < backup_before_migration.sql

# Code rollback
git checkout [tag-before-migration]

# System completely restored
# All data in original state
# Both authentication systems working as before

# Verify
php artisan test tests/Feature/AuthenticationTest.php
```

### Testing Rollback Procedures (BEFORE PRODUCTION)

On staging environment:

```bash
# Step 1: Perform migration
git checkout refactor/unified-auth
php artisan migrate

# Step 2: Verify new system works
php artisan test tests/Feature/AuthenticationTest.php

# Step 3: Test rollback
git checkout [previous-branch]
mysql -u root -p staging_db < backup_before_migration.sql

# Step 4: Verify old system works again
php artisan test tests/Feature/AuthenticationTest.php

# Both should pass!
```

### Rollback Checklist

- [ ] Both authentication systems documented before starting
- [ ] Database backups created before each phase
- [ ] Git tags created before each phase (for easy reference)
- [ ] Rollback procedures tested on staging
- [ ] Team knows rollback procedures
- [ ] Monitoring in place to detect issues early
- [ ] Gradual user switchover (not forced migration)

### Key Advantage: Dual Auth During Migration

Because both systems coexist for 2-3 weeks:
- ✓ No forced cutover date
- ✓ Easy rollback at any point
- ✓ Can test new system while old one runs
- ✓ Staff gradually migrated
- ✓ Data verified before deleting old table
- ✓ Multiple safe abort points

---

## Post-Migration Cleanup

After successful migration (2+ weeks):

1. Delete old Employee model
2. Remove auth('employees') guard from config/auth.php
3. Remove employee-specific migrations (if desired)
4. Archive old authentication documentation
5. Update team documentation with new system

---

## Success Metrics

After migration, verify:

- [ ] All tests passing
- [ ] No references to `auth('employees')` in codebase
- [ ] All role-based access working correctly
- [ ] Performance similar or improved
- [ ] No security issues identified in code review
- [ ] Team trained on new system

---

## Additional Resources

- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission/v6/introduction)
- [Laravel Authentication](https://laravel.com/docs/11.x/authentication)
- [Database Migrations](https://laravel.com/docs/11.x/migrations)
