<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'recorded_by_id',
        'recorded_by_type',
        'purchase_number',
        'purchase_date',
        'supplier_name',
        'supplier_contact',
        'total_fob_fc',
        'total_fob_ngn',
        'other_costs',
        'landing_cost',
        'payment_status',
        'notes',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_fob_fc' => 'decimal:2',
        'total_fob_ngn' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'landing_cost' => 'decimal:2',
    ];

    /**
     * Scope to filter purchases by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter draft purchases
     */
    public function scopeDrafts($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope to filter pending approval purchases
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope to filter approved purchases
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Get the branch that owns the purchase
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the employee who recorded the purchase
     */
    public function recorder(): BelongsTo
    {
        return $this->morphTo(
            'recorded_by',
            'recorded_by_type',
            'recorded_by_id'
        );
    }

    /**
     * Get the purchase items for this purchase
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Get the approval request for this purchase
     */
    public function approvalRequest()
    {
        return $this->hasOne(PurchaseApprovalRequest::class);
    }

    /**
     * Get the stock movements created from this purchase
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_id')
            ->where('reference_type', 'App\Models\Purchase');
    }

    /**
     * Calculate total purchase cost
     */
    public function calculateTotalCost(): float
    {
        return $this->total_fob_ngn + $this->other_costs;
    }

    /**
     * Generate unique purchase number
     */
    public static function generatePurchaseNumber($branchCode): string
    {
        $date = now()->format('Ymd');
        $lastPurchase = static::where('purchase_number', 'like', "PUR-{$branchCode}-{$date}-%")
            ->orderBy('purchase_number', 'desc')
            ->first();

        if ($lastPurchase) {
            $lastNumber = (int) substr($lastPurchase->purchase_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "PUR-{$branchCode}-{$date}-{$newNumber}";
    }
}
