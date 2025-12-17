# 06 - Notifications and Alerts System for Production Progress

## Overview

This document outlines the comprehensive notifications and alerts system for production progress tracking. The system provides timely notifications about production status, delays, quality issues, and milestones to relevant stakeholders.

## Alert Types and Categories

### 1. Progress-Based Alerts

#### Delay Alerts
- **Purpose**: Notify when production falls behind schedule
- **Triggers**:
  - Progress < 50% when 75% of time elapsed
  - No progress updates for 2+ hours during active shift
  - Estimated completion time exceeded
- **Severity**: Medium to High
- **Recipients**: Production supervisors, department heads

#### Completion Alerts
- **Purpose**: Notify when production items are completed
- **Triggers**:
  - Item reaches 100% completion
  - Batch ready for dispatch
  - Shift production goals met
- **Severity**: Low
- **Recipients**: Production staff, inventory managers

#### Quality Alerts
- **Purpose**: Flag quality issues that need attention
- **Triggers**:
  - Rejection rate > 5%
  - Quality score < 80%
  - Temperature deviations (kitchen/gelato)
  - Weight variations (confectionaries)
- **Severity**: High to Critical
- **Recipients**: Quality control, department heads

### 2. Resource-Based Alerts

#### Inventory Alerts
- **Purpose**: Warn about ingredient shortages
- **Triggers**:
  - Ingredients below reorder point
  - Insufficient stock for planned production
  - Expiring ingredients within 24 hours
- **Severity**: Medium
- **Recipients**: Inventory managers, production planners

#### Equipment Alerts
- **Purpose**: Monitor equipment status
- **Triggers**:
  - Equipment malfunction
  - Maintenance overdue
  - Calibration required
- **Severity**: High
- **Recipients**: Maintenance team, department heads

### 3. Time-Based Alerts

#### Shift Alerts
- **Purpose**: Manage shift handovers and deadlines
- **Triggers**:
  - 30 minutes before shift end with incomplete items
  - Shift handover with pending items
  - Overtime required to complete production
- **Severity**: Medium
- **Recipients**: Current shift staff, next shift staff

## Notification Channels

### 1. In-App Notifications

#### Live Dashboard Alerts
```php
<!-- resources/views/components/production-alert-banner.blade.php -->
@props([
    'alerts' => [],
    'position' => 'top-right' // top-right, top-left, bottom-right, bottom-left
])

@php
    $positionClasses = [
        'top-right' => 'top-4 right-4',
        'top-left' => 'top-4 left-4',
        'bottom-right' => 'bottom-4 right-4',
        'bottom-left' => 'bottom-4 left-4'
    ];

    $severityClasses = [
        'low' => 'bg-blue-50 border-blue-200 text-blue-800',
        'medium' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
        'high' => 'bg-orange-50 border-orange-200 text-orange-800',
        'critical' => 'bg-red-50 border-red-200 text-red-800'
    ];
@endphp

<div class="fixed {{ $positionClasses[$position] ?? 'top-4 right-4' }} z-50 space-y-2 max-w-sm">
    @foreach($alerts as $alert)
        <div
            class="alert-item {{ $severityClasses[$alert['severity']] ?? 'bg-gray-50 border-gray-200 text-gray-800' }}
                       border rounded-lg p-4 shadow-lg transform transition-all duration-300 ease-in-out
                       {{ $alert['show'] ? 'translate-x-0 opacity-100' : 'translate-x-full opacity-0' }}"
            data-alert-id="{{ $alert['id'] }}"
        >
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-2">
                    <div class="flex-shrink-0">
                        {!! $alert['icon'] !!}
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold">{{ $alert['title'] }}</h4>
                        <p class="text-xs opacity-90">{{ $alert['timestamp']->diffForHumans() }}</p>
                    </div>
                </div>
                <button
                    class="dismiss-alert flex-shrink-0 ml-4 text-current opacity-50 hover:opacity-75"
                    data-alert-id="{{ $alert['id'] }}"
                >
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- Message -->
            <p class="text-sm mt-2">{{ $alert['message'] }}</p>

            <!-- Actions -->
            @if(isset($alert['actions']) && count($alert['actions']) > 0)
            <div class="flex space-x-2 mt-3">
                @foreach($alert['actions'] as $action)
                <button
                    class="px-3 py-1 text-xs rounded {{ $action['style'] }} transition-colors"
                    wire:click="{{ $action['method'] }}"
                    @if(isset($action['params']))
                        wire:params="{{ json_encode($action['params']) }}"
                    @endif
                >
                    {{ $action['label'] }}
                </button>
                @endforeach
            </div>
            @endif
        </div>
    @endforeach
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    // Auto-dismiss alerts after timeout
    document.querySelectorAll('.alert-item').forEach(alert => {
        const alertId = alert.dataset.alertId;
        const severity = alert.dataset.severity;

        const timeouts = {
            'low': 5000,
            'medium': 10000,
            'high': 15000,
            'critical': 0 // Never auto-dismiss critical alerts
        };

        const timeout = timeouts[severity];
        if (timeout > 0) {
            setTimeout(() => {
                dismissAlert(alertId);
            }, timeout);
        }
    });

    // Handle dismiss buttons
    document.addEventListener('click', (e) => {
        if (e.target.closest('.dismiss-alert')) {
            const alertId = e.target.closest('.dismiss-alert').dataset.alertId;
            dismissAlert(alertId);
        }
    });

    function dismissAlert(alertId) {
        const alert = document.querySelector(`[data-alert-id="${alertId}"]`);
        if (alert) {
            alert.style.transform = 'translateX(100%)';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);

            // Notify server
            $wire.call('dismissAlert', alertId);
        }
    }
});
</script>
```

#### Notification Center
```php
<!-- resources/views/livewire/components/notification-center.blade.php -->
<div class="notification-center">
    <div class="flex items-center justify-between p-4 border-b">
        <h3 class="text-lg font-semibold">Notifications</h3>
        <div class="flex items-center space-x-2">
            <button wire:click="markAllRead" class="text-sm text-blue-600 hover:text-blue-700">
                Mark all read
            </button>
            <button wire:click="clearAll" class="text-sm text-red-600 hover:text-red-700">
                Clear all
            </button>
        </div>
    </div>

    <div class="max-h-96 overflow-y-auto">
        @forelse($notifications as $notification)
        <div class="notification-item p-4 border-b hover:bg-gray-50 {{ $notification['read'] ? 'opacity-60' : '' }}">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    {!! $notification['icon'] !!}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                    <p class="text-sm text-gray-600">{{ $notification['message'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $notification['created_at']->diffForHumans() }}</p>
                </div>
                @if(!$notification['read'])
                <div class="flex-shrink-0">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                </div>
                @endif
            </div>

            @if(isset($notification['actions']))
            <div class="flex space-x-2 mt-3">
                @foreach($notification['actions'] as $action)
                <button
                    wire:click="{{ $action['method'] }}"
                    class="px-3 py-1 text-xs rounded {{ $action['style'] }}"
                >
                    {{ $action['label'] }}
                </button>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7v5l-5 5V7h5z"/>
            </svg>
            <p>No notifications</p>
        </div>
        @endforelse
    </div>
</div>
```

### 2. Email Notifications

#### Alert Email Templates
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductionAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $alertData;
    public $user;

    public function __construct(array $alertData, $user)
    {
        $this->alertData = $alertData;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $severity = $this->alertData['severity'];
        $subject = $this->getSubjectForSeverity($severity);

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.production-alert',
            with: [
                'alert' => $this->alertData,
                'user' => $this->user,
            ]
        );
    }

    private function getSubjectForSeverity(string $severity): string
    {
        $prefixes = [
            'low' => 'Production Update:',
            'medium' => 'Production Alert:',
            'high' => 'Urgent Production Alert:',
            'critical' => 'CRITICAL Production Alert:',
        ];

        return ($prefixes[$severity] ?? 'Production Alert:') . ' ' . $this->alertData['title'];
    }
}
```

```html
<!-- resources/views/emails/production-alert.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $alert['title'] }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <!-- Header -->
        <div style="background-color: {{ $severityColors[$alert['severity']] }}; color: white; padding: 20px; border-radius: 5px 5px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">
                {{ $alertIcons[$alert['severity']] }} {{ $alert['title'] }}
            </h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">
                {{ $alert['timestamp']->format('M d, Y g:i A') }}
            </p>
        </div>

        <!-- Content -->
        <div style="background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px;">
            <p style="font-size: 16px; margin: 0 0 20px 0;">
                {{ $alert['message'] }}
            </p>

            <!-- Alert Details -->
            @if(isset($alert['details']))
            <div style="background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin: 0 0 10px 0; color: #666;">Details:</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($alert['details'] as $detail)
                    <li>{{ $detail }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Actions -->
            @if(isset($alert['actions']))
            <div style="margin: 20px 0;">
                <p style="margin: 0 0 10px 0;"><strong>Recommended Actions:</strong></p>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($alert['actions'] as $action)
                    <li>{{ $action }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- View in Dashboard -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('branch-dashboard.production.index', ['b_id' => $user->branch_id]) }}"
                   style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    View in Dashboard
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin: 20px 0; color: #666; font-size: 12px;">
            <p>This is an automated notification from the SweetTooth Production System.</p>
            <p>You received this because you are monitoring production activities.</p>
        </div>
    </div>
</body>
</html>
```

### 3. SMS Notifications

#### Critical Alert SMS
```php
<?php

namespace App\Services;

use Twilio\Rest\Client;

class SmsNotificationService
{
    protected Client $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendCriticalAlert(string $phoneNumber, array $alertData): void
    {
        $message = $this->formatSmsMessage($alertData);

        try {
            $this->twilio->messages->create($phoneNumber, [
                'from' => config('services.twilio.from'),
                'body' => $message
            ]);

            Log::info('Critical alert SMS sent', [
                'phone' => $phoneNumber,
                'alert_type' => $alertData['type']
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send critical alert SMS', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function formatSmsMessage(array $alertData): string
    {
        $prefixes = [
            'critical' => '🚨 CRITICAL:',
            'high' => '⚠️ URGENT:',
            'medium' => '⚡ ALERT:',
        ];

        $prefix = $prefixes[$alertData['severity']] ?? 'ℹ️ INFO:';

        // Keep SMS under 160 characters
        $message = substr("{$prefix} {$alertData['message']}", 0, 150);

        if (strlen($message) < strlen("{$prefix} {$alertData['message']}")) {
            $message .= '...';
        }

        return $message;
    }
}
```

## Alert Management System

### 1. Alert Rules Engine

```php
<?php

namespace App\Services;

class AlertRulesEngine
{
    protected array $rules;

    public function __construct()
    {
        $this->rules = $this->loadAlertRules();
    }

    public function evaluateAlert($context, $data): ?array
    {
        foreach ($this->rules as $rule) {
            if ($this->matchesContext($rule, $context) && $this->evaluateConditions($rule, $data)) {
                return $this->generateAlert($rule, $data);
            }
        }

        return null;
    }

    private function loadAlertRules(): array
    {
        return [
            [
                'id' => 'production_delay',
                'context' => 'production_progress',
                'conditions' => [
                    ['field' => 'progress_percentage', 'operator' => '<', 'value' => 50],
                    ['field' => 'time_elapsed_percentage', 'operator' => '>', 'value' => 75]
                ],
                'severity' => 'medium',
                'message' => 'Production is significantly behind schedule',
                'channels' => ['in_app', 'email'],
                'recipients' => ['supervisor', 'department_head']
            ],
            [
                'id' => 'quality_issue',
                'context' => 'batch_quality',
                'conditions' => [
                    ['field' => 'quality_score', 'operator' => '<', 'value' => 80]
                ],
                'severity' => 'high',
                'message' => 'Quality standards not met for batch',
                'channels' => ['in_app', 'email', 'sms'],
                'recipients' => ['quality_control', 'supervisor']
            ],
            [
                'id' => 'inventory_shortage',
                'context' => 'inventory_check',
                'conditions' => [
                    ['field' => 'available_quantity', 'operator' => '<', 'value' => 10]
                ],
                'severity' => 'medium',
                'message' => 'Critical ingredient running low',
                'channels' => ['in_app', 'email'],
                'recipients' => ['inventory_manager', 'production_planner']
            ]
        ];
    }

    private function matchesContext(array $rule, string $context): bool
    {
        return $rule['context'] === $context;
    }

    private function evaluateConditions(array $rule, array $data): bool
    {
        foreach ($rule['conditions'] as $condition) {
            $fieldValue = data_get($data, $condition['field']);

            if (!$this->evaluateCondition($fieldValue, $condition['operator'], $condition['value'])) {
                return false;
            }
        }

        return true;
    }

    private function evaluateCondition($fieldValue, string $operator, $conditionValue): bool
    {
        return match ($operator) {
            '>' => $fieldValue > $conditionValue,
            '<' => $fieldValue < $conditionValue,
            '>=' => $fieldValue >= $conditionValue,
            '<=' => $fieldValue <= $conditionValue,
            '==' => $fieldValue == $conditionValue,
            '!=' => $fieldValue != $conditionValue,
            default => false
        };
    }

    private function generateAlert(array $rule, array $data): array
    {
        return [
            'id' => uniqid('alert_'),
            'type' => $rule['id'],
            'severity' => $rule['severity'],
            'title' => $this->interpolateMessage($rule['message'], $data),
            'message' => $this->interpolateMessage($rule['message'], $data),
            'channels' => $rule['channels'],
            'recipients' => $rule['recipients'],
            'data' => $data,
            'timestamp' => now(),
            'actions' => $rule['actions'] ?? []
        ];
    }

    private function interpolateMessage(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }

        return $template;
    }
}
```

### 2. Notification Preferences

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'alert_type',
        'channels', // JSON: ['in_app', 'email', 'sms']
        'quiet_hours_start',
        'quiet_hours_end',
        'enabled'
    ];

    protected $casts = [
        'channels' => 'array',
        'enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shouldNotify(string $channel, string $alertType): bool
    {
        if (!$this->enabled) return false;

        if (!in_array($channel, $this->channels)) return false;

        // Check quiet hours
        if ($this->quiet_hours_start && $this->quiet_hours_end) {
            $now = now()->format('H:i');
            if ($now >= $this->quiet_hours_start && $now <= $this->quiet_hours_end) {
                return false;
            }
        }

        return true;
    }
}
```

### 3. Alert Escalation

```php
<?php

namespace App\Services;

class AlertEscalationService
{
    public function escalateAlert(array $alert): void
    {
        $escalationRules = [
            'low' => ['wait_minutes' => 60, 'escalate_to' => 'medium'],
            'medium' => ['wait_minutes' => 30, 'escalate_to' => 'high'],
            'high' => ['wait_minutes' => 15, 'escalate_to' => 'critical'],
            'critical' => ['immediate_action' => true]
        ];

        $rule = $escalationRules[$alert['severity']] ?? null;

        if (!$rule) return;

        if (isset($rule['immediate_action'])) {
            $this->takeImmediateAction($alert);
            return;
        }

        // Schedule escalation
        dispatch(new EscalateAlert($alert['id']))
            ->delay(now()->addMinutes($rule['wait_minutes']));
    }

    private function takeImmediateAction(array $alert): void
    {
        // Send SMS to all department heads
        // Trigger emergency protocols
        // Log critical incident

        Log::critical('Critical production alert triggered', $alert);

        // Notify emergency contacts
        $this->notifyEmergencyContacts($alert);
    }

    private function notifyEmergencyContacts(array $alert): void
    {
        $emergencyContacts = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['emergency_contact', 'plant_manager']);
        })->get();

        foreach ($emergencyContacts as $contact) {
            // Send immediate SMS
            app(SmsNotificationService::class)->sendCriticalAlert(
                $contact->phone,
                $alert
            );
        }
    }
}
```

## Performance and Reliability

### 1. Alert Deduplication

```php
<?php

namespace App\Services;

class AlertDeduplicationService
{
    public function isDuplicate(array $newAlert, int $timeWindowMinutes = 5): bool
    {
        $recentAlerts = Cache::get('recent_alerts', collect());

        $duplicate = $recentAlerts->first(function($existingAlert) use ($newAlert) {
            return $existingAlert['type'] === $newAlert['type'] &&
                   $existingAlert['context_id'] === $newAlert['context_id'] &&
                   abs($existingAlert['timestamp']->diffInMinutes($newAlert['timestamp'])) < 5;
        });

        if (!$duplicate) {
            // Add to recent alerts
            $recentAlerts->push($newAlert);
            $recentAlerts = $recentAlerts->take(100); // Keep last 100
            Cache::put('recent_alerts', $recentAlerts, now()->addMinutes(30));
        }

        return $duplicate !== null;
    }
}
```

### 2. Alert Queue Management

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\NotificationService;

class SendProductionAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $alert;
    public array $recipients;
    public string $channel;

    public function __construct(array $alert, array $recipients, string $channel)
    {
        $this->alert = $alert;
        $this->recipients = $recipients;
        $this->channel = $channel;
    }

    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendAlert($this->alert, $this->recipients, $this->channel);

        // Log successful delivery
        Log::info('Production alert sent', [
            'alert_id' => $this->alert['id'],
            'channel' => $this->channel,
            'recipients_count' => count($this->recipients)
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send production alert', [
            'alert_id' => $this->alert['id'],
            'channel' => $this->channel,
            'error' => $exception->getMessage()
        ]);
    }
}
```

This completes the notifications and alerts system. The system provides comprehensive coverage for all production progress scenarios with appropriate escalation and multiple notification channels.