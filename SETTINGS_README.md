# Super Admin Settings Module

## Overview
A complete settings management system for the SweetTooth POS application with 11 different configuration modules.

## Created Files

### Livewire Components (PHP)
Location: `app/Livewire/SuperAdmin/Settings/`

1. **Index.php** - Main settings page controller
2. **BusinessConfiguration.php** - Business info, branding, contacts
3. **CurrencyLocalization.php** - Currency, language, date formats
4. **BranchManagement.php** - Branch operations and administration
5. **InventoryManagement.php** - Product, stock, supplier management
6. **EmployeeManagement.php** - Roles, permissions, staff profiles
7. **PosConfiguration.php** - POS interface and payment modes
8. **AccountingCash.php** - Expenses, cash/bank management
9. **CustomerSupplier.php** - Customer and supplier management
10. **ReportsAnalytics.php** - Report generation settings
11. **SecurityAccess.php** - Authentication and audit settings
12. **NotificationsAlerts.php** - Alert and notification settings

### Blade Views
Location: `resources/views/livewire/super-admin/settings/`

- Corresponding `.blade.php` files for each component above
- Main index view with tabbed navigation

## Features

### Fully Functional Components
1. **Business Configuration** ✅
   - Company name, business type
   - Logo upload
   - Contact details (phone, email, website, VAT)
   - Storage settings (local/S3)
   - Subscription plan management

2. **Currency & Localization** ✅
   - Multi-currency support toggle
   - Primary currency selection
   - Multi-tax support
   - Language selection
   - Date format preferences

3. **Inventory Management** ✅
   - Category management (add/edit/delete)
   - Brand management
   - Multi-variant products
   - Auto SKU generation
   - Stock adjustments
   - Purchase returns
   - Supplier management
   - Low stock alerts with threshold
   - Expiry tracking
   - CSV import/export

### Database Integration
- All functional components save to respective global settings tables:
  - `global_business_configurations`
  - `global_currency_localizations`
  - `global_inventory_managements`
- Uses Eloquent models for data persistence
- Form validation included
- Success messages on save

## UI Features
- Dark mode support
- Responsive design (mobile-friendly)
- Tabbed navigation (11 tabs)
- Setting groups with visual organization
- Tailwind CSS styling
- Alpine.js for interactivity
- Livewire for reactive components

## Usage

### Access the Settings Page
```
http://your-app-url/super-admin/settings
```

### Route
```php
Route::get('/super-admin/settings', \App\Livewire\SuperAdmin\Settings\Index::class)
    ->name('super-admin.settings');
```

### Example: Using a Component
```blade
@livewire('super-admin.settings.business-configuration')
```

## Next Steps

### To Enhance Further:
1. Add remaining detailed implementations for:
   - Branch Management (branch operations)
   - Employee Management (roles & permissions)
   - POS Configuration (payment modes, receipts)
   - Accounting & Cash (expense categories)
   - Customer & Supplier (party management)
   - Reports & Analytics (report types)
   - Security & Access (authentication, audit logs)
   - Notifications & Alerts (alert channels)

2. Add file upload functionality for:
   - Company logo
   - Receipt templates
   - CSV imports

3. Add validation rules for all fields

4. Implement branch-specific override functionality

5. Add approval workflow for sensitive settings

## Dependencies
- Laravel 11+
- Livewire 3.x
- Tailwind CSS 3.x
- Alpine.js 3.x

## File Structure
```
app/
└── Livewire/
    └── SuperAdmin/
        └── Settings/
            ├── Index.php
            ├── BusinessConfiguration.php
            ├── CurrencyLocalization.php
            └── ... (9 more components)

resources/
└── views/
    └── livewire/
        └── super-admin/
            └── settings/
                ├── index.blade.php
                ├── business-configuration.blade.php
                ├── currency-localization.blade.php
                └── ... (9 more views)
```

## Models Used
- `App\Models\GlobalBusinessConfiguration`
- `App\Models\GlobalCurrencyLocalization`
- `App\Models\GlobalInventoryManagement`
- `App\Models\GlobalEmployeeManagement`
- `App\Models\GlobalPosConfiguration`
- `App\Models\GlobalAccountingCash`
- `App\Models\GlobalCustomerSupplierManagement`
- `App\Models\GlobalReportsAnalytics`
- `App\Models\GlobalSecurityAccess`
- `App\Models\GlobalNotificationsAlerts`

All models are already created with proper fillable fields, casts, and relationships.
