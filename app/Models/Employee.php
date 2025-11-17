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
        'id', 'branch_id', 'department_id', 'manager_id', 'employee_number', 'name', 'email', 'phone', 'address',
        'date_of_birth', 'gender', 'nationality', 'emergency_contact_name', 'emergency_contact_phone',
        'hire_date', 'termination_date', 'status', 'probation_end_date', 'shift_preference',
        'salary', 'hourly_rate', 'tax_id', 'bank_account', 'allergies', 'profile_photo',
        'last_performance_review_date', 'performance_rating', 'password'
    ];
    
    public function getMorphClass()
{
    return 'employee';
}


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

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    // Leave Management Relationships
    public function leaveBalances()
    {
        return $this->hasMany(EmployeeLeaveBalance::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function approvedLeaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'approved_by');
    }

    public function leaveAllocations()
    {
        return $this->hasMany(EmployeeLeaveAllocation::class);
    }

    public function stepouts()
    {
        return $this->hasMany(EmployeeStepout::class);
    }

}
