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

    // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('items', \App\Livewire\SuperAdmin\Inventory\Items::class)->name('items');
        Route::get('purchases', \App\Livewire\SuperAdmin\Inventory\Purchases::class)->name('purchases');
        Route::get('stocks', \App\Livewire\SuperAdmin\Inventory\Stocks::class)->name('stocks');
        Route::get('stock-movements', \App\Livewire\SuperAdmin\Inventory\StockMovements::class)->name('stock-movements');
        Route::get('item-requests', \App\Livewire\SuperAdmin\Inventory\ItemRequests::class)->name('item-requests');
        Route::get('item-dispatches', \App\Livewire\SuperAdmin\Inventory\ItemDispatches::class)->name('item-dispatches');
        Route::get('stock-takes', \App\Livewire\SuperAdmin\Inventory\StockTakes::class)->name('stock-takes');
        Route::get('health-checks', \App\Livewire\SuperAdmin\Inventory\HealthChecks::class)->name('health-checks');
    });
});
