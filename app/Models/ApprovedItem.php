<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovedItem extends Model
{
    public $fillable = [
        'request_id', 'item_id', 'approved_by',
        'branch_id', 'quantity', 'uom', 'approved_time',
        'status', 'shift',  'note',
    ];

    /**
     * Scope to filter by shift
     */
    public function scopeForShift($query, string $shift)
    {
        return $query->where('shift', $shift);
    }

    /**
     * Get the item request
     */
    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class, 'request_id');
    }

    /**
     * Get the item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Mark as received
     */
    public function markAsDispatched(): void
    {
        $this->received_time = now();
        $this->save();
    }
}
