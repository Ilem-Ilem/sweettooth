# Critical Tasks - December 13 Implementation

## Priority 1: IMMEDIATE

### Task 1.1: Create RoleOrPermission Middleware
**File:** `app/Http/Middleware/RoleOrPermission.php`
**Purpose:** Check if user has role OR permission (used for accounting routes)
**Status:** TO DO

### Task 1.2: Register Middleware in Bootstrap
**File:** `bootstrap/app.php`
**Action:** Add alias for new middleware
**Status:** TO DO

### Task 1.3: Add Bank/Cash Routes
**File:** `routes/branch-route.php`
**Action:** Add routes for bank positions, cash positions, reconciliation
**Status:** TO DO

---

## Priority 2: HIGH

### Task 2.1: Daily Bank Position Dashboard
**Files:**
- `app/Livewire/Accounting/DailyBankPositionDashboard.php`
- `resources/views/livewire/accounting/daily-bank-position-dashboard.blade.php`

**Features:**
- List all bank accounts
- Show today's position per bank
- Track opening balance, inflows, outflows, closing balance
- Show unreflected/pending items
- Date range filter

### Task 2.2: Cash Position Dashboard
**Files:**
- `app/Livewire/Accounting/CashPositionDashboard.php`
- `resources/views/livewire/accounting/cash-position-dashboard.blade.php`

**Features:**
- Record daily cash count
- Show variance (book vs physical)
- Track by cash type (sales cash, petty cash)
- Historical view

---

## Priority 3: MEDIUM

### Task 3.1: Bank Reconciliation
**Files:**
- `app/Livewire/Accounting/BankReconciliation.php`
- `resources/views/livewire/accounting/bank-reconciliation.blade.php`

**Features:**
- Select bank account
- Show unreconciled items (GL vs Bank)
- Auto-match by amount/date
- Manual match interface
- Mark items as reconciled
- Generate reconciliation report

---

## Commands to Run After Implementation

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Seed permissions (if new permissions added)
php artisan db:seed --class=AccountingAccessControlSeeder

# Check routes
php artisan route:list --name=accounting
```

---

## Verification Steps

### 1. Middleware Test
```
- Login as Super Admin → Access all accounting routes ✓
- Login as Employee with Accountant role → Access accounting ✓
- Login as Employee without permission → 403 Forbidden ✓
```

### 2. Bank Position Test
```
- View list of bank accounts
- See today's positions
- Filter by date range
- Drill down to transactions
```

### 3. Cash Position Test
```
- Record cash count
- System calculates variance
- View history
```

### 4. Reconciliation Test
```
- Select bank account
- System shows unreconciled items
- Match items
- Generate report
```
