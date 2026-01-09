# New Simplified RBAC System

## Core Principle

> **Role = Permission Level**
> **Department = Feature Access**

Instead of "Chef", "Head of Gelato", "Confectioneries Manager" (3 roles doing same thing in different departments), we have:

- **Manager** role + **Kitchen** department = Kitchen Manager
- **Manager** role + **Gelato Production** department = Gelato Manager
- **Manager** role + **Till** department = Till Manager

---

## New Role Structure (5 Roles Only)

| Role | Level | Description |
|------|-------|-------------|
| **Super Admin** | 5 | Full system access, all branches, all departments |
| **Admin** | 4 | Branch-wide access, can manage all departments in branch |
| **Manager** | 3 | Department manager, full control of their department |
| **Supervisor** | 2 | Department supervisor, limited management |
| **Staff** | 1 | Department worker, basic operational access |

---

## Department Categories

### Production Departments
| Department | What They Do |
|------------|--------------|
| Kitchen | Prepares food items |
| Gelato Production | Makes gelato/ice cream |
| Confectioneries Production | Makes confectionery items |

### Sales Departments
| Department | What They Do |
|------------|--------------|
| Till | Main sales counter |
| Corner Store | On-demand food sales |
| Confectioneries Sales | Sells confectionery items |

### Support Departments
| Department | What They Do |
|------------|--------------|
| Inventory/Store | Manages all stock |
| HR | Human resources |
| Accounting | Financial management |

---

## How Features Are Determined

### Production Department User:
```
User: John
Role: Staff
Department: Kitchen (category: Production)

Sidebar shows:
- Production Menu
  - Daily Produce
  - Recipes (view only)
  - Shift Closing

Does NOT show:
- Sales Menu
- HR Menu
- Inventory Menu
- Administration
```

### Sales Department User:
```
User: Sarah
Role: Supervisor
Department: Till (category: Sales)

Sidebar shows:
- Sales Menu
  - POS
  - My Sales
  - Shift Closing
  - Analytics (supervisor+)

Does NOT show:
- Production Menu
- HR Menu
- Inventory Menu
```

### Manager in Any Department:
```
User: Mike
Role: Manager
Department: Kitchen (category: Production)

Sidebar shows:
- Production Menu (ALL items)
  - Daily Produce
  - Recipes (can edit)
  - Products
  - Requests
  - Shift Closing
  - Reports
- Can see staff in their department
```

---

## Permission Matrix

### By Role Level:

| Permission | Staff | Supervisor | Manager | Admin | Super Admin |
|------------|-------|------------|---------|-------|-------------|
| View department dashboard | YES | YES | YES | YES | YES |
| Perform basic operations | YES | YES | YES | YES | YES |
| View reports | NO | YES | YES | YES | YES |
| Manage staff schedule | NO | YES | YES | YES | YES |
| Approve requests | NO | NO | YES | YES | YES |
| Edit department settings | NO | NO | YES | YES | YES |
| View all departments | NO | NO | NO | YES | YES |
| Manage roles | NO | NO | NO | NO | YES |
| System settings | NO | NO | NO | NO | YES |

### By Department Category:

| Feature | Production | Sales | Inventory | HR |
|---------|------------|-------|-----------|-----|
| Daily Produce | YES | NO | NO | NO |
| Recipes | YES | NO | NO | NO |
| POS | NO | YES | NO | NO |
| Sales Analytics | NO | YES | NO | NO |
| Stock Levels | VIEW | VIEW | FULL | NO |
| Employee Management | NO | NO | NO | YES |
| Leave Management | NO | NO | NO | YES |

---

## URL Structure

### Department-Scoped URLs:
```
/branch-dashboard/production/{deptSlug}/daily-produce
/branch-dashboard/production/{deptSlug}/recipes
/branch-dashboard/sales/{deptSlug}/pos
/branch-dashboard/sales/{deptSlug}/shift-closing
```

### Examples:
```
Kitchen Staff:     /branch-dashboard/production/kitchen/daily-produce
Gelato Manager:    /branch-dashboard/production/gelato-production/daily-produce
Till Cashier:      /branch-dashboard/sales/till/pos
Corner Store:      /branch-dashboard/sales/corner-store/pos
```

---

## Access Control Flow

```
1. User logs in
        |
        v
2. Get user's department_id and role
        |
        v
3. Redirect to: /{category}/{deptSlug}/dashboard
        |
        v
4. Sidebar renders based on:
   - Department category (Production/Sales/Support)
   - Role level (what they can do)
        |
        v
5. Every URL request passes through DepartmentScopeMiddleware:
   - Extract {deptSlug} from URL
   - Compare with user's department
   - Super Admin/Admin bypass
   - Manager+ can see all in category (optional)
   - Staff/Supervisor restricted to own dept
```

---

## Migration from Old to New

### Role Mapping:

| Old Role | New Role | Department |
|----------|----------|------------|
| Chef | Manager | Kitchen |
| Head of Gelato | Manager | Gelato Production |
| Confectioneries Manager | Manager | Confectioneries Production |
| Kitchen Staff | Staff | Kitchen |
| Gelato Production Staff | Staff | Gelato Production |
| Confectioneries Production Staff | Staff | Confectioneries Production |
| Sales Manager | Manager | (any Sales dept) |
| Till Supervisor | Supervisor | Till |
| Cashier | Staff | Till |
| Corner Store Manager | Manager | Corner Store |
| Corner Store Staff | Staff | Corner Store |
| HR Manager | Manager | HR |
| HR Officer | Staff | HR |
| Inventory Manager | Manager | Inventory/Store |
| Stock Controller | Supervisor | Inventory/Store |
| Store Keeper | Staff | Inventory/Store |

---

## Benefits

1. **Simpler:** 5 roles instead of 25+
2. **Scalable:** Add new department, no new roles needed
3. **Secure:** Department middleware enforces access
4. **Maintainable:** Role = level, Department = feature
5. **No typos:** Fewer role names to get wrong
