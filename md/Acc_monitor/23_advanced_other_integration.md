# Advanced and Other Features Integration to Laravel System

This guide explains how to integrate advanced and miscellaneous features from Manager.io into the existing Laravel-based accounting system.

## Features Covered
- Projects
- Capital Accounts
- Special Accounts
- Folders

## Implementation Steps

### 1. Database Migrations

```php
// Projects
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('customer_id')->nullable()->constrained();
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->decimal('budget', 15, 2)->nullable();
    $table->string('status')->default('active'); // active, completed, on_hold
    $table->timestamps();
});

// Capital Accounts
Schema::create('capital_accounts', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('owner_id')->constrained('users'); // Assuming users table for ownership
    $table->decimal('initial_contribution', 15, 2);
    $table->decimal('current_balance', 15, 2);
    $table->timestamps();
});

Schema::create('capital_contributions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('capital_account_id')->constrained()->cascadeOnDelete();
    $table->date('contribution_date');
    $table->decimal('amount', 15, 2);
    $table->text('description')->nullable();
    $table->timestamps();
});

Schema::create('capital_distributions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('capital_account_id')->constrained()->cascadeOnDelete();
    $table->date('distribution_date');
    $table->decimal('amount', 15, 2);
    $table->text('description')->nullable();
    $table->timestamps();
});

// Special Accounts
Schema::create('special_accounts', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('type'); // suspense, clearing, adjustment, etc.
    $table->text('purpose')->nullable();
    $table->boolean('system_account')->default(false);
    $table->timestamps();
});

// Folders (for organizing records)
Schema::create('folders', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('type'); // customers, suppliers, invoices, etc.
    $table->foreignId('parent_id')->nullable()->constrained('folders')->cascadeOnDelete();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// Add folder relationships to existing tables
Schema::table('customers', function (Blueprint $table) {
    $table->foreignId('folder_id')->nullable()->constrained();
});

Schema::table('suppliers', function (Blueprint $table) {
    $table->foreignId('folder_id')->nullable()->constrained();
});

// Projects relationships
Schema::table('sales_invoices', function (Blueprint $table) {
    $table->foreignId('project_id')->nullable()->constrained();
});

Schema::table('time_entries', function (Blueprint $table) {
    $table->foreignId('project_id')->nullable()->constrained();
});
```

### 2. Models and Relationships

```php
class Project extends Model {
    public function customer() { return $this->belongsTo(Customer::class); }
    public function salesInvoices() { return $this->hasMany(SalesInvoice::class); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }

    public function totalRevenue() {
        return $this->salesInvoices()->sum('total');
    }

    public function totalTime() {
        return $this->timeEntries()->sum('hours_worked');
    }
}

class CapitalAccount extends Model {
    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }
    public function contributions() { return $this->hasMany(CapitalContribution::class); }
    public function distributions() { return $this->hasMany(CapitalDistribution::class); }
}

class Folder extends Model {
    public function parent() { return $this->belongsTo(Folder::class, 'parent_id'); }
    public function children() { return $this->hasMany(Folder::class, 'parent_id'); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function suppliers() { return $this->hasMany(Supplier::class); }
}

class SpecialAccount extends Model {
    // For handling special accounting scenarios
}
```

### 3. Controllers and Features

- `ProjectController` for project management and reporting
- `CapitalAccountController` for owner equity tracking
- `FolderController` for organizational structure
- `SpecialAccountController` for advanced accounting

### 4. Integration Points

- **Projects**: Track profitability, time allocation, customer billing
- **Capital Accounts**: Owner contributions and distributions tracking
- **Special Accounts**: Handle complex accounting scenarios
- **Folders**: Organize customers, suppliers, and other records hierarchically

### 5. Additional Features

- Project budgeting and variance analysis
- Multi-level folder hierarchies
- Capital account reconciliation
- Special account rules and automation

This completes the integration of advanced organizational and accounting features.