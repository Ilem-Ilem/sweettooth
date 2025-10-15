<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallBack extends Model
{
    protected $fillable = [
        'shift_id',
        'callback_type',
        'reference_id',
        'quantity',
        'uom',
        'reason',
        'description',
        'reported_by',
        'callback_time',
        'action_taken',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'uom' => 'string',
        'reason' => 'string',
        'callback_time' => 'datetime',
        'action_taken' => 'string',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reported_by', 'id');
    }
}