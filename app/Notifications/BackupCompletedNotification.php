<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupCompletedNotification extends Notification
{
    use Queueable;

    /**
     * Backup result data
     */
    protected $result;

    /**
     * Whether the backup was successful
     */
    protected $success;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $result, bool $success)
    {
        $this->result = $result;
        $this->success = $success;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        if ($this->success) {
            return (new MailMessage)
                ->subject('Database Backup Completed Successfully')
                ->line('Your database backup has been completed successfully.')
                ->line('Filename: '.($this->result['filename'] ?? 'N/A'))
                ->line('Size: '.($this->result['size'] ?? 'N/A'))
                ->line('Thank you for using our application!');
        }

        return (new MailMessage)
            ->subject('Database Backup Failed')
            ->error()
            ->line('Your database backup has failed.')
            ->line('Error: '.($this->result['message'] ?? 'Unknown error'))
            ->line('Please check the logs for more details.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $filename = $this->result['filename'] ?? null;
        $size = $this->result['size'] ?? null;
        $errorMessage = $this->result['message'] ?? 'Unknown error';
        return [
            'type' => 'database_backup',
            'success' => $this->success,
            'title' => $this->success ? 'Database Backup Completed' : 'Database Backup Failed',
            'message' => $this->success
                ? 'Database backup completed successfully.'
                : "Database backup failed: {$errorMessage}",
            'summary' => $this->success ? 'Backup created successfully.' : 'Action required: review backup error.',
            'context' => [
                'filename' => $filename,
                'size' => $size,
                'status' => $this->success ? 'Success' : 'Failed',
            ],
            'filename' => $filename,
            'size' => $size,
            'path' => $this->result['path'] ?? null,
            'timestamp' => now()->toDateTimeString(),
        ];
    }
}
