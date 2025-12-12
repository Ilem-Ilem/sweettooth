# Missing Permissions Reference Guide

## Complete Permission Inventory

This document outlines all recommended permissions organized by business function.

---

## EXISTING PERMISSIONS (59 Total)

### Employee Guard (44)
```
✅ view-employee-dashboard
✅ view-analytics
✅ view-profile
✅ edit-profile
✅ view-production-queue
✅ start-production
✅ complete-production
✅ approve-production
✅ manage-recipes
✅ process-sale
✅ issue-refund
✅ view-daily-sales
✅ close-register
✅ receive-stock
✅ transfer-stock
✅ adjust-inventory
✅ view-stock-levels
✅ view-employees
✅ create-employees
✅ edit-employees
✅ delete-employees
✅ assign-roles
✅ view-departments
✅ view-branches
✅ view-roles
✅ view-department-reports
✅ manage-staff-schedule
✅ view-orders
✅ create-orders
✅ edit-orders
✅ delete-orders
✅ process-orders
✅ cancel-orders
✅ view-products
✅ create-products
✅ edit-products
✅ delete-products
✅ manage-inventory
✅ view-customers
✅ create-customers
✅ edit-customers
✅ delete-customers
✅ view-reports
✅ generate-reports
✅ export-reports
✅ view-settings
✅ edit-settings
```

### Web Guard (15)
```
✅ view-employees
✅ create-employees
✅ edit-employees
✅ delete-employees
✅ view-roles
✅ create-roles
✅ edit-roles
✅ delete-roles
✅ view-permissions
✅ create-permissions
✅ edit-permissions
✅ delete-permissions
✅ view-branches
✅ create-branches
✅ edit-branches
✅ delete-branches
✅ view-system-settings
✅ edit-system-settings
✅ view-audit-logs
```

---

## RECOMMENDED NEW PERMISSIONS (Add 40+)

### 1. PRODUCTION PERMISSIONS (8 new)
```
❌ quality-check-production       - Perform QA on production
❌ manage-production-schedule     - Schedule production tasks
❌ view-production-reports        - View production analytics
❌ batch-complete-production      - Bulk complete tasks
❌ recall-completed-production    - Undo completed production
❌ manage-waste-tracking          - Track production waste
❌ manage-production-callbacks     - Handle production issues
❌ view-production-analytics      - Detailed production metrics
```

### 2. SALES PERMISSIONS (6 new)
```
❌ override-product-price         - Override pricing
❌ process-bulk-discount          - Apply discounts
❌ view-sales-reports             - Sales analytics
❌ manage-gift-cards              - Handle gift card sales
❌ process-refund-approvals       - Approve refunds
❌ manage-sales-promotions        - Create/manage promotions
```

### 3. INVENTORY PERMISSIONS (8 new)
```
❌ perform-stock-take             - Conduct inventory counts
❌ manage-stock-categories        - Create/edit categories
❌ manage-suppliers               - Manage supplier info
❌ view-stock-levels-all-branches - Cross-branch inventory
❌ adjust-stock-variance          - Handle discrepancies
❌ manage-reorder-points          - Set inventory thresholds
❌ manage-warehouse-locations     - Physical stock locations
❌ view-inventory-forecast        - Demand forecasting
```

### 4. EMPLOYEE PERMISSIONS (10 new)
```
❌ view-employee-history          - Change audit trail
❌ manage-employee-leave          - Leave approvals
❌ manage-employee-payroll        - Salary/payment info
❌ manage-employee-performance    - Reviews/ratings
❌ manage-employee-shifts         - Shift assignments
❌ manage-employee-termination    - Offboarding
❌ bulk-manage-employees          - Batch employee operations
❌ import-employees               - Bulk employee import
❌ export-employees               - Bulk employee export
❌ manage-employee-documents      - HR documents
```

### 5. ORGANIZATION PERMISSIONS (7 new)
```
❌ view-organization-structure    - Org chart view
❌ manage-branch-access           - Control branch access
❌ assign-branch-managers         - Assign branch admins
❌ manage-departments             - Create/edit departments
❌ assign-department-heads        - Assign dept managers
❌ manage-cost-centers            - Financial cost centers
❌ manage-legal-entities          - Company structure
```

### 6. REPORTING PERMISSIONS (6 new)
```
❌ schedule-reports               - Schedule report generation
❌ view-advanced-analytics        - Complex analytics
❌ manage-report-templates        - Custom report builder
❌ export-bulk-reports            - Batch report export
❌ schedule-report-distribution   - Report delivery
❌ view-dashboard-builder         - Custom dashboard creation
```

### 7. SYSTEM ADMINISTRATION (8 new)
```
❌ manage-system-health           - System monitoring
❌ manage-audit-logs              - Audit trail management
❌ manage-backup                  - Database backups
❌ manage-permissions-advanced    - Permission scoping
❌ manage-role-templates          - Role templates
❌ manage-authentication          - Auth settings
❌ manage-api-keys                - API access
❌ manage-system-notifications    - Alert settings
```

### 8. SECURITY & COMPLIANCE (5 new)
```
❌ manage-two-factor-auth         - 2FA settings
❌ view-security-logs             - Security audit
❌ manage-compliance-reports      - Compliance tracking
❌ manage-data-retention          - Data privacy
❌ manage-encryption              - Data encryption
```

### 9. APPROVAL WORKFLOWS (5 new)
```
❌ approve-leave-requests         - Leave approval
❌ approve-expense-claims         - Expense approval
❌ approve-purchase-orders        - PO approval
❌ approve-high-value-orders      - Order threshold approval
❌ manage-approval-workflows      - Workflow configuration
```

### 10. BRANCH MANAGEMENT (4 new)
```
❌ view-all-branches              - List all branches
❌ manage-branch-config           - Branch settings
❌ manage-branch-hierarchy        - Branch relationships
❌ view-inter-branch-transfers    - Stock movements
```

### 11. ASSET MANAGEMENT (3 new)
```
❌ view-assets                    - Asset inventory
❌ manage-assets                  - Asset CRUD
❌ manage-asset-maintenance       - Maintenance tracking
```

### 12. CUSTOMER/SUPPLIER (4 new)
```
❌ manage-customer-pricing        - Custom pricing
❌ manage-customer-credits        - Credit limits
❌ manage-supplier-contracts      - Supplier terms
❌ manage-supplier-payments       - Supplier payments
```

---

## PERMISSION CATEGORIZATION SYSTEM

### Category Structure
```
production
├── queue
├── execution
├── quality
├── scheduling
└── reporting

sales
├── transactions
├── refunds
├── discounts
├── reporting
└── promotions

inventory
├── stock-management
├── receiving
├── transfers
├── warehousing
└── forecasting

employees
├── management
├── leaves
├── payroll
├── performance
└── scheduling

organization
├── structure
├── branches
├── departments
└── cost-centers

reports
├── generation
├── scheduling
├── distribution
└── templates

system
├── settings
├── health
├── logs
└── backup

security
├── authentication
├── compliance
└── audit

approvals
├── leaves
├── expenses
├── purchases
└── orders
```

---

## RECOMMENDED ROLE-PERMISSION MAPPINGS

### Managing Director [PROTECTED]
```
✅ ALL permissions in employees guard
✅ Full access to all modules
✅ Can assign any role
✅ Can approve any workflow
```

### Head of Production [PROTECTED]
```
✅ view-production-queue
✅ start-production
✅ complete-production
✅ approve-production
✅ manage-recipes
✅ quality-check-production
✅ manage-production-schedule
✅ view-production-reports
✅ view-production-analytics
✅ manage-production-callbacks
✅ manage-waste-tracking
✅ manage-staff-schedule
✅ view-employees (limited to prod dept)
✅ view-stock-levels
✅ view-analytics
✅ view-department-reports
✅ view-departments
❌ delete-employees
❌ delete-products
❌ process-sale
```

### Sales Manager
```
✅ process-sale
✅ issue-refund
✅ view-daily-sales
✅ close-register
✅ override-product-price
✅ process-bulk-discount
✅ view-sales-reports
✅ manage-gift-cards
✅ manage-sales-promotions
✅ view-stock-levels
✅ manage-staff-schedule
✅ view-employees (limited)
✅ view-analytics
✅ view-department-reports
❌ delete-employees
❌ process-orders (different module)
```

### Inventory Manager
```
✅ receive-stock
✅ transfer-stock
✅ adjust-inventory
✅ view-stock-levels
✅ perform-stock-take
✅ manage-stock-categories
✅ manage-suppliers
✅ view-stock-levels-all-branches
✅ adjust-stock-variance
✅ manage-reorder-points
✅ manage-warehouse-locations
✅ view-inventory-forecast
✅ view-analytics
✅ view-department-reports
❌ delete-employees
❌ process-sale
```

### HR Manager
```
✅ view-employees
✅ create-employees
✅ edit-employees
✅ delete-employees
✅ assign-roles
✅ view-employee-history
✅ manage-employee-leave
✅ manage-employee-payroll
✅ manage-employee-performance
✅ manage-employee-shifts
✅ bulk-manage-employees
✅ import-employees
✅ export-employees
✅ manage-employee-documents
✅ view-departments
✅ manage-departments
✅ assign-department-heads
✅ view-analytics
✅ view-department-reports
✅ manage-staff-schedule
❌ delete-products
❌ edit-system-settings
```

### Chef
```
✅ view-production-queue
✅ start-production
✅ complete-production
✅ manage-recipes
✅ view-stock-levels
✅ view-employees (limited to kitchen)
✅ view-daily-sales
✅ view-analytics
❌ delete-recipes
❌ approve-production
❌ manage-staff-schedule (view only)
```

### Cashier
```
✅ process-sale
✅ view-daily-sales
✅ view-stock-levels
✅ view-analytics
❌ issue-refund (manager approval needed)
❌ override-product-price
❌ delete-orders
```

---

## PERMISSION NAMING CONVENTIONS

### Standard Format: `{action}-{resource}`

**Actions:**
- `view` - Read/display access
- `create` - Create new records
- `edit` - Modify existing records
- `delete` - Remove records
- `approve` - Approval workflows
- `manage` - Full CRUD + config
- `export` - Data export
- `import` - Data import
- `assign` - Assign to users
- `override` - Exception/special access

**Resources:**
- `production-queue` - Production orders
- `recipes` - Recipes/formulas
- `sales` - Sales transactions
- `stock` - Inventory
- `employees` - Employee records
- `departments` - Departments
- `branches` - Business locations
- `reports` - Reporting features
- `settings` - System configuration

**Example:**
- `view-production-queue` ✅ (view + production-queue)
- `manage-recipes` ✅ (manage + recipes)
- `approve-production` ✅ (approve + production)
- `override-product-price` ✅ (override + product-price)

---

## MIGRATION SCRIPT

Run after adding new permissions:

```php
// In seeder or command
$newPermissions = [
    'employees' => [
        'quality-check-production' => 'Perform QA on production',
        'manage-production-schedule' => 'Schedule production tasks',
        // ... add all new permissions
    ],
];

foreach ($newPermissions as $guard => $permissions) {
    foreach ($permissions as $name => $description) {
        Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => $guard],
            [
                'description' => $description,
                'category' => 'pending', // Manual categorization
                'is_protected' => false,
            ]
        );
    }
}
```

Then:
```bash
php artisan tinker
>>> DB::table('permissions')->where('category', 'pending')->get();
# Manually update categories
```

---

## VERIFICATION

After adding permissions:

```bash
php artisan tinker

# Check count
>>> Permission::where('guard_name', 'employees')->count();
// Should show: 44 + new count

# Check protected
>>> Permission::where('is_protected', true)->get();

# Check categories
>>> Permission::groupBy('category')->count();
// Should show multiple categories

# List by category
>>> Permission::where('category', 'production')->get();
```

