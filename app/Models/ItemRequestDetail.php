<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'item_id',
        'quantity_requested',
        'quantity_approved',
        'quantity_dispatched',
        'uom_id',
        'notes',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'quantity_dispatched' => 'decimal:2',
    ];

    /**
     * Get the item request
     */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class, 'request_id');
    }

    /**
     * Get the item
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the unit of measure
     */
    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'uom_id');
    }

    /**
     * Get available stock for this item
     */
    public function getAvailableStock(): float
    {
        $stock = Stock::where('item_id', $this->item_id)
            ->where('branch_id', $this->itemRequest->branch_id)
            ->first();

        if (!$stock) {
            return 0;
        }

        return max(0, (float) $stock->quantity_available);
    }

    /**
     * Get remaining quantity to approve
     */
    public function getRemainingToApprove(): float
    {
        return $this->quantity_requested - $this->quantity_approved;
    }

    /**
     * Get remaining quantity to dispatch
     */
    public function getRemainingToDispatch(): float
    {
        return $this->quantity_approved - $this->quantity_dispatched;
    }

    /**
     * Check if detail is fully approved
     */
    public function isFullyApproved(): bool
    {
        return $this->quantity_approved >= $this->quantity_requested;
    }

    /**
     * Check if detail is fully dispatched
     */
    public function isFullyDispatched(): bool
    {
        return $this->quantity_dispatched >= $this->quantity_approved;
    }
}
