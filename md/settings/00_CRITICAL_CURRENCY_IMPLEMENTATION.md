# 00_CRITICAL_CURRENCY_IMPLEMENTATION.md

## Critical Priority: Core Currency Settings Implementation

### Current State
Currency settings are configured but not applied throughout the platform. This is the highest priority as it affects all financial calculations and displays.

### Immediate Implementation Points

#### 1. Sales POS System (`app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php`)
**Current Issues:**
- Lines 647-655: Hardcoded "GHS" currency symbol
- Lines 619-632: `number_format()` with fixed 2 decimals
- No integration with currency settings

**Required Changes:**
```php
// Replace hardcoded currency formatting
private function formatCurrency(float $amount): string
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($amount, 2);
}

private function getCurrencySymbol(string $currency): string
{
    $symbols = [
        'NGN' => '₦',
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'GHS' => '₵'
    ];
    return $symbols[$currency] ?? $currency;
}
```

**Apply to:**
- Line 620: `$price = number_format($item->unit_price, 2);`
- Line 621: `$total = number_format($item->total, 2);`
- Lines 647-655: Replace "GHS" with dynamic currency

#### 2. Base Dashboard Currency Formatting (`app/Livewire/Dashboards/BaseDashboard.php`)
**Current Issues:**
- Line 214: Hardcoded "$" symbol
- No multi-currency support

**Required Changes:**
```php
protected function formatCurrency(float $amount): string
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($amount, 2);
}
```

#### 3. Inventory Purchase System (`app/Livewire/BranchDashboard/Inventory/Purchases.php`)
**Current Issues:**
- Line 200: Comment indicates NGN-only, but should be configurable
- Currency conversion calculations not using settings

**Required Changes:**
```php
// Use currency settings for purchase calculations
$baseCurrency = Settings::currencyLocalization('primary_currency', 'NGN');
$multiCurrency = Settings::currencyLocalization('multi_currency', 'enabled') === 'enabled';

// Apply currency conversion logic based on settings
```

#### 4. Multi-Currency Service (`app/Services/MultiCurrencyService.php`)
**Current Issues:**
- Line 11: Hardcoded USD as base currency
- No integration with settings

**Required Changes:**
```php
public function __construct()
{
    $this->baseCurrency = Settings::currencyLocalization('primary_currency', 'USD');
}
```

### Database Impact
- Settings are already stored in `global_currency_localization` and `branch_currency_localization` tables
- Branch-specific overrides are supported via the Settings helper

### Testing Requirements
- Test currency display in POS receipts
- Test currency conversion in purchases
- Test multi-currency transaction recording
- Verify settings changes propagate immediately</content>
<parameter name="filePath">md/settings/00_CRITICAL_CURRENCY_IMPLEMENTATION.md