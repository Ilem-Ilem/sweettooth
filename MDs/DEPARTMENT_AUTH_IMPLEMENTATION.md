# Department Creation Authorization System

## Overview
Implemented role-based authorization for department creation using the centralized audit system. Non-super-admin users must provide a reason when creating departments, which is recorded in `approval_audit_requests` and `audit_logs` tables for approval tracking.

## Architecture

Uses existing audit system infrastructure:
- **`approval_audit_requests` table**: Tracks all approval requests with requester info, reason, and status
- **`audit_logs` table**: Records all actions with causer, auditable, status, and linked approval request

No changes to `departments` table structure required.

## System Integration

### ApprovalAuditRequest Model
Stores department creation requests with:
- `requester_id` / `requester_type`: Who requested (User/Employee)
- `action`: "create:department"
- `description`: The reason provided by requester
- `payload`: Full department data
- `status`: 'pending', 'approved', or 'denied'
- `approver_id` / `approver_type`: Who approved (set when status changes)

### AuditLog Model
Records every action with:
- `causer_type` / `causer_id`: User/Employee who performed action
- `auditable_type` / `auditable_id`: Department model affected
- `action`: 'create' or 'update'
- `description`: Reason or context
- `status`: 'pending' (awaiting approval) or 'completed' (executed)
- `approval_request_id`: Links to ApprovalAuditRequest

## Component Updates

### `CreateOrUpdate.php`

**New Imports:**
```php
use App\Models\ApprovalAuditRequest;
use App\Services\AuditService;
```

**New Properties:**
- `showReasonModal`: Controls visibility of the reason modal for non-super admins
- `creationReason`: Stores the reason text input

**New Methods:**
- `initiateCreate()`: Shows modal for non-super admins before saving
- `closeReasonModal()`: Closes the modal and clears the reason
- Updated `resetDepartmentForm()`: Now also resets modal state

**Updated `saveDepartment()` Logic:**

**For Updates:**
- Logs action with `AuditService::log()` using action='update'
- Tracks the updater and affected department

**For New Departments - Non-Super Admins:**
- Creates the department immediately
- Validates `creationReason` (min 5 characters required)
- Creates `ApprovalAuditRequest` record with:
  - `requester_id` / `requester_type` from auth user
  - `action` = 'create:department'
  - `description` = the reason provided
  - `payload` = full department data
  - `status` = 'pending'
- Logs in `AuditLog` with status='pending' and links to approval request
- Shows message: "Department creation request submitted for approval!"

**For New Departments - Super Admins:**
- Creates the department immediately
- Logs in `AuditLog` with status='completed'
- No approval request created (auto-approved)
- Shows message: "Department created successfully!"

## View Updates

### `create.blade.php`

**Added Modal (Non-Super Admins Only):**
- Fixed position modal with overlay
- Modal Title: "Create Department Request"
- Textarea for reason input with placeholder text
- Validation error display
- Cancel/Continue buttons
- Smooth transitions (scale/opacity)

**Updated Form Button:**
- Non-editing, non-super admin: triggers `initiateCreate()` → shows modal
- All other cases: triggers `saveDepartment()` directly
- Loading states maintained

## User Flow

### Super Admin
1. Fills out form (reason optional)
2. Clicks "Create Department"
3. Department created immediately with `status='approved'`

### Non-Super Admin
1. Fills out form
2. Clicks "Create Department"
3. Modal appears asking for reason
4. Types reason (min 5 characters)
5. Clicks "Continue"
6. Department created with `status='pending'` and requester info
7. Awaits approval (future implementation)

### Editing (Both Roles)
- Modal doesn't appear for edits
- Direct save

## Styling

Maintains consistency with existing design system:
- Zinc color palette for borders and text
- Blue accent for primary actions
- Dark mode support throughout
- Tailwind CSS utilities
- Smooth transitions and hover states

## Data Flow Diagram

```
Non-Super Admin Creates Department:
├─ Show Modal (initiateCreate)
├─ User enters reason
├─ saveDepartment() called
├─ Create Department (immediately)
├─ Create ApprovalAuditRequest (pending)
├─ Log in AuditLog (status=pending, linked to request)
└─ Redirect with "submitted for approval" message

Super Admin Creates Department:
├─ Click Create (no modal)
├─ saveDepartment() called
├─ Create Department (immediately)
├─ Log in AuditLog (status=completed)
└─ Redirect with "created successfully" message

Department Updated (Both Roles):
├─ Update Department
├─ Log in AuditLog (status=completed)
└─ Redirect with "updated successfully" message
```

## Next Steps (For Future Implementation)

1. **Approval Dashboard**: Create component to view and manage pending approval requests
2. **Approve/Reject Action**: Update `ApprovalAuditRequest.status` with approver info
3. **On Approval**: Optionally update department status or constraints
4. **Notifications**: Notify requester when approval status changes
5. **Index Filter**: Show pending vs completed requests in department listing
6. **Audit Report**: Use AuditLog queries to build approval history reports
