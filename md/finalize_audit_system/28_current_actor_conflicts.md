# current_actor() Function Conflicts

## Status: CRITICAL FUNCTION CONFLICT - WILL CAUSE AUDIT FAILURES

## Description
There are **TWO DIFFERENT** `current_actor()` functions defined in the codebase, causing unpredictable behavior in audit logging:

1. **AuthorizationHelper.php** (line 400): Returns `User|Employee|null`, calls `get_current_user()`
2. **BranchHelper.php** (line 101): Returns `User|null`, uses direct `Auth::user()`

## Critical Issues
- **AUDIT LOGGING FAILURE**: Wrong actor returned depending on which helper is loaded first
- **INCONSISTENT BEHAVIOR**: Some parts of code get User objects, others get User|Employee
- **SECURITY RISK**: Incorrect actor attribution in audit trails
- **APPROVAL WORKFLOW BREAKAGE**: Wrong requester/approver recorded

## Impact on Audit System
- Audit logs may attribute actions to wrong users
- Approval requests may show incorrect requesters
- Super admin bypass may fail in some contexts
- Audit trail integrity compromised

## Immediate Action Required
- Remove duplicate function definition
- Standardize on single `current_actor()` implementation
- Test all audit logging functionality after fix