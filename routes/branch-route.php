<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web,employees', 'setBranchContext', 'branch'])->prefix('branch-dashboard')->name('branch-dashboard.')->group(function () {
    Route::get('/', App\Livewire\BranchDashboard\Index::class)->name('index');

    Route::get('/employees', App\Livewire\BranchDashboard\EmployeeModule\Index::class)->name('employee.index');
    Route::get('employee/create', App\Livewire\BranchDashboard\EmployeeModule\Create::class)->name('employee.create');
    Route::get('/employee//{employee_number}/{id}/', \App\Livewire\BranchDashboard\EmployeeModule\Details::class)->name('employee.details');
    Route::get('/employee/{id}/edit', \App\Livewire\BranchDashboard\EmployeeModule\Edit::class)->name('employee.edit');
    // ROLE ASSIGNMENT
    Route::get('role-assignments', \App\Livewire\BranchDashboard\EmployeeModule\RolePermission\AssignRole::class)->name('role-assignments.index');
    Route::get('/role-permisssion', \App\Livewire\BranchDashboard\EmployeeModule\RolePermission\Index::class)->name('role-permission');

    // Clock-In Board Routes
    Route::prefix('clock-in-board')->name('clock-in-board.')->group(function () {
        Route::get('/', \App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\TodayIndex::class)->name('today');
        Route::get('all', \App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\GeneralClockInBoard::class)->name('all');
        Route::get('employee/{employee}/history', \App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\EmployeeHistory::class)->name('employee-history');
    });

    // Leave Management routes
    Route::prefix('leave')->name('leave.')->group(function () {
        Route::get('/types', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\LeaveTypes::class)->name('types');
        Route::get('/apply', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ApplyLeave::class)->name('apply');
        Route::get('/my-leaves', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\MyLeaves::class)->name('my-leaves');
        Route::get('/approve', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ApproveLeave::class)->name('approve');
        Route::get('/balance', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\LeaveBalance::class)->name('balance');
        Route::get('/manage-allocations', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ManageAllocations::class)->name('manage-allocations');
    });

    // DEPARTMENT / DEPARTMENT CATEGORY SECTION 
    Route::get('departments', App\Livewire\BranchDashboard\DepartmentModule\Index::class)->name('branch.departments.index');
    Route::get('department/create', \App\Livewire\BranchDashboard\DepartmentModule\Department\CreateOrUpdate::class)->name('department.create');
    Route::get('department/{id}/edit', \App\Livewire\BranchDashboard\DepartmentModule\Department\CreateOrUpdate::class)->name('department.edit');
    Route::get('departments/category', \App\Livewire\BranchDashboard\DepartmentModule\Category::class)->name('branch.departments.category');
    Route::get('/department/category/create', \App\Livewire\BranchDashboard\DepartmentModule\Cartegory\Create::class)->name('department.category.create');
    Route::get('department/category/{id}/edit', \App\Livewire\BranchDashboard\DepartmentModule\Cartegory\Edit::class)->name('department.category.edit');
    // Shift Selection functionality
  
    Route::get('auth/shift', \App\Livewire\Auth\Shift::class)->name('select_shift');
    // Inventory routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('items', \App\Livewire\BranchDashboard\Inventory\Items::class)->name('items');
        Route::get('purchases', \App\Livewire\BranchDashboard\Inventory\Purchases::class)->name('purchases');
        Route::get('stocks', \App\Livewire\BranchDashboard\Inventory\Stocks::class)->name('stocks');
        Route::get('item-requests', \App\Livewire\BranchDashboard\Inventory\ItemRequests::class)->name('item-requests');
        Route::get('item-dispatches', \App\Livewire\BranchDashboard\Inventory\ItemDispatches::class)->name('item-dispatches');
        Route::get('stock-takes', \App\Livewire\BranchDashboard\Inventory\StockTakes::class)->name('stock-takes');
        Route::get('health-checks', \App\Livewire\BranchDashboard\Inventory\HealthChecks::class)->name('health-checks');

        // Shift Closing - Inventory (not department-based)
        Route::get('shift-closing', \App\Livewire\BranchDashboard\Inventory\ShiftClosing\Index::class)->name('shift-closing');

        // Callbacks - Inventory reviewing production callbacks
        Route::prefix('callbacks')->name('callbacks.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\Inventory\Callbacks\ApproveCallbacks::class)->name('index');
        });

        // Inventory Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/stock-levels', \App\Livewire\BranchDashboard\Inventory\Reports\StockLevels\Index::class)->name('stock-levels');
            Route::get('/stock-movement', \App\Livewire\BranchDashboard\Inventory\Reports\StockMovement\Index::class)->name('stock-movement');
            Route::get('/turnover', \App\Livewire\BranchDashboard\Inventory\Reports\StockTurnover\Index::class)->name('turnover');
            Route::get('/reorder', \App\Livewire\BranchDashboard\Inventory\Reports\Reorder\Index::class)->name('reorder');
            Route::get('/variance', \App\Livewire\BranchDashboard\Inventory\Reports\Variance\Index::class)->name('variance');
        });
    });

    // Production routes - Modular System
    Route::prefix('production')->name('production.')->group(function () {

        // Helper function to register department routes
        $registerProductionDepartmentRoutes = function () {
            // Products Management
            Route::get('product-types/{deptSlug}', \App\Livewire\BranchDashboard\Production\ProductTypes::class)->name('product-types');
            Route::get('products/{deptSlug}', \App\Livewire\BranchDashboard\Production\Products::class)->name('products');

            // Request Management
            Route::prefix('request')->name('request.')->group(function () {
                Route::get('/{deptSlug}', \App\Livewire\BranchDashboard\Production\Request\Index::class)->name('index');
                Route::get('/{deptSlug}/create', \App\Livewire\BranchDashboard\Production\Request\Create::class)->name('create');
            });

            // Daily Produce
            Route::prefix('daily-produce')->name('daily-produce.')->group(function () {
                Route::get('/{deptSlug}', \App\Livewire\BranchDashboard\Production\DailyProduce\Index::class)->name('index');
            });

            // Shift Closing - Production (department-based)
            Route::prefix('shift-closing')->name('shift-closing.')->group(function () {
                Route::get('/{deptSlug}', \App\Livewire\BranchDashboard\Production\ShiftClosing\Index::class)->name('index');
            });

            // Recipes
            Route::get('recipes/{deptSlug}', App\Livewire\BranchDashboard\Production\Recipes::class)->name('recipes.index');
            Route::get('recipes/{deptSlug}/add', App\Livewire\BranchDashboard\Production\Recipes\Add::class)->name('recipes.add');
            Route::get('recipes/{deptSlug}/{id}/edit', App\Livewire\BranchDashboard\Production\Recipes\Edit::class)->name('recipes.edit');
            Route::get('recipes/{deptSlug}/{id}', App\Livewire\BranchDashboard\Production\RecipeDetail::class)->name('recipes.detail');

            // Module
            Route::prefix('module')->name('module.')->group(function () {
                Route::get('/', \App\Livewire\BranchDashboard\Production\KitchenModule\Index::class)->name('index');
                Route::get('/stock-monitor', \App\Livewire\BranchDashboard\Production\KitchenModule\StockMonitor::class)->name('stock-monitor');
            });

            // Raw Material Tracking
            Route::get('raw-material-tracking', \App\Livewire\BranchDashboard\Production\RawMaterialTracking::class)->name('raw-material-tracking');
        };

        $registerProductionDepartmentRoutes();

        // Callbacks - Production callbacks management
        Route::prefix('callbacks')->name('callbacks.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\Production\Callbacks\Index::class)->name('index');
            Route::get('/create-inventory', \App\Livewire\BranchDashboard\Production\Callbacks\CreateInventoryCallback::class)->name('create-inventory');
            Route::get('/approve-sales-callbacks', \App\Livewire\BranchDashboard\Production\Callbacks\ApproveCallbacks::class)->name('approve-sales-callbacks');
        });

        // Production Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/efficiency', \App\Livewire\BranchDashboard\Production\Reports\ProductionEfficiency\Index::class)->name('efficiency');
            Route::get('/quality', \App\Livewire\BranchDashboard\Production\Reports\QualityMetrics\Index::class)->name('quality');
            Route::get('/waste', \App\Livewire\BranchDashboard\Production\Reports\WasteAnalysis\Index::class)->name('waste');
            Route::get('/cost', \App\Livewire\BranchDashboard\Production\Reports\CostAnalysis\Index::class)->name('cost');
            Route::get('/recipe-performance', \App\Livewire\BranchDashboard\Production\Reports\RecipePerformance\Index::class)->name('recipe-performance');
            Route::get('/shift-summary', \App\Livewire\BranchDashboard\Production\Reports\ShiftSummary\Index::class)->name('shift-summary');
            Route::get('/ingredient-utilization', \App\Livewire\BranchDashboard\Production\Reports\IngredientUtilization\Index::class)->name('ingredient-utilization');
            Route::get('/pipeline', \App\Livewire\BranchDashboard\Production\Reports\PipelineStatus\Index::class)->name('pipeline');
            Route::get('/capacity', \App\Livewire\BranchDashboard\Production\Reports\CapacityPlanning\Index::class)->name('capacity');
        });
    });

    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('overview', \App\Livewire\BranchDashboard\Analytics\OverallSummaryDashboard::class)->name('overview');
        Route::get('stock-level', \App\Livewire\BranchDashboard\Analytics\StockLevelAnalytics::class)->name('stock-level');
        Route::get('stock-movement', \App\Livewire\BranchDashboard\Analytics\StockMovementAnalytics::class)->name('stock-movement');
        Route::get('purchase', \App\Livewire\BranchDashboard\Analytics\PurchaseAnalytics::class)->name('purchase');
        Route::get('request-dispatch', \App\Livewire\BranchDashboard\Analytics\RequestDispatchAnalytics::class)->name('request-dispatch');
        Route::get('alerts', \App\Livewire\BranchDashboard\Analytics\AlertsDashboard::class)->name('alerts');
        Route::get('stock-valuation', \App\Livewire\BranchDashboard\Analytics\StockValuation::class)->name('stock-valuation');
    });

    // Reporting Department Routes
    Route::prefix('reporting')->name('reporting.')->group(function () {
        Route::get('dashboard', \App\Livewire\BranchDashboard\ReportingDepartment\Dashboard\Index::class)->name('dashboard');
        Route::get('review', \App\Livewire\BranchDashboard\ReportingDepartment\ReviewReports\Index::class)->name('review');
        Route::get('compile', \App\Livewire\BranchDashboard\ReportingDepartment\CompileReports\Index::class)->name('compile');
        Route::get('compiled/{id}', \App\Livewire\BranchDashboard\ReportingDepartment\ViewCompiled\Index::class)->name('compiled.view');
        Route::get('send-to-md', \App\Livewire\BranchDashboard\ReportingDepartment\SendToMD\Index::class)->name('send-to-md');
    });

    // Audit Management Routes
    Route::prefix('audit')->name('audit.')->group(function () {
        Route::get('/', \App\Livewire\BranchDashboard\AuditManagement\Index::class)->name('index');
        Route::get('inventory-approvals', \App\Livewire\BranchDashboard\AuditManagement\InventoryApprovals::class)->name('inventory-approvals');
    });

    // Sales Dashboard routes - Modular System
    Route::prefix('sales-dashboard')->name('sales-dashboard.')->group(function () {

        // Helper function to register sales department routes
        $registerSalesDepartmentRoutes = function () {
            // POS Routes
            Route::prefix('pos')->name('pos.')->group(function () {
                Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\Pos\Index::class)->name('index');
            });

            Route::prefix('analytics')->name('analytics.')->group(function () {
                Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\Analytics\Index::class)->name('index');
            });

            // My Sales - Personal Sales Dashboard
            Route::prefix('my-sales')->name('my-sales.')->group(function () {
                Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\MySales\Index::class)->name('index');
            });

            // Shift Closing - Sales (department-based)
            Route::prefix('shift-closing')->name('shift-closing.')->group(function () {
                Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\ShiftClosing\Index::class)->name('index');
            });
        };

        // Expiry Alerts - shown after clock-in
        Route::get('/expiry-alerts', \App\Livewire\BranchDashboard\SalesDashboard\ExpiryAlerts::class)->name('expiry-alerts');

        Route::prefix('stock-opening')->name('stock-opening.')->group(function () {
            Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\StockOpening\Index::class)->name('index');
        });

        Route::prefix('dispatches')->name('dispatches.')->group(function () {
            Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\Dispatches\Index::class)->name('index');
        });

        Route::prefix('callbacks')->name('callbacks.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\SalesDashboard\Callbacks\Index::class)->name('index');
            Route::get('/dispatch-callbacks', \App\Livewire\BranchDashboard\SalesDashboard\Callbacks\CreateDispatchCallback::class)->name('dispatch-callbacks');
        });

        Route::get('/stock-monitor', \App\Livewire\BranchDashboard\SalesDashboard\StockMonitor::class)->name('stock-monitor');

        // Sales Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            // Route::get('/performance', \App\Livewire\BranchDashboard\SalesDashboard\Reports\SalesPerformance\Index::class)->name('performance');
            // Route::get('/employee', \App\Livewire\BranchDashboard\SalesDashboard\Reports\SalesEmployee\Index::class)->name('employee');
            // Route::get('/customer', \App\Livewire\BranchDashboard\SalesDashboard\Reports\CustomerAnalysis\Index::class)->name('customer');
            // Route::get('/payment', \App\Livewire\BranchDashboard\SalesDashboard\Reports\PaymentMethod\Index::class)->name('payment');
        });

        // Execute dynamic sales department routes
        $registerSalesDepartmentRoutes();
    });
});
