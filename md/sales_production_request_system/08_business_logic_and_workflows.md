# Sales to Production Request System - Business Logic & Workflows

Detailed business logic, decision trees, and complex workflows.

---

## REQUEST CREATION WORKFLOW

### Step 1: Sales User Initiates Request
```
┌─────────────────────────────────────┐
│ Sales User Opens Request Form       │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ System Loads Available Departments  │
│ - Filter: type = 'production'       │
│ - Filter: active = true             │
│ - Default: sales_dept->primary_prod_partner
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ Display Department Selector         │
│ - Show staff count                  │
│ - Show current workload             │
│ - Show estimated completion time    │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ User Selects Department             │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ System Loads Products               │
│ - Filter: department_id = selected  │
│ - Filter: active = true             │
│ - Show batch size & prep time       │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ User Adds Products & Quantities     │
│ - Select 1+ products                │
│ - Set batch quantities              │
│ - Optional: add notes per product   │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ User Sets Priority                  │
│ - normal (default)                  │
│ - urgent (move to front of queue)   │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ User Reviews & Submits              │
│ - Show summary of all products      │
│ - Show estimated completion         │
│ - Confirm and submit                │
└────────────┬────────────────────────┘
             │
             v
┌─────────────────────────────────────┐
│ System Validates Request            │
│ ✓ Department exists & active        │
│ ✓ Products exist & available        │
│ ✓ Quantities valid (min/max)        │
│ ✓ User has permission               │
└────────────┬────────────────────────┘
             │
        ┌────┴────┐
        │          │
       YES        NO
        │          │
        v          v
    ┌─────┐   ┌──────────┐
    │Save │   │Show Error│
    └──┬──┘   └────┬─────┘
       │           │
       v           │
  ┌────────────────┘
  │
  v
┌──────────────────────────────────────┐
│ Request Saved                        │
│ - status = 'pending'                 │
│ - created_by_id = current user       │
│ - sales_department_id = user dept    │
│ - production_department_id = selected│
│ - created_at = now()                 │
└────────────┬─────────────────────────┘
             │
             v
┌──────────────────────────────────────┐
│ Create Audit Log                     │
│ - action = 'request_created'         │
│ - details = request data             │
│ - user_id, ip, timestamp             │
└────────────┬─────────────────────────┘
             │
             v
┌──────────────────────────────────────┐
│ Broadcast Event                      │
│ Channel: production-dept.{dept_id}   │
│ Event: NewRequest                    │
│ Data: product_names, priority, etc   │
└────────────┬─────────────────────────┘
             │
             v
┌──────────────────────────────────────┐
│ Send Notifications                   │
│ - Email to dept manager              │
│ - SMS to on-duty staff (if urgent)   │
│ - In-app notification                │
└────────────┬─────────────────────────┘
             │
             v
┌──────────────────────────────────────┐
│ Show Success Message                 │
│ - "Request created successfully"     │
│ - Link to view request               │
│ - Option to create another           │
└──────────────────────────────────────┘
```

---

## PRODUCTION WORKFLOW

### Step 1: Receive & Review Request
```php
class ProductionRequestService
{
    /**
     * Get pending requests for department
     */
    public function getPendingRequests(Department $dept)
    {
        return ProductionRequest::where('production_department_id', $dept->id)
            ->where('status', 'pending')
            ->with('products', 'createdBy')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Calculate estimated time for request
     */
    public function estimateCompletionTime(ProductionRequest $request): Carbon
    {
        $totalMinutes = 0;

        foreach ($request->products as $product) {
            // Product prep time × batch quantity
            $totalMinutes += ($product->prep_time_minutes ?? 30) * $request->batchQuantities[$product->id];
        }

        // Add buffer (20%)
        $totalMinutes *= 1.2;

        return now()->addMinutes($totalMinutes);
    }

    /**
     * Check current workload
     */
    public function getCurrentWorkload(Department $dept): float
    {
        $activeRequests = ProductionRequest::where('production_department_id', $dept->id)
            ->whereIn('status', ['pending', 'in_progress', 'quality_check'])
            ->count();

        $maxCapacity = $dept->production_capacity ?? 10;

        return ($activeRequests / $maxCapacity) * 100;
    }
}
```

### Step 2: Start Production
```php
class StartProductionAction
{
    public function execute(ProductionRequest $request, User $user)
    {
        // Validate
        if ($request->status !== 'pending') {
            throw new InvalidStatusException(
                "Cannot start. Request status is {$request->status}"
            );
        }

        if ($user->department_id !== $request->production_department_id) {
            throw new UnauthorizedException('Not in target department');
        }

        // Update request
        $request->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Create initial progress record
        $feedback = ProductionProgressFeedback::create([
            'production_request_id' => $request->id,
            'milestone' => 'started',
            'progress_percentage' => 0,
            'notes' => "Production started by {$user->name}",
            'updated_by_id' => $user->id,
        ]);

        // Broadcast to sales user
        broadcast(new ProgressUpdated($feedback))->toOthers();

        // Log action
        AuditLog::create([
            'action' => 'production_started',
            'auditable_type' => ProductionRequest::class,
            'auditable_id' => $request->id,
            'user_id' => $user->id,
            'details' => ['timestamp' => now()],
        ]);

        return true;
    }
}
```

### Step 3: Update Progress
```php
class UpdateProductionProgressAction
{
    public function execute(
        ProductionRequest $request,
        string $milestone,
        int $percentage,
        ?string $notes,
        User $user
    ) {
        // Validate milestone transition
        $validTransitions = $this->getValidTransitions($request->status);
        if (!in_array($milestone, $validTransitions)) {
            throw new InvalidMilestoneException(
                "Cannot transition to {$milestone} from {$request->status}"
            );
        }

        // Validate percentage
        if ($percentage < 0 || $percentage > 100) {
            throw new InvalidPercentageException('Percentage must be 0-100');
        }

        // Don't allow progress to go backwards
        $latestProgress = $request->progressFeedback->latest()->first();
        if ($latestProgress && $percentage < $latestProgress->progress_percentage) {
            throw new ProgressRegressionException(
                'Progress cannot decrease'
            );
        }

        // Create progress record
        $feedback = ProductionProgressFeedback::create([
            'production_request_id' => $request->id,
            'milestone' => $milestone,
            'progress_percentage' => $percentage,
            'notes' => $notes,
            'updated_by_id' => $user->id,
        ]);

        // Update request status based on milestone
        $request->update([
            'status' => match ($milestone) {
                'started' => 'in_progress',
                'in_production' => 'in_progress',
                'quality_check' => 'quality_check',
                'completed' => 'completed',
                default => $request->status,
            }
        ]);

        // Broadcast
        broadcast(new ProgressUpdated($feedback))->toOthers();

        return $feedback;
    }

    private function getValidTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'pending' => ['started'],
            'in_progress' => ['in_production', 'quality_check'],
            'quality_check' => ['completed'],
            'completed' => [],
            default => [],
        };
    }
}
```

### Step 4: Complete & Dispatch
```php
class DispatchProductsAction
{
    public function execute(ProductionRequest $request, array $dispatchData, User $user)
    {
        if ($request->status !== 'completed') {
            throw new InvalidStatusException(
                'Request must be completed before dispatch'
            );
        }

        // Validate dispatch quantities
        foreach ($dispatchData['products'] as $productData) {
            $produced = $productData['quantity_produced'];
            $requested = $request->products()
                ->where('product_id', $productData['product_id'])
                ->value('pivot.batch_quantity');

            if ($produced > $requested * 1.1) {
                // Allow 10% overage
                throw new QuantityMismatchException(
                    'Dispatched quantity exceeds requested by more than 10%'
                );
            }
        }

        // Create dispatch records
        foreach ($dispatchData['products'] as $productData) {
            ProductDispatch::create([
                'production_request_id' => $request->id,
                'product_id' => $productData['product_id'],
                'quantity_produced' => $productData['quantity_produced'],
                'quantity_dispatched' => $productData['quantity_dispatched'],
                'status' => 'pending_verification',
            ]);
        }

        // Update request status
        $request->update([
            'status' => 'dispatched',
        ]);

        // Broadcast to sales
        broadcast(new DispatchCreated($request))->toOthers();

        // Notify sales user
        Notification::send(
            $request->createdBy,
            new DispatchReadyNotification($request)
        );

        return true;
    }
}
```

---

## VERIFICATION WORKFLOW

### Sales Verifies Dispatch
```php
class VerifyDispatchAction
{
    public function execute(
        ProductionRequest $request,
        string $action, // 'accept' | 'reject'
        ?array $rejectData,
        User $user
    ) {
        if ($request->status !== 'dispatched') {
            throw new InvalidStatusException('Request not dispatched');
        }

        if ($user->id !== $request->created_by_id) {
            throw new UnauthorizedException('Only request creator can verify');
        }

        if ($action === 'accept') {
            return $this->acceptDispatch($request, $user);
        } elseif ($action === 'reject') {
            return $this->rejectDispatch($request, $rejectData, $user);
        }

        throw new InvalidActionException("Unknown action: {$action}");
    }

    private function acceptDispatch(ProductionRequest $request, User $user)
    {
        // Mark all dispatches as accepted
        $request->dispatches()->update(['status' => 'accepted']);

        // Update request
        $request->update([
            'status' => 'accepted',
        ]);

        // Create audit log
        AuditLog::create([
            'action' => 'dispatch_accepted',
            'auditable_type' => ProductionRequest::class,
            'auditable_id' => $request->id,
            'user_id' => $user->id,
            'details' => ['accepted_at' => now()],
        ]);

        // Broadcast to production
        broadcast(new DispatchAccepted($request))->toOthers();

        // Notify production
        Notification::send(
            User::where('department_id', $request->production_department_id)->get(),
            new DispatchAcceptedNotification($request)
        );

        return true;
    }

    private function rejectDispatch(
        ProductionRequest $request,
        array $rejectData,
        User $user
    ) {
        // Mark dispatches as rejected
        $request->dispatches()->update(['status' => 'rejected']);

        // Update request
        $request->update([
            'status' => 'rejected',
        ]);

        // Create rejection record
        ProductionRequestFeedback::create([
            'production_request_id' => $request->id,
            'type' => 'rejection',
            'reason' => $rejectData['reason'],
            'message' => $rejectData['notes'],
            'created_by_id' => $user->id,
        ]);

        // Create audit log
        AuditLog::create([
            'action' => 'dispatch_rejected',
            'auditable_type' => ProductionRequest::class,
            'auditable_id' => $request->id,
            'user_id' => $user->id,
            'details' => [
                'reason' => $rejectData['reason'],
                'rejected_at' => now(),
            ],
        ]);

        // Broadcast to production
        broadcast(new DispatchRejected($request, $rejectData))->toOthers();

        // Notify production with feedback
        Notification::send(
            User::where('department_id', $request->production_department_id)->get(),
            new DispatchRejectedNotification($request, $rejectData)
        );

        return true;
    }
}
```

---

## EDGE CASES & ERROR HANDLING

### Case 1: Concurrent Requests
```php
/**
 * Handle multiple requests for same product
 */
class HandleConcurrentRequestsLogic
{
    public function calculateAvailability(Product $product, Department $dept)
    {
        $pendingQuantity = ProductionRequest::where('production_department_id', $dept->id)
            ->where('status', '!=', 'cancelled')
            ->whereHas('products', fn($q) => $q->where('product_id', $product->id))
            ->sum('batch_quantity');

        $available = $product->max_batch_quantity - $pendingQuantity;

        return max(0, $available);
    }
}
```

### Case 2: Cancelled Requests
```php
class CancelProductionRequestAction
{
    public function execute(ProductionRequest $request, ?string $reason, User $user)
    {
        // Can only cancel if pending or in_progress
        if (!in_array($request->status, ['pending', 'in_progress'])) {
            throw new CannotCancelException(
                "Cannot cancel request in {$request->status} status"
            );
        }

        // Update status
        $request->update(['status' => 'cancelled']);

        // Create audit log
        AuditLog::create([
            'action' => 'request_cancelled',
            'auditable_type' => ProductionRequest::class,
            'auditable_id' => $request->id,
            'user_id' => $user->id,
            'details' => ['reason' => $reason, 'cancelled_at' => now()],
        ]);

        // Notify production dept
        broadcast(new RequestCancelled($request))->toOthers();

        return true;
    }
}
```

### Case 3: Quantity Mismatch
```php
class HandleQuantityMismatchLogic
{
    public function validateDispatchQuantities(
        ProductionRequest $request,
        array $dispatchData
    ): array {
        $issues = [];

        foreach ($dispatchData['products'] as $productData) {
            $requested = $this->getRequestedQuantity($request, $productData['product_id']);
            $produced = $productData['quantity_produced'];

            // Alert if significantly under-produced
            if ($produced < $requested * 0.8) {
                $issues[] = [
                    'type' => 'under_produced',
                    'product_id' => $productData['product_id'],
                    'requested' => $requested,
                    'produced' => $produced,
                    'shortfall' => $requested - $produced,
                ];
            }

            // Alert if over-produced
            if ($produced > $requested * 1.1) {
                $issues[] = [
                    'type' => 'over_produced',
                    'product_id' => $productData['product_id'],
                    'requested' => $requested,
                    'produced' => $produced,
                    'excess' => $produced - $requested,
                ];
            }
        }

        return $issues;
    }

    private function getRequestedQuantity(ProductionRequest $request, int $productId): int
    {
        return $request->products()
            ->where('product_id', $productId)
            ->value('pivot.batch_quantity') ?? 0;
    }
}
```

---

## PERFORMANCE QUERIES

### Optimized Request List Query
```php
$requests = ProductionRequest::query()
    ->where('production_department_id', $deptId)
    ->where('status', '!=', 'cancelled')
    ->with([
        'createdBy:id,name',
        'productionDepartment:id,name',
        'progressFeedback' => fn($q) => $q->latest()->limit(1),
    ])
    ->orderBy('priority', 'desc')
    ->orderBy('created_at', 'asc')
    ->paginate(20);
```

### Count Active Requests (Cached)
```php
class GetDepartmentWorkloadAction
{
    public function execute(Department $dept): int
    {
        return Cache::remember(
            "dept-workload-{$dept->id}",
            now()->addMinutes(5),
            fn() => ProductionRequest::where('production_department_id', $dept->id)
                ->whereIn('status', ['pending', 'in_progress', 'quality_check'])
                ->count()
        );
    }
}
```

---

## BUSINESS RULES SUMMARY

| Rule | Enforcement |
|------|-------------|
| Can only start from pending | Controller validation |
| Can only dispatch from completed | Status check |
| Can only accept/reject from dispatched | Status check |
| Only creator can reject | User ID validation |
| No progress backwards | Compare with latest |
| Cannot cancel accepted/rejected | Status whitelist |
| Max 10% quantity overage allowed | Dispatch validation |
| Progress max 100% | Integer validation |
