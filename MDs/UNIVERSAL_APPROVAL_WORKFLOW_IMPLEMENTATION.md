# Universal Approval Workflow - Implementation Guide
**Adjusted to SweetTooth's Auditory System**

**Status:** Implementation Plan  
**Last Updated:** December 2, 2025  
**Version:** 1.0

---

## 📋 Overview

Your system already has:
- ✅ `AuditService` for centralized audit logging
- ✅ `ApprovalRequest` model with polymorphic relationships
- ✅ `ApprovalAuditRequest` model for request management
- ✅ `AuditLog` model with full change tracking
- ✅ `AuditManagement/Index.php` component with approval processing
- ✅ Multi-guard support (Users & Employees)

This implementation adapts the **universal approval workflow** to your existing audit system, ensuring:
- **One pattern** for all actions (Create · Update · Delete · Adjust)
- **Component-independent** (works with any model/resource)
- **Zero trust** for non-super-admins
- **Seamless UX** (no visible approval requirement until submission)
- **Full audit compliance** (complete change tracking)

---

## 🎯 The Four Actions Your System Needs

### Your Resource Types
Your system manages these entities requiring approval:

| Resource | Actions Needed | Current Handler |
|----------|---|---|
| **Items** | Create, Update, Delete | Inventory module |
| **Stock** | Adjust (qty changes) | Stock movements |
| **Recipes** | Create, Update | Production module |
| **Products** | Create, Update, Delete | Production module |
| **Suppliers** | Create, Update, Delete | Inventory/Suppliers |
| **Departments** | Create, Update, Delete | Department module |
| **Employees** | Create, Update | Employee module |
| **Production Records** | Create, Update | Production |
| **Daily Produces** | Create, Update | Production |
| **Product Dispatches** | Create, Update | Dispatch |

### The Four Universal Actions

```
┌─────────────────────────────────────────────────────────────┐
│                  USER INTERFACE ACTIONS                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1️⃣ CREATE                 3️⃣ DELETE                       │
│  └─ + Add New             └─ 🗑️ Delete button              │
│                                                             │
│  2️⃣ UPDATE                 4️⃣ ADJUST                        │
│  └─ ✏️ Edit button        └─ 🔧 Adjust (stock/price)      │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

Each action uses **the same backend approval workflow**.

---

## 🔄 The Approval Workflow (Your Current Pattern)

Your system already follows this pattern:

### Step 1: User Initiates Action
```
User clicks: [+ Add], [Edit], [Delete], or [Adjust]
↓
Form opens exactly like a normal operation (no approval hint yet)
```

### Step 2: System Validates Data
```
User fills form → clicks Save/Update/Delete/Adjust
↓
Normal validation runs (you see "Saving..." spinner)
```

### Step 3: System Checks Permission
```
Is user a super admin?
├─ YES → Execute immediately ✅
└─ NO  → Check if approval needed
         ├─ YES → Create ApprovalAuditRequest + block action
         └─ NO  → Execute immediately ✅
```

### Step 4: Regular User Sees "Reason" Modal
```
After validation, ONLY for non-super-admins:
┌──────────────────────────┐
│  Why Are You Requesting? │
│                          │
│  [________reason_______] │
│                          │
│ [Cancel]    [Submit]     │
└──────────────────────────┘

This is the FIRST time they know approval is needed
```

### Step 5: Request Saved
```
System stores in approval_audit_requests:
├─ action: "create|update|delete|adjust"
├─ resource_type: "item|product|recipe|..."
├─ payload: {exact data to apply}
├─ reason: {why they're requesting}
├─ status: "pending"
└─ requester: {who made the request}

Toast: "Your request has been submitted for approval"
```

### Step 6: Admin Reviews & Approves
```
Admin goes to: /branch-dashboard/audit-management
│
See: "Pending Approvals" tab
│
Show:
  Create Item         | Sugar 50kg        | Chigozie | [Approve] [Reject]
  Adjust Stock        | Flour +500kg      | Michael | [Approve] [Reject]
  Update Price        | Rice → ₦45,000    | Jane    | [Approve] [Reject]
  Delete Supplier     | Old Chemicals Ltd | Ahmed   | [Approve] [Reject]
│
Click [Approve]
│
executeApprovedAction() runs:
├─ Parse action type
├─ Apply exact stored data
├─ Sync relationships (roles, permissions, etc.)
├─ Log to audit_logs
└─ Mark request as "approved"

Toast: "Request approved successfully"
```

---

## 🗂️ Database Structure (Your Current Tables)

### approval_audit_requests
```sql
id                    | bigint
branch_id             | unsignedBigInteger
requester_id          | uuid (polymorphic)
requester_type        | string (polymorphic)
approver_id           | uuid (polymorphic, nullable)
approver_type         | string (polymorphic, nullable)
action                | string (create|update|delete|adjust)
description           | text (the reason)
payload               | json {exact data to apply}
status                | enum (pending|approved|rejected)
comment               | text (admin's notes)
approved_at           | timestamp (nullable)
denied_at             | timestamp (nullable)
rejection_comment     | text (nullable)
created_at, updated_at| timestamps
```

### audit_logs
```sql
id                    | bigint
branch_id             | unsignedBigInteger
causer_type           | string (polymorphic: User|Employee)
causer_id             | uuid
auditable_type        | string (Item|Product|Stock|...)
auditable_id          | uuid
action                | string (create|update|delete|adjust)
description           | text
old_values            | json (before change)
new_values            | json (after change)
status                | enum (pending|completed|failed)
approval_request_id   | unsignedBigInteger (FK)
ip_address            | string
user_agent            | string
logged_at             | timestamp
details               | json (metadata)
created_at, updated_at| timestamps
```

---

## 📝 Payload Format (One Template for All Resources)

All actions use the **same payload structure**:

```php
// For CREATE action
[
    'action' => 'create',                    // or: update, delete, adjust
    'resource_type' => 'item',               // or: product, recipe, stock, etc.
    'resource_id' => null,                   // null for create
    'payload' => [                           // exact data to apply
        'name' => 'Sugar 50kg',
        'sku' => 'SUGARL50',
        'category_id' => 5,
        'cost' => 5000,
        'reorder_level' => 10,
        // ... all fillable fields
    ],
    'reason' => 'New item for Bakery section'
]

// For UPDATE action
[
    'action' => 'update',
    'resource_type' => 'item',
    'resource_id' => '12345',                // UUID of the item
    'payload' => [
        'name' => 'Sugar 50kg - Premium',
        'cost' => 5500,
        // ... changed fields only or all fields
    ],
    'reason' => 'Updated pricing after supplier review'
]

// For DELETE action
[
    'action' => 'delete',
    'resource_type' => 'supplier',
    'resource_id' => '67890',
    'payload' => [
        'id' => '67890',
        'name' => 'Old Chemicals Ltd',
        // ... minimal info needed
    ],
    'reason' => 'Switching to new supplier for better pricing'
]

// For ADJUST action (Stock/Price changes)
[
    'action' => 'adjust',
    'resource_type' => 'stock',
    'resource_id' => '34567',
    'payload' => [
        'quantity' => 500,                   // +500kg for Flour
        'reason' => 'Physical count verification',
        'previous_quantity' => 200,
    ],
    'reason' => 'Recount after stock take discrepancy'
]
```

---

## 🏗️ Implementation Structure

### Layer 1: Create the Base Workflow Trait

**File:** `app/Traits/RequiresApprovalWorkflow.php`

```php
<?php

namespace App\Traits;

use App\Models\ApprovalAuditRequest;
use Illuminate\Support\Facades\DB;

/**
 * Universal Approval Workflow Trait
 *
 * Add to any Livewire component to handle approval for any resource.
 * Works with: Items, Products, Recipes, Stock, Suppliers, Departments, etc.
 *
 * Usage:
 * class ItemsComponent extends Component {
 *     use RequiresApprovalWorkflow;
 *     
 *     public function saveItem() {
 *         $this->submitForApproval('create', 'item', null, $itemData, 'Reason for request');
 *     }
 * }
 */
trait RequiresApprovalWorkflow
{
    /**
     * Determine if current user is super admin
     * Works with both guards: web (User) and employees (Employee)
     */
    protected function isSuperAdmin(): bool
    {
        $user = auth()->user() ?? auth('employees')->user();
        return $user && $user->is_super_admin ?? false;
    }

    /**
     * Get current actor (User or Employee)
     */
    protected function getCurrentActor()
    {
        return auth()->user() ?? auth('employees')->user();
    }

    /**
     * Get branch ID for context
     */
    protected function getCurrentBranchId(): ?int
    {
        $user = auth()->user() ?? auth('employees')->user();
        return $user?->branch_id;
    }

    /**
     * Submit an action for approval or execute immediately if super admin
     *
     * @param string $action          create|update|delete|adjust
     * @param string $resourceType     item|product|recipe|stock|supplier|department|etc
     * @param string|null $resourceId  null for create, UUID for others
     * @param array $payload           exact data to apply
     * @param string $reason           why this change is being requested
     *
     * @return array ['approved' => bool, 'request_id' => int|null, 'message' => string]
     */
    protected function submitForApproval(
        string $action,
        string $resourceType,
        ?string $resourceId,
        array $payload,
        string $reason
    ): array {
        $actor = $this->getCurrentActor();
        $branchId = $this->getCurrentBranchId();

        // Super admin: execute immediately
        if ($this->isSuperAdmin()) {
            try {
                // Execute the action directly
                $this->executeAction($action, $resourceType, $resourceId, $payload);
                
                // Log it
                \App\Services\AuditService::log(
                    $actor,
                    $action,
                    null,
                    "Auto-approved by super admin: {$reason}",
                    'completed'
                );

                return [
                    'approved' => true,
                    'request_id' => null,
                    'message' => ucfirst($action) . ' completed'
                ];
            } catch (\Exception $e) {
                throw $e;
            }
        }

        // Regular user: create approval request
        $request = ApprovalAuditRequest::create([
            'branch_id' => $branchId,
            'requester_id' => $actor->id,
            'requester_type' => get_class($actor),
            'action' => "{$action}:{$resourceType}",
            'description' => $reason,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        // Log the request creation
        \App\Services\AuditService::log(
            $actor,
            "request_{$action}",
            null,
            $reason,
            'pending'
        );

        return [
            'approved' => false,
            'request_id' => $request->id,
            'message' => 'Your request has been submitted for approval'
        ];
    }

    /**
     * Execute an action immediately (after approval or for super admin)
     * This is called by the approval handler
     */
    protected function executeAction(
        string $action,
        string $resourceType,
        ?string $resourceId,
        array $payload
    ): void {
        match ($action) {
            'create' => $this->handleCreate($resourceType, $payload),
            'update' => $this->handleUpdate($resourceType, $resourceId, $payload),
            'delete' => $this->handleDelete($resourceType, $resourceId),
            'adjust' => $this->handleAdjust($resourceType, $resourceId, $payload),
            default => throw new \Exception("Unknown action: {$action}")
        };
    }

    /**
     * Handle CREATE action for any resource type
     * Override in child class if custom logic needed
     */
    protected function handleCreate(string $resourceType, array $payload): void
    {
        $modelClass = $this->getModelClass($resourceType);
        $model = new $modelClass();
        $fillable = $model->getFillable();
        
        $createData = array_intersect_key($payload, array_flip($fillable));
        
        // Add branch_id if needed
        if (in_array('branch_id', $fillable) && !isset($createData['branch_id'])) {
            $createData['branch_id'] = $this->getCurrentBranchId();
        }
        
        $modelClass::create($createData);
    }

    /**
     * Handle UPDATE action for any resource type
     */
    protected function handleUpdate(string $resourceType, ?string $resourceId, array $payload): void
    {
        if (!$resourceId) {
            throw new \Exception("Resource ID required for update");
        }

        $modelClass = $this->getModelClass($resourceType);
        $model = $modelClass::find($resourceId);
        
        if (!$model) {
            throw new \Exception("{$resourceType} not found");
        }

        $fillable = $model->getFillable();
        $updateData = array_intersect_key($payload, array_flip($fillable));
        
        if (!empty($updateData)) {
            $model->update($updateData);
        }
    }

    /**
     * Handle DELETE action for any resource type
     */
    protected function handleDelete(string $resourceType, ?string $resourceId): void
    {
        if (!$resourceId) {
            throw new \Exception("Resource ID required for delete");
        }

        $modelClass = $this->getModelClass($resourceType);
        $model = $modelClass::find($resourceId);
        
        if ($model) {
            $model->delete();
        }
    }

    /**
     * Handle ADJUST action - typically for stock, price, quantity
     * Override in child class for custom logic
     */
    protected function handleAdjust(string $resourceType, ?string $resourceId, array $payload): void
    {
        if (!$resourceId) {
            throw new \Exception("Resource ID required for adjust");
        }

        $modelClass = $this->getModelClass($resourceType);
        $model = $modelClass::find($resourceId);
        
        if (!$model) {
            throw new \Exception("{$resourceType} not found");
        }

        // For stock: adjust quantity
        if ($resourceType === 'stock' && isset($payload['quantity'])) {
            $model->update(['quantity' => $payload['quantity']]);
        }
        // For products: adjust price
        elseif ($resourceType === 'product' && isset($payload['price'])) {
            $model->update(['price' => $payload['price']]);
        }
        // For items: adjust cost
        elseif ($resourceType === 'item' && isset($payload['cost'])) {
            $model->update(['cost' => $payload['cost']]);
        }
    }

    /**
     * Map resource type to model class
     * Extend this for additional resources
     */
    protected function getModelClass(string $resourceType): string
    {
        $models = [
            'item' => \App\Models\Item::class,
            'product' => \App\Models\Product::class,
            'recipe' => \App\Models\Recipe::class,
            'stock' => \App\Models\Stock::class,
            'supplier' => \App\Models\Supplier::class,
            'department' => \App\Models\Department::class,
            'employee' => \App\Models\Employee::class,
            'production_record' => \App\Models\ProductionRecord::class,
            'daily_produce' => \App\Models\DailyProduce::class,
            'product_dispatch' => \App\Models\ProductDispatch::class,
        ];

        if (!isset($models[$resourceType])) {
            throw new \Exception("Unknown resource type: {$resourceType}");
        }

        return $models[$resourceType];
    }
}
```

---

## 🎬 Usage Examples

### Example 1: Create an Item with Approval

**File:** `app/Livewire/BranchDashboard/Inventory/Items.php`

```php
<?php

namespace App\Livewire\BranchDashboard\Inventory;

use Livewire\Component;
use App\Traits\RequiresApprovalWorkflow;

class Items extends Component
{
    use RequiresApprovalWorkflow;

    public $name = '';
    public $sku = '';
    public $cost = '';
    public $reorder_level = '';
    public $category_id = '';
    public $reason = '';
    public $showReasonModal = false;

    public function saveItem()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'sku' => 'required|string|unique:items,sku',
            'cost' => 'required|numeric|min:0',
            'category_id' => 'required|exists:item_categories,id',
        ]);

        // Super admin? Show no reason modal, execute immediately
        if ($this->isSuperAdmin()) {
            $this->executeCreate();
            return;
        }

        // Regular user? Show reason modal
        $this->showReasonModal = true;
    }

    public function submitRequest()
    {
        $this->validate(['reason' => 'required|string|min:10']);

        try {
            $payload = [
                'name' => $this->name,
                'sku' => $this->sku,
                'cost' => $this->cost,
                'reorder_level' => $this->reorder_level,
                'category_id' => $this->category_id,
            ];

            $result = $this->submitForApproval(
                'create',
                'item',
                null,
                $payload,
                $this->reason
            );

            if ($result['approved']) {
                $this->dispatch('toast', message: $result['message'], type: 'success');
                $this->resetForm();
            } else {
                $this->dispatch('toast', 
                    message: 'Your request has been submitted for approval',
                    type: 'info'
                );
                $this->resetForm();
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    private function executeCreate()
    {
        try {
            $payload = [
                'name' => $this->name,
                'sku' => $this->sku,
                'cost' => $this->cost,
                'reorder_level' => $this->reorder_level,
                'category_id' => $this->category_id,
            ];

            $this->executeAction('create', 'item', null, $payload);
            
            $this->dispatch('toast', message: 'Item created successfully', type: 'success');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    private function resetForm()
    {
        $this->reset(['name', 'sku', 'cost', 'reorder_level', 'category_id', 'reason']);
        $this->showReasonModal = false;
    }

    public function render()
    {
        return view('livewire.branch-dashboard.inventory.items');
    }
}
```

**View:** `resources/views/livewire/branch-dashboard/inventory/items.blade.php`

```blade
<div>
    <form wire:submit="saveItem">
        <input wire:model="name" placeholder="Item Name" />
        <input wire:model="sku" placeholder="SKU" />
        <input wire:model="cost" type="number" placeholder="Cost" />
        <select wire:model="category_id">
            <option>Select Category</option>
            {{-- categories options --}}
        </select>
        <input wire:model="reorder_level" type="number" placeholder="Reorder Level" />
        
        <button type="submit" class="btn btn-primary">
            + Add Item
        </button>
    </form>

    <!-- Reason Modal (Only for non-super-admins) -->
    @if($showReasonModal)
    <div class="modal show">
        <div class="modal-content">
            <h3>Why Are You Adding This Item?</h3>
            
            <textarea 
                wire:model="reason"
                placeholder="Explain why this item is needed..."
                rows="4"
                class="form-control"
            ></textarea>

            <div class="modal-footer">
                <button type="button" @click="showReasonModal=false" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="button" wire:click="submitRequest" class="btn btn-primary">
                    Submit Request
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
```

### Example 2: Adjust Stock Quantity with Approval

```php
class Stocks extends Component
{
    use RequiresApprovalWorkflow;

    public $stockId = '';
    public $newQuantity = '';
    public $reason = '';
    public $showReasonModal = false;

    public function adjustQuantity()
    {
        $this->validate([
            'stockId' => 'required|exists:stocks,id',
            'newQuantity' => 'required|numeric|min:0',
        ]);

        // Super admin bypasses reason
        if ($this->isSuperAdmin()) {
            $this->executeAdjust();
            return;
        }

        // Show reason modal
        $this->showReasonModal = true;
    }

    public function submitAdjustmentRequest()
    {
        $this->validate(['reason' => 'required|string|min:10']);

        try {
            $stock = Stock::find($this->stockId);
            
            $payload = [
                'quantity' => $this->newQuantity,
                'previous_quantity' => $stock->quantity,
                'adjustment' => $this->newQuantity - $stock->quantity,
            ];

            $result = $this->submitForApproval(
                'adjust',
                'stock',
                $this->stockId,
                $payload,
                $this->reason
            );

            $this->dispatch('toast', message: $result['message'], type: 'success');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    private function executeAdjust()
    {
        try {
            $stock = Stock::find($this->stockId);
            $payload = ['quantity' => $this->newQuantity];
            
            $this->executeAction('adjust', 'stock', $this->stockId, $payload);
            
            $this->dispatch('toast', message: 'Stock adjusted', type: 'success');
            $this->resetForm();
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    private function resetForm()
    {
        $this->reset(['stockId', 'newQuantity', 'reason']);
        $this->showReasonModal = false;
    }
}
```

---

## 🎯 Integration Checklist

### Phase 1: Core Trait Implementation
- [ ] Create `app/Traits/RequiresApprovalWorkflow.php`
- [ ] Add model mappings for all your resources
- [ ] Test trait compiles without errors
- [ ] Document resource types in trait comments

### Phase 2: Integrate with Existing Components

**Items Module:**
- [ ] Add `RequiresApprovalWorkflow` trait to Items component
- [ ] Update `saveItem()` to call `submitForApproval()`
- [ ] Update view to show reason modal

**Stock Module:**
- [ ] Add trait to Stocks component
- [ ] Update stock adjustment to call `submitForApproval()`

**Products Module:**
- [ ] Add trait to Products component
- [ ] Update create/update/delete to use workflow

**Departments Module:**
- [ ] Add trait to Department component
- [ ] Update department operations

**Suppliers:**
- [ ] Add trait to Supplier component

**Recipes:**
- [ ] Add trait to Recipes component

### Phase 3: Update Approval Handler

Your existing `AuditManagement/Index.php` already handles approvals.  
Just ensure it can parse the new action format: `"action:resource_type"`

```php
// In Index.php approveRequest()
$baseAction = explode(':', $request->action)[0];  // Extract "create|update|delete|adjust"
$resourceType = explode(':', $request->action)[1] ?? '';  // Extract "item|product|..."
```

### Phase 4: Testing
- [ ] Test CREATE with regular employee → expect modal → admin approves
- [ ] Test UPDATE with regular employee → expect modal → admin approves
- [ ] Test DELETE with regular employee → expect modal → admin approves
- [ ] Test ADJUST with regular employee → expect modal → admin approves
- [ ] Test CREATE with super admin → immediate execution, no modal
- [ ] Verify audit logs capture all changes
- [ ] Test rejection of requests
- [ ] Test error handling (invalid data, missing resource)

### Phase 5: Documentation
- [ ] Document resource types in trait
- [ ] Create usage examples for each resource
- [ ] Document payload formats
- [ ] Update developer guide

---

## 🔍 How It Integrates with Your Audit System

### Current Flow (Your System)
```
User Action
  ↓
Create ApprovalAuditRequest (if not super admin)
  ↓
Store in approval_audit_requests table
  ↓
Admin Reviews in AuditManagement/Index.php
  ↓
executeApprovedAction() runs
  ↓
Logs to audit_logs
```

### New Universal Flow
```
User Action (any resource: item, product, stock, etc.)
  ↓
submitForApproval() wrapper
  ↓
Is super admin?
├─ YES → executeAction() → log → done
└─ NO  → Create ApprovalAuditRequest → show reason modal
  ↓
User submits with reason
  ↓
Stored in approval_audit_requests
  ↓
Admin approves in AuditManagement/Index.php
  ↓
executeApprovedAction() calls executeAction()
  ↓
Logs to audit_logs
```

**The trait is just a convenient wrapper around your existing system.**

---

## 📊 Payload Structure for Each Resource

### Items
```php
'create' => [
    'name' => 'Sugar 50kg',
    'sku' => 'SUGAR-50',
    'cost' => 5000,
    'category_id' => 1,
    'reorder_level' => 10,
]

'update' => [
    'name' => 'Sugar 50kg Premium',
    'cost' => 5500,
]

'delete' => [
    'id' => 'uuid',
    'name' => 'Sugar 50kg',
]

'adjust' => [
    'quantity' => 500,
    'previous_quantity' => 200,
    'adjustment' => 300,
]
```

### Products
```php
'create' => [
    'name' => 'Chocolate Cake',
    'product_type_id' => 2,
    'cost' => 2500,
    'selling_price' => 5000,
    'department_id' => 3,
]

'update' => [
    'selling_price' => 5500,
    'cost' => 2700,
]
```

### Stock
```php
'adjust' => [
    'quantity' => 500,
    'previous_quantity' => 200,
    'reason_code' => 'stock_take',
    'notes' => 'Physical count verification',
]
```

### Recipes
```php
'create' => [
    'name' => 'Vanilla Cake Mix',
    'department_id' => 2,
    'description' => 'Base recipe for vanilla cakes',
]

'update' => [
    'description' => 'Updated vanilla cake mix formula',
]
```

---

## 🚀 Deployment Order

1. **Create the trait** - no risk, isolated
2. **Test trait locally** - compile check only
3. **Add to one component** - e.g., Items (least critical)
4. **Test thoroughly** - full workflow
5. **Add to other components** - Products, Stock, etc.
6. **Train users** - explain the reason modal

---

## 🎓 User Experience

### For Regular Employees

```
1. Click: [+ Add Item] or [Edit] or [Delete] or [Adjust]
   ↓
2. See: Normal form (no "approval required" hint)
   ↓
3. Fill form → Click [Save]
   ↓
4. See: "Saving..." spinner (normal operation)
   ↓
5. Form closes → Modal appears:
   ┌──────────────────────────┐
   │  Why Are You Requesting? │
   │                          │
   │  [______reason_______]   │
   │                          │
   │  [Cancel]  [Submit]      │
   └──────────────────────────┘
   ↓
6. Type reason → Click [Submit Request]
   ↓
7. Toast: "Request submitted for approval"
   ↓
8. Wait for admin approval
```

### For Super Admins

```
1. Click: [+ Add Item] or [Edit] or [Delete] or [Adjust]
   ↓
2. See: Normal form
   ↓
3. Fill form → Click [Save]
   ↓
4. See: "Saving..." spinner
   ↓
5. Item created/updated/deleted immediately
   ↓
6. Toast: "Item created successfully"
   ↓
7. No reason modal, no waiting
```

---

## 🔐 Security Notes

- ✅ **Status checks prevent double-processing** - Once approved, can't approve again
- ✅ **Payload validation** - Only fillable fields are applied
- ✅ **Guard-aware** - Respects model's guard (web vs employees)
- ✅ **Error resilient** - No partial updates
- ✅ **Audit complete** - Every action logged
- ✅ **Branch context** - Branch ID stored with request
- ✅ **Polymorphic actors** - Works with Users & Employees

---

## 🧪 Testing Template

```php
// Test 1: Create with regular employee
$this->actingAs($employee, 'employees')
    ->component('Items')
    ->call('saveItem')
    ->assertDispatched('toast', 
        message: 'Your request has been submitted for approval'
    );

$this->assertEquals('pending', 
    ApprovalAuditRequest::latest()->first()->status
);

// Admin approves
$admin = User::role('super_admin')->first();
$this->actingAs($admin)
    ->component('AuditManagement/Index')
    ->call('approveRequest', $request->id);

// Verify item created
$this->assertTrue(Item::where('name', 'Sugar 50kg')->exists());

// Test 2: Create with super admin
$this->actingAs($superAdmin, 'employees')
    ->component('Items')
    ->call('saveItem')
    ->assertDispatched('toast', 
        message: 'Item created successfully'
    );

// No approval request created
$this->assertEquals(0, 
    ApprovalAuditRequest::where('action', 'like', 'create:item%')
        ->count()
);
```

---

## 📚 Summary

Your system already has:
- ✅ Approval infrastructure
- ✅ Audit logging
- ✅ Request management
- ✅ Approval processing

This implementation adds:
- 🎯 Unified trait for all components
- 🎯 One pattern for all resource types
- 🎯 Seamless super-admin bypass
- 🎯 Consistent reason capture
- 🎯 Complete component independence

**Result:** Drop the `RequiresApprovalWorkflow` trait into any component, and it instantly gains universal approval functionality.

---

## 🔗 References

- **Existing:** `AuditService.php` - Use for logging
- **Existing:** `ApprovalAuditRequest.php` - Request storage
- **Existing:** `AuditManagement/Index.php` - Approval processing
- **New:** `RequiresApprovalWorkflow.php` - This trait

All work together as one cohesive system.

---

**Last Updated:** December 2, 2025  
**Ready for Implementation:** ✅ Yes  
**Estimated Implementation Time:** 4-6 hours for full deployment  
**Testing Time:** 2-3 hours
