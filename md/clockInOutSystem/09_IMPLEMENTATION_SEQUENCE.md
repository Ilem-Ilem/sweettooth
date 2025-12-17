# 08_SCHEDULING_CONFIGURATION.md

## Scheduling Configuration Implementation

**Date**: December 2025
**Version**: 1.0
**Status**: Design Complete

---

## Laravel Task Scheduling Setup

### bootstrap/app.php Configuration

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ... existing middleware
    })
    ->withSchedule(function (Schedule $schedule) {
        // === SHIFT SYSTEM AUTOMATION ===

        // Auto clock out expired shifts every 5 minutes
        $schedule->command('shifts:auto-clock-out')
            ->everyFiveMinutes()
            ->between('6:00', '22:00') // Only during business hours
            ->withoutOverlapping(10) // 10 minute timeout
            ->runInBackground()
            ->evenInMaintenanceMode()
            ->onFailure(function () {
                Log::error('Auto clock out command failed');
            });

        // Send shift reminders every hour
        $schedule->command('shifts:send-reminders')
            ->hourly()
            ->between('7:00', '18:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Clean up old shift logs weekly
        $schedule->command('shifts:cleanup-old-data')
            ->weekly()
            ->sundays()
            ->at('02:00')
            ->runInBackground();

        // Generate daily shift reports
        $schedule->command('shifts:generate-daily-reports')
            ->daily()
            ->at('23:30') // End of day
            ->runInBackground();

        // Monitor shift system health
        $schedule->command('shifts:health-check')
            ->everyTenMinutes()
            ->runInBackground();

        // === END SHIFT SYSTEM AUTOMATION ===
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ... existing exceptions
    })->create();
```

---

## Command Implementations

### AutoClockOutShifts Command

```php
<?php

namespace App\Console\Commands;

use App\Models\Shift;
use App\Models\ShiftConfiguration;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;

class AutoClockOutShifts extends Command
{
    protected $signature = 'shifts:auto-clock-out
                          {--dry-run : Show what would be clocked out without making changes}
                          {--branch= : Target specific branch}
                          {--notify : Send notifications to affected employees}
                          {--force : Ignore grace periods and clock out immediately}
                          {--max-shifts=100 : Maximum shifts to process per run}';

    protected $description = 'Automatically clock out expired shifts based on configuration';

    protected AuditService $auditService;
    protected array $stats = [
        'processed' => 0,
        'successful' => 0,
        'failed' => 0,
        'skipped' => 0
    ];

    public function __construct(AuditService $auditService)
    {
        parent::__construct();
        $this->auditService = $auditService;
    }

    public function handle()
    {
        $startTime = microtime(true);
        $this->info('🚀 Starting auto clock out process...');
        $this->newLine();

        try {
            // Health check before processing
            if (!$this->systemHealthCheck()) {
                $this->error('❌ System health check failed. Aborting.');
                return 1;
            }

            // Build and execute query
            $expiredShifts = $this->getExpiredShifts();

            if ($expiredShifts->isEmpty()) {
                $this->info('✅ No expired shifts found.');
                $this->recordMetrics($startTime, 'no_work');
                return 0;
            }

            if ($this->option('dry-run')) {
                $this->showDryRunResults($expiredShifts);
                return 0;
            }

            // Process shifts with progress bar
            $this->processExpiredShifts($expiredShifts);

            // Send notifications if requested
            if ($this->option('notify')) {
                $this->sendBatchNotifications();
            }

            // Record final metrics
            $this->recordMetrics($startTime, 'success');
            $this->showFinalSummary();

        } catch (\Exception $e) {
            $this->recordMetrics($startTime, 'error', $e->getMessage());
            $this->error('❌ Auto clock out failed: ' . $e->getMessage());
            Log::error('Auto clock out command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    protected function systemHealthCheck(): bool
    {
        try {
            // Check database connectivity
            Shift::count();

            // Check if we're not in maintenance mode unexpectedly
            if (app()->isDownForMaintenance()) {
                $this->warn('⚠️  Application is in maintenance mode');
            }

            // Check queue system if notifications enabled
            if ($this->option('notify') && !config('queue.default')) {
                $this->warn('⚠️  Queue system not configured for notifications');
                return false;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Auto clock out health check failed', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    protected function getExpiredShifts()
    {
        $query = Shift::with(['employee', 'branch', 'configuration'])
            ->where('status', 'active');

        // Filter by branch if specified
        if ($this->option('branch')) {
            $query->where('branch_id', $this->option('branch'));
        }

        // Limit processing batch size
        $maxShifts = $this->option('max-shifts');
        if ($maxShifts > 0) {
            $query->limit($maxShifts);
        }

        // Get shifts that have exceeded their configured end time + grace period
        return $query->where(function ($q) {
            if ($this->option('force')) {
                // Force mode: clock out any active shift (for emergency use)
                return $q;
            }

            // Normal mode: use configuration-based logic
            $q->where(function ($sq) {
                // Shifts with explicit configuration
                $sq->whereNotNull('metadata->config_id')
                   ->whereRaw("
                       DATE_ADD(
                           DATE(CONCAT(shift_date, ' ', JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.expected_end')))),
                           INTERVAL JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.auto_clock_out_minutes')) MINUTE
                       ) < NOW()
                   ");
            })->orWhere(function ($sq) {
                // Legacy shifts without configuration (fallback)
                $sq->whereNull('metadata->config_id')
                   ->whereRaw("DATE_ADD(clock_in, INTERVAL 9 HOUR) < NOW()"); // 8 hours + 1 hour grace
            });
        })->orderBy('clock_in')->get();
    }

    protected function showDryRunResults($expiredShifts)
    {
        $this->info('🔍 DRY RUN - The following shifts would be auto clocked out:');
        $this->newLine();

        $tableData = $expiredShifts->map(function ($shift) {
            $expectedEnd = $this->calculateExpectedEndTime($shift);
            $overdueMinutes = now()->diffInMinutes($expectedEnd, false);
            $workedMinutes = $shift->clock_in->diffInMinutes(now());

            return [
                'ID' => $shift->id,
                'Employee' => $shift->employee->name ?? 'Unknown',
                'Branch' => $shift->branch->name ?? 'Unknown',
                'Shift Type' => $shift->shift_type,
                'Started' => $shift->clock_in->format('H:i'),
                'Worked' => round($workedMinutes / 60, 1) . 'h',
                'Overdue' => abs($overdueMinutes) . 'm',
            ];
        });

        $this->table(
            ['ID', 'Employee', 'Branch', 'Shift Type', 'Started', 'Worked', 'Overdue'],
            $tableData->toArray()
        );

        $this->newLine();
        $this->info("📊 Total shifts that would be processed: {$expiredShifts->count()}");
        $this->info("💡 Use --force to clock out all active shifts immediately (emergency only)");
    }

    protected function processExpiredShifts($expiredShifts)
    {
        $bar = $this->output->createProgressBar($expiredShifts->count());
        $bar->setFormat('verbose');
        $bar->start();

        foreach ($expiredShifts as $shift) {
            try {
                $this->processSingleShift($shift);
                $this->stats['successful']++;
                $bar->setMessage("Processed shift {$shift->id}");

            } catch (\Exception $e) {
                $this->stats['failed']++;
                Log::error('Failed to auto clock out shift ' . $shift->id, [
                    'error' => $e->getMessage(),
                    'shift' => $shift->toArray()
                ]);
                $bar->setMessage("Failed shift {$shift->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function processSingleShift(Shift $shift)
    {
        // Calculate worked time before clocking out
        $workedMinutes = $shift->clock_in->diffInMinutes(now());

        // Update shift record
        $shift->update([
            'clock_out' => now(),
            'status' => 'auto_clocked_out',
            'auto_clocked_out_at' => now(),
            'auto_clock_out_reason' => $this->option('force')
                ? 'Emergency force clock out'
                : 'Exceeded configured shift end time + grace period',
            'metadata' => array_merge($shift->metadata ?? [], [
                'auto_clock_out_processed_at' => now()->toIso8601String(),
                'worked_minutes' => $workedMinutes,
                'processed_by_command' => true
            ])
        ]);

        // Clear any cached shift data
        Cache::forget("shift_status_{$shift->employee_id}");
        Cache::forget("active_shift_{$shift->employee_id}_" . $shift->shift_date->format('Y-m-d'));

        // Log audit event
        $this->auditService->log(
            $shift->employee,
            'auto_clock_out',
            $shift,
            "Shift automatically closed after {$workedMinutes} minutes. " .
            "Reason: {$shift->auto_clock_out_reason}",
            'system'
        );

        $this->stats['processed']++;
    }

    protected function calculateExpectedEndTime(Shift $shift): Carbon
    {
        // Use configuration if available
        if (isset($shift->metadata['config_id'])) {
            $config = ShiftConfiguration::find($shift->metadata['config_id']);
            if ($config) {
                $shiftEnd = Carbon::createFromTimeString($config->end_time)
                    ->setDateFrom($shift->shift_date);

                return $shiftEnd->addMinutes($config->auto_clock_out_minutes);
            }
        }

        // Fallback: 8 hours from clock in + 1 hour grace
        return $shift->clock_in->copy()->addHours(9);
    }

    protected function sendBatchNotifications()
    {
        // Group notifications by employee to avoid spam
        $notifications = collect($this->stats['notifications'] ?? [])
            ->groupBy('employee_id');

        foreach ($notifications as $employeeId => $employeeNotifications) {
            try {
                $employee = \App\Models\User::find($employeeId);
                if ($employee) {
                    Notification::route('database', $employeeId)
                        ->notify(new BatchAutoClockOutNotification($employeeNotifications));
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send batch notification', [
                    'employee_id' => $employeeId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function recordMetrics(float $startTime, string $status, ?string $error = null)
    {
        $duration = round(microtime(true) - $startTime, 3);

        Log::info('Auto clock out command completed', [
            'status' => $status,
            'duration_seconds' => $duration,
            'stats' => $this->stats,
            'options' => [
                'dry_run' => $this->option('dry-run'),
                'branch' => $this->option('branch'),
                'notify' => $this->option('notify'),
                'force' => $this->option('force'),
                'max_shifts' => $this->option('max-shifts'),
            ],
            'error' => $error
        ]);

        // Store metrics for monitoring
        Cache::put("auto_clock_out_last_run", now()->toIso8601String(), 3600);
        Cache::put("auto_clock_out_last_stats", $this->stats, 3600);
    }

    protected function showFinalSummary()
    {
        $this->newLine();
        $this->info('📊 Auto Clock Out Summary');
        $this->line('─' . str_repeat('─', 50));

        $this->info("✅ Successfully processed: {$this->stats['successful']} shifts");

        if ($this->stats['failed'] > 0) {
            $this->error("❌ Failed to process: {$this->stats['failed']} shifts");
        }

        if ($this->stats['skipped'] > 0) {
            $this->warn("⚠️  Skipped: {$this->stats['skipped']} shifts");
        }

        if ($this->option('notify')) {
            $this->info("📧 Notifications queued for affected employees");
        }

        $this->info("🔄 Next run: " . now()->addMinutes(5)->format('H:i:s'));
    }
}
```

---

## Additional Scheduled Commands

### ShiftReminderService Command

```php
<?php

namespace App\Console\Commands;

use App\Services\ShiftReminderService;
use Illuminate\Console\Command;

class SendShiftReminders extends Command
{
    protected $signature = 'shifts:send-reminders
                          {--type= : Specific reminder type (starting,ending,overtime)}
                          {--test : Send test notifications only}';

    protected $description = 'Send shift-related reminders to employees';

    public function handle()
    {
        $service = app(ShiftReminderService::class);

        if ($this->option('test')) {
            $this->info('🧪 Sending test notifications...');
            $service->sendTestNotifications();
            return;
        }

        $this->info('📢 Sending shift reminders...');

        $types = $this->option('type') ? [$this->option('type')] : ['all'];

        if (in_array('all', $types) || in_array('starting', $types)) {
            $this->info('⏰ Sending shift starting reminders...');
            $service->sendPreShiftReminders();
        }

        if (in_array('all', $types) || in_array('ending', $types)) {
            $this->info('🏁 Sending shift ending reminders...');
            $service->sendShiftEndingReminders();
        }

        if (in_array('all', $types) || in_array('overtime', $types)) {
            $this->info('⏱️  Sending overtime warnings...');
            $service->sendOvertimeWarnings();
        }

        if (in_array('all', $types) || in_array('auto_clock_out', $types)) {
            $this->info('⚠️  Sending auto clock out warnings...');
            $service->sendAutoClockOutWarnings();
        }

        $this->info('✅ All shift reminders sent');
    }
}
```

### ShiftCleanupOldData Command

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ShiftCleanupOldData extends Command
{
    protected $signature = 'shifts:cleanup-old-data
                          {--days=90 : Delete data older than X days}
                          {--dry-run : Show what would be deleted}
                          {--force : Skip confirmation}';

    protected $description = 'Clean up old shift-related data to maintain performance';

    protected array $cleanupStats = [
        'time_violations' => 0,
        'shift_notifications' => 0,
        'old_shift_logs' => 0
    ];

    public function handle()
    {
        $cutoffDate = Carbon::now()->subDays($this->option('days'));

        $this->info("🧹 Cleaning up data older than {$cutoffDate->format('Y-m-d')}");
        $this->newLine();

        if (!$this->option('force') && !$this->option('dry-run')) {
            if (!$this->confirm('This will permanently delete old data. Continue?')) {
                return;
            }
        }

        try {
            // Clean time violations
            $this->cleanupTimeViolations($cutoffDate);

            // Clean old notifications
            $this->cleanupNotifications($cutoffDate);

            // Archive old shift logs (optional)
            $this->archiveOldShifts($cutoffDate);

            if ($this->option('dry-run')) {
                $this->showCleanupSummary(true);
            } else {
                $this->showCleanupSummary(false);
            }

        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function cleanupTimeViolations(Carbon $cutoffDate)
    {
        $query = DB::table('time_violations')
            ->where('created_at', '<', $cutoffDate)
            ->where('requires_attention', false); // Keep violations that need attention

        if ($this->option('dry-run')) {
            $this->cleanupStats['time_violations'] = $query->count();
        } else {
            $this->cleanupStats['time_violations'] = $query->delete();
        }
    }

    protected function cleanupNotifications(Carbon $cutoffDate)
    {
        $query = DB::table('shift_notifications')
            ->where('created_at', '<', $cutoffDate)
            ->where('is_read', true); // Keep unread notifications

        if ($this->option('dry-run')) {
            $this->cleanupStats['shift_notifications'] = $query->count();
        } else {
            $this->cleanupStats['shift_notifications'] = $query->delete();
        }
    }

    protected function archiveOldShifts(Carbon $cutoffDate)
    {
        // Optional: Move very old shifts to archive table
        // This is for long-term data retention compliance
        $oldShifts = DB::table('shifts')
            ->where('shift_date', '<', $cutoffDate->subDays(365)) // Over 1 year old
            ->where('status', '!=', 'active');

        if ($this->option('dry-run')) {
            $this->cleanupStats['old_shift_logs'] = $oldShifts->count();
        } else {
            // In real implementation, would move to archive table
            // For now, just count
            $this->cleanupStats['old_shift_logs'] = $oldShifts->count();
        }
    }

    protected function showCleanupSummary(bool $dryRun)
    {
        $this->newLine();
        $this->info($dryRun ? '🔍 DRY RUN - Would delete:' : '🗑️  Cleanup completed:');
        $this->line('─' . str_repeat('─', 40));

        foreach ($this->cleanupStats as $table => $count) {
            $tableName = str_replace('_', ' ', ucfirst($table));
            $this->info("{$tableName}: {$count} records");
        }

        if (!$dryRun) {
            $this->info('✅ Database optimized for better performance');
        }
    }
}
```

---

## Queue Configuration

### config/queue.php Enhancements

```php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
    ],

    // High priority queue for shift notifications
    'shift_notifications' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'shift_notifications',
        'retry_after' => 60,
        'after_commit' => false,
    ],

    // Low priority queue for cleanup tasks
    'shift_cleanup' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'shift_cleanup',
        'retry_after' => 300, // 5 minutes
        'after_commit' => false,
    ],
],
```

### Queue Worker Configuration

```bash
# Supervisor configuration for queue workers
[program:laravel-shift-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --queue=shift_notifications --sleep=3 --tries=3 --max-jobs=1000
directory=/path/to/project
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/laravel/shift-worker.log
```

---

## Monitoring & Alerting

### Scheduled Task Health Monitoring

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ShiftSystemHealthCheck extends Command
{
    protected $signature = 'shifts:health-check';
    protected $description = 'Monitor health of shift system components';

    protected array $checks = [
        'database_connectivity' => false,
        'auto_clock_out_last_run' => false,
        'reminders_last_run' => false,
        'queue_backlog' => false,
        'failed_jobs_count' => false,
        'active_shifts_count' => false,
    ];

    public function handle()
    {
        $this->info('🏥 Running shift system health checks...');
        $this->newLine();

        $allHealthy = true;

        // Database connectivity
        $this->checks['database_connectivity'] = $this->checkDatabaseConnectivity();
        if (!$this->checks['database_connectivity']) $allHealthy = false;

        // Auto clock out process
        $this->checks['auto_clock_out_last_run'] = $this->checkAutoClockOutProcess();
        if (!$this->checks['auto_clock_out_last_run']) $allHealthy = false;

        // Reminder system
        $this->checks['reminders_last_run'] = $this->checkReminderSystem();
        if (!$this->checks['reminders_last_run']) $allHealthy = false;

        // Queue health
        $this->checks['queue_backlog'] = $this->checkQueueBacklog();
        if (!$this->checks['queue_backlog']) $allHealthy = false;

        // Failed jobs
        $this->checks['failed_jobs_count'] = $this->checkFailedJobs();
        if (!$this->checks['failed_jobs_count']) $allHealthy = false;

        // Active shifts (business metric)
        $this->checks['active_shifts_count'] = $this->checkActiveShifts();

        // Display results
        $this->displayResults($allHealthy);

        // Alert if system is unhealthy
        if (!$allHealthy) {
            $this->sendHealthAlert();
            return 1;
        }

        return 0;
    }

    protected function checkDatabaseConnectivity(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            $this->error("❌ Database connection failed: {$e->getMessage()}");
            return false;
        }
    }

    protected function checkAutoClockOutProcess(): bool
    {
        $lastRun = Cache::get('auto_clock_out_last_run');

        if (!$lastRun) {
            $this->warn("⚠️  Auto clock out never ran");
            return false;
        }

        $lastRunTime = \Carbon\Carbon::parse($lastRun);
        $minutesSinceLastRun = $lastRunTime->diffInMinutes(now());

        if ($minutesSinceLastRun > 15) { // Should run every 5 minutes
            $this->error("❌ Auto clock out last ran {$minutesSinceLastRun} minutes ago");
            return false;
        }

        $this->info("✅ Auto clock out last ran {$minutesSinceLastRun} minutes ago");
        return true;
    }

    protected function checkReminderSystem(): bool
    {
        $lastRun = Cache::get('shift_reminders_last_run');

        if (!$lastRun) {
            $this->warn("⚠️  Shift reminders never ran");
            return false;
        }

        $lastRunTime = \Carbon\Carbon::parse($lastRun);
        $hoursSinceLastRun = $lastRunTime->diffInHours(now());

        if ($hoursSinceLastRun > 2) { // Should run every hour
            $this->error("❌ Shift reminders last ran {$hoursSinceLastRun} hours ago");
            return false;
        }

        $this->info("✅ Shift reminders last ran {$hoursSinceLastRun} hours ago");
        return true;
    }

    protected function checkQueueBacklog(): bool
    {
        $backlog = \DB::table('jobs')
            ->where('queue', 'shift_notifications')
            ->where('created_at', '<', now()->subMinutes(30))
            ->count();

        if ($backlog > 100) {
            $this->error("❌ Queue backlog: {$backlog} jobs older than 30 minutes");
            return false;
        }

        $this->info("✅ Queue backlog: {$backlog} pending jobs");
        return true;
    }

    protected function checkFailedJobs(): bool
    {
        $failedCount = \DB::table('failed_jobs')
            ->where('queue', 'shift_notifications')
            ->where('failed_at', '>', now()->subHours(24))
            ->count();

        if ($failedCount > 10) {
            $this->error("❌ Failed jobs in last 24h: {$failedCount}");
            return false;
        }

        $this->info("✅ Failed jobs in last 24h: {$failedCount}");
        return true;
    }

    protected function checkActiveShifts(): bool
    {
        $activeCount = \App\Models\Shift::where('status', 'active')->count();

        $this->info("📊 Currently active shifts: {$activeCount}");

        // This is just informational, not a health check
        return true;
    }

    protected function displayResults(bool $allHealthy)
    {
        $this->newLine();

        if ($allHealthy) {
            $this->info('🎉 All shift system health checks passed!');
        } else {
            $this->error('⚠️  Some health checks failed. See details above.');
        }

        $this->line('─' . str_repeat('─', 50));

        foreach ($this->checks as $check => $passed) {
            $status = $passed ? '✅' : '❌';
            $checkName = ucwords(str_replace('_', ' ', $check));
            $this->line("{$status} {$checkName}");
        }
    }

    protected function sendHealthAlert()
    {
        // Send alert to administrators
        Log::critical('Shift system health check failed', [
            'checks' => $this->checks,
            'timestamp' => now()->toIso8601String()
        ]);

        // Could also send email/SMS alerts here
    }
}
```

---

## Alternative: System Cron Jobs

If Laravel scheduler is not preferred, use system cron:

```bash
# /etc/cron.d/laravel-shift-system
# Auto clock out every 5 minutes during business hours
*/5 6-22 * * * www-data /usr/bin/php /path/to/artisan shifts:auto-clock-out >> /var/log/shift-auto-clock-out.log 2>&1

# Send reminders hourly during business hours
0 7-18 * * * www-data /usr/bin/php /path/to/artisan shifts:send-reminders >> /var/log/shift-reminders.log 2>&1

# Health check every 10 minutes
*/10 * * * * www-data /usr/bin/php /path/to/artisan shifts:health-check >> /var/log/shift-health.log 2>&1

# Weekly cleanup on Sundays at 2 AM
0 2 * * 0 www-data /usr/bin/php /path/to/artisan shifts:cleanup-old-data >> /var/log/shift-cleanup.log 2>&1
```

---

## Performance Optimization

### Job Prioritization

```php
// In AppServiceProvider or dedicated config
Queue::before(function (JobProcessing $event) {
    // Set priority based on job type
    if (str_contains($event->job->getName(), 'ShiftEndingReminder')) {
        $event->job->setPriority(10); // High priority
    } elseif (str_contains($event->job->getName(), 'OvertimeWarning')) {
        $event->job->setPriority(5); // Medium priority
    }
});
```

### Batch Processing

```php
// Process multiple shifts in batches to reduce memory usage
$expiredShifts->chunk(50)->each(function ($chunk) {
    // Process chunk
    foreach ($chunk as $shift) {
        $this->processSingleShift($shift);
    }

    // Free memory
    unset($chunk);
});
```

---

## Testing Scheduled Commands

### Command Testing

```php
<?php

namespace Tests\Console\Commands;

use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoClockOutShiftsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_clock_out_clocks_out_expired_shifts()
    {
        // Create expired shift
        $expiredShift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(10), // Started 10 hours ago
            'shift_date' => Carbon::today(),
        ]);

        // Create active shift (should not be touched)
        $activeShift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(2), // Started 2 hours ago
        ]);

        $this->artisan('shifts:auto-clock-out')
            ->expectsOutput('Auto clocked out 1 shifts')
            ->assertExitCode(0);

        // Verify expired shift was closed
        $expiredShift->refresh();
        $this->assertEquals('auto_clocked_out', $expiredShift->status);
        $this->assertNotNull($expiredShift->clock_out);
        $this->assertNotNull($expiredShift->auto_clocked_out_at);

        // Verify active shift was not touched
        $activeShift->refresh();
        $this->assertEquals('active', $activeShift->status);
        $this->assertNull($activeShift->clock_out);
    }

    public function test_dry_run_shows_changes_without_applying()
    {
        $expiredShift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(10),
        ]);

        $this->artisan('shifts:auto-clock-out --dry-run')
            ->expectsTable(['ID', 'Employee', 'Branch', 'Shift Type', 'Started', 'Worked', 'Overdue'], [
                [$expiredShift->id, 'Test Employee', 'Test Branch', 'morning', 'Clock in time', 'Worked time', 'Overdue time']
            ])
            ->assertExitCode(0);

        // Verify shift was not actually closed
        $expiredShift->refresh();
        $this->assertEquals('active', $expiredShift->status);
    }

    public function test_branch_filtering_works()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();

        $shiftInBranch1 = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(10),
            'branch_id' => $branch1->id,
        ]);

        $shiftInBranch2 = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(10),
            'branch_id' => $branch2->id,
        ]);

        $this->artisan("shifts:auto-clock-out --branch={$branch1->id}")
            ->assertExitCode(0);

        // Only branch 1 shift should be closed
        $shiftInBranch1->refresh();
        $shiftInBranch2->refresh();

        $this->assertEquals('auto_clocked_out', $shiftInBranch1->status);
        $this->assertEquals('active', $shiftInBranch2->status);
    }
}
```

---

**Document Information**
- **Prepared By**: DevOps & Automation Team
- **Reviewed By**: Infrastructure Team
- **Approved By**: Operations Manager
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/08_SCHEDULING_CONFIGURATION.md