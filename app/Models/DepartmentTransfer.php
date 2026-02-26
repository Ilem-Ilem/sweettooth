<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class DepartmentTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'branch_id',
        'from_department_id',
        'to_department_id',
        'requested_by_id',
        'requested_by_type',
        'receiver_id',
        'receiver_type',
        'status',
        'approved_by_id',
        'approved_by_type',
        'approved_at',
        'dispatched_by_id',
        'dispatched_by_type',
        'dispatched_at',
        'received_by_id',
        'received_by_type',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (DepartmentTransfer $transfer) {
            if (! $transfer->transfer_number) {
                $date = now()->format('Ymd');
                $random = Str::upper(Str::random(4));
                $transfer->transfer_number = "DTR-{$date}-{$random}";
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function requester(): MorphTo
    {
        return $this->morphTo('requested_by');
    }

    public function approver(): MorphTo
    {
        return $this->morphTo('approved_by');
    }

    public function dispatcher(): MorphTo
    {
        return $this->morphTo('dispatched_by');
    }

    public function receiver(): MorphTo
    {
        return $this->morphTo('receiver');
    }

    public function receivedBy(): MorphTo
    {
        return $this->morphTo('received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DepartmentTransferItem::class);
    }
}
