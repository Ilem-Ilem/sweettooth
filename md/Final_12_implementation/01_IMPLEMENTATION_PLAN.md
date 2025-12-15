# Final December Implementation Plan

## Overview

This document outlines the remaining implementation tasks for:
1. Completing the Accounting System
2. Advancing Role-Based Access Control (RBAC)

---

## Authentication Architecture

### Two User Types

| Type | Table | Guard | Description |
|------|-------|-------|-------------|
| Super Admin | `users` | `web` | Full system access, can access all branches |
| Employee | `employees` | `employees` | Branch-specific access, role-based permissions |

### Key Helper Functions (`app/Helpers/BranchHelper.php`)
- `is_super_admin()` - Check if user is from `users` table (web guard)
- `current_branch_id()` - Get current branch context
- `can_access_all_branches()` - Super admins or MD roles
- `validate_branch_access($id)` - Check if user can access branch

### Key Helper Class (`app/Helpers/RolePermission.php`)
- `RolePermission::isSuperAdmin()` - Check role
- `RolePermission::hasPermission($permission)` - Check permission
- `RolePermission::hasAnyPermission($permissions)` - Check any permission
- `RolePermission::getRoleLevel($role)` - Get hierarchy level (1-5)

---

## Accounting System - Remaining Tasks

### COMPLETED
- [x] GL Accounts Model & Migration
- [x] GL Entries Model & Migration
- [x] Accounting Periods Model & Migration
- [x] Bank Accounts Model & Migration
- [x] Daily Bank Position Model & Migration
- [x] Cash Position Model & Migration
- [x] GlPostingService (auto-posting)
- [x] Sale/Purchase/Payment/StockMovement Observers
- [x] Financial Reports (Trial Balance, Income Statement, Balance Sheet)
- [x] Manual Journal Entry Component
- [x] Period Management Component
- [x] Chart of Accounts List Component
- [x] Accounting Dashboard Component
- [x] Accounting Permissions Seeder

### TO BE IMPLEMENTED

#### 1. Bank Reconciliation Module
- [ ] `BankReconciliation.php` Livewire component
- [ ] Bank statement upload functionality
- [ ] Auto-match transactions
- [ ] Manual match interface
- [ ] Reconciliation report

#### 2. Daily Bank Position Dashboard
- [ ] `DailyBankPositionDashboard.php` component
- [ ] View daily positions per bank
- [ ] Track inflows/outflows
- [ ] Show unreflected items (pending)
- [ ] Link to actual bank transactions

#### 3. Cash Position Dashboard
- [ ] `CashPositionDashboard.php` component
- [ ] Daily cash count entry
- [ ] Variance tracking (book vs physical)
- [ ] Cash movement history

#### 4. Posting Status Monitor Enhancement
- [ ] Retry failed postings
- [ ] Bulk retry functionality
- [ ] Error detail view

---

## Role-Based Access - Improvements

### Current Middleware
| Middleware | Purpose |
|------------|---------|
| `BranchMiddleware` | Validates branch access |
| `SuperAdminOrPermission` | Allows super admin OR specific permission |
| `SetBranchContext` | Sets branch in session |
| `ProtectCoreRoles` | Protects system roles from editing |

### New Middleware Needed
| Middleware | Purpose |
|------------|---------|
| `RoleOrPermission` | Check role OR permission (for accounting routes) |
| `AccountingAccess` | Unified accounting access check |

### Permission Categories for Accounting

```
Base Access:
- access_accounting
- view_financial_reports

GL Management:
- manage_accounts, view_gl_accounts, create_gl_accounts, edit_gl_accounts

Journal Entries:
- view_gl_entries, create_journal_entries, approve_gl_entries, reverse_gl_entries

Bank & Cash:
- view_bank_accounts, create_bank_accounts, reconcile_bank_accounts
- view_cash_positions, record_cash_count

Periods:
- manage_periods, create_accounting_periods, close_accounting_periods

Reports:
- view_general_ledger, view_trial_balance, view_balance_sheet, view_income_statement
```

---

## Route Structure for Accounting

```php
// Base route group
Route::prefix('accounting')->name('accounting.')
    ->middleware('role_or_permission:access_accounting,view_financial_reports')
    ->group(function () {

    // Dashboard - all accounting users
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // Chart of Accounts - management
    Route::middleware('role_or_permission:manage_accounts')
        ->get('/accounts', GlAccountList::class)->name('accounts');

    // Period Management
    Route::middleware('role_or_permission:manage_periods')
        ->get('/periods', PeriodManagement::class)->name('periods');

    // Manual Journal Entry
    Route::middleware('role_or_permission:create_journal_entries')
        ->get('/journal-entry', ManualJournalEntry::class)->name('journal-entry');

    // Bank Reconciliation
    Route::middleware('role_or_permission:reconcile_bank_accounts')
        ->get('/bank-reconciliation', BankReconciliation::class)->name('bank-reconciliation');

    // Daily Bank Position
    Route::middleware('role_or_permission:view_daily_bank_positions')
        ->get('/bank-positions', DailyBankPositionDashboard::class)->name('bank-positions');

    // Cash Position
    Route::middleware('role_or_permission:view_cash_positions')
        ->get('/cash-positions', CashPositionDashboard::class)->name('cash-positions');

    // Financial Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/general-ledger', GeneralLedgerReport::class)->name('general-ledger');
        Route::get('/trial-balance', TrialBalanceReport::class)->name('trial-balance');
        Route::get('/income-statement', IncomeStatementReport::class)->name('income-statement');
        Route::get('/balance-sheet', BalanceSheetReport::class)->name('balance-sheet');
    });
});
```

---

## Implementation Order

### Phase 1: Middleware & Routes (Today)
1. Create `RoleOrPermission` middleware
2. Update accounting routes with proper middleware
3. Add new routes for bank/cash dashboards

### Phase 2: Bank Position Dashboard
1. Create `DailyBankPositionDashboard.php`
2. Create view `daily-bank-position-dashboard.blade.php`
3. Add route

### Phase 3: Cash Position Dashboard
1. Create `CashPositionDashboard.php`
2. Create view `cash-position-dashboard.blade.php`
3. Add route

### Phase 4: Bank Reconciliation
1. Create `BankReconciliation.php`
2. Create view `bank-reconciliation.blade.php`
3. Bank statement upload logic
4. Auto-match algorithm
5. Add route

---

## Files to Create

```
app/
├── Http/Middleware/
│   └── RoleOrPermission.php (NEW)
├── Livewire/Accounting/
│   ├── DailyBankPositionDashboard.php (NEW)
│   ├── CashPositionDashboard.php (NEW)
│   └── BankReconciliation.php (NEW)

resources/views/livewire/accounting/
├── daily-bank-position-dashboard.blade.php (NEW)
├── cash-position-dashboard.blade.php (NEW)
└── bank-reconciliation.blade.php (NEW)
```

---

## Testing Checklist

- [ ] Super Admin can access all accounting features
- [ ] MD can access all accounting features
- [ ] Admin can access management features
- [ ] Accountant role can access all accounting
- [ ] Regular employees cannot access accounting (unless permission granted)
- [ ] Bank positions display correctly
- [ ] Cash positions can be recorded
- [ ] Bank reconciliation matches transactions
