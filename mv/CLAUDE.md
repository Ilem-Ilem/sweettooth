# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SweetTooth is a multi-branch bakery/food production management system built with Laravel 12, Livewire/Volt, and Flux UI components. It manages inventory, production, sales, employees, and accounting across multiple branches.

## Development Commands

```bash
# Start all services (server, queue, logs, vite) concurrently
composer dev

# Run tests
composer test
# Or directly
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse

# Build frontend assets
npm run build

# Start Vite dev server only
npm run dev
```

## Architecture

### Authentication System
Two separate authentication guards:
- **`web` guard**: Users table - Super admins who can access all branches
- **`employees` guard**: Employees table - Branch-specific staff with roles/permissions

Check auth type:
```php
is_super_admin()           // User guard, not employee guard
auth('employees')->check() // Branch employee
can_access_all_branches()  // Super admin or multi-branch roles
```

### Branch Context
Multi-tenancy by branch. Key helpers in `app/Helpers/BranchHelper.php`:
- `current_branch_id()` - Get active branch from session or employee
- `current_branch()` - Get Branch model
- `set_current_branch($id)` - Set branch context
- `validate_branch_access($id)` - Check if user can access branch

### Route Structure
- `/` - Login page
- `/branch-dashboard/*` - Main application routes (requires auth + branch context)
- `/super-admin/*` - Legacy routes, redirect to branch-dashboard

All branch routes use middleware: `auth:web,employees`, `setBranchContext`, `branch`, `redirect-super-admin`

### Livewire Components
Located in `app/Livewire/`:
- `BranchDashboard/` - Main modules (Inventory, Production, Employees, Accounting, etc.)
- `Dashboards/` - Role-based dashboards (Admin, Manager, Supervisor, etc.)
- `BaseComponent.php` - Base class with common functionality

### Key Models & Domains
- **Inventory**: Item, Stock, StockMovement, Purchase, ItemRequest, ItemDispatch
- **Production**: Recipe, RecipeIngredient, DailyProduce, ProductionRequest, ProductDispatch
- **Sales**: Sale, SaleItem, ProductStock
- **Employees**: Employee, Department, EmployeeLeaveApplication, ClockIn
- **Accounting**: GlAccount, GlEntry, AccountingPeriod, CashPosition, DailyBankPosition

### Services Layer
Business logic in `app/Services/`:
- `AuditService` - Audit logging (also available as `audit()` helper)
- `GlPostingService` - General ledger postings
- `RolePermissionService` - RBAC management
- Accounting reports: BalanceSheetService, IncomeStatementService, TrialBalanceService

### Permissions
Uses `spatie/laravel-permission`. Roles include: super-admin, admin, manager, supervisor, and department-specific roles.

### Observers
Model observers in `app/Observers/` handle automatic GL entries for financial transactions (Sales, Purchases, Payments, StockMovements).

## Key Patterns

### Branch-Scoped Queries
Most models should be scoped to current branch:
```php
Stock::where('branch_id', current_branch_id())->get()
```

### Audit Logging
```php
audit($actor, 'action_name', $model, 'Description', 'completed', null, ['key' => 'value']);
```

## Testing
Uses Pest PHP. Tests use in-memory SQLite. Test files in `tests/Feature/` and `tests/Unit/`.
