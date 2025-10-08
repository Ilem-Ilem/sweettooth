<?php

use App\Livewire\SuperAdmin\Roles\Index;
use App\Livewire\SuperAdmin\Roles\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('roles', Index::class)->name('roles.index');
    Route::get('branches', \App\Livewire\SuperAdmin\BranchModule\Index::class)->name('branches.index');
    Route::get('deleted-branch', App\Livewire\SuperAdmin\BranchModule\DeleteBranch::class)->name('branches.deleted');

    // Organization Structure routes
    Route::get('department-categories', \App\Livewire\SuperAdmin\DepartmentCategories\Index::class)->name('department-categories.index');
    Route::get('departments', \App\Livewire\SuperAdmin\Departments\Index::class)->name('departments.index');
    Route::get('positions', \App\Livewire\SuperAdmin\Positions\Index::class)->name('positions.index');

    Route::get('employees', App\Livewire\SuperAdmin\EmployeeModule\Index::class)->name('employee.index');
    Route::get('create-employee', App\Livewire\SuperAdmin\EmployeeModule\CreateEmployee::class)->name('employee.create');
    Route::get('employees/{id}/edit', App\Livewire\SuperAdmin\EmployeeModule\EditEmployee::class)->name('employee.edit');
    Route::get('assignments', \App\Livewire\SuperAdmin\Assignments\Index::class)->name('assignments.index');
});
