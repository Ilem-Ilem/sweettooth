<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'moved_by',
        'movement_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_before' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    /**
     * Scope to filter by movement type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the stock record
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    /**
     * Get the employee who moved the stock
     */
    public function mover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'moved_by');
    }

    /**
     * Get the reference (polymorphic relationship)
     */
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }

    /**
     * Check if movement is inbound
     */
    public function isInbound(): bool
    {
        return in_array($this->type, ['in', 'return']);
    }

    /**
     * Check if movement is outbound
     */
    public function isOutbound(): bool
    {
        return in_array($this->type, ['out', 'damaged', 'transfer']);
    }
}
