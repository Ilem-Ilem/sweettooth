<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyProduce extends Model
{
    protected $fillable = [
        'shift_id',
        'recipe_id',
        'produce_date',
        'shift_type',
        'opening_quantity',
        'requested_quantity',
        'produced_quantity',
        'sent_out_quantity',
        'order_quantity',
        'callback_quantity',
        'closing_quantity',
        'expected_closing',
        'variance',
        'notes',
    ];

    protected $casts = [
        'produce_date' => 'date',
        'shift_type' => 'string',
        'opening_quantity' => 'decimal:2',
        'requested_quantity' => 'decimal:2',
        'produced_quantity' => 'decimal:2',
        'sent_out_quantity' => 'decimal:2',
        'order_quantity' => 'decimal:2',
        'callback_quantity' => 'decimal:2',
        'closing_quantity' => 'decimal:2',
        'expected_closing' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function productionRecords(): HasMany
    {
        return $this->hasMany(ProductionRecord::class);
    }
}