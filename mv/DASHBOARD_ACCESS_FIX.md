# Dashboard Access Control Fix

## Problem
Super admin users were receiving a 403 error: "Your account does not have access to any dashboard. Please contact your administrator."

## Root Cause
The Dashboard Router was checking for specific roles and permissions before routing users to the super-admin dashboard. If a super admin user didn't have explicit roles assigned (or the role check failed), they would hit the 403 error instead of being directed to the super-admin dashboard.

The system has two types of users:
1. **Web Guard Users** (Super Admins): Authenticated in the default `auth()` guard, NOT in the `auth('employees')` guard
2. **Employee Users**: Authenticated in the `auth('employees')` guard

Super admin users (web guard only) should ALWAYS have access to the super-admin dashboard, regardless of what roles/permissions are explicitly assigned in the database.

## Solution

### Changes Made

#### 1. `/app/Livewire/BranchDashboard/Dashboards/Router.php`
- **Line 36**: Simplified the web guard check to `$isWebGuardOnly = auth()->check() && !auth('employees')->check()`
- **Lines 46-58**: Removed role checking for web guard users. If a user is authenticated in the web guard (and not in employees guard), they are automatically redirected to the super-admin dashboard
- **Lines 116-133**: Enhanced logging for the error case to help debug permission issues for employee users

Key change: Web guard users no longer need specific roles to access the super-admin dashboard. The guard distinction itself is sufficient.

#### 2. `/app/Livewire/Dashboards/DashboardRouter.php` (Deprecated Component)
- **Lines 27-30**: Updated to match the same logic - web guard users are automatically redirected to super-admin dashboard

### How It Works Now

1. **User authenticates** → Router component mounts
2. **Check if web guard user** → `auth()->check() && !auth('employees')->check()`
   - YES → Redirect to `branch-dashboard.dashboards.super-admin`
   - NO → Continue to employee dashboard routing
3. **For employees** → Check permissions/roles to determine which dashboard (Production, Sales, HR, etc.)
4. **No dashboard access** → Show 403 error with detailed logging

## Testing

To verify the fix:

1. Login as a super admin user (web guard)
2. You should be immediately redirected to the super-admin dashboard
3. No 403 error should occur

For employees without dashboard permissions:
1. Login as an employee (employees guard)
2. If they don't have any role with dashboard access, they'll see the 403 error
3. Check the logs to see which permissions/roles they're missing

## Debugging

If users still see the 403 error, check:

1. **Log location**: Check Laravel logs for the `Router component called` info and `Employee denied dashboard access` warning
2. **Guard detection**: Ensure the user is being authenticated to the correct guard:
   - Super admins should use `auth()` (web guard)
   - Employees should use `auth('employees')`
3. **Permissions**: For employees, ensure they have at least one of:
   - A production role (Chef, Head of Production, etc.)
   - A sales role (Cashier, Sales Manager, etc.)
   - An inventory role (Inventory Manager, etc.)
   - An HR/employee management permission
   - A reporting/admin role

## Files Modified
- `/home/ilem/Documents/sweettooth/app/Livewire/BranchDashboard/Dashboards/Router.php`
- `/home/ilem/Documents/sweettooth/app/Livewire/Dashboards/DashboardRouter.php`
