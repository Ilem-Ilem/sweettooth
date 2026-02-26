# Employee Notifications - User-Friendly Redesign

## Overview
These notifications handle employee-related events including creation, updates, role changes, and deletions in the HR/Employee module.

---

## 1. EmployeeCreatedNotification

**File:** `app/Notifications/EmployeeCreatedNotification.php`

**Trigger:** When a new employee is added to the system.

**Recipients:** HR team, department heads, management.

### Current Output (Problem)
```
Type: employee_created
Message: Employee John Doe created.
Employee id: 123
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'employee_created',
    'title' => 'New Employee Added',
    'message' => 'A new employee has been added to the system.',
    'summary' => 'John Doe - Production Assistant',
    'context' => [
        'employee_name' => 'John Doe',
        'employee_number' => 'EMP-2026-0042',
        'position' => 'Production Assistant',
        'department' => 'Production',
        'branch' => 'SweetTooth Port Harcourt',
        'start_date' => 'Feb 25, 2026',
        'employment_type' => 'Full-time',
        'created_by' => 'Sarah Johnson (HR Manager)',
        'created_at' => 'Feb 25, 2026 09:00 AM',
    ],
    'action_url' => '/branch-dashboard/employee?b_id=xxx',
    'action_text' => 'View Employee',
    // Internal use only:
    'employee_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: New Employee Joined: John Doe

Hi [Recipient Name],

A new employee has been added to the team.

Employee Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Name:              John Doe
Employee Number:   EMP-2026-0042
Position:          Production Assistant
Department:        Production
Employment Type:   Full-time
Start Date:        Feb 25, 2026
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Added By:
  • Sarah Johnson (HR Manager)
  • Feb 25, 2026 at 09:00 AM

Please join us in welcoming John to the team!

[View Employee Profile →]

Best regards,
SweetTooth HR System
```

---

## 2. EmployeeUpdatedNotification

**File:** `app/Notifications/EmployeeUpdatedNotification.php`

**Trigger:** When an employee's information is updated.

**Recipients:** HR team, department heads, the employee (for certain changes).

### Current Output (Problem)
```
Type: employee_updated
Message: Employee John Doe updated.
Employee id: 123
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'employee_updated',
    'title' => 'Employee Record Updated',
    'message' => 'Employee information has been updated.',
    'summary' => 'John Doe - Position changed to Senior Production Assistant',
    'context' => [
        'employee_name' => 'John Doe',
        'employee_number' => 'EMP-2026-0042',
        'updated_fields' => 'Position, Department',
        'old_position' => 'Production Assistant',
        'new_position' => 'Senior Production Assistant',
        'old_department' => 'Production',
        'new_department' => 'Production (Supervisor Track)',
        'branch' => 'SweetTooth Port Harcourt',
        'updated_by' => 'Sarah Johnson (HR Manager)',
        'updated_at' => 'Feb 25, 2026 02:30 PM',
    ],
    'action_url' => '/branch-dashboard/employee/details?id=123&employee_number=EMP-2026-0042&b_id=xxx',
    'action_text' => 'View Employee',
    // Internal use only:
    'employee_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Employee Record Updated: John Doe

Hi [Recipient Name],

An employee's information has been updated.

Employee Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Name:              John Doe
Employee Number:   EMP-2026-0042
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Changes Made:
┌─────────────────────────────────────────────────────────┐
│ Field          Previous Value         New Value         │
├─────────────────────────────────────────────────────────┤
│ Position       Production Assistant   Senior Production │
│ Department     Production             Production        │
│                (Supervisor Track)                       │
└─────────────────────────────────────────────────────────┘

Updated By:
  • Sarah Johnson (HR Manager)
  • Feb 25, 2026 at 02:30 PM

[View Employee Profile →]

Best regards,
SweetTooth HR System
```

---

## 3. EmployeeRoleUpdatedNotification

**File:** `app/Notifications/EmployeeRoleUpdatedNotification.php`

**Trigger:** When an employee's system roles/permissions are changed.

**Recipients:** HR team, IT administrators, management.

### Current Output (Problem)
```
Type: employee_roles_updated
Message: Roles updated for John Doe.
Roles: ["Inventory Manager", "Report Viewer"]
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'employee_roles_updated',
    'title' => 'Employee Access Roles Updated',
    'message' => 'System access roles have been updated for this employee.',
    'summary' => 'John Doe - Now has: Inventory Manager, Report Viewer',
    'context' => [
        'employee_name' => 'John Doe',
        'employee_number' => 'EMP-2026-0042',
        'position' => 'Senior Production Assistant',
        'department' => 'Production',
        'roles_added' => 'Inventory Manager, Report Viewer',
        'roles_removed' => 'None',
        'current_roles' => 'Inventory Manager, Report Viewer, Employee',
        'branch' => 'SweetTooth Port Harcourt',
        'updated_by' => 'Admin User (System Administrator)',
        'updated_at' => 'Feb 25, 2026 03:00 PM',
    ],
    'action_url' => '/branch-dashboard/employee/details?id=123&employee_number=EMP-2026-0042&b_id=xxx',
    'action_text' => 'View Access Roles',
    // Internal use only:
    'employee_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
    'roles_array' => ['Inventory Manager', 'Report Viewer'],
]
```

#### Email Notification (`toMail`)
```
Subject: Access Roles Updated: John Doe

Hi [Recipient Name],

An employee's system access roles have been updated.

Employee Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Name:              John Doe
Employee Number:   EMP-2026-0042
Position:          Senior Production Assistant
Department:        Production
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Role Changes:
┌─────────────────────────────────────────────────────────┐
│ Roles Added:     Inventory Manager                      │
│                  Report Viewer                          │
├─────────────────────────────────────────────────────────┤
│ Roles Removed:   None                                   │
├─────────────────────────────────────────────────────────┤
│ Current Roles:   Inventory Manager                      │
│                  Report Viewer                          │
│                  Employee                               │
└─────────────────────────────────────────────────────────┘

Updated By:
  • Admin User (System Administrator)
  • Feb 25, 2026 at 03:00 PM

[View Access Roles →]

Best regards,
SweetTooth HR System
```

---

## 4. EmployeeDeletedNotification

**File:** `app/Notifications/EmployeeDeletedNotification.php`

**Trigger:** When an employee is removed/deactivated from the system.

**Recipients:** HR team, department heads, management.

### Current Output (Problem)
```
Type: employee_deleted
Message: Employee John Doe deleted.
Employee id: 123
Branch: SweetTooth Port Harcourt
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'employee_deleted',
    'title' => 'Employee Record Removed',
    'message' => 'An employee has been removed from the system.',
    'summary' => 'John Doe (EMP-2026-0042) - Production Assistant',
    'context' => [
        'employee_name' => 'John Doe',
        'employee_number' => 'EMP-2026-0042',
        'position' => 'Production Assistant',
        'department' => 'Production',
        'branch' => 'SweetTooth Port Harcourt',
        'deletion_type' => 'Deactivated',
        'deletion_reason' => 'Employment ended',
        'last_working_day' => 'Feb 24, 2026',
        'deleted_by' => 'Sarah Johnson (HR Manager)',
        'deleted_at' => 'Feb 25, 2026 09:00 AM',
    ],
    'action_url' => '/branch-dashboard/employee?b_id=xxx',
    'action_text' => 'View Employees',
    // Internal use only:
    'employee_id' => '123',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: Employee Record Removed: John Doe

Hi [Recipient Name],

An employee has been removed from the system.

Employee Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Name:              John Doe
Employee Number:   EMP-2026-0042
Position:          Production Assistant
Department:        Production
Branch:            SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Removal Information:
  • Type:            Deactivated
  • Reason:          Employment ended
  • Last Working Day: Feb 24, 2026
  • Processed By:    Sarah Johnson (HR Manager)
  • Processed At:    Feb 25, 2026 at 09:00 AM

Note: All system access has been revoked. Historical records 
remain accessible for compliance purposes.

[View Employee List →]

Best regards,
SweetTooth HR System
```

---

## 5. SalesProductionRequestCreatedNotification

**File:** `app/Notifications/SalesProductionRequestCreatedNotification.php`

**Trigger:** When a sales order is converted to a production request.

**Recipients:** Production department, production managers.

### Current Output (Problem)
```
Type: sales_production_request_created
Message: Sales request SPR-2025-00089 sent to production.
Sales request number: SPR-2025-00089
Requested by: John Doe
Sales department: Sales
Production departments: ["Production", "Packaging"]
Items: ["Cake 500g x 100 pcs (Production)", "Cake 500g Box x 100 pcs (Packaging)"]
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'sales_production_request_created',
    'title' => 'New Production Request from Sales',
    'message' => 'A new production request has been created from a sales order.',
    'summary' => 'SPR-2025-00089 - 2 departments, 5 items',
    'context' => [
        'request_number' => 'SPR-2025-00089',
        'sales_department' => 'Sales',
        'production_departments' => 'Production, Packaging',
        'item_count' => '5 items',
        'requested_by' => 'John Doe',
        'requester_role' => 'Sales Representative',
        'branch' => 'SweetTooth Port Harcourt',
        'submitted_at' => 'Feb 25, 2026 10:00 AM',
    ],
    'items_by_department' => [
        'Production' => [
            ['name' => 'Cake 500g', 'quantity' => '100', 'unit' => 'pcs'],
            ['name' => 'Cake 1kg', 'quantity' => '50', 'unit' => 'pcs'],
        ],
        'Packaging' => [
            ['name' => 'Cake 500g Box', 'quantity' => '100', 'unit' => 'pcs'],
            ['name' => 'Cake 1kg Box', 'quantity' => '50', 'unit' => 'pcs'],
            ['name' => 'Labels', 'quantity' => '150', 'unit' => 'pcs'],
        ],
    ],
    'action_url' => '/branch-dashboard/production/requests?b_id=xxx',
    'action_text' => 'View Request',
    // Internal use only:
    'sales_production_request_id' => '567',
    'branch_id' => '019c850c-7294-72ea-9ec5-1b00d6b0bdd8',
]
```

#### Email Notification (`toMail`)
```
Subject: New Production Request: SPR-2025-00089

Hi [Recipient Name],

A new production request has been created from a sales order.

Request Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Request Number:    SPR-2025-00089
Sales Department:  Sales
Requested By:      John Doe (Sales Representative)
Production Depts:  Production, Packaging
Total Items:       5 items
Branch:            SweetTooth Port Harcourt
Submitted:         Feb 25, 2026 at 10:00 AM
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Items by Department:

Production:
  • Cake 500g - 100 pcs
  • Cake 1kg - 50 pcs

Packaging:
  • Cake 500g Box - 100 pcs
  • Cake 1kg Box - 50 pcs
  • Labels - 150 pcs

Please review and schedule production accordingly.

[View Request →]

Best regards,
SweetTooth Production System
```

---

## Blade Template Integration

### Employee-Specific Display Components

```blade
{{-- Employee Avatar/Initials --}}
@if(!empty($notification->data['context']['employee_name']))
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-700 dark:text-indigo-400 font-semibold text-sm">
            {{ strtoupper(substr($notification->data['context']['employee_name'], 0, 1)) }}
            {{ strtoupper(substr(strstr($notification->data['context']['employee_name'], ' '), 1, 1)) }}
        </div>
        <div>
            <div class="font-medium text-zinc-900 dark:text-zinc-100">
                {{ $notification->data['context']['employee_name'] }}
            </div>
            @if(!empty($notification->data['context']['employee_number']))
                <div class="text-xs text-zinc-500 dark:text-zinc-400">
                    {{ $notification->data['context']['employee_number'] }}
                </div>
            @endif
        </div>
    </div>
@endif

{{-- Role Badges --}}
@if(!empty($notification->data['context']['current_roles']))
    <div class="mt-2 flex flex-wrap gap-1">
        @foreach(explode(', ', $notification->data['context']['current_roles']) as $role)
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                {{ $role }}
            </span>
        @endforeach
    </div>
@endif

{{-- Deletion Type Badge --}}
@if($notification->data['type'] === 'employee_deleted')
    @php
        $deletionTypeStyles = [
            'Deactivated' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'Deleted' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'Transferred' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        ];
        $style = $deletionTypeStyles[$notification->data['context']['deletion_type'] ?? 'Deactivated'] ?? $deletionTypeStyles['Deactivated'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $style }}">
        {{ $notification->data['context']['deletion_type'] }}
    </span>
@endif

{{-- Updated Fields Display --}}
@if(!empty($notification->data['context']['updated_fields']))
    <div class="mt-3 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
        <table class="min-w-full text-xs">
            <thead class="bg-zinc-50 dark:bg-zinc-800">
                <tr>
                    <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">Field</th>
                    <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">Previous</th>
                    <th class="px-3 py-2 text-left font-medium text-zinc-600 dark:text-zinc-300">New</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($notification->data['context']['old_position']))
                    <tr class="border-t border-zinc-100 dark:border-zinc-800">
                        <td class="px-3 py-2 text-zinc-600 dark:text-zinc-400">Position</td>
                        <td class="px-3 py-2 text-zinc-500">{{ $notification->data['context']['old_position'] }}</td>
                        <td class="px-3 py-2 text-green-600 dark:text-green-400 font-medium">
                            {{ $notification->data['context']['new_position'] }}
                        </td>
                    </tr>
                @endif
                @if(!empty($notification->data['context']['old_department']))
                    <tr class="border-t border-zinc-100 dark:border-zinc-800">
                        <td class="px-3 py-2 text-zinc-600 dark:text-zinc-400">Department</td>
                        <td class="px-3 py-2 text-zinc-500">{{ $notification->data['context']['old_department'] }}</td>
                        <td class="px-3 py-2 text-green-600 dark:text-green-400 font-medium">
                            {{ $notification->data['context']['new_department'] }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endif

{{-- Items by Department for Production Request --}}
@if(!empty($notification->data['items_by_department']))
    <div class="mt-4 space-y-3">
        @foreach($notification->data['items_by_department'] as $deptName => $items)
            <div>
                <div class="text-xs font-semibold text-zinc-600 dark:text-zinc-400 mb-1">
                    {{ $deptName }}
                </div>
                <ul class="text-xs text-zinc-700 dark:text-zinc-300 space-y-0.5">
                    @foreach($items as $item)
                        <li class="flex justify-between">
                            <span>{{ $item['name'] }}</span>
                            <span class="font-medium">{{ $item['quantity'] }} {{ $item['unit'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
@endif
```

---

## Employment Type Mapping

| Type Value | Display Label |
|------------|---------------|
| `full_time` | Full-time |
| `part_time` | Part-time |
| `contract` | Contract |
| `temporary` | Temporary |
| `internship` | Internship |

---

## Implementation Checklist

- [ ] Update `EmployeeCreatedNotification.php`
- [ ] Update `EmployeeUpdatedNotification.php`
- [ ] Update `EmployeeRoleUpdatedNotification.php`
- [ ] Update `EmployeeDeletedNotification.php`
- [ ] Update `SalesProductionRequestCreatedNotification.php`
- [ ] Add employee avatar component
- [ ] Add role badge styling
- [ ] Add comparison table for updates
- [ ] Test all five notification types
- [ ] Verify email rendering
- [ ] Test with various employment types
