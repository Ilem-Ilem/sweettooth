# Critical Issues in Bank Reconciliation Module

**Source:** `mv/BANK_RECONCILIATION_AUDIT.md`, `mv/BANK_RECONCILIATION_IMPLEMENTATION_GUIDE.md`, `mv/BANK_RECONCILIATION_COMPONENT_REWRITE.md`

The `BankReconciliation.php` component is **not production ready** and has critical architectural and functional gaps. The documentation indicates that a rewrite is required.

## Key Issues:

### 1. No GL Entry Posting on Reconciliation
- **Problem:** The component marks GL entries and bank transactions as reconciled but does **not** create corresponding GL entries or post to the general ledger.
- **Impact:** This breaks the core accounting principle of GL-backed transactions. The reconciliation happens in isolation from the rest of the accounting system.

### 2. Missing `reconciled` Field in `DailyBankTransaction` and `GlEntry` Models
- **Problem:** The component's queries filter by a `reconciled` boolean field that does not exist in the `daily_bank_transactions` and `gl_entries` tables.
- **Impact:** Queries will fail or return incorrect data, making it impossible to distinguish between reconciled and unreconciled items.

### 3. Hardcoded GL Account Lookup
- **Problem:** The `getGlAccountIdForBank()` method hardcodes the GL account number '1050' instead of using the `gl_account_id` relationship on the `BankAccount` model.
- **Impact:** The component will always use the same GL account, regardless of the bank account's actual mapping, leading to incorrect reconciliations.

### 4. Lack of Branch Context Validation
- **Problem:** The component does not validate that the selected bank account belongs to the current user's branch.
- **Impact:** Potential security issue, allowing users to reconcile bank accounts from other branches. Data integrity is at risk.

### 5. No Audit Logging
- **Problem:** Reconciliation actions do not create audit logs.
- **Impact:** No audit trail for who reconciled what and when, which is a critical compliance failure for an accounting system.

### 6. No Persistent State Management
- **Problem:** Matched items are stored in an in-memory property (`$matchedItems`), which is lost on page refresh.
- **Impact:** Users cannot save a draft reconciliation and return to it later. There is no historical record of past reconciliations.

## Proposed Solution (from documentation):

The provided documentation (`BANK_RECONCILIATION_IMPLEMENTATION_GUIDE.md` and `BANK_RECONCILIATION_COMPONENT_REWRITE.md`) outlines a complete rewrite of the component and the introduction of new database tables and services.

### Required Actions:
1.  **Create Migrations:**
    *   Add `reconciled`, `reconciled_at`, `reconciled_by_*` fields to `gl_entries` and `daily_bank_transactions` tables.
    *   Create a `bank_reconciliations` table to store reconciliation sessions.
    *   Create a `bank_reconciliation_details` table to store matched pairs.
2.  **Create Models:**
    *   `BankReconciliation` model.
    *   `BankReconciliationDetail` model.
3.  **Create Service:**
    *   `BankReconciliationService` to handle all business logic.
4.  **Rewrite Component:**
    *   Rewrite the `BankReconciliation.php` component to use the new service and models.
5.  **Add Audit Logging:**
    *   Integrate the `AuditService` to log all reconciliation actions.
