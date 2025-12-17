# 02_INVENTORY_CURRENCY_INTEGRATION.md

## Inventory System Currency Integration

### Current Issues in Inventory Components

#### 1. Inventory Purchases (`app/Livewire/BranchDashboard/Inventory/Purchases.php`)
**Problem Areas:**
- Line 200: Hardcoded NGN assumption in comment
- Lines 283, 367: FOB calculations using fixed currency
- Purchase display lacks currency formatting
- Multi-currency conversion not implemented

**Required Changes:**
```php
// Replace hardcoded currency assumptions
private function getPurchaseCurrency(): string
{
    return Settings::currencyLocalization('primary_currency', 'NGN');
}

// Add currency conversion for FOB calculations
private function convertFOBToBaseCurrency(float $fobAmount, string $fromCurrency): float
{
    if ($fromCurrency === $this->getPurchaseCurrency()) {
        return $fobAmount;
    }
    
    $multiCurrencyService = new MultiCurrencyService();
    return $multiCurrencyService->convert($fobAmount, $fromCurrency, $this->getPurchaseCurrency());
}

// Update display formatting
private function formatCurrency(float $amount): string
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($amount, 2);
}
```

#### 2. Inventory Stock Management (`app/Livewire/BranchDashboard/Inventory/Stocks.php`)
**Problem Areas:**
- Stock valuation displays without currency symbols
- Cost calculations don't account for currency changes
- Inventory value reports not currency-aware

**Required Changes:**
```php
// Add currency context to stock valuation
private function calculateStockValue(): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $multiCurrency = Settings::currencyLocalization('multi_currency', 'disabled') === 'enabled';
    
    return [
        'total_value' => $this->formatCurrency($totalValue),
        'currency' => $currency,
        'multi_currency_enabled' => $multiCurrency,
        'valuation_date' => now()->format($this->getDateFormat())
    ];
}

private function getDateFormat(): string
{
    return Settings::currencyLocalization('date_format', 'MM/DD/YYYY');
}
```

#### 3. Inventory Analytics (`app/Livewire/BranchDashboard/Inventory/Analytics.php`)
**Problem Areas:**
- Cost analysis reports lack currency formatting
- Turnover calculations don't account for currency fluctuations
- Reorder suggestions not currency-aware

**Required Changes:**
```php
// Currency-aware analytics calculations
private function generateCostAnalysis(): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'total_inventory_cost' => $this->formatCurrency($totalCost),
        'average_item_cost' => $this->formatCurrency($avgCost),
        'currency_symbol' => $this->getCurrencySymbol($currency),
        'cost_trend' => $this->calculateCostTrend($currency)
    ];
}
```

### Database Integration Points
- `purchase_items` table should include currency column
- `stocks` valuation records need currency context
- `inventory_movements` require currency conversion tracking

### Testing Requirements
- Test purchase order creation with different currencies
- Verify stock valuation currency conversion
- Test inventory reports with currency switching
- Validate multi-currency inventory valuations