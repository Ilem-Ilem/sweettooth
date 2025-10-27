<?php

namespace App\Livewire\SuperAdmin\Settings;

use Livewire\Component;
use App\Services\DatabaseBackupService;
use App\Jobs\DatabaseBackupJob;
use App\Helpers\Settings as SettingsHelper;
use Illuminate\Support\Facades\Storage;
use TallStackUi\Traits\Interactions;

class BackupManagement extends Component
{
    use Interactions;

    public $backups = [];
    public $backupInfo = [];
    public $isBackupRunning = false;
    public $selectedBackup = null;
    public $showBackupDetails = false;
    public $compareBackup1 = null;
    public $compareBackup2 = null;
    public $comparisonResult = null;

    public function mount()
    {
        $this->loadBackups();
        $this->loadBackupInfo();
    }

    public function loadBackups()
    {
        $backupService = app(DatabaseBackupService::class);
        $this->backups = $backupService->listBackups();
    }

    public function loadBackupInfo()
    {
        $backupService = app(DatabaseBackupService::class);

        $this->backupInfo = [
            'auto_backup_enabled' => $backupService->isAutoBackupEnabled(),
            'backup_interval' => $backupService->getBackupInterval(),
            'backup_period' => $backupService->getBackupPeriod(),
            'next_backup' => $backupService->getNextBackupTime()?->format('Y-m-d H:i:s'),
            'next_backup_human' => $backupService->getNextBackupTime()?->diffForHumans(),
            'last_backup' => $backupService->getLastBackupTime()?->format('Y-m-d H:i:s'),
            'last_backup_human' => $backupService->getLastBackupTime()?->diffForHumans(),
            'is_backup_due' => $backupService->isBackupDue(),
            'total_backups' => count($this->backups),
        ];
    }

    public function viewBackupDetails($index)
    {
        if (isset($this->backups[$index])) {
            $this->selectedBackup = $this->backups[$index];
            $this->showBackupDetails = true;
        }
    }

    public function closeBackupDetails()
    {
        $this->showBackupDetails = false;
        $this->selectedBackup = null;
    }

    public function compareBackups()
    {
        if (!$this->compareBackup1 || !$this->compareBackup2) {
            $this->toast()
                ->warning('Please select two backups to compare')
                ->send();
            return;
        }

        $backupService = app(DatabaseBackupService::class);
        $result = $backupService->compareBackups($this->compareBackup1, $this->compareBackup2);

        if ($result['success']) {
            $this->comparisonResult = $result['comparison'];
        } else {
            $this->toast()
                ->error('Comparison Failed', $result['message'])
                ->send();
        }
    }

    public function clearComparison()
    {
        $this->compareBackup1 = null;
        $this->compareBackup2 = null;
        $this->comparisonResult = null;
    }

    public function createBackup()
    {
        try {
            $this->isBackupRunning = true;

            // Dispatch backup job to queue
            DatabaseBackupJob::dispatch(auth()->user());

            $this->toast()
                ->success('Backup Started', 'Database backup has been queued. You will be notified when it completes.')
                ->send();

            // Refresh backup list after a delay
            $this->dispatch('backup-started');
        } catch (\Exception $e) {
            $this->toast()
                ->error('Backup Failed', $e->getMessage())
                ->send();

            $this->isBackupRunning = false;
        }
    }

    public function downloadBackup($filename)
    {
        $backupPath = storage_path("app/backups/{$filename}");

        if (file_exists($backupPath)) {
            return response()->download($backupPath);
        }

        $this->toast()
            ->error('Error', 'Backup file not found.')
            ->send();
    }

    public function deleteBackup($filename)
    {
        try {
            $backupService = app(DatabaseBackupService::class);

            if ($backupService->deleteBackup($filename)) {
                $this->toast()
                    ->success('Deleted', "Backup '{$filename}' has been deleted.")
                    ->send();

                $this->loadBackups();
                $this->loadBackupInfo();
            } else {
                $this->toast()
                    ->error('Error', 'Failed to delete backup file.')
                    ->send();
            }
        } catch (\Exception $e) {
            $this->toast()
                ->error('Error', $e->getMessage())
                ->send();
        }
    }

    public function confirmDelete($filename)
    {
        $this->dialog()
            ->question('Delete Backup?', "Are you sure you want to delete '{$filename}'?")
            ->confirm('Delete', 'deleteBackup', $filename)
            ->cancel('Cancel')
            ->send();
    }

    public function restoreBackup($filename)
    {
        try {
            $backupService = app(DatabaseBackupService::class);
            $result = $backupService->restore($filename);

            if ($result['success']) {
                $this->toast()
                    ->success('Restored', $result['message'])
                    ->send();

                $this->loadBackups();
                $this->loadBackupInfo();
            } else {
                $this->toast()
                    ->error('Restore Failed', $result['message'])
                    ->send();
            }
        } catch (\Exception $e) {
            $this->toast()
                ->error('Error', $e->getMessage())
                ->send();
        }
    }

    public function confirmRestore($filename)
    {
        $this->dialog()
            ->question('Restore Database?', "Are you sure you want to restore from '{$filename}'? This will overwrite your current database!")
            ->confirm('Restore', 'restoreBackup', $filename)
            ->cancel('Cancel')
            ->send();
    }

    public function refreshBackups()
    {
        $this->loadBackups();
        $this->loadBackupInfo();
        $this->isBackupRunning = false;

        $this->toast()
            ->success('Refreshed', 'Backup list has been refreshed.')
            ->send();
    }

    public function render()
    {
        return view('livewire.super-admin.settings.backup-management');
    }
}
