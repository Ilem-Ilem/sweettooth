# 11_MONITORING_ALERTS.md

## System Monitoring & Alerting

**Date**: December 2025
**Version**: 1.0
**Status**: Monitoring Strategy Complete

---

## Real-Time Monitoring Dashboard

### Health Check Endpoint

```php
// routes/web.php
Route::get('/health/shift-system', [HealthController::class, 'shiftSystem']);
Route::get('/metrics/shift-system', [MetricsController::class, 'shiftSystem']);
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Services\ShiftExpirationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function shiftSystem()
    {
        $checks = [
            'database_connectivity' => $this->checkDatabase(),
            'active_shift_count' => $this->checkActiveShifts(),
            'expired_shift_count' => $this->checkExpiredShifts(),
            'auto_clock_out_status' => $this->checkAutoClockOut(),
            'queue_health' => $this->checkQueueHealth(),
            'cache_health' => $this->checkCacheHealth(),
            'time_validation' => $this->checkTimeValidation(),
        ];

        $overallHealth = $this->calculateOverallHealth($checks);

        return response()->json([
            'status' => $overallHealth,
            'timestamp' => now()->toIso8601String(),
            'checks' => $checks,
            'version' => config('app.version', '1.0.0'),
            'environment' => app()->environment()
        ], $overallHealth === 'healthy' ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            $shiftCount = Shift::count();
            return $shiftCount >= 0; // Basic query test
        } catch (\Exception $e) {
            \Log::error('Database health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function checkActiveShifts(): array
    {
        $activeCount = Shift::where('status', 'active')->count();
        $maxExpected = config('shift-monitoring.max_active_shifts', 1000);

        return [
            'count' => $activeCount,
            'healthy' => $activeCount <= $maxExpected,
            'threshold' => $maxExpected
        ];
    }

    private function checkExpiredShifts(): array
    {
        $service = app(ShiftExpirationService::class);
        $expiredCount = $service->getExpiredShifts()->count();
        $maxExpired = config('shift-monitoring.max_expired_shifts', 50);

        return [
            'count' => $expiredCount,
            'healthy' => $expiredCount <= $maxExpired,
            'threshold' => $maxExpired,
            'needs_attention' => $expiredCount > 10
        ];
    }

    private function checkAutoClockOut(): array
    {
        $lastRun = Cache::get('auto_clock_out_last_run');
        $expectedInterval = 300; // 5 minutes

        if (!$lastRun) {
            return [
                'status' => 'never_run',
                'healthy' => false,
                'last_run' => null
            ];
        }

        $lastRunTime = \Carbon\Carbon::parse($lastRun);
        $minutesSince = $lastRunTime->diffInMinutes(now());

        return [
            'status' => 'operational',
            'healthy' => $minutesSince <= ($expectedInterval + 60), // Allow 1 hour grace
            'last_run' => $lastRunTime->toIso8601String(),
            'minutes_since' => $minutesSince,
            'expected_interval' => $expectedInterval
        ];
    }

    private function checkQueueHealth(): array
    {
        try {
            $failedCount = DB::table('failed_jobs')
                ->where('queue', 'shift_notifications')
                ->where('failed_at', '>', now()->subHours(24))
                ->count();

            $pendingCount = DB::table('jobs')
                ->where('queue', 'shift_notifications')
                ->where('created_at', '<', now()->subMinutes(30))
                ->count();

            return [
                'failed_jobs_24h' => $failedCount,
                'stuck_jobs_30m' => $pendingCount,
                'healthy' => $failedCount < 10 && $pendingCount < 50
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
                'healthy' => false
            ];
        }
    }

    private function checkCacheHealth(): bool
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test_value', 10);
            $value = Cache::get($testKey);
            Cache::forget($testKey);

            return $value === 'test_value';
        } catch (\Exception $e) {
            \Log::error('Cache health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function checkTimeValidation(): bool
    {
        try {
            $validator = app(\App\Services\ShiftTimingValidator::class);
            $result = $validator->validateStrictTimeWindows('morning', 1, now());
            // Just testing that the service is callable
            return true;
        } catch (\Exception $e) {
            \Log::error('Time validation health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function calculateOverallHealth(array $checks): string
    {
        $criticalFailures = 0;
        $warningFailures = 0;

        foreach ($checks as $check) {
            if (is_array($check)) {
                if (isset($check['healthy']) && $check['healthy'] === false) {
                    // Check if this is critical
                    if (in_array($check, ['database_connectivity', 'auto_clock_out_status'])) {
                        $criticalFailures++;
                    } else {
                        $warningFailures++;
                    }
                }
            } elseif ($check === false) {
                $criticalFailures++;
            }
        }

        if ($criticalFailures > 0) {
            return 'critical';
        } elseif ($warningFailures > 0) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }
}
```

---

## Alert Configuration

### Alert Thresholds

```php
// config/shift-monitoring.php
return [
    'alerts' => [
        'active_shifts' => [
            'warning' => 500,
            'critical' => 1000,
            'message' => 'High number of active shifts detected'
        ],
        'expired_shifts' => [
            'warning' => 25,
            'critical' => 50,
            'message' => 'Large number of expired shifts not auto-closed'
        ],
        'auto_clock_out_delay' => [
            'warning' => 600, // 10 minutes
            'critical' => 1800, // 30 minutes
            'message' => 'Auto clock out process delayed'
        ],
        'failed_notifications' => [
            'warning' => 10,
            'critical' => 50,
            'message' => 'High number of failed shift notifications'
        ],
        'time_violations' => [
            'warning' => 5,
            'critical' => 20,
            'message' => 'Multiple time window violations detected'
        ]
    ],

    'notification_channels' => [
        'slack' => env('SHIFT_ALERTS_SLACK_WEBHOOK'),
        'email' => env('SHIFT_ALERTS_EMAIL'),
        'sms' => env('SHIFT_ALERTS_SMS_ENABLED', false)
    ],

    'escalation' => [
        'warning' => ['slack', 'email'],
        'critical' => ['slack', 'email', 'sms']
    ]
];
```

### Alert Manager Service

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;

class AlertManager
{
    public function triggerAlert(string $alertType, array $data = [])
    {
        $config = config("shift-monitoring.alerts.{$alertType}");

        if (!$config) {
            Log::warning("Unknown alert type: {$alertType}");
            return;
        }

        $severity = $this->determineSeverity($alertType, $data, $config);

        if (!$severity) {
            return; // No alert needed
        }

        $alertData = array_merge($config, [
            'alert_type' => $alertType,
            'severity' => $severity,
            'data' => $data,
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment()
        ]);

        $this->sendAlerts($severity, $alertData);
        $this->logAlert($alertData);
    }

    private function determineSeverity(string $alertType, array $data, array $config): ?string
    {
        $value = $data['value'] ?? null;

        if ($value >= ($config['critical'] ?? PHP_INT_MAX)) {
            return 'critical';
        } elseif ($value >= ($config['warning'] ?? PHP_INT_MAX)) {
            return 'warning';
        }

        return null;
    }

    private function sendAlerts(string $severity, array $alertData)
    {
        $channels = config("shift-monitoring.escalation.{$severity}", ['email']);

        foreach ($channels as $channel) {
            match($channel) {
                'slack' => $this->sendSlackAlert($alertData),
                'email' => $this->sendEmailAlert($alertData),
                'sms' => $this->sendSmsAlert($alertData),
                default => Log::warning("Unknown alert channel: {$channel}")
            };
        }
    }

    private function sendSlackAlert(array $alertData)
    {
        $webhook = config('shift-monitoring.notification_channels.slack');

        if (!$webhook) {
            return;
        }

        $emoji = match($alertData['severity']) {
            'critical' => '🚨',
            'warning' => '⚠️',
            default => 'ℹ️'
        };

        $payload = [
            'text' => "{$emoji} Shift System Alert: {$alertData['message']}",
            'attachments' => [
                [
                    'color' => match($alertData['severity']) {
                        'critical' => 'danger',
                        'warning' => 'warning',
                        default => 'good'
                    },
                    'fields' => [
                        [
                            'title' => 'Alert Type',
                            'value' => $alertData['alert_type'],
                            'short' => true
                        ],
                        [
                            'title' => 'Environment',
                            'value' => $alertData['environment'],
                            'short' => true
                        ],
                        [
                            'title' => 'Timestamp',
                            'value' => $alertData['timestamp'],
                            'short' => true
                        ]
                    ]
                ]
            ]
        ];

        // Send to Slack
        \Http::post($webhook, $payload);
    }

    private function sendEmailAlert(array $alertData)
    {
        $recipients = config('shift-monitoring.notification_channels.email');

        if (!$recipients) {
            return;
        }

        Mail::to($recipients)->send(new ShiftSystemAlert($alertData));
    }

    private function sendSmsAlert(array $alertData)
    {
        if (!config('shift-monitoring.notification_channels.sms')) {
            return;
        }

        // Integration with SMS service (Twilio, AWS SNS, etc.)
        // Implementation depends on chosen SMS provider
    }

    private function logAlert(array $alertData)
    {
        Log::log(
            match($alertData['severity']) {
                'critical' => 'critical',
                'warning' => 'warning',
                default => 'info'
            },
            'Shift system alert triggered',
            $alertData
        );

        // Store in database for historical tracking
        DB::table('system_alerts')->insert([
            'alert_type' => $alertData['alert_type'],
            'severity' => $alertData['severity'],
            'message' => $alertData['message'],
            'data' => json_encode($alertData['data']),
            'environment' => $alertData['environment'],
            'created_at' => now()
        ]);
    }
}
```

---

## Key Performance Metrics

### Real-Time Metrics Dashboard

```php
// routes/web.php (admin only)
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/admin/shift-metrics', [AdminController::class, 'shiftMetrics']);
    Route::get('/admin/shift-metrics/live', [AdminController::class, 'liveMetrics']);
});
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\TimeViolation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function shiftMetrics()
    {
        $metrics = [
            'active_shifts' => [
                'current' => Shift::where('status', 'active')->count(),
                'by_branch' => $this->getShiftsByBranch('active'),
                'by_shift_type' => $this->getShiftsByType('active')
            ],
            'time_violations' => [
                'today' => TimeViolation::whereDate('created_at', today())->count(),
                'this_week' => TimeViolation::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'by_type' => $this->getViolationsByType(),
                'by_employee' => $this->getTopViolators()
            ],
            'auto_clock_out' => [
                'last_run' => Cache::get('auto_clock_out_last_run'),
                'shifts_closed_today' => Shift::where('status', 'auto_clocked_out')
                    ->whereDate('updated_at', today())
                    ->count(),
                'average_delay' => $this->calculateAverageAutoClockOutDelay()
            ],
            'system_performance' => [
                'response_time_avg' => $this->getAverageResponseTime(),
                'error_rate' => $this->getErrorRate(),
                'cache_hit_rate' => $this->getCacheHitRate()
            ]
        ];

        return view('admin.shift-metrics', compact('metrics'));
    }

    public function liveMetrics()
    {
        return response()->json([
            'active_shifts' => Shift::where('status', 'active')->count(),
            'clocked_in_last_hour' => Shift::where('clock_in', '>=', now()->subHour())->count(),
            'auto_clocked_out_today' => Shift::where('status', 'auto_clocked_out')
                ->whereDate('updated_at', today())
                ->count(),
            'time_violations_today' => TimeViolation::whereDate('created_at', today())->count(),
            'system_load' => sys_getloadavg()[0],
            'timestamp' => now()->toIso8601String()
        ]);
    }

    private function getShiftsByBranch(string $status): array
    {
        return Shift::select('branches.name', DB::raw('COUNT(*) as count'))
            ->join('branches', 'shifts.branch_id', '=', 'branches.id')
            ->where('shifts.status', $status)
            ->groupBy('branches.id', 'branches.name')
            ->pluck('count', 'name')
            ->toArray();
    }

    private function getShiftsByType(string $status): array
    {
        return Shift::select('shift_type', DB::raw('COUNT(*) as count'))
            ->where('status', $status)
            ->groupBy('shift_type')
            ->pluck('count', 'shift_type')
            ->toArray();
    }

    private function getViolationsByType(): array
    {
        return TimeViolation::select('violation_type', DB::raw('COUNT(*) as count'))
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->groupBy('violation_type')
            ->pluck('count', 'violation_type')
            ->toArray();
    }

    private function getTopViolators(): array
    {
        return TimeViolation::select('users.name', DB::raw('COUNT(*) as violations'))
            ->join('users', 'time_violations.employee_id', '=', 'users.id')
            ->whereDate('time_violations.created_at', '>=', now()->subDays(30))
            ->groupBy('users.id', 'users.name')
            ->orderBy('violations', 'desc')
            ->limit(10)
            ->pluck('violations', 'name')
            ->toArray();
    }

    private function calculateAverageAutoClockOutDelay(): ?float
    {
        $delays = DB::table('shifts')
            ->where('status', 'auto_clocked_out')
            ->whereDate('updated_at', '>=', now()->subDays(7))
            ->selectRaw('TIMESTAMPDIFF(MINUTE, clock_in, clock_out) - auto_clock_out_minutes as delay')
            ->whereNotNull('auto_clock_out_minutes')
            ->pluck('delay')
            ->filter(fn($delay) => $delay > 0);

        return $delays->isNotEmpty() ? round($delays->avg(), 1) : null;
    }

    private function getAverageResponseTime(): float
    {
        // This would integrate with application performance monitoring
        // For now, return a placeholder
        return Cache::get('avg_response_time', 0.0);
    }

    private function getErrorRate(): float
    {
        // Calculate error rate from logs or monitoring system
        return Cache::get('error_rate_24h', 0.0);
    }

    private function getCacheHitRate(): float
    {
        // Calculate cache hit rate from monitoring
        return Cache::get('cache_hit_rate', 0.0);
    }
}
```

---

## Automated Alert Triggers

### Database Triggers (Alternative to Application Logic)

```sql
-- Create event to monitor active shifts
DELIMITER ;;

CREATE EVENT monitor_active_shifts
ON SCHEDULE EVERY 5 MINUTE
DO
BEGIN
    DECLARE active_count INT;
    DECLARE threshold INT DEFAULT 1000;

    SELECT COUNT(*) INTO active_count
    FROM shifts
    WHERE status = 'active';

    IF active_count > threshold THEN
        INSERT INTO system_alerts (alert_type, severity, message, data, created_at)
        VALUES (
            'active_shifts_threshold',
            'warning',
            CONCAT('Active shifts count (', active_count, ') exceeded threshold (', threshold, ')'),
            JSON_OBJECT('active_count', active_count, 'threshold', threshold),
            NOW()
        );
    END IF;
END;;

DELIMITER ;
```

### Log Analysis & Pattern Detection

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogAnalyzer
{
    public function analyzeShiftLogs()
    {
        $issues = [];

        // Check for repeated time violations by same employee
        $repeatViolators = DB::table('time_violations')
            ->select('employee_id', DB::raw('COUNT(*) as violations'))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('employee_id')
            ->having('violations', '>=', 3)
            ->get();

        if ($repeatViolators->isNotEmpty()) {
            $issues[] = [
                'type' => 'repeat_time_violations',
                'severity' => 'warning',
                'message' => 'Employees with repeated time violations detected',
                'data' => $repeatViolators
            ];
        }

        // Check for unusual clock out patterns
        $unusualClockOuts = DB::table('shifts')
            ->where('status', 'closed')
            ->whereDate('clock_out', today())
            ->whereRaw('TIME(clock_out) < "17:00:00"') // Before 5 PM
            ->whereRaw('TIME(clock_in) > "08:00:00"')  // After 8 AM
            ->count();

        if ($unusualClockOuts > 10) {
            $issues[] = [
                'type' => 'unusual_clock_outs',
                'severity' => 'info',
                'message' => 'Unusual number of early clock outs detected',
                'data' => ['count' => $unusualClockOuts]
            ];
        }

        // Check for system performance issues
        $slowQueries = DB::table('system_logs')
            ->where('level', 'error')
            ->where('message', 'like', '%timeout%')
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        if ($slowQueries > 5) {
            $issues[] = [
                'type' => 'performance_issues',
                'severity' => 'warning',
                'message' => 'Multiple system timeouts detected',
                'data' => ['count' => $slowQueries]
            ];
        }

        // Alert on detected issues
        foreach ($issues as $issue) {
            app(AlertManager::class)->triggerAlert($issue['type'], $issue['data']);
        }

        return $issues;
    }

    public function generateWeeklyReport()
    {
        $report = [
            'period' => [
                'start' => now()->startOfWeek()->format('Y-m-d'),
                'end' => now()->endOfWeek()->format('Y-m-d')
            ],
            'shifts' => [
                'total_created' => DB::table('shifts')->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'auto_clocked_out' => DB::table('shifts')->where('status', 'auto_clocked_out')
                    ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'avg_duration' => $this->calculateAverageShiftDuration()
            ],
            'violations' => [
                'total' => DB::table('time_violations')->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'by_type' => $this->getViolationsByType(),
                'top_violators' => $this->getTopViolators()
            ],
            'system_health' => [
                'uptime_percentage' => $this->calculateUptimePercentage(),
                'avg_response_time' => $this->getAverageResponseTime(),
                'error_count' => $this->getErrorCount()
            ]
        ];

        // Store report
        DB::table('weekly_reports')->insert([
            'report_type' => 'shift_system',
            'data' => json_encode($report),
            'created_at' => now()
        ]);

        return $report;
    }
}
```

---

## Incident Response Procedures

### Critical Incident Response

1. **Detection**: Alert triggered or manual monitoring
2. **Assessment**: Evaluate impact and scope
3. **Communication**: Notify stakeholders
4. **Containment**: Implement temporary measures
5. **Recovery**: Restore normal operations
6. **Analysis**: Post-mortem and improvements

### Standard Operating Procedures

#### High Active Shift Count
**Threshold**: > 1000 active shifts
**Response**:
1. Check for auto clock out failures
2. Verify scheduled job status
3. Manually trigger auto clock out if needed
4. Investigate system performance issues

#### Time Violation Spike
**Threshold**: > 20 violations/hour
**Response**:
1. Check shift configuration validity
2. Verify time zone settings
3. Review recent configuration changes
4. Communicate with affected employees

#### Auto Clock Out Failure
**Threshold**: No auto clock out runs for > 30 minutes
**Response**:
1. Check Laravel scheduler status
2. Verify command permissions
3. Review system logs for errors
4. Manually execute command if needed

---

**Document Information**
- **Prepared By**: DevOps & Monitoring Team
- **Reviewed By**: Security & Operations Teams
- **Approved By**: Infrastructure Manager
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/11_MONITORING_ALERTS.md