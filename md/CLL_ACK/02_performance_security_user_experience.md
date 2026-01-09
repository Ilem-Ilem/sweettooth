# Performance, Security, and User Experience Issues in Callback System

## 3. Performance and Scalability Faults

### Problem Description
The callback system suffers from N+1 queries in listings and stats calculations, causing slow page loads especially with large datasets.

### Specific Code Evidence
- In `ApproveCallbacks.php:310-331`, stats are recalculated on every render without caching
- The `getRowsProperty()` method in multiple components loads related models inefficiently
- In `ApproveCallbacks.php:87-121`, the `getRowsProperty()` method uses multiple `whereHas` clauses without proper eager loading

### Performance Issues in Stats Calculation
```php
// From ApproveCallbacks.php - inefficient stats calculation
public function render()
{
    // Get stats for the branch - recalculated on every render
    $stats = [
        'total' => ProductDispatchCallback::whereHas('productDispatch.salesShift', function($q) {
            $q->where('branch_id', $this->getBranchId());
        })->count(),

        'pending' => ProductDispatchCallback::whereHas('productDispatch.salesShift', function($q) {
            $q->where('branch_id', $this->getBranchId());
        })->where('status', 'pending')->count(),
        // ... other stats calculated individually
    ];
    // This results in multiple database queries on each render
}
```

### Impact
- Slow page loads, especially with large datasets
- High database load due to multiple queries
- Poor user experience with delayed responses

### Solution: Implement Eager Loading and Caching
```php
// Optimized stats calculation with caching
public function getStatsProperty()
{
    return cache()->remember(
        "callback_stats_{$this->getBranchId()}", 
        now()->addMinutes(5), 
        function () {
            $baseQuery = ProductDispatchCallback::whereHas('productDispatch.salesShift', function($q) {
                $q->where('branch_id', $this->getBranchId());
            });
            
            return [
                'total' => $baseQuery->count(),
                'pending' => $baseQuery->where('status', 'pending')->count(),
                'approved' => $baseQuery->where('status', 'approved_by_production')->count(),
                'received' => $baseQuery->where('status', 'received_by_production')->count(),
                'completed' => $baseQuery->where('status', 'completed')->count(),
            ];
        }
    );
}

// Optimized getRowsProperty with eager loading
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

    // Apply filters efficiently
    if ($this->search) {
        $query->whereHas('product', function ($productQuery) {
            $productQuery->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('sku', 'like', '%' . $this->search . '%');
        });
    }

    if ($this->filterStatus) {
        $query->where('status', $this->filterStatus);
    }

    return $query->orderBy('callback_time', 'desc')
                 ->paginate($this->quantity);
}
```

### Memory-Intensive Exports
#### Problem Description
Large dataset exports load all filtered callbacks into memory, causing out-of-memory errors.

#### Evidence
- In the export functionality, all records are loaded into memory before processing
- No chunked processing for large datasets

#### Solution: Implement Chunked Processing
```php
// Example of chunked export processing
public function exportCallbacks($filters)
{
    $filename = 'callbacks_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    return response()->stream(function() use ($filters) {
        $handle = fopen('php://output', 'w');
        
        // Write CSV headers
        fputcsv($handle, ['ID', 'Product', 'Quantity', 'Status', 'Callback Time', 'Reason']);
        
        // Process in chunks to avoid memory issues
        ProductDispatchCallback::where($filters)
            ->chunk(1000, function ($callbacks) use ($handle) {
                foreach ($callbacks as $callback) {
                    fputcsv($handle, [
                        $callback->id,
                        $callback->product->name ?? 'N/A',
                        $callback->quantity,
                        $callback->status,
                        $callback->callback_time,
                        $callback->reason
                    ]);
                }
            });
        
        fclose($handle);
    }, 200, $headers);
}
```

## 4. Security and Access Control Faults

### Problem Description
The callback system has inconsistent authorization checks and potential security vulnerabilities.

### Specific Code Evidence
- In `ApproveCallbacks.php:130-159`, the `approveCallback()` method checks for an actor but doesn't verify specific permissions
- The `completeCallback()` method (lines 218-250) doesn't verify user roles before completing callbacks
- Session-based employee ID retrieval is vulnerable to manipulation

### Security Issues in Authorization
```php
// From ApproveCallbacks.php - insufficient authorization
public function approveCallback($callbackId)
{
    try {
        DB::beginTransaction();

        $callback = ProductDispatchCallback::find($callbackId);

        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }

        if (!$callback->canBeApproved()) {
            $this->toast()->error('Callback cannot be approved. Current status: ' . $callback->formatted_status)->send();
            return;
        }

        $actor = current_actor(); // No role verification
        if (!$actor) {
            $this->toast()->error('No authenticated actor found. Please ensure you are logged in.')->send();
            return;
        }

        $callback->approve($actor);

        DB::commit();
        $this->toast()->success('Callback approved successfully!')->send();
        $this->dispatch('$refresh');

    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Failed to approve callback: ' . $e->getMessage())->send();
    }
}
```

### Impact
- Unauthorized users could approve/reject callbacks
- Potential for privilege escalation
- Data integrity issues from unauthorized changes

### Solution: Implement Comprehensive Authorization
```php
// Enhanced authorization with policies
public function approveCallback($callbackId)
{
    try {
        DB::beginTransaction();

        $callback = ProductDispatchCallback::find($callbackId);

        if (!$callback) {
            $this->toast()->error('Callback not found.')->send();
            return;
        }

        // Check authorization using Laravel policies
        $this->authorize('approve', $callback);

        if (!$callback->canBeApproved()) {
            $this->toast()->error('Callback cannot be approved. Current status: ' . $callback->formatted_status)->send();
            return;
        }

        $actor = current_actor();
        if (!$actor) {
            $this->toast()->error('No authenticated actor found. Please ensure you are logged in.')->send();
            return;
        }

        $callback->approve($actor);

        DB::commit();
        $this->toast()->success('Callback approved successfully!')->send();
        $this->dispatch('$refresh');

    } catch (AuthorizationException $e) {
        $this->toast()->error('You are not authorized to approve this callback.')->send();
    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Failed to approve callback: ' . $e->getMessage())->send();
    }
}

// Create a Policy class for callbacks
class ProductDispatchCallbackPolicy
{
    use HandlesAuthorization;

    public function approve(User $user, ProductDispatchCallback $callback): bool
    {
        // Only production managers can approve callbacks
        return $user->hasRole('production_manager') || 
               $user->hasPermissionTo('approve-callbacks');
    }
    
    public function complete(User $user, ProductDispatchCallback $callback): bool
    {
        // Only inventory managers can complete callbacks
        return $user->hasRole('inventory_manager') || 
               $user->hasPermissionTo('complete-callbacks');
    }
}
```

### Input Sanitization Issues
#### Problem Description
No input sanitization for user-provided data in searches and notes.

#### Evidence
- Direct string concatenation in queries like `like '%' . $this->search . '%'`
- User input in notes fields not properly sanitized

#### Solution: Implement Input Sanitization
```php
// Sanitize search input
public function updatedSearch()
{
    // Sanitize the search input to prevent injection
    $this->search = e($this->search); // HTML escape
    // Or use Laravel's built-in validation
    $this->validate(['search' => 'nullable|string|max:255']);
}

// In the query, use parameterized queries (which Laravel Eloquent does by default)
public function getRowsProperty()
{
    $query = ProductDispatchCallback::with(['product', 'salesShift'])
        ->whereHas('product', function ($q) {
            // Laravel automatically handles parameterization
            $q->where('name', 'like', '%' . $this->search . '%');
        });
    
    return $query->paginate($this->quantity);
}
```

## 5. User Experience and Error Handling Faults

### Problem Description
The callback system has poor error messages and no user-friendly validation feedback.

### Specific Code Evidence
- Generic exceptions like "Callback not found" without context
- No client-side validation in forms
- Modal state management issues where modals don't reset properly on errors

### Error Handling Issues
```php
// From CreateDispatchCallback.php - poor error handling
public function submitCallback()
{
    // Validation happens but error messages are generic
    $this->validate([
        'callbackQuantity' => 'required|numeric|min:0.01',
        'callbackReason' => 'required|in:expired,damaged,quality_issue,customer_return,over_received,wrong_item,other',
    ], [
        'callbackQuantity.required' => 'Callback quantity is required',
        'callbackQuantity.min' => 'Callback quantity must be greater than 0',
        'callbackReason.required' => 'Please select a callback reason',
    ]);

    try {
        // ... processing logic
    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Error creating callback: ' . $e->getMessage())->send();
        // Generic error message without context
    }
}
```

### Impact
- Users confused by cryptic errors
- Poor user experience with inadequate feedback
- Difficulty troubleshooting issues

### Solution: Implement Comprehensive Error Handling
```php
// Enhanced error handling with contextual messages
public function submitCallback()
{
    $this->validate([
        'callbackQuantity' => 'required|numeric|min:0.01|max:'. $this->getAvailableQuantity($this->selectedDispatch),
        'callbackReason' => 'required|in:expired,damaged,quality_issue,customer_return,over_received,wrong_item,other',
    ], [
        'callbackQuantity.required' => 'Please enter the quantity to return',
        'callbackQuantity.min' => 'Return quantity must be greater than 0',
        'callbackQuantity.max' => 'Return quantity cannot exceed available quantity',
        'callbackReason.required' => 'Please select a reason for the return',
    ]);

    try {
        DB::beginTransaction();

        if (!$this->selectedDispatch) {
            throw new \Exception('Dispatch record not found. Please refresh the page and try again.');
        }

        // Validate callback quantity doesn't exceed available
        $availableQty = $this->getAvailableQuantity($this->selectedDispatch);
        if ($this->callbackQuantity > $availableQty) {
            throw new \Exception("Return quantity cannot exceed available quantity ({$availableQty}).");
        }

        $employee = auth()->user();
        $shiftId = $this->selectedSalesShiftId ?? $this->currentSalesShiftId;

        // Create callback record
        $callback = ProductDispatchCallback::create([
            'product_dispatch_id' => $this->selectedDispatch->id,
            'sales_shift_id' => $shiftId,
            'product_id' => $this->selectedDispatch->product_id,
            'recorded_by_id' => $employee->id,
            'recorded_by_type' => get_class($employee),
            'quantity' => $this->callbackQuantity,
            'uom' => $this->selectedDispatch->uom,
            'reason' => $this->callbackReason,
            'status' => 'pending',
            'notes' => $this->callbackNotes,
            'callback_time' => now(),
        ]);

        DB::commit();

        $this->toast()->success("Return callback created successfully! Callback ID: {$callback->id}. Awaiting production approval.")->send();
        $this->closeCallbackModal();
        $this->resetPage();

    } catch (\Exception $e) {
        DB::rollBack();
        $this->toast()->error('Error creating return: ' . $e->getMessage())->send();
        \Log::error('Callback creation failed', [
            'error' => $e->getMessage(),
            'user_id' => auth()->id(),
            'dispatch_id' => $this->selectedDispatch?->id ?? 'null'
        ]);
    }
}

// Proper modal lifecycle management
public function closeCallbackModal()
{
    $this->showCallbackModal = false;
    $this->selectedDispatch = null;
    $this->callbackQuantity = 0;
    $this->callbackReason = '';
    $this->callbackNotes = '';
    // Clear validation errors
    $this->resetValidation();
}
```

## Implementation Checklist

- [ ] Implement eager loading with constraints
- [ ] Add caching for stats calculations
- [ ] Create database indexes on branch_id, status, callback_time
- [ ] Implement chunked processing for large exports
- [ ] Add maximum page size enforcement
- [ ] Create comprehensive policy classes
- [ ] Add role-based permissions
- [ ] Implement input sanitization
- [ ] Add CSRF protection
- [ ] Add contextual error messages
- [ ] Implement real-time validation
- [ ] Add proper modal lifecycle management
- [ ] Add auto-reset on errors