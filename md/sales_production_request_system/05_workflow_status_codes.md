# Sales to Production Request System - Workflow & Status Codes

## Request Status Lifecycle

```
┌─────────┐
│ pending │  ← Initial state when created
└────┬────┘
     │
     v
┌──────────────┐
│ in_progress  │  ← Production starts work
└────┬─────────┘
     │
     v
┌──────────────┐
│quality_check │  ← Undergoing quality control
└────┬─────────┘
     │
     v
┌──────────────┐
│  completed   │  ← Production finished
└────┬─────────┘
     │
     v
┌──────────────┐
│  dispatched  │  ← Sent to sales for verification
└────┬─────────┘
     │
     ├─────────────────────────────┐
     │                             │
     v                             v
┌──────────┐                 ┌──────────┐
│ accepted │                 │ rejected │  ← Sales rejected dispatch
└──────────┘                 └────┬─────┘
   (End)                          │
                                  v
                            ┌─────────────┐
                            │ in_progress │  ← Create new request
                            └─────────────┘

Can cancel at any time:
┌───────────────────────────────────────────────┐
│                                               │
any status ──────────────────────────────> cancelled
```

---

## Status Definitions

### PENDING
- **When**: Request just created
- **Who**: Sales department
- **Duration**: Until production starts work
- **Actions Available**:
  - ✅ Start production
  - ✅ Cancel request
  - ✅ View request
- **Actions NOT Available**:
  - ❌ Accept/reject dispatch
  - ❌ Update progress
- **Notes**: Waiting for production to acknowledge

---

### IN_PROGRESS
- **When**: Production starts work
- **Who**: Production department
- **Duration**: While actively producing
- **Actions Available**:
  - ✅ Update progress milestones
  - ✅ Add notes
  - ✅ Mark as complete
  - ✅ Cancel request
- **Actions NOT Available**:
  - ❌ Accept/reject dispatch
  - ❌ Re-start production
- **Notes**: Production is actively working

---

### QUALITY_CHECK
- **When**: Product is undergoing QC
- **Who**: Production department
- **Duration**: During quality verification
- **Actions Available**:
  - ✅ Update progress
  - ✅ Add QC notes
  - ✅ Approve QC and move to complete
  - ✅ Mark issues and revert to in_progress
- **Actions NOT Available**:
  - ❌ Accept/reject dispatch
  - ❌ Cancel request
- **Notes**: Quality assurance phase

---

### COMPLETED
- **When**: Production finished
- **Who**: Production department
- **Duration**: After production, before dispatch
- **Actions Available**:
  - ✅ Create dispatch
  - ✅ Add final notes
- **Actions NOT Available**:
  - ❌ Accept/reject dispatch
  - ❌ Update progress
  - ❌ Cancel request
- **Notes**: Ready to dispatch to sales

---

### DISPATCHED
- **When**: Products sent to sales department
- **Who**: Production department
- **Duration**: Until sales verifies
- **Actions Available**:
  - ✅ Sales: Accept dispatch
  - ✅ Sales: Reject dispatch
  - ✅ View dispatch details
- **Actions NOT Available**:
  - ❌ Cancel request
  - ❌ Update progress
  - ❌ Production: Modify
- **Notes**: Awaiting verification from sales

---

### ACCEPTED
- **When**: Sales verifies and accepts
- **Who**: Sales department
- **Duration**: Final state
- **Actions Available**:
  - ✅ View request
  - ✅ View dispatch
  - ✅ Close request
- **Actions NOT Available**:
  - ❌ Any modifications
- **Notes**: Request complete and verified

---

### REJECTED
- **When**: Sales rejects dispatch
- **Who**: Sales department
- **Duration**: Until new request created
- **Actions Available**:
  - ✅ Create new production request
  - ✅ View rejection reason
  - ✅ Communicate with production
- **Actions NOT Available**:
  - ❌ Accept dispatch
  - ❌ Modify current request
- **Notes**: Products didn't meet requirements. Create new request or retry.

---

### CANCELLED
- **When**: Request cancelled
- **Who**: Any authorized user
- **Duration**: Final state
- **Actions Available**:
  - ✅ View request history
  - ✅ Create new request
- **Actions NOT Available**:
  - ❌ Resume production
  - ❌ Modify any fields
- **Notes**: Request is permanently closed

---

## Progress Milestones

### STARTED
```json
{
  "milestone": "started",
  "progress_percentage": 0,
  "notes": "Production has begun",
  "typical_duration": "Immediate"
}
```

### IN_PRODUCTION
```json
{
  "milestone": "in_production",
  "progress_percentage": "0-99",
  "notes": "Actively producing items",
  "typical_duration": "Varies by product"
}
```

### QUALITY_CHECK
```json
{
  "milestone": "quality_check",
  "progress_percentage": "80-99",
  "notes": "Undergoing QC verification",
  "typical_duration": "5-15 minutes"
}
```

### COMPLETED
```json
{
  "milestone": "completed",
  "progress_percentage": 100,
  "notes": "Production complete, ready to dispatch",
  "typical_duration": "Immediate transition to dispatched"
}
```

---

## Status Transition Rules

### Valid Transitions
```
pending           → in_progress, cancelled
in_progress       → quality_check, in_progress (add notes), cancelled
quality_check     → completed, in_progress (issues found)
completed         → dispatched
dispatched        → accepted, rejected
accepted          → (final state)
rejected          → (final state, new request created)
cancelled         → (final state)
```

### Invalid Transitions
```
pending           ✗ accepted, rejected, dispatched, quality_check, completed
in_progress       ✗ accepted, rejected, dispatched, completed
quality_check     ✗ accepted, rejected, dispatched
completed         ✗ accepted, rejected, in_progress
dispatched        ✗ in_progress, quality_check, completed
accepted          ✗ any (final)
rejected          ✗ any (final)
cancelled         ✗ any (final)
```

---

## Role-Based Actions

### Sales Department

#### Can Create
- Production request with:
  - Select production department
  - Select products (from that department)
  - Set batch quantities
  - Set priority
  - Add notes

#### Can View
- All their requests
- Progress updates in real-time
- Dispatch details
- Request history

#### Can Do
- Accept dispatch (verify products match request)
- Reject dispatch (with reason/feedback)
- Cancel pending requests
- Add notes to requests

#### Cannot Do
- Update progress (production only)
- Mark as complete (production only)
- Create dispatch (production only)

---

### Production Department

#### Can Create
- Start working on request
- Progress milestones
- Dispatch (when completed)

#### Can View
- All requests for their department
- All progress for their requests
- Rejection feedback from sales

#### Can Do
- Mark request as started
- Update progress milestones
- Add production notes
- Mark as complete
- Create dispatch with quantities
- View dispatch status

#### Cannot Do
- Accept/reject dispatch (sales only)
- Create requests (sales only)
- Change priority (sales only)

---

### Admin/Manager

#### Can
- View all requests across all departments
- Cancel any request
- Override status (with audit trail)
- Export request history
- Generate reports
- Configure workflow rules

---

## Request Timeline Example

```
12:00 PM  - Sales creates request for 10 Croissants
           Status: pending
           
12:05 PM  - Production receives notification
           
12:10 PM  - Production starts work
           Status: in_progress
           Progress: 0%, Milestone: started
           
12:30 PM  - Production updates progress
           Status: in_progress
           Progress: 40%, Milestone: in_production
           Notes: "Mixing and shaping"
           
01:00 PM  - Production updates progress
           Status: in_progress
           Progress: 80%, Milestone: quality_check
           Notes: "QC inspection starting"
           
01:15 PM  - Production completes QC
           Status: completed
           Progress: 100%, Milestone: completed
           
01:20 PM  - Production dispatches products
           Status: dispatched
           Quantities: 10 produced, 10 dispatched
           
01:30 PM  - Sales receives dispatch notification
           
01:40 PM  - Sales verifies and accepts
           Status: accepted
           Notes: "All products verified"
           
01:45 PM  - Request closed
```

---

## Error States

### Invalid Status Transition
```
Error: Cannot transition from 'pending' to 'accepted'
Reason: Invalid status transition requested
Action: Update to valid status (in_progress, cancelled)
```

### Unauthorized Action
```
Error: Only production department can update progress
Reason: User is from sales department
Action: Login with production user account
```

### Missing Required Data
```
Error: Cannot dispatch without quantity_produced
Reason: Dispatch creation requires quantity details
Action: Provide all required dispatch fields
```

---

## Audit Trail

All status changes are logged:
```json
{
  "request_id": 42,
  "timestamp": "2025-12-27T12:00:00Z",
  "from_status": "pending",
  "to_status": "in_progress",
  "triggered_by": "Jane Production",
  "notes": "Started batch 1",
  "ip_address": "192.168.1.100"
}
```

---

## Notifications by Status

| Status | Notify | Via | Content |
|--------|--------|-----|---------|
| pending | Production Dept | Alert, Email | "New production request available" |
| in_progress | Sales User | Alert | "Production has started" |
| quality_check | Sales User | Alert | "Product undergoing QC" |
| completed | Sales User | Alert | "Production complete" |
| dispatched | Sales User | Alert, Email | "Ready for verification" |
| accepted | Production Dept | Alert | "Dispatch accepted" |
| rejected | Production Dept | Alert, Email | "Dispatch rejected - feedback provided" |
| cancelled | Both | Alert | "Request cancelled" |
