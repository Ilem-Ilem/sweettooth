# Inventory Module - Audit Testing Guide

**Date:** December 3, 2025  
**Status:** All 13 audit logging points implemented and ready for testing

---

## Quick Test Checklist

Use this guide to verify all audit logging implementations are working correctly.

### 1. Items Management (3 Tests)

#### Test 1.1: Item Creation
1. Go to Inventory → Items
2. Click "Create Item"
3. Fill in: Name, Category, UOM, Reorder Level, Max Stock
4. Click Save (if super admin) or Submit for Approval
5. **Verify:** Check audit_logs table
   ```bash
   php artisan tinker
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Item')
        ->where('action_type', 'create')
        ->latest()
        ->first();
   ```
6. **Expected:** Log with item name, SKU, category, UOM in description

#### Test 1.2: Item Update
1. Go to Inventory → Items
2. Click Edit on any item
3. Change name, category, UOM, or reorder level
4. Save changes
5. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Item')
        ->where('action_type', 'update')
        ->latest()
        ->first();
   ```
6. **Expected:** Log showing what changed (e.g., "Name: Old → New")

#### Test 1.3: Item Deletion
1. Go to Inventory → Items
2. Click Delete on any item
3. Confirm deletion
4. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Item')
        ->where('action_type', 'delete')
        ->latest()
        ->first();
   ```
6. **Expected:** Log with deleted item name and SKU

---

### 2. Purchases (2 Tests)

#### Test 2.1: Purchase Creation
1. Go to Inventory → Purchases
2. Click "Create Purchase"
3. Fill in: Supplier, Date, Add items with quantities and costs
4. Click Save
5. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Purchase')
        ->where('action_type', 'create')
        ->latest()
        ->first();
   ```
6. **Expected:** Log with purchase number, supplier, FOB costs, landing cost, item count

#### Test 2.2: Purchase Deletion
1. Go to Inventory → Purchases
2. Find a purchase and click Delete
3. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Purchase')
        ->where('action_type', 'delete')
        ->latest()
        ->first();
   ```
6. **Expected:** Log with purchase number, supplier, item count, landing cost

---

### 3. Stocks (2 Tests)

#### Test 3.1: Stock Adjustment Request (Non-Admin)
1. Go to Inventory → Stocks
2. Click Edit on any stock
3. Change quantities (available, reserved, damaged)
4. Click Save
5. Enter reason for adjustment in audit modal
6. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Stock')
        ->where('action_type', 'update')
        ->where('status', 'pending')
        ->latest()
        ->first();
   ```
7. **Expected:** Log with status='pending', item name, quantities, and reason

#### Test 3.2: Stock Direct Update (Super Admin)
1. Log in as super admin
2. Go to Inventory → Stocks
3. Click Edit on any stock
4. Change quantities
5. Click Save (no audit modal for admins)
6. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\Stock')
        ->where('action_type', 'update')
        ->where('status', 'completed')
        ->latest()
        ->first();
   ```
7. **Expected:** Log with status='completed', changes listed

---

### 4. Item Requests (1 Test)

#### Test 4.1: Item Request Creation
1. Go to Inventory → Item Requests
2. Click "Create Request"
3. Select Department
4. Add Items with quantities
5. Click Save
6. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ItemRequest')
        ->where('action_type', 'create')
        ->latest()
        ->first();
   ```
7. **Expected:** Log with request number, department name, item count, request date

---

### 5. Item Dispatches (2 Tests)

#### Test 5.1: Item Approval
1. Go to Inventory → Item Dispatches
2. Click on a pending request in the accordion
3. Enter approve quantities for items
4. Click "Approve Items"
5. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ItemRequest')
        ->where('action_type', 'update')
        ->orderBy('created_at', 'desc')
        ->first();
   ```
6. **Expected:** Log with "Approved X item(s)" and item names with quantities

#### Test 5.2: Item Dispatch
1. From the same request in dispatch modal
2. Confirm items are approved
3. Click "Dispatch Items"
4. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ItemRequest')
        ->where('action_type', 'update')
        ->orderBy('created_at', 'desc')
        ->limit(1)
        ->first();
   ```
6. **Expected:** Log with "Dispatched items from request" and status updates

---

### 6. Stock Takes (2 Tests)

#### Test 6.1: Stock Take Creation
1. Go to Inventory → Stock Takes
2. Click "Create Stock Take"
3. Select type (full/partial/cycle)
4. Enter physical quantities for items
5. Click Save
6. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\StockTake')
        ->where('action_type', 'create')
        ->latest()
        ->first();
   ```
7. **Expected:** Log with stock take number, type, items counted, variance details

#### Test 6.2: Stock Take Completion
1. Go to Inventory → Stock Takes
2. Find a stock take with status "in_progress"
3. Click "Complete"
4. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\StockTake')
        ->where('action_type', 'update')
        ->latest()
        ->first();
   ```
7. **Expected:** Log with "Completed stock take" and matched/surplus/shortage counts

---

### 7. Health Checks (1 Test)

#### Test 7.1: Health Check Creation
1. Go to Inventory → Health Checks
2. Click "Create Health Check"
3. Select Stock
4. Fill in: Date, Condition, Quantity Affected, Observations, Action
5. Click Save
6. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\HealthCheck')
        ->where('action_type', 'create')
        ->latest()
        ->first();
   ```
7. **Expected:** Log with item name, condition, quantity affected, observations

---

### 8. Approval Request Tracking (4 Tests)

#### Test 8.1: Stock Adjustment Approval Request
1. Go to Inventory → Stocks
2. Click Edit on any stock
3. Change quantities (non-admin user)
4. Click Save and enter reason for audit
5. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ApprovalAuditRequest')
        ->where('action_type', 'create')
        ->where('status', 'pending')
        ->latest()
        ->first();
   ```
6. **Expected:** Log showing "Requested stock adjustment approval"

#### Test 8.2: Item Creation Approval Request
1. Go to Inventory → Items
2. Click Create (non-admin user)
3. Enter item details
4. Click Save and enter approval reason
5. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ApprovalAuditRequest')
        ->where('action_type', 'create')
        ->orderBy('created_at', 'desc')
        ->first();
   ```
6. **Expected:** Log showing "Requested item creation approval"

#### Test 8.3: Item Update Approval Request
1. Go to Inventory → Items
2. Click Edit (non-admin user)
3. Modify item details
4. Click Save and enter approval reason
5. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ApprovalAuditRequest')
        ->where('action_type', 'create')
        ->orderBy('created_at', 'desc')
        ->first();
   ```
6. **Expected:** Log showing "Requested item update approval"

#### Test 8.4: Item Deletion Approval Request
1. Go to Inventory → Items
2. Click Delete (non-admin user)
3. Enter approval reason
4. **Verify:**
   ```bash
   >>> DB::table('audit_logs')
        ->where('auditable_type', 'App\Models\ApprovalAuditRequest')
        ->where('action_type', 'create')
        ->orderBy('created_at', 'desc')
        ->first();
   ```
6. **Expected:** Log showing "Requested item deletion approval"

---

## Bulk Verification Query

Run this to see all inventory audits created:

```bash
php artisan tinker

# Count all inventory audits
>>> DB::table('audit_logs')
     ->whereIn('auditable_type', [
       'App\Models\Item',
       'App\Models\Purchase',
       'App\Models\Stock',
       'App\Models\ItemRequest',
       'App\Models\ItemDispatch',
       'App\Models\StockTake',
       'App\Models\HealthCheck'
     ])
     ->count();

# View all with pagination
>>> DB::table('audit_logs')
     ->whereIn('auditable_type', [
       'App\Models\Item',
       'App\Models\Purchase',
       'App\Models\Stock',
       'App\Models\ItemRequest',
       'App\Models\ItemDispatch',
       'App\Models\StockTake',
       'App\Models\HealthCheck'
     ])
     ->orderBy('created_at', 'desc')
     ->paginate(20);

# View specific component audits
>>> DB::table('audit_logs')
     ->where('auditable_type', 'App\Models\Item')
     ->orderBy('created_at', 'desc')
     ->paginate(10);
```

---

## Expected Results After All Tests

```
Items:                3 creates + 1 update + 1 delete = 5 logs
Purchases:            1 create + 1 delete = 2 logs
Stocks:               1 pending + 1 completed = 2 logs
Item Requests:        1 create = 1 log
Item Dispatches:      1 approval + 1 dispatch = 2 logs
Stock Takes:          1 create + 1 update = 2 logs
Health Checks:        1 create = 1 log
Approval Requests:    4 (stock adj + item create + item update + item delete) = 4 logs

TOTAL:                ~21 audit logs
```

---

## Troubleshooting

### Issue: No audit logs appearing

**Check 1:** Verify AuditService exists
```bash
ls -la app/Services/AuditService.php
```

**Check 2:** Verify current_actor() helper works
```bash
php artisan tinker
>>> current_actor()
```

**Check 3:** Check audit_logs table exists
```bash
php artisan tinker
>>> DB::table('audit_logs')->first();
```

**Check 4:** Check Laravel logs for errors
```bash
tail -f storage/logs/laravel.log
```

### Issue: Wrong actor in logs

**Check:** Verify current_actor() returns correct user
```bash
php artisan tinker
>>> current_actor()
>>> Auth::guard('employees')->user()
```

### Issue: Model class not found

**Check:** Verify morphClass is set in models
```bash
# Check Item model
cat app/Models/Item.php | grep morphClass

# Should see: protected $morphClass = 'App\Models\Item';
```

---

## Performance Check

After testing, verify no performance degradation:

```bash
# Monitor database queries
>>> DB::enableQueryLog();
>>> // Run an operation
>>> print_r(DB::getQueryLog());

# Check for N+1 queries
# Should see only 1-2 extra queries for audit logging
```

---

## Success Criteria

- ✅ All 13 audit points log correctly
- ✅ Logs show correct actor
- ✅ Descriptions are clear and detailed
- ✅ Action types match operation (create/update/delete)
- ✅ Status reflects workflow state
- ✅ No errors in logs
- ✅ No performance degradation
- ✅ All timestamps are accurate

---

## Next Steps After Verification

1. ✅ **Test** - Follow checklist above
2. ✅ **Verify** - Run bulk verification queries
3. **Document** - Update team on audit capabilities
4. **Deploy** - Push to production with confidence
5. **Monitor** - Watch for any issues
6. **Archive** - Keep these test results for reference

---

**Document Created:** December 3, 2025  
**Reference:** AUDIT_IMPLEMENTATION_COMPLETED.md  
**Status:** Ready for Testing
