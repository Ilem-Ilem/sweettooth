# System Notifications - User-Friendly Redesign

## Overview
These notifications handle system-level events including backup completions and stuck callback alerts.

---

## 1. BackupCompletedNotification

**File:** `app/Notifications/BackupCompletedNotification.php`

**Trigger:** When a database backup completes (successfully or fails).

**Recipients:** System administrators, IT team.

### Current Output (Problem)
```
Type: database_backup
Success: true
Message: Database backup completed successfully
Filename: backup_2026_02_26_120000.sql.gz
Size: 15.5 MB
Path: /storage/backups/...
Timestamp: 2026-02-26 12:00:00
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'database_backup',
    'title' => $success ? '✓ Database Backup Completed' : '⚠️ Database Backup Failed',
    'message' => $success 
        ? 'The scheduled database backup completed successfully.'
        : 'The database backup failed and requires attention.',
    'summary' => $success ? '15.5 MB backed up at 12:00 PM' : 'Error: Disk space insufficient',
    'context' => [
        'status' => $success ? 'Success' : 'Failed',
        'backup_type' => 'Scheduled Backup',
        'filename' => $success ? 'backup_2026_02_26_120000.sql.gz' : null,
        'file_size' => $success ? '15.5 MB' : null,
        'location' => 'SweetTooth Port Harcourt Server',
        'completed_at' => 'Feb 26, 2026 12:00 PM',
        'duration' => '2m 34s',
        'error_message' => $success ? null : 'Insufficient disk space on backup volume',
    ],
    'action_url' => $success ? '/admin/backups' : '/admin/backups/logs',
    'action_text' => $success ? 'View Backups' : 'View Error Log',
    // Internal use only:
    'success_raw' => $success,
    'backup_path' => '/storage/backups/...',
]
```

#### Email Notification (`toMail`) - Success
```
Subject: ✓ Database Backup Completed Successfully

Hi Admin,

The scheduled database backup has completed successfully.

Backup Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Status:            Success
Backup Type:       Scheduled Backup
File Size:         15.5 MB
Duration:          2m 34s
Location:          SweetTooth Port Harcourt Server
Completed:         Feb 26, 2026 at 12:00 PM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

File Information:
  • Filename:    backup_2026_02_26_120000.sql.gz
  • Format:      SQL (Gzip Compressed)
  • Location:    /storage/backups/2026/02/

Backup Statistics:
  • Tables Backed Up:    45
  • Records Processed:   125,430
  • Compression Ratio:   65%

Next Scheduled Backup: Feb 27, 2026 at 12:00 AM

[View All Backups →]

Best regards,
SweetTooth System Monitor
```

#### Email Notification (`toMail`) - Failed
```
Subject: ⚠️ Database Backup Failed - Action Required

Hi Admin,

The database backup has failed and requires your attention.

Backup Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Status:            Failed
Backup Type:       Scheduled Backup
Location:          SweetTooth Port Harcourt Server
Failed At:         Feb 26, 2026 at 12:00 PM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Error Details:
┌─────────────────────────────────────────────────────────┐
│ Error:      Insufficient disk space on backup volume    │
│ Code:       ENOSPC                                      │
│ Path:       /storage/backups/2026/02/                   │
└─────────────────────────────────────────────────────────┘

Recommended Actions:
  1. Check available disk space on the backup volume
  2. Clean up old backup files if necessary
  3. Verify backup volume is properly mounted
  4. Retry the backup operation

[View Error Log →]

Best regards,
SweetTooth System Monitor
```

---

## 2. StuckCallbackNotification

**File:** `app/Notifications/StuckCallbackNotification.php`

**Trigger:** When production callbacks are stuck and need attention.

**Recipients:** Production managers, system administrators.

### Current Output (Problem)
```
Type: stuck_callback_alert
Message: Stuck callbacks detected.
Branch: SweetTooth Port Harcourt
Summary: [{"production_id": 123, "hours_stuck": 4}, ...]
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'stuck_callback_alert',
    'title' => '⚠️ Production Callbacks Need Attention',
    'message' => 'Some production callbacks are stuck and require review.',
    'summary' => '3 callbacks stuck (oldest: 4 hours)',
    'context' => [
        'total_stuck' => '3 callbacks',
        'oldest_stuck' => '4 hours',
        'affected_department' => 'Production',
        'branch' => 'SweetTooth Port Harcourt',
        'alert_generated' => 'Feb 26, 2026 02:00 PM',
        'severity' => 'Medium',
    ],
    'stuck_callbacks' => [
        ['production_request' => 'PR-2026-00089', 'item' => 'Chocolate Cake 500g', 'stuck_since' => '10:00 AM', 'hours_stuck' => '4', 'stage' => 'Quality Check'],
        ['production_request' => 'PR-2026-00091', 'item' => 'Vanilla Cake 1kg', 'stuck_since' => '11:30 AM', 'hours_stuck' => '2.5', 'stage' => 'Packaging'],
        ['production_request' => 'PR-2026-00095', 'item' => 'Strawberry Muffins', 'stuck_since' => '12:00 PM', 'hours_stuck' => '2', 'stage' => 'Cooling'],
    ],
    'action_url' => '/branch-dashboard/production/callbacks/approve?b_id=xxx',
    'action_text' => 'Review Callbacks',
    // Internal use only:
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: ⚠️ Action Required: 3 Production Callbacks Stuck

Hi Production Team,

Some production callbacks are stuck and require your attention.

Alert Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total Stuck:       3 callbacks
Oldest Stuck:      4 hours
Affected Dept:     Production
Branch:            SweetTooth Port Harcourt
Alert Generated:   Feb 26, 2026 at 02:00 PM
Severity:          Medium
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Stuck Callbacks:
┌─────────────────────────────────────────────────────────┐
│ Request          Item               Stage         Time  │
├─────────────────────────────────────────────────────────┤
│ PR-2026-00089    Chocolate Cake     Quality Check 4h    │
│ PR-2026-00091    Vanilla Cake       Packaging     2.5h  │
│ PR-2026-00095    Strawberry Muffins Cooling       2h    │
└─────────────────────────────────────────────────────────┘

Impact:
  • Delayed orders: 3
  • Affected customers: 3
  • Potential revenue at risk: ₦45,000

Recommended Actions:
  1. Review each stuck callback
  2. Identify the bottleneck (equipment, staffing, materials)
  3. Approve or reject pending callbacks
  4. Escalate if systemic issues are identified

[Review Callbacks →]

Best regards,
SweetTooth Production System
```

---

## 3. HealthCheckAlert (System Level)

**File:** `app/Notifications/HealthCheckAlert.php`

**Trigger:** When system health checks identify issues.

**Recipients:** System administrators, IT team.

### Current Output (Problem)
```
Type: health_check_alert
Message: 4 health check(s) require action.
Branch: SweetTooth Port Harcourt
Checks: [{"type": "negative_stock", "item": "Flour"}, ...]
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'health_check_alert',
    'title' => '⚠️ System Health Issues Detected',
    'message' => 'The system health check has identified issues requiring attention.',
    'summary' => '4 issues found (2 critical, 1 warning, 1 info)',
    'context' => [
        'total_issues' => '4 issues',
        'critical' => '2',
        'warnings' => '1',
        'info' => '1',
        'branch' => 'SweetTooth Port Harcourt',
        'check_run' => 'Feb 26, 2026 06:00 AM',
    ],
    'health_issues' => [
        ['severity' => 'critical', 'category' => 'Inventory', 'issue' => 'Negative Stock', 'detail' => 'Flour: -5 kg', 'impact' => 'Affects production planning'],
        ['severity' => 'critical', 'category' => 'Inventory', 'issue' => 'Negative Stock', 'detail' => 'Sugar: -2 kg', 'impact' => 'Affects production planning'],
        ['severity' => 'warning', 'category' => 'Inventory', 'issue' => 'Near Expiry', 'detail' => 'Yeast expires in 7 days', 'impact' => 'May cause waste if not used'],
        ['severity' => 'info', 'category' => 'Inventory', 'issue' => 'Overstock', 'detail' => 'Packaging Boxes at 150% capacity', 'impact' => 'Ties up capital'],
    ],
    'action_url' => '/branch-dashboard/inventory/health?b_id=xxx',
    'action_text' => 'View Health Report',
    // Internal use only:
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: System Health Check: 4 Issues Require Attention

Hi Admin,

The automated system health check has identified issues.

Health Check Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Branch:            SweetTooth Port Harcourt
Check Run:         Feb 26, 2026 at 06:00 AM
Total Issues:      4
  🔴 Critical:     2
  🟡 Warning:      1
  ℹ️  Info:        1
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Critical Issues (Immediate Action Required):
┌─────────────────────────────────────────────────────────┐
│ 🔴 Negative Stock: Flour                                │
│    Current: -5 kg                                       │
│    Impact: Affects production planning                  │
├─────────────────────────────────────────────────────────┤
│ 🔴 Negative Stock: Sugar                                │
│    Current: -2 kg                                       │
│    Impact: Affects production planning                  │
└─────────────────────────────────────────────────────────┘

Warnings (Review Soon):
  🟡 Near Expiry: Yeast (expires in 7 days)
     Impact: May cause waste if not used

Informational (For Awareness):
  ℹ️  Overstock: Packaging Boxes (150% of max)
     Impact: Ties up capital

Recommended Actions:
  1. Investigate negative stock immediately
  2. Perform stock reconciliation
  3. Review inventory management procedures
  4. Use near-expiry items promptly

[View Full Health Report →]

Best regards,
SweetTooth System Monitor
```

---

## Blade Template Integration

### System Notification Display Components

```blade
{{-- Status Badge --}}
@if(!empty($notification->data['context']['status']))
    @php
        $statusConfig = [
            'Success' => ['class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'icon' => '✓'],
            'Failed' => ['class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400', 'icon' => '✗'],
            'Warning' => ['class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'icon' => '⚠'],
        ];
        $config = $statusConfig[$notification->data['context']['status']] ?? $statusConfig['Warning'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $config['class'] }}">
        <span class="mr-1">{{ $config['icon'] }}</span>
        {{ $notification->data['context']['status'] }}
    </span>
@endif

{{-- Severity Badge --}}
@if(!empty($notification->data['context']['severity']))
    @php
        $severityConfig = [
            'Critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'High' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            'Medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            'Low' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            'Info' => 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400',
        ];
        $severityClass = $severityConfig[$notification->data['context']['severity']] ?? $severityConfig['Info'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $severityClass }}">
        {{ $notification->data['context']['severity'] }}
    </span>
@endif

{{-- Issue Count Summary --}}
@if(!empty($notification->data['context']['critical']) || !empty($notification->data['context']['warnings']))
    <div class="mt-2 flex items-center gap-3 text-xs">
        @if(!empty($notification->data['context']['critical']))
            <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="font-medium">{{ $notification->data['context']['critical'] }} Critical</span>
            </span>
        @endif
        @if(!empty($notification->data['context']['warnings']))
            <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-medium">{{ $notification->data['context']['warnings'] }} Warning</span>
            </span>
        @endif
        @if(!empty($notification->data['context']['info']))
            <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                <span class="font-medium">{{ $notification->data['context']['info'] }} Info</span>
            </span>
        @endif
    </div>
@endif

{{-- Health Issues Table --}}
@if(!empty($notification->data['health_issues']))
    <div class="mt-4 space-y-2">
        @foreach($notification->data['health_issues'] as $issue)
            <div class="p-3 rounded-lg border 
                @if($issue['severity'] === 'critical') bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800
                @elseif($issue['severity'] === 'warning') bg-amber-50 dark:bg-amber-900/10 border-amber-200 dark:border-amber-800
                @else bg-blue-50 dark:bg-blue-900/10 border-blue-200 dark:border-blue-800
                @endif">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-2">
                        @if($issue['severity'] === 'critical')
                            <span class="text-red-500">🔴</span>
                        @elseif($issue['severity'] === 'warning')
                            <span class="text-amber-500">🟡</span>
                        @else
                            <span class="text-blue-500">ℹ️</span>
                        @endif
                        <div>
                            <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                                {{ $issue['category'] }}: {{ $issue['issue'] }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $issue['detail'] }}
                            </div>
                        </div>
                    </div>
                </div>
                @if(!empty($issue['impact']))
                    <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400">
                        <span class="font-medium">Impact:</span> {{ $issue['impact'] }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

{{-- Backup File Info --}}
@if(!empty($notification->data['context']['filename']))
    <div class="mt-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
        <div class="flex items-center gap-2 text-xs">
            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="text-zinc-600 dark:text-zinc-400">File:</span>
            <span class="font-mono text-zinc-800 dark:text-zinc-200">
                {{ $notification->data['context']['filename'] }}
            </span>
        </div>
        @if(!empty($notification->data['context']['file_size']))
            <div class="flex items-center gap-2 text-xs mt-1">
                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                <span class="text-zinc-600 dark:text-zinc-400">Size:</span>
                <span class="text-zinc-800 dark:text-zinc-200">{{ $notification->data['context']['file_size'] }}</span>
            </div>
        @endif
    </div>
@endif

{{-- Error Display --}}
@if(!empty($notification->data['context']['error_message']))
    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <div class="text-xs font-semibold text-red-700 dark:text-red-400 mb-1">
                    Error Details
                </div>
                <div class="text-sm text-red-600 dark:text-red-300 font-mono">
                    {{ $notification->data['context']['error_message'] }}
                </div>
            </div>
        </div>
    </div>
@endif
```

---

## Implementation Checklist

- [ ] Update `BackupCompletedNotification.php`
- [ ] Update `StuckCallbackNotification.php`
- [ ] Update `HealthCheckAlert.php`
- [ ] Add status badge component
- [ ] Add severity indicator
- [ ] Add health issues table display
- [ ] Add backup file info display
- [ ] Add error message styling
- [ ] Test all three notification types
- [ ] Verify email rendering
- [ ] Test with various severity levels
