<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'department_id',
        'request_number',
        'requested_by',
        'request_date',
        'required_date',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Scope to filter requests by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope to filter by department
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
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
     * Get the department
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employee who made the request
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    /**
     * Get the employee who approved the request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    /**
     * Get the request details
     */
    public function requestDetails(): HasMany
    {
        return $this->hasMany(ItemRequestDetail::class, 'request_id');
    }

    /**
     * Get the item dispatches for this request
     */
    public function itemDispatches(): HasMany
    {
        return $this->hasMany(ItemDispatch::class, 'request_id');
    }

    /**
     * Generate unique request number
     */
    public static function generateRequestNumber($branchCode, $departmentCode): string
    {
        $date = now()->format('Ymd');
        $lastRequest = static::where('request_number', 'like', "REQ-{$branchCode}-{$departmentCode}-{$date}-%")
            ->orderBy('request_number', 'desc')
            ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "REQ-{$branchCode}-{$departmentCode}-{$date}-{$newNumber}";
    }

    /**
     * Check if request can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending' && $this->requestDetails()->count() > 0;
    }

    /**
     * Check if request is fully dispatched
     */
    public function isFullyDispatched(): bool
    {
        return $this->requestDetails()
            ->whereColumn('quantity_dispatched', '<', 'quantity_approved')
            ->doesntExist();
    }
}
