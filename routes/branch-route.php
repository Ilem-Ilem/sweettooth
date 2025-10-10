<?php

use App\Livewire\SuperAdmin\Roles\Index;
use App\Livewire\SuperAdmin\Roles\Permissions;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:employees', 'branch'])->prefix('branch-dashboard')->name('branch-dashboard.')->group(function () {
    Route::get('/', App\Livewire\BranchDashboard\Index::class)->name('index');
    Route::get('/employees', App\Livewire\BranchDashboard\EmployeeModule\Index::class)->name('employees');


    Route::get('/branch-dashboard/departments',  App\Livewire\BranchDashboard\DepartmentModule\Index::class)
        ->name('branch.departments.index');


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
});
