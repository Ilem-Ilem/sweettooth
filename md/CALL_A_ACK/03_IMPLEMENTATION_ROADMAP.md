# Callback System - Implementation Roadmap

**Date:** January 8, 2026  
**Goal:** From 54% to 100% completion  
**Total Effort:** 18-20 hours  
**Timeline:** 2-3 weeks

---

## Phase Overview

```
Phase 1: Verification (Week 1)
├─ Unit Tests (4h)
├─ Feature Tests (4h)
└─ Documentation (2h)
= 10 hours → System is verifiable and documented

Phase 2: Production Ready (Week 2)
├─ Event Listeners (4h)
├─ Status Standardization (3h)
└─ Edge Case Documentation (1.5h)
= 8.5 hours → System is audit-compliant and consistent

Phase 3: Polish (Week 3)
├─ Legacy Table Cleanup (2h)
├─ Performance Optimization (2h)
└─ Integration Testing (2h)
= 6 hours → System is optimized and mature
```

---

## Phase 1: Verification (REQUIRED)

### Day 1-2: Unit Tests (4 hours)

**Objective:** Create comprehensive unit tests for all models and methods.

#### Step 1.1: Create ProductDispatchCallbackTest
**File:** `tests/Unit/Models/ProductDispatchCallbackTest.php`
**Duration:** 1.5 hours

```php
<?php

namespace Tests\Unit\Models;

use App\Models\ProductDispatchCallback;
use App\Models\ProductStock;
use App\Models\DailyProduce;
use App\Models\ProductDispatch;
use App\Models\Product;
use App\Models\User;
use App\Enums\CallbackStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class ProductDispatchCallbackTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that callbacks validate quantity against available inventory
     */
    public function test_validates_quantity_against_available_stock(): void
    {
        // Setup: Create dispatch with 100 units received
        $dispatch = ProductDispatch::factory()->create(['received_quantity' => 100]);
        $product = Product::factory()->create();

        // Create 2 existing callbacks totaling 50 units
        ProductDispatchCallback::factory()->create([
            'product_dispatch_id' => $dispatch->id,
            'product_id' => $product->id,
            'quantity' => 30,
        ]);
        ProductDispatchCallback::factory()->create([
            'product_dispatch_id' => $dispatch->id,
            'product_id' => $product->id,
            'quantity' => 20,
        ]);

        // Available: 100 - 30 - 20 = 50
        
        // Test: 50 units should be allowed
        $callback = ProductDispatchCallback::make([
            'product_dispatch_id' => $dispatch->id,
            'product_id' => $product->id,
            'quantity' => 50,
        ]);
        $this->assertTrue($callback->validateQuantity());

        // Test: 51 units should be rejected
        $callback->quantity = 51;
        $this->expectException(ValidationException::class);
        $callback->validateQuantity();
    }

    /**
     * Test that orphaned callbacks are allowed
     */
    public function test_allows_orphaned_callbacks(): void
    {
        $callback = ProductDispatchCallback::make([
            'product_dispatch_id' => null,
            'quantity' => 100,
        ]);

        // Should not throw error even though product_dispatch_id is null
        $this->assertTrue($callback->validateQuantity());
    }

    /**
     * Test that completeWithStockUpdate updates ProductStock
     */
    public function test_completeWithStockUpdate_updates_product_stock(): void
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::RECEIVED_BY_PRODUCTION,
            'quantity' => 50,
        ]);
        
        ProductStock::factory()->create([
            'product_id' => $callback->product_id,
            'callback_quantity' => 0,
        ]);

        $callback->completeWithStockUpdate();

        $stock = ProductStock::where('product_id', $callback->product_id)->first();
        $this->assertEquals(50, $stock->callback_quantity);
    }

    /**
     * Test that completeWithStockUpdate updates DailyProduce
     */
    public function test_completeWithStockUpdate_updates_daily_produce(): void
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::RECEIVED_BY_PRODUCTION,
            'quantity' => 50,
        ]);

        $dailyProduce = DailyProduce::factory()->create([
            'product_id' => $callback->product_id,
            'opening_quantity' => 100,
            'produced' => 50,
            'closing_quantity' => 150,
            'callback_quantity' => 0,
        ]);

        $callback->completeWithStockUpdate();

        $dailyProduce->refresh();
        $this->assertEquals(50, $dailyProduce->callback_quantity);
        // closing = opening + produced - callback = 100 + 50 - 50 = 100
        $this->assertEquals(100, $dailyProduce->closing_quantity);
    }

    /**
     * Test that only received callbacks can be completed
     */
    public function test_completeWithStockUpdate_requires_received_status(): void
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::APPROVED_BY_PRODUCTION,
        ]);

        $this->expectException(\RuntimeException::class);
        $callback->completeWithStockUpdate();
    }

    /**
     * Test polymorphic relationships with User
     */
    public function test_recordedBy_relationship_with_user(): void
    {
        $user = User::factory()->create();
        $callback = ProductDispatchCallback::factory()->create([
            'recordedby_id' => $user->id,
            'recordedby_type' => User::class,
        ]);

        $this->assertEquals($user->id, $callback->recordedBy->id);
        $this->assertInstanceOf(User::class, $callback->recordedBy);
    }

    /**
     * Test polymorphic relationships with Employee
     */
    public function test_recordedBy_relationship_with_employee(): void
    {
        $employee = Employee::factory()->create();
        $callback = ProductDispatchCallback::factory()->create([
            'recordedby_id' => $employee->id,
            'recordedby_type' => Employee::class,
        ]);

        $this->assertEquals($employee->id, $callback->recordedBy->id);
        $this->assertInstanceOf(Employee::class, $callback->recordedBy);
    }

    /**
     * Test status transitions
     */
    public function test_status_transitions(): void
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::PENDING,
        ]);

        // pending → approved_by_production (allowed)
        $this->assertTrue(
            CallbackStatus::PENDING->canTransitionTo(CallbackStatus::APPROVED_BY_PRODUCTION)
        );

        // approved_by_production → received_by_production (allowed)
        $this->assertTrue(
            CallbackStatus::APPROVED_BY_PRODUCTION->canTransitionTo(CallbackStatus::RECEIVED_BY_PRODUCTION)
        );

        // pending → completed (not allowed)
        $this->assertFalse(
            CallbackStatus::PENDING->canTransitionTo(CallbackStatus::COMPLETED)
        );
    }
}
```

**Checklist:**
- [ ] Create test file
- [ ] Create factory for ProductDispatchCallback if not exists
- [ ] Write 7-10 test methods
- [ ] Run tests: `php artisan test tests/Unit/Models/ProductDispatchCallbackTest.php`
- [ ] All tests passing

---

#### Step 1.2: Create ProductionCallbackTest
**File:** `tests/Unit/Models/ProductionCallbackTest.php`
**Duration:** 1.5 hours

```php
<?php

namespace Tests\Unit\Models;

use App\Models\ProductionCallback;
use App\Models\Stock;
use App\Models\DailyProduce;
use App\Models\Item;
use App\Models\Product;
use App\Models\User;
use App\Enums\CallbackStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class ProductionCallbackTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that finished product callbacks validate quantity
     */
    public function test_validates_finished_product_quantity(): void
    {
        $product = Product::factory()->create();
        $dailyProduce = DailyProduce::factory()->create([
            'product_id' => $product->id,
            'produced' => 100,
        ]);

        // Test: 100 units allowed
        $callback = ProductionCallback::make([
            'source_type' => 'finished_product',
            'product_id' => $product->id,
            'quantity' => 100,
        ]);
        $this->assertTrue($callback->validateQuantity());

        // Test: 101 units rejected
        $callback->quantity = 101;
        $this->expectException(ValidationException::class);
        $callback->validateQuantity();
    }

    /**
     * Test that raw material callbacks allow any quantity
     */
    public function test_allows_any_quantity_for_raw_materials(): void
    {
        $item = Item::factory()->create();
        
        $callback = ProductionCallback::make([
            'source_type' => 'raw_material',
            'item_id' => $item->id,
            'quantity' => 999999,
        ]);

        // Should not throw error
        $this->assertTrue($callback->validateQuantity());
    }

    /**
     * Test that approve updates raw material stock
     */
    public function test_approve_updates_raw_material_stock(): void
    {
        $item = Item::factory()->create();
        $stock = Stock::factory()->create([
            'item_id' => $item->id,
            'quantity_available' => 100,
            'quantity_damaged' => 0,
        ]);

        $callback = ProductionCallback::factory()->create([
            'source_type' => 'raw_material',
            'item_id' => $item->id,
            'quantity' => 30,
            'status' => CallbackStatus::PENDING,
        ]);

        $callback->approve(auth()->user());

        $stock->refresh();
        $this->assertEquals(70, $stock->quantity_available);
        $this->assertEquals(30, $stock->quantity_damaged);
    }

    /**
     * Test that approve updates finished product stock
     */
    public function test_approve_updates_finished_product_stock(): void
    {
        $product = Product::factory()->create();
        $dailyProduce = DailyProduce::factory()->create([
            'product_id' => $product->id,
            'callback_quantity' => 0,
        ]);

        $callback = ProductionCallback::factory()->create([
            'source_type' => 'finished_product',
            'product_id' => $product->id,
            'quantity' => 25,
            'status' => CallbackStatus::PENDING,
        ]);

        $callback->approve(auth()->user());

        $dailyProduce->refresh();
        $this->assertEquals(25, $dailyProduce->callback_quantity);
    }

    /**
     * Test that reject does not update stock
     */
    public function test_reject_prevents_stock_update(): void
    {
        $item = Item::factory()->create();
        $stock = Stock::factory()->create([
            'item_id' => $item->id,
            'quantity_available' => 100,
        ]);

        $callback = ProductionCallback::factory()->create([
            'source_type' => 'raw_material',
            'item_id' => $item->id,
            'quantity' => 30,
            'status' => CallbackStatus::PENDING,
        ]);

        $callback->reject(auth()->user(), 'Duplicate callback');

        $stock->refresh();
        $this->assertEquals(100, $stock->quantity_available); // Unchanged
    }

    /**
     * Test rejection reason is stored
     */
    public function test_rejection_reason_is_stored(): void
    {
        $callback = ProductionCallback::factory()->create();

        $callback->reject(auth()->user(), 'Item already returned');

        $this->assertEquals('Item already returned', $callback->rejection_reason);
        $this->assertEquals(CallbackStatus::REJECTED, $callback->status);
    }
}
```

**Checklist:**
- [ ] Create test file
- [ ] Create factory for ProductionCallback if not exists
- [ ] Write 6-8 test methods
- [ ] Run tests: `php artisan test tests/Unit/Models/ProductionCallbackTest.php`
- [ ] All tests passing

---

### Day 3-4: Feature Tests (4 hours)

**Objective:** Create feature tests for complete workflows.

#### Step 2.1: Feature Test for Inventory Workflow
**File:** `tests/Feature/Callbacks/ProductDispatchCallbackWorkflowTest.php`
**Duration:** 2 hours

```php
<?php

namespace Tests\Feature\Callbacks;

use App\Models\ProductDispatchCallback;
use App\Models\ProductStock;
use App\Models\DailyProduce;
use App\Models\User;
use App\Enums\CallbackStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDispatchCallbackWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test complete inventory callback workflow
     * Sales creates callback → Production approves → receives → completes
     */
    public function test_complete_inventory_callback_workflow(): void
    {
        // Setup: Create necessary data
        $user = User::factory()->create();
        $this->actingAs($user);

        // Step 1: Create callback (Sales)
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::PENDING,
            'quantity' => 50,
        ]);
        $this->assertEquals(CallbackStatus::PENDING, $callback->status);

        // Step 2: Production approves
        $callback->approve($user);
        $this->assertEquals(CallbackStatus::APPROVED_BY_PRODUCTION, $callback->status);

        // Step 3: Production receives
        $callback->markAsReceived($user);
        $this->assertEquals(CallbackStatus::RECEIVED_BY_PRODUCTION, $callback->status);

        // Step 4: Create stock records before completion
        ProductStock::factory()->create([
            'product_id' => $callback->product_id,
            'callback_quantity' => 0,
        ]);
        DailyProduce::factory()->create([
            'product_id' => $callback->product_id,
            'opening_quantity' => 100,
            'produced' => 50,
            'closing_quantity' => 150,
            'callback_quantity' => 0,
        ]);

        // Step 5: Production completes with stock update
        $callback->completeWithStockUpdate();
        $this->assertEquals(CallbackStatus::COMPLETED, $callback->status);

        // Verify stock was updated correctly
        $stock = ProductStock::where('product_id', $callback->product_id)->first();
        $this->assertEquals(50, $stock->callback_quantity);

        $produce = DailyProduce::where('product_id', $callback->product_id)->first();
        $this->assertEquals(50, $produce->callback_quantity);
        $this->assertEquals(100, $produce->closing_quantity); // 100 + 50 - 50
    }

    /**
     * Test that skipping approval stages is prevented
     */
    public function test_cannot_skip_approval_stages(): void
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::PENDING,
        ]);

        // Try to complete without receiving first
        $this->expectException(\RuntimeException::class);
        $callback->completeWithStockUpdate();
    }

    /**
     * Test that over-approved callbacks are prevented
     */
    public function test_cannot_over_approve_callback(): void
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::APPROVED_BY_PRODUCTION,
        ]);

        $user = User::factory()->create();

        // Try to approve again
        $callback->approve($user);
        // Should stay approved, not double-approve
        $this->assertEquals(CallbackStatus::APPROVED_BY_PRODUCTION, $callback->status);
    }
}
```

#### Step 2.2: Feature Test for Production Workflow
**File:** `tests/Feature/Callbacks/ProductionCallbackWorkflowTest.php`
**Duration:** 2 hours

```php
<?php

namespace Tests\Feature\Callbacks;

use App\Models\ProductionCallback;
use App\Models\Stock;
use App\Models\DailyProduce;
use App\Models\Item;
use App\Models\Product;
use App\Models\User;
use App\Enums\CallbackStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductionCallbackWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test raw material callback workflow
     */
    public function test_raw_material_callback_workflow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Setup: Create raw material with stock
        $item = Item::factory()->create();
        $stock = Stock::factory()->create([
            'item_id' => $item->id,
            'quantity_available' => 100,
        ]);

        // Step 1: Create callback
        $callback = ProductionCallback::factory()->create([
            'source_type' => 'raw_material',
            'item_id' => $item->id,
            'quantity' => 30,
            'status' => CallbackStatus::PENDING,
        ]);

        // Step 2: Inventory approves (auto stock update)
        $callback->approve($user);
        $this->assertEquals(CallbackStatus::APPROVED_BY_INVENTORY, $callback->status);

        // Verify stock updated
        $stock->refresh();
        $this->assertEquals(70, $stock->quantity_available);
        $this->assertEquals(30, $stock->quantity_damaged);
    }

    /**
     * Test finished product callback workflow
     */
    public function test_finished_product_callback_workflow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Setup: Create finished product with production data
        $product = Product::factory()->create();
        $dailyProduce = DailyProduce::factory()->create([
            'product_id' => $product->id,
            'produced' => 100,
            'callback_quantity' => 0,
        ]);

        // Step 1: Create callback
        $callback = ProductionCallback::factory()->create([
            'source_type' => 'finished_product',
            'product_id' => $product->id,
            'quantity' => 25,
            'status' => CallbackStatus::PENDING,
        ]);

        // Step 2: Inventory approves (auto stock update)
        $callback->approve($user);
        $this->assertEquals(CallbackStatus::APPROVED_BY_INVENTORY, $callback->status);

        // Verify stock updated
        $dailyProduce->refresh();
        $this->assertEquals(25, $dailyProduce->callback_quantity);
    }

    /**
     * Test rejection workflow
     */
    public function test_rejection_workflow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $callback = ProductionCallback::factory()->create([
            'status' => CallbackStatus::PENDING,
        ]);

        // Reject callback
        $callback->reject($user, 'Duplicate entry');

        $this->assertEquals(CallbackStatus::REJECTED, $callback->status);
        $this->assertEquals('Duplicate entry', $callback->rejection_reason);
    }
}
```

**Checklist:**
- [ ] Create both test files
- [ ] Write 5-6 test methods per file
- [ ] Run tests: `php artisan test tests/Feature/Callbacks/`
- [ ] All tests passing

---

### Day 5: Documentation Update (2 hours)

**Objective:** Update all documentation with implementation completion.

#### Step 3.1: Update Progress Files
**Files to Update:**
- `mv/CALLBACK_PROGRESS_TRACKING.txt` - Mark 4.1, 4.2 as DONE
- `mv/CALLBACK_IMPLEMENTATION_COMPLETE.txt` - Add test file information

**Content:**
```
[DONE] 4.1 - Create Unit Tests
   Status: DONE
   Files Created:
   - tests/Unit/Models/ProductDispatchCallbackTest.php (250 lines, 10 tests)
   - tests/Unit/Models/ProductionCallbackTest.php (200 lines, 8 tests)
   Total Coverage: ~95% of model methods
   
[DONE] 4.2 - Create Feature Tests
   Status: DONE
   Files Created:
   - tests/Feature/Callbacks/ProductDispatchCallbackWorkflowTest.php (150 lines, 3 tests)
   - tests/Feature/Callbacks/ProductionCallbackWorkflowTest.php (180 lines, 3 tests)
   Workflows Tested: 6 complete workflows
```

#### Step 3.2: Create API Reference
**File:** `md/CALL_A_ACK/API_REFERENCE.md`
**Content:**
```markdown
# Callback System API Reference

## ProductDispatchCallback

### Methods

#### approve(actor: User|Employee)
Approves a pending inventory callback.

```php
$callback = ProductDispatchCallback::find(1);
$callback->approve(auth()->user());
// Status: pending → approved_by_production
```

#### markAsReceived(actor: User|Employee)
Marks callback as received by production.

```php
$callback->markAsReceived(auth()->user());
// Status: approved_by_production → received_by_production
```

#### completeWithStockUpdate()
Completes callback and updates stock.

```php
$callback->completeWithStockUpdate();
// Updates: ProductStock.callback_quantity
// Updates: DailyProduce.callback_quantity
// Status: received_by_production → completed
```

### Properties

| Property | Type | Description |
|----------|------|-------------|
| `status` | CallbackStatus | Current status |
| `quantity` | int | Callback quantity |
| `recordedBy` | User\|Employee | Who created it |
| `approvedBy` | User\|Employee | Who approved it |
| `receivedBy` | User\|Employee | Who received it |

## ProductionCallback

### Methods

#### approve(actor: User|Employee)
Approves callback and updates stock automatically.

```php
$callback = ProductionCallback::find(1);
$callback->approve(auth()->user());
// Status: pending → approved_by_inventory
// Stock: updated automatically based on source_type
```

#### reject(actor: User|Employee, reason: string)
Rejects callback and stores rejection reason.

```php
$callback->reject(auth()->user(), 'Duplicate item');
// Status: pending → rejected
```

#### complete()
Completes the callback.

```php
$callback->complete();
// Status: approved_by_inventory → completed
```
```

**Checklist:**
- [ ] Update progress tracking files
- [ ] Create API reference
- [ ] Create troubleshooting guide
- [ ] Create deployment checklist

---

## Phase 2: Production Ready (OPTIONAL BUT RECOMMENDED)

### Day 6-7: Event Listeners (4 hours)

See `02_IMPROVEMENTS_AND_SOLUTIONS.md` for complete event listener implementation.

**Checklist:**
- [ ] Create events (3 files)
- [ ] Create listener (1 file)
- [ ] Create audit model (1 file)
- [ ] Create migration (1 file)
- [ ] Register in models (2 files modified)
- [ ] Test event dispatching

### Day 8-9: Status Standardization (3 hours)

See `02_IMPROVEMENTS_AND_SOLUTIONS.md` for status standardization.

**Checklist:**
- [ ] Create migration file
- [ ] Update enum (if needed)
- [ ] Update all queries
- [ ] Update views
- [ ] Test all workflows
- [ ] Run migrations

---

## Phase 3: Polish (OPTIONAL)

### Legacy Table Review (2 hours)

**Step 1:** Check for data
```bash
php artisan tinker
>>> DB::table('product_callbacks')->count()
```

**Step 2:** Search for references
```bash
grep -r "ProductCallback" app/ --include="*.php"
```

**Step 3:** Decide
- If no data & no references: Create migration to drop table
- If data exists: Document why it's kept

**Step 4:** Document decision
```markdown
# Legacy ProductCallback Table

## Status: DEPRECATED

This table is no longer used. All callbacks are now handled by:
- ProductDispatchCallback (inventory returns)
- ProductionCallback (production damage)

## Data: [X records]
## Decision: [Keep / Drop]
## Action: [Migration created / Documentation added]
```

---

## Testing Checklist (Before Deploy)

```
✅ All 18 unit tests pass
✅ All 6 feature tests pass
✅ Stock updates verified
✅ Validation prevents invalid quantities
✅ Status transitions enforced
✅ Polymorphic relationships work
✅ Orphaned callbacks work
✅ Enum status casting works
✅ Database indexes created
✅ No N+1 queries
✅ No data integrity issues
✅ Performance improved
✅ Event listeners dispatched (if implemented)
✅ Documentation complete
```

---

## Deployment Steps

```bash
# 1. Run migrations
php artisan migrate

# 2. Run tests
php artisan test

# 3. Clear cache
php artisan cache:clear

# 4. Monitor logs
tail -f storage/logs/laravel.log

# 5. Verify in production
# - Create test callback
# - Approve and receive
# - Complete (verify stock updates)
# - Check audit trail
```

---

## Success Metrics

| Metric | Before | After | Target |
|--------|--------|-------|--------|
| Test Coverage | 0% | ~95% | >90% |
| Audit Trail | None | Complete | ✅ |
| Status Consistency | No | Yes | ✅ |
| Documentation | Incomplete | Complete | ✅ |
| Completion Rate | 54% | 100% | ✅ |

---

## Timeline Summary

| Phase | Duration | Status |
|-------|----------|--------|
| Phase 1: Verification | 10 hours | Ready |
| Phase 2: Production Ready | 8.5 hours | Optional |
| Phase 3: Polish | 6 hours | Optional |
| **Total** | **~25 hours** | **54% → 100%** |

---

## Next Steps

1. **Start Phase 1** - Create unit and feature tests
2. **Run tests** - Verify system works
3. **Update docs** - Document completion
4. **Deploy to staging** - Test in realistic environment
5. **Consider Phase 2** - Add event listeners for audit trail
6. **Deploy to production** - Roll out to users

---

## Support & Questions

For implementation questions:
1. Check API Reference (`03_API_REFERENCE.md`)
2. Review existing code examples
3. Consult troubleshooting guide
4. Check test files for usage examples

