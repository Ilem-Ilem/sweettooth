<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_transfer_id',
        'item_id',
        'uom_id',
        'quantity',
        'quantity_dispatched',
        'quantity_received',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'quantity_dispatched' => 'decimal:2',
        'quantity_received' => 'decimal:2',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(DepartmentTransfer::class, 'department_transfer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitOfMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitOfMeasure::class, 'uom_id');
    }
}
