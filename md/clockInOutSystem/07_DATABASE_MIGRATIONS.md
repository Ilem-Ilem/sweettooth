# 06_ENHANCED_UI_NOTIFICATIONS.md

## Enhanced UI Notifications System

**Date**: December 2025
**Version**: 1.0
**Status**: Design Complete

---

## Overview

The Enhanced UI Notifications system provides real-time feedback, proactive warnings, and intuitive user guidance for the clock in/out system. This creates a responsive, informative user experience that prevents errors and keeps users informed about their shift status.

---

## 1. Real-Time Shift Status Dashboard

### Enhanced HeaderClockInOut Component

```php
<?php

namespace App\Livewire\BranchDashboard;

use Livewire\Component;
use Livewire\Attributes\{On, Url};
use App\Models\{Shift as ShiftModel, Branch, Department};
use App\Services\ShiftTimingValidator;
use Carbon\Carbon;
use TallStackUi\Traits\Interactions;

class HeaderClockInOut extends Component
{
    use Interactions;

    #[Url(keep: true)]
    public ?string $b_id = null;

    public $currentShift = null;
    public $hasActiveShift = false;
    public $timeWorked = '0h 0m';
    public $timeRemaining = null;
    public $shiftWarnings = [];

    // Real-time update every 30 seconds
    public function mount()
    {
        $this->loadCurrentShift();
        $this->checkShiftWarnings();
    }

    public function loadCurrentShift()
    {
        $employee_id = auth()->id();

        if (!$employee_id) {
            return;
        }

        $this->currentShift = ShiftModel::where('employee_id', $employee_id)
            ->where('shift_date', Carbon::today())
            ->where('status', 'active')
            ->with(['branch:id,name', 'configuration'])
            ->first();

        $this->hasActiveShift = $this->currentShift !== null;

        if ($this->hasActiveShift) {
            $this->calculateTimeWorked();
            $this->calculateTimeRemaining();
            $this->checkShiftWarnings();
        }
    }

    public function calculateTimeWorked()
    {
        if (!$this->currentShift || !$this->currentShift->clock_in) {
            $this->timeWorked = '0h 0m';
            return;
        }

        $now = Carbon::now();
        $totalMinutes = $this->currentShift->clock_in->diffInMinutes($now);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $this->timeWorked = "{$hours}h {$minutes}m";
    }

    public function calculateTimeRemaining()
    {
        if (!$this->currentShift || !isset($this->currentShift->configuration)) {
            $this->timeRemaining = null;
            return;
        }

        $config = $this->currentShift->configuration;
        $expectedEnd = Carbon::createFromTimeString($config->end_time)
            ->setDateFrom($this->currentShift->shift_date);

        $remainingMinutes = Carbon::now()->diffInMinutes($expectedEnd, false);

        $this->timeRemaining = $remainingMinutes > 0 ? $remainingMinutes : null;
    }

    public function checkShiftWarnings()
    {
        $this->shiftWarnings = [];

        if (!$this->currentShift) {
            return;
        }

        // Warning: Less than 30 minutes remaining
        if ($this->timeRemaining !== null && $this->timeRemaining <= 30) {
            $this->shiftWarnings[] = [
                'type' => 'time_remaining',
                'level' => 'warning',
                'message' => "Shift ends in {$this->timeRemaining} minutes",
                'icon' => '⏰'
            ];
        }

        // Critical: Less than 10 minutes remaining
        if ($this->timeRemaining !== null && $this->timeRemaining <= 10) {
            $this->shiftWarnings[] = [
                'type' => 'time_critical',
                'level' => 'danger',
                'message' => "Only {$this->timeRemaining} minutes left in shift!",
                'icon' => '🚨'
            ];
        }

        // Overtime warning (if configured)
        if ($this->isOvertimeApproaching()) {
            $this->shiftWarnings[] = [
                'type' => 'overtime_warning',
                'level' => 'info',
                'message' => 'Approaching overtime hours',
                'icon' => '⚠️'
            ];
        }
    }

    private function isOvertimeApproaching(): bool
    {
        if (!$this->currentShift || !isset($this->currentShift->configuration)) {
            return false;
        }

        $config = $this->currentShift->configuration;
        $workedMinutes = $this->currentShift->clock_in->diffInMinutes(Carbon::now());
        $overtimeThreshold = ($config->max_overtime_hours ?? 2) * 60 * 0.8; // 80% of max

        return $workedMinutes >= $overtimeThreshold;
    }

    public function redirectToShiftPage()
    {
        $branchId = $this->b_id ?: request()->query('b_id');

        return redirect()->route('branch-dashboard.select_shift', ['b_id' => $branchId]);
    }

    public function clockOut()
    {
        try {
            if (!$this->currentShift) {
                $this->toast()->error('No active shift found!')->send();
                return;
            }

            // ... existing clock out logic ...

            // Clear warnings after successful clock out
            $this->shiftWarnings = [];

            $this->toast()->success("Clocked out! Total time: {$totalHours}h {$totalMinutes}m")->send();

        } catch (\Exception $e) {
            $this->toast()->error('Error clocking out: ' . $e->getMessage())->send();
        }
    }

    #[On('shift-updated')]
    public function refreshShift()
    {
        $this->loadCurrentShift();
    }

    public function render()
    {
        // Update calculations on each render
        if ($this->hasActiveShift) {
            $this->calculateTimeWorked();
            $this->calculateTimeRemaining();
            $this->checkShiftWarnings();
        }

        return view('livewire.branch-dashboard.header-clock-in-out');
    }
}
```

---

## 2. Enhanced Header UI Component

### header-clock-in-out.blade.php with Advanced Features

```php
<div class="flex items-center gap-3 relative" wire:poll.30s>
    @if($hasActiveShift && $currentShift)
        <!-- Active Shift Display with Enhanced Features -->
        <div class="flex items-center gap-3">
            <!-- Shift Status Indicator -->
            <div class="relative">
                <!-- Warning Pulse for Critical Alerts -->
                @if(collect($shiftWarnings)->where('level', 'danger')->count() > 0)
                <div class="absolute -inset-2 bg-red-500 rounded-full animate-ping opacity-20"></div>
                @endif

                <!-- Main Status Display -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border-2 transition-all duration-300
                            @if(collect($shiftWarnings)->where('level', 'danger')->count() > 0)
                                bg-red-50 dark:bg-red-900/20 border-red-400 dark:border-red-700 text-red-700 dark:text-red-400
                            @elseif(collect($shiftWarnings)->where('level', 'warning')->count() > 0)
                                bg-yellow-50 dark:bg-yellow-900/20 border-yellow-400 dark:border-yellow-700 text-yellow-700 dark:text-yellow-400
                            @else
                                bg-green-50 dark:bg-green-900/20 border-green-400 dark:border-green-700 text-green-700 dark:text-green-400
                            @endif">

                    <!-- Animated Status Dot -->
                    <div class="flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full
                                       @if(collect($shiftWarnings)->where('level', 'danger')->count() > 0) bg-red-400
                                       @elseif(collect($shiftWarnings)->where('level', 'warning')->count() > 0) bg-yellow-400
                                       @else bg-green-400 @endif opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2
                                       @if(collect($shiftWarnings)->where('level', 'danger')->count() > 0) bg-red-500
                                       @elseif(collect($shiftWarnings)->where('level', 'warning')->count() > 0) bg-yellow-500
                                       @else bg-green-500 @endif"></span>
                        </span>
                        <span class="text-sm font-medium">Active</span>
                    </div>

                    <!-- Time Worked -->
                    <span class="text-xs opacity-90">{{ $timeWorked }}</span>
                </div>
            </div>

            <!-- Time Remaining Indicator -->
            @if($timeRemaining !== null)
            <div class="flex items-center gap-1 px-2 py-1 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-md">
                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs font-medium text-blue-700 dark:text-blue-400">
                    {{ $timeRemaining }}m left
                </span>
            </div>
            @endif

            <!-- Clock Out Button with Smart Styling -->
            <button wire:click="clockOut"
                    wire:confirm="Are you sure you want to clock out?"
                    class="px-3 py-1.5 rounded-lg transition-all duration-200 flex items-center gap-2 text-sm font-medium
                           @if(collect($shiftWarnings)->where('level', 'danger')->count() > 0)
                               bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-500/30 animate-pulse
                           @else
                               bg-red-600 hover:bg-red-700 text-white
                           @endif">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span wire:loading.remove wire:target="clockOut">Clock Out</span>
                <span wire:loading wire:target="clockOut">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>

            <!-- Shift Details (Desktop only) -->
            <div class="hidden lg:flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400">
                <span class="capitalize">{{ str_replace('_', ' ', $currentShift->shift_type) }}</span>
                <span>•</span>
                <span>Since {{ \Carbon\Carbon::parse($currentShift->clock_in)->format('h:i A') }}</span>
            </div>
        </div>

        <!-- Shift Warnings Display -->
        @if(!empty($shiftWarnings))
        <div class="absolute top-full mt-2 left-0 right-0 z-50">
            @foreach($shiftWarnings as $warning)
            <div class="mb-1 px-3 py-2 rounded-lg shadow-lg border-l-4 animate-fade-in-up
                        @if($warning['level'] === 'danger') bg-red-50 dark:bg-red-900/20 border-red-500 text-red-700 dark:text-red-400
                        @elseif($warning['level'] === 'warning') bg-yellow-50 dark:bg-yellow-900/20 border-yellow-500 text-yellow-700 dark:text-yellow-400
                        @else bg-blue-50 dark:bg-blue-900/20 border-blue-500 text-blue-700 dark:text-blue-400 @endif">

                <div class="flex items-center gap-2">
                    <span class="text-lg">{{ $warning['icon'] }}</span>
                    <span class="text-sm font-medium">{{ $warning['message'] }}</span>

                    @if($warning['type'] === 'time_critical')
                    <div class="ml-auto">
                        <div class="flex items-center gap-1 text-xs">
                            <span class="animate-pulse">⚠️</span>
                            <span>Urgent</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

    @else
        <!-- Clock In Button with Enhanced Styling -->
        <div class="relative">
            <button wire:click="redirectToShiftPage"
                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200 flex items-center gap-2 hover:shadow-lg group">
                <svg class="w-4 h-4 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Clock In</span>
            </button>

            <!-- No Active Shift Indicator -->
            <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                No active shift - click to start
            </div>
        </div>
    @endif
</div>

<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.3s ease-out forwards;
}

/* Pulse animation for critical warnings */
@keyframes critical-pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
    }
}

.animate-critical-pulse {
    animation: critical-pulse 1.5s infinite;
}
</style>
```

---

## 3. Proactive Notification System

### ShiftReminderService

```php
<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class ShiftReminderService
{
    /**
     * Send reminders for upcoming shift events
     */
    public function sendShiftReminders()
    {
        $this->sendPreShiftReminders();
        $this->sendShiftEndingReminders();
        $this->sendOvertimeWarnings();
        $this->sendAutoClockOutWarnings();
    }

    /**
     * Send reminders 30 minutes before shift starts
     */
    private function sendPreShiftReminders()
    {
        $upcomingShifts = Shift::where('status', 'scheduled')
            ->where('shift_date', Carbon::today())
            ->whereRaw('TIME(clock_in) BETWEEN TIME(DATE_ADD(NOW(), INTERVAL 25 MINUTE)) AND TIME(DATE_ADD(NOW(), INTERVAL 35 MINUTE))')
            ->with('employee')
            ->get();

        foreach ($upcomingShifts as $shift) {
            $minutesUntilStart = Carbon::now()->diffInMinutes($shift->clock_in);

            Notification::route('database', $shift->employee_id)
                ->notify(new ShiftStartingReminder($shift, $minutesUntilStart));
        }
    }

    /**
     * Send reminders when shift is ending soon
     */
    private function sendShiftEndingReminders()
    {
        $endingShifts = Shift::where('status', 'active')
            ->where('shift_date', Carbon::today())
            ->with(['employee', 'configuration'])
            ->get()
            ->filter(function ($shift) {
                if (!$shift->configuration) return false;

                $expectedEnd = Carbon::createFromTimeString($shift->configuration->end_time)
                    ->setDateFrom($shift->shift_date);

                $minutesUntilEnd = Carbon::now()->diffInMinutes($expectedEnd, false);
                return $minutesUntilEnd > 0 && $minutesUntilEnd <= 30; // Last 30 minutes
            });

        foreach ($endingShifts as $shift) {
            $expectedEnd = Carbon::createFromTimeString($shift->configuration->end_time)
                ->setDateFrom($shift->shift_date);

            $minutesUntilEnd = Carbon::now()->diffInMinutes($expectedEnd, false);

            Notification::route('database', $shift->employee_id)
                ->notify(new ShiftEndingReminder($shift, $minutesUntilEnd));
        }
    }

    /**
     * Send warnings when approaching overtime
     */
    private function sendOvertimeWarnings()
    {
        $overtimeShifts = Shift::where('status', 'active')
            ->with(['employee', 'configuration'])
            ->get()
            ->filter(function ($shift) {
                if (!$shift->configuration || !$shift->configuration->max_overtime_hours) {
                    return false;
                }

                $workedMinutes = $shift->clock_in->diffInMinutes(Carbon::now());
                $maxOvertimeMinutes = $shift->configuration->max_overtime_hours * 60;
                $warningThreshold = $maxOvertimeMinutes * 0.9; // 90% of max overtime

                return $workedMinutes >= $warningThreshold;
            });

        foreach ($overtimeShifts as $shift) {
            Notification::route('database', $shift->employee_id)
                ->notify(new OvertimeWarning($shift));
        }
    }

    /**
     * Send warnings before auto clock out
     */
    private function sendAutoClockOutWarnings()
    {
        $shiftsNeedingWarning = Shift::where('status', 'active')
            ->with(['employee', 'configuration'])
            ->get()
            ->filter(function ($shift) {
                if (!$shift->configuration) return false;

                $expectedEnd = Carbon::createFromTimeString($shift->configuration->end_time)
                    ->setDateFrom($shift->shift_date);

                $graceEnd = $expectedEnd->addMinutes($shift->configuration->auto_clock_out_minutes ?? 15);
                $warningTime = $graceEnd->subMinutes(5); // Warn 5 minutes before auto clock out

                return Carbon::now()->between($warningTime, $graceEnd);
            });

        foreach ($shiftsNeedingWarning as $shift) {
            $autoClockOutIn = Carbon::now()->diffInMinutes(
                Carbon::createFromTimeString($shift->configuration->end_time)
                    ->setDateFrom($shift->shift_date)
                    ->addMinutes($shift->configuration->auto_clock_out_minutes ?? 15)
            );

            Notification::route('database', $shift->employee_id)
                ->notify(new AutoClockOutWarning($shift, $autoClockOutIn));
        }
    }
}
```

---

## 4. Notification Classes

### ShiftStartingReminder

```php
<?php

namespace App\Notifications;

use App\Models\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftStartingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shift $shift,
        public int $minutesUntilStart
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'shift_reminder',
            'title' => 'Shift Starting Soon',
            'message' => "Your {$this->shift->shift_type} shift starts in {$this->minutesUntilStart} minutes",
            'shift_id' => $this->shift->id,
            'action_url' => route('branch-dashboard.index', ['b_id' => $this->shift->branch_id]),
            'icon' => '⏰'
        ];
    }

    public function toBroadcast($notifiable): array
    {
        return [
            'title' => 'Shift Starting Soon',
            'body' => "Your shift starts in {$this->minutesUntilStart} minutes",
            'icon' => '/notification-icon.png',
            'badge' => '/badge-icon.png',
            'data' => [
                'shift_id' => $this->shift->id,
                'url' => route('branch-dashboard.index', ['b_id' => $this->shift->branch_id])
            ]
        ];
    }
}
```

### ShiftEndingReminder

```php
<?php

namespace App\Notifications;

use App\Models\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ShiftEndingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shift $shift,
        public int $minutesUntilEnd
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        $urgency = $this->minutesUntilEnd <= 10 ? 'urgent' : 'normal';

        return [
            'type' => 'shift_warning',
            'title' => 'Shift Ending Soon',
            'message' => "Your shift ends in {$this->minutesUntilEnd} minutes",
            'shift_id' => $this->shift->id,
            'urgency' => $urgency,
            'action_url' => route('branch-dashboard.index', ['b_id' => $this->shift->branch_id]),
            'icon' => $urgency === 'urgent' ? '🚨' : '⏰'
        ];
    }
}
```

---

## 5. Real-Time Dashboard Updates

### Livewire Polling & Events

```php
// In dashboard layout - real-time shift status
<div wire:poll.30s="updateShiftStatus" class="shift-status-widget">
    <livewire:branch-dashboard.header-clock-in-out :b_id="$b_id" />
</div>

// In HeaderClockInOut component
public function updateShiftStatus()
{
    $this->loadCurrentShift();

    // Dispatch browser events for real-time UI updates
    if ($this->hasActiveShift) {
        $this->dispatch('shift-status-updated', [
            'has_shift' => true,
            'time_worked' => $this->timeWorked,
            'time_remaining' => $this->timeRemaining,
            'warnings' => $this->shiftWarnings
        ]);
    } else {
        $this->dispatch('shift-status-updated', [
            'has_shift' => false,
            'available_shifts' => $this->getAvailableShifts()
        ]);
    }
}
```

### JavaScript Real-Time Updates

```javascript
// In dashboard layout
document.addEventListener('livewire:initialized', () => {
    Livewire.on('shift-status-updated', (data) => {
        updateShiftStatusWidget(data);
    });
});

function updateShiftStatusWidget(data) {
    const widget = document.querySelector('.shift-status-widget');

    if (data.has_shift) {
        // Update time worked
        const timeElement = widget.querySelector('.time-worked');
        if (timeElement) {
            timeElement.textContent = data.time_worked;
        }

        // Update time remaining
        const remainingElement = widget.querySelector('.time-remaining');
        if (remainingElement && data.time_remaining) {
            remainingElement.textContent = `${data.time_remaining}m left`;
            remainingElement.classList.toggle('text-red-600', data.time_remaining <= 10);
        }

        // Show warnings
        if (data.warnings && data.warnings.length > 0) {
            showShiftWarnings(data.warnings);
        }
    } else {
        // Show available shifts
        if (data.available_shifts) {
            showAvailableShifts(data.available_shifts);
        }
    }
}

function showShiftWarnings(warnings) {
    const container = document.querySelector('.shift-warnings-container') || createWarningsContainer();

    container.innerHTML = '';

    warnings.forEach(warning => {
        const alert = document.createElement('div');
        alert.className = `alert alert-${warning.level} animate-fade-in`;
        alert.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="text-lg">${warning.icon}</span>
                <span>${warning.message}</span>
            </div>
        `;
        container.appendChild(alert);
    });
}
```

---

## 6. Mobile-Responsive Design

### Mobile-Optimized Shift Widget

```php
<!-- Mobile-specific shift display -->
<div class="lg:hidden fixed bottom-4 left-4 right-4 z-50">
    @if($hasActiveShift)
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <div>
                    <div class="text-sm font-medium">{{ $timeWorked }}</div>
                    @if($timeRemaining)
                    <div class="text-xs text-gray-500">{{ $timeRemaining }}m left</div>
                    @endif
                </div>
            </div>
            <button wire:click="clockOut" class="px-3 py-1 bg-red-600 text-white text-sm rounded">
                Out
            </button>
        </div>

        @if(!empty($shiftWarnings))
        <div class="mt-2 space-y-1">
            @foreach($shiftWarnings as $warning)
            <div class="text-xs px-2 py-1 rounded
                        @if($warning['level'] === 'danger') bg-red-100 text-red-700
                        @else bg-yellow-100 text-yellow-700 @endif">
                {{ $warning['icon'] }} {{ $warning['message'] }}
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @else
    <button wire:click="redirectToShiftPage"
            class="w-full bg-green-600 text-white py-3 rounded-lg shadow-lg">
        🕐 Clock In
    </button>
    @endif
</div>
```

---

## 7. Progressive Enhancement

### Graceful Degradation

```php
// Check for JavaScript availability
<noscript>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    <strong>JavaScript Disabled:</strong> Some real-time features may not work. Please enable JavaScript for the best experience.
                </p>
            </div>
        </div>
    </div>
</noscript>
```

### Accessibility Features

```php
<!-- Screen reader support -->
<div class="sr-only" aria-live="polite" aria-atomic="true">
    @if($hasActiveShift)
        Current shift active. Time worked: {{ $timeWorked }}.
        @if($timeRemaining) Time remaining: {{ $timeRemaining }} minutes. @endif
        @if(!empty($shiftWarnings))
            @foreach($shiftWarnings as $warning)
                {{ $warning['message'] }}
            @endforeach
        @endif
    @else
        No active shift. Click to start shift.
    @endif
</div>

<!-- Keyboard navigation -->
<button wire:click="clockOut"
        tabindex="0"
        aria-label="Clock out of current shift"
        class="focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 ...">
    Clock Out
</button>
```

---

## 8. Testing Strategy

### UI Component Tests

```php
<?php

namespace Tests\Feature\Components;

use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class HeaderClockInOutTest extends TestCase
{
    public function test_displays_active_shift_with_time_worked()
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();

        $shift = Shift::factory()->create([
            'employee_id' => $user->id,
            'branch_id' => $branch->id,
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(4),
            'shift_date' => Carbon::today(),
        ]);

        Livewire::actingAs($user)
            ->test(HeaderClockInOut::class, ['b_id' => $branch->id])
            ->assertSet('hasActiveShift', true)
            ->assertSet('timeWorked', '4h 0m');
    }

    public function test_shows_shift_warnings_when_time_running_out()
    {
        $user = User::factory()->create();
        $branch = Branch::factory()->create();

        $shift = Shift::factory()->create([
            'employee_id' => $user->id,
            'branch_id' => $branch->id,
            'status' => 'active',
            'clock_in' => Carbon::now()->subMinutes(30),
            'shift_date' => Carbon::today(),
        ]);

        // Mock configuration with short shift
        // ... configuration setup ...

        Livewire::actingAs($user)
            ->test(HeaderClockInOut::class, ['b_id' => $branch->id])
            ->assertSet('shiftWarnings.0.level', 'warning')
            ->assertSet('shiftWarnings.0.message', 'Shift ends in 30 minutes');
    }
}
```

### Notification Tests

```php
<?php

namespace Tests\Feature\Notifications;

use App\Models\Shift;
use App\Notifications\ShiftEndingReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ShiftNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_shift_ending_reminder_sent_at_correct_time()
    {
        Notification::fake();

        $shift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(7)->setTime(9, 0), // Started at 9 AM
            'shift_date' => Carbon::today(),
        ]);

        // Mock configuration for 8-hour shift
        // ... configuration setup ...

        // Simulate time being 2 PM (30 minutes before end)
        Carbon::setTestNow(Carbon::today()->setTime(14, 0));

        // Run reminder service
        app(ShiftReminderService::class)->sendShiftReminders();

        Notification::assertSentTo(
            $shift->employee,
            ShiftEndingReminder::class,
            function ($notification) {
                return $notification->minutesUntilEnd === 30;
            }
        );

        Carbon::setTestNow();
    }
}
```

---

## 9. Performance Optimization

### Caching Strategy

```php
// Cache shift status for short periods
public function getCachedShiftStatus(int $employeeId): array
{
    return Cache::remember(
        "shift_status_{$employeeId}",
        30, // 30 seconds
        function () use ($employeeId) {
            return $this->loadCurrentShiftData($employeeId);
        }
    );
}

// Clear cache on shift changes
public function clearShiftCache(int $employeeId): void
{
    Cache::forget("shift_status_{$employeeId}");
}
```

### Lazy Loading

```php
// Only load warnings when needed
public function getShiftWarnings(): array
{
    if (!$this->shouldCheckWarnings()) {
        return [];
    }

    return Cache::remember(
        "shift_warnings_{$this->currentShift->id}",
        60, // 1 minute
        function () {
            return $this->calculateWarnings();
        }
    );
}
```

---

**Document Information**
- **Prepared By**: UI/UX Team
- **Reviewed By**: Frontend & Backend Teams
- **Approved By**: Product Manager
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/06_ENHANCED_UI_NOTIFICATIONS.md