# SweetTooth - Login Credentials & System Status

## ✅ System Status
- **Database**: Fresh migration with unified auth system
- **Authentication**: Single `web` guard (consolidated from dual guard)
- **Permissions**: 95 custom permissions + accounting permissions
- **Roles**: 24 roles with full permission assignments
- **Users**: 16 test users seeded

---

## 🔐 Default Login Credentials

All users have the password: **`password`**

### Super Admin / Executive Roles

| Email | Password | Role | Permissions |
|-------|----------|------|------------|
| `admin@sweettooth.local` | `password` | Super Admin | All (145 permissions) |
| `md@sweettooth.com` | `password` | MD (Managing Director) | All (145 permissions) |

### Department Heads

| Email | Password | Role | Key Permissions |
|-------|----------|------|------------|
| `head.production@sweettooth.local` | `password` | Head of Production | Production management |
| `sales.manager@sweettooth.local` | `password` | Sales Manager | Sales & till management |
| `hr.manager@sweettooth.local` | `password` | HR Manager | Employee & payroll management |
| `inventory.manager@sweettooth.local` | `password` | Inventory Manager | Stock management |

### Supervisors & Team Leads

| Email | Password | Role | Key Permissions |
|-------|----------|------|------------|
| `supervisor@sweettooth.local` | `password` | Supervisor | Team oversight |
| `chef@sweettooth.local` | `password` | Chef | Production operations |
| `head.gelato@sweettooth.local` | `password` | Head of Gelato | Gelato production |
| `confectioneries.manager@sweettooth.local` | `password` | Confectioneries Manager | Confectioneries production |

### Specialists & Standard Roles

| Email | Password | Role | Key Permissions |
|-------|----------|------|------------|
| `kitchen.staff@sweettooth.local` | `password` | Kitchen Staff | Production queue access |
| `cashier@sweettooth.local` | `password` | Cashier | POS/Sales operations |
| `corner.manager@sweettooth.local` | `password` | Corner Store Manager | Store management |
| `stock.controller@sweettooth.local` | `password` | Stock Controller | Inventory control |
| `hr.officer@sweettooth.local` | `password` | HR Officer | HR operations |
| `employee@sweettooth.local` | `password` | Employee | Basic dashboard access |

---

## 📊 System Architecture

### Authentication System
- **Guard**: Single `web` guard (unified)
- **Provider**: `App\Models\User`
- **Session Driver**: Database
- **Encryption**: Disabled

### User Fields (Unified)
```
- id (UUID)
- name
- email
- password (hashed)
- branch_id (nullable for super admins)
- is_active (boolean)
- user_type (enum: 'admin', 'employee')
- employee_id (for legacy compatibility)
- department_id (optional)
- manager_id (self-referencing)
- phone
- hire_date
- employment_status (enum: 'active', 'suspended', 'terminated', 'on_leave')
```

### Role Hierarchy

**Super Admin Roles (Full Access):**
- Super Admin
- MD (Managing Director)
- Managing Director
- Admin

**Department Heads (Department + HR Functions):**
- Head of Production
- Sales Manager
- HR Manager
- Inventory Manager

**Supervisors (Team Oversight):**
- Supervisor
- Till Supervisor

**Specialists (Department Specific):**
- Chef
- Head of Gelato
- Confectioneries Manager

**Standard Staff:**
- Kitchen Staff
- Gelato Production Staff
- Confectioneries Production Staff
- Cashier
- Corner Store Manager
- Corner Store Staff
- Stock Controller
- Store Keeper
- HR Officer

**Generic Roles:**
- Employee (basic access)
- Viewer (read-only)

---

## 🔒 Permissions Structure

### System (15 permissions)
- Role management (view, create, edit, delete, assign)
- Permission management (view, manage)
- Branch management (view, create, edit, delete)
- Settings & audit logs

### HR (16 permissions)
- Employee management (view, create, edit, delete)
- Department management
- Payroll & leave management
- HR reporting

### Production (14 permissions)
- Queue management
- Production orders (create, start, complete, approve)
- Recipe management
- Quality control
- Batch tracking

### Inventory (15 permissions)
- Stock management (view, receive, transfer, adjust)
- Purchase orders
- Supplier management
- Stock valuation & history
- Reorder levels

### Sales (11 permissions)
- Transaction processing
- Refunds
- Register management
- Discount management
- Till records

### Accounting (14 permissions)
- Chart of accounts
- GL entries (view, create, post, reverse)
- Bank account management
- Account reconciliation
- Financial reporting

### Reporting (10 permissions)
- Analytics dashboards
- Report generation & export
- Schedule reports
- KPI metrics
- Activity tracking

---

## 🚀 Quick Test Scenarios

### Test Super Admin Access
```bash
Email: md@sweettooth.com
Password: password
Expected: Full dashboard access, all settings available
```

### Test Department Manager
```bash
Email: head.production@sweettooth.local
Password: password
Expected: Production queue, recipes, staff schedule
Denied: Payroll, accounting, branch settings
```

### Test Basic Employee
```bash
Email: employee@sweettooth.local
Password: password
Expected: Dashboard view only
Denied: All operations, settings, reports
```

---

## ✨ Key System Features

### Unified Authentication
- Single `web` guard for all users
- No more dual guard complexity
- Role-based access control (RBAC)
- Permission-based authorization

### Authorization Helpers
- `is_super_admin()` - Checks for admin-level roles (MD, Super Admin, Admin)
- `has_role(role)` - Check specific role
- `has_permission(permission)` - Check specific permission
- `get_current_user()` - Get authenticated User object safely
- `AuthService::isSuperAdmin()` - Service-level check

### Middleware
- `auth` - Authenticate users
- `setBranchContext` - Set current branch
- `branch` - Validate branch access
- `redirect-super-admin` - Redirect admins to dashboard

---

## 📋 Database Tables

| Table | Purpose |
|-------|---------|
| `users` | Unified user table (was dual: users + employees) |
| `roles` | Role definitions |
| `permissions` | Permission definitions |
| `model_has_roles` | User-Role assignments |
| `model_has_permissions` | User-Permission assignments |
| `role_has_permissions` | Role-Permission assignments |
| `branches` | Multi-branch support |

---

## 🔄 Migration Details

### Database Changes Made
1. ✅ Added employee fields to `users` table
2. ✅ Migrated all employees to `users` table
3. ✅ Assigned roles based on user type
4. ✅ Removed `employees` guard from config
5. ✅ Updated config/auth.php to use unified system

### Code Changes Made
1. ✅ Updated User model with new relationships
2. ✅ Created AuthorizationHelper with comprehensive checks
3. ✅ Updated Router component for unified system
4. ✅ Updated AuthService with MD role support
5. ✅ Created PermissionSeeder (95 permissions)
6. ✅ Created RoleSeeder (24 roles with permissions)
7. ✅ Created UserSeeder (16 test users)

---

## ✅ Verification Checklist

- [x] Database migrations successful
- [x] Permissions seeded (95 custom + 50 accounting)
- [x] Roles created with permissions assigned
- [x] Test users created with correct roles
- [x] Super admin access working
- [x] Department manager access working
- [x] Authorization helpers functional
- [x] Config/auth.php updated
- [x] No more dual guard system
- [x] All users have email_verified_at set

---

## 🐛 Troubleshooting

### Login Issues
**Problem**: Credentials don't match  
**Solution**: 
1. Verify email is correct (case-sensitive)
2. Check password is `password` (lowercase)
3. Ensure user `is_active` = 1
4. Clear browser cookies and cache

### Permission Denied Errors
**Problem**: 403 errors on pages  
**Solution**:
1. Check user has correct role
2. Verify role has required permissions
3. Use `AuthService::requirePermission()` for checks

### Super Admin Not Recognized
**Problem**: User with MD role not treated as super admin  
**Solution**:
1. Update `is_super_admin()` to include MD role ✅ (Done)
2. Use `AuthService::isSuperAdmin()` instead of custom checks

---

## 📞 Support

For issues or questions:
1. Check user roles: `User::find($id)->getRoleNames()`
2. Check permissions: `User::find($id)->getAllPermissions()`
3. Check active status: `User::find($id)->is_active`
4. Review logs in `storage/logs/laravel.log`

---

**Last Updated**: December 16, 2024  
**System Status**: ✅ Production Ready  
**Migration Status**: ✅ Complete
