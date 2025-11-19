<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDispatchCallback extends Model
{
    protected $fillable = [
        'product_dispatch_id',
        'sales_shift_id',
        'product_id',
        'recorded_by',
        'quantity',
        'uom',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'received_by',
        'received_at',
        'notes',
        'callback_time',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'callback_time' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    /**
     * Relationship: Product Dispatch
     */
    public function productDispatch(): BelongsTo
    {
        return $this->belongsTo(ProductDispatch::class);
    }

    /**
     * Relationship: Sales Shift
     */
    public function salesShift(): BelongsTo
    {
        return $this->belongsTo(SalesShift::class);
    }

    /**
     * Relationship: Product
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
     * Relationship: Production employee who approved
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    /**
     * Relationship: Production employee who received
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by');
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
        return $query->where('status', 'approved_by_production');
    }

    /**
     * Scope: Received callbacks
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received_by_production');
    }

    /**
     * Scope: Completed callbacks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: By sales shift
     */
    public function scopeBySalesShift($query, $salesShiftId)
    {
        return $query->where('sales_shift_id', $salesShiftId);
    }

    /**
     * Scope: By product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if callback can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if callback can be received
     */
    public function canBeReceived(): bool
    {
        return $this->status === 'approved_by_production';
    }

    /**
     * Approve the callback
     */
    public function approve($employeeId): bool
    {
        if (! $this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'approved_by_production',
            'approved_by' => $employeeId,
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark as received
     */
    public function markAsReceived($employeeId): bool
    {
        if (! $this->canBeReceived()) {
            return false;
        }

        $this->update([
            'status' => 'received_by_production',
            'received_by' => $employeeId,
            'received_at' => now(),
        ]);

        return true;
    }

    /**
     * Complete the callback
     */
    public function complete(): bool
    {
        if ($this->status !== 'received_by_production') {
            return false;
        }

        $this->update([
            'status' => 'completed',
        ]);

        return true;
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
}
