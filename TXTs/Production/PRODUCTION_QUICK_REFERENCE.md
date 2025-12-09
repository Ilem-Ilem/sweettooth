# Production Module - Quick Reference Guide

## 📊 Status Dashboard

| Component | Status | Details |
|-----------|--------|---------|
| **Actor Pattern** | ✅ | Uses `current_actor()` everywhere |
| **Stock Logic** | ✅ | In models, not components |
| **Navigation** | ✅ | All callback routes included |
| **Documentation** | ✅ | Full PHPDoc coverage |
| **Polymorphic** | ✅ | Employee & User supported |
| **Transactions** | ✅ | All multi-step ops protected |

---

## 🚀 Most Important Methods

### ProductDispatchCallback (Product Returns)
```php
// Approve a return from sales
$callback->approve(current_actor());

// Mark as received
$callback->markAsReceived(current_actor());

// Complete with stock updates (THE KEY METHOD)
$callback->completeWithStockUpdate();

// One-liners
$callback->approveAndReceive(current_actor());
$callback->approveReceiveAndComplete(current_actor());
```

### ProductionCallback (Damaged Items)
```php
// Approve with automatic stock update
$callback->approve(current_actor());

// Reject with reason
$callback->reject(current_actor(), 'Item is actually fine');

// Complete
$callback->complete();
```

---

## 🔄 Workflow States

### ProductDispatchCallback (Sales→Production)
```
pending
    ↓ approve()
approved_by_production
    ↓ markAsReceived()
received_by_production
    ↓ completeWithStockUpdate()
completed ✅
```

### ProductionCallback (Production→Inventory)
```
pending
    ↓ approve() or reject()
approved_by_inventory / rejected
    ↓ complete() (if approved)
completed ✅
```

---

## 🔐 Polymorphic Tracking

```php
// Get actor (Employee or User)
$actor = current_actor();

// Store it
[
    'recorded_by_id' => $actor->id,
    'recorded_by_type' => get_class($actor),
]

// Retrieve it
$callback->recordedBy  // Returns Employee|User object
$callback->approvedBy  // Returns Employee|User object
$callback->receivedBy  // Returns Employee|User object
```

---

## 📁 File Structure

```
app/Models/
├── ProductionCallback.php      (Production→Inventory)
└── ProductDispatchCallback.php (Sales→Production)

app/Livewire/BranchDashboard/Production/Callbacks/
├── ApproveCallbacks.php        (Approve returns)
├── CreateInventoryCallback.php (Create callbacks)
└── Index.php                   (List callbacks)

resources/views/livewire/branch-dashboard/production/callbacks/
├── approve-callbacks.blade.php
├── create-inventory-callback.blade.php
└── index.blade.php
```

---

## 🧪 Testing Snippets

```bash
# In tinker
$callback = ProductionCallback::first();
$callback->recordedBy               # Should be Employee|User
get_class($callback->recordedBy)    # Should be full class name

$callback->approve(current_actor())
$callback->fresh()->status          # Should be 'approved_by_inventory'

# Stock updated?
ProductStock::where('product_id', 1)->first()->callback_quantity
DailyProduce::where('id', 1)->first()->callback_quantity
```

---

## ✅ Checklist for New Features

When adding new callback features:

- [ ] Use `current_actor()` for actor tracking
- [ ] Store both `id` and `type` for polymorphic
- [ ] Put business logic in models, not components
- [ ] Use transactions for multi-step operations
- [ ] Add comprehensive PHPDoc
- [ ] Test with both Employee and User
- [ ] Use `eager_loading()` for relationships
- [ ] Verify API compatibility
- [ ] Check database query performance

---

## 🚨 Common Mistakes to Avoid

❌ **Wrong:**
```php
$callback->approve($employeeId);  // Only ID
where('status', 'approved')       // Hard-coded status
$stock->update(...);              // Logic in component
```

✅ **Right:**
```php
$callback->approve(current_actor());  // Full actor
where('status', CallbackStatus::ApprovedByInventory)
$callback->approve($actor);  // Model handles stock
```

---

## 🎯 Key Locations

| What | Where |
|------|-------|
| Navigation logic | `resources/views/components/layouts/app/branch-dashboard.blade.php` (lines 191-280) |
| Callback routes | `routes/branch-route.php` (lines 71-133) |
| Page seeding | `app/Observers/DepartmentObserver.php` (lines 150-187) |
| Status enums | `app/Enums/CallbackStatus.php` |
| Stock update | `app/Models/ProductDispatchCallback.php` (lines 323-337) |
| Actor pattern | `app/Models/ProductionCallback.php` (line 198-202) |

---

## 📊 Status Filter Options

```php
// ProductionCallback (Index.php)
'pending' => 'Pending'
'approved_by_inventory' => 'Approved by Inventory'
'rejected' => 'Rejected'
'completed' => 'Completed'

// ProductDispatchCallback (ApproveCallbacks.php)
'pending' => 'Pending Approval'
'approved_by_production' => 'Approved (Awaiting Receipt)'
'received_by_production' => 'Received (Awaiting Completion)'
'completed' => 'Completed'
```

---

## 🎓 Learning Path

1. **Understand**: `REVISED_CRITICAL_ISSUES.txt` (5 min)
2. **Review**: `PRODUCTION_IMPLEMENTATION_VERIFICATION.md` (10 min)
3. **Code**: Model methods in `ProductionCallback.php` (15 min)
4. **Implement**: Follow patterns in this guide (ongoing)

---

## 🔍 Verification Commands

```bash
# Verify PHPDoc
php artisan tinker
>>> $r = new ReflectionClass(ProductionCallback::class);
>>> echo $r->getDocComment();

# Verify migrations
php artisan migrate:status

# Verify database
SELECT * FROM production_callbacks LIMIT 1;
SELECT * FROM product_dispatch_callbacks LIMIT 1;

# Verify scopes
ProductionCallback::pending()->count()
ProductionCallback::approved()->count()
```

---

## 📞 Need Help?

| Question | Answer |
|----------|--------|
| How do polymorphic relationships work? | See `app/Models/ProductionCallback.php` class docs |
| How is stock updated? | See `completeWithStockUpdate()` method in model |
| How to create a callback? | See `CreateInventoryCallback` component |
| How to approve a callback? | See `ApproveCallbacks` component + model `approve()` |
| What's current_actor()? | Returns logged-in Employee or User |
| Where's the navigation code? | `branch-dashboard.blade.php` lines 191-280 |

---

## 🎯 Daily Development

When working with callbacks:

```php
// Always use this
$actor = current_actor();

// Always store both
'recorded_by_id' => $actor->id,
'recorded_by_type' => get_class($actor),

// Always verify relationships
with(['recordedBy', 'approvedBy', 'shift'])

// Always use transactions
DB::transaction(function () {
    // multi-step operation
});

// Always add PHPDoc
/**
 * What does this do?
 * @param Type $param
 * @return Type
 */
```

---

## 📈 Performance Tips

- ✅ Use `lockForUpdate()` for stock updates
- ✅ Eager load relationships with `with()`
- ✅ Index `status` and `shift_id` columns
- ✅ Limit queries to last 30 days
- ✅ Paginate large result sets
- ✅ Use scopes for common filters
- ✅ Monitor N+1 queries in development

---

## 🚀 Ready to Deploy

The Production module is:
- ✅ Fully implemented
- ✅ Completely documented
- ✅ API compatible
- ✅ Polymorphic-ready
- ✅ Transaction-safe
- ✅ Performance-optimized

**Status: PRODUCTION READY**

---

**Last Updated:** December 8, 2025  
**Maintained By:** Development Team  
**Version:** 1.0
