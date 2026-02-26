<?php

namespace App\Notifications;

use App\Models\ItemRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ItemRequestDispatchedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ItemRequest $request;
    /** @var array<int, string> */
    public array $dispatchedItems;

    /**
     * @param array<int, string> $dispatchedItems
     */
    public function __construct(ItemRequest $request, array $dispatchedItems = [])
    {
        $this->request = $request;
        $this->dispatchedItems = $dispatchedItems;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->request->loadMissing([
            'branch',
            'department',
            'requestedBy',
            'dispatcher',
            'requestDetails.item',
            'requestDetails.unitOfMeasure',
        ]);

        $remainingItems = $this->request->requestDetails
            ->map(function ($detail) {
                $remaining = (float) $detail->quantity_approved - (float) $detail->quantity_dispatched;
                if ($remaining <= 0) {
                    return null;
                }

                $itemName = $detail->item?->name ?? 'Unknown Item';
                $qty = number_format($remaining, 2);
                $uom = $detail->unitOfMeasure?->symbol ?? $detail->item?->unitOfMeasure?->symbol ?? null;

                return $uom ? "{$itemName} x {$qty} {$uom}" : "{$itemName} x {$qty}";
            })
            ->filter()
            ->values()
            ->toArray();

        $dispatchedByRole = null;
        if ($this->request->dispatcher && method_exists($this->request->dispatcher, 'getRoleNames')) {
            $dispatchedByRole = $this->request->dispatcher->getRoleNames()->implode(', ');
        }

        return [
            'type' => 'item_request_dispatched',
            'item_request_id' => $this->request->id,
            'branch_id' => $this->request->branch_id,
            'branch_name' => $this->request->branch?->name,
            'display_type' => $this->request->status === 'completed'
                ? 'Inventory request fully dispatched'
                : 'Inventory request partially dispatched',
            'request_number' => $this->request->request_number,
            'title' => $this->request->status === 'completed'
                ? 'Inventory Request Dispatched'
                : 'Inventory Request Partially Dispatched',
            'message' => "Request {$this->request->request_number} was dispatched by inventory.",
            'summary' => $this->request->dispatcher?->name
                ? "Dispatched by {$this->request->dispatcher->name}."
                : 'Dispatched by inventory.',
            'context' => [
                'request_number' => $this->request->request_number,
                'requested_by' => $this->request->requestedBy?->name,
                'dispatched_by' => $this->request->dispatcher?->name,
                'dispatched_by_role' => $dispatchedByRole,
                'department' => $this->request->department?->name,
                'status' => $this->request->status === 'completed' ? 'Success' : 'Pending',
            ],
            'requesting_department' => $this->request->department?->name,
            'requested_by' => $this->request->requestedBy?->name,
            'urgency' => $this->request->priority ?? 'normal',
            'status' => $this->request->status,
            'dispatched_by' => $this->request->dispatcher?->name,
            'dispatched_by_role' => $dispatchedByRole,
            'dispatched_items' => $this->dispatchedItems,
            'remaining_items' => $remainingItems,
            'items_preview' => $this->dispatchedItems,
        ];
    }
}
