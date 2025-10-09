<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTake extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'stock_take_number',
        'stock_take_date',
        'type',
        'conducted_by',
        'status',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'stock_take_date' => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * Scope to filter by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by status
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get the branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the employee who conducted the stock take
     */
    public function conductor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'conducted_by');
    }

    /**
     * Get the employee who verified the stock take
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'verified_by');
    }

    /**
     * Get the stock take details
     */
    public function stockTakeDetails(): HasMany
    {
        return $this->hasMany(StockTakeDetail::class);
    }

    /**
     * Generate unique stock take number
     */
    public static function generateStockTakeNumber($branchCode): string
    {
        $date = now()->format('Ymd');
        $lastStockTake = static::where('stock_take_number', 'like', "ST-{$branchCode}-{$date}-%")
            ->orderBy('stock_take_number', 'desc')
            ->first();

        if ($lastStockTake) {
            $lastNumber = (int) substr($lastStockTake->stock_take_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ST-{$branchCode}-{$date}-{$newNumber}";
    }

    /**
     * Check if stock take has variances
     */
    public function hasVariances(): bool
    {
        return $this->stockTakeDetails()
            ->where('variance_type', '!=', 'match')
            ->exists();
    }

    /**
     * Get total variance count
     */
    public function getTotalVariances(): int
    {
        return $this->stockTakeDetails()
            ->where('variance_type', '!=', 'match')
            ->count();
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->save();
    }

    /**
     * Verify stock take
     */
    public function verify($verifiedBy): void
    {
        $this->status = 'verified';
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->save();
    }
}
