<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductCallback extends Model
{
    protected $fillable = [
        'product_stock_id',
        'product_id',
        'sales_shift_id',
        'recorded_by',
        'quantity',
        'reason',
        'notes',
        'callback_time',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'callback_time' => 'datetime',
    ];

    // Relationships
    public function productStock(): BelongsTo
    {
        return $this->belongsTo(ProductStock::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function salesShift(): BelongsTo
    {
        return $this->belongsTo(SalesHift::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'recorded_by');
    }

    // Helper Methods
    public function getReasonLabel(): string
    {
        return match($this->reason) {
            'expired' => 'Expired',
            'damaged' => 'Damaged',
            'quality_issue' => 'Quality Issue',
            'customer_return' => 'Customer Return',
            'other' => 'Other',
            default => ucfirst($this->reason),
        };
    }

    public function getReasonBadgeColor(): string
    {
        return match($this->reason) {
            'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'damaged' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            'quality_issue' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'customer_return' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'other' => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400',
            default => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400',
        };
    }

    // Scopes
    public function scopeExpired($query)
    {
        return $query->where('reason', 'expired');
    }

    public function scopeDamaged($query)
    {
        return $query->where('reason', 'damaged');
    }

    public function scopeForShift($query, $shiftId)
    {
        return $query->where('sales_shift_id', $shiftId);
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('callback_time', '>=', now()->subDays($days));
    }
}
