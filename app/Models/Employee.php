<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Authenticatable
{
    use HasUuids, SoftDeletes, Notifiable, TwoFactorAuthenticatable, HasRoles;
    
    protected $guard = 'employees';
    
    protected $fillable = [
        'id', 'branch_id', 'department_id', 'position_id', 'manager_id', 'employee_number', 'name', 'email', 'phone', 'address',
        'date_of_birth', 'gender', 'nationality', 'emergency_contact_name', 'emergency_contact_phone',
        'position', 'hire_date', 'termination_date', 'status', 'probation_end_date', 'shift_preference',
        'salary', 'hourly_rate', 'tax_id', 'bank_account', 'allergies', 'profile_photo',
        'last_performance_review_date', 'performance_rating', 'password'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+
        'is_active' => 'boolean',
    ];
    
    public function initials(): string
    {
        $firstName = $this->name ?? ''; // Adjust field name if different (e.g., $this->name)

        $initials = mb_strtoupper(
            mb_substr($firstName, 0, 1)
        );

        return $initials ?: '??'; // Fallback if no names
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * Get position assignments for this employee.
     */
    public function positionAssignments()
    {
        return $this->hasMany(PositionAssignment::class);
    }

    /**
     * Get active position assignments.
     */
    public function activeAssignments()
    {
        return $this->positionAssignments()->active();
    }

    /**
     * Get the employee's current position assignment.
     */
    public function getCurrentAssignment()
    {
        return $this->activeAssignments()->first();
    }

    /**
     * Get all permissions from active assignments.
     */
    public function getAllPermissionsFromAssignments()
    {
        $permissions = collect();

        foreach ($this->activeAssignments as $assignment) {
            if ($assignment->position && $assignment->position->role) {
                $permissions = $permissions->merge($assignment->position->role->permissions);
            }
        }

        return $permissions->unique('id');
    }

    /**
     * Check if employee has permission through their assignments.
     */
    public function hasPermissionViaAssignment($permission)
    {
        return $this->getAllPermissionsFromAssignments()
                    ->pluck('name')
                    ->contains($permission);
    }

}
