# 01_SALES_DASHBOARD_CURRENCY_APPLICATION.md

## Sales Dashboard Currency Application

### Current Issues in Sales Components

#### 1. Sales Analytics (`app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`)
**Problem Areas:**
- Lines 149-154: Currency calculations use raw amounts without formatting
- Lines 187-192: Return values should be formatted with currency symbols
- Lines 222, 236: Currency-specific database aggregations

**Required Changes:**
```php
// Lines 187-192: Add currency formatting
return [
    'total_sales' => $this->formatCurrency($totalSales),
    'total_orders' => $totalOrders,
    'avg_order_value' => $this->formatCurrency($avgOrderValue),
    'total_discount' => $this->formatCurrency($totalDiscount),
    'total_tax' => $this->formatCurrency($totalTax),
    'subtotal' => $this->formatCurrency($subtotal),
    // ...
];

// Add currency formatting method
private function formatCurrency(float $amount): string
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($amount, 2);
}
```

#### 2. Sales Product List (`app/Livewire/BranchDashboard/SalesDashboard/ProductList/Index.php`)
**Problem Areas:**
- Line 174: Department pricing doesn't account for currency
- Price displays throughout component lack currency symbols

**Required Changes:**
```php
// Price formatting for display
private function formatPrice(float $price): string
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($price, 2);
}

// Apply to department pricing
$this->departmentPrice = $product->pivot->department_price;
$this->formattedDepartmentPrice = $this->formatPrice($this->departmentPrice);
```

### Implementation Impact
- Affects all financial displays in sales analytics
- Touches POS pricing and customer-facing displays
- Critical for revenue reporting accuracy

### File Changes Summary
- `app/Livewire/BranchDashboard/SalesDashboard/Analytics/Index.php`: Add currency formatting to all monetary values
- `app/Livewire/BranchDashboard/SalesDashboard/ProductList/Index.php`: Format department prices and retail prices