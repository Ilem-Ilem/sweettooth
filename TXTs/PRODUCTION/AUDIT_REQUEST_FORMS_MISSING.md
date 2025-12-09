# Production Module - Missing Audit Request Forms

## Overview
The Production module is **MISSING** audit request reason forms for critical CRUD operations on Products and Recipes. This is a **CRITICAL** compliance and accountability gap.

## Current Status

### ❌ Missing Audit Request Forms

| Operation | File | Status | Priority |
|-----------|------|--------|----------|
| **Product Create** | Products.php | ❌ NO audit modal | CRITICAL |
| **Product Edit** | Products.php | ❌ NO audit modal | CRITICAL |
| **Product Delete** | Products.php | ❌ NO audit modal | CRITICAL |
| **Recipe Create** | Recipes/Add.php | ❌ NO audit modal | CRITICAL |
| **Recipe Edit** | Recipes/Edit.php | ❌ NO audit modal | CRITICAL |
| **Recipe Delete** | Recipes.php | ❌ NO audit modal | CRITICAL |

### ✅ What Exists (Partial)

**ProductionAuditService.php** - Has audit logging methods:
- ✅ `logRecipeCreated()`
- ✅ `logRecipeDeleted()`
- ✅ `logProductUpdated()`
- ✅ `logProductDeleted()`
- ✅ Other audit logging methods

**But:** These just log AFTER action, don't capture REASON BEFORE action.

---

## Reference Implementation (Inventory Module)

### Example: Items.php (Inventory Module) - Lines 50-80, 128-135, 223-240

```php
// Properties
public bool $showAuditModal = false;
public ?string $auditAction = null;          // create|edit|delete
public string $auditReason = '';              // Reason text
public ?int $pendingItemId = null;            // Which item

// Open audit modal for edit
public function openEditModal($id)
{
    $this->pendingItemId = $id;
    $this->auditAction = 'edit';
    $this->showAuditModal = true;             // Show modal first
}

// After user provides reason, process the action
public function submitAuditRequest()
{
    $this->validate([
        'auditReason' => 'required|string|min:10|max:500',
    ]);

    // Now create ApprovalAuditRequest
    ApprovalAuditRequest::create([
        'user_id' => auth()->id(),
        'model_type' => 'App\Models\Item',
        'model_id' => $this->pendingItemId,
        'action' => $this->auditAction,
        'reason' => $this->auditReason,
        'status' => 'pending',
    ]);

    $this->closeAuditModal();
    $this->toast()->success('Approval request submitted')->send();
}

private function closeAuditModal()
{
    $this->showAuditModal = false;
    $this->auditReason = '';
    $this->auditAction = null;
    $this->pendingItemId = null;
}
```

### View: audit-modal.blade.php (Inventory)

```blade
<x-modal :show="$showAuditModal" wire:model="showAuditModal" size="lg">
    <x-slot name="title">
        {{ ucfirst($auditAction) }} Approval Request
    </x-slot>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Reason for {{ $auditAction }}</label>
            <textarea 
                wire:model="auditReason"
                class="mt-1 block w-full rounded-md border-gray-300"
                rows="4"
                placeholder="Explain why this {{ $auditAction }} is necessary..."
            ></textarea>
            @error('auditReason')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <x-slot name="footer">
        <button 
            type="button" 
            wire:click="$set('showAuditModal', false)"
            class="btn btn-secondary"
        >Cancel</button>
        <button 
            type="button" 
            wire:click="submitAuditRequest"
            class="btn btn-primary"
        >Submit Request</button>
    </x-slot>
</x-modal>
```

---

## Production Module - Current Implementation

### Products.php - What's Missing

**Current Flow:**
```php
// Line 189 - Direct save without reason
public function save()
{
    $this->validate($this->rules());
    
    if ($this->productId) {
        // UPDATE - NO REASON CAPTURED
        $product = Product::find($this->productId);
        $product->update($this->productData);
    } else {
        // CREATE - NO REASON CAPTURED
        Product::create($this->productData);
    }
    
    $this->toast()->success('Product saved')->send();
}

// Line 228 - Direct delete with dialog only
public function delete($id)
{
    $this->dialog()->confirm([
        'title' => 'Delete Product',
        'description' => 'Are you sure?',
        'acceptAction' => 'confirmedDelete',
        'acceptText' => 'Yes, Delete',
    ])->send();
}

public function confirmedDelete($id)
{
    // DELETE - NO REASON CAPTURED
    Product::find($id)?->delete();
    $this->resetPage();
}
```

**Should Be:**
```php
// Properties
public bool $showAuditModal = false;
public ?string $auditAction = null;
public string $auditReason = '';
public ?int $pendingProductId = null;
public array $pendingProductData = [];

// Save with audit modal
public function save()
{
    $this->validate($this->rules());
    
    $this->auditAction = $this->productId ? 'edit' : 'create';
    $this->pendingProductId = $this->productId;
    $this->pendingProductData = $this->productData;
    $this->showAuditModal = true;  // SHOW MODAL FIRST
}

// Submit reason
public function submitAuditRequest()
{
    $this->validate([
        'auditReason' => 'required|string|min:10|max:500',
    ]);

    ApprovalAuditRequest::create([
        'user_id' => auth('employees')->id(),
        'model_type' => 'App\Models\Product',
        'model_id' => $this->pendingProductId,
        'action' => $this->auditAction,
        'reason' => $this->auditReason,
        'status' => 'pending',
    ]);

    $this->closeAuditModal();
    $this->toast()->success('Approval request submitted')->send();
}

// Delete with audit modal
public function delete($id)
{
    $this->pendingProductId = $id;
    $this->auditAction = 'delete';
    $this->showAuditModal = true;  // SHOW MODAL FOR REASON
}

private function closeAuditModal()
{
    $this->showAuditModal = false;
    $this->auditReason = '';
    $this->auditAction = null;
    $this->pendingProductId = null;
    $this->pendingProductData = [];
}
```

### Recipes/Add.php & Recipes/Edit.php - Similar Issues

**Current:**
- ❌ Direct save without reason capture
- ✅ Has audit logging AFTER (via ProductionAuditService)
- ❌ Missing: ApprovalAuditRequest creation

---

## Affected Files to Update

### 1. Production Module Components
```
app/Livewire/BranchDashboard/Production/Products.php
app/Livewire/BranchDashboard/Production/Recipes/Add.php
app/Livewire/BranchDashboard/Production/Recipes/Edit.php
app/Livewire/BranchDashboard/Production/Recipes.php
```

### 2. View Files to Create
```
resources/views/livewire/branch-dashboard/production/partials/audit-modal.blade.php
resources/views/livewire/branch-dashboard/production/partials/product-audit-modal.blade.php
resources/views/livewire/branch-dashboard/production/partials/recipe-audit-modal.blade.php
```

### 3. Services to Integrate
```
app/Services/ProductionAuditService.php (already exists, enhance it)
app/Models/ApprovalAuditRequest.php (already exists, ensure proper usage)
```

---

## Implementation Steps

### STEP 1: Update Products.php (CRITICAL)

Add properties:
```php
public bool $showAuditModal = false;
public ?string $auditAction = null;
public string $auditReason = '';
public ?int $pendingProductId = null;
public array $pendingProductData = [];
```

Update methods:
```php
// Change save() to open modal instead of directly saving
// Change delete() to open modal instead of direct delete
// Add submitAuditRequest() to handle approval request creation
// Add closeAuditModal() to reset state
```

### STEP 2: Update Recipes/Add.php (CRITICAL)

Add properties:
```php
public bool $showAuditModal = false;
public string $auditReason = '';
public array $pendingRecipeData = [];
```

Update save():
```php
// Save logic should create ApprovalAuditRequest for 'create'
```

### STEP 3: Update Recipes/Edit.php (CRITICAL)

Same as Add.php but for 'edit' action.

### STEP 4: Update Recipes.php (CRITICAL)

Update delete():
```php
// Should open audit modal instead of direct deletion
```

### STEP 5: Create Audit Modal View

Create `resources/views/livewire/branch-dashboard/production/partials/audit-modal.blade.php`

```blade
<x-modal :show="$showAuditModal" wire:model="showAuditModal" size="lg">
    <x-slot name="title">
        {{ ucfirst($auditAction) }} {{ $auditModelName ?? 'Item' }} - Approval Request
    </x-slot>

    <div class="space-y-4">
        <div class="bg-blue-50 p-4 rounded">
            <p class="text-sm text-gray-700">
                <strong>Action:</strong> {{ ucfirst($auditAction) }}<br>
                <strong>Module:</strong> Production<br>
                <strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s') }}
            </p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Reason for {{ ucfirst($auditAction) }}
            </label>
            <textarea 
                wire:model="auditReason"
                class="w-full rounded-md border border-gray-300 px-3 py-2"
                rows="5"
                placeholder="Explain why this {{ $auditAction }} is necessary. Minimum 10 characters required."
                required
            ></textarea>
            @error('auditReason')
                <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
            <p class="text-xs text-gray-600">
                <strong>Note:</strong> This action will create an approval request. A supervisor will review and approve/reject your request.
            </p>
        </div>
    </div>

    <x-slot name="footer">
        <button 
            type="button" 
            wire:click="closeAuditModal"
            class="btn btn-secondary"
        >Cancel</button>
        <button 
            type="button" 
            wire:click="submitAuditRequest"
            class="btn btn-primary"
        >Submit Request</button>
    </x-slot>
</x-modal>
```

### STEP 6: Add Validation Rule

In ProductionAuditService or create new service:
```php
public function validateAuditReason(string $reason): bool
{
    // Minimum 10 characters
    // No profanity
    // Proper grammar checks
    return strlen($reason) >= 10;
}
```

---

## Database Schema Check

Verify `approval_audit_requests` table has:
```sql
CREATE TABLE approval_audit_requests (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED,              -- Who requested
    model_type VARCHAR(255),               -- App\Models\Product, etc
    model_id BIGINT UNSIGNED,              -- Product ID, Recipe ID
    action VARCHAR(50),                    -- create, edit, delete
    reason TEXT,                           -- User-provided reason
    status VARCHAR(50),                    -- pending, approved, rejected
    reviewed_by BIGINT UNSIGNED,           -- Who approved/rejected
    reviewed_at TIMESTAMP,                 -- When reviewed
    rejection_reason TEXT,                 -- Why rejected
    created_at, updated_at
);
```

---

## Workflow After Implementation

### Current (Broken):
1. User clicks Edit Product
2. ❌ Changes saved immediately
3. ❌ No reason captured
4. ❌ No approval process
5. ✅ Audit log created (but no context)

### After Implementation (Correct):
1. User clicks Edit Product
2. ✅ Audit Modal appears
3. ✅ User enters reason
4. ✅ Validates reason (min 10 chars)
5. ✅ Creates ApprovalAuditRequest (status=pending)
6. ✅ Supervisor reviews and approves
7. ✅ Upon approval: Changes applied, audit log created
8. ✅ Full trail: reason → approval → action

---

## Risk Assessment

### Current Risk Level: **CRITICAL** 🔴

**Compliance Risks:**
- ❌ No audit trail of WHY changes made
- ❌ No approval process for sensitive changes
- ❌ Cannot track who requested change
- ❌ Cannot show change rejection reason
- ❌ Regulatory non-compliance

**Operational Risks:**
- ❌ Product specifications changed without documentation
- ❌ Recipes modified without approval
- ❌ Cannot prevent accidental deletions
- ❌ Cannot trace production issues to spec changes

**Financial Risks:**
- ❌ Cannot audit cost changes
- ❌ Cannot verify yield modifications
- ❌ Missing approval for recipe cost updates

---

## Implementation Priority

### TIER 1 - CRITICAL (Implement This Week):
1. Products.php - Add audit modal for create/edit/delete
2. Recipes/Add.php - Add audit modal for create
3. Recipes.php - Add audit modal for delete
4. Create audit-modal view

### TIER 2 - HIGH (Implement Next Week):
1. Recipes/Edit.php - Add full edit flow with audit
2. ProductTypes.php - Check if needs audit modal
3. Add validation service for audit reasons

### TIER 3 - MEDIUM (Implement Following Week):
1. Create reports on approval requests
2. Add supervisor review dashboard
3. Add rejection reason tracking
4. Add audit trail visibility in UI

---

## Files to Reference

### How to Implement (Use as Template):
- ✅ `app/Livewire/BranchDashboard/Inventory/Items.php` (Lines 50-120)
- ✅ `resources/views/livewire/branch-dashboard/inventory/partials/audit-modal.blade.php`
- ✅ `app/Models/ApprovalAuditRequest.php`

### Production Services (Already Exist):
- ✅ `app/Services/ProductionAuditService.php`
- ✅ `app/Models/Product.php`
- ✅ `app/Models/Recipe.php`

---

## Testing Checklist

After implementation:
- [ ] Create product → Modal appears → Reason captured → Request created
- [ ] Edit product → Modal appears → Reason captured → Request created
- [ ] Delete product → Modal appears → Reason captured → Request created
- [ ] Create recipe → Modal appears → Reason captured → Request created
- [ ] Edit recipe → Modal appears → Reason captured → Request created
- [ ] Delete recipe → Modal appears → Reason captured → Request created
- [ ] Reason validation (min 10 chars) works
- [ ] ApprovalAuditRequest records created correctly
- [ ] Supervisor can review and approve/reject
- [ ] Audit logs created after approval
- [ ] Cannot bypass modal (form submission blocked)

---

## Estimated Effort

- **Implementation:** 4-6 hours (for all CRUD operations)
- **Testing:** 2-3 hours
- **Documentation:** 1 hour
- **Supervisor Dashboard:** 4-6 hours (separate task)
- **Total:** 2-3 days for core implementation

---

## Approval Process Workflow (Recommended)

```
User Action (Create/Edit/Delete)
    ↓
Audit Modal Appears (Capture Reason)
    ↓
ApprovalAuditRequest Created (status=pending)
    ↓
[Immediate] Show: "Request submitted for approval"
    ↓
[Supervisor] Reviews in Approval Dashboard
    ↓
├─ APPROVED: Apply changes + Update ApprovalAuditRequest (status=approved)
│           + Create audit log
│           + Notify user
│
└─ REJECTED: Update ApprovalAuditRequest (status=rejected)
             + Store rejection reason
             + Notify user to resubmit with new reason
```

---

## Summary

**Status:** ❌ MISSING - CRITICAL COMPLIANCE GAP

**Impact:** 
- No approval workflow for product/recipe changes
- No reason capture for changes
- Regulatory non-compliance
- Audit trail incomplete

**Solution:** Implement audit modal for all 6 CRUD operations (Products: 3, Recipes: 3)

**Timeline:** 2-3 days for implementation, then supervisor review dashboard (separate task)

**Reference:** Use Inventory/Items.php as template - already correctly implemented there.

