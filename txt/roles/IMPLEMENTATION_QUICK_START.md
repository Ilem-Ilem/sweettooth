# Quick Start Implementation Guide

## Execute These Steps to Implement Protected Roles

### Step 1: Create Migration (5 minutes)
```bash
php artisan make:migration add_protection_to_roles
```

Add to the new migration file:
```php
Schema::table('roles', function (Blueprint $table) {
    $table->boolean('is_protected')->default(false)->after('guard_name');
    $table->text('description')->nullable()->after('is_protected');
    $table->integer('display_order')->default(0)->after('description');
});

Schema::table('permissions', function (Blueprint $table) {
    $table->boolean('is_protected')->default(false)->after('guard_name');
    $table->text('description')->nullable()->after('is_protected');
    $table->string('category')->default('general')->after('description');
});
```

Run:
```bash
php artisan migrate
```

### Step 2: Create Service Class (10 minutes)

Copy the `RolePermissionService.php` content from the analysis document to:
```
app/Services/RolePermissionService.php
```

### Step 3: Create Middleware (5 minutes)

Copy the `ProtectCoreRoles.php` content to:
```
app/Http/Middleware/ProtectCoreRoles.php
```

Register in `bootstrap/app.php` or `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ... existing middleware
    'protect-roles' => \App\Http\Middleware\ProtectCoreRoles::class,
];
```

### Step 4: Update Seeders (10 minutes)

Add to `database/seeders/PermissionSeeder.php`:
```php
foreach ($employeePermissions as $category => $permissions) {
    foreach ($permissions as $name => $description) {
        Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'employees'],
            [
                'description' => $description,
                'category' => $category,
                'is_protected' => false,
            ]
        );
    }
}
```

Add to `database/seeders/RoleSeeder.php`:
```php
$managingDirector = Role::firstOrCreate(
    ['name' => 'Managing Director', 'guard_name' => $guard],
    [
        'is_protected' => true,
        'description' => 'Executive level with full operational control. Cannot be deleted.',
        'display_order' => 1,
    ]
);
```

### Step 5: Update Livewire Component (10 minutes)

Replace role deletion logic in `app/Livewire/BranchDashboard/Roles/Index.php`:
```php
public function confirmedDeleteRole(string $message): void
{
    if ($this->selectedRoleId) {
        try {
            RolePermissionService::deleteRole($this->selectedRoleId);
            $this->dialog()->success('Success', 'Role deleted successfully!')->send();
            $this->selectedRoleId = null;
        } catch (\Exception $e) {
            $this->dialog()->error('Error', $e->getMessage())->send();
        }
    }
}
```

### Step 6: Update Routes (5 minutes)

Add middleware to role management routes in `routes/branch-route.php`:
```php
Route::middleware(['auth:web,employees', 'setBranchContext', 'branch', 'protect-roles'])
    ->prefix('branch-dashboard')
    ->name('branch-dashboard.')
    ->group(function () {
        Route::get('roles', \App\Livewire\BranchDashboard\Roles\Index::class)->name('roles.index');
        // ... other role routes
    });
```

### Step 7: Run Database Update

```bash
php artisan migrate
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
```

### Step 8: Test

```bash
# Test as non-super-admin - should get 403
php artisan tinker
>>> $emp = Employee::where('name', 'Test')->first();
>>> $emp->hasRole('Super Admin') // Should be false

# Test deleting protected role - should throw exception
try {
    RolePermissionService::deleteRole(1); // If ID 1 is protected
} catch (Exception $e) {
    echo $e->getMessage(); // "Cannot delete protected role"
}
```

---

## Verification Checklist

- [ ] Migration runs without errors
- [ ] `roles` table has `is_protected`, `description`, `display_order` columns
- [ ] `permissions` table has `is_protected`, `description`, `category` columns
- [ ] RolePermissionService is in `app/Services/`
- [ ] ProtectCoreRoles middleware is in `app/Http/Middleware/`
- [ ] Middleware is registered in app
- [ ] Routes have middleware applied
- [ ] Seeds run successfully
- [ ] Super Admin role has `is_protected = true`
- [ ] Managing Director role has `is_protected = true`
- [ ] Attempting to delete protected role throws exception
- [ ] Role deletion is logged

---

## Testing Commands

```bash
# Check protected roles
php artisan tinker
>>> DB::table('roles')->where('is_protected', true)->get();

# Test deletion prevention
>>> RolePermissionService::deleteRole(1); // Should fail if protected

# Clear cache after changes
>>> RolePermissionService::clearCache();

# View role logs
>>> DB::table('audit_logs')->where('model_type', 'Spatie\\Permission\\Models\\Role')->latest()->take(10)->get();
```

---

## Expected Outcome

After implementation:

1. ✅ Core roles (Super Admin, Managing Director, Admin) cannot be deleted
2. ✅ Core roles cannot be modified without super admin access
3. ✅ All role/permission changes are logged
4. ✅ System cannot collapse from role deletion
5. ✅ Clear audit trail of who changed what
6. ✅ Route-level access control enforcement
7. ✅ Better permission organization
8. ✅ Protected permissions cannot be deleted

