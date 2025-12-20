# RequiresApproval.php

## Status: Fully Implemented (Legacy Trait)

## Description
Legacy approval trait that provides basic approval workflow functionality. Automatically creates approval requests for model operations and handles approval checking. Uses the older ApprovalRequest model and provides integration with the audit system.

## Key Features
- Automatic approval request creation on model operations
- Integration with AuditService for logging
- Supports bypass for web guard users with superadmin role
- Flexible action-based approval requirements

## Faults
- Uses older ApprovalRequest model instead of ApprovalAuditRequest
- Less comprehensive than RequiresApprovalWorkflow trait
- No super admin bypass using the global helper function
- Legacy trait should be deprecated in favor of RequiresApprovalWorkflow

## To Be Done
- Consider migration to RequiresApprovalWorkflow for consistency
- Update to use ApprovalAuditRequest model