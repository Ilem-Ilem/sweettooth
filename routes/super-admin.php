<?php

use App\Livewire\SuperAdmin\Roles\Index;
use App\Livewire\SuperAdmin\Roles\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('roles', Index::class)->name('roles.index');
    Route::get('branches', \App\Livewire\SuperAdmin\BranchModule\Index::class)->name('branches.index');
    Route::get('deleted-branch', App\Livewire\SuperAdmin\BranchModule\DeleteBranch::class)->name('branches.deleted');
    Route::get('departments', \App\Livewire\SuperAdmin\Departments\Index::class)->name('departments.index');

    Route::get('employees', App\Livewire\SuperAdmin\EmployeeModule\Index::class)->name('employee.index');
});
