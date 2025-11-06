<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'department_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'discount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helper Methods
    public function calculateSubtotal(): float
    {
        return $this->quantity * $this->unit_price;
    }

    public function calculateTotal(): float
    {
        return $this->subtotal - $this->discount;
    }

    public function updateCalculatedFields(): void
    {
        $this->subtotal = $this->calculateSubtotal();
        $this->total = $this->calculateTotal();
    }

    // Boot method to auto-calculate fields
    protected static function booted(): void
    {
        static::saving(function (SaleItem $saleItem) {
            $saleItem->updateCalculatedFields();
        });

        // Update parent sale totals when sale item changes
        static::saved(function (SaleItem $saleItem) {
            if ($saleItem->sale) {
                $saleItem->sale->calculateTotals();
                $saleItem->sale->saveQuietly();
            }

            // Update product stock - deduct quantity sold
            if ($saleItem->wasRecentlyCreated && $saleItem->sale && $saleItem->sale->salesShift) {
                $productStock = ProductStock::where('sales_shift_id', $saleItem->sale->sales_shift_id)
                    ->where('product_id', $saleItem->product_id)
                    ->where('stock_date', $saleItem->sale->sale_time->format('Y-m-d'))
                    ->first();

                if ($productStock) {
                    $productStock->quantity_sold += $saleItem->quantity;
                    $productStock->amount += $saleItem->total;
                    $productStock->updateCalculatedFields();
                    $productStock->save();
                }
            }
        });

        static::deleted(function (SaleItem $saleItem) {
            if ($saleItem->sale) {
                $saleItem->sale->calculateTotals();
                $saleItem->sale->saveQuietly();
            }

            // Restore product stock when sale item is deleted
            if ($saleItem->sale && $saleItem->sale->salesShift) {
                $productStock = ProductStock::where('sales_shift_id', $saleItem->sale->sales_shift_id)
                    ->where('product_id', $saleItem->product_id)
                    ->where('stock_date', $saleItem->sale->sale_time->format('Y-m-d'))
                    ->first();

                if ($productStock) {
                    $productStock->quantity_sold -= $saleItem->quantity;
                    $productStock->amount -= $saleItem->total;
                    $productStock->updateCalculatedFields();
                    $productStock->save();
                }
            }
        });
    }
}
