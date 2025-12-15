# Phase 2 Deployment Checklist

## Pre-Deployment (Before Running Migrations)

- [ ] **Backup Database**
  ```bash
  php artisan backup:run
  # or manually
  mysqldump -u root -p sweettooth > backup_$(date +%Y%m%d).sql
  ```

- [ ] **Review Changes**
  ```bash
  git diff HEAD~1 HEAD
  # Should show:
  # - 4 new migration files
  # - 4 new observer files
  # - 5 modified model files
  # - Modified AppServiceProvider.php
  ```

- [ ] **Verify Laravel Version**
  ```bash
  php artisan --version
  # Should be Laravel 10.x or higher
  ```

- [ ] **Check Database Connection**
  ```bash
  php artisan tinker
  > DB::connection()->getPDO();
  # Should return PDO connection
  ```

## Deployment (Running Migrations)

- [ ] **Run Migrations**
  ```bash
  php artisan migrate
  # Should show all 4 new migrations executed
  ```

- [ ] **Verify Migrations**
  ```bash
  php artisan migrate:status
  # All should show "Yes" in Batch column
  ```

- [ ] **Check Database Schema**
  ```bash
  php artisan tinker
  > Schema::hasColumn('sales', 'gl_posting_status')
  > Schema::hasColumn('purchases', 'gl_posting_status')
  > Schema::hasColumn('payments', 'gl_posting_status')
  > Schema::hasColumn('stock_movements', 'gl_posting_status')
  # All should return true
  ```

## Post-Deployment (After Migrations)

- [ ] **Clear Cache**
  ```bash
  php artisan cache:clear
  php artisan config:cache
  php artisan route:cache
  ```

- [ ] **Verify Observer Registration**
  ```bash
  php artisan tinker
  > $sale = Sale::first();
  > # No errors should occur
  ```

- [ ] **Check GL Accounts Exist**
  ```bash
  php artisan tinker
  > GlAccount::count()
  # Should be 50+
  > GlAccount::where('account_number', '4010')->first()
  # Should return Sales Revenue account
  ```

- [ ] **Check Accounting Period is Open**
  ```bash
  php artisan tinker
  > AccountingPeriod::current()->first()
  # Should return current month's period
  # Check status = 'open'
  ```

## Testing (Manual QA)

### Test 1: Sale Observer
```bash
php artisan tinker

# Create test data
$shift = SalesShift::first();
$branch = $shift->branch;
$dept = Department::first();
$emp = Employee::first();

# Create sale
$sale = Sale::create([
    'sales_shift_id' => $shift->id,
    'branch_id' => $branch->id,
    'department_id' => $dept->id,
    'sold_by_type' => Employee::class,
    'sold_by_id' => $emp->id,
    'sale_number' => 'TEST-SALE-001',
    'sale_time' => now(),
    'subtotal' => 1000,
    'tax' => 100,
    'discount' => 0,
    'total' => 1100,
    'status' => 'pending',
]);

# Check status
echo $sale->gl_posting_status; # Should be 'pending'

# Create payment to trigger observer
Payment::create([
    'sale_id' => $sale->id,
    'payment_method' => 'cash',
    'amount' => 1100,
    'status' => 'pending',
]);

# Mark as completed
$sale->update(['status' => 'completed']);
$sale->refresh();

echo $sale->gl_posting_status; # Should be 'posted' or 'failed'

# If failed, check error
echo $sale->gl_posting_error;

# Verify GL entries
GlEntry::where('reference_type', 'App\Models\Sale')
    ->where('reference_id', $sale->id)
    ->count(); # Should be 2+
```

**Expected Result:** ✓ GL entries created, status = 'posted'

### Test 2: Purchase Observer
```bash
php artisan tinker

$emp = Employee::first();
$branch = Branch::first();

$purchase = Purchase::create([
    'branch_id' => $branch->id,
    'recorded_by_type' => Employee::class,
    'recorded_by_id' => $emp->id,
    'purchase_number' => 'TEST-PUR-001',
    'purchase_date' => now()->date(),
    'supplier_name' => 'Test Supplier',
    'total_fob_ngn' => 5000,
    'other_costs' => 500,
    'landing_cost' => 5500,
    'status' => 'draft',
]);

echo $purchase->gl_posting_status; # Should be 'pending'

$purchase->update(['status' => 'approved']);
$purchase->refresh();

echo $purchase->gl_posting_status; # Should be 'posted' or 'failed'

# Verify GL entries
GlEntry::where('reference_type', 'App\Models\Purchase')
    ->where('reference_id', $purchase->id)
    ->count(); # Should be 2
```

**Expected Result:** ✓ GL entries created, status = 'posted'

### Test 3: Payment Observer
```bash
php artisan tinker

# Use existing sale
$sale = Sale::where('gl_posting_status', 'posted')->first();
if (!$sale) {
    # Create new sale (see Test 1)
}

$payment = Payment::create([
    'sale_id' => $sale->id,
    'payment_method' => 'bank_transfer',
    'amount' => 500,
    'status' => 'pending',
]);

echo $payment->gl_posting_status; # Should be 'pending'

$payment->update(['status' => 'completed']);
$payment->refresh();

echo $payment->gl_posting_status; # Should be 'posted' or 'failed'
```

**Expected Result:** ✓ GL entries created, status = 'posted'

### Test 4: StockMovement Observer
```bash
php artisan tinker

$stock = Stock::first();
$emp = Employee::first();

# Test damage movement
$movement = StockMovement::create([
    'stock_id' => $stock->id,
    'type' => 'damage',
    'quantity' => 5,
    'quantity_before' => 100,
    'quantity_after' => 95,
    'moved_by_type' => Employee::class,
    'moved_by_id' => $emp->id,
    'movement_date' => now(),
]);

echo $movement->gl_posting_status; # Should be 'posted' or 'failed'

# Verify GL entries
GlEntry::where('reference_type', 'App\Models\StockMovement')
    ->where('reference_id', $movement->id)
    ->count(); # Should be 2
```

**Expected Result:** ✓ GL entries created, status = 'posted'

### Test 5: GL Balancing
```bash
php artisan tinker

# Calculate trial balance
$debits = GlEntry::where('status', 'posted')->sum('debit');
$credits = GlEntry::where('status', 'posted')->sum('credit');

echo "Debits: " . $debits;
echo "Credits: " . $credits;
echo "Difference: " . ($debits - $credits);

# Should be 0 (or very small due to rounding)
```

**Expected Result:** ✓ Difference = 0

## Rollback Plan (If Issues Found)

### If Observers Not Working
1. Check AppServiceProvider.php has observer registrations
2. Restart Laravel (clear cache)
3. Verify observer files exist in app/Observers/

### If GL Posting Failing
1. Check accounting period is open
2. Check GL accounts exist (GlAccount::count() should be 50+)
3. Check logs: `tail storage/logs/laravel.log`

### If Need to Rollback
```bash
# Rollback last migration batch
php artisan migrate:rollback

# Then verify tables are back to original state
php artisan migrate:status
```

## Post-Testing Validation

- [ ] **Check Error Logs**
  ```bash
  tail -n 100 storage/logs/laravel.log
  # Should see "Sale posted to GL" messages
  # No "GL Posting Error" messages
  ```

- [ ] **Verify Data Integrity**
  ```bash
  php artisan tinker
  
  # All GL entries should be in 'draft' or 'posted' status
  GlEntry::where('status', '!=', 'draft')
          ->where('status', '!=', 'posted')
          ->count()  # Should be 0
  
  # All transactions should have gl_posting_status set
  Sale::whereNull('gl_posting_status')->count()  # Should be 0
  ```

- [ ] **Verify No Duplicate Entries**
  ```bash
  php artisan tinker
  
  # Each transaction should have unique GL entries
  $sales = Sale::all();
  foreach ($sales as $sale) {
      $count = GlEntry::where('reference_type', Sale::class)
                      ->where('reference_id', $sale->id)
                      ->count();
      if ($count > 0 && $count % 2 != 0) {
          echo "Warning: Sale {$sale->id} has odd number of GL entries: {$count}";
      }
  }
  ```

## Sign-Off

- [ ] **Developer Review:** Code looks good, tests pass
- [ ] **QA Testing:** Manual tests all passed
- [ ] **Database Integrity:** No errors, GL balanced
- [ ] **Performance:** No slowdowns observed
- [ ] **Logging:** Error logs clean, posting logs present

## Deployment Completed

**Date:** _______________
**Deployed By:** _______________
**Environment:** ☐ Dev ☐ Staging ☐ Production
**Notes:** _______________

---

**If any test fails:** Review ACCOUNTING_PHASE2_IMPLEMENTATION.md or ACCOUNTING_PHASE2_QUICK_START.md for troubleshooting.
