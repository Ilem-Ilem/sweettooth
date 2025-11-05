<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionCallback extends Model
{
    protected $fillable = [
        'shift_id',
        'source_type',
        'item_id',
        'product_id',
        'recorded_by',
        'quantity',
        'uom',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'callback_time',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'callback_time' => 'datetime',
        'approved_at' => 'datetime',
    ];

    /**
     * Relationship: Production Shift
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Relationship: Raw Material Item (if applicable)
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship: Finished Product (if applicable)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: Employee who recorded the callback
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    /**
     * Relationship: Inventory employee who approved
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    /**
     * Scope: Pending callbacks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Approved callbacks
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved_by_inventory');
    }

    /**
     * Scope: Completed callbacks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Rejected callbacks
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: By shift
     */
    public function scopeByShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    /**
     * Scope: Raw material callbacks
     */
    public function scopeRawMaterial($query)
    {
        return $query->where('source_type', 'raw_material_from_stock');
    }

    /**
     * Scope: Finished product callbacks
     */
    public function scopeFinishedProduct($query)
    {
        return $query->where('source_type', 'finished_product_reject');
    }

    /**
     * Check if callback is for raw material
     */
    public function isRawMaterial(): bool
    {
        return $this->source_type === 'raw_material_from_stock';
    }

    /**
     * Check if callback is for finished product
     */
    public function isFinishedProduct(): bool
    {
        return $this->source_type === 'finished_product_reject';
    }

    /**
     * Check if callback can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Approve the callback
     */
    public function approve($employeeId): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved_by_inventory',
            'approved_by' => $employeeId,
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Reject the callback
     */
    public function reject($employeeId, $reason = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $notes = $this->notes;
        if ($reason) {
            $notes .= "\n\nRejected: " . $reason;
        }

        $this->update([
            'status' => 'rejected',
            'approved_by' => $employeeId,
            'approved_at' => now(),
            'notes' => $notes,
        ]);

        return true;
    }

    /**
     * Complete the callback
     */
    public function complete(): bool
    {
        if ($this->status !== 'approved_by_inventory') {
            return false;
        }

        $this->update([
            'status' => 'completed',
        ]);

        return true;
    }

    /**
     * Get formatted source type
     */
    public function getFormattedSourceTypeAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->source_type));
    }

    /**
     * Get formatted reason
     */
    public function getFormattedReasonAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->reason));
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get the item name (raw material or product)
     */
    public function getItemNameAttribute(): string
    {
        if ($this->isRawMaterial() && $this->item) {
            return $this->item->name;
        }

        if ($this->isFinishedProduct() && $this->product) {
            return $this->product->name;
        }

        return 'Unknown';
    }
}
