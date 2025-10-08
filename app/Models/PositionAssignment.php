<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PositionAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'position_id',
        'branch_id',
        'department_id',
        'assignment_type',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the position.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the branch (if assigned).
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the department (if assigned).
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope to get active assignments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('end_date')
                           ->orWhere('end_date', '>=', now());
                     });
    }

    /**
     * Get the permissions for this assignment.
     */
    public function getPermissions()
    {
        return $this->position->role ? $this->position->role->permissions : collect();
    }
}
