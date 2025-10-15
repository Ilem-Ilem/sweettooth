<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    protected $fillable = [
        'branch_id',
        'department_id',
        'product_name',
        'sku',
        'category_id',
        'product_type',
        'cost_per_unit',
        'uom',
        'yield_quantity',
        'preparation_time',
        'instructions',
        'status',
        'created_by',
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

    // public function category(): BelongsTo
    // {
    //     return $this->belongsTo(Category::class)->withDefault();
    // }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'created_by', 'id');
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
}