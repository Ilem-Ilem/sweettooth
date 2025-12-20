# AuditService.php

## Status: Fully Implemented

## Description
Centralized service for handling audit logging across the entire application. Replaces AuditHelper and ActionRequestHelper with a unified, model-agnostic approach. Works seamlessly with all models using morphic *_by_id / *_by_type relationships, sensitive actions requiring approval, multi-guard authentication (Users & Employees), and action-specific reasoning and metadata.

## Key Features
- Log method for recording any action that affects data (who performed, what was affected, changes made, approval status, request metadata)
- logSensitiveAction for actions requiring supervisory approval (checks bypass, creates approval request if needed)
- updateActorReference for safely updating morphic columns with audit logging
- bulkUpdateActorReference for efficient bulk operations
- executeApprovedAction for executing approved sensitive actions
- logSync for tracking relationship syncs (attached/detached/updated)
- Branch context awareness and IP/user agent tracking
- Comprehensive error handling and logging

## Faults
- String causer handling has error logging but may need refinement for edge cases
- Unused variables: `$oldId`, `$oldType` in `updateActorReference()` method
- Unused variable: `$causer` in `actionRequiresApproval()` method
- **DEPRECATED METHOD STILL EXISTS**: `getPendingApprovals()` uses old `ApprovalRequest` model instead of `ApprovalAuditRequest`
  - Method is marked @deprecated but still functional and could be used incorrectly
  - Should be removed or updated to use ApprovalAuditRequest
- **DEPRECATED FUNCTION USAGE**: Uses `current_branch_id` which is deprecated
  - Multiple instances in `log()` and `logSync()` methods
  - Should be updated to use current branch context method

## To Be Done
- None identified - fully functional