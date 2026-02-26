<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HealthCheckAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public array $checks;
    public array $branchContext;

    public function __construct(array $checks, array $branchContext)
    {
        $this->checks = $checks;
        $this->branchContext = $branchContext;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = count($this->checks);
        $branchName = $this->branchContext['branch_name'] ?? 'Branch';

        return (new MailMessage)
            ->subject("Inventory Health Alert ({$branchName})")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$count} inventory health check(s) require action.")
            ->action('View Inventory', route('branch-dashboard.dashboard.inventory', ['b_id' => $this->branchContext['branch_id'] ?? null]));
    }

    public function toArray(object $notifiable): array
    {
        $count = count($this->checks);
        $branchName = $this->branchContext['branch_name'] ?? 'Branch';
        return [
            'type' => 'health_check_alert',
            'branch_id' => $this->branchContext['branch_id'] ?? null,
            'branch_name' => $branchName,
            'checks' => $this->checks,
            'title' => 'Inventory Health Alert',
            'message' => "{$count} inventory health check(s) require action.",
            'summary' => 'Review flagged inventory checks.',
            'context' => [
                'branch' => $branchName,
                'status' => 'Pending',
            ],
            'action_url' => route('branch-dashboard.dashboard.inventory', ['b_id' => $this->branchContext['branch_id'] ?? null]),
            'action_text' => 'View Inventory',
        ];
    }
}
