# Critical Improvements & Implementation Guide

## Priority 1: Fix Polymorphic Type Mismatch (BLOCKING)

### Investigation Required

First, check the actual database schema:

```bash
DESCRIBE product_dispatch_callbacks;
DESCRIBE production_callbacks;
```

### If Polymorphic (Current Issue)

**Problem**: Migrations create polymorphic columns but models don't handle them.

**Solution A: Use Polymorphic Relationship**

```php
// In ProductDispatchCallback.php
public function recordedBy()
{
    return $this->morphTo(
        'recorded_by',           // Relationship name
        'recorded_by_type',      // Type column
        'recorded_by_id'         // ID column
    );
}

public function approvedBy()
{
    return $this->morphTo(
        'approved_by',
        'approved_by_type',
        'approved_by_id'
    );
}

public function receivedBy()
{
    return $this->morphTo(
        'received_by',
        'received_by_type',
        'received_by_id'
    );
}

// Update fillable:
protected $fillable = [
    // ... existing fields ...
    'recorded_by_id',
    'recorded_by_type',
    'approved_by_id',
    'approved_by_type',
    'received_by_id',
    'received_by_type',
];
```

**Usage**:
```php
$callback->recordedBy;   // Returns Employee instance
$callback->approvedBy;   // Returns Employee instance
```

### Solution B: Simplify to Direct Foreign Key

**Migration**:
```php
public function up(): void
{
    // Create new columns
    Schema::table('product_dispatch_callbacks', function (Blueprint $table) {
        $table->uuid('recorded_by_employee_id')->nullable();
        $table->uuid('approved_by_employee_id')->nullable();
        $table->uuid('received_by_employee_id')->nullable();
        
        $table->foreign('recorded_by_employee_id')
            ->references('id')->on('employees')
            ->onDelete('set null');
        $table->foreign('approved_by_employee_id')
            ->references('id')->on('employees')
            ->onDelete('set null');
        $table->foreign('received_by_employee_id')
            ->references('id')->on('employees')
            ->onDelete('set null');
    });
    
    // Migrate data from polymorphic
    DB::statement("
        UPDATE product_dispatch_callbacks
        SET recorded_by_employee_id = recorded_by_id
        WHERE recorded_by_type = 'App\\Models\\Employee'
    ");
    
    // Drop old columns
    Schema::table('product_dispatch_callbacks', function (Blueprint $table) {
        $table->dropColumn(['recorded_by_id', 'recorded_by_type', 'approved_by_id', 'approved_by_type', 'received_by_id', 'received_by_type']);
    });
}
```

**Model** (Simplified):
```php
public function recordedBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'recorded_by_employee_id');
}

public function approvedBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'approved_by_employee_id');
}

public function receivedBy(): BelongsTo
{
    return $this->belongsTo(Employee::class, 'received_by_employee_id');
}
```

**Recommendation**: Solution B is simpler and more maintainable. Use Solution A only if you need polymorphic relationships to other models.

---

## Priority 2: Move Stock Logic to Models

### Current Problem
Stock updates are in Livewire components - not reusable, not testable, breaks API consistency.

### Solution: Extract to Model Methods

**File**: `/app/Models/ProductDispatchCallback.php`

```php
class ProductDispatchCallback extends Model
{
    // ... existing code ...
    
    /**
     * Complete the callback and update all related stock
     * 
     * @throws InvalidStateException
     */
    public function completeWithStockUpdate(): bool
    {
        if ($this->status !== 'received_by_production') {
            throw new InvalidStateException(
                'Callback must be received before completion. Current status: ' . $this->status
            );
        }
        
        return DB::transaction(function () {
            $this->updateProductStock();
            $this->updateDailyProduce();
            $this->update(['status' => 'completed']);
            return true;
        });
    }
    
    /**
     * Update ProductStock when product is returned to sales
     */
    private function updateProductStock(): void
    {
        $productStock = ProductStock::where('sales_shift_id', $this->sales_shift_id)
            ->where('product_id', $this->product_id)
            ->lockForUpdate()
            ->first();
        
        if (!$productStock) {
            throw new RuntimeException(
                'Product stock record not found for sales shift: ' . $this->sales_shift_id
            );
        }
        
        $productStock->increment('callback_quantity', $this->quantity);
        // This should trigger ProductStock::booted() to recalculate total_available
    }
    
    /**
     * Update DailyProduce when product is returned to production
     */
    private function updateDailyProduce(): void
    {
        // Find the original production that created this product
        $productDispatch = $this->productDispatch;
        if (!$productDispatch || !$productDispatch->shift_id) {
            throw new RuntimeException('Product dispatch or shift not found for callback');
        }
        
        $recipe = Recipe::where('product_id', $this->product_id)->first();
        if (!$recipe) {
            throw new RuntimeException('Recipe not found for product: ' . $this->product_id);
        }
        
        $dailyProduce = DailyProduce::where('shift_id', $productDispatch->shift_id)
            ->where('recipe_id', $recipe->id)
            ->lockForUpdate()
            ->first();
        
        if ($dailyProduce) {
            $dailyProduce->increment('callback_quantity', $this->quantity);
            $dailyProduce->increment('closing_quantity', $this->quantity);
            $dailyProduce->updateCalculations();
        } else {
            Log::warning('DailyProduce not found for callback', [
                'callback_id' => $this->id,
                'shift_id' => $productDispatch->shift_id,
                'recipe_id' => $recipe->id,
            ]);
        }
    }
}
```

**File**: `/app/Models/ProductionCallback.php`

```php
class ProductionCallback extends Model
{
    // ... existing code ...
    
    /**
     * Approve callback with automatic stock updates
     */
    public function approveWithStockUpdate(string $employeeId): bool
    {
        return DB::transaction(function () use ($employeeId) {
            // Update stock based on type
            if ($this->isRawMaterial()) {
                $this->updateRawMaterialStock();
            } elseif ($this->isFinishedProduct()) {
                $this->updateFinishedProductStock();
            }
            
            // Then approve
            $this->approve($employeeId);
            return true;
        });
    }
    
    /**
     * Update stock for raw material callbacks
     */
    private function updateRawMaterialStock(): void
    {
        if (!$this->item_id) {
            throw new InvalidArgumentException('Raw material callback missing item_id');
        }
        
        $stock = Stock::where('item_id', $this->item_id)
            ->where('branch_id', $this->shift->branch_id)
            ->lockForUpdate()
            ->first();
        
        if (!$stock) {
            throw new RuntimeException(
                'Stock record not found for item: ' . $this->item_id
            );
        }
        
        // Decrease available, increase damaged
        $stock->quantity_available = max(0, $stock->quantity_available - $this->quantity);
        $stock->quantity_damaged += $this->quantity;
        $stock->save();
        
        // Log the movement
        StockMovement::create([
            'stock_id' => $stock->id,
            'type' => 'callback',
            'quantity' => -$this->quantity,
            'reference_type' => 'production_callback',
            'reference_id' => $this->id,
            'notes' => 'Production callback: ' . $this->reason,
            'movement_date' => now(),
        ]);
    }
    
    /**
     * Update stock for finished product callbacks
     */
    private function updateFinishedProductStock(): void
    {
        if (!$this->product_id) {
            throw new InvalidArgumentException('Finished product callback missing product_id');
        }
        
        $recipe = Recipe::where('product_id', $this->product_id)->first();
        if (!$recipe) {
            throw new RuntimeException('Recipe not found for product: ' . $this->product_id);
        }
        
        $dailyProduce = DailyProduce::where('shift_id', $this->shift_id)
            ->where('recipe_id', $recipe->id)
            ->lockForUpdate()
            ->first();
        
        if (!$dailyProduce) {
            throw new RuntimeException(
                'DailyProduce not found for shift: ' . $this->shift_id . 
                ', recipe: ' . $recipe->id
            );
        }
        
        $dailyProduce->callback_quantity += $this->quantity;
        $dailyProduce->updateCalculations();
    }
}
```

**Update Livewire Components**:

```php
// Before:
public function completeCallback($id)
{
    try {
        DB::beginTransaction();
        $callback = ProductDispatchCallback::find($id);
        $this->handleStockImpact($callback);  // Manual update
        $callback->complete();
        DB::commit();
    } catch (Exception $e) {
        DB::rollBack();
    }
}

// After:
public function completeCallback($id)
{
    try {
        $callback = ProductDispatchCallback::find($id);
        $callback->completeWithStockUpdate();  // Model handles all
        $this->toast()->success('Callback completed successfully!')->send();
    } catch (Exception $e) {
        $this->toast()->error('Failed: ' . $e->getMessage())->send();
    }
}
```

---

## Priority 3: Add Comprehensive Validation

### ProductDispatchCallback Validation

```php
class ProductDispatchCallback extends Model
{
    /**
     * Validate callback quantity against available quantity
     */
    public function validateQuantity(): bool
    {
        if (!$this->productDispatch) {
            throw new RuntimeException('No dispatch found for callback');
        }
        
        $totalCallbacks = self::where('product_dispatch_id', $this->product_dispatch_id)
            ->where('id', '!=', $this->id ?? 'fake')
            ->whereIn('status', ['pending', 'approved_by_production', 'received_by_production', 'completed'])
            ->sum('quantity');
        
        $available = $this->productDispatch->received_quantity - $totalCallbacks;
        
        if ($this->quantity > $available) {
            throw new ValidationException(
                "Callback quantity ({$this->quantity}) exceeds available ({$available})"
            );
        }
        
        return true;
    }
    
    /**
     * Boot method with validation
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($callback) {
            $callback->validateQuantity();
        });
        
        static::updating(function ($callback) {
            // Only validate quantity if quantity changed
            if ($callback->isDirty('quantity')) {
                $callback->validateQuantity();
            }
        });
    }
}
```

### ProductionCallback Validation

```php
class ProductionCallback extends Model
{
    /**
     * Validate finished product quantity
     */
    public function validateFinishedProductQuantity(): bool
    {
        if (!$this->isFinishedProduct()) {
            return true;
        }
        
        $dailyProduce = DailyProduce::whereHas('recipe', function ($q) {
            $q->where('product_id', $this->product_id);
        })
        ->where('shift_id', $this->shift_id)
        ->first();
        
        if (!$dailyProduce) {
            throw new RuntimeException(
                'DailyProduce not found for product and shift'
            );
        }
        
        if ($this->quantity > $dailyProduce->produced_quantity) {
            throw new ValidationException(
                "Callback quantity ({$this->quantity}) exceeds produced ({$dailyProduce->produced_quantity})"
            );
        }
        
        return true;
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($callback) {
            $callback->validateFinishedProductQuantity();
        });
    }
}
```

---

## Priority 4: Improve Status Management

### Create Status Enum

```php
// app/Enums/CallbackStatus.php
enum CallbackStatus: string
{
    case PENDING = 'pending';
    case APPROVED_BY_PRODUCTION = 'approved_by_production';
    case RECEIVED_BY_PRODUCTION = 'received_by_production';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
    
    public function canTransitionTo(CallbackStatus $target): bool
    {
        return match($this) {
            self::PENDING => in_array($target, [
                self::APPROVED_BY_PRODUCTION,
                self::REJECTED,
            ]),
            self::APPROVED_BY_PRODUCTION => in_array($target, [
                self::RECEIVED_BY_PRODUCTION,
            ]),
            self::RECEIVED_BY_PRODUCTION => in_array($target, [
                self::COMPLETED,
            ]),
            default => false,
        };
    }
    
    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => 'Pending Approval',
            self::APPROVED_BY_PRODUCTION => 'Approved - Awaiting Receipt',
            self::RECEIVED_BY_PRODUCTION => 'Received - Awaiting Completion',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
        };
    }
}
```

### Use in Model

```php
class ProductDispatchCallback extends Model
{
    protected $casts = [
        'status' => CallbackStatus::class,
        // ... other casts
    ];
    
    /**
     * Ensure valid status transitions
     */
    public function setStatusAttribute(CallbackStatus|string $value)
    {
        $newStatus = $value instanceof CallbackStatus ? $value : CallbackStatus::from($value);
        $currentStatus = $this->status ?? CallbackStatus::PENDING;
        
        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new InvalidStateException(
                "Cannot transition from {$currentStatus->value} to {$newStatus->value}"
            );
        }
        
        $this->attributes['status'] = $newStatus->value;
    }
}
```

---

## Priority 5: Add Event Listeners for Audit Trail

### Create Event Classes

```php
// app/Events/CallbackApproved.php
class CallbackApproved
{
    public function __construct(
        public ProductDispatchCallback $callback,
        public Employee $approvedBy,
    ) {}
}

// app/Events/CallbackCompleted.php
class CallbackCompleted
{
    public function __construct(
        public ProductDispatchCallback $callback,
        public Employee $completedBy,
    ) {}
}
```

### Create Listener

```php
// app/Listeners/LogCallbackStatusChange.php
class LogCallbackStatusChange
{
    public function handle(CallbackApproved $event)
    {
        CallbackAudit::create([
            'callback_id' => $event->callback->id,
            'action' => 'approved',
            'old_status' => 'pending',
            'new_status' => 'approved_by_production',
            'performed_by' => $event->approvedBy->id,
            'notes' => null,
            'timestamp' => now(),
        ]);
    }
}
```

### Register in Model

```php
class ProductDispatchCallback extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        static::updating(function ($callback) {
            if ($callback->isDirty('status') && $callback->status === 'approved_by_production') {
                event(new CallbackApproved($callback, Auth::user()));
            }
        });
    }
}
```

---

## Priority 6: Add Database Indexes

```php
// database/migrations/XXXX_XX_XX_add_callback_indexes.php
Schema::table('product_dispatch_callbacks', function (Blueprint $table) {
    // Branch filtering optimization
    $table->index(['sales_shift_id', 'callback_time']);
    
    // Quick status reports
    $table->index(['status', 'callback_time']);
    
    // Product-specific queries
    $table->index(['product_id', 'callback_time']);
});

Schema::table('production_callbacks', function (Blueprint $table) {
    // Branch filtering
    $table->index(['shift_id', 'callback_time']);
    
    // Status reports
    $table->index(['status', 'callback_time']);
    
    // Type-specific queries
    $table->index(['source_type', 'callback_time']);
});
```

---

## Implementation Timeline

| Week | Task | Priority | Effort |
|------|------|----------|--------|
| 1 | Fix polymorphic types | CRITICAL | 4h |
| 1 | Move stock logic to models | CRITICAL | 8h |
| 2 | Add validation | HIGH | 4h |
| 2 | Status enums | HIGH | 3h |
| 3 | Event listeners | MEDIUM | 4h |
| 3 | Database indexes | MEDIUM | 2h |
| 4 | Testing & documentation | HIGH | 8h |

**Total**: ~33 hours

---

## Testing Checklist

- [ ] Stock updates work via model method
- [ ] Validation prevents invalid quantities
- [ ] Status transitions are enforced
- [ ] Callbacks can't be over-approved
- [ ] Audit trail captures all changes
- [ ] APIs work with new methods
- [ ] Livewire components updated
- [ ] Tests pass (unit + feature)
- [ ] No data integrity issues
- [ ] Performance improved with indexes
