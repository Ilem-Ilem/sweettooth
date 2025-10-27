
  Current Implementation Status

  Inventory Module ✅ (Mostly Complete)

  - Items, Purchases, Stocks - Complete
  - Item Requests (Create/View) - Complete
  - Item Dispatches - Complete
  - Stock Movements - Complete
  - Stock Takes - Complete
  - Health Checks - Complete
  - Analytics dashboards - Complete

  Production Module ✅ (Complete)

  Based on your todo.md and recent commits:
  - ✅ Products (CRUD) - Complete
  - ✅ Product Types - Complete
  - ✅ Recipes (Create/Edit/View) - Complete
  - ✅ Production Requests (Create/View/Cancel) - Complete
  - ✅ Daily Produce Tracking - Complete
  - ✅ Raw Material Tracking - Complete (shift-dependent)
  - ✅ Record Production Batch Modal - Complete
  - ⚠ Item Request Approval Flow - Needs enhancement
  - ⚠ Kitchen Module Dashboard - Needs enhancement

  Employee Module ⚠ (Basic CRUD Complete)

  - ✅ Employee CRUD - Complete
  - ✅ Employee Shifts table/model created
  - ⚠ Shifts UI - Minimal implementation

  Analytics Module ✅ (Complete)

  - All analytics dashboards implemented

  ---
  RECOMMENDED NEXT STEPS (Priority Order)

  Based on your todo.md file and the phased implementation plan, here are the next critical features:

  1. Record Production Batch Modal (HIGHEST PRIORITY)

  Location: app/Livewire/BranchDashboard/Production/DailyProduce/Index.php:*

  What's Missing:
  - Currently the "Record Batch" button exists but does nothing
  - Need modal to record production batches with fields:
    - quantity_produced
    - quantity_approved
    - quantity_rejected
    - quality_status
    - notes
  - Should create ProductionRecord entries
  - Auto-update produced_quantity in DailyProduce table

  ---
  2. Item Request Approval Flow (For Inventory Department)

  Location: app/Livewire/BranchDashboard/Inventory/ItemRequests.php:*

  What's Missing:
  - Interface to view pending item requests
  - Approve/reject requests
  - Modify approved quantities (can approve less than requested)
  - Track approval status
  - Update quantity_approved in item_request_details

  ---
  3. Item Dispatch Interface (For Inventory Department)

  What's Missing:
  - View approved requests waiting for dispatch
  - Create dispatch records
  - Record actual quantities dispatched
  - Track dispatch status
  - Updates quantity_dispatched in ItemRequestDetails

  ---
  4. Kitchen Module Dashboard

  Location: app/Livewire/BranchDashboard/Production/KitchenModule/Index.php (file exists but incomplete)

  Should Display:
  - Today's production requests
  - Items to be collected (from item requests)
  - Current production status
  - Quick access to record production
  - Shift summary

  ---
  5. Employee Shift Management

  Location: app/Livewire/BranchDashboard/EmployeeModule/Shifts/Index.php

  Features Needed:
  - Clock in/out functionality
  - View active shifts
  - Shift assignment
  - Shift reports

  ---
  My Recommendation:

  Start with #1 - Record Production Batch Modal since:
  1. It's flagged as HIGHEST PRIORITY in your todo.md
  2. The button is already in the UI but non-functional (bad UX)
  3. It's core to the production workflow
  4. It will unblock other production features

  Then proceed with #2 - Item Request Approval Flow to complete the inventory → production pipeline.

  Would you like me to implement any of these features? If so, which one should I start with?


The configuration provided defines a **multi-branch SaaS system** with global and branch-specific settings for managing various aspects of a business, such as inventory, POS, currency, and security. The settings are organized into sections like `[BusinessConfiguration]`, `[CurrencyAndLocalization]`, `[BranchManagement]`, etc., with a subset of branch-specific settings under `[BranchConfiguration]` and related sections. A key feature is the **override system**, where global settings (set by a super admin) act as defaults for all branches, but branches can override specific settings to customize their behavior. This explanation will break down the settings structure, the override mechanism, and how they are implemented in the provided Laravel migrations and models.

---

### **Overview of Settings**

The configuration is divided into **global settings** (applicable to all branches) and **branch-specific settings** (customizable per branch). Each section corresponds to a functional area of the system, and settings within these sections control features, permissions, and behavior. Below is a summary of each section, its purpose, and key settings:

1. **[BusinessConfiguration]** (Global)
   - **Purpose**: Defines high-level business settings for branding and compliance across all branches.
   - **Key Settings**:
     - `company_name`: Branding name (e.g., "Your Business Name").
     - `logo_upload`: Enables/disables logo uploads for receipts and reports (`enabled`).
     - `contact_details`: List of contact fields (e.g., `["phone", "email", "website", "vat_number"]`).
     - `business_type`: Supported business types (e.g., `["retail", "wholesale", "services"]`).
     - `storage_settings`: File storage options (e.g., `["local", "s3"]`).
     - `subscription_plan`: SaaS plan (e.g., `basic`, `pro`, `enterprise`).
   - **Scope**: Global, applied to all branches unless overridden (though branch overrides are rare for this section).

2. **[CurrencyAndLocalization]** (Global and Branch-Specific)
   - **Purpose**: Manages currency, language, and unit settings for transactions and UI.
   - **Global Settings**:
     - `multi_currency`: Enables multiple currency support (`enabled`).
     - `primary_currency`: Default currency (e.g., `USD`).
     - `currency_list`: Supported currencies (e.g., `["USD", "EUR", "GBP", "INR"]`).
     - `multi_tax`: Supports multiple tax types (e.g., VAT + GST, `enabled`).
     - `default_language`: Default UI language (e.g., `en`).
     - `language_options`: Available languages (e.g., `["en", "es", "fr", "ar"]`).
     - `date_format`: Default date format (e.g., `MM/DD/YYYY`).
     - `units_of_measure`: Product units (e.g., `["piece", "kg", "liter"]`).
   - **Branch-Specific Settings** (from `[BranchConfiguration]`):
     - `currency_display`: Currency shown in branch UI (`inherit` or specific, e.g., `USD`).
     - `language`: Branch UI language (`inherit` or specific, e.g., `en`).
     - `units_local`: Units used locally (`inherit` or `view`).
   - **Scope**: Global settings apply to all branches, with branches able to override `currency_display`, `language`, and `units_local`.

3. **[BranchManagement]** (Global)
   - **Purpose**: Controls branch and warehouse management features.
   - **Key Settings**:
     - `warehouse_add`, `branch_edit`, `branch_delete`, `branch_admin_assign`, `inter_branch_transfer`, `central_warehouse`, `saas_tenant`: Enable/disable branch management features (all `enabled`).
     - `branch_hours`: Allows setting operating hours (`set`).
   - **Scope**: Global, with branch-specific overrides for `branch_details`, `operating_hours`, and `tax_override`.

4. **[BranchConfiguration]** (Branch-Specific)
   - **Purpose**: Defines branch-specific management settings, overriding global `[BranchManagement]`.
   - **Key Settings**:
     - `branch_details`: Edit branch info with approval (`edit,approval`).
     - `operating_hours`: Local POS hours (`set`).
     - `tax_override`: View global tax settings (`view_only`).
   - **Scope**: Branch-specific, with global defaults from `[BranchManagement]`.

5. **[InventoryManagement]** (Global and Branch-Specific)
   - **Purpose**: Manages product and stock operations.
   - **Global Settings**:
     - `categories`, `brands`, `products`: CRUD operations (e.g., `["add", "edit", "delete"]`).
     - `multi_variant`, `stock_adjustment`, `purchase_returns`, `expiry_tracking`, `import_csv`: Enable/disable features (all `enabled`).
     - `supplier_management`: Supplier actions (e.g., `["add", "edit", "link"]`).
     - `low_stock_alert`: Threshold for alerts (e.g., `threshold:10%`).
   - **Branch-Specific Settings**:
     - `local_stock`: Branch stock actions (`add,edit,view`).
     - `stock_adjustment`: Adjustments with approval (`local,approval`).
     - `purchase_returns`: Return submissions (`submit`).
     - `supplier_link`: View assigned suppliers (`view`).
     - `low_stock_alert`: Custom threshold (`customize`).
     - `csv_import`: Local imports (`local`).
   - **Scope**: Global settings apply unless branches override specific fields.

6. **[EmployeeManagement]** (Global and Branch-Specific)
   - **Purpose**: Manages staff roles and permissions.
   - **Global Settings**:
     - `roles`, `permissions`, `staff_profiles`, `departments`: Define roles and access (e.g., `["pos", "inventory", "reports"]`).
     - `shift_scheduling`, `pin_login`: Enable/disable features (`enabled`).
   - **Branch-Specific Settings**:
     - `branch_staff`: Local employee management (`add,edit`).
     - `permissions_local`: Branch-limited permissions (`pos,local_reports`).
     - `pin_assign`: Local POS pin assignment (`enabled`).
   - **Scope**: Global defaults with branch overrides.

7. **[POSConfiguration]** (Global and Branch-Specific)
   - **Purpose**: Configures point-of-sale features.
   - **Global Settings**:
     - `pos_interface`, `sales_returns`, `offline_mode`, `online_shop_sync`: Enable/disable features (`enabled`).
     - `payment_modes`: Payment options (e.g., `["add", "edit"]`).
     - `receipt_template`: Receipt customization (`custom`).
   - **Branch-Specific Settings**:
     - `pos_use`: Local POS access (`enabled`).
     - `payment_modes`: Apply global modes (`apply`).
     - `receipt_custom`: Limited customization (`limited`).
   - **Scope**: Global defaults with branch overrides.

8. **[AccountingAndCash]** (Global and Branch-Specific)
   - **Purpose**: Manages financial tracking.
   - **Global Settings**:
     - `expenses_categories`: Expense tracking (e.g., `["add", "edit"]`).
     - `cash_bank`, `profit_loss_reports`, `accounting_entries`: Financial features (`enabled`, `by_date`, `auto`).
   - **Branch-Specific Settings**:
     - `local_expenses`: Branch expense tracking (`add`).
     - `cash_transactions`: Local payment tracking (`track`).
   - **Scope**: Global defaults with branch overrides.

9. **[CustomerAndSupplierManagement]** (Global and Branch-Specific)
   - **Purpose**: Manages customer and supplier data.
   - **Global Settings**:
     - `customers`, `suppliers`: CRUD operations (e.g., `["add", "edit", "groups"]`).
     - `party_import`: CSV import (`enabled`).
   - **Branch-Specific Settings**:
     - `local_customers`: Branch customer management (`add,view`).
     - `local_suppliers`: View assigned suppliers (`view`).
   - **Scope**: Global defaults with branch overrides.

10. **[ReportsAndAnalytics]** (Global and Branch-Specific)
    - **Purpose**: Configures reporting capabilities.
    - **Global Settings**:
      - `reports`: Available reports (e.g., `["sales", "purchases", "stock", "pl"]`).
      - `custom_date_range`, `multi_select_delete`: Report features (`enabled`).
      - `export`: Export formats (e.g., `["csv", "pdf"]`).
    - **Branch-Specific Settings**:
      - `branch_reports`: Local reports (`sales,stock`).
      - `date_filter`: Date filtering (`enabled`).
      - `export`: Export format (`csv`).
    - **Scope**: Global defaults with branch overrides.

11. **[SecurityAndAccess]** (Global and Branch-Specific)
    - **Purpose**: Manages security and access controls.
    - **Global Settings**:
      - `authentication`: Login security (`2fa`).
      - `audit_logs`: Change tracking (`enabled`).
      - `data_isolation`: Tenant separation (`saas_company`).
    - **Branch-Specific Settings**:
      - `local_logs`: View branch audit logs (`view`).
    - **Scope**: Global defaults with branch overrides for logs.

12. **[NotificationsAndAlerts]** (Global and Branch-Specific)
    - **Purpose**: Configures alerts and tasks.
    - **Global Settings**:
      - `alerts`: Notification channels (e.g., `["email", "sms"]`).
      - `task_todo`: Admin tasks (`enabled`).
    - **Branch-Specific Settings**:
      - `alerts`: Branch-specific notification channels (e.g., `["email"]`).
    - **Scope**: Global defaults with branch overrides for alerts.

13. **Additional Tables**:
    - `audit_logs`: Tracks changes (e.g., setting updates) with `branch_id`, `setting_type`, `action`, `user_id`, and `details`.
    - `approval_requests`: Manages settings requiring super admin approval (e.g., `stock_adjustment`, `branch_details`) with `branch_id`, `setting_type`, `proposed_value`, `status`, and `super_admin_id`.

---

### **Override System**

The override system ensures that **global settings** act as defaults for all branches, but branches can **explicitly override** specific settings to customize their behavior. This is critical for a multi-branch SaaS system with tenant isolation (`saas_tenant = enabled`). Here’s how it works:

1. **Global Defaults**:
   - Global settings (e.g., `global_currency_localizations.primary_currency = USD`) are defined by a super admin and apply to all branches unless overridden.
   - Stored in global tables (e.g., `global_currency_localizations`, `global_inventory_managements`).
   - Typically managed by super admins and rarely change.

2. **Branch Overrides**:
   - Branches can override specific settings (e.g., `branch_currency_localizations.currency_display = EUR`) to tailor functionality.
   - Stored in branch-specific tables (e.g., `branch_currency_localizations`, `branch_inventory_managements`) with a `branch_id` foreign key.
   - If a branch setting is `NULL` (or `inherit` for some fields like `currency_display`), the global setting is used.

3. **Inheritance Logic**:
   - When fetching a setting, the system checks the branch-specific table first. If the setting is `NULL` (or `inherit`), it falls back to the global table.
   - Example: For `currency_display`:
     - Branch setting: `branch_currency_localizations.currency_display = NULL` or `'inherit'`.
     - Global setting: `global_currency_localizations.primary_currency = USD`.
     - Effective setting: `USD` (global default).
   - If `branch_currency_localizations.currency_display = EUR`, the branch uses `EUR`.

4. **Approval Workflow**:
   - Some settings (e.g., `stock_adjustment = local,approval`, `branch_details = edit,approval`) require super admin approval before changes are applied.
   - Stored in the `approval_requests` table with `status` (`pending`, `approved`, `rejected`).
   - Example: A branch admin proposes `stock_adjustment = local`. The change is logged in `approval_requests` and only applied to `branch_inventory_managements` after approval.

5. **Data Isolation**:
   - Branch admins can only access their branch’s settings (enforced via `branch_id` filters).
   - Global settings are read-only for branch admins, ensuring tenant separation (`data_isolation = saas_company`).

6. **Auditability**:
   - Changes to settings are logged in the `audit_logs` table with `branch_id`, `setting_type`, `action`, and `details`.
   - Branch admins can view logs for their branch (`local_logs = view`).

---

### **Implementation in Laravel**

The Laravel migrations and models implement this structure using a **multi-table approach**, as described below:

#### **Database Structure**
- **Global Tables**: One table per configuration section (e.g., `global_business_configurations`, `global_currency_localizations`). These store default settings.
  - Example: `global_currency_localizations` has columns like `primary_currency`, `currency_list` (JSON), `multi_tax`.
- **Branch Tables**: Corresponding tables (e.g., `branch_currency_localizations`, `branch_inventory_managements`) with `branch_id` and nullable columns for overrides.
  - Example: `branch_currency_localizations` has `currency_display`, `language`, `units_local`, which are `NULL` if global defaults apply.
- **Audit Logs**: `audit_logs` table tracks changes with `branch_id`, `setting_type`, `action`, `user_id`, and `details` (JSON).
- **Approval Requests**: `approval_requests` table stores pending changes with `branch_id`, `setting_type`, `proposed_value`, `status`, and `super_admin_id`.

#### **Model Logic**
- **Global Models**: Simple Eloquent models (e.g., `GlobalCurrencyLocalization`) with `fillable` and `casts` for JSON fields.
- **Branch Models**: Include:
  - `BelongsTo` relationship to `Branch` for `branch_id`.
  - `forBranch` scope to filter by `branch_id` (e.g., `BranchCurrencyLocalization::forBranch($branchId)`).
  - `getEffectiveSettings` method to combine branch and global settings using `??` for fallbacks or explicit `inherit` checks.
  - Example:
    ```php
    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalCurrencyLocalization::first();
        return [
            'currency_display' => $branchSettings?->currency_display === 'inherit' ? ($globalSettings?->primary_currency ?? 'USD') : ($branchSettings?->currency_display ?? $globalSettings?->primary_currency ?? 'USD'),
            'language' => $branchSettings?->language === 'inherit' ? ($globalSettings?->default_language ?? 'en') : ($branchSettings?->language ?? $globalSettings?->default_language ?? 'en'),
            // ...
        ];
    }
    ```
- **AuditLog and ApprovalRequest Models**: Include relationships to `Branch` and `User`, with scopes for branch-specific filtering.

#### **Override Mechanism in Models**
- The `getEffectiveSettings` method in each branch model implements the override logic:
  - Checks branch settings first.
  - Falls back to global settings if `NULL` or `inherit`.
  - Example: For `branch_inventory_managements.low_stock_alert`, if `NULL`, use `global_inventory_managements.low_stock_alert` (`threshold:10%`).
- For settings like `currency_display`, the value `inherit` explicitly triggers the global `primary_currency`.

#### **Data Isolation**
- The `forBranch` scope ensures branch admins only access their branch’s data:
  ```php
  public function scopeForBranch($query, int $branchId)
  {
      return $query->where('branch_id', $branchId);
  }
  ```
- Used in controllers:
  ```php
  $settings = BranchInventoryManagement::forBranch($branchId)->first();
  ```

#### **Approval Workflow**
- Settings requiring approval (e.g., `stock_adjustment`) are submitted to `approval_requests`:
  ```php
  ApprovalRequest::create([
      'branch_id' => $branchId,
      'setting_type' => 'stock_adjustment',
      'proposed_value' => 'local',
      'status' => 'pending',
  ]);
  ```
- Super admins approve/reject, updating the branch table if approved.

#### **Audit Logging**
- Changes are logged in `audit_logs`:
  ```php
  AuditLog::create([
      'branch_id' => $branchId,
      'setting_type' => 'inventory',
      'action' => 'update',
      'user_id' => auth()->id(),
      'details' => ['field' => 'low_stock_alert', 'new_value' => 'threshold:20%'],
  ]);
  ```

#### **Permissions**
- Permissions (e.g., `pos`, `local_reports`) are enforced using Laravel’s Gate or Spatie’s Permission package:
  ```php
  Gate::define('view-branch-settings', function (User $user, Branch $branch) {
      return $user->branch_id === $branch->id && $user->hasPermissionTo('local_reports');
  });
  ```

#### **Currency and Localization Specifics**
- Global settings like `currency_list` and `language_options` are stored as JSON arrays and apply to all branches.
- Branch settings (`currency_display`, `language`, `units_local`) can be `inherit` or specific values.
- Example: If `branch_currency_localizations.currency_display = inherit`, the system uses `global_currency_localizations.primary_currency`.

---

### **Example Workflow**
1. **Super Admin Sets Global Defaults**:
   - Updates `global_currency_localizations`:
     ```php
     GlobalCurrencyLocalization::updateOrCreate([], [
         'primary_currency' => 'USD',
         'currency_list' => ['USD', 'EUR', 'GBP', 'INR'],
         'multi_currency' => 'enabled',
     ]);
     ```

2. **Branch Admin Overrides**:
   - Updates `branch_currency_localizations` for branch ID 1:
     ```php
     BranchCurrencyLocalization::updateOrCreate(
         ['branch_id' => 1],
         ['currency_display' => 'EUR', 'is_overridden' => true]
     );
     ```

3. **Fetching Effective Settings**:
   - API call:
     ```php
     $settings = BranchCurrencyLocalization::getEffectiveSettings(1);
     // Returns: ['currency_display' => 'EUR', 'language' => 'en', ...]
     ```

4. **Approval for Restricted Setting**:
   - Branch admin proposes `stock_adjustment = local`:
     ```php
     ApprovalRequest::create([
         'branch_id' => 1,
         'setting_type' => 'stock_adjustment',
         'proposed_value' => 'local',
         'status' => 'pending',
     ]);
     ```
   - Super admin approves, updating `branch_inventory_managements`.

5. **Audit Log**:
   - Log the change:
     ```php
     AuditLog::create([
         'branch_id' => 1,
         'setting_type' => 'inventory',
         'action' => 'update',
         'user_id' => auth()->id(),
         'details' => ['field' => 'stock_adjustment', 'new_value' => 'local'],
     ]);
     ```

---

### **Key Features of the Override System**
- **Flexibility**: Branches can customize settings (e.g., `low_stock_alert = threshold:20%`) while inheriting global defaults for others.
- **Control**: Super admins maintain global standards (e.g., `primary_currency`, `authentication`) with approval workflows for sensitive changes.
- **Isolation**: Branch admins are restricted to their `branch_id`, ensuring SaaS tenant separation.
- **Scalability**: The multi-table structure supports many branches with efficient indexing on `branch_id`.
- **Auditability**: All changes are tracked, and branch admins can only view their logs.

---

### **Final Answer**
The settings system is organized into global and branch-specific configurations across multiple functional areas (e.g., `CurrencyAndLocalization`, `InventoryManagement`). Global settings, set by super admins, act as defaults, while branches can override specific settings (e.g., `currency_display`, `low_stock_alert`) using `NULL` or `inherit` to fall back to global values. The Laravel implementation uses separate tables for each section (e.g., `global_currency_localizations`, `branch_currency_localizations`), with `getEffectiveSettings` methods handling overrides via `??` or explicit `inherit` checks. Data isolation is enforced via `branch_id` scopes, and approval workflows and audit logs ensure compliance. This structure supports the SaaS model with tenant isolation and granular permissions. If you need further details or specific code snippets (e.g., API controllers), let me know!