# Callback System Fixes - Priority 2 (High Impact)

## Overview
These are high-priority fixes that improve code quality, fix bugs, and standardize the callback system.

---

## Fix 1: Extract Duplicate Query Logic

### Problem
Both `getFilteredQuery()` and `getRowsProperty()` in ApproveCallbacks contain nearly identical code.

### Current Code (DUPLICATE)
```php
// Line 75-84: getFilteredQuery()
protected function getFilteredQuery()
{
    $query = ProductionCallback::query()
        ->with(['shift', 'item', 'product', 'recordedBy', 'approvedBy'])
        ->whereHas('shift', function ($q) {
            $q->where('branch_id', $this->getBranchId());
        });

    return $query;
}

// Line 97-145: getRowsProperty()
public function getRowsProperty()
{
    $query = ProductionCallback::with([
        'shift',
        'item',
        'product',
        'recordedBy',
        'approvedBy'
    ])
    ->whereHas('shift', function ($q) {
        $q->where('branch_id', $this->getBranchId());
    });
    
    // ... filtering code ...
    return $query->orderBy('callback_time', 'desc')->paginate($this->quantity);
}
```

### Solution
```php
/**
 * Get base query with all filters applied
 */
protected function buildQuery()
{
    $query = ProductionCallback::with([
        'shift',
        'item',
        'product',
        'recordedBy',
        'approvedBy'
    ])
    ->whereHas('shift', function ($q) {
        $q->where('branch_id', $this->getBranchId());
    });

    // Apply all filters
    $query = $this->applyFilters($query);

    return $query;
}

/**
 * Apply all active filters to query
 */
protected function applyFilters($query)
{
    // Search filter
    if ($this->search) {
        $query->where(function ($q) {
            $q->whereHas('item', function ($itemQuery) {
                $itemQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('product', function ($productQuery) {
                $productQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('recordedBy', function ($employeeQuery) {
                $employeeQuery->where('name', 'like', '%' . $this->search . '%');
            });
        });
    }

    // Status filter
    if ($this->filterStatus) {
        $query->where('status', $this->filterStatus);
    }

    // Source type filter
    if ($this->filterSourceType) {
        $query->where('source_type', $this->filterSourceType);
    }

    // Date range filter
    if ($this->startDate) {
        $query->whereDate('callback_time', '>=', $this->startDate);
    }
    if ($this->endDate) {
        $query->whereDate('callback_time', '<=', $this->endDate);
    }

    return $query;
}

/**
 * Get filtered query without pagination (for counts, exports)
 */
protected function getFilteredQuery()
{
    return $this->buildQuery();
}

/**
 * Get paginated rows property
 */
public function getRowsProperty()
{
    return $this->buildQuery()
        ->orderBy('callback_time', 'desc')
        ->paginate($this->quantity);
}
```

---

## Fix 2: Standardize Status Workflows

### Problem
Each callback system has different status flows:
- **Inventory:** pending → approved_by_inventory → completed OR rejected
- **Production:** pending → approved_by_production → received_by_production → completed
- **Sales:** Multiple variations

### Solution: Create Unified Callback Status Class

**Create: `app/Enums/CallbackStatus.php`**
```php
<?php

namespace App\Enums;

enum CallbackStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case RECEIVED = 'received'; // For workflows requiring physical receipt
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Approval',
            self::APPROVED => 'Approved',
            self::RECEIVED => 'Received',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::APPROVED => 'blue',
            self::RECEIVED => 'purple',
            self::COMPLETED => 'green',
            self::REJECTED => 'red',
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return match($this) {
            self::PENDING => in_array($target, [self::APPROVED, self::REJECTED]),
            self::APPROVED => in_array($target, [self::RECEIVED, self::COMPLETED]),
            self::RECEIVED => in_array($target, [self::COMPLETED, self::REJECTED]),
            self::COMPLETED => false,
            self::REJECTED => false, // No transitions from rejected
        };
    }
}
```

**Update Models to use Enum:**
```php
// ProductionCallback.php
use App\Enums\CallbackStatus;

protected $casts = [
    'status' => CallbackStatus::class,
    'approved_at' => 'datetime',
];

public function canBeApproved(): bool
{
    return $this->status === CallbackStatus::PENDING;
}

public function approve($employeeId): bool
{
    if (!$this->canBeApproved()) {
        throw new \Exception("Cannot approve callback in {$this->status->label()} state");
    }

    $this->update([
        'status' => CallbackStatus::APPROVED,
        'approved_by' => $employeeId,
        'approved_at' => now(),
    ]);

    return true;
}

public function complete(): bool
{
    if (!$this->status->canTransitionTo(CallbackStatus::COMPLETED)) {
        throw new \Exception("Cannot complete callback in {$this->status->label()} state");
    }

    $this->update(['status' => CallbackStatus::COMPLETED]);
    return true;
}
```

---

## Fix 3: Add Missing Rejection Modal to Production Callbacks

### Current Issue
Production Callbacks component is missing rejection functionality entirely.

### Solution

**Update: `app/Livewire/BranchDashboard/Production/Callbacks/ApproveCallbacks.php`**

Add properties:
```php
// Rejection modal (currently missing)
public $showRejectModal = false;
public $rejectReason = '';
public $callbackToReject = null;
```

Add methods:
```php
public function openRejectModal($callbackId)
{
    $this->callbackToReject = $callbackId;
    $this->rejectReason = '';
    $this->showRejectModal = true;
}

public function closeRejectModal()
{
    $this->showRejectModal = false;
    $this->callbackToReject = null;
    $this->rejectReason = '';
}

public function rejectCallback()
{
    if (empty($this->rejectReason) || strlen($this->rejectReason) < 10) {
        $this->toast()->error('Rejection reason must be at least 10 characters.')->send();
        return;
    }

    try {
        DB::beginTransaction();

        $callback = ProductDispatchCallback::whereHas('productDispatch.salesShift', function($q) {
            $q->where('branch_id', $this->getBranchId());
        })->find($this->callbackToReject);

        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }

        if (!$callback->canBeApproved()) {
            $this->toast()->error('Callback cannot be rejected. Current status: ' . $callback->formatted_status)->send();
            return;
        }

        $employeeId = $this->getEmployeeId();
        $callback->reject($employeeId, $this->rejectReason);

        AuditService::log(
            auth()->guard('employees')->user(),
            'reject',
            $callback,
            "Rejected dispatch callback #{$callback->id}. Reason: {$this->rejectReason}",
            'completed'
        );

        DB::commit();

        $this->toast()->success('Callback rejected successfully.')->send();
        $this->closeRejectModal();
        $this->dispatch('$refresh');

    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Failed: ' . $e->getMessage())->send();
    }
}
```

**Add to blade template: `resources/views/livewire/branch-dashboard/production/callbacks/approve-callbacks.blade.php`**

Add button to action column:
```blade
@if($row->status === 'pending')
    <button wire:click="openRejectModal({{ $row->id }})"
        class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-medium transition-colors">
        Reject
    </button>
@endif
```

Add rejection modal at end of file:
```blade
<!-- Rejection Modal -->
@if($showRejectModal)
    <div x-data="{ show: @entangle('showRejectModal') }" x-show="show" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="show" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-zinc-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-zinc-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-zinc-100">
                                Reject Callback
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Please provide a detailed reason for rejecting this callback.
                                </p>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Rejection Reason</label>
                                <textarea wire:model="rejectReason" rows="4"
                                    class="w-full px-3 py-2 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 focus:ring-2 focus:ring-red-500 text-sm"
                                    placeholder="Enter the reason for rejection..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button wire:click="rejectCallback" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Reject Callback
                    </button>
                    <button wire:click="closeRejectModal" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-700 text-base font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
```

---

## Fix 4: Create CallbackService for Shared Logic

**Create: `app/Services/CallbackService.php`**

```php
<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\DailyProduce;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Model;

class CallbackService
{
    /**
     * Handle raw material callback stock updates
     */
    public function handleRawMaterialReturn($itemId, $quantity, $branchId, $callbackId)
    {
        $stock = Stock::where('item_id', $itemId)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        $stock->decrement('quantity_available', $quantity);
        $stock->increment('quantity_damaged', $quantity);

        StockMovement::create([
            'stock_id' => $stock->id,
            'type' => 'callback',
            'quantity' => -$quantity,
            'reference_type' => 'App\\Models\\ProductionCallback',
            'reference_id' => $callbackId,
            'movement_date' => now(),
            'notes' => "Raw material callback processed (Callback #{$callbackId})",
        ]);

        return $stock;
    }

    /**
     * Handle finished product callback
     */
    public function handleFinishedProductReject($productId, $shiftId, $quantity, $callbackId)
    {
        $recipe = Recipe::where('product_id', $productId)
            ->firstOrFail();

        $dailyProduce = DailyProduce::where('shift_id', $shiftId)
            ->where('recipe_id', $recipe->id)
            ->firstOrFail();

        $dailyProduce->increment('callback_quantity', $quantity);
        $dailyProduce->updateCalculations();
        $dailyProduce->save();

        return $dailyProduce;
    }

    /**
     * Validate callback can transition to new state
     */
    public function validateTransition($callback, $newStatus): void
    {
        if (!$callback->status->canTransitionTo($newStatus)) {
            throw new \Exception(
                "Cannot transition from {$callback->status->label()} to {$newStatus->label()}"
            );
        }
    }
}
```

Update ApproveCallbacks to use service:
```php
public function approveCallback($callbackId)
{
    try {
        DB::beginTransaction();
        
        $callback = $this->getAuthorizedCallback($callbackId);
        
        $employeeId = $this->getEmployeeId();
        $callback->approve($employeeId);

        // Use service for business logic
        if ($callback->isRawMaterial()) {
            app(CallbackService::class)->handleRawMaterialReturn(
                $callback->item_id,
                $callback->quantity,
                $this->getBranchId(),
                $callback->id
            );
        } elseif ($callback->isFinishedProduct()) {
            app(CallbackService::class)->handleFinishedProductReject(
                $callback->product_id,
                $callback->shift_id,
                $callback->quantity,
                $callback->id
            );
        }

        DB::commit();
        // ...
    }
}
```

---

## Implementation Checklist

- [ ] Create `app/Enums/CallbackStatus.php`
- [ ] Update ProductionCallback to use CallbackStatus enum
- [ ] Update ProductDispatchCallback to use CallbackStatus enum
- [ ] Update database migrations to use enum (or string with validation)
- [ ] Extract `buildQuery()` and `applyFilters()` methods
- [ ] Remove duplicate filtering logic from `getRowsProperty()`
- [ ] Create `CallbackService` class
- [ ] Add rejection modal to Production Callbacks
- [ ] Add `openRejectModal()`, `closeRejectModal()`, `rejectCallback()` methods
- [ ] Update blade template with rejection button and modal
- [ ] Add `reject()` method to ProductDispatchCallback model
- [ ] Test: verify rejection workflow works end-to-end
- [ ] Test: verify status transitions are validated
- [ ] Test: verify service handles both callback types correctly
