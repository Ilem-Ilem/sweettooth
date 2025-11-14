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

    //EMPLOYEE ROUTES
    Route::get('employees', App\Livewire\SuperAdmin\EmployeeModule\Index::class)->name('employee.index');
    Route::get('create-employee', App\Livewire\SuperAdmin\EmployeeModule\CreateEmployee::class)->name('employee.create');
    Route::get('employees/{id}/edit', App\Livewire\SuperAdmin\EmployeeModule\EditEmployee::class)->name('employee.edit');
    Route::get('role-assignments', \App\Livewire\SuperAdmin\EmployeeModule\RoleAssignment::class)->name('role-assignments.index');
    Route::get('employee/{employee_number}/{id}/', \App\Livewire\SuperAdmin\EmployeeModule\EmployeeDetails::class)->name('employee.detail');

    //LEAVE MANAGEMENT
    Route::get('leave-management', App\Livewire\SuperAdmin\EmployeeModule\LeaveManagement\Index::class)->name('leave.index');
    


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

    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('dashboard', \App\Livewire\SuperAdmin\Analytics\OverallSummaryDashboard::class)->name('dashboard');
        Route::get('stock-level', \App\Livewire\SuperAdmin\Analytics\StockLevelAnalytics::class)->name('stock-level');
        Route::get('stock-movement', \App\Livewire\SuperAdmin\Analytics\StockMovementAnalytics::class)->name('stock-movement');
        Route::get('purchase', \App\Livewire\SuperAdmin\Analytics\PurchaseAnalytics::class)->name('purchase');
        Route::get('request-dispatch', \App\Livewire\SuperAdmin\Analytics\RequestDispatchAnalytics::class)->name('request-dispatch');
        Route::get('stock-valuation', \App\Livewire\SuperAdmin\Analytics\StockValuation::class)->name('stock-valuation');
        Route::get('alerts', \App\Livewire\SuperAdmin\Analytics\AlertsDashboard::class)->name('alerts');
        Route::get('supplier-performance', \App\Livewire\SuperAdmin\Analytics\SupplierPerformance::class)->name('supplier-performance');
    });

    // Super Admin Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', \App\Livewire\SuperAdmin\Settings\Index::class)->name('index');
        Route::get('/business-configuration', \App\Livewire\SuperAdmin\Settings\BusinessConfiguration::class)->name('business-configuration');
        Route::get('/backup-management', \App\Livewire\SuperAdmin\Settings\BackupManagement::class)->name('backup-management');
    });

    // MD Reports Dashboard Routes
    Route::prefix('md-reports')->name('md-reports.')->group(function () {
        Route::get('dashboard', \App\Livewire\SuperAdmin\MDReports\Dashboard\Index::class)->name('dashboard');
        Route::get('view/{id}', \App\Livewire\SuperAdmin\MDReports\ViewReport\Index::class)->name('view');
    });
});
