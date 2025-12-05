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
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id')
            ->withDefault(null);
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
     * Avoids lazy-loading by checking if reference is already loaded
     */
    public function getDepartmentNameAttribute(): ?string
    {
        // Handle invalid/missing reference types (manual_adjustment, production_callback, etc.)
        if (!$this->reference_type || strpos($this->reference_type, '\\') === false) {
            return ucfirst(str_replace('_', ' ', $this->reference_type ?? 'Manual'));
        }

        // Only access reference if it's already been loaded to avoid lazy-loading errors
        if (!$this->relationLoaded('reference')) {
            return null;
        }

        $ref = $this->getRelation('reference');
        if (! $ref) {
            return null;
        }

        // 1. Purchase → show "Purchases"
        if (is_a($ref, \App\Models\Purchase::class, true)) {
            return 'Purchases';
        }

        // 2. ItemRequest, Issue, Transfer, etc. → try common relations
        $relations = ['department', 'fromDepartment', 'toDepartment', 'from_department', 'to_department'];

        foreach ($relations as $relation) {
            if (method_exists($ref, $relation)) {
                $dept = $ref->{$relation};
                if ($dept) {
                    return $dept->name ?? $dept;
                }
            }
        }

        // 3. Fallback: show model name
        return class_basename($ref);
    }
}
