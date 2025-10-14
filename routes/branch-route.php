<?php

use App\Livewire\SuperAdmin\Roles\Index;
use App\Livewire\SuperAdmin\Roles\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:employees', 'branch'])->prefix('branch-dashboard')->name('branch-dashboard.')->group(function () {
    Route::get('/', App\Livewire\BranchDashboard\Index::class)->name('index');

    Route::get('/employees', App\Livewire\BranchDashboard\EmployeeModule\Index::class)->name('employees.index');
    Route::get('employee/create', App\Livewire\BranchDashboard\EmployeeModule\Create::class)->name('employee.create');
    Route::get('/employee//{employee_number}/{id}/', \App\Livewire\BranchDashboard\EmployeeModule\Details::class)->name('employee.details');
    Route::get('/employee/{id}/edit', \App\Livewire\BranchDashboard\EmployeeModule\Edit::class)->name('employee.edit');

    Route::get('departments',  App\Livewire\BranchDashboard\DepartmentModule\Index::class)->name('branch.departments.index');
    Route::get('departments/category', \App\Livewire\BranchDashboard\DepartmentModule\Category::class)->name('branch.departments.category');
    // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('items', \App\Livewire\BranchDashboard\Inventory\Items::class)->name('items');
        Route::get('purchases', \App\Livewire\BranchDashboard\Inventory\Purchases::class)->name('purchases');
        Route::get('stocks', \App\Livewire\BranchDashboard\Inventory\Stocks::class)->name('stocks');
        Route::get('stock-movements', \App\Livewire\BranchDashboard\Inventory\StockMovements::class)->name('stock-movements');
        Route::get('item-requests', \App\Livewire\BranchDashboard\Inventory\ItemRequests::class)->name('item-requests');
        Route::get('item-dispatches', \App\Livewire\BranchDashboard\Inventory\ItemDispatches::class)->name('item-dispatches');
        Route::get('stock-takes', \App\Livewire\BranchDashboard\Inventory\StockTakes::class)->name('stock-takes');
        Route::get('health-checks', \App\Livewire\BranchDashboard\Inventory\HealthChecks::class)->name('health-checks');
    });

    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('stock-analytics', \App\Livewire\BranchDashboard\Analytics\StockAnalytics::class)->name('stock-analytics');
        Route::get('employee-usage', \App\Livewire\BranchDashboard\Analytics\EmployeeUsageAnalytics::class)->name('employee-usage');
    });
});
