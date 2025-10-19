<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'item_request_id',
        'recipe_id',
        'planned_production_quantity',
        'notes',
    ];

    protected $casts = [
        'planned_production_quantity' => 'decimal:2',
    ];

    /**
     * Scope to filter by shift
     */
    public function scopeForShift($query, $shiftId)
    {
        return $query->where('shift_id', $shiftId);
    }

    /**
     * Scope to filter by branch (via item request)
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->whereHas('itemRequest', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }

    /**
     * Get the shift this production request is assigned to
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Get the item request that triggered this production
     */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    /**
     * Get the recipe to be used for production
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->withDefault();
    }

    /**
     * Check if production request has been assigned a recipe
     */
    public function hasRecipe(): bool
    {
        return !is_null($this->recipe_id);
    }

    /**
     * Check if production request is ready for production
     */
    public function isReadyForProduction(): bool
    {
        return $this->shift_id &&
               $this->recipe_id &&
               $this->planned_production_quantity > 0;
    }

    /**
     * Get the computed status based on item request details
     */
    public function getComputedStatus(): string
    {
        if (!$this->itemRequest) {
            return 'unknown';
        }

        // Check if cancelled
        if ($this->itemRequest->status === 'cancelled') {
            return 'cancelled';
        }

        // Check request details to determine status
        $details = $this->itemRequest->requestDetails;

        if ($details->isEmpty()) {
            return 'pending';
        }

        $allCompleted = true;
        $anyPartiallyDispatched = false;
        $anyPartiallyApproved = false;
        $anyApproved = false;
        $allPending = true;

        foreach ($details as $detail) {
            $requested = (float) $detail->quantity_requested;
            $approved = (float) $detail->quantity_approved;
            $dispatched = (float) $detail->quantity_dispatched;

            // Check if this item is pending (approved = 0, dispatched = 0)
            if ($approved > 0 || $dispatched > 0) {
                $allPending = false;
            }

            // Check if this item is completed (requested = approved = dispatched AND not 0)
            if (!($requested == $approved && $approved == $dispatched && $requested > 0)) {
                $allCompleted = false;
            }

            // Check if partially dispatched (dispatched > 0 but < approved)
            if ($dispatched > 0 && $dispatched < $approved) {
                $anyPartiallyDispatched = true;
            }

            // Check if partially approved (approved > 0 but < requested, and dispatched = 0)
            if ($approved > 0 && $approved < $requested) {
                $anyPartiallyApproved = true;
            }

            // Check if fully approved (requested = approved > 0, dispatched = 0)
            if ($approved > 0 && $requested == $approved && $dispatched == 0) {
                $anyApproved = true;
            }
        }

        // Determine status based on priority
        if ($allCompleted) {
            return 'completed';
        } elseif ($anyPartiallyDispatched) {
            return 'partially_dispatched';
        } elseif ($anyPartiallyApproved) {
            return 'partially_approved';
        } elseif ($anyApproved) {
            return 'approved';
        } elseif ($allPending) {
            return 'pending';
        }

        return 'pending';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor(): string
    {
        $status = $this->getComputedStatus();

        return match($status) {
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'partially_dispatched' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'partially_approved' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'approved' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            default => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900 dark:text-zinc-200',
        };
    }

    /**
     * Check if request can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->itemRequest &&
               $this->itemRequest->status !== 'cancelled' &&
               $this->getComputedStatus() !== 'completed';
    }
}