<?php

namespace App\Models;

use App\Enums\CallbackStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use InvalidArgumentException;
use Illuminate\Validation\ValidationException;

class ProductDispatchCallback extends Model
{
    protected $fillable = [
        'product_dispatch_id',
        'sales_shift_id',
        'product_id',
        'recorded_by_id',
        'recorded_by_type',
        'quantity',
        'uom',
        'reason',
        'status',
        'approved_by_id',
        'approved_by_type',
        'approved_at',
        'received_by_id',
        'received_by_type',
        'received_at',
        'notes',
        'callback_time',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'callback_time' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'status' => CallbackStatus::class,
    ];

    /**
     * Boot the model with validation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($callback) {
            $callback->validateQuantity();
        });

        static::updating(function ($callback) {
            // Only validate quantity if quantity changed
            if ($callback->isDirty('quantity')) {
                $callback->validateQuantity();
            }
        });
    }

    /**
     * Relationship: Product Dispatch
     */
    public function productDispatch(): BelongsTo
    {
        return $this->belongsTo(ProductDispatch::class);
    }

    /**
     * Relationship: Sales Shift
     */
    public function salesShift(): BelongsTo
    {
        return $this->belongsTo(SalesShift::class);
    }

    /**
     * Relationship: Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship: User or Employee who recorded the callback (polymorphic)
     */
    public function recordedBy(): MorphTo
    {
        return $this->morphTo('recorded_by', 'recorded_by_type', 'recorded_by_id');
    }

    /**
     * Relationship: User or Employee who approved (polymorphic)
     */
    public function approvedBy(): MorphTo
    {
        return $this->morphTo('approved_by', 'approved_by_type', 'approved_by_id');
    }

    /**
     * Relationship: User or Employee who received (polymorphic)
     */
    public function receivedBy(): MorphTo
    {
        return $this->morphTo('received_by', 'received_by_type', 'received_by_id');
    }

    /**
     * Scope: Pending callbacks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Approved callbacks
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved_by_production');
    }

    /**
     * Scope: Received callbacks
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'received_by_production');
    }

    /**
     * Scope: Completed callbacks
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: By sales shift
     */
    public function scopeBySalesShift($query, $salesShiftId)
    {
        return $query->where('sales_shift_id', $salesShiftId);
    }

    /**
     * Scope: By product
     */
    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Check if callback can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if callback can be received
     */
    public function canBeReceived(): bool
    {
        return $this->status === 'approved_by_production';
    }

    /**
     * Approve the callback
     */
    public function approve($actor = null): bool
    {
        if (! $this->canBeApproved()) {
            return false;
        }

        $actor = $actor ?? current_actor();
        if (! $actor) {
            throw new RuntimeException('No authenticated actor found');
        }

        $this->update([
            'status' => 'approved_by_production',
            'approved_by_id' => $actor->id,
            'approved_by_type' => get_class($actor),
            'approved_at' => now(),
        ]);

        return true;
    }

    /**
     * Mark as received
     */
    public function markAsReceived($actor = null): bool
    {
        if (! $this->canBeReceived()) {
            return false;
        }

        $actor = $actor ?? current_actor();
        if (! $actor) {
            throw new RuntimeException('No authenticated actor found');
        }

        $this->update([
            'status' => 'received_by_production',
            'received_by_id' => $actor->id,
            'received_by_type' => get_class($actor),
            'received_at' => now(),
        ]);

        return true;
    }

    /**
     * Complete the callback
     */
    public function complete(): bool
    {
        if ($this->status !== 'received_by_production') {
            return false;
        }

        $this->update([
            'status' => 'completed',
        ]);

        return true;
    }

    /**
     * Mark callback as approved and received (convenience method)
     */
    public function approveAndReceive($actor = null): bool
    {
        if (!$this->approve($actor)) {
            return false;
        }

        return $this->markAsReceived($actor);
    }

    /**
     * Mark callback as approved, received, and completed (full workflow)
     */
    public function approveReceiveAndComplete($actor = null): bool
    {
        if (!$this->approve($actor)) {
            return false;
        }

        if (!$this->markAsReceived($actor)) {
            return false;
        }

        return $this->completeWithStockUpdate();
    }

    /**
     * Get formatted reason
     */
    public function getFormattedReasonAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->reason));
    }

    /**
     * Get formatted status
     */
    public function getFormattedStatusAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Validate callback quantity against available quantity
     *
     * @throws ValidationException
     * @return bool
     */
    public function validateQuantity(): bool
    {
        if (! $this->product_dispatch_id) {
            // Orphaned callback - no dispatch reference
            return true;
        }

        if (! $this->productDispatch) {
            throw new RuntimeException('No dispatch found for callback');
        }

        $totalCallbacks = self::where('product_dispatch_id', $this->product_dispatch_id)
            ->where('id', '!=', $this->id ?? 'fake-id')
            ->whereIn('status', ['pending', 'approved_by_production', 'received_by_production', 'completed'])
            ->sum('quantity');

        $available = $this->productDispatch->received_quantity - $totalCallbacks;

        if ($this->quantity > $available) {
            throw new ValidationException(
                "Callback quantity ({$this->quantity}) exceeds available ({$available})"
            );
        }

        return true;
    }

    /**
     * Complete the callback with automatic stock updates
     *
     * @throws RuntimeException
     * @return bool
     */
    public function completeWithStockUpdate(): bool
    {
        if ($this->status !== 'received_by_production') {
            throw new RuntimeException(
                'Callback must be received before completion. Current status: '.$this->status
            );
        }

        return DB::transaction(function () {
            $this->updateProductStock();
            $this->updateDailyProduce();
            $this->update(['status' => 'completed']);
            return true;
        });
    }

    /**
     * Update ProductStock when product is returned to sales
     *
     * @throws RuntimeException
     * @return void
     */
    private function updateProductStock(): void
    {
        $productStock = ProductStock::where('sales_shift_id', $this->sales_shift_id)
            ->where('product_id', $this->product_id)
            ->lockForUpdate()
            ->first();

        if (! $productStock) {
            throw new RuntimeException(
                'Product stock record not found for sales shift: '.$this->sales_shift_id
            );
        }

        $productStock->increment('callback_quantity', $this->quantity);
        // This should trigger ProductStock::booted() to recalculate total_available
    }

    /**
     * Update DailyProduce when product is returned to production
     *
     * @throws RuntimeException
     * @return void
     */
    private function updateDailyProduce(): void
    {
        // Find the original production that created this product
        $productDispatch = $this->productDispatch;
        if (! $productDispatch || ! $productDispatch->shift_id) {
            throw new RuntimeException('Product dispatch or shift not found for callback');
        }

        $recipe = Recipe::where('product_id', $this->product_id)->first();
        if (! $recipe) {
            throw new RuntimeException('Recipe not found for product: '.$this->product_id);
        }

        $dailyProduce = DailyProduce::where('shift_id', $productDispatch->shift_id)
            ->where('recipe_id', $recipe->id)
            ->lockForUpdate()
            ->first();

        if ($dailyProduce) {
            $dailyProduce->increment('callback_quantity', $this->quantity);
            $dailyProduce->increment('closing_quantity', $this->quantity);
            $dailyProduce->updateCalculations();
        } else {
            Log::warning('DailyProduce not found for callback', [
                'callback_id' => $this->id,
                'shift_id' => $productDispatch->shift_id,
                'recipe_id' => $recipe->id,
            ]);
        }
    }
}
