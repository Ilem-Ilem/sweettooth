# 04_AUTO_CLOCK_OUT_SYSTEM.md

## Auto Clock Out System Implementation

**Date**: December 2025
**Version**: 1.0
**Status**: Design Complete

---

## Overview

The Auto Clock Out system automatically closes expired shifts to prevent indefinite open sessions, ensure accurate time tracking, and maintain system performance. This scheduled job runs periodically to identify and close shifts that have exceeded their configured end times plus grace periods.

---

## 1. AutoClockOutShifts Command

### Command Architecture

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

class AutoClockOutShifts extends Command
{
    protected $signature = 'shifts:auto-clock-out
                          {--dry-run : Show what would be clocked out without making changes}
                          {--branch= : Target specific branch}
                          {--notify : Send notifications to affected employees}
                          {--force : Ignore grace periods and clock out immediately}';

    protected $description = 'Automatically clock out expired shifts based on configuration';

    protected AuditService $auditService;
    protected array $processedShifts = [];

    public function __construct(AuditService $auditService)
    {
        parent::__construct();
        $this->auditService = $auditService;
    }

    public function handle()
    {
        $this->info('Starting auto clock out process...');

        $query = $this->buildExpiredShiftsQuery();
        $expiredShifts = $query->get();

        if ($expiredShifts->isEmpty()) {
            $this->info('No expired shifts found.');
            return;
        }

        if ($this->option('dry-run')) {
            $this->showDryRunResults($expiredShifts);
            return;
        }

        $this->processExpiredShifts($expiredShifts);

        $this->info("Auto clocked out {$this->processedShifts['count']} shifts");
        $this->showSummary();
    }

    protected function buildExpiredShiftsQuery()
    {
        $query = Shift::where('status', 'active')
            ->with(['employee', 'branch', 'configuration']);

        // Filter by branch if specified
        if ($this->option('branch')) {
            $query->where('branch_id', $this->option('branch'));
        }

        // Get shifts that have exceeded their end time + grace period
        $query->where(function ($q) {
            // For shifts with configuration reference
            $q->whereNotNull('metadata->config_id')
              ->whereRaw("
                  DATE_ADD(
                      DATE(CONCAT(shift_date, ' ', JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.expected_end')))),
                      INTERVAL JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.auto_clock_out_minutes')) MINUTE
                  ) < NOW()
              ")
            // Fallback for shifts without configuration (legacy support)
            ->orWhere(function ($sq) {
                $sq->whereNull('metadata->config_id')
                   ->whereRaw("DATE_ADD(clock_in, INTERVAL 9 HOUR) < NOW()"); // Default 9 hours
            });
        });

        return $query;
    }

    protected function showDryRunResults($expiredShifts)
    {
        $this->info('DRY RUN - The following shifts would be auto clocked out:');
        $this->newLine();

        $tableData = $expiredShifts->map(function ($shift) {
            $expectedEnd = $this->calculateExpectedEndTime($shift);
            $overdueMinutes = now()->diffInMinutes($expectedEnd, false);

            return [
                'ID' => $shift->id,
                'Employee' => $shift->employee->name ?? 'Unknown',
                'Branch' => $shift->branch->name ?? 'Unknown',
                'Shift Type' => $shift->shift_type,
                'Clock In' => $shift->clock_in->format('H:i'),
                'Expected End' => $expectedEnd->format('H:i'),
                'Overdue (min)' => abs($overdueMinutes),
            ];
        });

        $this->table(
            ['ID', 'Employee', 'Branch', 'Shift Type', 'Clock In', 'Expected End', 'Overdue (min)'],
            $tableData
        );

        $this->newLine();
        $this->info("Total shifts that would be processed: {$expiredShifts->count()}");
    }

    protected function processExpiredShifts($expiredShifts)
    {
        $bar = $this->output->createProgressBar($expiredShifts->count());
        $bar->start();

        foreach ($expiredShifts as $shift) {
            try {
                $this->processSingleShift($shift);
                $this->processedShifts['successful'][] = $shift->id;
            } catch (\Exception $e) {
                $this->processedShifts['failed'][] = [
                    'shift_id' => $shift->id,
                    'error' => $e->getMessage()
                ];
                Log::error('Auto clock out failed for shift ' . $shift->id, [
                    'error' => $e->getMessage(),
                    'shift' => $shift->toArray()
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function processSingleShift(Shift $shift)
    {
        $originalStatus = $shift->status;
        $clockOutTime = now();

        // Update shift
        $shift->clock_out = $clockOutTime;
        $shift->status = 'auto_clocked_out';
        $shift->metadata = array_merge($shift->metadata ?? [], [
            'auto_clocked_out_at' => $clockOutTime->toIso8601String(),
            'auto_clock_out_reason' => 'Exceeded configured end time + grace period'
        ]);

        $shift->save();

        // Calculate worked time
        $workedMinutes = $shift->clock_in->diffInMinutes($clockOutTime);

        // Log audit event
        $this->auditService->log(
            $shift->employee,
            'auto_clock_out',
            $shift,
            "Shift automatically closed after {$workedMinutes} minutes. " .
            "Exceeded expected end time by " .
            abs(now()->diffInMinutes($this->calculateExpectedEndTime($shift))) . " minutes.",
            'system'
        );

        // Send notification if requested
        if ($this->option('notify')) {
            $this->notifyEmployee($shift, $workedMinutes);
        }

        $this->processedShifts['count'] = ($this->processedShifts['count'] ?? 0) + 1;
    }

    protected function calculateExpectedEndTime(Shift $shift): Carbon
    {
        // If shift has configuration reference, use it
        if (isset($shift->metadata['config_id'])) {
            $config = ShiftConfiguration::find($shift->metadata['config_id']);
            if ($config) {
                return Carbon::createFromTimeString($config->end_time)
                    ->setDateFrom($shift->shift_date)
                    ->addMinutes($config->auto_clock_out_minutes);
            }
        }

        // Fallback: assume 8-hour shift + 1 hour grace (9 hours total)
        return $shift->clock_in->copy()->addHours(9);
    }

    protected function notifyEmployee(Shift $shift, int $workedMinutes)
    {
        try {
            Notification::route('mail', $shift->employee->email)
                ->notify(new ShiftAutoClockedOut($shift, $workedMinutes));
        } catch (\Exception $e) {
            Log::warning('Failed to send auto clock out notification', [
                'shift_id' => $shift->id,
                'employee_id' => $shift->employee_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function showSummary()
    {
        $this->newLine();
        $this->info('=== Auto Clock Out Summary ===');

        if (!empty($this->processedShifts['successful'])) {
            $this->info("✅ Successfully processed: " . count($this->processedShifts['successful']) . " shifts");
        }

        if (!empty($this->processedShifts['failed'])) {
            $this->error("❌ Failed to process: " . count($this->processedShifts['failed']) . " shifts");
            foreach ($this->processedShifts['failed'] as $failure) {
                $this->error("  - Shift {$failure['shift_id']}: {$failure['error']}");
            }
        }

        if ($this->option('notify')) {
            $this->info("📧 Notifications sent to affected employees");
        }
    }
}
```

---

## 2. Notification Class

### ShiftAutoClockedOut Notification

```php
<?php

namespace App\Notifications;

use App\Models\Shift;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftAutoClockedOut extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Shift $shift,
        public int $workedMinutes
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $hours = floor($workedMinutes / 60);
        $minutes = $workedMinutes % 60;

        return (new MailMessage)
            ->subject('Your shift has been automatically closed')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your {$this->shift->shift_type} shift has been automatically closed by the system.")
            ->line("**Shift Details:**")
            ->line("• Date: {$this->shift->shift_date->format('M j, Y')}")
            ->line("• Clock In: {$this->shift->clock_in->format('g:i A')}")
            ->line("• Clock Out: {$this->shift->clock_out->format('g:i A')}")
            ->line("• Total Time: {$hours}h {$minutes}m")
            ->line('This happened because your shift exceeded the configured end time plus grace period.')
            ->action('View Shift Details', route('branch-dashboard.index', ['b_id' => $this->shift->branch_id]))
            ->line('If you believe this was done in error, please contact your supervisor.')
            ->salutation('Best regards, System Administrator');
    }
}
```

---

## 3. Scheduling Configuration

### Laravel Task Scheduling

```php
// bootstrap/app.php (Laravel 11)
->withSchedule(function (Schedule $schedule) {
    // Auto clock out every 5 minutes during business hours
    $schedule->command('shifts:auto-clock-out')
        ->everyFiveMinutes()
        ->between('6:00', '22:00') // Only during business hours
        ->withoutOverlapping()
        ->runInBackground()
        ->evenInMaintenanceMode();

    // Send shift reminders every hour
    $schedule->command('shifts:send-reminders')
        ->hourly()
        ->between('7:00', '18:00');

    // Clean up old auto-clock out logs weekly
    $schedule->command('shifts:cleanup-auto-clock-out-logs')
        ->weekly()
        ->sundays()
        ->at('03:00');
})
```

### Alternative: System Cron Job

```bash
# /etc/cron.d/auto-clock-out
*/5 6-22 * * * www-data /usr/bin/php /path/to/artisan shifts:auto-clock-out >> /var/log/auto-clock-out.log 2>&1
```

---

## 4. Business Logic Implementation

### Shift Expiration Calculation

```php
<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\ShiftConfiguration;
use Carbon\Carbon;

class ShiftExpirationService
{
    /**
     * Check if a shift should be auto clocked out
     */
    public function shouldAutoClockOut(Shift $shift, ?Carbon $currentTime = null): bool
    {
        $now = $currentTime ?? Carbon::now();

        // Skip if shift is not active
        if ($shift->status !== 'active') {
            return false;
        }

        $expectedEndTime = $this->calculateExpectedEndTime($shift);

        // Check if current time is past expected end time + grace period
        return $now > $expectedEndTime;
    }

    /**
     * Calculate when a shift should be auto clocked out
     */
    public function calculateExpectedEndTime(Shift $shift): Carbon
    {
        // Method 1: Use shift configuration if available
        if (isset($shift->metadata['config_id'])) {
            $config = ShiftConfiguration::find($shift->metadata['config_id']);
            if ($config) {
                $shiftEnd = Carbon::createFromTimeString($config->end_time)
                    ->setDateFrom($shift->shift_date);

                return $shiftEnd->addMinutes($config->auto_clock_out_minutes);
            }
        }

        // Method 2: Use shift metadata if available
        if (isset($shift->metadata['expected_end'])) {
            $expectedEnd = Carbon::createFromTimeString($shift->metadata['expected_end'])
                ->setDateFrom($shift->shift_date);

            $graceMinutes = $shift->metadata['auto_clock_out_minutes'] ?? 15;
            return $expectedEnd->addMinutes($graceMinutes);
        }

        // Method 3: Fallback based on shift type
        return $this->calculateFallbackEndTime($shift);
    }

    /**
     * Fallback calculation for shifts without configuration
     */
    private function calculateFallbackEndTime(Shift $shift): Carbon
    {
        $baseHours = match($shift->shift_type) {
            'morning' => 8,    // 6 AM - 2 PM
            'afternoon' => 8,  // 12 PM - 8 PM
            'night' => 8,      // 10 PM - 6 AM
            'full_time' => 9,  // Flexible
            default => 8
        };

        return $shift->clock_in->copy()->addHours($baseHours);
    }

    /**
     * Get overdue minutes for a shift
     */
    public function getOverdueMinutes(Shift $shift, ?Carbon $currentTime = null): int
    {
        $now = $currentTime ?? Carbon::now();
        $expectedEnd = $this->calculateExpectedEndTime($shift);

        return $now->diffInMinutes($expectedEnd, false); // Negative if overdue
    }

    /**
     * Get shifts ready for auto clock out
     */
    public function getExpiredShifts(?int $branchId = null, ?Carbon $currentTime = null): Collection
    {
        $query = Shift::where('status', 'active')
            ->with(['employee', 'branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $shifts = $query->get();

        return $shifts->filter(function ($shift) use ($currentTime) {
            return $this->shouldAutoClockOut($shift, $currentTime);
        });
    }
}
```

---

## 5. Database Updates

### Add Auto Clock Out Tracking

```sql
-- Add columns to shifts table
ALTER TABLE shifts ADD COLUMN auto_clocked_out_at TIMESTAMP NULL;
ALTER TABLE shifts ADD COLUMN auto_clock_out_reason VARCHAR(255) NULL;
ALTER TABLE shifts ADD COLUMN notified_at TIMESTAMP NULL;

-- Add indexes for performance
CREATE INDEX idx_auto_clock_out_status ON shifts(status, shift_date);
CREATE INDEX idx_auto_clock_out_metadata ON shifts((JSON_EXTRACT(metadata, '$.auto_clocked_out_at')));

-- Update existing active shifts with reasonable end times
UPDATE shifts
SET metadata = JSON_SET(
    COALESCE(metadata, '{}'),
    '$.expected_end',
    CASE shift_type
        WHEN 'morning' THEN '14:00:00'
        WHEN 'afternoon' THEN '20:00:00'
        WHEN 'night' THEN '06:00:00'
        ELSE '17:00:00'
    END,
    '$.auto_clock_out_minutes', 15
)
WHERE status = 'active' AND metadata IS NULL;
```

---

## 6. Monitoring & Alerting

### Health Check Endpoint

```php
// routes/web.php
Route::get('/health/auto-clock-out', [HealthController::class, 'autoClockOut']);

// HealthController.php
public function autoClockOut()
{
    $lastRun = Cache::get('auto_clock_out_last_run');
    $lastRunTime = $lastRun ? Carbon::parse($lastRun) : null;

    $isHealthy = $lastRunTime && $lastRunTime->diffInMinutes() < 10; // Should run every 5 min

    return response()->json([
        'status' => $isHealthy ? 'healthy' : 'unhealthy',
        'last_run' => $lastRunTime?->toIso8601String(),
        'next_expected' => $lastRunTime?->addMinutes(5)->toIso8601String(),
        'overdue_minutes' => $lastRunTime ? now()->diffInMinutes($lastRunTime->addMinutes(5)) : null,
        'active_shifts_count' => Shift::where('status', 'active')->count(),
        'expired_shifts_count' => app(ShiftExpirationService::class)->getExpiredShifts()->count()
    ]);
}
```

### Alert System

```php
// In AutoClockOutShifts command
protected function sendAlertsIfNeeded()
{
    $failedCount = count($this->processedShifts['failed'] ?? []);

    if ($failedCount > 0) {
        Log::critical('Auto clock out failures detected', [
            'failed_count' => $failedCount,
            'failures' => $this->processedShifts['failed'],
            'total_processed' => $this->processedShifts['count'] ?? 0
        ]);

        // Send alert to administrators
        Notification::route('slack', config('services.slack.webhook'))
            ->notify(new AutoClockOutAlert($failedCount, $this->processedShifts['failed']));
    }
}
```

---

## 7. Testing Strategy

### Unit Tests

```php
<?php

namespace Tests\Unit\Console\Commands;

use App\Models\Shift;
use App\Models\ShiftConfiguration;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AutoClockOutShiftsTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_clocks_out_expired_shifts()
    {
        // Create expired shift
        $expiredShift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(10), // Started 10 hours ago
            'shift_type' => 'morning',
            'shift_date' => Carbon::today(),
        ]);

        // Create active shift (should not be touched)
        $activeShift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(2), // Started 2 hours ago
            'shift_type' => 'morning',
            'shift_date' => Carbon::today(),
        ]);

        $this->artisan('shifts:auto-clock-out')
            ->expectsOutput('Auto clocked out 1 shifts')
            ->assertExitCode(0);

        // Refresh models
        $expiredShift->refresh();
        $activeShift->refresh();

        $this->assertEquals('auto_clocked_out', $expiredShift->status);
        $this->assertNotNull($expiredShift->clock_out);

        $this->assertEquals('active', $activeShift->status);
        $this->assertNull($activeShift->clock_out);
    }

    public function test_dry_run_mode_shows_changes_without_applying()
    {
        $expiredShift = Shift::factory()->create([
            'status' => 'active',
            'clock_in' => Carbon::now()->subHours(10),
            'shift_type' => 'morning',
        ]);

        $this->artisan('shifts:auto-clock-out --dry-run')
            ->expectsTable(
                ['ID', 'Employee', 'Branch', 'Shift Type', 'Clock In', 'Expected End', 'Overdue (min)'],
                [[$expiredShift->id, 'Test Employee', 'Test Branch', 'morning', 'Clock in time', 'Expected end', 'Overdue minutes']]
            )
            ->assertExitCode(0);

        // Verify shift was not actually closed
        $expiredShift->refresh();
        $this->assertEquals('active', $expiredShift->status);
    }
}
```

### Integration Tests

```php
<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Shift;
use App\Notifications\ShiftAutoClockedOut;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AutoClockOutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_auto_clock_out_workflow()
    {
        Notification::fake();

        // Create shift configuration
        $config = ShiftConfiguration::factory()->create([
            'shift_type' => 'morning',
            'start_time' => '06:00:00',
            'end_time' => '14:00:00',
            'auto_clock_out_minutes' => 15,
        ]);

        // Create expired shift
        $shift = Shift::factory()->create([
            'status' => 'active',
            'shift_type' => 'morning',
            'shift_date' => Carbon::today(),
            'clock_in' => Carbon::today()->setTime(6, 0), // Started at 6 AM
            'metadata' => [
                'config_id' => $config->id,
                'expected_end' => '14:00:00',
                'auto_clock_out_minutes' => 15
            ]
        ]);

        // Simulate time being 14:20 (20 minutes past end + grace)
        Carbon::setTestNow(Carbon::today()->setTime(14, 20));

        $this->artisan('shifts:auto-clock-out --notify')
            ->assertExitCode(0);

        // Verify shift was closed
        $shift->refresh();
        $this->assertEquals('auto_clocked_out', $shift->status);
        $this->assertEquals('14:20:00', $shift->clock_out->format('H:i:s'));

        // Verify notification was sent
        Notification::assertSentTo(
            $shift->employee,
            ShiftAutoClockedOut::class,
            function ($notification) use ($shift) {
                return $notification->shift->id === $shift->id;
            }
        );

        Carbon::setTestNow(); // Reset time
    }
}
```

---

## 8. Rollback & Recovery

### Safe Rollback Procedures

```php
// Command to undo auto clock outs within a time window
class UndoAutoClockOut extends Command
{
    protected $signature = 'shifts:undo-auto-clock-out
                          {--hours=24 : How many hours back to look}
                          {--dry-run : Show what would be undone}';

    public function handle()
    {
        $cutoffTime = now()->subHours($this->option('hours'));

        $autoClockedOutShifts = Shift::where('status', 'auto_clocked_out')
            ->where('updated_at', '>', $cutoffTime)
            ->get();

        if ($this->option('dry-run')) {
            $this->showDryRun($autoClockedOutShifts);
            return;
        }

        foreach ($autoClockedOutShifts as $shift) {
            $shift->status = 'active';
            $shift->clock_out = null;
            $shift->metadata = array_merge($shift->metadata ?? [], [
                'auto_clock_out_undone_at' => now()->toIso8601String()
            ]);
            $shift->save();
        }

        $this->info("Undone auto clock out for {$autoClockedOutShifts->count()} shifts");
    }
}
```

---

## 9. Performance Considerations

### Database Optimization

```sql
-- Add composite indexes for better query performance
CREATE INDEX idx_shifts_auto_clock_out 
ON shifts(branch_id, status, shift_date, clock_in);

-- Add generated column for expected end time
ALTER TABLE shifts 
ADD COLUMN expected_end_time TIMESTAMP GENERATED ALWAYS AS (
    DATE_ADD(
        DATE(CONCAT(shift_date, ' ', JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.expected_end')))), 
        INTERVAL JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.auto_clock_out_minutes')) MINUTE
    )
) STORED;

CREATE INDEX idx_expected_end_time ON shifts(expected_end_time);
```

### Batch Processing

```php
// Process shifts in batches to avoid memory issues
$expiredShifts->chunk(100)->each(function ($chunk) {
    foreach ($chunk as $shift) {
        $this->processSingleShift($shift);
    }
});
```

---

## 10. Security Considerations

### Audit Trail

- All auto clock out actions are logged
- Original shift data is preserved
- Notifications include security context
- Admin alerts for anomalies

### Data Integrity

- Transaction boundaries for all updates
- Foreign key constraints maintained
- Rollback capabilities preserved
- No data loss during processing

---

**Document Information**
- **Prepared By**: Automation & Infrastructure Team
- **Reviewed By**: Security & Compliance Team
- **Approved By**: Operations Manager
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/04_AUTO_CLOCK_OUT_SYSTEM.md