<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExpiredItemsAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public array $items;
    public array $branchContext;

    public function __construct(array $items, array $branchContext)
    {
        $this->items = $items;
        $this->branchContext = $branchContext;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $count = count($this->items);
        $branchName = $this->branchContext['branch_name'] ?? 'Branch';

        return (new MailMessage)
            ->subject("Expired Items Alert ({$branchName})")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$count} item(s) are expired.")
            ->line('Please review and take action.')
            ->action('View Inventory', route('branch-dashboard.dashboard.inventory', ['b_id' => $this->branchContext['branch_id'] ?? null]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'expired_items_alert',
            'branch_id' => $this->branchContext['branch_id'] ?? null,
            'branch_name' => $this->branchContext['branch_name'] ?? null,
            'items' => $this->items,
            'message' => count($this->items).' item(s) are expired.',
            'action_url' => route('branch-dashboard.dashboard.inventory', ['b_id' => $this->branchContext['branch_id'] ?? null]),
            'action_text' => 'View Inventory',
        ];
    }
}
