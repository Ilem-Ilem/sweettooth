# Audit Management and Audit Request System in Accounting

## Overview

This documentation provides a comprehensive analysis of the audit management and approval request system in the SweetTooth application, specifically focusing on the accounting module. The system implements a sophisticated audit trail and approval workflow to ensure proper oversight of financial transactions and sensitive operations.

## Core Components

### 1. AuditLog Model
- **Location**: `app/Models/AuditLog.php`
- **Purpose**: Centralized audit trail for all system activities
- **Key Features**: 
  - Polymorphic relationships to track any model changes
  - Stores old and new values for change tracking
  - Captures IP addresses and user agents for security
  - Links to approval requests when applicable

### 2. ApprovalRequest Model
- **Location**: `app/Models/ApprovalRequest.php`
- **Purpose**: Manages approval workflows for sensitive operations
- **Key Features**:
  - Tracks request status (pending, approved, rejected)
  - Polymorphic relationships for flexible actor handling
  - Metadata storage for context information

### 3. AuditService
- **Location**: `app/Services/AuditService.php`
- **Purpose**: Centralized service for audit logging
- **Key Features**:
  - Unified logging interface for all modules
  - Sensitive action detection and approval requirements
  - Actor reference management
  - Bulk operations support

### 4. ApprovalAuditRequest Model
- **Location**: `app/Models/ApprovalAuditRequest.php`
- **Purpose**: Specialized approval requests with audit capabilities
- **Key Features**:
  - Extended approval request functionality
  - Payload storage for complex operations
  - Status tracking and comments

## Audit Management Implementation

### 1. Audit Trail Creation
The system automatically captures audit information for all significant operations:

```php
// From AuditService.php - core audit logging
public static function log(
    Model|string|null $causer,
    string $action,
    ?Model $auditable = null,
    ?string $description = null,
    string $status = 'completed',
    ?ApprovalRequest $approvalRequest = null,
    array $metadata = []
): AuditLog {
    // Extracts branch context
    $branchId = null;
    if ($auditable) {
        $branchId = $auditable->branch_id ?? $auditable->branch ?? null;
    }
    
    // Creates comprehensive audit log
    $auditLog = AuditLog::create([
        'branch_id' => $branchId,
        'causer_type' => $causer ? get_class($causer) : null,
        'causer_id' => $causer?->id,
        'auditable_type' => $auditable ? get_class($auditable) : null,
        'auditable_id' => $auditable?->id,
        'action' => $action,
        'description' => $description,
        'old_values' => $auditable?->getOriginal() ?? null,
        'new_values' => $auditable?->getDirty() ?? null,
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'status' => $status,
        'approval_request_id' => $approvalRequest?->id,
        'logged_at' => now(),
        'details' => array_merge($metadata, [
            'causer' => $causer ? get_class($causer) : null,
            'auditable' => $auditable ? get_class($auditable) : null,
        ]),
    ]);
    
    return $auditLog;
}
```

### 2. Sensitive Action Detection
The system identifies operations that require approval:

```php
// From AuditService.php - sensitive action detection
protected static function actionRequiresApproval(Model $causer, string $action): bool
{
    $sensitiveActions = [
        'delete_product',
        'negative_stock_adjustment',
        'price_drop_below_cost',
        'mass_stock_reset',
        'delete_department',
        'disable_branch',
        'approve_sensitive_leave',
        'sync_roles',
        'sync_permissions',
    ];

    return in_array($action, $sensitiveActions);
}
```

### 3. Approval Workflow
For sensitive operations, the system creates approval requests:

```php
// From AuditService.php - sensitive action workflow
public static function logSensitiveAction(
    Model $causer,
    string $action,
    Model $auditable,
    ?string $reason = null,
    array $context = []
): array {
    $requiresApproval = self::actionRequiresApproval($causer, $action);

    if ($requiresApproval && !self::canBypassApproval($causer, $action)) {
        // Create approval request
        $approvalRequest = ApprovalRequest::create([
            'requested_by_id' => $causer->id,
            'requested_by_type' => get_class($causer),
            'action' => $action,
            'auditable_id' => $auditable->id,
            'auditable_type' => get_class($auditable),
            'status' => 'pending',
            'reason' => $reason,
            'metadata' => $context,
        ]);

        // Log as pending
        self::log(
            $causer,
            $action,
            $auditable,
            $reason,
            'pending',
            $approvalRequest,
            $context
        );

        return [
            'status' => 'pending',
            'approval_request' => $approvalRequest,
        ];
    }

    // Action approved or doesn't require approval
    self::log(
        $causer,
        $action,
        $auditable,
        $reason,
        'completed',
        null,
        $context
    );

    return [
        'status' => 'completed',
        'approval_request' => null,
    ];
}
```

## Accounting-Specific Audit Implementation

### 1. Journal Entry Auditing
The accounting system integrates with the audit service for all journal entry operations:

```php
// Example from GlEntry model - audit integration
class GlEntry extends Model
{
    protected static function booted()
    {
        static::created(function ($entry) {
            if (auth()->check()) {
                app(AccountingAuditService::class)->logEntryCreation($entry, auth()->user());
            }
        });

        static::updated(function ($entry) {
            if (auth()->check() && $entry->isDirty('status')) {
                if ($entry->status === 'posted') {
                    app(AccountingAuditService::class)->logEntryPosting($entry, auth()->user());
                }
            }
        });
    }
}
```

### 2. Accounting Audit Service
Specialized service for accounting-specific audit operations:

```php
// From AccountingAuditService.php - example structure
class AccountingAuditService
{
    public function logEntryCreation(GlEntry $entry, User $user): void
    {
        activity()
            ->performedOn($entry)
            ->causedBy($user)
            ->withProperties([
                'action' => 'create',
                'gl_account' => $entry->glAccount->account_number . ': ' . $entry->glAccount->account_name,
                'debit' => $entry->debit,
                'credit' => $entry->credit,
                'period' => $entry->period?->name,
                'status' => $entry->status,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log('gl_entry_created');
    }

    public function logEntryPosting(GlEntry $entry, User $user): void
    {
        activity()
            ->performedOn($entry)
            ->causedBy($user)
            ->withProperties([
                'action' => 'post',
                'previous_status' => $entry->getOriginal('status'),
                'new_status' => $entry->status,
                'posted_by' => $user->name,
                'posting_time' => now(),
                'account_balance_change' => $this->calculateAccountBalanceChange($entry),
            ])
            ->log('gl_entry_posted');
    }
}
```

### 3. Approval Integration for Accounting Operations
The system implements approval workflows for significant accounting operations:

```php
// From JournalEntryApprovalService - example structure
class JournalEntryApprovalService
{
    public function requestApproval(GlEntry $entry, string $reason, ?User $approver = null): ApprovalRequest
    {
        $requester = auth()->user();
        
        // Validate that requester is not the same as potential approver
        if ($approver && $approver->id === $entry->entered_by_id) {
            throw new AuthorizationException('Same user cannot create and approve their own entries');
        }

        $approvalRequest = ApprovalRequest::create([
            'requested_by_id' => $requester->id,
            'requested_by_type' => get_class($requester),
            'approver_id' => $approver?->id,
            'approver_type' => $approver ? get_class($approver) : null,
            'auditable_type' => get_class($entry),
            'auditable_id' => $entry->id,
            'action' => 'post_journal_entry',
            'status' => 'pending',
            'reason' => $reason,
            'branch_id' => $entry->branch_id,
        ]);

        // Send notification to approver
        if ($approver) {
            $approver->notify(new JournalEntryApprovalRequest($approvalRequest));
        }

        return $approvalRequest;
    }
}
```

## Audit Request Management

### 1. Request Lifecycle
The system manages the complete lifecycle of audit requests:

- **Pending**: Request submitted, awaiting approval
- **Approved**: Request approved by authorized personnel
- **Rejected**: Request denied with reason
- **Executed**: Approved action has been executed

### 2. Status Tracking
Each audit request tracks its status and associated metadata:

```php
// From ApprovalRequest model - status tracking
public function approve(Model $approver, ?string $notes = null): self
{
    $this->update([
        'status' => 'approved',
        'approved_at' => now(),
        'approved_by_id' => $approver->id,
        'approved_by_type' => get_class($approver),
    ]);

    return $this;
}

public function reject(Model $rejector, string $reason): self
{
    $this->update([
        'status' => 'rejected',
        'approved_at' => now(),
        'approved_by_id' => $rejector->id,
        'approved_by_type' => get_class($rejector),
        'rejection_reason' => $reason,
    ]);

    return $this;
}
```

### 3. Actor Management
The system supports multiple actor types (Users and Employees) with polymorphic relationships:

```php
// From ApprovalRequest model - polymorphic relationships
public function requestedBy(): MorphTo
{
    return $this->morphTo('requested_by', 'requested_by_type', 'requested_by_id');
}

public function auditable(): MorphTo
{
    return $this->morphTo('auditable', 'auditable_type', 'auditable_id');
}

public function approvedBy(): MorphTo
{
    return $this->morphTo('approved_by', 'approved_by_type', 'approved_by_id');
}
```

## Security and Compliance Features

### 1. Segregation of Duties
The system enforces separation of duties to prevent fraud:

```php
// Example validation for segregation of duties
public function validateSegregationOfDuties(ApprovalRequest $request, User $approver): bool
{
    return $request->requested_by_id !== $approver->id;
}
```

### 2. Permission-Based Access
The system uses Laravel's built-in permission system:

```php
// Example permission checks
public function canBypassApproval(Model $causer, string $action): bool
{
    // Super admins always bypass
    if (is_super_admin()) {
        return true;
    }

    // Check if model has a canBypassApproval method
    if (method_exists($causer, 'canBypassApproval')) {
        return $causer->canBypassApproval($action);
    }

    return false;
}
```

### 3. Comprehensive Logging
All operations are logged with detailed context:

- Who performed the action (causer)
- What was affected (auditable)
- When it happened (timestamp)
- From where (IP address)
- What changed (old vs new values)
- Approval status and context

## Reporting and Monitoring

### 1. Audit Trail Queries
The system provides methods to retrieve audit information:

```php
// From AuditService - audit retrieval methods
public static function getLogsFor(Model $auditable, ?string $action = null, int $limit = 100)
{
    $query = AuditLog::where('auditable_type', get_class($auditable))
        ->where('auditable_id', $auditable->id);

    if ($action) {
        $query->where('action', $action);
    }

    return $query->orderBy('logged_at', 'desc')->limit($limit)->get();
}

public static function getLogsByActor(Model $causer, ?string $action = null, int $limit = 100)
{
    $query = AuditLog::where('causer_type', get_class($causer))
        ->where('causer_id', $causer->id);

    if ($action) {
        $query->where('action', $action);
    }

    return $query->orderBy('logged_at', 'desc')->limit($limit)->get();
}
```

### 2. Compliance Reporting
The system supports generation of compliance reports for auditing purposes.

## Benefits

### 1. Enhanced Security
- Comprehensive audit trails for all operations
- Segregation of duties enforcement
- Permission-based access control

### 2. Regulatory Compliance
- Detailed logging for audit requirements
- Approval workflows for sensitive operations
- Change tracking with before/after values

### 3. Operational Efficiency
- Automated approval workflows
- Centralized audit management
- Flexible actor reference system

### 4. Data Integrity
- Validation of sensitive operations
- Prevention of unauthorized changes
- Comprehensive change tracking

This audit management system provides a robust foundation for maintaining accountability, security, and compliance in the accounting operations of the SweetTooth application.