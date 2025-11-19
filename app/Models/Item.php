<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'sku',
        'category',
        'uom',
        'reorder_level',
        'max_stock_level',
        'status',
    ];

    protected $casts = [
        'reorder_level' => 'decimal:2',
        'max_stock_level' => 'decimal:2',
    ];

    /**
     * Scope to filter items by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get the branch that owns the item
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the stocks for this item
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the purchase items for this item
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Get the item request details for this item
     */
    public function itemRequestDetails(): HasMany
    {
        return $this->hasMany(ItemRequestDetail::class);
    }

    /**
     * Get the item dispatches for this item
     */
    public function itemDispatches(): HasMany
    {
        return $this->hasMany(ItemDispatch::class);
    }

    /**
     * Get the stock take details for this item
     */
    public function stockTakeDetails(): HasMany
    {
        return $this->hasMany(StockTakeDetail::class);
    }

    /**
     * Get current stock for this item at a specific branch
     */
    public function getCurrentStock($branchId = null)
    {
        $branchId = $branchId ?? $this->branch_id;

        return $this->stocks()
            ->where('branch_id', $branchId)
            ->first()?->quantity_available ?? 0;
    }

    /**
     * Check if item is below reorder level
     */
    public function isBelowReorderLevel($branchId = null): bool
    {
        if (! $this->reorder_level) {
            return false;
        }

        return $this->getCurrentStock($branchId) < $this->reorder_level;
    }
}
