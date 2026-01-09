# 02 - Models & Relationships

## 🏗️ Model Architecture

The advanced RBAC system extends existing models and adds validation logic while maintaining full compatibility with Spatie Laravel Permission and the existing SweetTooth structure.

## 📋 Extended Existing Models

### 1. DepartmentCategory Model (Minor Extensions)

The existing DepartmentCategory model gets validation helpers:

```php
<?php
// app/Models/DepartmentCategory.php (extended)

class DepartmentCategory extends Model
{
    use HasUuids;

    public $fillable = ['name', 'description'];

    /**
     * Get all departments in this category
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'category_id');
    }

    /**
     * Get role constraints for this category
     */
    public function roleConstraints()
    {
        return $this->hasMany(RoleCategoryConstraint::class, 'category_id');
    }

    /**
     * Check if a role is allowed in this category
     */
    public function allowsRole(string $roleName): bool
    {
        return $this->roleConstraints()
            ->where('role_name', $roleName)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get allowed department types for a role in this category
     */
    public function getRoleConstraints(string $roleName): ?RoleCategoryConstraint
    {
        return $this->roleConstraints()
            ->where('role_name', $roleName)
            ->where('is_active', true)
            ->first();
    }
}
```

### 2. Department Model (Extended)

The existing Department model gets additional fields and validation methods:

```php
<?php
// app/Models/Department.php (extended)

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'category_id',
        'name',
        'slug',
        'description',
        'is_active',           // New field
        'enable_table_management',
        'table_management_settings',
        'manager_user_id'      // New field
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'enable_table_management' => 'boolean',
        'table_management_settings' => 'array',
        'is_active' => 'boolean'  // New cast
    ];

    // ... existing relationships ...

    /**
     * Get the department manager (extended)
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    /**
     * Get users assigned to this department (via user.department_id)
     */
    public function assignedUsers()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Get active assigned users
     */
    public function activeAssignedUsers()
    {
        return $this->assignedUsers()->where('is_active', true);
    }

    /**
     * Check if a role is allowed in this department
     */
    public function allowsRole(string $roleName): bool
    {
        $constraint = $this->category->getRoleConstraints($roleName);

        if (!$constraint) {
            return false; // Role not defined for this category
        }

        return match ($constraint->department_type) {
            'branch_wide' => true,
            'category_wide' => true,
            'specific' => in_array($this->slug, $constraint->allowed_department_slugs ?? []),
        };
    }

    /**
     * Get valid roles for this department
     */
    public function getValidRoles()
    {
        return $this->category->roleConstraints()
            ->where('is_active', true)
            ->get()
            ->filter(function ($constraint) {
                return $this->allowsRole($constraint->role_name);
            })
            ->pluck('role_name');
    }

    // ... existing methods ...

    /**
     * Scope for active departments (extended)
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ... existing scopes ...
}
```

### 3. RoleCategoryConstraint Model (New)

Validates which roles can be used in which category contexts:

```php
<?php
// app/Models/RoleCategoryConstraint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleCategoryConstraint extends Model
{
    use HasFactory;

    protected $table = 'role_category_constraints';

    protected $fillable = [
        'role_name',
        'category_id',
        'department_type',
        'allowed_department_slugs',
        'is_active'
    ];

    protected $casts = [
        'allowed_department_slugs' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the category this constraint applies to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DepartmentCategory::class, 'category_id');
    }

    /**
     * Check if this constraint allows a specific department
     */
    public function allowsDepartment(string $departmentSlug): bool
    {
        return match ($this->department_type) {
            'branch_wide' => true,
            'category_wide' => true,
            'specific' => in_array($departmentSlug, $this->allowed_department_slugs ?? []),
        };
    }

    /**
     * Scope for active constraints
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for constraints of a specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('department_type', $type);
    }
}
```

### 4. No UserDepartmentRole Model Needed

Since we're leveraging the existing `user.department_id` relationship, we don't need a separate assignment table. Role assignments continue to use Spatie's `model_has_roles` table, with validation ensuring roles are appropriate for the user's department.

## 🔄 Extended User Model

Add these methods to the existing User model to enable department-aware role validation:

```php
<?php
// app/Models/User.php (additions)

class User extends Authenticatable
{
    // ... existing code ...

    /**
     * Get the user's department (existing relationship)
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Check if user's current role is valid for their department
     */
    public function validateRoleForDepartment(): bool
    {
        if (!$this->department) {
            return false; // User not assigned to any department
        }

        // Get user's current roles
        $userRoles = $this->roles->pluck('name')->toArray();

        // Check if any role is valid for this department
        foreach ($userRoles as $roleName) {
            if ($this->department->allowsRole($roleName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get valid roles for user's current department
     */
    public function getValidRolesForDepartment()
    {
        if (!$this->department) {
            return collect();
        }

        return $this->department->getValidRoles();
    }

    /**
     * Check if user can be assigned a specific role in their department
     */
    public function canBeAssignedRole(string $roleName): bool
    {
        if (!$this->department) {
            return false;
        }

        return $this->department->allowsRole($roleName);
    }

    /**
     * Get all users in the same department with a specific role
     */
    public function getDepartmentColleaguesWithRole(string $roleName)
    {
        if (!$this->department) {
            return collect();
        }

        return $this->department->assignedUsers()
            ->whereHas('roles', fn($q) => $q->where('name', $roleName))
            ->where('id', '!=', $this->id)
            ->get();
    }

    /**
     * Enhanced isSuperAdmin check (existing in SweetTooth)
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasAnyRole(['Super Admin', 'MD', 'Managing Director', 'Admin']);
    }

    // ... existing code ...
}
```

## 🔗 Model Relationships Summary

### DepartmentCategory Relationships
- `departments()` - HasMany departments
- `roleConstraints()` - HasMany role category constraints

### Department Relationships (Extended)
- `category()` - BelongsTo department category
- `branch()` - BelongsTo branch
- `manager()` - BelongsTo user (manager)
- `assignedUsers()` - HasMany users (via user.department_id)
- `activeAssignedUsers()` - HasMany active users

### User Relationships (Extended)
- `department()` - BelongsTo department (existing)

### RoleCategoryConstraint Relationships
- `category()` - BelongsTo department category

### Key Validation Methods
- `DepartmentCategory::allowsRole()` - Check if role is valid for category
- `Department::allowsRole()` - Check if role is valid for specific department
- `User::validateRoleForDepartment()` - Validate user's roles against their department
- `User::canBeAssignedRole()` - Check if user can be assigned a role

## 🎯 Key Methods

### Authorization Methods
- `hasRoleInDepartment()` - Check role in specific department
- `canAccessDepartment()` - Check department access
- `canAccessCategory()` - Check category access
- `isSuperAdmin()` - Enhanced super admin check

### Management Methods
- `assignToDepartment()` - Assign role in department
- `removeFromDepartment()` - Remove role from department
- `getAccessibleDepartmentsInCategory()` - Get accessible departments

## 📋 Next Steps

With the models and relationships defined, proceed to [03 - Implementation](./03-implementation.md) to understand seeders, middleware, and authorization logic.</content>
<parameter name="filePath">md/advance_RBAS/02-models-relationships.md