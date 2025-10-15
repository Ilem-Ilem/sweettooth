<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRecord extends Model
{
    protected $fillable = [
        'daily_produce_id',
        'recipe_id',
        'produced_by',
        'quantity_produced',
        'quantity_approved',
        'quantity_rejected',
        'production_time',
        'quality_status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'quantity_produced' => 'decimal:2',
        'quantity_approved' => 'decimal:2',
        'quantity_rejected' => 'decimal:2',
        'production_time' => 'datetime',
        'quality_status' => 'string',
    ];

    public function dailyProduce(): BelongsTo
    {
        return $this->belongsTo(DailyProduce::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function producedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'produced_by', 'id');
    }
}