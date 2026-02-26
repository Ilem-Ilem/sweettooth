<?php

namespace App\Notifications;

use App\Models\ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemRequestCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ItemRequest $request;

    public function __construct(ItemRequest $request)
    {
        $this->request = $request;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Item Request')
            ->greeting("Hi {$notifiable->name},")
            ->line("New item request {$this->request->request_number} created.")
            ->action('View Requests', route('branch-dashboard.inventory.item-requests', ['b_id' => $this->request->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        $this->request->loadMissing([
            'branch',
            'department',
            'requestedBy',
            'requestDetails.item',
            'requestDetails.unitOfMeasure',
        ]);

        $items = $this->request->requestDetails
            ->map(function ($detail) {
                $itemName = $detail->item?->name ?? 'Unknown Item';
                $qty = number_format((float) $detail->quantity_requested, 2);
                $uom = $detail->unitOfMeasure?->symbol ?? $detail->item?->unitOfMeasure?->symbol ?? null;

                return $uom ? "{$itemName} x {$qty} {$uom}" : "{$itemName} x {$qty}";
            })
            ->values()
            ->toArray();

        $requestedByRole = null;
        if ($this->request->requestedBy && method_exists($this->request->requestedBy, 'getRoleNames')) {
            $requestedByRole = $this->request->requestedBy->getRoleNames()->implode(', ');
        }

        return [
            'type' => 'item_request_created',
            'item_request_id' => $this->request->id,
            'branch_id' => $this->request->branch_id,
            'branch_name' => $this->request->branch?->name,
            'display_type' => 'Inventory request created',
            'request_number' => $this->request->request_number,
            'message' => "Request {$this->request->request_number} sent to inventory.",
            'requesting_department' => $this->request->department?->name,
            'requested_by' => $this->request->requestedBy?->name,
            'requested_by_role' => $requestedByRole,
            'urgency' => $this->request->priority ?? 'normal',
            'status' => $this->request->status,
            'items' => $items,
            'action_url' => route('branch-dashboard.inventory.item-requests', ['b_id' => $this->request->branch_id]),
            'action_text' => 'View Requests',
        ];
    }
}
