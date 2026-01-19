# Float Conversion & Financial Precision Analysis

## Executive Summary
This document outlines critical issues regarding the use of floating-point arithmetic for financial calculations within the codebase. While the database schema correctly uses `DECIMAL` types, the PHP application layer frequently casts these values to `float`, introducing unexpected precision errors (e.g., `0.1 + 0.2 !== 0.3`).

## Identified Issues

### 1. MultiCurrencyService (`app/Services/MultiCurrencyService.php`)
**Severity: High**
The service relies entirely on standard float arithmetic for currency conversion.
- **Problem**: `return floatval($amount * $exchangeRate);`
- **Risk**: Multiplication of floats can result in recurring decimals that are inexact. When these are stored back into a `DECIMAL(n,2)` database column, unpredictable rounding occurs.
- **Code Reference**:
  ```php
  public function convert(float $amount, ...): float {
      // ...
      return floatval($amount * $exchangeRate);
  }
  ```

### 2. AccountingService (`app/Services/AccountingService.php`)
**Severity: Medium**
Explicit casting to float before processing.
- **Problem**: `$amount = (float) $sale->total;`
- **Risk**: Even if the source is a string (from `decimal:2` cast), forcing it to float exposes it to precision loss before it reaches the `createEntry` method.

### 3. Sale Model (`app/Models/Sale.php`)
**Severity: Medium**
Internal total calculations usage.
- **Problem**: `$this->total = $subtotalAfterDiscount + $this->tax;`
- **Risk**: If these attributes are accessed as floats, distinct values like `19.99` * 100 might result in `1998.99999999...` instead of `1999`.

## Recommendations

### Short Term (Immediate Mitigation)
1. **Use String Math (BCMath)**: If available, use `bcadd`, `bcmul` functions for all currency math.
2. **Round Explicitly**: If remaining on floats, wrap every operation in `round($value, 2)` before storage or return.
   ```php
   return round($amount * $exchangeRate, 2);
   ```

### Long Term (Best Practice)
1. **Money Pattern**: Adopt a library like `moneyphp/money`.
   - Store values as integers (cents) in the code.
   - Example: 10.00 USD is stored/manipulated as `1000`.
2. **Refactor Constants**: Ensure exchange rates are stored with high precision (e.g. 4-6 decimal places) to minimize conversion drift.
