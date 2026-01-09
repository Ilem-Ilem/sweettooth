# Business Logic and Code Quality Issues in Callback System

## 6. Business Logic and Workflow Faults

### Problem Description
The callback system has incomplete workflow handling where some callbacks can get stuck in intermediate states, and business logic is scattered across multiple components.

### Specific Code Evidence
- In `ProductDispatchCallback.php`, the workflow can get stuck if a callback is approved but never received
- No automated escalation or timeout mechanisms for stuck callbacks
- Stock update logic is duplicated across `ProductDispatchCallback.php` and `ProductionCallback.php`

### Workflow Incompleteness
```php
// From ProductDispatchCallback.php - incomplete workflow handling
public function approveAndReceive($actor = null): bool
{
    if (!$this->approve($actor)) {
        return false;
    }

    return $this->markAsReceived($actor);
}

public function approveReceiveAndComplete($actor = null): bool
{
    if (!$this->approve($actor)) {
        return false;
    }

    if (!$this->markAsReceived($actor)) {
        return false;
    }

    return $this->completeWithStockUpdate();
}

// But there's no mechanism to handle callbacks that get stuck between states
```

### Impact
- Forgotten callbacks block inventory
- Manual intervention required to resolve stuck workflows
- Inconsistent business process execution

### Solution: Add Workflow Timeouts and Escalation
```php
// Add scheduled job to handle stuck callbacks
class HandleStuckCallbacksJob implements ShouldQueue
{
    public function handle()
    {
        // Find callbacks that have been pending for more than 24 hours
        $stuckCallbacks = ProductDispatchCallback::where('status', 'pending')
            ->where('callback_time', '<', now()->subHours(24))
            ->get();

        foreach ($stuckCallbacks as $callback) {
            // Send notification to manager
            $manager = User::role('production_manager')
                ->where('branch_id', $callback->salesShift->branch_id)
                ->first();
                
            if ($manager) {
                $manager->notify(new StuckCallbackNotification($callback));
            }
        }
    }
}

// Add timeout methods to the model
class ProductDispatchCallback extends Model
{
    public function isStuck(): bool
    {
        $timeInCurrentState = now()->diffInHours($this->updated_at);
        
        switch ($this->status) {
            case 'pending':
                return $timeInCurrentState > 24; // More than 24 hours in pending
            case 'approved_by_production':
                return $timeInCurrentState > 48; // More than 48 hours after approval
            default:
                return false;
        }
    }
    
    public function escalate(): bool
    {
        if (!$this->isStuck()) {
            return false;
        }
        
        // Add escalation logic
        $this->notes .= "\n\nEscalated on " . now() . ": Callback has been stuck in {$this->status} status for too long.";
        $this->save();
        
        // Notify higher authority
        $this->notifyStakeholders();
        
        return true;
    }
}
```

### Scattered Stock Update Logic
#### Problem Description
Stock update logic is duplicated across different callback types and controllers.

#### Evidence
- `ProductDispatchCallback.php` has `updateProductStock()` and `updateDailyProduce()` methods
- `ProductionCallback.php` has `updateRawMaterialStock()` and `updateFinishedProductStock()` methods
- Similar logic exists in other inventory components

#### Impact
- Inconsistent updates across the system
- Hard to maintain and debug
- Risk of introducing bugs when updating one location but not others

#### Solution: Centralize Stock Operations in Dedicated Services
```php
// Create a dedicated service for stock operations
class StockUpdateService
{
    public function handleProductDispatchCallback(ProductDispatchCallback $callback): void
    {
        DB::transaction(function () use ($callback) {
            $this->updateProductStock($callback);
            $this->updateDailyProduce($callback);
        });
    }
    
    public function handleProductionCallback(ProductionCallback $callback): void
    {
        DB::transaction(function () use ($callback) {
            if ($callback->isRawMaterial()) {
                $this->updateRawMaterialStock($callback);
            } elseif ($callback->isFinishedProduct()) {
                $this->updateFinishedProductStock($callback);
            }
        });
    }
    
    private function updateProductStock(ProductDispatchCallback $callback): void
    {
        $productStock = ProductStock::where('sales_shift_id', $callback->sales_shift_id)
            ->where('product_id', $callback->product_id)
            ->lockForUpdate()
            ->first();

        if (! $productStock) {
            throw new RuntimeException(
                'Product stock record not found for sales shift: '.$callback->sales_shift_id
            );
        }

        $productStock->increment('callback_quantity', $callback->quantity);
    }
    
    private function updateDailyProduce(ProductDispatchCallback $callback): void
    {
        $productDispatch = $callback->productDispatch;
        if (! $productDispatch || ! $productDispatch->shift_id) {
            throw new RuntimeException('Product dispatch or shift not found for callback');
        }

        $recipe = Recipe::where('product_id', $callback->product_id)->first();
        if (! $recipe) {
            throw new RuntimeException('Recipe not found for product: '.$callback->product_id);
        }

        $dailyProduce = DailyProduce::where('shift_id', $productDispatch->shift_id)
            ->where('recipe_id', $recipe->id)
            ->lockForUpdate()
            ->first();

        if ($dailyProduce) {
            $dailyProduce->increment('callback_quantity', $callback->quantity);
            $dailyProduce->increment('closing_quantity', $callback->quantity);
            $dailyProduce->updateCalculations();
        }
    }
    
    // Similar methods for raw materials and finished products...
}

// Update the models to use the service
class ProductDispatchCallback extends Model
{
    public function completeWithStockUpdate(): bool
    {
        if ($this->status !== 'received_by_production') {
            throw new RuntimeException(
                'Callback must be received before completion. Current status: '.$this->status
            );
        }

        return DB::transaction(function () {
            app(StockUpdateService::class)->handleProductDispatchCallback($this);
            $this->update(['status' => 'completed']);
            return true;
        });
    }
}
```

### No Integration with Financial Systems
#### Problem Description
Callbacks don't trigger corresponding accounting entries, leading to incomplete financial tracking.

#### Evidence
- No journal entry creation when callbacks affect inventory values
- No cost accounting for returned or damaged goods

#### Impact
- Incomplete financial reporting
- Discrepancies between inventory and financial records

#### Solution: Integrate with Accounting Module
```php
// Create an accounting integration service
class CallbackAccountingService
{
    public function recordProductDispatchCallbackAdjustment(ProductDispatchCallback $callback): void
    {
        // Calculate the value of the returned items
        $unitCost = $callback->product->cost_price ?? 0;
        $totalValue = $callback->quantity * $unitCost;
        
        if ($totalValue > 0) {
            // Create journal entry for inventory adjustment
            $journalEntry = JournalEntry::create([
                'date' => now(),
                'description' => "Callback adjustment for product return: {$callback->product->name}",
                'reference_type' => 'product_dispatch_callback',
                'reference_id' => $callback->id,
                'branch_id' => $callback->salesShift->branch_id,
            ]);
            
            // Debit inventory account (reduce inventory value)
            $journalEntry->details()->create([
                'account_id' => $this->getInventoryAccountId($callback->product),
                'debit' => $totalValue,
                'credit' => 0,
            ]);
            
            // Credit COGS account (reverse the cost of goods sold)
            $journalEntry->details()->create([
                'account_id' => $this->getCOGSAccountId($callback->product),
                'debit' => 0,
                'credit' => $totalValue,
            ]);
        }
    }
    
    public function recordProductionCallbackAdjustment(ProductionCallback $callback): void
    {
        // Similar logic for production callbacks
        $itemOrProduct = $callback->isRawMaterial() ? $callback->item : $callback->product;
        $unitCost = $itemOrProduct->cost_price ?? 0;
        $totalValue = $callback->quantity * $unitCost;
        
        if ($totalValue > 0) {
            $journalEntry = JournalEntry::create([
                'date' => now(),
                'description' => "Callback adjustment for damaged goods: {$itemOrProduct->name}",
                'reference_type' => 'production_callback',
                'reference_id' => $callback->id,
                'branch_id' => $callback->shift->branch_id,
            ]);
            
            // Adjust inventory accounts based on callback type
            if ($callback->isRawMaterial()) {
                // Debit waste/damage account, credit raw materials inventory
                $journalEntry->details()->create([
                    'account_id' => $this->getWasteAccountId(),
                    'debit' => $totalValue,
                    'credit' => 0,
                ]);
                
                $journalEntry->details()->create([
                    'account_id' => $this->getRawMaterialsAccountId(),
                    'debit' => 0,
                    'credit' => $totalValue,
                ]);
            } else {
                // For finished goods, adjust finished goods inventory
                $journalEntry->details()->create([
                    'account_id' => $this->getWasteAccountId(),
                    'debit' => $totalValue,
                    'credit' => 0,
                ]);
                
                $journalEntry->details()->create([
                    'account_id' => $this->getFinishedGoodsAccountId(),
                    'debit' => 0,
                    'credit' => $totalValue,
                ]);
            }
        }
    }
    
    private function getInventoryAccountId($product): int
    {
        // Return appropriate inventory account ID based on product category
        return Account::where('name', 'Inventory - ' . $product->category->name)->first()->id;
    }
    
    private function getCOGSAccountId($product): int
    {
        // Return appropriate COGS account ID
        return Account::where('name', 'Cost of Goods Sold')->first()->id;
    }
    
    // Additional helper methods for other account types...
}
```

## 7. Code Quality and Maintainability Faults

### Problem Description
The callback system has duplicated code across components and hardcoded values that make maintenance difficult.

### Specific Code Evidence
- Similar query builders in `ApproveCallbacks.php`, `CreateDispatchCallback.php`, and other callback components
- Validation logic duplicated across multiple files
- Status strings like 'pending', 'approved_by_production' scattered throughout the codebase
- Magic numbers and strings used directly in business logic

### Duplicated Code Example
```php
// From ApproveCallbacks.php - similar to other components
public function getRowsProperty()
{
    $query = ProductDispatchCallback::with([
        'product',
        'salesShift',
        'productDispatch.salesShift',
        'recordedBy',
        'approvedBy',
        'receivedBy'
    ])
    ->whereHas('productDispatch.salesShift', function ($q) {
        $q->where('branch_id', $this->getBranchId());
    });

    // Search filter
    if ($this->search) {
        $query->where(function ($q) {
            $q->whereHas('product', function ($productQuery) {
                $productQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('recordedBy', function ($employeeQuery) {
                $employeeQuery->where('name', 'like', '%' . $this->search . '%');
            });
        });
    }
    // ... rest of the method
}

// From CreateDispatchCallback.php - very similar logic
public function getRowsProperty()
{
    $shiftId = $this->selectedSalesShiftId ?? $this->currentSalesShiftId;

    if (!$shiftId) {
        return ProductDispatch::query()->whereRaw('1=0')->paginate($this->quantity);
    }

    $query = ProductDispatch::with(['product', 'shift', 'productDispatchCallbacks'])
        ->where('sales_shift_id', $shiftId)
        ->where('status', 'received');

    // Search filter
    if ($this->search) {
        $query->whereHas('product', function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('sku', 'like', '%' . $this->search . '%');
        });
    }
    // ... rest of the method
}
```

### Impact
- Changes require updates in multiple places
- Risk of inconsistencies when updating logic
- Higher maintenance overhead

### Solution: Extract Common Logic into Traits and Services
```php
// Create a trait for common filtering logic
trait HasCallbackFilters
{
    public ?string $search = null;
    public ?string $filterStatus = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }
    
    protected function applySearchFilter($query, $relations = ['product'])
    {
        if ($this->search) {
            $query->where(function ($q) use ($relations) {
                foreach ($relations as $relation) {
                    $q->orWhereHas($relation, function ($relQuery) {
                        $relQuery->where('name', 'like', '%' . $this->search . '%')
                                 ->orWhere('sku', 'like', '%' . $this->search . '%');
                    });
                }
            });
        }
        
        return $query;
    }
    
    protected function applyStatusFilter($query, $statusField = 'status')
    {
        if ($this->filterStatus) {
            $query->where($statusField, $this->filterStatus);
        }
        
        return $query;
    }
    
    protected function applyDateRangeFilter($query, $dateField = 'callback_time')
    {
        if ($this->startDate) {
            $query->whereDate($dateField, '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate($dateField, '<=', $this->endDate);
        }
        
        return $query;
    }
}

// Create a base callback component
abstract class BaseCallbackComponent extends BaseComponent
{
    use WithPagination, Interactions, HasCallbackFilters;
    
    public ?int $quantity = 20;
    
    abstract protected function getModelClass();
    
    protected function getBaseQuery()
    {
        return $this->getModelClass()::query();
    }
    
    protected function getEagerLoadRelations(): array
    {
        return ['product', 'recordedBy'];
    }
    
    public function getRowsProperty()
    {
        $query = $this->getBaseQuery()
            ->with($this->getEagerLoadRelations());
            
        $query = $this->applySearchFilter($query);
        $query = $this->applyStatusFilter($query);
        $query = $this->applyDateRangeFilter($query);
        
        return $query->orderBy('callback_time', 'desc')
                     ->paginate($this->quantity);
    }
}

// Now extend the base component
class ApproveCallbacks extends BaseCallbackComponent
{
    use WithPagination, Interactions, HasCallbackFilters;
    
    protected function getModelClass(): string
    {
        return ProductDispatchCallback::class;
    }
    
    protected function getEagerLoadRelations(): array
    {
        return [
            'product',
            'salesShift',
            'productDispatch.salesShift',
            'recordedBy',
            'approvedBy',
            'receivedBy'
        ];
    }
    
    protected function getBaseQuery()
    {
        return ProductDispatchCallback::query()
            ->whereHas('productDispatch.salesShift', function ($q) {
                $q->where('branch_id', $this->getBranchId());
            });
    }
}
```

### Hardcoded Values and Magic Strings
#### Problem Description
Status strings and other values are scattered throughout the codebase, making maintenance difficult and prone to typos.

#### Evidence
- Status strings like 'pending', 'approved_by_production' used directly in controllers and views
- Magic numbers for pagination limits
- Hardcoded role names and permission strings

#### Solution: Use Constants and Enums
```php
// Already using CallbackStatus enum, but ensure consistent usage
enum CallbackStatus: string
{
    case PENDING = 'pending';
    case APPROVED_BY_PRODUCTION = 'approved_by_production';
    case RECEIVED_BY_PRODUCTION = 'received_by_production';
    case COMPLETED = 'completed';
    case APPROVED_BY_INVENTORY = 'approved_by_inventory';
    case REJECTED = 'rejected';
    
    // Already implemented methods for labels and colors
}

// Create a configuration file for callback settings
// config/callbacks.php
return [
    'pagination' => [
        'default_per_page' => 20,
        'max_per_page' => 100,
    ],
    
    'workflow' => [
        'approval_timeout_hours' => 24,
        'receipt_timeout_hours' => 48,
    ],
    
    'permissions' => [
        'approve_callbacks' => 'approve-callbacks',
        'complete_callbacks' => 'complete-callbacks',
        'reject_callbacks' => 'reject-callbacks',
    ],
    
    'roles' => [
        'production_manager' => 'production_manager',
        'inventory_manager' => 'inventory_manager',
    ],
];

// Use configuration values in components
class ApproveCallbacks extends BaseComponent
{
    public ?int $quantity = null;
    
    public function mount()
    {
        $this->quantity = $this->quantity ?: config('callbacks.pagination.default_per_page');
        // ... rest of mount
    }
    
    public function getMaxPerPage(): int
    {
        return config('callbacks.pagination.max_per_page');
    }
    
    public function getTimeoutHours(string $status): int
    {
        return match($status) {
            'pending' => config('callbacks.workflow.approval_timeout_hours'),
            'approved_by_production' => config('callbacks.workflow.receipt_timeout_hours'),
            default => 0,
        };
    }
}
```

## Missing Comprehensive Tests
### Problem Description
No comprehensive test coverage for callback workflows, state transitions, and business logic.

### Evidence
- No visible test files for callback workflows in the project structure
- Complex business logic without automated verification

### Impact
- Risk of regression bugs
- Unreliable deployments
- Manual testing required for every change

### Solution: Add Comprehensive Test Suite
```php
// Feature test for callback workflow
class ProductDispatchCallbackTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_can_create_a_product_dispatch_callback()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $salesShift = SalesShift::factory()->create(['employee_id' => $user->id]);
        $productDispatch = ProductDispatch::factory()->create([
            'product_id' => $product->id,
            'sales_shift_id' => $salesShift->id,
            'received_quantity' => 100,
        ]);
        
        $response = $this->actingAs($user)
            ->post('/api/product-dispatch-callbacks', [
                'product_dispatch_id' => $productDispatch->id,
                'product_id' => $product->id,
                'quantity' => 10,
                'uom' => 'units',
                'reason' => 'customer_return',
                'notes' => 'Customer was not satisfied',
                'callback_time' => now(),
            ]);
            
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('product_dispatch_callbacks', [
            'product_dispatch_id' => $productDispatch->id,
            'status' => 'pending',
            'quantity' => 10,
        ]);
    }
    
    /** @test */
    public function it_can_approve_a_callback()
    {
        $user = User::factory()->create();
        $callback = ProductDispatchCallback::factory()->create(['status' => 'pending']);
        
        $this->actingAs($user)
            ->post("/api/product-dispatch-callbacks/{$callback->id}/approve");
            
        $this->assertDatabaseHas('product_dispatch_callbacks', [
            'id' => $callback->id,
            'status' => 'approved_by_production',
            'approved_by_id' => $user->id,
        ]);
    }
    
    /** @test */
    public function it_cannot_approve_a_non_pending_callback()
    {
        $user = User::factory()->create();
        $callback = ProductDispatchCallback::factory()->create(['status' => 'completed']);
        
        $response = $this->actingAs($user)
            ->post("/api/product-dispatch-callbacks/{$callback->id}/approve");
            
        $response->assertStatus(422);
    }
    
    /** @test */
    public function it_updates_stock_when_callback_is_completed()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $salesShift = SalesShift::factory()->create(['employee_id' => $user->id]);
        $productDispatch = ProductDispatch::factory()->create([
            'product_id' => $product->id,
            'sales_shift_id' => $salesShift->id,
            'received_quantity' => 100,
        ]);
        
        $callback = ProductDispatchCallback::factory()->create([
            'product_dispatch_id' => $productDispatch->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'status' => 'received_by_production',
        ]);
        
        // Initially, no callback quantity
        $productStock = ProductStock::create([
            'sales_shift_id' => $salesShift->id,
            'product_id' => $product->id,
            'quantity_available' => 50,
            'callback_quantity' => 0,
        ]);
        
        $this->actingAs($user)
            ->post("/api/product-dispatch-callbacks/{$callback->id}/complete-with-stock-update");
            
        $productStock->refresh();
        $this->assertEquals(10, $productStock->callback_quantity);
    }
}

// Unit test for state transitions
class CallbackStatusTransitionTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function it_can_transition_from_pending_to_approved_by_production()
    {
        $callback = ProductDispatchCallback::factory()->create(['status' => 'pending']);
        
        $result = $callback->approve();
        
        $this->assertTrue($result);
        $this->assertEquals('approved_by_production', $callback->fresh()->status);
    }
    
    /** @test */
    public function it_cannot_transition_from_completed_to_pending()
    {
        $callback = ProductDispatchCallback::factory()->create(['status' => 'completed']);
        
        // Attempt to change status manually (should not be allowed by business logic)
        $callback->status = 'pending';
        $result = $callback->save();
        
        // This should fail validation or business rules should prevent it
        $this->assertEquals('completed', $callback->fresh()->status);
    }
}
```

## Implementation Checklist

- [ ] Add workflow timeout and escalation mechanisms
- [ ] Create centralized stock update services
- [ ] Implement accounting integration for callbacks
- [ ] Extract common logic into traits and base classes
- [ ] Replace hardcoded values with configuration and enums
- [ ] Add comprehensive test suite for callback workflows
- [ ] Implement proper error handling in services
- [ ] Add event-based architecture for decoupled updates
- [ ] Create repository pattern for data access
- [ ] Add proper logging for debugging