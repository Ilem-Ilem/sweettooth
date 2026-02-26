<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StuckCallbackNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $callbacksSummary;
    public array $branchContext;

    public function __construct(array $callbacksSummary, array $branchContext)
    {
        $this->callbacksSummary = $callbacksSummary;
        $this->branchContext = $branchContext;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $branchName = $this->branchContext['branch_name'] ?? 'Branch';
        return (new MailMessage)
            ->subject("Stuck Callbacks Alert ({$branchName})")
            ->greeting("Hi {$notifiable->name},")
            ->line('Some production callbacks are stuck and need attention.')
            ->action('View Callbacks', route('branch-dashboard.production.callbacks.approve', ['b_id' => $this->branchContext['branch_id'] ?? null]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'stuck_callback_alert',
            'branch_id' => $this->branchContext['branch_id'] ?? null,
            'branch_name' => $this->branchContext['branch_name'] ?? null,
            'summary' => $this->callbacksSummary,
            'message' => 'Stuck callbacks detected.',
            'action_url' => route('branch-dashboard.production.callbacks.approve', ['b_id' => $this->branchContext['branch_id'] ?? null]),
            'action_text' => 'View Callbacks',
        ];
    }
}
