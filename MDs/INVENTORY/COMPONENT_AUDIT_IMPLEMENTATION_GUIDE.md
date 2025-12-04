# Component-by-Component Audit Implementation Guide

## Table of Contents
1. [Items.php](#itemsphp) - ✅ Reference Implementation
2. [Stocks.php](#stocksphp) - ✅ Recently Enhanced
3. [Purchases.php](#purchasesphp) - ⚠️ Needs Completion
4. [HealthChecks.php](#healthchecksphp) - 🔴 Needs Implementation
5. [StockTakes.php](#stocktakesphp) - 🔴 Needs Implementation
6. [ItemRequests.php](#itemrequestsphp) - 🔴 Needs Implementation

---

## Items.php
### ✅ REFERENCE IMPLEMENTATION - Complete

**Current Status**: Fully implemented with approval workflow

### Implementation Details

#### Component Properties
```php
// Audit Modal State
public ?string $auditAction = null;           // 'create_item', 'update_item', 'delete_item'
public string $auditReason = '';              // User's reason for action
public ?int $pendingItemId = null;            // Item awaiting approval
public array $pendingItemData = [];           // Data to apply after approval
```

#### Flow Diagram
```
User Action
    ↓
Form Validation
    ↓
is_super_admin()?
    ├─ YES → executeImmediateSave()
    │         ↓
    │       Update Database
    │         ↓
    │       AuditService::log() (completed)
    │
    └─ NO → Store in pendingItemData
             ↓
           Show auditModal
             ↓
           User enters reason
             ↓
           InventoryApprovalService::requestItemCreation/Update/Deletion()
             ↓
           AuditService::log() (pending)
             ↓
           Awaits Approval
```

#### Key Code Patterns

**1. Save Method with Conditional Logic**
```php
public function save()
{
    $this->validate($this->itemValidationRules());
    
    $data = [
        'branch_id' => $this->getBranchId(),
        'name' => $this->name,
        'sku' => $this->sku,
        // ... all fields
    ];
    
    if (is_super_admin()) {
        $this->executeImmediateSave($data);  // Direct update
        return;
    }
    
    // Non-super-admin: Require approval
    $this->pendingItemData = $data;
    $this->auditAction = $this->isEditing ? 'update_item' : 'create_item';
    $this->auditReason = '';
    $this->showAuditModal = true;
}
```

**2. Audit Request Submission**
```php
public function submitAuditRequest()
{
    $this->validate(['auditReason' => 'required|string|min:10|max:500']);
    
    try {
        $user = Auth::guard('employees')->user();
        
        if ($this->auditAction === 'create_item') {
            $request = InventoryApprovalService::requestItemCreation(
                $user,
                $this->pendingItemData,
                $this->auditReason
            );
            // Success message...
        }
        // Other actions...
    } catch (\Exception $e) {
        // Error handling...
    }
}
```

**3. Audit Logging with Change Tracking**
```php
$changes = [];
if ($oldName !== $this->name) {
    $changes[] = "Name: {$oldName} → {$this->name}";
}
// Track all field changes...

AuditService::log(
    current_actor(),
    'update',
    $item,
    "Updated item '{$item->name}'. Changes: " . implode(', ', $changes),
    'completed'
);
```

#### Blade Template Pattern
- **Modal**: `item-modal.blade.php` - Form inputs
- **Audit Modal**: `audit-modal.blade.php` - Reason capture
- **Features**:
  - Real-time SKU generation
  - Form validation feedback
  - Loading states

#### Service Integration
```php
// Services/InventoryApprovalService.php
requestItemCreation($user, $itemData, $reason)
requestItemUpdate($user, $itemId, $changes, $reason)
requestItemDeletion($user, $itemId, $reason)
```

---

## Stocks.php
### ✅ RECENTLY ENHANCED - Production Ready

**Current Status**: Full approval workflow with all fields

### Implementation Details

#### Recent Enhancements (Latest)
```php
// NEW: Reorder and Max Stock Level Fields
public $reorder_level = 0;
public $max_stock_level = 0;

// Enhanced Data sent to approval
'reorder_level' => (float) ($this->reorder_level ?? 0),
'max_stock_level' => (float) ($this->max_stock_level ?? 0),

// Item-level updates on approval execution
$stock->item->update([
    'reorder_level' => (float) ($payload['reorder_level'] ?? ...),
    'max_stock_level' => (float) ($payload['max_stock_level'] ?? ...),
]);
```

#### Component Properties (Complete)
```php
// Stock Fields
public $quantity_available = 0;
public $quantity_reserved = 0;
public $quantity_damaged = 0;
public $average_cost = 0;
public $health_status = 'good';
public $expiry_date = '';
public $notes = '';
public $reorder_level = 0;           // NEW
public $max_stock_level = 0;         // NEW

// Modal States
public $showEditModal = false;
public $showAuditModal = false;
public $auditAction = null;
public $pendingStockId = null;
```

#### Flow Diagram
```
openEditModal($stockId)
    ↓
Load Stock Data + Item Reorder/Max Levels
    ↓
Show Edit Modal
    ↓
User modifies 9 fields
    ↓
saveStock() validation
    ↓
is_super_admin()?
    ├─ YES → performStockUpdate()
    │         ↓
    │       Update Stock + Item
    │         ↓
    │       AuditService::log() (completed)
    │
    └─ NO → Store in pendingStockData
             ↓
           Close Edit Modal
             ↓
           Show Audit Modal
             ↓
           User enters reason
             ↓
           proceedWithStockAdjustment()
```

#### Payload Structure (Complete)
```php
'payload' => [
    'stock_item_id' => $stockId,
    'quantity_available' => (float) $adjustments['quantity_available'],
    'quantity_reserved' => (float) $adjustments['quantity_reserved'],
    'quantity_damaged' => (float) $adjustments['quantity_damaged'],
    'average_cost' => (float) $adjustments['average_cost'],
    'health_status' => $adjustments['health_status'],
    'expiry_date' => $adjustments['expiry_date'],
    'reorder_level' => (float) $adjustments['reorder_level'],     // NEW
    'max_stock_level' => (float) $adjustments['max_stock_level'],  // NEW
    'notes' => $adjustments['notes'],
]
```

#### UI Enhancements
- **Loading States**: Alpine.js with 100ms delay
- **Dynamic Cards**: Real-time updates as you type
- **Field Spinners**: Loading indicator in each input
- **Form Locking**: Disabled during load

---

## Purchases.php
### ⚠️ INCOMPLETE - Needs Phase 1 Fixes

**Current Status**: Approval request structure exists but execution is stub

### Issues to Fix

#### 1. Incomplete Execution Logic
**File**: `Services/InventoryApprovalService.php` lines 344-356

```php
public static function executePurchaseCreation(ApprovalAuditRequest $request, Employee $approver)
{
    // ❌ STUB - Only marks as approved, doesn't create purchase!
    $request->update([
        'approver_id' => $approver->id,
        'approver_type' => Employee::class,
        'status' => 'approved',
        'approved_at' => now(),
    ]);
    
    return $request;
}
```

#### 2. What Needs to be Implemented

```php
public static function executePurchaseCreation(ApprovalAuditRequest $request, Employee $approver)
{
    return DB::transaction(function () use ($request, $approver) {
        $payload = $request->payload;
        $purchase_date = $payload['purchase_date'] ?? now();
        
        // 1. Create Purchase Record
        $purchase = Purchase::create([
            'branch_id' => $request->branch_id,
            'purchase_date' => $purchase_date,
            'supplier_name' => $payload['supplier_name'],
            'supplier_contact' => $payload['supplier_contact'],
            'currency' => $payload['currency'] ?? 'NGN',
            'exchange_rate' => $payload['exchange_rate'] ?? 1,
            'other_costs' => $payload['other_costs'] ?? 0,
            'payment_status' => $payload['payment_status'] ?? 'pending',
            'notes' => $payload['notes'] ?? '',
            'created_by_id' => $request->requester_id,
        ]);
        
        // 2. Create Purchase Items
        foreach ($payload['items'] ?? [] as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'item_id' => $item['item_id'],
                'quantity' => $item['quantity'],
                'unit_fob_fc' => $item['unit_fob_fc'],
                'uom' => $item['uom'],
            ]);
            
            // 3. Update Stock (add to quantity_available)
            $stock = Stock::where('item_id', $item['item_id'])
                ->where('branch_id', $request->branch_id)
                ->first();
                
            if ($stock) {
                $stock->increment('quantity_available', $item['quantity']);
            }
        }
        
        // 4. Mark Approval as Complete
        $request->update([
            'approver_id' => $approver->id,
            'approver_type' => Employee::class,
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        
        // 5. Audit Log
        AuditService::log(
            $approver,
            'approve',
            $purchase,
            "Approved purchase from {$payload['supplier_name']}. Items: " . 
            count($payload['items'] ?? []),
            'completed'
        );
        
        return $purchase;
    });
}
```

#### 3. Component Audit State (Currently)
```php
// In Purchases.php component
public $showAuditModal = false;
public $auditReason = '';
public $auditAction = null;
public $pendingPurchaseData = [];
public $pendingItemId = null;

// ISSUE: Variables defined but flow may be incomplete
```

#### 4. What's Missing in save() Method

```php
public function save()
{
    // Current: Likely saves directly
    // NEED: Conditional logic like Items.php
    
    if (is_super_admin()) {
        // Direct creation
        $this->createPurchase($data);
    } else {
        // Store pending and show audit modal
        $this->pendingPurchaseData = $data;
        $this->auditAction = 'create_purchase';
        $this->showAuditModal = true;
    }
}
```

### Implementation Checklist for Purchases.php

- [ ] **Step 1**: Implement conditional save() logic (admin vs non-admin)
- [ ] **Step 2**: Implement submitAuditRequest() to call InventoryApprovalService
- [ ] **Step 3**: Complete executePurchaseCreation() in service
- [ ] **Step 4**: Add delete approval workflow
- [ ] **Step 5**: Test full purchase creation → approval → execution
- [ ] **Step 6**: Verify stock updates after approval

---

## HealthChecks.php
### 🔴 NEEDS IMPLEMENTATION - Approval for Critical Items

**Current Status**: Audit logging only

### Recommended Implementation

#### Threshold-Based Approval
```php
public function save()
{
    $this->validate($this->rules);
    
    // Critical condition requires approval
    $criticalConditions = ['critical', 'expired', 'damaged'];
    
    if (in_array($this->condition, $criticalConditions) && !is_super_admin()) {
        // Require approval
        $this->pendingCheckData = [
            'stock_id' => $this->stock_id,
            'check_date' => $this->check_date,
            'condition' => $this->condition,
            'quantity_affected' => $this->quantity_affected,
            'observations' => $this->observations,
            'action_taken' => $this->action_taken,
        ];
        $this->auditAction = 'critical_health_check';
        $this->showAuditModal = true;
        return;
    }
    
    // Good/Fair conditions: Direct save + audit log
    $this->performHealthCheck();
}
```

#### Component Properties to Add
```php
public $showAuditModal = false;
public $auditReason = '';
public $auditAction = null;
public array $pendingCheckData = [];

protected $bulkActions = [
    'quarantine' => ['label' => 'Quarantine Items', 'method' => 'quarantineSelected'],
];
```

#### Audit Service Integration
```php
// Option 1: Simple approval
InventoryApprovalService::requestHealthCheckReview($user, $checkData, $reason);

// Option 2: Auto-approve good/fair
if ($this->condition === 'good' || $this->condition === 'fair') {
    $this->performHealthCheck();
    AuditService::log(..., 'completed');
} else {
    // Critical: require approval
}
```

---

## StockTakes.php
### 🔴 NEEDS IMPLEMENTATION - Threshold-Based Approval

**Current Status**: Audit logging only, no approval workflow

### Recommended Implementation

#### Variance Threshold Approval
```php
public function submitStockTake()
{
    $this->validate($this->rules);
    
    // Calculate variance for each item
    $requiresApproval = false;
    $varianceData = [];
    
    foreach ($this->takeItems as $takeItem) {
        $currentStock = Stock::find($takeItem['stock_id'])?->quantity_available ?? 0;
        $variance = abs($currentStock - $takeItem['counted_quantity']) / max($currentStock, 1) * 100;
        
        // If variance > 10%, requires approval
        if ($variance > 10) {
            $requiresApproval = true;
            $varianceData[] = [
                'stock_id' => $takeItem['stock_id'],
                'current' => $currentStock,
                'counted' => $takeItem['counted_quantity'],
                'variance_percent' => $variance,
            ];
        }
    }
    
    if (!is_super_admin() && $requiresApproval) {
        // Store for approval
        $this->pendingTakeData = [
            'take_date' => $this->take_date,
            'items' => $this->takeItems,
            'variance_data' => $varianceData,
        ];
        $this->auditAction = 'stock_take_variance';
        $this->showAuditModal = true;
        return;
    }
    
    // Direct submission if small variance or super-admin
    $this->executeTake();
}
```

#### Component Additions
```php
public $showAuditModal = false;
public $auditReason = '';
public array $pendingTakeData = [];
public const VARIANCE_THRESHOLD = 10; // percent
```

---

## ItemRequests.php
### 🔴 NEEDS IMPLEMENTATION - Full Approval Workflow

**Current Status**: Audit logging only, pure request system

### Recommended Implementation

This should be a multi-stage workflow:

#### Stage 1: Request Creation (Employee)
```php
public function submitRequest()
{
    $this->validate($this->rules);
    
    // Store request (creates ItemRequest record)
    $itemRequest = ItemRequest::create([
        'branch_id' => $this->getBranchId(),
        'requested_by_id' => Auth::guard('employees')->id(),
        'department_id' => $this->department_id,
        'request_date' => $this->request_date,
        'status' => 'pending',
        'notes' => $this->notes,
    ]);
    
    // Create detail records
    foreach ($this->requestItems as $item) {
        ItemRequestDetail::create([
            'item_request_id' => $itemRequest->id,
            'item_id' => $item['item_id'],
            'quantity_requested' => $item['quantity_requested'],
            'quantity_approved' => null,
            'status' => 'pending',
        ]);
    }
    
    AuditService::log(..., 'pending_approval');
}
```

#### Stage 2: Manager Approval
```php
public function approveRequest($requestId, $approvalData)
{
    $request = ItemRequest::findOrFail($requestId);
    
    // Update detail quantities
    foreach ($approvalData['items'] as $itemId => $approved_qty) {
        ItemRequestDetail::where('item_request_id', $requestId)
            ->where('item_id', $itemId)
            ->update(['quantity_approved' => $approved_qty]);
    }
    
    $request->update(['status' => 'approved', 'approved_by_id' => Auth::id()]);
    
    AuditService::log(..., 'completed');
}
```

#### Stage 3: Fulfillment (Inventory)
```
Request Status Workflow:
pending → approved → fulfilled → completed
OR
pending → rejected → rejected
```

---

## Service Layer Pattern Summary

### For Each Component Needing Approval:

1. **Create Request Method**
   ```php
   public static function request[Action](Employee $requester, array $data, string $reason): ApprovalAuditRequest
   ```

2. **Execute Method**
   ```php
   public static function execute[Action](ApprovalAuditRequest $request): Model
   ```

3. **Reject Method**
   ```php
   public static function reject[Action](ApprovalAuditRequest $request, Employee $approver, string $comment): void
   ```

### Payload Structure Requirements
- Always include complete dataset needed for execution
- Include IDs and references
- Include timestamps and metadata
- Allow null coalescing in execution

---

## Testing Strategy

### For Each Component Implementation

**Test Case Template:**
```gherkin
Scenario: Non-admin user requests action
  Given I am a non-admin employee
  When I submit [action]
  Then approval modal appears
  And I enter reason
  And approval request is created
  And status is 'pending'

Scenario: Super-admin performs action
  Given I am a super-admin
  When I submit [action]
  Then changes apply immediately
  And audit log shows 'completed'
```

---

## Implementation Priority

**Week 1-2**: Fix Purchases.php (financial impact)
**Week 2-3**: Implement HealthChecks & StockTakes approval
**Week 3-4**: ItemRequests workflow
**Week 4+**: Enhancements and SLA tracking

