# 04_ACCOUNTING_CURRENCY_INTEGRATION.md

## Accounting System Currency Integration

### Current Issues in Accounting Components

#### 1. Journal Entries and GL (`app/Models/JournalEntry.php`)
**Problem Areas:**
- Debit/credit amounts stored without currency context
- GL balance calculations assume single currency
- Financial reports not multi-currency aware

**Required Changes:**
```php
// Add currency context to journal entries
public function save(array $options = [])
{
    $this->currency = $this->currency ?? Settings::currencyLocalization('primary_currency', 'NGN');
    return parent::save($options);
}

// Currency-aware balance calculations
public function getFormattedDebitAttribute(): string
{
    return $this->formatCurrency($this->debit, $this->currency);
}

public function getFormattedCreditAttribute(): string
{
    return $this->formatCurrency($this->credit, $this->currency);
}

private function formatCurrency(float $amount, string $currency): string
{
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($amount, 2);
}
```

#### 2. Accounting Reports (`app/Services/AccountingReportService.php`)
**Problem Areas:**
- Trial balance lacks currency formatting
- Income statement calculations not currency-aware
- Balance sheet not multi-currency compatible

**Required Changes:**
```php
// Currency-aware financial statements
public function generateTrialBalance($period = null): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $multiCurrency = Settings::currencyLocalization('multi_currency', 'disabled') === 'enabled';
    
    $trialBalance = $this->calculateTrialBalance($period);
    
    return [
        'period' => $period,
        'currency' => $currency,
        'multi_currency_enabled' => $multiCurrency,
        'accounts' => $this->formatAccountsWithCurrency($trialBalance),
        'total_debits' => $this->formatCurrency($trialBalance->total_debits),
        'total_credits' => $this->formatCurrency($trialBalance->total_credits),
        'is_balanced' => abs($trialBalance->total_debits - $trialBalance->total_credits) < 0.01
    ];
}

// Income statement with currency conversion
public function generateIncomeStatement($startDate, $endDate): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'period' => ['start' => $startDate, 'end' => $endDate],
        'currency' => $currency,
        'revenue' => [
            'sales_revenue' => $this->formatCurrency($salesRevenue),
            'service_revenue' => $this->formatCurrency($serviceRevenue),
            'total_revenue' => $this->formatCurrency($totalRevenue)
        ],
        'expenses' => [
            'cost_of_goods_sold' => $this->formatCurrency($cogs),
            'operating_expenses' => $this->formatCurrency($operatingExpenses),
            'total_expenses' => $this->formatCurrency($totalExpenses)
        ],
        'net_income' => $this->formatCurrency($netIncome),
        'gross_profit_margin' => $grossProfitMargin . '%'
    ];
}

// Balance sheet with currency context
public function generateBalanceSheet($asOfDate): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'as_of_date' => $asOfDate,
        'currency' => $currency,
        'assets' => [
            'current_assets' => $this->formatCurrency($currentAssets),
            'fixed_assets' => $this->formatCurrency($fixedAssets),
            'total_assets' => $this->formatCurrency($totalAssets)
        ],
        'liabilities' => [
            'current_liabilities' => $this->formatCurrency($currentLiabilities),
            'long_term_liabilities' => $this->formatCurrency($longTermLiabilities),
            'total_liabilities' => $this->formatCurrency($totalLiabilities)
        ],
        'equity' => $this->formatCurrency($totalEquity),
        'is_balanced' => abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01
    ];
}
```

#### 3. Payment Processing (`app/Models/Payment.php`)
**Problem Areas:**
- Payment amounts lack currency context
- Multi-currency payment processing not implemented
- Currency conversion not tracked for foreign payments

**Required Changes:**
```php
// Currency-aware payment processing
public function processPayment(float $amount, string $currency = null): array
{
    $baseCurrency = Settings::currencyLocalization('primary_currency', 'NGN');
    $paymentCurrency = $currency ?? $baseCurrency;
    
    $multiCurrencyService = new MultiCurrencyService();
    $convertedAmount = $paymentCurrency === $baseCurrency 
        ? $amount 
        : $multiCurrencyService->convert($amount, $paymentCurrency, $baseCurrency);
    
    return [
        'original_amount' => $amount,
        'original_currency' => $paymentCurrency,
        'converted_amount' => $convertedAmount,
        'base_currency' => $baseCurrency,
        'exchange_rate' => $multiCurrencyService->getExchangeRate($paymentCurrency, $baseCurrency),
        'processed_at' => now()
    ];
}

// Payment receipt formatting
public function getFormattedAmountAttribute(): string
{
    return $this->formatCurrency($this->amount, $this->currency);
}

private function formatCurrency(float $amount, string $currency): string
{
    $symbol = $this->getCurrencySymbol($currency);
    return $symbol . ' ' . number_format($amount, 2);
}
```

#### 4. Tax Calculations (`app/Services/AccountingService.php`)
**Problem Areas:**
- Tax calculations don't account for currency
- Tax reporting not multi-currency aware
- VAT calculations assume single currency

**Required Changes:**
```php
// Currency-aware tax calculations
public function calculateTax(float $amount, float $taxRate, string $currency = null): array
{
    $currency = $currency ?? Settings::currencyLocalization('primary_currency', 'NGN');
    $multiTax = Settings::currencyLocalization('multi_tax', 'disabled') === 'enabled';
    
    $taxAmount = $amount * ($taxRate / 100);
    $totalAmount = $amount + $taxAmount;
    
    return [
        'principal_amount' => $this->formatCurrency($amount, $currency),
        'tax_rate' => $taxRate,
        'tax_amount' => $this->formatCurrency($taxAmount, $currency),
        'total_amount' => $this->formatCurrency($totalAmount, $currency),
        'currency' => $currency,
        'multi_tax_enabled' => $multiTax
    ];
}

// Tax reporting with currency context
public function generateTaxReport($period): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'period' => $period,
        'currency' => $currency,
        'sales_tax_collected' => $this->formatCurrency($salesTaxCollected),
        'input_tax_paid' => $this->formatCurrency($inputTaxPaid),
        'net_tax_payable' => $this->formatCurrency($netTaxPayable),
        'tax_breakdown' => $this->getDetailedTaxBreakdown($period)
    ];
}
```

### Database Schema Updates
- `journal_entries`: Add currency column
- `payments`: Add currency and exchange_rate columns
- `tax_records`: Add currency context
- `financial_reports`: Track reporting currency

### Testing Requirements
- Test multi-currency journal entries
- Verify currency conversion in financial statements
- Test tax calculations across different currencies
- Validate payment processing with foreign currencies