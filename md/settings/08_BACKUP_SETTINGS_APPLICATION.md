# 08_BACKUP_SETTINGS_APPLICATION.md

## Backup Settings Application

### Current Issues with Backup Settings Usage

#### 1. Database Backup Service
**Current State:** Already properly implemented
**Location:** `app/Services/DatabaseBackupService.php`

**Working Implementation:**
```php
public function isAutoBackupEnabled(): bool
{
    return Settings::businessConfiguration('auto_backup', false) === true;
}

public function getBackupInterval(): int
{
    return Settings::businessConfiguration('backup_interval', 2);
}

public function getBackupPeriod(): string
{
    return Settings::businessConfiguration('backup_period', 'months');
}
```

#### 2. Backup Scheduling Integration
**Problem Areas:**
- Console commands don't use settings
- Backup job scheduling not configurable
- Manual backup options not respecting settings

**Required Changes:**
```php
// app/Console/Commands/DatabaseBackupCommand.php
public function handle()
{
    $autoBackupEnabled = Settings::businessConfiguration('auto_backup', false);
    
    if (!$autoBackupEnabled) {
        $this->info('Auto backup is disabled in settings.');
        return;
    }
    
    $interval = Settings::businessConfiguration('backup_interval', 2);
    $period = Settings::businessConfiguration('backup_period', 'months');
    
    $this->info("Starting backup with interval: {$interval} {$period}");
    
    // Execute backup
    $backupService = new DatabaseBackupService();
    $result = $backupService->performBackup();
    
    if ($result['success']) {
        $this->info("Backup completed: {$result['filename']}");
    } else {
        $this->error("Backup failed: {$result['error']}");
    }
}

// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $autoBackupEnabled = Settings::businessConfiguration('auto_backup', false);
    
    if ($autoBackupEnabled) {
        $interval = Settings::businessConfiguration('backup_interval', 2);
        $period = Settings::businessConfiguration('backup_period', 'months');
        
        $expression = $this->getCronExpression($interval, $period);
        
        $schedule->command('backup:database')
                 ->cron($expression)
                 ->description('Automatic database backup');
    }
}

private function getCronExpression(int $interval, string $period): string
{
    switch ($period) {
        case 'days':
            return "0 2 */{$interval} * *"; // Every X days at 2 AM
        case 'weeks':
            return "0 2 */{$interval} * 0"; // Every X weeks on Sunday at 2 AM
        case 'months':
            return "0 2 1 */{$interval} *"; // Every X months on 1st at 2 AM
        default:
            return "0 2 * * *"; // Daily at 2 AM
    }
}
```

#### 3. Backup UI Integration
**Problem Areas:**
- Settings UI doesn't reflect current backup status
- Manual backup triggers not available
- Backup history not displayed

**Required Changes:**
```php
// Add backup management to settings
// app/Livewire/BranchDashboard/Settings/BackupManagement.php
class BackupManagement extends Component
{
    public $autoBackup;
    public $backupInterval;
    public $backupPeriod;
    public $lastBackup;
    public $nextScheduledBackup;
    public $backupHistory;
    public $isBackupRunning;

    public function mount()
    {
        $this->loadBackupSettings();
        $this->loadBackupStatus();
        $this->loadBackupHistory();
    }

    public function loadBackupSettings()
    {
        $this->autoBackup = Settings::businessConfiguration('auto_backup', false);
        $this->backupInterval = Settings::businessConfiguration('backup_interval', 2);
        $this->backupPeriod = Settings::businessConfiguration('backup_period', 'months');
    }

    public function loadBackupStatus()
    {
        $backupService = new DatabaseBackupService();
        
        $this->lastBackup = $backupService->getLastBackupInfo();
        $this->nextScheduledBackup = $backupService->getNextScheduledBackup();
        $this->isBackupRunning = $backupService->isBackupRunning();
    }

    public function loadBackupHistory()
    {
        $backupService = new DatabaseBackupService();
        $this->backupHistory = $backupService->getBackupHistory(10); // Last 10 backups
    }

    public function saveBackupSettings()
    {
        $branchId = current_branch_id();
        $settings = BranchBusinessConfiguration::where('branch_id', $branchId)->first();

        if (!$settings) {
            $settings = new BranchBusinessConfiguration();
            $settings->branch_id = $branchId;
        }

        $storageSettings = $settings->storage_settings ?? [];
        $storageSettings['auto_backup'] = $this->autoBackup;
        $storageSettings['backup_interval'] = $this->backupInterval;
        $storageSettings['backup_period'] = $this->backupPeriod;

        $settings->storage_settings = $storageSettings;
        $settings->save();

        // Clear cache
        Settings::clearCache();

        // Update scheduler
        $this->updateBackupSchedule();

        session()->flash('message', 'Backup settings updated successfully!');
    }

    public function runManualBackup()
    {
        $this->isBackupRunning = true;

        try {
            $backupService = new DatabaseBackupService();
            $result = $backupService->performBackup();

            if ($result['success']) {
                session()->flash('message', 'Manual backup completed successfully!');
            } else {
                session()->flash('error', 'Manual backup failed: ' . $result['error']);
            }

            $this->loadBackupStatus();
            $this->loadBackupHistory();
        } catch (\Exception $e) {
            session()->flash('error', 'Backup failed: ' . $e->getMessage());
        } finally {
            $this->isBackupRunning = false;
        }
    }

    public function deleteBackup($backupId)
    {
        try {
            $backupService = new DatabaseBackupService();
            $result = $backupService->deleteBackup($backupId);

            if ($result['success']) {
                session()->flash('message', 'Backup deleted successfully!');
            } else {
                session()->flash('error', 'Failed to delete backup: ' . $result['error']);
            }

            $this->loadBackupHistory();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete backup: ' . $e->getMessage());
        }
    }

    public function downloadBackup($backupId)
    {
        try {
            $backupService = new DatabaseBackupService();
            return $backupService->downloadBackup($backupId);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to download backup: ' . $e->getMessage());
            return null;
        }
    }

    private function updateBackupSchedule()
    {
        // Update the Laravel scheduler
        Artisan::call('schedule:run');
    }

    public function render()
    {
        return view('livewire.branch-dashboard.settings.backup-management');
    }
}
```

#### 4. Backup Monitoring and Alerts
**Problem Areas:**
- Backup failures not monitored
- No alerts for backup issues
- Storage monitoring not implemented

**Required Changes:**
```php
// app/Services/BackupMonitoringService.php
class BackupMonitoringService
{
    public function checkBackupHealth(): array
    {
        $autoBackup = Settings::businessConfiguration('auto_backup', false);
        $interval = Settings::businessConfiguration('backup_interval', 2);
        $period = Settings::businessConfiguration('backup_period', 'months');

        $lastBackup = $this->getLastSuccessfulBackup();
        $nextExpectedBackup = $this->calculateNextExpectedBackup($lastBackup, $interval, $period);
        $storageUsed = $this->calculateStorageUsage();

        return [
            'auto_backup_enabled' => $autoBackup,
            'last_backup' => $lastBackup,
            'next_expected_backup' => $nextExpectedBackup,
            'is_overdue' => $nextExpectedBackup->isPast() && $autoBackup,
            'storage_used_percentage' => $storageUsed,
            'status' => $this->getBackupStatus($lastBackup, $nextExpectedBackup, $autoBackup)
        ];
    }

    public function sendBackupAlerts(): void
    {
        $health = $this->checkBackupHealth();

        if ($health['is_overdue']) {
            $this->sendOverdueAlert($health);
        }

        if ($health['storage_used_percentage'] > 80) {
            $this->sendStorageAlert($health);
        }
    }

    private function sendOverdueAlert(array $health): void
    {
        $message = "Backup is overdue. Last successful backup was on {$health['last_backup']}. Expected by {$health['next_expected_backup']}.";
        
        // Send to system administrators
        $this->notifyAdministrators('Backup Overdue Alert', $message, 'warning');
    }

    private function sendStorageAlert(array $health): void
    {
        $message = "Backup storage usage is at {$health['storage_used_percentage']}%. Consider cleaning up old backups.";
        
        // Send to system administrators
        $this->notifyAdministrators('Backup Storage Alert', $message, 'error');
    }

    private function notifyAdministrators(string $subject, string $message, string $level): void
    {
        $admins = User::role('Super Admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new SystemAlertNotification($subject, $message, $level));
        }
    }

    private function getLastSuccessfulBackup(): ?Carbon
    {
        $backupService = new DatabaseBackupService();
        $lastBackup = $backupService->getLastBackupInfo();
        
        return $lastBackup ? Carbon::parse($lastBackup['created_at']) : null;
    }

    private function calculateNextExpectedBackup(?Carbon $lastBackup, int $interval, string $period): Carbon
    {
        if (!$lastBackup) {
            return now()->addHours(1); // Expect backup soon if never run
        }

        return match ($period) {
            'days' => $lastBackup->addDays($interval),
            'weeks' => $lastBackup->addWeeks($interval),
            'months' => $lastBackup->addMonths($interval),
            default => $lastBackup->addDay()
        };
    }

    private function calculateStorageUsage(): float
    {
        $backupPath = storage_path('app/backups');
        $totalSpace = disk_total_space($backupPath);
        $freeSpace = disk_free_space($backupPath);
        
        if ($totalSpace === false || $freeSpace === false) {
            return 0;
        }

        return (($totalSpace - $freeSpace) / $totalSpace) * 100;
    }

    private function getBackupStatus(?Carbon $lastBackup, Carbon $nextExpected, bool $autoBackup): string
    {
        if (!$autoBackup) {
            return 'disabled';
        }

        if (!$lastBackup) {
            return 'never_run';
        }

        if ($nextExpected->isPast()) {
            return 'overdue';
        }

        return 'healthy';
    }
}
```

### Implementation Priority
1. **Backup Management UI** - High (user control)
2. **Manual Backup Triggers** - High (user control)
3. **Backup History** - Medium (transparency)
4. **Monitoring Alerts** - Medium (reliability)
5. **Storage Management** - Low (maintenance)

### Files to Update
- `app/Console/Commands/DatabaseBackupCommand.php`
- `app/Console/Kernel.php`
- Create `app/Livewire/BranchDashboard/Settings/BackupManagement.php`
- Create `app/Services/BackupMonitoringService.php`
- Update settings index to include backup management tab

### Testing Requirements
- Test manual backup triggers
- Verify backup schedule updates
- Test backup history display
- Validate backup monitoring alerts
- Test backup download and deletion