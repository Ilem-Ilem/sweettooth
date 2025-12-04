# Universal Approval Workflow - Visual Guide

---

## 1. Complete User Journey

### Regular Employee Flow
```
┌─────────────────────────────────────────────────────────────────┐
│                    REGULAR EMPLOYEE                             │
└─────────────────────────────────────────────────────────────────┘

   1. Click "+ Add Item"
   ↓
   ┌────────────────────┐
   │  Form appears      │
   │  Name: [_______]   │
   │  SKU:  [_______]   │
   │  Cost: [_______]   │
   │  [Save]            │
   └────────┬───────────┘
           │
   2. Fill details
   ↓
   Click [Save]
           │
   3. Form closes
   ↓
   ┌──────────────────────────────┐
   │ "Why Are You Adding This?"   │
   │                              │
   │ [____________________reason] │
   │ [Cancel]      [Submit]       │
   └────────┬─────────────────────┘
           │
   4. Type reason
   ↓
   Click [Submit]
           │
   5. Request stored
   ↓
   ┌──────────────────────────────┐
   │ Toast:                       │
   │ "Request submitted for       │
   │  approval"                   │
   └──────────────────────────────┘
           │
   6. WAIT for admin approval
   ↓
   ┌──────────────────────────────┐
   │ (Admin goes to Audit         │
   │  Management, clicks Approve) │
   └────────┬─────────────────────┘
           │
   7. Request approved
   ↓
   ✅ Item is created
   ✅ Can now be used in system
```

### Super Admin Flow
```
┌─────────────────────────────────────────────────────────────────┐
│                      SUPER ADMIN                                │
└─────────────────────────────────────────────────────────────────┘

   1. Click "+ Add Item"
   ↓
   ┌────────────────────┐
   │  Form appears      │
   │  Name: [_______]   │
   │  SKU:  [_______]   │
   │  Cost: [_______]   │
   │  [Save]            │
   └────────┬───────────┘
           │
   2. Fill details
   ↓
   Click [Save]
           │
   3. NO MODAL
   ↓
   ✅ Item created immediately
   ↓
   ┌──────────────────────────────┐
   │ Toast:                       │
   │ "Item created successfully"  │
   └──────────────────────────────┘
           │
   4. Ready to use
   ↓
   ✅ Done
```

---

## 2. System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      USER INTERFACE LAYER                    │
│  (Livewire Components: Items, Products, Stocks, etc.)       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  saveItem()        saveProduct()       adjustStock()        │
│      │                  │                   │               │
│      └──────────────────┴───────────────────┘               │
│                      │                                      │
└──────────────────────┼──────────────────────────────────────┘
                       │
                       │ Uses Trait
                       │
┌──────────────────────▼──────────────────────────────────────┐
│              REQUIRESAPPROVALWORKFLOW TRAIT                  │
│           (app/Traits/RequiresApprovalWorkflow.php)          │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  submitForApproval()                                         │
│  ├─ Check: isSuperAdmin()?                                  │
│  │  ├─ YES → executeAction() → Direct execution             │
│  │  └─ NO  → Create ApprovalAuditRequest                   │
│  └─ Return result [approved, request_id, message]           │
│                                                              │
│  executeAction()                                             │
│  ├─ handleCreate()                                          │
│  ├─ handleUpdate()                                          │
│  ├─ handleDelete()                                          │
│  └─ handleAdjust()                                          │
│                                                              │
│  getModelClass()                                             │
│  └─ Map: 'item' → Item::class, 'product' → Product::class  │
│                                                              │
└──────────────┬──────────────────────────────────────────────┘
               │
               │ Calls
               │
┌──────────────▼──────────────────────────────────────────────┐
│              DATABASE ACCESS LAYER                           │
│                                                              │
│  ApprovalAuditRequest::create()  → stores request           │
│  Item::create()/update()/delete() → executes action         │
│  AuditService::log()              → logs to audit_logs      │
│                                                              │
└──────────────┬──────────────────────────────────────────────┘
               │
               │ Persists
               │
┌──────────────▼──────────────────────────────────────────────┐
│              DATABASE TABLES                                 │
│                                                              │
│  approval_audit_requests  → Request queue                   │
│  audit_logs              → Complete trail                   │
│  items, products, etc.   → Resources                        │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

---

## 3. Request State Machine

```
                    ┌─────────────┐
                    │ NO REQUEST  │
                    │ Super admin │
                    │ or auto     │
                    └─────────────┘
                          │
                          │ Regular user action
                          │ without bypass
                          │
                    ┌─────▼─────────┐
                    │    PENDING    │
                    │ Waiting for   │
                    │ admin action  │
                    └─────┬─────┬───┘
                          │     │
                     APPROVE   REJECT
                          │     │
            ┌─────────────┘     └──────────────┐
            │                                   │
       ┌────▼──────┐                   ┌──────▼─────┐
       │ APPROVED  │                   │ REJECTED   │
       │ Processed │                   │ Denied     │
       │ Executed  │                   │ Request    │
       └───────────┘                   │ Stays      │
                                      │ Pending*   │
                                      └────────────┘

* Can be re-submitted by user or admin can override


    Status Flow:
    ┌──────────────┐
    │   PENDING    │  ← Initial state after creation
    │ (waiting)    │
    └──────┬───────┘
           │
      ┌────┴────┐
      │          │
   APPROVE    REJECT
      │          │
   ┌──▼──┐   ┌──▼────┐
   │APPR │   │REJEC  │
   │OVED │   │TED    │
   └─────┘   └───────┘
   (FINAL)   (FINAL)

Rules:
• Once in APPROVED → can't change
• Once in REJECTED → can't change
• Only PENDING can be processed
• Prevents double-processing
```

---

## 4. Data Flow Diagram

```
EMPLOYEE ACTION
    │
    ▼
┌─────────────────────────────┐
│ Validate Form Data          │
│ ├─ name required?           │
│ ├─ sku unique?              │
│ └─ cost positive?           │
└─────────┬───────────────────┘
          │
   Success│
          │
          ▼
┌─────────────────────────────┐
│ Is Super Admin?             │
│                             │
│ YES → Execute directly      │
│ NO  → Create approval req   │
└─────┬───────────────────────┘
      │
      ├─ Super Admin Path ─────┐
      │                        │
      │                   ┌────▼──────┐
      │                   │ Call      │
      │                   │ executeAction()
      │                   └────┬──────┘
      │                        │
      │                   ┌────▼────────┐
      │                   │ Insert into │
      │                   │ items table │
      │                   └────┬────────┘
      │                        │
      │                   ┌────▼────────────┐
      │                   │ Log to          │
      │                   │ audit_logs      │
      │                   │ status=         │
      │                   │ "completed"     │
      │                   └────┬────────────┘
      │                        │
      │                   ✅ DONE
      │
      └─ Regular User Path ──┐
                             │
                        ┌────▼──────────────┐
                        │ Create request in │
                        │ approval_audit_   │
                        │ requests table    │
                        │ status="pending"  │
                        └────┬─────────────┘
                             │
                        ┌────▼──────────┐
                        │ Show reason   │
                        │ modal to user │
                        └────┬──────────┘
                             │
                        User types reason
                             │
                        ┌────▼──────────┐
                        │ Submit request│
                        └────┬──────────┘
                             │
                        ⏳ WAITING FOR ADMIN
                             │
                        ┌────▼──────────────────┐
                        │ Admin goes to Audit   │
                        │ Management dashboard  │
                        └────┬─────────────────┘
                             │
                        Admin clicks [Approve]
                             │
                        ┌────▼──────────────┐
                        │ executeApprovedAction()
                        │ (in AuditManagement)
                        └────┬──────────────┘
                             │
                        ┌────▼────────────┐
                        │ Call            │
                        │ executeAction() │
                        │ from trait      │
                        └────┬────────────┘
                             │
                        ┌────▼──────────┐
                        │ Insert into   │
                        │ items table   │
                        └────┬──────────┘
                             │
                        ┌────▼──────────────┐
                        │ Update request    │
                        │ status="approved" │
                        └────┬──────────────┘
                             │
                        ┌────▼────────────┐
                        │ Log to          │
                        │ audit_logs      │
                        └────┬────────────┘
                             │
                        ✅ DONE
```

---

## 5. Trait Method Hierarchy

```
RequiresApprovalWorkflow
│
├─ PUBLIC METHODS (Called by component)
│  │
│  ├─ submitForApproval()
│  │  └─ Main entry point
│  │     ├─ Checks if super admin
│  │     ├─ If YES: calls executeAction() directly
│  │     └─ If NO: creates ApprovalAuditRequest
│  │
│  ├─ executeAction()
│  │  └─ Dispatcher method
│  │     ├─ Routes to handleCreate()
│  │     ├─ Routes to handleUpdate()
│  │     ├─ Routes to handleDelete()
│  │     └─ Routes to handleAdjust()
│  │
│  └─ isSuperAdmin()
│     └─ Permission check
│
├─ PROTECTED METHODS (Internal use)
│  │
│  ├─ getCurrentActor()
│  │  └─ Returns logged-in User or Employee
│  │
│  ├─ getCurrentBranchId()
│  │  └─ Returns branch context
│  │
│  ├─ handleCreate(resourceType, payload)
│  │  └─ Creates new record
│  │
│  ├─ handleUpdate(resourceType, resourceId, payload)
│  │  └─ Updates existing record
│  │
│  ├─ handleDelete(resourceType, resourceId)
│  │  └─ Deletes record
│  │
│  ├─ handleAdjust(resourceType, resourceId, payload)
│  │  └─ Adjusts stock/price/quantity
│  │
│  └─ getModelClass(resourceType)
│     └─ Maps 'item' → Item::class, etc.
│
└─ CALL FLOW
   │
   Component.saveItem()
   │
   → if (isSuperAdmin())
   │    executeAction('create', 'item', null, payload)
   │    └─ handleCreate('item', payload)
   │       └─ Item::create(payload)
   │
   → else
      submitForApproval('create', 'item', null, payload, reason)
      └─ ApprovalAuditRequest::create(...)
         └─ Show reason modal in UI
```

---

## 6. Database Schema Relationships

```
┌──────────────────────────────┐
│  approval_audit_requests     │
├──────────────────────────────┤
│ id                           │
│ branch_id ──────┐            │
│ requester_id    │            │
│ requester_type  │ (Polymorphic)
│ action ◄────────┼─────┐      │
│ description     │     │      │
│ payload         │     │      │
│ status ◄────────┼─┐   │      │
│ approved_at     │ │   │      │
│ approver_id     │ │   │      │
│ approver_type   │ │   │      │
└──────────────────────────────┘
                  │ │   │
                  │ │   └─────────┐
                  │ │             │
                  │ └───┐    ┌────▼──────────────────┐
                  │     │    │  audit_logs          │
                  │     │    ├──────────────────────┤
                  │     │    │ id                   │
                  │     │    │ branch_id ◄──────────┤
                  │     │    │ causer_type          │
                  │     │    │ causer_id            │
                  │     │    │ auditable_type       │
                  │     │    │ auditable_id         │
                  │     │    │ action ◄─────────────┤
                  │     │    │ status ◄─────────────┤
                  │     │    │ approval_request_id ◄┤
                  │     │    │ old_values           │
                  │     │    │ new_values           │
                  │     │    └──────────────────────┘
                  │     │
                  │     └─ Both track the "approval" action
                  │
                  └─ Branch context for multi-branch systems


Polymorphic Relationships:
requester_type can be:
├─ App\Models\User (web guard)
└─ App\Models\Employee (employees guard)

approver_type can be:
├─ App\Models\User (web guard)
└─ App\Models\Employee (employees guard)
```

---

## 7. Component Integration Pattern

```
YOUR COMPONENT
│
├─ Properties
│  ├─ name, sku, cost (form fields)
│  ├─ reason (for approval reason)
│  └─ showReasonModal (visibility toggle)
│
├─ Add Trait
│  └─ use RequiresApprovalWorkflow;
│
├─ Public Methods (called from view)
│  ├─ saveItem()
│  │  ├─ Validate form
│  │  ├─ Check: isSuperAdmin()?
│  │  ├─ YES: executeCreate() → direct
│  │  └─ NO: show reason modal
│  │
│  └─ submitRequest()
│     ├─ Validate reason
│     └─ submitForApproval('create', 'item', null, payload, reason)
│
├─ Private Methods (helpers)
│  ├─ executeCreate()
│  │  └─ executeAction('create', 'item', null, payload)
│  │
│  └─ resetForm()
│     └─ Reset all fields
│
└─ View (blade template)
   ├─ Form for item details
   └─ Modal for reason
      └─ Only shown for non-super-admins


Usage Example:
class Items extends Component {
    use RequiresApprovalWorkflow;
    
    public $name = '';
    public $showReasonModal = false;
    public $reason = '';
    
    public function saveItem() {
        if ($this->isSuperAdmin()) {
            $this->executeCreate();
        } else {
            $this->showReasonModal = true;
        }
    }
    
    public function submitRequest() {
        $result = $this->submitForApproval(
            'create',
            'item',
            null,
            ['name' => $this->name],
            $this->reason
        );
    }
}
```

---

## 8. Admin Approval Dashboard Flow

```
AUDIT MANAGEMENT INDEX COMPONENT
│
├─ Query approval_audit_requests
│  └─ WHERE status = 'pending'
│
├─ Display as table/list
│  ├─ Request type (create_item, update_product, etc.)
│  ├─ Resource details (from payload)
│  ├─ Requested by (requester name)
│  ├─ Reason (description)
│  └─ Actions: [Approve] [Reject]
│
├─ Admin clicks [Approve]
│  │
│  ├─ Validate request still pending
│  │  └─ Prevent double-approval
│  │
│  ├─ Call approveRequest(requestId)
│  │
│  ├─ Execute execution flow:
│  │  │
│  │  ├─ Parse action string: "create:item"
│  │  │
│  │  ├─ Call executeApprovedAction()
│  │  │  │
│  │  │  ├─ Match on action type
│  │  │  │
│  │  │  ├─ Call handleCreateAction('item', payload)
│  │  │  │  │
│  │  │  │  ├─ Extract fillable fields from payload
│  │  │  │  │
│  │  │  │  └─ Item::create(fillableData)
│  │  │  │
│  │  │  └─ Return created model
│  │  │
│  │  ├─ Update request status to "approved"
│  │  │
│  │  ├─ Log approval action to audit_logs
│  │  │
│  │  └─ Show success toast
│  │
│  └─ ✅ Complete
│
└─ Admin clicks [Reject]
   │
   ├─ Validate request still pending
   │
   ├─ Show rejection reason modal
   │
   ├─ Admin types reason
   │
   ├─ Call rejectRequest(requestId, reason)
   │
   ├─ Update request status to "rejected"
   │
   ├─ Log rejection to audit_logs
   │
   └─ ✅ Complete (item NOT created)
```

---

## 9. Testing Workflow

```
TEST SCENARIO 1: Create with Regular Employee
┌─────────────────────────────────────────────────┐
│ Login as Employee                               │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Navigate to Items, click "+ Add Item"            │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Fill form: name="Sugar 50kg", sku="SUGAR-50"   │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Click [Save]                                    │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ ✓ See reason modal                              │
│   (PASS: Modal shown for non-super-admin)       │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Type reason: "New item for bakery"              │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Click [Submit Request]                          │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ ✓ See toast: "Request submitted for approval"  │
│   (PASS: Toast shown)                           │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Check database:                                 │
│ SELECT * FROM approval_audit_requests           │
│ WHERE status = 'pending'                        │
│ AND action LIKE 'create:item%'                  │
│                                                  │
│ ✓ Request found                                 │
│   (PASS: Request stored)                        │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Login as Admin                                  │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Navigate to Audit Management                    │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ ✓ See pending request in dashboard              │
│   (PASS: Request visible to admin)              │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Click [Approve]                                 │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ ✓ See toast: "Request approved successfully"   │
│   (PASS: Approval confirmation shown)           │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Check database:                                 │
│ SELECT * FROM items WHERE sku = 'SUGAR-50'     │
│                                                  │
│ ✓ Item created                                  │
│   (PASS: Item exists)                           │
└─────────────┬───────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────┐
│ Check audit trail:                              │
│ SELECT * FROM audit_logs                        │
│ WHERE auditable_type LIKE '%Item'               │
│ AND action LIKE '%approve%'                     │
│                                                  │
│ ✓ Audit log entry found                        │
│   (PASS: Action logged)                         │
└─────────────┬───────────────────────────────────┘
              │
              ▼
         ✅ TEST PASS
```

---

**Last Updated:** December 2, 2025  
**Status:** ✅ Complete
