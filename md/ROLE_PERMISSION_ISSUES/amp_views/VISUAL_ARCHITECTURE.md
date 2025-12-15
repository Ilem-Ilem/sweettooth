# Visual Architecture Guide

## Current Architecture (Problem)

```
┌─────────────────────────────────────────────────────────────────┐
│                        REQUEST                                  │
└─────────────────────┬───────────────────────────────────────────┘
                      │
        ┌─────────────┴──────────────┐
        │                            │
    ┌───▼──────┐           ┌────────▼────┐
    │ BranchHelper.php    │AuthService.php│
    │                     │              │
    │ is_super_admin():   │isSuperAdmin()│
    │   auth() &&         │  Auth::guard  │
    │   !auth('emp')      │  ('web') &&   │
    │                     │  !Auth::guard │
    │                     │  ('employees')│
    └────┬────────────────┴────────┬─────┘
         │         ✗ INCONSISTENT │
         │         ✗ FRAGILE      │
         │         ✗ RISKY        │
         │                        │
    ┌────▼──────┐  ┌──────────┐  ┌─────────────┐
    │IsAdmin MW │  │SuperAdminOr│BranchMiddleware
    │           │  │Permission  │
    │Uses:      │  │Uses:       │Uses:
    │AuthService│  │AuthService │is_super_admin()
    │::isSuperAd│  │::isSuperAd │auth('employees')
    │min()      │  │min()       │validate_branch()
    └───────────┘  └──────────┘  └──────────────┘
         │              │              │
         │              │              │
         └──────┬───────┴──────┬───────┘
                │              │
         ┌──────▼──────────────▼─────┐
         │  DATABASE / BUSINESS LOGIC │
         └────────────────────────────┘
```

**Problem:** Multiple definitions that could diverge

---

## After Immediate Fix (Solution 1)

```
┌─────────────────────────────────────────────────────────────────┐
│                        REQUEST                                  │
└─────────────────────┬───────────────────────────────────────────┘
                      │
    ┌─────────────────▼──────────────────┐
    │  AuthorizationHelper.php           │
    │  (SINGLE SOURCE OF TRUTH)          │
    │                                    │
    │  ✓ is_super_admin()               │
    │  ✓ is_employee()                  │
    │  ✓ get_current_user()             │
    │  ✓ get_user_branch_id()           │
    │  ✓ validate_branch_access()       │
    │  ✓ can_access_all_branches()      │
    │  ✓ get_accessible_branches()      │
    │  ✓ set_current_branch()           │
    │  ✓ current_branch()               │
    │  ✓ current_actor()                │
    └────────┬──────────────────────────┘
             │
    ┌────────┼────────┬──────────────┬──────────────────┐
    │        │        │              │                  │
┌───▼──┐ ┌──▼──┐ ┌───▼───┐ ┌────────▼──┐ ┌────────────▼─┐
│Branch│ │Auth │ │IsAdmin│ │SuperAdmin-│ │BranchMiddle- │
│Helper│ │Serv.│ │Middle │ │OrPermission│ │ware          │
│      │ │     │ │ware   │ │Middleware  │ │              │
│Uses: │ │Uses:│ │       │ │           │ │Uses:         │
│is_su │ │is_s │ │Uses:  │ │Uses:      │ │is_super_admin│
│per_ad│ │uper │ │is_sup │ │is_super_a │ │is_employee() │
│min() │ │_adm │ │er_adm │ │dmin()     │ │validate_     │
│etc   │ │in() │ │in()   │ │hasAnyPerm │ │branch_access │
└──────┘ └─────┘ └───────┘ └───────────┘ └──────────────┘
    │        │        │              │                  │
    └────────┴────────┴──────────────┴──────────────────┘
             │
    ┌────────▼───────────────────┐
    │  DATABASE / BUSINESS LOGIC  │
    └─────────────────────────────┘
```

**Benefit:** Single definition, all components use it

---

## After Long-Term Migration (Solution 2)

```
┌────────────────────────────────────────────────────────┐
│                    REQUEST                             │
└────────┬─────────────────────────────────────────────┬─┘
         │                                             │
    ┌────▼──────────────────┐        ┌────────────────▼──┐
    │  auth()->user()        │        │  auth()->user()   │
    │  (Single Guard:        │        │  ->hasRole()      │
    │   'web')               │        │  (spatie)         │
    │                        │        │                   │
    │  Returns:              │        │  Returns: bool    │
    │  User|Employee model   │        │                   │
    └────┬──────────────────┘        └────────┬──────────┘
         │                                     │
    ┌────▼──────────────────┐        ┌────────▼──────────┐
    │  User Model Methods:   │        │  Role Definitions:│
    │                        │        │                   │
    │  isSuperAdmin()        │        │  super-admin      │
    │  isEmployee()          │        │  admin            │
    │  canAccessBranch($id)  │        │  branch-manager   │
    │  branch()              │        │  supervisor       │
    │                        │        │  employee         │
    └────┬──────────────────┘        └────────┬──────────┘
         │                                     │
    ┌────▼─────────────────────────────────────▼─────┐
    │         Auth Service (Facade)                   │
    │                                                 │
    │  isSuperAdmin()   → user->isSuperAdmin()       │
    │  isEmployee()     → user->isEmployee()         │
    │  hasRole()        → user->hasRole()            │
    │  hasPermission()  → user->hasPermission()      │
    └────┬────────────────────────────────────┬──────┘
         │                                    │
    ┌────▼──────┐  ┌──────────┐  ┌──────────▼────┐
    │IsAdmin MW │  │SuperAdminOr│BranchMiddleware│
    │           │  │Permission  │                │
    │Uses:      │  │Uses:       │Uses:           │
    │auth()     │  │auth()      │auth()->user()  │
    │->user()   │  │->user()    │->canAccessBr() │
    │->isSuper  │  │->isSuperAd │                │
    │Admin()    │  │min()       │                │
    │           │  │->hasAnyPerm│                │
    └───────────┘  │()          │                │
                   └────────────┴────────────────┘
                         │
                    ┌────▼──────────────────┐
                    │  DATABASE              │
                    │  ┌───────────────────┐ │
                    │  │ users table       │ │
                    │  │ - id              │ │
                    │  │ - name            │ │
                    │  │ - email           │ │
                    │  │ - branch_id       │ │
                    │  │ - user_type       │ │
                    │  │ - ...             │ │
                    │  └───────────────────┘ │
                    │  ┌───────────────────┐ │
                    │  │ roles table       │ │
                    │  │ - id              │ │
                    │  │ - name            │ │
                    │  │ - ...             │ │
                    │  └───────────────────┘ │
                    │  ┌───────────────────┐ │
                    │  │ model_has_roles   │ │
                    │  │ - model_id        │ │
                    │  │ - role_id         │ │
                    │  └───────────────────┘ │
                    └───────────────────────┘
```

**Benefit:** Unified system, proper RBAC, modern approach

---

## Data Structure Comparison

### Current (Dual Table)
```
┌──────────────────┐         ┌─────────────────────┐
│   users table    │         │  employees table    │
├──────────────────┤         ├─────────────────────┤
│ id (PK)          │         │ id (PK)             │
│ name             │         │ name                │
│ email            │         │ email               │
│ password         │         │ password            │
│ created_at       │         │ branch_id (FK)      │
│ updated_at       │         │ employee_id         │
│ last_accessed_   │         │ hired_at            │
│   branch_id      │         │ employment_status   │
│                  │         │ is_active           │
│                  │         │ created_at          │
│                  │         │ updated_at          │
└──────────────────┘         └─────────────────────┘
      │                              │
      │ Mutual                       │
      │ Exclusion                    │
      └──────────────────────────────┘
      
Auth Guards:
  - 'web' → users table
  - 'employees' → employees table
  
Issues:
  ✗ Two separate tables for same concept (users)
  ✗ Two separate guards
  ✗ Complex to manage
  ✗ Duplication risk
```

### After Migration (Unified Table)
```
┌─────────────────────────────────┐
│      users table                │
├─────────────────────────────────┤
│ id (PK)                         │
│ name                            │
│ email                           │
│ password                        │
│ branch_id (FK) - nullable       │
│ user_type enum (admin/employee) │
│ is_active                       │
│ employee_id - nullable          │
│ hired_at - nullable             │
│ employment_status - nullable    │
│ created_at                      │
│ updated_at                      │
│ deleted_at (soft delete)        │
└─────────────────────────────────┘
      │
      └─→ 'web' guard
      
┌──────────────────┐  ┌──────────────────┐
│  roles table     │  │ permissions table│
├──────────────────┤  ├──────────────────┤
│ id (PK)          │  │ id (PK)          │
│ name             │  │ name             │
│ guard_name       │  │ guard_name       │
│ created_at       │  │ created_at       │
│ updated_at       │  │ updated_at       │
└──────────────────┘  └──────────────────┘
      │                      │
      └──────────────────────┘
           (spatie)
      
Roles: super-admin, admin, branch-manager, supervisor, employee

Benefits:
  ✓ Single table for all users
  ✓ Single guard
  ✓ Clear role hierarchy
  ✓ Easier to manage
  ✓ No duplication
```

---

## Authorization Flow Comparison

### Current System
```
User Login
  │
  ├─ Email/Password Match in users table?
  │  └─ Yes → Set web guard → is_super_admin()
  │
  ├─ Email/Password Match in employees table?
  │  └─ Yes → Set employees guard → is_employee()
  │
  └─ Neither → Unauthorized
     
When checking permissions:
  - is_super_admin() → auth().check() && !auth('employees').check()
  - is_employee() → auth('employees').check()
  
Problem: Complex guard switching, multiple checks needed
```

### After Migration
```
User Login
  │
  └─ Email/Password Match in users table?
     └─ Yes → Set web guard → Check role
        │
        ├─ super-admin role? → Super admin privileges
        │
        ├─ admin role? → Admin privileges
        │
        ├─ branch-manager role? → Manager privileges
        │
        ├─ supervisor role? → Supervisor privileges
        │
        └─ employee role? → Employee privileges
        
When checking permissions:
  - auth()->user()->hasRole('super-admin')
  - auth()->user()->hasPermission('view-inventory')
  
Benefit: Single guard, clear role-based flow
```

---

## File Dependency Graph

### Current (Problematic)
```
BranchMiddleware
    ├─ is_super_admin() [defined in BranchHelper]
    ├─ is_employee() [assumed pattern]
    ├─ validate_branch_access() [defined in BranchHelper]
    └─ set_current_branch() [defined in BranchHelper]

IsAdmin Middleware
    └─ AuthService::requireSuperAdmin()
           └─ AuthService::isSuperAdmin()
                  └─ Auth::guard('web') && !Auth::guard('employees')

SuperAdminOrPermission Middleware
    └─ AuthService::isSuperAdmin()
           └─ Auth::guard('web') && !Auth::guard('employees')

Problem: Two different files define same logic
         ↑ Risk of divergence
```

### After Immediate Fix (Centralized)
```
AuthorizationHelper.php [SINGLE SOURCE OF TRUTH]
    ├─ is_super_admin()
    ├─ is_employee()
    ├─ get_current_user()
    ├─ get_user_branch_id()
    ├─ validate_branch_access()
    ├─ can_access_all_branches()
    ├─ get_accessible_branches()
    ├─ set_current_branch()
    ├─ current_branch()
    └─ current_actor()
         │
         ├─ Used by BranchHelper (delegates)
         ├─ Used by AuthService (delegates)
         ├─ Used by BranchMiddleware (directly)
         ├─ Used by IsAdmin Middleware (directly)
         └─ Used by SuperAdminOrPermission (directly)

Benefit: Single definition, all code uses it
```

### After Long-Term Migration (Role-Based)
```
User Model
    ├─ isSuperAdmin() → hasRole('super-admin')
    ├─ isEmployee() → hasRole('employee')
    ├─ canAccessBranch() → branch_id check
    └─ getAccessibleBranches()
         │
         ├─ Used by AuthService (facade)
         ├─ Used by Middleware
         ├─ Used by Controllers
         └─ Used by Blade Templates

spatie/laravel-permission
    ├─ Roles: super-admin, admin, branch-manager, supervisor, employee
    ├─ Permissions: fine-grained access control
    └─ Assignments in model_has_roles

Benefit: Unified approach, proper separation of concerns
```

---

## Request Lifecycle

### Current System (Dual Guard)
```
HTTP Request
    │
    └─ Authentication Middleware
        │
        ├─ Try web guard
        │  └─ Credentials match users table? → Set auth
        │
        ├─ Try employees guard
        │  └─ Credentials match employees table? → Set auth
        │
        └─ Both can be set (edge case) ✗
    
    └─ Route-Specific Middleware
        │
        ├─ BranchMiddleware
        │  ├─ Check is_super_admin() → from BranchHelper
        │  └─ Validate branch access → from BranchHelper
        │
        ├─ IsAdmin Middleware
        │  └─ Check is_super_admin() → from AuthService
        │
        ├─ SuperAdminOrPermission
        │  └─ Check is_super_admin() OR permission → from AuthService
        │
        └─ Controller Action
            └─ Business Logic
    
    └─ Response
    
Problem: Different sources for authorization checks
```

### After Migration (Unified)
```
HTTP Request
    │
    └─ Authentication Middleware
        │
        └─ Try web guard
            └─ Credentials match users table? → Set auth
                 (Single authentication path)
    
    └─ Route-Specific Middleware
        │
        ├─ BranchMiddleware
        │  ├─ Check auth()->user()->isSuperAdmin()
        │  └─ Check auth()->user()->canAccessBranch()
        │
        ├─ IsAdmin Middleware
        │  └─ Check auth()->user()?->isSuperAdmin()
        │
        ├─ SuperAdminOrPermission
        │  └─ Check auth()->user()?->isSuperAdmin()
        │     OR auth()->user()?->hasPermission()
        │
        └─ Controller Action
            └─ Business Logic
                └─ Can use auth()->user()->hasRole('...')
    
    └─ Response
    
Benefit: Single authentication path, unified checks
```

---

## Security Implications

### Current Risk
```
Middleware A (IsAdmin)           Middleware B (SuperAdminOrPermission)
  │                                 │
  ├─ Uses: AuthService              ├─ Uses: AuthService
  │   └─ is_super_admin()           │   └─ is_super_admin()
  │       └─ auth() && !auth('emp') │       └─ auth() && !auth('emp')
  │                                 │
  ├─ Rejects: User with both guards ├─ Allows: Same user (maybe?)
  │                                 │
  └─ INCONSISTENT AUTHORIZATION ◄───┘
  
Result: Same user rejected by one middleware, allowed by another
Risk Level: CRITICAL
```

### After Immediate Fix
```
Middleware A (IsAdmin)           Middleware B (SuperAdminOrPermission)
  │                                 │
  ├─ Uses: is_super_admin()         ├─ Uses: is_super_admin()
  │   (from AuthorizationHelper)    │   (from AuthorizationHelper)
  │                                 │
  ├─ Same definition                ├─ Same definition
  │                                 │
  └─ CONSISTENT AUTHORIZATION ───────┘
  
Result: All middleware use same definition
Risk Level: LOW (stable)
```

### After Long-Term Migration
```
Middleware A (IsAdmin)           Middleware B (SuperAdminOrPermission)
  │                                 │
  ├─ Uses: auth()->user()            ├─ Uses: auth()->user()
  │   ->isSuperAdmin()               │   ->isSuperAdmin()
  │   (from User model)              │   (from User model)
  │                                 │
  ├─ Same definition                ├─ Same definition
  │ (hasRole check)                 │ (hasRole check)
  │                                 │
  └─ UNIFIED AUTHORIZATION ──────────┘
  
Result: Single role system, unified checks, clear permissions
Risk Level: VERY LOW (proper RBAC)
```

---

## Decision Matrix

```
Criteria              │ Current  │ Immediate Fix │ Long-Term
─────────────────────┼──────────┼───────────────┼──────────
Complexity          │ High     │ High          │ Medium
Risk of divergence  │ High ✗   │ None ✓        │ None ✓
Data changes        │ None     │ None ✓        │ Yes
Breaking changes    │ N/A      │ None ✓        │ Yes
Implementation time │ N/A      │ 1-2 days ✓    │ 5-6 weeks
Team training needed│ No       │ No            │ Yes
Rollback difficulty │ N/A      │ Easy ✓        │ Medium
Long-term benefit   │ None     │ Low           │ High ✓
RBAC support        │ None     │ None          │ Full ✓
```

---

This document serves as a visual reference for understanding the architecture changes.
