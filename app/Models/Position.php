<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'department_id',
        'role_id',
        'reports_to',
        'level',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'level' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the department that owns the position.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position that this position reports to.
     */
    public function reportsTo()
    {
        return $this->belongsTo(Position::class, 'reports_to');
    }

    /**
     * Get the positions that report to this position.
     */
    public function subordinates()
    {
        return $this->hasMany(Position::class, 'reports_to');
    }

    /**
     * Get the employees in this position.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the role assigned to this position.
     */
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    /**
     * Get the position assignments.
     */
    public function assignments()
    {
        return $this->hasMany(PositionAssignment::class);
    }

    /**
     * Get the level name.
     */
    public function getLevelNameAttribute()
    {
        return match($this->level) {
            1 => 'Executive',
            2 => 'Management',
            3 => 'Staff',
            default => 'Unknown',
        };
    }
}
