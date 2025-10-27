To fully implement the **Branch Admin Settings** with global override functionality in a Laravel application, including the `[CurrencyAndLocalization]` section that was previously omitted, I’ll provide **database migrations** and **Eloquent models** for all configuration sections specified in the provided configuration. This includes `BusinessConfiguration`, `CurrencyAndLocalization`, `BranchManagement`, `InventoryManagement`, `EmployeeManagement`, `POSConfiguration`, `AccountingAndCash`, `CustomerAndSupplierManagement`, `ReportsAndAnalytics`, `SecurityAndAccess`, `NotificationsAndAlerts`, and the branch-specific settings from `[BranchConfiguration]` to `[SecurityAndAccess]`. The implementation will use the **multi-table settings** approach, as recommended, with global settings overriding branch settings unless explicitly set. I’ll also include `audit_logs` and `approval_requests` tables for compliance and support for data isolation, approval workflows, and auditability.

### **Assumptions**
1. **Laravel Version**: Laravel 12.x (latest as of October 2025).
2. **Database**: MySQL (or compatible).
3. **Existing Tables**: `branches` (with `id`, `name`, etc.) and `users` (with `id`, `branch_id`, etc.) exist.
4. **Override Mechanism**: Branch settings use `NULL` to inherit global defaults.
5. **Data Isolation**: Enforced via `branch_id` foreign keys and Laravel query scopes.
6. **Enums**: Stored as strings for flexibility, with validation in the application layer.
7. **JSON Fields**: Used for complex settings (e.g., `currency_list`, `language_options`) to avoid multiple tables for lists.

### **Database Migrations**

Below are migrations for global and branch-specific settings for all configuration sections, plus `audit_logs` and `approval_requests`. Each table includes a `branch_id` for branch settings and supports overrides by allowing `NULL` values.

#### **Migration 1: Global Business Configuration**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_business_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('company_name', 255)->default('Your Business Name');
            $table->string('logo_upload', 50)->default('enabled');
            $table->json('contact_details')->default(json_encode(['phone', 'email', 'website', 'vat_number']));
            $table->json('business_type')->default(json_encode(['retail', 'wholesale', 'services']));
            $table->json('storage_settings')->default(json_encode(['local', 's3']));
            $table->string('subscription_plan', 50)->default('basic');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_business_configurations');
    }
};
```

#### **Migration 2: Branch Business Configuration**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_business_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('company_name', 255)->nullable();
            $table->string('logo_upload', 50)->nullable();
            $table->json('contact_details')->nullable();
            $table->json('business_type')->nullable();
            $table->json('storage_settings')->nullable();
            $table->string('subscription_plan', 50)->nullable();
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_business_configurations');
    }
};
```

#### **Migration 3: Global Currency and Localization**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_currency_localizations', function (Blueprint $table) {
            $table->id();
            $table->string('multi_currency', 50)->default('enabled');
            $table->string('primary_currency', 10)->default('NGN');
            $table->string('primary_curreny_exchange_rate')->nullabale();
            $table->json('currency_list')->default(json_encode(['USD', 'EUR', 'GBP', 'INR', 'NGN]));
            $table->string('multi_tax', 50)->default('enabled');
            $table->string('default_language', 10)->default('en');
            $table->json('language_options')->default(json_encode(['en', 'es', 'fr', 'ar']));
            $table->string('date_format', 50)->default('MM/DD/YYYY');
            $table->json('units_of_measure')->default(json_encode(['piece', 'kg', 'liter']));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_currency_localizations');
    }
};
```

#### **Migration 4: Branch Currency and Localization**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_currency_localizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('currency_display', 50)->nullable(); // 'inherit' or specific currency
            $table->string('language', 10)->nullable(); // 'inherit' or specific language
            $table->string('units_local', 50)->nullable(); // 'inherit' or view-only
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_currency_localizations');
        
    }
};
```

#### **Migration 5: Global Branch Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_branch_managements', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_add', 50)->default('enabled');
            $table->string('branch_edit', 50)->default('enabled');
            $table->string('branch_delete', 50)->default('enabled');
            $table->string('branch_admin_assign', 50)->default('enabled');
            $table->string('inter_branch_transfer', 50)->default('enabled');
            $table->string('central_warehouse', 50)->default('enabled');
            $table->string('branch_hours', 50)->default('set');
            $table->string('saas_tenant', 50)->default('enabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_branch_managements');
    }
};
```

#### **Migration 6: Branch Management (BranchConfiguration)**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('branch_details', 50)->nullable(); // 'edit,approval'
            $table->string('operating_hours', 50)->nullable();
            $table->string('tax_override', 50)->nullable(); // 'view_only'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_managements');
    }
};
```

#### **Migration 7: Global Inventory Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_inventory_managements', function (Blueprint $table) {
            $table->id();
            $table->json('categories')->default(json_encode(['add', 'edit', 'delete']));
            $table->json('brands')->default(json_encode(['add', 'edit', 'delete']));
            $table->json('products')->default(json_encode(['add', 'edit', 'sku_auto']));
            $table->string('multi_variant', 50)->default('enabled');
            $table->string('stock_adjustment', 50)->default('enabled');
            $table->string('purchase_returns', 50)->default('enabled');
            $table->json('supplier_management')->default(json_encode(['add', 'edit', 'link']));
            $table->string('low_stock_alert', 50)->default('threshold:10%');
            $table->string('expiry_tracking', 50)->default('enabled');
            $table->string('import_csv', 50)->default('enabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_inventory_managements');
    }
};
```

#### **Migration 8: Branch Inventory Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_inventory_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('local_stock', 50)->nullable(); // 'add,edit,view'
            $table->string('stock_adjustment', 50)->nullable(); // 'local,approval'
            $table->string('purchase_returns', 50)->nullable(); // 'submit'
            $table->string('supplier_link', 50)->nullable(); // 'view'
            $table->string('low_stock_alert', 50)->nullable(); // 'customize'
            $table->string('csv_import', 50)->nullable(); // 'local'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_inventory_managements');
    }
};
```

#### **Migration 9: Global Employee Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_employee_managements', function (Blueprint $table) {
            $table->id();
            $table->json('roles')->default(json_encode(['create', 'edit']));
            $table->json('permissions')->default(json_encode(['pos', 'inventory', 'reports']));
            $table->json('staff_profiles')->default(json_encode(['add', 'edit', 'delete']));
            $table->json('departments')->default(json_encode(['sales', 'warehouse']));
            $table->string('shift_scheduling', 50)->default('enabled');
            $table->string('pin_login', 50)->default('enabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_employee_managements');
    }
};
```

#### **Migration 10: Branch Employee Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_employee_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->json('branch_staff')->nullable(); // 'add,edit'
            $table->json('permissions_local')->nullable(); // 'pos,local_reports'
            $table->string('pin_assign', 50)->nullable(); // 'enabled'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_employee_managements');
    }
};
```

#### **Migration 11: Global POS Configuration**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_pos_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('pos_interface', 50)->default('enabled');
            $table->json('payment_modes')->default(json_encode(['add', 'edit']));
            $table->string('receipt_template', 50)->default('custom');
            $table->string('sales_returns', 50)->default('enabled');
            $table->string('offline_mode', 50)->default('enabled');
            $table->string('online_shop_sync', 50)->default('enabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_pos_configurations');
    }
};
```

#### **Migration 12: Branch POS Configuration**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_pos_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('pos_use', 50)->nullable(); // 'enabled'
            $table->string('payment_modes', 50)->nullable(); // 'apply'
            $table->string('receipt_custom', 50)->nullable(); // 'limited'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_pos_configurations');
    }
};
```

#### **Migration 13: Global Accounting and Cash**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_accounting_cash', function (Blueprint $table) {
            $table->id();
            $table->json('expenses_categories')->default(json_encode(['add', 'edit']));
            $table->string('cash_bank', 50)->default('enabled');
            $table->string('profit_loss_reports', 50)->default('by_date');
            $table->string('accounting_entries', 50)->default('auto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_accounting_cash');
    }
};
```

#### **Migration 14: Branch Accounting and Cash**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_accounting_cash', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('local_expenses', 50)->nullable(); // 'add'
            $table->string('cash_transactions', 50)->nullable(); // 'track'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_accounting_cash');
    }
};
```

#### **Migration 15: Global Customer and Supplier Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_customer_supplier_managements', function (Blueprint $table) {
            $table->id();
            $table->json('customers')->default(json_encode(['add', 'edit', 'groups']));
            $table->json('suppliers')->default(json_encode(['add', 'edit']));
            $table->string('party_import', 50)->default('enabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_customer_supplier_managements');
    }
};
```

#### **Migration 16: Branch Customer and Supplier Management**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_customer_supplier_managements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->json('local_customers')->nullable(); // 'add,view'
            $table->string('local_suppliers', 50)->nullable(); // 'view'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_customer_supplier_managements');
    }
};
```

#### **Migration 17: Global Reports and Analytics**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_reports_analytics', function (Blueprint $table) {
            $table->id();
            $table->json('reports')->default(json_encode(['sales', 'purchases', 'stock', 'pl']));
            $table->string('custom_date_range', 50)->default('enabled');
            $table->string('multi_select_delete', 50)->default('enabled');
            $table->json('export')->default(json_encode(['csv', 'pdf']));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_reports_analytics');
    }
};
```

#### **Migration 18: Branch Reports and Analytics**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_reports_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->json('branch_reports')->nullable(); // 'sales,stock'
            $table->string('date_filter', 50)->nullable(); // 'enabled'
            $table->string('export', 50)->nullable(); // 'csv'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_reports_analytics');
    }
};
```

#### **Migration 19: Global Security and Access**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_security_access', function (Blueprint $table) {
            $table->id();
            $table->string('authentication', 50)->default('2fa');
            $table->string('audit_logs', 50)->default('enabled');
            $table->string('data_isolation', 50)->default('saas_company');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_security_access');
    }
};
```

#### **Migration 20: Branch Security and Access**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_security_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('local_logs', 50)->nullable(); // 'view'
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_security_access');
    }
};
```

#### **Migration 21: Global Notifications and Alerts**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_notifications_alerts', function (Blueprint $table) {
            $table->id();
            $table->json('alerts')->default(json_encode(['email', 'sms']));
            $table->string('task_todo', 50)->default('enabled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_notifications_alerts');
    }
};
```

#### **Migration 22: Branch Notifications and Alerts**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_notifications_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->json('alerts')->nullable();
            $table->boolean('is_overridden')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_notifications_alerts');
    }
};
```

#### **Migration 23: Audit Logs**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('setting_type', 50); // e.g., 'inventory', 'pos'
            $table->string('action', 50); // e.g., 'update', 'delete'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
```

#### **Migration 24: Approval Requests**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('setting_type', 50); // e.g., 'stock_adjustment'
            $table->string('proposed_value', 255);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('super_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
    }
};
```

### **Eloquent Models**

Below are the Eloquent models for each table, with methods to handle global overrides, branch-specific access, and query scopes for data isolation. I’ll provide models for key sections to avoid repetition, but the pattern is consistent across all sections.

#### **Model 1: GlobalBusinessConfiguration**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalBusinessConfiguration extends Model
{
    protected $fillable = ['company_name', 'logo_upload', 'contact_details', 'business_type', 'storage_settings', 'subscription_plan'];

    protected $casts = [
        'contact_details' => 'array',
        'business_type' => 'array',
        'storage_settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 2: BranchBusinessConfiguration**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchBusinessConfiguration extends Model
{
    protected $fillable = ['branch_id', 'company_name', 'logo_upload', 'contact_details', 'business_type', 'storage_settings', 'subscription_plan', 'is_overridden'];

    protected $casts = [
        'contact_details' => 'array',
        'business_type' => 'array',
        'storage_settings' => 'array',
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalBusinessConfiguration::first();

        return [
            'company_name' => $branchSettings?->company_name ?? $globalSettings?->company_name ?? 'Your Business Name',
            'logo_upload' => $branchSettings?->logo_upload ?? $globalSettings?->logo_upload ?? 'enabled',
            'contact_details' => $branchSettings?->contact_details ?? $globalSettings?->contact_details ?? ['phone', 'email', 'website', 'vat_number'],
            'business_type' => $branchSettings?->business_type ?? $globalSettings?->business_type ?? ['retail', 'wholesale', 'services'],
            'storage_settings' => $branchSettings?->storage_settings ?? $globalSettings?->storage_settings ?? ['local', 's3'],
            'subscription_plan' => $branchSettings?->subscription_plan ?? $globalSettings?->subscription_plan ?? 'basic',
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 3: GlobalCurrencyLocalization**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalCurrencyLocalization extends Model
{
    protected $fillable = ['multi_currency', 'primary_currency', 'currency_list', 'multi_tax', 'default_language', 'language_options', 'date_format', 'units_of_measure'];

    protected $casts = [
        'currency_list' => 'array',
        'language_options' => 'array',
        'units_of_measure' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 4: BranchCurrencyLocalization**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchCurrencyLocalization extends Model
{
    protected $fillable = ['branch_id', 'currency_display', 'language', 'units_local', 'is_overridden'];

    protected $casts = [
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalCurrencyLocalization::first();

        return [
            'currency_display' => $branchSettings?->currency_display === 'inherit' ? ($globalSettings?->primary_currency ?? 'USD') : ($branchSettings?->currency_display ?? $globalSettings?->primary_currency ?? 'USD'),
            'language' => $branchSettings?->language === 'inherit' ? ($globalSettings?->default_language ?? 'en') : ($branchSettings?->language ?? $globalSettings?->default_language ?? 'en'),
            'units_local' => $branchSettings?->units_local === 'inherit' ? ($globalSettings?->units_of_measure ?? ['piece', 'kg', 'liter']) : ($branchSettings?->units_local ?? $globalSettings?->units_of_measure ?? ['piece', 'kg', 'liter']),
            'multi_currency' => $globalSettings?->multi_currency ?? 'enabled', // Global only
            'currency_list' => $globalSettings?->currency_list ?? ['USD', 'EUR', 'GBP', 'INR'], // Global only
            'multi_tax' => $globalSettings?->multi_tax ?? 'enabled', // Global only
            'language_options' => $globalSettings?->language_options ?? ['en', 'es', 'fr', 'ar'], // Global only
            'date_format' => $globalSettings?->date_format ?? 'MM/DD/YYYY', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 5: GlobalBranchManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalBranchManagement extends Model
{
    protected $fillable = ['warehouse_add', 'branch_edit', 'branch_delete', 'branch_admin_assign', 'inter_branch_transfer', 'central_warehouse', 'branch_hours', 'saas_tenant'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 6: BranchManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchManagement extends Model
{
    protected $fillable = ['branch_id', 'branch_details', 'operating_hours', 'tax_override', 'is_overridden'];

    protected $casts = [
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalBranchManagement::first();

        return [
            'branch_details' => $branchSettings?->branch_details ?? $globalSettings?->branch_edit ?? 'enabled',
            'operating_hours' => $branchSettings?->operating_hours ?? $globalSettings?->branch_hours ?? 'set',
            'tax_override' => $branchSettings?->tax_override ?? 'view_only',
            'warehouse_add' => $globalSettings?->warehouse_add ?? 'enabled', // Global only
            'branch_admin_assign' => $globalSettings?->branch_admin_assign ?? 'enabled', // Global only
            'inter_branch_transfer' => $globalSettings?->inter_branch_transfer ?? 'enabled', // Global only
            'central_warehouse' => $globalSettings?->central_warehouse ?? 'enabled', // Global only
            'saas_tenant' => $globalSettings?->saas_tenant ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 7: GlobalInventoryManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalInventoryManagement extends Model
{
    protected $fillable = ['categories', 'brands', 'products', 'multi_variant', 'stock_adjustment', 'purchase_returns', 'supplier_management', 'low_stock_alert', 'expiry_tracking', 'import_csv'];

    protected $casts = [
        'categories' => 'array',
        'brands' => 'array',
        'products' => 'array',
        'supplier_management' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 8: BranchInventoryManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchInventoryManagement extends Model
{
    protected $fillable = ['branch_id', 'local_stock', 'stock_adjustment', 'purchase_returns', 'supplier_link', 'low_stock_alert', 'csv_import', 'is_overridden'];

    protected $casts = [
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalInventoryManagement::first();

        return [
            'local_stock' => $branchSettings?->local_stock ?? $globalSettings?->categories ?? ['add', 'edit', 'view'],
            'stock_adjustment' => $branchSettings?->stock_adjustment ?? $globalSettings?->stock_adjustment ?? 'local',
            'purchase_returns' => $branchSettings?->purchase_returns ?? $globalSettings?->purchase_returns ?? 'submit',
            'supplier_link' => $branchSettings?->supplier_link ?? $globalSettings?->supplier_management ?? ['view'],
            'low_stock_alert' => $branchSettings?->low_stock_alert ?? $globalSettings?->low_stock_alert ?? 'customize',
            'csv_import' => $branchSettings?->csv_import ?? $globalSettings?->import_csv ?? 'local',
            'categories' => $globalSettings?->categories ?? ['add', 'edit', 'delete'], // Global only
            'brands' => $globalSettings?->brands ?? ['add', 'edit', 'delete'], // Global only
            'products' => $globalSettings?->products ?? ['add', 'edit', 'sku_auto'], // Global only
            'multi_variant' => $globalSettings?->multi_variant ?? 'enabled', // Global only
            'expiry_tracking' => $globalSettings?->expiry_tracking ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 9: GlobalEmployeeManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalEmployeeManagement extends Model
{
    protected $fillable = ['roles', 'permissions', 'staff_profiles', 'departments', 'shift_scheduling', 'pin_login'];

    protected $casts = [
        'roles' => 'array',
        'permissions' => 'array',
        'staff_profiles' => 'array',
        'departments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 10: BranchEmployeeManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchEmployeeManagement extends Model
{
    protected $fillable = ['branch_id', 'branch_staff', 'permissions_local', 'pin_assign', 'is_overridden'];

    protected $casts = [
        'branch_staff' => 'array',
        'permissions_local' => 'array',
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalEmployeeManagement::first();

        return [
            'branch_staff' => $branchSettings?->branch_staff ?? $globalSettings?->staff_profiles ?? ['add', 'edit'],
            'permissions_local' => $branchSettings?->permissions_local ?? $globalSettings?->permissions ?? ['pos', 'local_reports'],
            'pin_assign' => $branchSettings?->pin_assign ?? $globalSettings?->pin_login ?? 'enabled',
            'roles' => $globalSettings?->roles ?? ['create', 'edit'], // Global only
            'departments' => $globalSettings?->departments ?? ['sales', 'warehouse'], // Global only
            'shift_scheduling' => $globalSettings?->shift_scheduling ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 11: GlobalPosConfiguration**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalPosConfiguration extends Model
{
    protected $fillable = ['pos_interface', 'payment_modes', 'receipt_template', 'sales_returns', 'offline_mode', 'online_shop_sync'];

    protected $casts = [
        'payment_modes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 12: BranchPosConfiguration**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchPosConfiguration extends Model
{
    protected $fillable = ['branch_id', 'pos_use', 'payment_modes', 'receipt_custom', 'is_overridden'];

    protected $casts = [
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalPosConfiguration::first();

        return [
            'pos_use' => $branchSettings?->pos_use ?? $globalSettings?->pos_interface ?? 'enabled',
            'payment_modes' => $branchSettings?->payment_modes ?? $globalSettings?->payment_modes ?? ['apply'],
            'receipt_custom' => $branchSettings?->receipt_custom ?? $globalSettings?->receipt_template ?? 'limited',
            'sales_returns' => $globalSettings?->sales_returns ?? 'enabled', // Global only
            'offline_mode' => $globalSettings?->offline_mode ?? 'enabled', // Global only
            'online_shop_sync' => $globalSettings?->online_shop_sync ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 13: GlobalAccountingCash**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalAccountingCash extends Model
{
    protected $fillable = ['expenses_categories', 'cash_bank', 'profit_loss_reports', 'accounting_entries'];

    protected $casts = [
        'expenses_categories' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 14: BranchAccountingCash**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchAccountingCash extends Model
{
    protected $fillable = ['branch_id', 'local_expenses', 'cash_transactions', 'is_overridden'];

    protected $casts = [
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
n        $globalSettings = GlobalAccountingCash::first();

        return [
            'local_expenses' => $branchSettings?->local_expenses ?? $globalSettings?->expenses_categories ?? ['add'],
            'cash_transactions' => $branchSettings?->cash_transactions ?? $globalSettings?->cash_bank ?? 'track',
            'profit_loss_reports' => $globalSettings?->profit_loss_reports ?? 'by_date', // Global only
            'accounting_entries' => $globalSettings?->accounting_entries ?? 'auto', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 15: GlobalCustomerSupplierManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalCustomerSupplierManagement extends Model
{
    protected $fillable = ['customers', 'suppliers', 'party_import'];

    protected $casts = [
        'customers' => 'array',
        'suppliers' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 16: BranchCustomerSupplierManagement**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchCustomerSupplierManagement extends Model
{
    protected $fillable = ['branch_id', 'local_customers', 'local_suppliers', 'is_overridden'];

    protected $casts = [
        'local_customers' => 'array',
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalCustomerSupplierManagement::first();

        return [
            'local_customers' => $branchSettings?->local_customers ?? $globalSettings?->customers ?? ['add', 'view'],
            'local_suppliers' => $branchSettings?->local_suppliers ?? $globalSettings?->suppliers ?? ['view'],
            'party_import' => $globalSettings?->party_import ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 17: GlobalReportsAnalytics**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalReportsAnalytics extends Model
{
    protected $fillable = ['reports', 'custom_date_range', 'multi_select_delete', 'export'];

    protected $casts = [
        'reports' => 'array',
        'export' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 18: BranchReportsAnalytics**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchReportsAnalytics extends Model
{
    protected $fillable = ['branch_id', 'branch_reports', 'date_filter', 'export', 'is_overridden'];

    protected $casts = [
        'branch_reports' => 'array',
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalReportsAnalytics::first();

        return [
            'branch_reports' => $branchSettings?->branch_reports ?? $globalSettings?->reports ?? ['sales', 'stock'],
            'date_filter' => $branchSettings?->date_filter ?? $globalSettings?->custom_date_range ?? 'enabled',
            'export' => $branchSettings?->export ?? $globalSettings?->export ?? ['csv'],
            'multi_select_delete' => $globalSettings?->multi_select_delete ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 19: GlobalSecurityAccess**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSecurityAccess extends Model
{
    protected $fillable = ['authentication', 'audit_logs', 'data_isolation'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 20: BranchSecurityAccess**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchSecurityAccess extends Model
{
    protected $fillable = ['branch_id', 'local_logs', 'is_overridden'];

    protected $casts = [
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalSecurityAccess::first();

        return [
            'local_logs' => $branchSettings?->local_logs ?? $globalSettings?->audit_logs ?? 'view',
            'authentication' => $globalSettings?->authentication ?? '2fa', // Global only
            'data_isolation' => $globalSettings?->data_isolation ?? 'saas_company', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 21: GlobalNotificationsAlerts**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalNotificationsAlerts extends Model
{
    protected $fillable = ['alerts', 'task_todo'];

    protected $casts = [
        'alerts' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### **Model 22: BranchNotificationsAlerts**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchNotificationsAlerts extends Model
{
    protected $fillable = ['branch_id', 'alerts', 'is_overridden'];

    protected $casts = [
        'alerts' => 'array',
        'is_overridden' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getEffectiveSettings(int $branchId): array
    {
        $branchSettings = self::where('branch_id', $branchId)->first();
        $globalSettings = GlobalNotificationsAlerts::first();

        return [
            'alerts' => $branchSettings?->alerts ?? $globalSettings?->alerts ?? ['email', 'sms'],
            'task_todo' => $globalSettings?->task_todo ?? 'enabled', // Global only
        ];
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 23: AuditLog**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['branch_id', 'setting_type', 'action', 'user_id', 'details'];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

#### **Model 24: ApprovalRequest**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRequest extends Model
{
    protected $fillable = ['branch_id', 'setting_type', 'proposed_value', 'status', 'super_admin_id'];

    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function superAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
```

### **Implementation Notes**

1. **Running Migrations**:
   - Place migration files in `database/migrations/`.
   - Run `php artisan migrate` to create the tables.
   - Ensure `branches` and `users` tables exist, as they are referenced by foreign keys.

2. **Override Mechanism**:
   - The `getEffectiveSettings` method in each branch model uses `??` to fall back to global settings if branch settings are `null`.
   - For `CurrencyAndLocalization`, the `inherit` value explicitly triggers global fallback (e.g., `currency_display = 'inherit'` uses `primary_currency`).
   - Example usage:
     ```php
     $settings = BranchCurrencyLocalization::getEffectiveSettings($branchId);
     return response()->json($settings);
     ```

3. **Data Isolation**:
   - The `forBranch` scope restricts queries to the authenticated user’s `branch_id`.
   - Example:
     ```php
     $settings = BranchInventoryManagement::forBranch($branchId)->first();
     ```

4. **Approval Workflow**:
   - For settings requiring approval (e.g., `stock_adjustment = approval`, `branch_details = edit,approval`):
     ```php
     ApprovalRequest::create([
         'branch_id' => $branchId,
         'setting_type' => 'stock_adjustment',
         'proposed_value' => 'local',
         'status' => 'pending',
     ]);
     ```
   - Super admins approve/reject via an API, updating the `status` and applying changes to the relevant branch table.

5. **Audit Logging**:
   - Log changes in `AuditLog`:
     ```php
     AuditLog::create([
         'branch_id' => $branchId,
         'setting_type' => 'inventory',
         'action' => 'update',
         'user_id' => auth()->id(),
         'details' => ['field' => 'low_stock_alert', 'new_value' => 'threshold:20%'],
     ]);
     ```

6. **Permissions**:
   - Use Laravel’s Gate or Spatie’s Permission package for granular permissions (e.g., `pos`, `local_reports`).
   - Example Gate:
     ```php
     Gate::define('view-branch-settings', function (User $user, Branch $branch) {
         return $user->branch_id === $branch->id && $user->hasPermissionTo('local_reports');
     });
     ```

7. **Caching**:
   - Cache global settings to reduce database queries:
     ```php
     Cache::remember('global_currency_localizations', 3600, fn () => GlobalCurrencyLocalization::first());
     ```

8. **API Endpoints**:
   - Example controller for fetching effective settings:
     ```php
     namespace App\Http\Controllers;

     use App\Models\BranchCurrencyLocalization;
     use Illuminate\Http\Request;

     class BranchCurrencyController extends Controller
     {
         public function show(Request $request, int $branchId)
         {
             $this->authorize('view-branch-settings', Branch::findOrFail($branchId));
             return response()->json(BranchCurrencyLocalization::getEffectiveSettings($branchId));
         }
     }
     ```

9. **Currency and Localization Notes**:
   - The `[CurrencyAndLocalization]` section includes global settings like `currency_list` and `language_options` that are not overridden at the branch level, per the configuration (`currency_display = inherit`).
   - Branch-level settings (`currency_display`, `language`, `units_local`) use `inherit` to explicitly fall back to global defaults.
   - JSON fields (`currency_list`, `language_options`, `units_of_measure`) store arrays for flexibility.

10. **Validation**:
    - Validate inputs in controllers (e.g., `currency_display` must be `inherit` or a valid currency code).
    - Example request validation:
      ```php
      $request->validate([
          'currency_display' => 'nullable|string|in:inherit,USD,EUR,GBP,INR',
          'language' => 'nullable|string|in:inherit,en,es,fr,ar',
      ]);
      ```

### **Additional Considerations**
- **Seeding**: Create seeders for global settings tables to initialize defaults:
  ```php
  GlobalCurrencyLocalization::create([
      'multi_currency' => 'enabled',
      'primary_currency' => 'USD',
      'currency_list' => ['USD', 'EUR', 'GBP', 'INR'],
      'multi_tax' => 'enabled',
      'default_language' => 'en',
      'language_options' => ['en', 'es', 'fr', 'ar'],
      'date_format' => 'MM/DD/YYYY',
      'units_of_measure' => ['piece', 'kg', 'liter'],
  ]);
  ```
- **Testing**: Write tests to verify override logic, data isolation, and approval workflows.
- **Scalability**: For large systems, consider database partitioning by `branch_id` or sharding for SaaS tenants.
- **Performance**: Index `branch_id` in all branch tables and cache global settings.

### **Final Answer**
The provided migrations and models cover all specified configuration sections, including `[CurrencyAndLocalization]`, for a Laravel application using the **multi-table settings** approach. Each section has global and branch-specific tables, with `NULL` or `inherit` values enabling global overrides. The `getEffectiveSettings` methods handle fallback logic, and `forBranch` scopes enforce data isolation. `audit_logs` and `approval_requests` support compliance. Run `php artisan migrate` to apply the migrations. If you need specific controller logic, seeders, or additional features (e.g., API routes), let me know!