<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemDispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'request_id',
        'item_id',
        'dispatched_by',
        'received_by',
        'quantity',
        'uom',
        'dispatch_time',
        'received_time',
        'shift',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'dispatch_time' => 'datetime',
        'received_time' => 'datetime',
    ];

    /**
     * Scope to filter by shift
     */
    public function scopeForShift($query, string $shift)
    {
        return $query->where('shift', $shift);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('dispatch_time', [$startDate, $endDate]);
    }

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
     * Get the employee who dispatched
     */
    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'dispatched_by');
    }

    /**
     * Get the employee who received
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }

    /**
     * Check if dispatch has been received
     */
    public function isReceived(): bool
    {
        return !is_null($this->received_time);
    }

    /**
     * Mark as received
     */
    public function markAsReceived(): void
    {
        $this->recei = now();
        $this->save();
    }
}
