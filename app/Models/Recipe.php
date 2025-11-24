<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Recipe extends Model
{
    protected $fillable = [
        'branch_id',
        'department_id',
        'product_id',
        'product_name',
        'sku',
        'product_type',
        'cost_per_unit',
        'uom',
        'yield_quantity',
        'preparation_time',
        'instructions',
        'status',
        'created_by_id',
        'created_by_type',
    ];

    protected $casts = [
        'product_type' => 'string',
        'uom' => 'string',
        'status' => 'string',
        'cost_per_unit' => 'decimal:4',
        'yield_quantity' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class)->withDefault();
    // }

    public function createdBy(): MorphTo
    {
        return $this->morphTo('created_by', 'created_by_type', 'created_by_id');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function dailyProduces(): HasMany
    {
        return $this->hasMany(DailyProduce::class);
    }

    public function productionRecords(): HasMany
    {
        return $this->hasMany(ProductionRecord::class);
    }

    public function productionRequests(): HasMany
    {
        return $this->hasMany(ProductionRequest::class);
    }

    public function rawMaterialUtilizations(): HasMany
    {
        return $this->hasMany(RawMaterialUtilization::class);
    }

    /**
     * Calculate total ingredient cost for this recipe
     */
    public function calculateTotalIngredientCost(): float
    {
        return $this->ingredients->sum(function ($ingredient) {
            return $ingredient->getTotalCost();
        });
    }

    /**
     * Calculate cost per unit of final product
     */
    public function calculateCostPerUnit(): float
    {
        $totalCost = $this->calculateTotalIngredientCost();
        $yieldQty = (float) $this->yield_quantity;

        if ($yieldQty <= 0) {
            return 0;
        }

        return $totalCost / $yieldQty;
    }

    /**
     * Calculate ingredients needed for a specific batch size
     */
    public function calculateIngredientsForBatch(int $batchSize): array
    {
        $ingredients = [];

        foreach ($this->ingredients as $ingredient) {
            $ingredients[] = [
                'item_id' => $ingredient->item_id,
                'item_name' => $ingredient->item->name ?? 'N/A',
                'quantity' => $ingredient->getQuantityForBatchSize($batchSize),
                'base_quantity' => (float) $ingredient->quantity,
                'uom' => $ingredient->uom,
                'cost_per_unit' => (float) $ingredient->cost_per_unit,
                'total_cost' => $ingredient->getCostForBatchSize($batchSize),
                'waste_percentage' => (float) $ingredient->waste_percentage,
                'notes' => $ingredient->notes,
                'preparation_notes' => $ingredient->preparation_notes,
            ];
        }

        return $ingredients;
    }

    /**
     * Calculate total cost for a specific batch size
     */
    public function calculateTotalCostForBatch(int $batchSize): float
    {
        return $this->ingredients->sum(function ($ingredient) use ($batchSize) {
            return $ingredient->getCostForBatchSize($batchSize);
        });
    }

    /**
     * Get recipe instructions as array
     */
    public function getInstructionsArray(): array
    {
        if (empty($this->instructions)) {
            return [];
        }

        $decoded = json_decode($this->instructions, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Calculate final yield for a batch size
     */
    public function calculateYieldForBatch(int $batchSize): float
    {
        return (float) $this->yield_quantity * $batchSize;
    }
}
