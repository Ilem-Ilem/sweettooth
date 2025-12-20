# ApprovalAuditRequest.php

## Status: Fully Implemented

## Description
Database model for storing approval requests with polymorphic requester/approver relationships. Manages the workflow for actions requiring administrative approval, including payload storage and status tracking.

## Key Features
- Polymorphic morphTo relationships (requester, approver)
- Payload storage for action data
- Status management (pending, approved, rejected)
- Branch context awareness
- Static createPending method for creating requests with notification setup
- Notification integration (commented out pending implementation)

## Faults
- **MAJOR FEATURE MISSING**: Notification system completely disabled
  - `Notification::send()` calls are commented out in `createPending()` method
  - No notifications sent to superadmins or approvers when requests are created
  - Approval workflow relies on manual checking instead of proactive notifications
- Unused import: `App\Models\Notification`
- Unused variables: `$superadmins`, `$approvers` in `createPending()` method (would be used for notifications)

## To Be Done
- Implement notification system for approval requests (email/SMS/in-app notifications to superadmins and approvers)