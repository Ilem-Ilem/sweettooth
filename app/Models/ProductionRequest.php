<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'item_request_id',
        'recipe_id',
        'planned_production_quantity',
        'notes',
    ];

    protected $casts = [
        'planned_production_quantity' => 'decimal:2',
    ];

    /**
     * Scope to filter by shift
     */
    public function scopeForShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    /**
     * Scope to filter by branch (via item request)
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->whereHas('itemRequest', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }

    /**
     * Get the shift this production request is assigned to
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Get the item request that triggered this production
     */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    /**
     * Get the recipe to be used for production
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->withDefault();
    }

    /**
     * Check if production request has been assigned a recipe
     */
    public function hasRecipe(): bool
    {
        return !is_null($this->recipe_id);
    }

    /**
     * Check if production request is ready for production
     */
    public function isReadyForProduction(): bool
    {
        return $this->shift_id &&
               $this->recipe_id &&
               $this->planned_production_quantity > 0;
    }
}