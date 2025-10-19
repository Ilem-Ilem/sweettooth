<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:employees', 'branch'])->prefix('branch-dashboard')->name('branch-dashboard.')->group(function () {
    Route::get('/', App\Livewire\BranchDashboard\Index::class)->name('index');

    Route::get('/employees', App\Livewire\BranchDashboard\EmployeeModule\Index::class)->name('employees.index');
    Route::get('employee/create', App\Livewire\BranchDashboard\EmployeeModule\Create::class)->name('employee.create');
    Route::get('/employee//{employee_number}/{id}/', \App\Livewire\BranchDashboard\EmployeeModule\Details::class)->name('employee.details');
    Route::get('/employee/{id}/edit', \App\Livewire\BranchDashboard\EmployeeModule\Edit::class)->name('employee.edit');

    Route::get('departments', App\Livewire\BranchDashboard\DepartmentModule\Index::class)->name('branch.departments.index');
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

    // Production routes
    Route::prefix('production')->name('production.')->group(function () {
        Route::get('product-types', \App\Livewire\BranchDashboard\Production\ProductTypes::class)->name('product-types');
        Route::get('products', \App\Livewire\BranchDashboard\Production\Products::class)->name('products');

        // Production Requests routes
        Route::prefix('request')->name('request.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\Production\Request\Index::class)->name('index');
            Route::get('/create', \App\Livewire\BranchDashboard\Production\Request\Create::class)->name('create');
        });

        // Daily Produce routes
        Route::prefix('daily-produce')->name('daily-produce.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\Production\DailyProduce\Index::class)->name('index');
        });

        // Recipes routes
        Route::get('recipes', App\Livewire\BranchDashboard\Production\Recipes::class)->name('recipes.index');
        Route::get('recipes/add', App\Livewire\BranchDashboard\Production\Recipes\Add::class)->name('recipes.add');
        Route::get('recipes/{id}', App\Livewire\BranchDashboard\Production\RecipeDetail::class)->name('recipes.detail');

        Route::prefix('kitchen')->name('kitchen.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\Production\KitchenModule\Index::class)->name('index');
        });

        // Raw Material Tracking
        Route::get('raw-material-tracking', \App\Livewire\BranchDashboard\Production\RawMaterialTracking::class)->name('raw-material-tracking');
    });

    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('overview', \App\Livewire\BranchDashboard\Analytics\OverallSummaryDashboard::class)->name('overview');
        Route::get('stock-level', \App\Livewire\BranchDashboard\Analytics\StockLevelAnalytics::class)->name('stock-level');
        Route::get('stock-movement', \App\Livewire\BranchDashboard\Analytics\StockMovementAnalytics::class)->name('stock-movement');
        Route::get('purchase', \App\Livewire\BranchDashboard\Analytics\PurchaseAnalytics::class)->name('purchase');
        Route::get('request-dispatch', \App\Livewire\BranchDashboard\Analytics\RequestDispatchAnalytics::class)->name('request-dispatch');
        Route::get('stock-variance', \App\Livewire\BranchDashboard\Analytics\StockVarianceAnalytics::class)->name('stock-variance');
        Route::get('branch-performance', \App\Livewire\BranchDashboard\Analytics\BranchPerformance::class)->name('branch-performance');
        Route::get('supplier-performance', \App\Livewire\BranchDashboard\Analytics\SupplierPerformance::class)->name('supplier-performance');
        Route::get('alerts', \App\Livewire\BranchDashboard\Analytics\AlertsDashboard::class)->name('alerts');
        Route::get('stock-valuation', \App\Livewire\BranchDashboard\Analytics\StockValuation::class)->name('stock-valuation');
    });
});
