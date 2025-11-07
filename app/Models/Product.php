<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'product_type_id',
        'category_id',
        'description',
        'price',
        'cost',
        'shelf_life_days',
        'uom',
        'unit_weight',
        'is_active',
        'is_available',
        'image_url',
        'allergens',
        'tags',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'shelf_life_days' => 'integer',
        'unit_weight' => 'decimal:2',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'allergens' => 'array',
        'tags' => 'array',
    ];

    /**
     * Scope to filter active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter available products
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter by product type
     */
    public function scopeByType($query, $typeId)
    {
        return $query->where('product_type_id', $typeId);
    }

    /**
     * Scope to filter products that belong to a specific department
     * Only returns products that are available in that department
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $departmentId Department ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->whereHas('departments', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId)
              ->where('is_available', true);
        });
    }

    /**
     * Get the product type that owns the product
     */
    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Get the recipes for this product
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Get the departments that this product belongs to
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_product')
            ->withPivot(['is_available', 'department_price', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Calculate profit margin
     */
    public function getProfitMarginAttribute(): ?float
    {
        if (!$this->cost || $this->cost == 0) {
            return null;
        }

        return (($this->price - $this->cost) / $this->cost) * 100;
    }

    /**
     * Check if product has allergens
     */
    public function hasAllergens(): bool
    {
        return !empty($this->allergens);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Calculate how many batches needed to produce desired quantity
     *
     * @param float $desiredQuantity Number of units to produce
     * @return float Number of recipe batches needed
     */
    public function calculateBatchesNeeded(float $desiredQuantity): float
    {
        // Get yield from primary recipe
        $recipe = $this->recipes()->first();
        if (!$recipe || $recipe->yield_quantity <= 0) {
            return 0;
        }

        // Simple calculation: desired quantity / yield per batch
        return $desiredQuantity / $recipe->yield_quantity;
    }

    /**
     * Calculate raw material requirements for desired quantity
     *
     * @param float $desiredQuantity Number of units to produce
     * @return array Array of ingredients with scaled quantities
     */
    public function calculateRawMaterialRequirements(float $desiredQuantity): array
    {
        $batchesNeeded = $this->calculateBatchesNeeded($desiredQuantity);

        if (!$this->recipes()->exists()) {
            return [];
        }

        // Get the primary recipe (or first recipe)
        $recipe = $this->recipes()->with('ingredients.item')->first();

        if (!$recipe) {
            return [];
        }

        $requirements = [];

        foreach ($recipe->ingredients as $ingredient) {
            $scaledQuantity = $ingredient->quantity * $batchesNeeded;

            $requirements[] = [
                'item_id' => $ingredient->item_id,
                'item_name' => $ingredient->item->name,
                'item_sku' => $ingredient->item->sku,
                'base_quantity' => $ingredient->quantity,
                'required_quantity' => $scaledQuantity,
                'uom' => $ingredient->item->uom,
                'batches_needed' => $batchesNeeded,
            ];
        }

        return $requirements;
    }

    /**
     * Calculate total weight of desired quantity
     *
     * @param float $desiredQuantity Number of units
     * @return float|null Total weight in grams/ml
     */
    public function calculateTotalWeight(float $desiredQuantity): ?float
    {
        if ($this->unit_weight) {
            return $desiredQuantity * $this->unit_weight;
        }

        return null;
    }

    /**
     * Get estimated cost based on recipe ingredients
     *
     * @return float|null Estimated cost per unit
     */
    public function getEstimatedCostAttribute(): ?float
    {
        if (!$this->recipes()->exists()) {
            return $this->cost;
        }

        $recipe = $this->recipes()->with('ingredients.item.purchaseItems')->first();

        if (!$recipe) {
            return $this->cost;
        }

        $totalCost = 0;

        foreach ($recipe->ingredients as $ingredient) {
            // Get average cost from recent purchase items
            $avgCost = $ingredient->item->purchaseItems()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->avg('unit_price');

            if ($avgCost) {
                $totalCost += ($ingredient->quantity * $avgCost);
            }
        }

        // Cost per unit = total recipe cost / recipe yield
        if ($recipe->yield_quantity > 0) {
            return $totalCost / $recipe->yield_quantity;
        }

        return $totalCost;
    }
}
