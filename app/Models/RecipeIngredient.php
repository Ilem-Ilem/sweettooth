<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RecipeIngredient extends Model
{
    protected $fillable = [
        'recipe_id',
        'item_id',
        'quantity',
        'uom_id',
        'cost_per_unit',
        'waste_percentage',
        'sort_order',
        'notes',
        'preparation_notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'cost_per_unit' => 'decimal:4',
        'waste_percentage' => 'decimal:2',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'uom_id');
    }

    /**
     * Accessor for UOM symbol
     */
    protected function uomSymbol(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->unitOfMeasure?->symbol ?? 'N/A',
        );
    }

    /**
     * Calculate the actual quantity needed including waste
     */
    public function getActualQuantityNeeded(): float
    {
        $baseQuantity = (float) $this->quantity;
        $wastePercent = (float) $this->waste_percentage;

        if ($wastePercent > 0) {
            return $baseQuantity * (1 + ($wastePercent / 100));
        }

        return $baseQuantity;
    }

    /**
     * Calculate the total cost for this ingredient
     */
    public function getTotalCost(): float
    {
        return $this->getActualQuantityNeeded() * (float) $this->cost_per_unit;
    }

    /**
     * Calculate ingredient cost for a specific batch size
     */
    public function getCostForBatchSize(int $batchSize): float
    {
        return $this->getTotalCost() * $batchSize;
    }

    /**
     * Calculate quantity needed for a specific batch size
     */
    public function getQuantityForBatchSize(int $batchSize): float
    {
        return $this->getActualQuantityNeeded() * $batchSize;
    }
}
