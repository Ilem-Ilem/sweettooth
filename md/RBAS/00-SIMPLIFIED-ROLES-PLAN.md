# Simplified Roles Plan for SweetTooth

## Current State Analysis

### Department Categories (3)
| Category | Purpose |
|----------|---------|
| Production | Makes products |
| Sales | Sells products |
| Support | Back-office functions |

### Departments (8)
| Department | Category |
|------------|----------|
| Kitchen | Production |
| Gelato Production | Production |
| Confectionaries Production | Production |
| Till | Sales |
| Corner Store | Sales |
| Confectionaries Sales | Sales |
| Inventory/Store | Support |
| HR | Support |

### Current Roles (29 - TOO MANY!)

**Roles WITH users:**
| Role | Users | Can Be Replaced By |
|------|-------|-------------------|
| Super Admin | 1 | Keep as Super Admin |
| Admin | 5 | Keep as Admin |
| Head of Production | 5 | Manager (in Production dept) |
| Sales Manager | 5 | Manager (in Sales dept) |
| HR Manager | 5 | Manager (in HR dept) |
| Inventory Manager | 5 | Manager (in Inventory dept) |
| Till Supervisor | 5 | Supervisor (in Till dept) |
| Chef | 5 | Manager (in Kitchen dept) |
| Head of Gelato | 5 | Manager (in Gelato dept) |
| Kitchen Staff | 15 | Staff (in Kitchen dept) |
| Gelato Production Staff | 10 | Staff (in Gelato dept) |
| Cashier | 15 | Staff (in Till dept) |
| Corner Store Manager | 5 | Manager (in Corner Store dept) |
| Corner Store Staff | 10 | Staff (in Corner Store dept) |
| Stock Controller | 5 | Supervisor (in Inventory dept) |
| Store Keeper | 5 | Staff (in Inventory dept) |
| Employee | 150 | Staff (generic) |

**Roles with 0 users (DELETE):**
- MD
- Managing Director
- Supervisor (generic)
- Confectioneries Manager
- Confectioneries Production Staff
- Sales Supervisor
- Sales Associate
- Junior Cashier
- HR Officer
- Viewer
- Manager (generic)
- Operator

---

## New Simplified Structure

### Only 5 Roles Needed

| Role | Level | Description |
|------|-------|-------------|
| **Super Admin** | 5 | Full system access, all branches |
| **Admin** | 4 | Branch-wide access, manages all departments |
| **Manager** | 3 | Manages their department |
| **Supervisor** | 2 | Team lead in department |
| **Staff** | 1 | Regular worker in department |

### How It Works

**Department + Role = Access**

```
User: John
Department: Kitchen
Role: Manager
Result: Can manage Kitchen department, see all Production departments

User: Sarah
Department: Till
Role: Staff
Result: Can only access Till department, basic sales functions

User: Mike
Department: None (branch level)
Role: Admin
Result: Can access all departments in branch
```

---

## Role Mapping (Old -> New)

### Production Roles
| Old Role | New Role | Department |
|----------|----------|------------|
| Head of Production | Manager | Kitchen (or any Production) |
| Chef | Manager | Kitchen |
| Head of Gelato | Manager | Gelato Production |
| Confectioneries Manager | Manager | Confectionaries Production |
| Kitchen Staff | Staff | Kitchen |
| Gelato Production Staff | Staff | Gelato Production |
| Confectioneries Production Staff | Staff | Confectionaries Production |

### Sales Roles
| Old Role | New Role | Department |
|----------|----------|------------|
| Sales Manager | Manager | Till (or any Sales) |
| Till Supervisor | Supervisor | Till |
| Cashier | Staff | Till |
| Junior Cashier | Staff | Till |
| Corner Store Manager | Manager | Corner Store |
| Corner Store Staff | Staff | Corner Store |
| Sales Associate | Staff | Any Sales dept |

### Support Roles
| Old Role | New Role | Department |
|----------|----------|------------|
| Inventory Manager | Manager | Inventory/Store |
| Stock Controller | Supervisor | Inventory/Store |
| Store Keeper | Staff | Inventory/Store |
| HR Manager | Manager | HR |
| HR Officer | Staff | HR |

### Admin Roles
| Old Role | New Role | Department |
|----------|----------|------------|
| Super Admin | Super Admin | None (all access) |
| MD | Super Admin | None |
| Managing Director | Super Admin | None |
| Admin | Admin | None (branch level) |

### Generic Roles
| Old Role | New Role | Department |
|----------|----------|------------|
| Employee | Staff | Their assigned dept |
| Viewer | Staff | Their assigned dept |
| Supervisor | Supervisor | Their assigned dept |

---

## Access Matrix by Role Level

### What Each Role Can Do

| Action | Staff | Supervisor | Manager | Admin | Super Admin |
|--------|-------|------------|---------|-------|-------------|
| View own dept dashboard | YES | YES | YES | YES | YES |
| Perform basic operations | YES | YES | YES | YES | YES |
| View dept reports | NO | YES | YES | YES | YES |
| Manage staff schedule | NO | YES | YES | YES | YES |
| Approve requests | NO | NO | YES | YES | YES |
| Edit dept settings | NO | NO | YES | YES | YES |
| View other depts (same category) | NO | NO | YES | YES | YES |
| View all depts in branch | NO | NO | NO | YES | YES |
| Manage users | NO | NO | NO | YES | YES |
| Manage roles | NO | NO | NO | NO | YES |
| System settings | NO | NO | NO | NO | YES |

### What Each Department Category Sees

| Menu Section | Production Dept | Sales Dept | Inventory Dept | HR Dept |
|--------------|-----------------|------------|----------------|---------|
| Production Menu | YES | NO | NO | NO |
| Sales Menu | NO | YES | NO | NO |
| Inventory Menu | View only | View only | YES | NO |
| HR Menu | NO | NO | NO | YES |
| Reports | By role level | By role level | By role level | By role level |

---

## Sidebar Menu Logic

### Staff (Level 1)
```
Dashboard
[Department Category Menu]
  └── [Own Department Only]
       ├── Basic operations
       └── Shift closing
```

### Supervisor (Level 2)
```
Dashboard
[Department Category Menu]
  └── [Own Department Only]
       ├── Basic operations
       ├── Reports
       ├── Staff schedule
       └── Shift closing
```

### Manager (Level 3)
```
Dashboard
[Department Category Menu]
  ├── [Own Department]
  │    ├── All operations
  │    ├── Reports
  │    ├── Staff schedule
  │    └── Settings
  └── [Other Depts in Category] (view only)
```

### Admin (Level 4)
```
Dashboard
Production Menu (all depts)
Sales Menu (all depts)
Inventory Menu
HR Menu
Administration
  ├── Users
  └── Departments
```

### Super Admin (Level 5)
```
Dashboard
Production Menu (all depts)
Sales Menu (all depts)
Inventory Menu
HR Menu
Administration
  ├── Users
  ├── Departments
  ├── Roles & Permissions
  ├── Branches
  ├── Settings
  └── Audit Logs
```

---

## Migration SQL

### Step 1: Create new roles (keep old ones temporarily)
```sql
-- The 5 new roles
INSERT INTO roles (name, guard_name, level) VALUES
('Super Admin', 'web', 5),  -- Already exists, update level
('Admin', 'web', 4),        -- Already exists, update level
('Manager', 'web', 3),      -- Exists but unused
('Supervisor', 'web', 2),   -- Exists but unused
('Staff', 'web', 1);        -- New
```

### Step 2: Update users to new roles
```sql
-- Map old roles to new roles
UPDATE model_has_roles mhr
JOIN roles old_role ON mhr.role_id = old_role.id
JOIN roles new_role ON new_role.name = CASE
    WHEN old_role.name IN ('Super Admin', 'MD', 'Managing Director') THEN 'Super Admin'
    WHEN old_role.name = 'Admin' THEN 'Admin'
    WHEN old_role.name IN ('Head of Production', 'Chef', 'Head of Gelato',
         'Confectioneries Manager', 'Sales Manager', 'HR Manager',
         'Inventory Manager', 'Corner Store Manager') THEN 'Manager'
    WHEN old_role.name IN ('Till Supervisor', 'Sales Supervisor',
         'Stock Controller', 'Supervisor') THEN 'Supervisor'
    ELSE 'Staff'
END
SET mhr.role_id = new_role.id;
```

### Step 3: Delete unused old roles (AFTER migration)
```sql
DELETE FROM roles WHERE name IN (
    'MD', 'Managing Director', 'Head of Production', 'Chef',
    'Head of Gelato', 'Confectioneries Manager', 'Sales Manager',
    'HR Manager', 'Inventory Manager', 'Till Supervisor',
    'Sales Supervisor', 'Stock Controller', 'Corner Store Manager',
    'Kitchen Staff', 'Gelato Production Staff', 'Confectioneries Production Staff',
    'Cashier', 'Junior Cashier', 'Corner Store Staff', 'Sales Associate',
    'Store Keeper', 'HR Officer', 'Employee', 'Viewer', 'Operator'
);
```

---

## Summary

| Before | After |
|--------|-------|
| 29 roles | 5 roles |
| Role determines features | Department determines features |
| Hardcoded role names everywhere | Simple level check (1-5) |
| No URL protection | DepartmentScopeMiddleware |
| Complex permission matrix | Simple level-based permissions |

---

## Next Steps

1. Review this plan
2. Approve the role mapping
3. Create migration script
4. Create DepartmentScopeMiddleware
5. Update SidebarVisibilityService
6. Test with sample users
7. Run migration on production
8. Delete old roles
