<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlert extends Notification implements ShouldQueue
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
            ->subject("Low Stock Alert ({$branchName})")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$count} item(s) are below reorder level.")
            ->line('Please review inventory levels.')
            ->action('View Inventory', route('branch-dashboard.dashboard.inventory', ['b_id' => $this->branchContext['branch_id'] ?? null]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_stock_alert',
            'branch_id' => $this->branchContext['branch_id'] ?? null,
            'branch_name' => $this->branchContext['branch_name'] ?? null,
            'items' => $this->items,
            'message' => count($this->items).' item(s) are below reorder level.',
            'action_url' => route('branch-dashboard.dashboard.inventory', ['b_id' => $this->branchContext['branch_id'] ?? null]),
            'action_text' => 'View Inventory',
        ];
    }
}
