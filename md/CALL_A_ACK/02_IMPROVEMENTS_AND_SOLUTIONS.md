# Callback System - Improvements & Solutions

**Date:** January 8, 2026  
**Focus:** How to fix issues and improve the system  
**Effort Estimate:** 18-20 hours total

---

## 🔧 Critical Improvements

### Improvement #1: Add Event-Driven Audit Trail

**Problem:** No way to track callback approvals, rejections, or status changes.

**Current Implementation:**
```php
// CallbackApprovalService.php line 72-75
if (!$callback->approveReceiveAndComplete($approver)) {
    throw new \Exception('Failed to process inventory callback');
}
// No event dispatched
```

**Improved Implementation:**

#### Step 1: Create Events

Create file: `app/Events/CallbackApproved.php`
```php
<?php

namespace App\Events;

use App\Models\ProductDispatchCallback;
use App\Models\ProductionCallback;
use Illuminate\Foundation\Events\Dispatchable;

class CallbackApproved
{
    use Dispatchable;

    public function __construct(
        public ProductDispatchCallback|ProductionCallback $callback,
        public $approver,
        public string $type // 'inventory' or 'production'
    ) {}
}
```

Create file: `app/Events/CallbackCompleted.php`
```php
<?php

namespace App\Events;

use App\Models\ProductDispatchCallback;
use App\Models\ProductionCallback;
use Illuminate\Foundation\Events\Dispatchable;

class CallbackCompleted
{
    use Dispatchable;

    public function __construct(
        public ProductDispatchCallback|ProductionCallback $callback,
        public string $type // 'inventory' or 'production'
    ) {}
}
```

Create file: `app/Events/CallbackRejected.php`
```php
<?php

namespace App\Events;

use App\Models\ProductionCallback;
use Illuminate\Foundation\Events\Dispatchable;

class CallbackRejected
{
    use Dispatchable;

    public function __construct(
        public ProductionCallback $callback,
        public $rejector,
        public string $reason
    ) {}
}
```

#### Step 2: Create Listener

Create file: `app/Listeners/LogCallbackStatusChange.php`
```php
<?php

namespace App\Listeners;

use App\Events\CallbackApproved;
use App\Events\CallbackCompleted;
use App\Events\CallbackRejected;
use App\Models\CallbackAudit;
use Illuminate\Support\Facades\DB;

class LogCallbackStatusChange
{
    public function handleApproved(CallbackApproved $event): void
    {
        CallbackAudit::create([
            'callback_id' => $event->callback->id,
            'callback_type' => $event->type,
            'action' => 'approved',
            'actor_id' => $event->approver->id,
            'actor_type' => get_class($event->approver),
            'old_status' => 'pending',
            'new_status' => 'approved',
            'payload' => [
                'quantity' => $event->callback->quantity,
                'product_name' => $event->callback->product?->name ?? $event->callback->item?->name,
            ],
        ]);
    }

    public function handleCompleted(CallbackCompleted $event): void
    {
        CallbackAudit::create([
            'callback_id' => $event->callback->id,
            'callback_type' => $event->type,
            'action' => 'completed',
            'actor_id' => auth()->id(),
            'actor_type' => auth()->user()?::class,
            'old_status' => $event->callback->status->value,
            'new_status' => 'completed',
            'payload' => null,
        ]);
    }

    public function handleRejected(CallbackRejected $event): void
    {
        CallbackAudit::create([
            'callback_id' => $event->callback->id,
            'callback_type' => 'production',
            'action' => 'rejected',
            'actor_id' => $event->rejector->id,
            'actor_type' => get_class($event->rejector),
            'old_status' => $event->callback->status->value,
            'new_status' => 'rejected',
            'payload' => [
                'reason' => $event->reason,
            ],
        ]);
    }
}
```

#### Step 3: Create Migration

Create file: `database/migrations/2026_01_08_000001_create_callback_audits_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('callback_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('callback_id');
            $table->enum('callback_type', ['inventory', 'production']);
            $table->enum('action', ['created', 'approved', 'completed', 'rejected']);
            $table->unsignedBigInteger('actor_id');
            $table->string('actor_type');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['callback_id', 'callback_type']);
            $table->index(['actor_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('callback_audits');
    }
};
```

#### Step 4: Create Model

Create file: `app/Models/CallbackAudit.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallbackAudit extends Model
{
    protected $fillable = [
        'callback_id',
        'callback_type',
        'action',
        'actor_id',
        'actor_type',
        'old_status',
        'new_status',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function getActorAttribute()
    {
        return $this->actor_type::find($this->actor_id);
    }
}
```

#### Step 5: Register Events in Models

Update `app/Models/ProductDispatchCallback.php`:
```php
protected static function boot()
{
    parent::boot();

    static::updated(function ($callback) {
        if ($callback->wasChanged('status')) {
            if ($callback->status === CallbackStatus::APPROVED_BY_PRODUCTION) {
                event(new CallbackApproved($callback, current_actor(), 'inventory'));
            } elseif ($callback->status === CallbackStatus::COMPLETED) {
                event(new CallbackCompleted($callback, 'inventory'));
            }
        }
    });
}
```

**Benefits:**
- ✅ Complete audit trail of all changes
- ✅ Track who approved what and when
- ✅ Compliance-ready for regulatory bodies
- ✅ Easy debugging - follow the trail
- ✅ Accountability - can see who did what

**Effort:** 4 hours  
**Files:** 4 new files, 2 modified files

---

### Improvement #2: Add Comprehensive Unit Tests

**Problem:** Stock update logic and validation untested.

**Solution:**

Create file: `tests/Unit/Models/ProductDispatchCallbackTest.php`
```php
<?php

namespace Tests\Unit\Models;

use App\Models\ProductDispatchCallback;
use App\Models\ProductStock;
use App\Models\DailyProduce;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDispatchCallbackTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_quantity_against_available_stock()
    {
        $dispatch = ProductDispatch::factory()->create(['received_quantity' => 100]);
        $product = Product::factory()->create();

        // Create 2 existing callbacks
        ProductDispatchCallback::factory()->create([
            'product_dispatch_id' => $dispatch->id,
            'quantity' => 30,
        ]);
        ProductDispatchCallback::factory()->create([
            'product_dispatch_id' => $dispatch->id,
            'quantity' => 20,
        ]);

        // Available: 100 - 30 - 20 = 50
        // Should allow 50
        $callback = ProductDispatchCallback::make([
            'product_dispatch_id' => $dispatch->id,
            'quantity' => 50,
        ]);
        $this->assertTrue($callback->validateQuantity());

        // Should reject 51
        $callback->quantity = 51;
        $this->expectException(ValidationException::class);
        $callback->validateQuantity();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_orphaned_callbacks()
    {
        $callback = ProductDispatchCallback::make([
            'product_dispatch_id' => null,
            'quantity' => 100,
        ]);

        // Should not throw error even though product_dispatch_id is null
        $this->assertTrue($callback->validateQuantity());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_product_stock_on_completion()
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::RECEIVED_BY_PRODUCTION,
            'quantity' => 50,
        ]);
        $productStock = ProductStock::factory()->create([
            'product_id' => $callback->product_id,
        ]);

        $callback->completeWithStockUpdate();

        $productStock->refresh();
        $this->assertEquals(50, $productStock->callback_quantity);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_daily_produce_on_completion()
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::RECEIVED_BY_PRODUCTION,
            'quantity' => 50,
        ]);
        $dailyProduce = DailyProduce::factory()->create([
            'product_id' => $callback->product_id,
        ]);

        $callback->completeWithStockUpdate();

        $dailyProduce->refresh();
        $this->assertEquals(50, $dailyProduce->callback_quantity);
        // closing_quantity should be reduced by callback quantity
        $this->assertEquals($dailyProduce->opening_quantity + $dailyProduce->produced - 50, $dailyProduce->closing_quantity);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_received_status_before_completion()
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::APPROVED_BY_PRODUCTION,
        ]);

        $this->expectException(RuntimeException::class);
        $callback->completeWithStockUpdate();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_uses_polymorphic_relationships()
    {
        $user = User::factory()->create();
        $callback = ProductDispatchCallback::factory()->create([
            'recordedby_id' => $user->id,
            'recordedby_type' => User::class,
        ]);

        $this->assertEquals($user->id, $callback->recordedBy->id);
        $this->assertInstanceOf(User::class, $callback->recordedBy);
    }
}
```

Create file: `tests/Unit/Models/ProductionCallbackTest.php`
```php
<?php

namespace Tests\Unit\Models;

use App\Models\ProductionCallback;
use App\Models\Stock;
use App\Models\DailyProduce;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductionCallbackTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_validates_finished_product_quantity()
    {
        $dailyProduce = DailyProduce::factory()->create(['produced' => 100]);
        
        // Create callback with excessive quantity
        $callback = ProductionCallback::make([
            'source_type' => 'finished_product',
            'product_id' => $dailyProduce->product_id,
            'quantity' => 101, // Exceeds produced quantity
        ]);

        $this->expectException(ValidationException::class);
        $callback->validateQuantity();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_any_quantity_for_raw_materials()
    {
        $callback = ProductionCallback::make([
            'source_type' => 'raw_material',
            'item_id' => Item::factory()->create()->id,
            'quantity' => 999999,
        ]);

        // Should not throw error
        $this->assertTrue($callback->validateQuantity());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_raw_material_stock_on_approval()
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

        $callback->approve(current_actor());

        $stock->refresh();
        $this->assertEquals(70, $stock->quantity_available);
        $this->assertEquals(30, $stock->quantity_damaged);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_finished_product_stock_on_approval()
    {
        $dailyProduce = DailyProduce::factory()->create();

        $callback = ProductionCallback::factory()->create([
            'source_type' => 'finished_product',
            'product_id' => $dailyProduce->product_id,
            'quantity' => 25,
            'status' => CallbackStatus::PENDING,
        ]);

        $callback->approve(current_actor());

        $dailyProduce->refresh();
        $this->assertEquals(25, $dailyProduce->callback_quantity);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_stock_update_on_rejection()
    {
        $stock = Stock::factory()->create(['quantity_available' => 100]);
        
        $callback = ProductionCallback::factory()->create([
            'source_type' => 'raw_material',
            'item_id' => $stock->item_id,
            'quantity' => 30,
            'status' => CallbackStatus::PENDING,
        ]);

        $callback->reject(current_actor(), 'Duplicate');

        $stock->refresh();
        $this->assertEquals(100, $stock->quantity_available); // Unchanged
    }
}
```

**Benefits:**
- ✅ Catch regressions early
- ✅ Verify edge cases work
- ✅ Document expected behavior
- ✅ Confidence in stock logic

**Effort:** 4 hours  
**Files:** 2 new test files

---

### Improvement #3: Add Feature Tests for Workflows

**Problem:** Complete workflows not tested end-to-end.

**Solution:**

Create file: `tests/Feature/Callbacks/ProductDispatchCallbackWorkflowTest.php`
```php
<?php

namespace Tests\Feature\Callbacks;

use App\Models\ProductDispatchCallback;
use App\Models\ProductStock;
use App\Models\DailyProduce;
use App\Enums\CallbackStatus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductDispatchCallbackWorkflowTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_completes_full_inventory_callback_workflow()
    {
        // 1. Create callback (Sales)
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::PENDING,
            'quantity' => 50,
        ]);

        // 2. Approve callback (Production)
        $callback->approve(auth()->user());
        $this->assertEquals(CallbackStatus::APPROVED_BY_PRODUCTION, $callback->status);

        // 3. Mark as received (Production)
        $callback->markAsReceived(auth()->user());
        $this->assertEquals(CallbackStatus::RECEIVED_BY_PRODUCTION, $callback->status);

        // 4. Create stock before completion
        ProductStock::factory()->create([
            'product_id' => $callback->product_id,
            'callback_quantity' => 0,
        ]);
        DailyProduce::factory()->create([
            'product_id' => $callback->product_id,
            'callback_quantity' => 0,
            'opening_quantity' => 100,
            'produced' => 50,
            'closing_quantity' => 150,
        ]);

        // 5. Complete callback (Production)
        $callback->completeWithStockUpdate();
        $this->assertEquals(CallbackStatus::COMPLETED, $callback->status);

        // 6. Verify stock updated
        $stock = ProductStock::where('product_id', $callback->product_id)->first();
        $this->assertEquals(50, $stock->callback_quantity);

        $produce = DailyProduce::where('product_id', $callback->product_id)->first();
        $this->assertEquals(50, $produce->callback_quantity);
        $this->assertEquals(100, $produce->closing_quantity); // 100 + 50 - 50
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_skipping_approval_stages()
    {
        $callback = ProductDispatchCallback::factory()->create([
            'status' => CallbackStatus::PENDING,
        ]);

        // Try to complete without receiving first
        $this->expectException(\RuntimeException::class);
        $callback->completeWithStockUpdate();
    }
}
```

**Benefits:**
- ✅ Test complete workflows
- ✅ Catch integration issues
- ✅ Verify all systems work together

**Effort:** 4 hours  
**Files:** 2 new test files

---

## 🎯 High-Priority Improvements

### Improvement #4: Standardize Status Names

**Problem:** ProductDispatchCallback and ProductionCallback use different status names.

**Current:**
```
ProductDispatchCallback: pending, approved_by_production, received_by_production, completed
ProductionCallback: pending, approved_by_inventory, completed, rejected
```

**Proposed Standard:**
```
PENDING           (initial state)
APPROVED          (approved by authority)
RECEIVED          (only for inventory callbacks)
COMPLETED         (final state)
REJECTED          (rejected state)
```

**Migration Steps:**

1. Create migration: `database/migrations/2026_01_08_000002_standardize_callback_statuses.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ProductDispatchCallback
        DB::table('product_dispatch_callbacks')
            ->where('status', 'approved_by_production')
            ->update(['status' => 'approved']);

        DB::table('product_dispatch_callbacks')
            ->where('status', 'received_by_production')
            ->update(['status' => 'received']);

        // ProductionCallback
        DB::table('production_callbacks')
            ->where('status', 'approved_by_inventory')
            ->update(['status' => 'approved']);
    }

    public function down(): void
    {
        // ProductDispatchCallback
        DB::table('product_dispatch_callbacks')
            ->where('status', 'approved')
            ->where('received_by_type', '!=', null) // only if not received
            ->update(['status' => 'approved_by_production']);

        DB::table('product_dispatch_callbacks')
            ->where('status', 'received')
            ->update(['status' => 'received_by_production']);

        // ProductionCallback
        DB::table('production_callbacks')
            ->where('status', 'approved')
            ->update(['status' => 'approved_by_inventory']);
    }
};
```

2. Update `CallbackStatus` enum to use new values
3. Update all queries in components
4. Update all views and templates

**Benefits:**
- ✅ Unified queries across callback types
- ✅ Easier to write aggregate reports
- ✅ Less confusion for developers
- ✅ Better code maintainability

**Effort:** 3 hours

---

### Improvement #5: Complete Documentation

**Problem:** Documentation not updated with completed work.

**Solution:**

Create/Update files:

1. **README:** `TXTs/CALLBACK/README.md`
```markdown
# Callback System Documentation

## Overview
Complete documentation of callback system...

## Current Status (54% Complete)
- ✅ Core implementation done
- ⏳ Unit tests pending
- ⏳ Feature tests pending
- ⏳ Event listeners pending

## Quick Start
...
```

2. **API Reference:** `md/CALL_A_ACK/API_REFERENCE.md`
- All method signatures
- Usage examples
- Parameter descriptions
- Return values

3. **Troubleshooting:** `md/CALL_A_ACK/TROUBLESHOOTING.md`
- Common issues
- How to debug
- Error messages explained
- Solutions

**Effort:** 2 hours

---

## Summary Table

| Improvement | Priority | Hours | Impact | Status |
|------------|----------|-------|--------|--------|
| Event Listeners | Critical | 4 | Audit trail | Not Started |
| Unit Tests | Critical | 4 | Confidence | Not Started |
| Feature Tests | High | 4 | Integration | Not Started |
| Status Standardization | High | 3 | Maintainability | Not Started |
| Documentation | High | 2 | Knowledge | In Progress |
| Orphaned Callbacks | Medium | 1.5 | Clarity | Not Started |
| Polymorphic Testing | Medium | 2 | Safety | Not Started |
| Performance Optimization | Low | 3+ | Speed | Not Started |

---

## Recommended Implementation Order

1. **Week 1:** Unit Tests + Feature Tests (8 hours) → System verified
2. **Week 2:** Event Listeners + Standardization (7 hours) → Audit trail + consistency
3. **Week 3:** Documentation + Polish (5+ hours) → Complete

---

## Code Quality Improvements

Each improvement maintains:
- ✅ Type safety (use types, enums, assertions)
- ✅ Error handling (try-catch, exceptions)
- ✅ Transactions (DB::transaction for data integrity)
- ✅ Logging (Log facade for debugging)
- ✅ Testing (comprehensive test coverage)
- ✅ Documentation (inline comments + docstrings)

---

## Next: Implementation

See `03_IMPLEMENTATION_ROADMAP.md` for detailed implementation steps.

