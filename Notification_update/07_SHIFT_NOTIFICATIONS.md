# Shift Management Notifications - User-Friendly Redesign

## Overview
These notifications handle employee shift management including reminders, warnings, completion notifications, and auto clock-out alerts.

---

## 1. ShiftReminder

**File:** `app/Notifications/ShiftReminder.php`

**Trigger:** Scheduled reminder before a shift starts.

**Recipients:** Employees with upcoming shifts.

### Current Output (Problem)
```
Type: shift_reminder
Message: Reminder: Your morning shift is scheduled for 6:00 AM
Shift type: morning
Scheduled time: 2026-02-26 06:00:00
Time window: 6:00 AM - 12:00 PM
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'shift_reminder',
    'title' => 'Upcoming Shift Reminder',
    'message' => "Don't forget! Your {$shiftType} shift starts at {$scheduledTime}.",
    'summary' => 'Morning Shift • Today at 6:00 AM',
    'context' => [
        'shift_type' => 'Morning Shift',
        'shift_date' => 'Feb 26, 2026',
        'clock_in_time' => '6:00 AM',
        'clock_out_time' => '12:00 PM',
        'time_window' => '6:00 AM - 12:00 PM',
        'clock_in_deadline' => '6:15 AM',
        'location' => 'SweetTooth Port Harcourt',
        'reminder_sent' => 'Feb 25, 2026 08:00 PM',
    ],
    'action_url' => '/branch-dashboard/select_shift',
    'action_text' => 'Go to Dashboard',
    // Internal use only:
    'shift_type_raw' => 'morning',
    'scheduled_time_raw' => '2026-02-26 06:00:00',
]
```

#### Email Notification (`toMail`)
```
Subject: 📅 Reminder: Your Morning Shift Tomorrow at 6:00 AM

Hi [Employee Name],

This is a friendly reminder about your upcoming shift.

Shift Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Shift Type:        Morning Shift
Date:              Feb 26, 2026 (Tomorrow)
Time:              6:00 AM - 12:00 PM
Location:          SweetTooth Port Harcourt
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Important Times:
  • Earliest Clock-In:  5:45 AM
  • Shift Starts:       6:00 AM
  • Latest Clock-In:    6:15 AM (15 min grace period)
  • Shift Ends:         12:00 PM

Remember:
  ✓ Arrive at least 10 minutes early
  ✓ Clock in within the allowed time window
  ✓ Wear your uniform and ID badge
  ✓ Review your assigned tasks for the day

[Go to Dashboard →]

Best regards,
SweetTooth Management Team
```

---

## 2. ShiftEndingWarning

**File:** `app/Notifications/ShiftEndingWarning.php`

**Trigger:** Warning notification when a shift is about to end.

**Recipients:** Employees currently on shift.

### Current Output (Problem)
```
Type: shift_ending_warning
Message: Your morning shift will end in 15 minutes
Shift type: morning
Minutes remaining: 15
Clock in time: 2026-02-26 06:00:00
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'shift_ending_warning',
    'title' => 'Shift Ending Soon',
    'message' => "Your {$shiftType} shift will end in {$minutesRemaining} minutes.",
    'summary' => 'Morning Shift ends in 15 minutes',
    'context' => [
        'shift_type' => 'Morning Shift',
        'shift_date' => 'Feb 26, 2026',
        'clock_in_time' => '6:00 AM',
        'expected_end' => '12:00 PM',
        'minutes_remaining' => '15 minutes',
        'hours_worked' => '5h 45m',
        'branch' => 'SweetTooth Port Harcourt',
    ],
    'action_url' => '/branch-dashboard/select_shift?b_id=xxx',
    'action_text' => 'Clock Out',
    // Internal use only:
    'shift_id' => '456',
    'shift_type_raw' => 'morning',
    'minutes_remaining_raw' => 15,
]
```

#### Email Notification (`toMail`)
```
Subject: ⚠️ Your Shift Ends in 15 Minutes

Hi [Employee Name],

Your current shift will be ending soon.

Shift Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Shift Type:        Morning Shift
Date:              Feb 26, 2026
Clocked In:        6:00 AM
Expected End:      12:00 PM
Time Remaining:    15 minutes
Hours Worked:      5h 45m
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Before You Clock Out:
  ✓ Complete pending tasks
  ✓ Hand over to the next shift
  ✓ Clean and organize your workstation
  ✓ Report any issues to your supervisor
  ✓ Clock out before leaving

[Clock Out Now →]

Best regards,
SweetTooth Management Team
```

---

## 3. ShiftCompleted

**File:** `app/Notifications/ShiftCompleted.php`

**Trigger:** When a shift is completed (manually clocked out or auto-closed).

**Recipients:** Employees who completed the shift.

### Current Output (Problem)
```
Type: shift_completed
Message: Your morning shift has been completed (6h 0m)
Shift type: morning
Completion reason: manual
Clock in time: 2026-02-26 06:00:00
Clock out time: 2026-02-26 12:00:00
Total hours: 6
Total minutes: 0
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'shift_completed',
    'title' => $completionReason === 'auto' 
        ? 'Shift Automatically Closed' 
        : 'Shift Completed Successfully',
    'message' => $completionReason === 'auto'
        ? 'Your shift was automatically closed due to time limit.'
        : 'Great job! Your shift has been completed.',
    'summary' => 'Morning Shift - 6h 0m worked',
    'context' => [
        'shift_type' => 'Morning Shift',
        'shift_date' => 'Feb 26, 2026',
        'clock_in_time' => '6:00 AM',
        'clock_out_time' => '12:00 PM',
        'total_duration' => '6h 0m',
        'completion_type' => 'Manual Clock Out',
        'branch' => 'SweetTooth Port Harcourt',
        'completed_at' => 'Feb 26, 2026 12:00 PM',
    ],
    'action_url' => '/branch-dashboard/select_shift',
    'action_text' => 'View Dashboard',
    // Internal use only:
    'shift_id' => '456',
    'shift_type_raw' => 'morning',
    'completion_reason' => 'manual',
]
```

#### Email Notification (`toMail`)
```
Subject: ✓ Shift Completed: Morning Shift (Feb 26)

Hi [Employee Name],

Your shift has been completed successfully.

Shift Summary:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Shift Type:        Morning Shift
Date:              Feb 26, 2026
Clocked In:        6:00 AM
Clocked Out:       12:00 PM
Total Duration:    6h 0m
Completion:        Manual Clock Out
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Great work today! Thank you for your dedication.

Upcoming Shifts:
  • Tomorrow: Morning Shift (6:00 AM - 12:00 PM)
  • Feb 28: Off Day

[View Dashboard →]

Best regards,
SweetTooth Management Team
```

---

## 4. ShiftAutoClockOutImminent

**File:** `app/Notifications/ShiftAutoClockOutImminent.php`

**Trigger:** Urgent warning before automatic clock-out.

**Recipients:** Employees who haven't clocked out past shift end.

### Current Output (Problem)
```
Type: auto_clock_out_warning
Message: Your morning shift will auto clock out in 5 minutes
Shift type: morning
Minutes until auto clock: 5
Clock in time: 2026-02-26 06:00:00
Auto clock time: 2026-02-26 12:05:00
Urgent: true
```

### Proposed User-Friendly Output

#### Database Notification (`toArray`)
```php
[
    'type' => 'auto_clock_out_warning',
    'title' => '🚨 Urgent: Auto Clock-Out Warning',
    'message' => "Your shift will automatically close in {$minutesUntilAutoClockOut} minutes!",
    'summary' => 'Clock out now to avoid auto-closure in 5 minutes',
    'context' => [
        'shift_type' => 'Morning Shift',
        'shift_date' => 'Feb 26, 2026',
        'clock_in_time' => '6:00 AM',
        'scheduled_end' => '12:00 PM',
        'auto_clock_time' => '12:05 PM',
        'minutes_remaining' => '5 minutes',
        'hours_worked' => '5h 55m',
        'branch' => 'SweetTooth Port Harcourt',
        'warning' => 'Auto clock-out may require additional approval',
    ],
    'action_url' => '/branch-dashboard/select_shift?b_id=xxx',
    'action_text' => 'Clock Out Now',
    'urgent' => true,
    // Internal use only:
    'shift_id' => '456',
    'shift_type_raw' => 'morning',
    'minutes_until_auto_clock_raw' => 5,
]
```

#### Email Notification (`toMail`)
```
Subject: 🚨 URGENT: Auto Clock-Out in 5 Minutes!

Hi [Employee Name],

⚠️ Your shift will be automatically closed in 5 minutes!

Shift Details:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Shift Type:        Morning Shift
Date:              Feb 26, 2026
Clocked In:        6:00 AM
Scheduled End:     12:00 PM
Auto Clock-Out:    12:05 PM (in 5 minutes!)
Hours Worked:      5h 55m
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

⚠️ Important:
  • Auto clock-out will preserve your worked time
  • However, it may require additional approval
  • Please clock out manually to avoid any issues

[Clock Out NOW →]

If you believe this is an error, please contact your supervisor 
immediately.

Best regards,
SweetTooth Management Team
```

---

## Blade Template Integration

### Shift-Specific Display Components

```blade
{{-- Shift Type Badge with Icon --}}
@if(!empty($notification->data['context']['shift_type']))
    @php
        $shiftTypeConfig = [
            'Morning Shift' => ['icon' => '🌅', 'color' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
            'Afternoon Shift' => ['icon' => '☀️', 'color' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'],
            'Evening Shift' => ['icon' => '🌆', 'color' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'],
            'Night Shift' => ['icon' => '🌙', 'color' => 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400'],
            'Full Time' => ['icon' => '🕐', 'color' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
        ];
        $config = $shiftTypeConfig[$notification->data['context']['shift_type']] ?? $shiftTypeConfig['Full Time'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $config['color'] }}">
        <span class="mr-1">{{ $config['icon'] }}</span>
        {{ $notification->data['context']['shift_type'] }}
    </span>
@endif

{{-- Time Display with Icons --}}
@if(!empty($notification->data['context']['clock_in_time']))
    <div class="mt-2 space-y-1 text-xs">
        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span>Clocked In: <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $notification->data['context']['clock_in_time'] }}</span></span>
        </div>
        @if(!empty($notification->data['context']['clock_out_time']))
            <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                <span>Clocked Out: <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $notification->data['context']['clock_out_time'] }}</span></span>
            </div>
        @endif
    </div>
@endif

{{-- Duration/Time Remaining Display --}}
@if(!empty($notification->data['context']['minutes_remaining']))
    @php
        $minutes = (int) filter_var($notification->data['context']['minutes_remaining'], FILTER_SANITIZE_NUMBER_INT);
        $urgencyClass = $minutes <= 5 
            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 animate-pulse' 
            : ($minutes <= 15 
                ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' 
                : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400');
    @endphp
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $urgencyClass }}">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $notification->data['context']['minutes_remaining'] }} remaining
    </span>
@endif

@if(!empty($notification->data['context']['total_duration']))
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $notification->data['context']['total_duration'] }} worked
    </span>
@endif

{{-- Auto Clock-Out Warning Banner --}}
@if($notification->data['type'] === 'auto_clock_out_warning')
    <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border-2 border-red-300 dark:border-red-700 rounded-lg">
        <div class="flex items-start gap-2">
            <svg class="w-6 h-6 text-red-600 dark:text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <div class="text-sm font-bold text-red-700 dark:text-red-400">
                    Action Required!
                </div>
                <div class="text-sm text-red-600 dark:text-red-300 mt-1">
                    {{ $notification->data['context']['warning'] ?? 'Auto clock-out may require additional approval for your timesheet.' }}
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Completion Type Badge --}}
@if(!empty($notification->data['context']['completion_type']))
    @php
        $completionStyles = [
            'Manual Clock Out' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'Auto Clock Out' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
            'System Closed' => 'bg-slate-100 text-slate-700 dark:bg-slate-900/30 dark:text-slate-400',
        ];
        $style = $completionStyles[$notification->data['context']['completion_type']] ?? $completionStyles['Manual Clock Out'];
    @endphp
    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ $style }}">
        {{ $notification->data['context']['completion_type'] }}
    </span>
@endif

{{-- Shift Timeline Visualization --}}
<div class="mt-4 relative">
    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400 mb-1">
        <span>{{ $notification->data['context']['clock_in_time'] ?? 'Start' }}</span>
        <span>{{ $notification->data['context']['clock_out_time'] ?? $notification->data['context']['expected_end'] ?? 'End' }}</span>
    </div>
    <div class="h-2 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
        @if(!empty($notification->data['context']['minutes_remaining']))
            @php
                $remainingPercent = min(100, max(0, ((int) filter_var($notification->data['context']['minutes_remaining'], FILTER_SANITIZE_NUMBER_INT) / 60) * 100));
            @endphp
            <div class="h-full bg-gradient-to-r from-amber-500 to-red-500 rounded-full transition-all duration-300" 
                 style="width: {{ $remainingPercent }}%"></div>
        @else
            <div class="h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full" style="width: 100%"></div>
        @endif
    </div>
</div>
```

---

## Shift Type Configuration

| Shift Type | Display Name | Icon | Default Hours | Color |
|------------|--------------|------|---------------|-------|
| `morning` | Morning Shift | 🌅 | 6:00 AM - 12:00 PM | Amber |
| `afternoon` | Afternoon Shift | ☀️ | 12:00 PM - 8:00 PM | Orange |
| `evening` | Evening Shift | 🌆 | 4:00 PM - 10:00 PM | Indigo |
| `night` | Night Shift | 🌙 | 10:00 PM - 6:00 AM | Slate |
| `full_time` | Full Time | 🕐 | 8:00 AM - 5:00 PM | Blue |

---

## Implementation Checklist

- [ ] Update `ShiftReminder.php`
- [ ] Update `ShiftEndingWarning.php`
- [ ] Update `ShiftCompleted.php`
- [ ] Update `ShiftAutoClockOutImminent.php`
- [ ] Add shift type badge component
- [ ] Add time display component
- [ ] Add urgency indicator for auto clock-out
- [ ] Add shift timeline visualization
- [ ] Test all four notification types
- [ ] Verify email rendering
- [ ] Test urgency levels (5 min, 15 min, 30 min)
- [ ] Test with various shift types
