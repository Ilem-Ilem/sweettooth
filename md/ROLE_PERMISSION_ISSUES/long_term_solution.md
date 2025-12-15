# Proposed Long-Term Solution: Unifying Authentication

The current dual-guard authentication system, while functional, introduces unnecessary complexity and has led to inconsistencies in authorization logic. A more robust and maintainable solution is to refactor the application to use a single authentication guard and leverage the existing `spatie/laravel-permission` package for role-based access control.

## Steps for Refactoring

1.  **Consolidate User Models:**
    *   Merge the `employees` table into the `users` table.
    *   Add a `branch_id` column to the `users` table to associate users with their respective branches. A `nullable` `branch_id` can be used for users who are not tied to a specific branch (e.g., super admins).
    *   Migrate all existing employee records to the `users` table.

2.  **Unify Authentication Guard:**
    *   Remove the `employees` guard from `config/auth.php`.
    *   Ensure all authentication routes and logic use the default `web` guard.

3.  **Implement Role-Based Access Control (RBAC):**
    *   Define a clear set of roles using `spatie/laravel-permission`. Examples include:
        *   `super-admin`: Has all permissions.
        *   `admin`: Manages the entire system but with some restrictions.
        *   `branch-manager`: Manages a specific branch.
        *   `employee`: Regular user with limited access to a specific branch.
    *   Assign permissions to each role.
    *   Assign the appropriate role to each user.

4.  **Refactor Application Code:**
    *   Remove all instances of `auth('employees')`, `Auth::guard('employees')`, and `is_super_admin()` helper.
    *   Replace manual permission checks with `spatie/laravel-permission` directives like `@can`, `@role`, and the `can()` method on the user model.
    *   Update the `AuthService` to work with a single authentication guard and use the RBAC system for authorization checks.
    *   Refactor the `BranchMiddleware` to use the user's `branch_id` from the `users` table and the RBAC system to validate branch access.

## Benefits of this Approach

*   **Simplicity:** A single authentication guard and user model are easier to understand and maintain.
*   **Consistency:** Authorization logic is centralized in the role and permission definitions, eliminating the risk of conflicting implementations.
*   **Flexibility:** The `spatie/laravel-permission` package provides a powerful and flexible way to manage access control as the application grows.
*   **Security:** A clear and consistent authorization system is less prone to security vulnerabilities.
