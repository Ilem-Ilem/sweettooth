# Employee Module - Implementation Quick Start

## Overview

This guide provides step-by-step instructions to implement the audit system in the Employee Module (Create, Edit, Index).

## Files to Modify

1. `app/Livewire/BranchDashboard/EmployeeModule/Create.php`
2. `app/Livewire/BranchDashboard/EmployeeModule/Edit.php` ✅ Partially done
3. `app/Livewire/BranchDashboard/EmployeeModule/Index.php`
4. View files (create.blade.php, edit.blade.php, index.blade.php)

## Step-by-Step Implementation

### STEP 1: Update Create.php

#### 1.1 Add Imports

```php
<?php

namespace App\Livewire\BranchDashboard\EmployeeModule;

use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Department;
use App\Models\ApprovalAuditRequest;  // ADD THIS
use App\Services\AuditService;        // ADD THIS
use App\Traits\AuditableSyncTrait;    // ADD THIS
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\{Layout, Url, On};
```

#### 1.2 Add Trait to Class Declaration

```php
#[Layout('components.layouts.app.branch-dashboard')]
class Create extends BaseComponent
{
    use WithFileUploads, AuditableSyncTrait;  // ADD AuditableSyncTrait
```

#### 1.3 Add Properties for Workflow

```php
    // ... existing properties ...
    
    // ADD THESE:
    // Reason modal state for employees
    public bool $showCreationReasonModal = false;
    public string $creationReason = '';
    public bool $creatingEmployee = false;
```

#### 1.4 Add initiateSave() Method

Add this method before `saveEmployee()`:

```php
    /**
     * Initiate employee creation workflow
     * 
     * For super admins: Proceeds directly to save
     * For employees: Shows reason modal first
     */
    public function initiateSave()
    {
        // Validate form first
        try {
            $this->validate([
                'department_id' => 'required|exists:departments,id',
                'employee_number' => 'required|string|unique:employees,employee_number',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
                'nationality' => 'nullable|string|max:100',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:50',
                'hire_date' => 'required|date',
                'termination_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,terminated,on_probation,on_leave',
                'probation_end_date' => 'nullable|date',
                'shift_preference' => 'nullable|in:morning,afternoon,night,rotating,flexible',
                'salary' => 'nullable|numeric|min:0',
                'hourly_rate' => 'nullable|numeric|min:0',
                'tax_id' => 'nullable|string|max:50',
                'bank_account' => 'nullable|string|max:100',
                'allergies' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
                'last_performance_review_date' => 'nullable|date',
                'performance_rating' => 'nullable|numeric|min:0|max:5',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return;
        }

        if (is_super_admin()) {
            // Super admin: proceed directly
            $this->saveEmployee();
        } else {
            // Employee: show reason modal
            $this->showCreationReasonModal = true;
        }
    }

    /**
     * Close the reason modal
     */
    public function closeCreationReasonModal()
    {
        $this->showCreationReasonModal = false;
        $this->creationReason = '';
    }

    /**
     * Proceed with creation after providing reason
     */
    public function proceedWithCreationReason()
    {
        if (strlen($this->creationReason) < 5) {
            $this->toast()->error('Reason must be at least 5 characters long')->send();
            return;
        }
        
        $this->showCreationReasonModal = false;
        $this->saveEmployee();
    }
```

#### 1.5 Replace saveEmployee() Method

Replace the entire `saveEmployee()` method with:

```php
    public function saveEmployee()
    {
        if ($this->creatingEmployee) return; // Prevent double-click
        
        $this->creatingEmployee = true;

        try {
            // Validate form input
            $this->validate([
                'department_id' => 'required|exists:departments,id',
                'employee_number' => 'required|string|unique:employees,employee_number',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
                'nationality' => 'nullable|string|max:100',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:50',
                'hire_date' => 'required|date',
                'termination_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,terminated,on_probation,on_leave',
                'probation_end_date' => 'nullable|date',
                'shift_preference' => 'nullable|in:morning,afternoon,night,rotating,flexible',
                'salary' => 'nullable|numeric|min:0',
                'hourly_rate' => 'nullable|numeric|min:0',
                'tax_id' => 'nullable|string|max:50',
                'bank_account' => 'nullable|string|max:100',
                'allergies' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
                'last_performance_review_date' => 'nullable|date',
                'performance_rating' => 'nullable|numeric|min:0|max:5',
                // Reason required for employees
                'creationReason' => is_super_admin() ? 'nullable|string' : 'required|string|min:5',
            ]);

            $data = [
                'branch_id' => $this->b_id,
                'department_id' => $this->department_id,
                'employee_number' => $this->employee_number,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'nationality' => $this->nationality,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'hire_date' => $this->hire_date,
                'termination_date' => $this->termination_date,
                'status' => $this->status,
                'probation_end_date' => $this->probation_end_date,
                'shift_preference' => $this->shift_preference,
                'salary' => $this->salary,
                'hourly_rate' => $this->hourly_rate,
                'tax_id' => $this->tax_id,
                'bank_account' => $this->bank_account,
                'allergies' => $this->allergies,
                'last_performance_review_date' => $this->last_performance_review_date,
                'performance_rating' => $this->performance_rating,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ];

            if ($this->profile_photo) {
                $data['profile_photo'] = $this->profile_photo->store('employee-photos', 'public');
            }

            $user = current_actor();

            if (!is_super_admin()) {
                // EMPLOYEE: Create approval request (don't create employee yet)
                ApprovalAuditRequest::create([
                    'branch_id' => $this->b_id,
                    'requester_id' => $user->id,
                    'requester_type' => get_class($user),
                    'action' => 'create:' . Employee::class,
                    'description' => $this->creationReason,
                    'payload' => array_merge($data, ['selectedRoles' => $this->selectedRoles]),
                    'status' => 'pending',
                ]);

                // Log as pending
                AuditService::log(
                    $user,
                    'create',
                    null,
                    $this->creationReason,
                    'pending'
                );

                $this->toast()->success('Employee creation request submitted for approval!')->send();
                $this->redirectRoute('branch-dashboard.employee.index', ['b_id' => $this->b_id]);
                return;
            }

            // SUPER ADMIN: Create immediately
            $employee = Employee::create($data);

            // Sync roles with audit
            if (!empty($this->selectedRoles)) {
                $this->syncWithAudit(
                    $employee,
                    'roles',
                    $this->selectedRoles,
                    "Created employee {$employee->name} with roles"
                );
            }

            // Log as completed
            AuditService::log(
                $user,
                'create',
                $employee,
                'Employee created by super admin',
                'completed'
            );

            $this->toast()->success('Employee created successfully!')->send();
            $this->redirectRoute('branch-dashboard.employee.index', ['b_id' => $this->b_id]);

        } finally {
            $this->creatingEmployee = false;
        }
    }
```

#### 1.6 Update Button in View

In `resources/views/livewire/branch-dashboard/employee-module/create.blade.php`, change the save button:

```blade
<!-- OLD -->
<button type="button" wire:click="saveEmployee"
    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">

<!-- NEW -->
<button type="button" wire:click="initiateSave"
    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
```

#### 1.7 Add Reason Modal to View

Add this before the form in `create.blade.php`:

```blade
<!-- Reason Modal for Non-Super Admins -->
@if(!is_super_admin())
<div class="fixed inset-0 z-40 bg-black bg-opacity-50 transition-opacity {{ $showCreationReasonModal ? 'opacity-100 visible' : 'opacity-0 invisible' }}"
    wire:click="closeCreationReasonModal"></div>
<div class="fixed inset-0 z-50 flex items-center justify-center {{ $showCreationReasonModal ? 'pointer-events-auto' : 'pointer-events-none' }}">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all {{ $showCreationReasonModal ? 'scale-100 opacity-100' : 'scale-95 opacity-0' }}"
        @click.stop>
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <h3 class="text-lg font-bold">Create Employee - Reason Required</h3>
            <button type="button" wire:click="closeCreationReasonModal"
                class="text-zinc-500 hover:text-zinc-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="px-6 py-4" @click.stop>
            <p class="text-sm text-zinc-600 mb-4">
                Please provide a reason for creating this employee. This will be reviewed by an administrator.
            </p>
            <textarea wire:model.live="creationReason" rows="4"
                class="w-full px-4 py-2 border border-zinc-300 rounded-lg bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500"
                placeholder="Explain your reason (minimum 5 characters)..."></textarea>
            @error('creationReason')
                <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
            @enderror
            <p class="text-xs text-zinc-500 mt-2">
                {{ strlen($creationReason) }}/5 minimum characters required
            </p>
        </div>
        
        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t border-zinc-200 flex gap-3 justify-end">
            <button type="button" wire:click="closeCreationReasonModal"
                class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-lg">
                Cancel
            </button>
            <button type="button" wire:click="proceedWithCreationReason" 
                {{ strlen($creationReason) < 5 ? 'disabled' : '' }}
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 rounded-lg">
                <span wire:loading.remove>Submit Request</span>
                <span wire:loading>Submitting...</span>
            </button>
        </div>
    </div>
</div>
@endif
```

### STEP 2: Update Edit.php

#### 2.1 Add Properties

```php
    // Add to existing properties:
    public bool $showUpdateReasonModal = false;
    public string $updateReason = '';
    public bool $updatingEmployee = false;
```

#### 2.2 Add Methods

Add these methods before `saveEmployee()`:

```php
    public function initiateSave()
    {
        try {
            $this->validate([
                'department_id' => 'required|exists:departments,id',
                'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employeeId,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $this->employeeId,
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
                'nationality' => 'nullable|string|max:100',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:50',
                'hire_date' => 'required|date',
                'termination_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,terminated,on_probation,on_leave',
                'probation_end_date' => 'nullable|date',
                'shift_preference' => 'nullable|in:morning,afternoon,night,rotating,flexible',
                'salary' => 'nullable|numeric|min:0',
                'hourly_rate' => 'nullable|numeric|min:0',
                'tax_id' => 'nullable|string|max:50',
                'bank_account' => 'nullable|string|max:100',
                'allergies' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
                'last_performance_review_date' => 'nullable|date',
                'performance_rating' => 'nullable|numeric|min:0|max:5',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return;
        }

        if (is_super_admin()) {
            $this->saveEmployee();
        } else {
            $this->showUpdateReasonModal = true;
        }
    }

    public function closeUpdateReasonModal()
    {
        $this->showUpdateReasonModal = false;
        $this->updateReason = '';
    }

    public function proceedWithUpdateReason()
    {
        if (strlen($this->updateReason) < 5) {
            $this->toast()->error('Reason must be at least 5 characters long')->send();
            return;
        }
        
        $this->showUpdateReasonModal = false;
        $this->saveEmployee();
    }
```

#### 2.3 Update saveEmployee() Method

Replace `saveEmployee()` with:

```php
    public function saveEmployee()
    {
        if ($this->updatingEmployee) return;
        
        $this->updatingEmployee = true;

        try {
            $this->validate([
                'department_id' => 'required|exists:departments,id',
                'employee_number' => 'required|string|unique:employees,employee_number,' . $this->employeeId,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:employees,email,' . $this->employeeId,
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
                'nationality' => 'nullable|string|max:100',
                'emergency_contact_name' => 'nullable|string|max:255',
                'emergency_contact_phone' => 'nullable|string|max:50',
                'hire_date' => 'required|date',
                'termination_date' => 'nullable|date',
                'status' => 'required|in:active,inactive,terminated,on_probation,on_leave',
                'probation_end_date' => 'nullable|date',
                'shift_preference' => 'nullable|in:morning,afternoon,night,rotating,flexible',
                'salary' => 'nullable|numeric|min:0',
                'hourly_rate' => 'nullable|numeric|min:0',
                'tax_id' => 'nullable|string|max:50',
                'bank_account' => 'nullable|string|max:100',
                'allergies' => 'nullable|string',
                'profile_photo' => 'nullable|image|max:2048',
                'last_performance_review_date' => 'nullable|date',
                'performance_rating' => 'nullable|numeric|min:0|max:5',
                'updateReason' => is_super_admin() ? 'nullable|string' : 'required|string|min:5',
            ]);

            $employee = Employee::findOrFail($this->employeeId);

            $data = [
                'branch_id' => $this->b_id,
                'department_id' => $this->department_id,
                'employee_number' => $this->employee_number,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'nationality' => $this->nationality,
                'emergency_contact_name' => $this->emergency_contact_name,
                'emergency_contact_phone' => $this->emergency_contact_phone,
                'hire_date' => $this->hire_date,
                'termination_date' => $this->termination_date,
                'status' => $this->status,
                'probation_end_date' => $this->probation_end_date,
                'shift_preference' => $this->shift_preference,
                'salary' => $this->salary,
                'hourly_rate' => $this->hourly_rate,
                'tax_id' => $this->tax_id,
                'bank_account' => $this->bank_account,
                'allergies' => $this->allergies,
                'last_performance_review_date' => $this->last_performance_review_date,
                'performance_rating' => $this->performance_rating,
            ];

            if ($this->profile_photo) {
                $data['profile_photo'] = $this->profile_photo->store('employee-photos', 'public');
            }

            $user = current_actor();

            if (!is_super_admin()) {
                // EMPLOYEE: Create approval request
                ApprovalAuditRequest::create([
                    'branch_id' => $this->b_id,
                    'requester_id' => $user->id,
                    'requester_type' => get_class($user),
                    'action' => 'update:' . Employee::class . ':' . $this->employeeId,
                    'description' => $this->updateReason,
                    'payload' => array_merge($data, ['selectedRoles' => $this->selectedRoles]),
                    'status' => 'pending',
                ]);

                // Log as pending
                AuditService::log(
                    $user,
                    'update',
                    $employee,
                    $this->updateReason,
                    'pending'
                );

                $this->toast()->success('Employee update request submitted for approval!')->send();
                $this->redirectRoute('branch-dashboard.employees.index', ['b_id' => $this->b_id]);
                return;
            }

            // SUPER ADMIN: Update immediately
            $employee->update($data);

            // Sync roles with audit
            $this->syncWithAudit(
                $employee,
                'roles',
                $this->selectedRoles,
                "Updated employee {$employee->name} - roles changed"
            );

            // Log as completed
            AuditService::log(
                $user,
                'update',
                $employee,
                'Employee updated by super admin',
                'completed'
            );

            $this->toast()->success('Employee updated successfully!')->send();
            $this->redirectRoute('branch-dashboard.employees.index', ['b_id' => $this->b_id]);

        } finally {
            $this->updatingEmployee = false;
        }
    }
```

#### 2.4 Update Button and Add Modal in View

Do similar to Create.php changes.

### STEP 3: Update Index.php

**This is the most complex - see EmployeeModuleAudit.md for full implementation of:**
- Delete workflow with reason modal
- Bulk delete with approval
- Role assignment with audit
- All necessary methods

## Testing

After implementing:

1. Test super admin workflows (no modals)
2. Test employee workflows (modals appear)
3. Check audit logs in Audit Management
4. Verify ApprovalAuditRequest created
5. Test approval and execution

## See Also

- `EmployeeModuleAudit.md` - Complete detailed guide
- `DepartmentAudit.md` - Similar implementation pattern
