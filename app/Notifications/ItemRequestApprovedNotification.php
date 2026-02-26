<?php

namespace App\Notifications;

use App\Models\ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemRequestApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Item Request Approved')
            ->greeting("Hi {$notifiable->name},")
            ->line("Item request {$this->request->request_number} was approved.")
            ->action('View Request', route('branch-dashboard.inventory.item-requests', ['b_id' => $this->request->branch_id]));
    }

    public function toArray(object $notifiable): array
    {
        $this->request->loadMissing([
            'branch',
            'department',
            'requestedBy',
            'approver',
            'requestDetails.item',
            'requestDetails.unitOfMeasure',
        ]);

        $approvedItems = $this->request->requestDetails
            ->map(function ($detail) {
                $itemName = $detail->item?->name ?? 'Unknown Item';
                $qty = number_format((float) $detail->quantity_approved, 2);
                $uom = $detail->unitOfMeasure?->symbol ?? $detail->item?->unitOfMeasure?->symbol ?? null;

                return $uom ? "{$itemName} x {$qty} {$uom}" : "{$itemName} x {$qty}";
            })
            ->values()
            ->toArray();

        $approvedByRole = null;
        if ($this->request->approver && method_exists($this->request->approver, 'getRoleNames')) {
            $approvedByRole = $this->request->approver->getRoleNames()->implode(', ');
        }

        return [
            'type' => 'item_request_approved',
            'item_request_id' => $this->request->id,
            'branch_id' => $this->request->branch_id,
            'branch_name' => $this->request->branch?->name,
            'display_type' => 'Inventory request approved',
            'request_number' => $this->request->request_number,
            'message' => "Request {$this->request->request_number} approved by inventory.",
            'requesting_department' => $this->request->department?->name,
            'requested_by' => $this->request->requestedBy?->name,
            'urgency' => $this->request->priority ?? 'normal',
            'status' => $this->request->status,
            'approved_by' => $this->request->approver?->name,
            'approved_by_role' => $approvedByRole,
            'approved_items' => $approvedItems,
            'action_url' => route('branch-dashboard.inventory.item-requests', ['b_id' => $this->request->branch_id]),
            'action_text' => 'View Request',
        ];
    }
}
