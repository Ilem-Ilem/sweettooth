# Assets and Investments Features Integration to Laravel System

This guide explains how to integrate assets and investments features from Manager.io into the existing Laravel-based accounting system.

## Features Covered
- Investments
- Fixed Assets
- Depreciation Entries
- Intangible Assets
- Amortization Entries

## Implementation Steps

### 1. Database Migrations

```php
// Investments
Schema::create('investments', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('type'); // stocks, bonds, mutual_funds, etc.
    $table->string('symbol')->nullable();
    $table->decimal('quantity', 15, 4);
    $table->decimal('purchase_price', 15, 2);
    $table->decimal('current_market_price', 15, 2)->nullable();
    $table->date('purchase_date');
    $table->decimal('dividends_received', 15, 2)->default(0);
    $table->timestamps();
});

Schema::create('investment_market_prices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('investment_id')->constrained()->cascadeOnDelete();
    $table->date('price_date');
    $table->decimal('price', 15, 2);
    $table->timestamps();
});

// Fixed Assets
Schema::create('fixed_assets', function (Blueprint $table) {
    $table->id();
    $table->string('asset_number')->unique();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('purchase_price', 15, 2);
    $table->date('purchase_date');
    $table->decimal('salvage_value', 15, 2)->default(0);
    $table->integer('useful_life_years');
    $table->string('depreciation_method')->default('straight_line'); // straight_line, declining_balance
    $table->decimal('accumulated_depreciation', 15, 2)->default(0);
    $table->decimal('current_value', 15, 2);
    $table->string('status')->default('active'); // active, disposed
    $table->timestamps();
});

// Depreciation Entries
Schema::create('depreciation_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('fixed_asset_id')->constrained()->cascadeOnDelete();
    $table->date('depreciation_date');
    $table->decimal('depreciation_amount', 15, 2);
    $table->decimal('accumulated_depreciation', 15, 2);
    $table->timestamps();
});

// Intangible Assets
Schema::create('intangible_assets', function (Blueprint $table) {
    $table->id();
    $table->string('asset_number')->unique();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('purchase_price', 15, 2);
    $table->date('purchase_date');
    $table->integer('useful_life_years');
    $table->string('amortization_method')->default('straight_line');
    $table->decimal('accumulated_amortization', 15, 2)->default(0);
    $table->decimal('current_value', 15, 2);
    $table->string('status')->default('active');
    $table->timestamps();
});

// Amortization Entries
Schema::create('amortization_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('intangible_asset_id')->constrained()->cascadeOnDelete();
    $table->date('amortization_date');
    $table->decimal('amortization_amount', 15, 2);
    $table->decimal('accumulated_amortization', 15, 2);
    $table->timestamps();
});
```

### 2. Models and Relationships

```php
class Investment extends Model {
    public function marketPrices() { return $this->hasMany(InvestmentMarketPrice::class); }

    public function currentValue() {
        return $this->current_market_price ?? $this->marketPrices()->latest()->first()->price ?? $this->purchase_price;
    }

    public function unrealizedGainLoss() {
        return $this->currentValue() * $this->quantity - $this->purchase_price * $this->quantity;
    }
}

class FixedAsset extends Model {
    public function depreciationEntries() { return $this->hasMany(DepreciationEntry::class); }

    public function calculateDepreciation($date = null) {
        $date = $date ?? now()->toDateString();
        // Implement depreciation calculation logic
    }
}

class DepreciationEntry extends Model {
    public function fixedAsset() { return $this->belongsTo(FixedAsset::class); }
}

class IntangibleAsset extends Model {
    public function amortizationEntries() { return $this->hasMany(AmortizationEntry::class); }

    public function calculateAmortization($date = null) {
        // Similar to depreciation
    }
}

class AmortizationEntry extends Model {
    public function intangibleAsset() { return $this->belongsTo(IntangibleAsset::class); }
}
```

### 3. Controllers and Automation

- `InvestmentController` for portfolio management
- `FixedAssetController` for asset tracking
- `DepreciationController` for automated depreciation runs
- `IntangibleAssetController` for intangible assets
- `AmortizationController` for amortization processing

Implement scheduled commands for monthly depreciation/amortization.

### 4. Integration Points

- **Accounting**: Auto-create journal entries for depreciation/amortization expenses
- **Investments**: Track unrealized gains/losses, dividend income
- **Reporting**: Asset registers, depreciation schedules
- **Tax**: Depreciation for tax calculations

### 5. Additional Features

- Asset disposal tracking
- Revaluation of assets
- Insurance tracking for assets
- Integration with market data APIs for investment prices
- Automated revaluation adjustments

This adds comprehensive asset management and investment tracking capabilities.