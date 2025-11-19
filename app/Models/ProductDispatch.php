<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductDispatch extends Model
{
    protected $fillable = [
        'branch_id',
        'daily_produce_id',
        'production_shift_id',
        'sales_shift_id',
        'sales_department_id',
        'product_id',
        'dispatched_by',
        'quantity',
        'received_quantity',
        'uom',
        'dispatch_time',
        'shift_type',
        'dispatch_date',
        'received_by',
        'received_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'dispatch_time' => 'datetime',
        'dispatch_date' => 'date',
        'received_at' => 'datetime',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function dailyProduce(): BelongsTo
    {
        return $this->belongsTo(DailyProduce::class);
    }

    public function productionShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'production_shift_id');
    }

    public function salesShift(): BelongsTo
    {
        return $this->belongsTo(SalesShift::class);
    }

    public function salesDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sales_department_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'dispatched_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }

    public function productDispatchCallbacks(): HasMany
    {
        return $this->hasMany(ProductDispatchCallback::class);
    }

    // Helper Methods
    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'dispatched';
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'dispatched' => 'Pending Receipt',
            'received' => 'Received',
            'rejected' => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'dispatched' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'received' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/30 dark:text-zinc-400',
        };
    }

    // Scopes
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('dispatch_date', $date);
    }

    public function scopeForShiftType($query, $shiftType)
    {
        return $query->where('shift_type', $shiftType);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'dispatched');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeForSalesDepartment($query, $salesDepartmentId)
    {
        return $query->where('sales_department_id', $salesDepartmentId);
    }
}
