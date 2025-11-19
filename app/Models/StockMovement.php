<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'moved_by_type',
        'moved_by_id',
        'movement_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    // Automatically include department_name in all collections/JSON
    protected $appends = ['department_name'];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function mover(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'moved_by_type', 'moved_by_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    public function isInbound(): bool
    {
        return in_array($this->type, ['in', 'return']);
    }

    public function isOutbound(): bool
    {
        return in_array($this->type, ['out', 'damaged', 'transfer']);
    }

    /**
     * Smart accessor: returns correct department name for ALL reference types
     */
    public function getDepartmentNameAttribute(): ?string
    {
        if (! $this->reference) {
            return null;
        }

        // 1. Purchase → show "Purchases"
        if (is_a($this->reference, \App\Models\Purchase::class, true)) {
            return 'Purchases';
        }

        // 2. ItemRequest, Issue, Transfer, etc. → try common relations
        $relations = ['department', 'fromDepartment', 'toDepartment', 'from_department', 'to_department'];

        foreach ($relations as $relation) {
            if (method_exists($this->reference, $relation)) {
                $dept = $this->reference->{$relation};
                if ($dept) {
                    return $dept->name ?? $dept;
                }
            }
        }

        // 3. Fallback: show model name
        return class_basename($this->reference);
    }
}
