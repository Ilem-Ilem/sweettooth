# ApprovalRequest.php

## Status: Fully Implemented (Legacy Model)

## Description
Legacy approval request model with detailed execution tracking. Stores approval requests with polymorphic requester/approver relationships and comprehensive payload storage. Includes executed_by tracking which the newer ApprovalAuditRequest model lacks.

## Key Features
- Polymorphic requester/approver relationships
- Payload storage for action data
- Status management (pending, approved, rejected)
- Execution tracking with executed_by_id, executed_by_type, executed_at
- Created_at and denied_at timestamps

## Faults
- Appears to be superseded by ApprovalAuditRequest model
- No branch context (unlike ApprovalAuditRequest)
- Still referenced in AuditService (should be migrated)

## To Be Done
- Consider deprecation in favor of ApprovalAuditRequest for consistency
- Migrate existing data if still in use