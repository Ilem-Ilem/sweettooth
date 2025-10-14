<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory, HasUuids, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'location',
        'phone',
        'email',
        'description',
        'manager_user_id',
        'country',
        'state',
        'city',
        'postal_code',
        'timezone',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the manager of the branch.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }
}
