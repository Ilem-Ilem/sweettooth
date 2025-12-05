# Filter Consistency & Fixes - Implementation Guide

## Overview
Comprehensive guide to fix filter issues, add missing reset functionality, and ensure consistency across all analytics components.

---

## Issue 1: Shift & Department Filters (StockMovementAnalytics)

### Problem
Filters only work for movements with ItemRequest reference, excluding:
- Purchases (reference_type = 'App\Models\Purchase')
- Manual adjustments (reference_type = null)
- Other movement sources

### Current Code (Broken)
```php
->when($this->filterShift, fn ($q) => 
    $q->whereHasMorph('reference', ['App\Models\ItemRequest'], 
        fn ($sq) => $sq->where('shift', $this->filterShift)
    )
)
```

### Solution: Denormalize Shift Data to StockMovement

#### Step 1: Create Migration

```php
// database/migrations/xxxx_xx_xx_add_shift_to_stock_movements.php
return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('shift')->nullable()->after('moved_by_id');
            $table->unsignedBigInteger('department_id')->nullable()->after('shift');
            $table->index(['shift', 'movement_date']);
            $table->index(['department_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['shift', 'movement_date']);
            $table->dropIndex(['department_id', 'movement_date']);
            $table->dropColumn(['shift', 'department_id']);
        });
    }
};
```

#### Step 2: Update StockMovement Model

```php
// app/Models/StockMovement.php

protected $fillable = [
    'stock_id',
    'type',
    'quantity',
    'quantity_before',
    'quantity_after',
    'reference_type',
    'reference_id',
    'moved_by_type',
    'moved_by_id',
    'shift',           // ← NEW
    'department_id',   // ← NEW
    'movement_date',
    'notes',
];
```

#### Step 3: Update All StockMovement Creation Points

**Purchase Creation (BranchDashboard):**
```php
// app/Livewire/BranchDashboard/Inventory/Purchases.php

StockMovement::create([
    'stock_id' => $stock->id,
    'type' => 'in',
    'quantity_before' => $quantity_before,
    'quantity_after' => $stock->quantity_available,
    'quantity' => $quantity,
    'reference_type' => 'App\Models\Purchase',
    'reference_id' => $purchase->id,
    'moved_by_type' => get_class($actor),
    'moved_by_id' => $actor->id,
    'movement_date' => $this->purchase_date,
    'shift' => null,                    // ← ADD
    'department_id' => null,            // ← ADD
    'notes' => 'Purchase: ' . $purchaseNumber,
]);
```

**Item Dispatch (When available):**
```php
StockMovement::create([
    // ... existing fields ...
    'shift' => $dispatch->shift ?? null,           // ← ADD if dispatch has shift
    'department_id' => $dispatch->department_id ?? null, // ← ADD if applicable
    // ...
]);
```

**Item Request (Existing):**
```php
StockMovement::create([
    // ... existing fields ...
    'shift' => $itemRequest->shift,              // ← Already has shift
    'department_id' => $itemRequest->department_id, // ← Already available
    // ...
]);
```

**Manual Adjustment:**
```php
// app/Livewire/BranchDashboard/Inventory/Stocks.php

StockMovement::create([
    // ... existing fields ...
    'shift' => null,                    // ← ADD (no shift for manual)
    'department_id' => null,            // ← ADD (no department for manual)
    // ...
]);
```

#### Step 4: Update Component Filter Method

```php
// app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php

private function applyFiltersToQuery($query)
{
    $query->when($this->selectedItem, fn ($q) => $q->where('stock_id', $this->selectedItem))
        ->when($this->movementType, fn ($q) => $q->where('type', $this->movementType))
        ->when($this->searchTerm, function ($q) {
            $q->where(function ($sq) {
                $sq->whereHas('stock.item', fn ($ssq) => $ssq->where('name', 'like', "%{$this->searchTerm}%")
                    ->orWhere('sku', 'like', "%{$this->searchTerm}%"))
                    ->orWhereHas('mover', fn ($ssq) => $ssq->where('name', 'like', "%{$this->searchTerm}%"))
                    ->orWhere('notes', 'like', "%{$this->searchTerm}%");
            });
        })
        // NEW: Use denormalized fields instead of morphTo
        ->when($this->filterShift, fn ($q) => $q->where('shift', $this->filterShift))
        ->when($this->filterDepartment, fn ($q) => $q->where('department_id', $this->filterDepartment));
}
```

---

## Issue 2: Missing Reset Filters Button

### Solution: Add Reset Method & Button

#### Step 1: Add Method to Component

```php
// app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php

public function resetFilters()
{
    $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    $this->dateTo = now()->format('Y-m-d');
    $this->movementType = '';
    $this->selectedItem = null;
    $this->searchTerm = '';
    $this->itemSearch = '';
    $this->filterShift = '';
    $this->filterDepartment = '';
    $this->resetPage();
    
    // Optional: Show toast message
    session()->flash('success', 'Filters reset successfully.');
}
```

#### Step 2: Add Button to Blade

```blade
<!-- In filters section, next to "Close Filters" button -->
<div class="flex gap-2">
    <button @click="open = !open"
        class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white transition-all duration-200">
        <!-- Toggle button code -->
    </button>
    
    <!-- NEW: Reset Button -->
    <button wire:click="resetFilters"
        class="flex items-center px-2.5 py-1 rounded text-xs font-medium bg-gray-600 hover:bg-gray-700 text-white transition-all duration-200">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        Reset Filters
    </button>
</div>
```

---

## Issue 3: Filter Validation (From ≤ To)

### Solution: Add Validation

#### Step 1: Add Method to Component

```php
public function updatedDateFrom()
{
    $this->validateDateRange();
    $this->resetPage();
}

public function updatedDateTo()
{
    $this->validateDateRange();
    $this->resetPage();
}

private function validateDateRange()
{
    $from = Carbon::parse($this->dateFrom);
    $to = Carbon::parse($this->dateTo);
    
    if ($from->greaterThan($to)) {
        // Swap dates
        $temp = $this->dateFrom;
        $this->dateFrom = $this->dateTo;
        $this->dateTo = $temp;
        
        session()->flash('warning', 'Date range was automatically corrected.');
    }
    
    // Optional: Warn if range > 1 year
    if ($from->diffInDays($to) > 365) {
        session()->flash('warning', 'Note: Large date ranges may impact performance.');
    }
}
```

#### Step 2: Add Validation Messages in Blade

```blade
<!-- In filters section -->
@if (session('warning'))
    <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded text-xs text-yellow-800 dark:text-yellow-200">
        {{ session('warning') }}
    </div>
@endif

@if (session('success'))
    <div class="p-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded text-xs text-green-800 dark:text-green-200">
        {{ session('success') }}
    </div>
@endif
```

---

## Issue 4: Display Applied Filters

### Solution: Show Active Filter Tags

#### Step 1: Create Applied Filters Section

Add to blade template (after filter inputs):

```blade
<!-- Applied Filters Display -->
@if($movementType || $selectedItem || $searchTerm || $filterShift || $filterDepartment)
    <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-700">
        <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-2">Active Filters:</p>
        <div class="flex flex-wrap gap-2">
            @if($movementType)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                    {{ ucfirst($movementType) }}
                    <button wire:click="$set('movementType', '')" class="ml-1 hover:font-bold">×</button>
                </span>
            @endif
            
            @if($selectedItem)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                    Item Selected
                    <button wire:click="$set('selectedItem', null)" class="ml-1 hover:font-bold">×</button>
                </span>
            @endif
            
            @if($searchTerm)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                    "{{ substr($searchTerm, 0, 20) }}{{ strlen($searchTerm) > 20 ? '...' : '' }}"
                    <button wire:click="$set('searchTerm', '')" class="ml-1 hover:font-bold">×</button>
                </span>
            @endif
            
            @if($filterShift)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                    Shift: {{ ucfirst($filterShift) }}
                    <button wire:click="$set('filterShift', '')" class="ml-1 hover:font-bold">×</button>
                </span>
            @endif
            
            @if($filterDepartment)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                    Department Selected
                    <button wire:click="$set('filterDepartment', '')" class="ml-1 hover:font-bold">×</button>
                </span>
            @endif
        </div>
    </div>
@endif
```

---

## Issue 5: Consistent Filter Pattern Across All Analytics

### Checklist for Each Component

Create similar filter sections for:
- [ ] StockLevelAnalytics
- [ ] RequestDispatchAnalytics
- [ ] StockValuation
- [ ] PurchaseAnalytics

**Template:**
```php
// Component
public function resetFilters()
{
    // Reset all filter properties
    $this->resetPage();
}

// Blade
<button wire:click="resetFilters" class="...">Reset Filters</button>

<!-- Applied filters display -->
@if($anyFilterActive)
    <!-- Show active filter tags -->
@endif
```

---

## Implementation Order

1. **Step 1:** Create migration for shift/department denormalization
2. **Step 2:** Update StockMovement model
3. **Step 3:** Update all StockMovement creation points (4 places)
4. **Step 4:** Update component filter logic
5. **Step 5:** Add reset filters method
6. **Step 6:** Add reset button to blade
7. **Step 7:** Add date validation
8. **Step 8:** Add filter display section
9. **Step 9:** Repeat for other components
10. **Step 10:** Test all filters

---

## Testing Checklist

### Migration & Data
- [ ] Migration runs without errors
- [ ] Existing data handles new nullable columns
- [ ] No data loss

### Filter Functionality
- [ ] Shift filter works on all movement types
- [ ] Department filter works on all movement types
- [ ] Date validation prevents invalid ranges
- [ ] Reset button clears all filters
- [ ] Applied filters display shows active filters
- [ ] Clicking × on filter tag removes that filter

### Data Accuracy
- [ ] StockMovement counts match before/after filter
- [ ] Charts update correctly with filters
- [ ] No duplicate movements
- [ ] Performance acceptable with all filters active

### UI/UX
- [ ] Filter tags render correctly (mobile & desktop)
- [ ] Reset button visually distinct
- [ ] Validation messages clear and helpful
- [ ] Dark mode styling consistent

---

## Performance Impact

### Positive
- ✓ Faster queries (direct column instead of morphTo)
- ✓ Better indexing possible
- ✓ Reduced database joins

### Potential Issues
- ⚠️ Denormalized data (slight redundancy)
- ⚠️ Need to update shift/department when source changes (rare)

### Mitigation
- Add observer to update shift if ItemRequest changes
- Document update requirement in code comments

---

## Backward Compatibility

- Existing movements without shift/department: Works fine (NULL values)
- Old filters still work: Yes (backwards compatible)
- No breaking changes: Correct

---

## Related Files to Update

1. `app/Livewire/BranchDashboard/Analytics/StockMovementAnalytics.php` - Filter logic
2. `app/Models/StockMovement.php` - Add fillable fields
3. `resources/views/livewire/branch-dashboard/analytics/stock-movement-analytics.blade.php` - UI
4. All creation points (4 files) - Add shift/department when creating

---

## Rollback Plan

If issues occur:
1. Down migration (removes columns)
2. Revert component filter logic
3. Remove reset button and display
4. Filters return to ItemRequest-only functionality

---

## Future Enhancement

Consider adding audit log for filter changes to track analysis behavior.
