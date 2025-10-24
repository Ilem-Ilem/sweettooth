<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'sale_id',
        'payment_method',
        'amount',
        'reference_number',
        'payment_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_time' => 'datetime',
    ];

    // Relationships
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    // Helper Methods
    public function isCash(): bool
    {
        return $this->payment_method === 'cash';
    }

    public function isPos(): bool
    {
        return $this->payment_method === 'pos';
    }

    public function isTransfer(): bool
    {
        return $this->payment_method === 'transfer';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }

    public function markAsFailed(): bool
    {
        $this->status = 'failed';
        return $this->save();
    }

    // Boot method
    protected static function booted(): void
    {
        // Update sale payment status when payment changes
        static::saved(function (Payment $payment) {
            if ($payment->sale && $payment->isCompleted()) {
                if ($payment->sale->isFullyPaid()) {
                    $payment->sale->markAsCompleted();
                }
            }
        });
    }
}
