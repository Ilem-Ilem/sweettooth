<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'setBranchContext', 'branch', 'redirect-super-admin'])->prefix('branch-dashboard')->name('branch-dashboard.')->group(function () {
    // Dashboard Router - Redirects to appropriate dashboard based on role
    Route::get('/dashboard/router', App\Livewire\BranchDashboard\Dashboards\Router::class)->name('dashboards.router');

    // New Role-Based Dashboards (Phase 3) - Protected by role.level middleware
    Route::get('/dashboards/super-admin', App\Livewire\BranchDashboard\Dashboards\SuperAdminDashboard::class)
        ->middleware('role.level:5')
        ->name('dashboards.super-admin');
    Route::get('/dashboards/admin', App\Livewire\BranchDashboard\Dashboards\AdminDashboard::class)
        ->middleware('role.level:4')
        ->name('dashboards.admin');
    Route::get('/dashboards/manager', App\Livewire\BranchDashboard\Dashboards\ManagerDashboard::class)
        ->middleware('role.level:3')
        ->name('dashboards.manager');
    Route::get('/dashboards/supervisor', App\Livewire\BranchDashboard\Dashboards\SupervisorDashboard::class)
        ->middleware('role.level:2')
        ->name('dashboards.supervisor');

    // Legacy Role-Specific Dashboards (kept for backward compatibility)
    Route::middleware('role_or_permission:view_inventory_dashboard')->get('/dashboard/inventory', App\Livewire\Dashboards\InventoryDashboard::class)->name('dashboard.inventory');
    Route::middleware('role_or_permission:view_production_dashboard')->get('/dashboard/production/{deptSlug?}', App\Livewire\Dashboards\ProductionDashboard::class)->name('dashboard.production');
    Route::middleware('role_or_permission:view-sales-dashboard')->get('/dashboard/sales/{salesDeptSlug?}', App\Livewire\Dashboards\SalesDashboard::class)->name('dashboard.sales');
    Route::middleware('role_or_permission:view-sales-dashboard')->get('/dashboard/corner-store', App\Livewire\Dashboards\CornerStoreDashboard::class)->name('dashboard.corner-store');
    Route::middleware('role_or_permission:manage_organization')->get('/dashboard/hr', \App\Livewire\Dashboards\HRDashboard::class)->name('dashboard.hr');
    Route::middleware('role_or_permission:manage_branches')->get('/dashboard/admin', \App\Livewire\Dashboards\BranchAdminDashboard::class)->name('dashboard.admin');
    Route::middleware('role_or_permission:manage_system')->get('/dashboard/super-admin', \App\Livewire\Dashboards\SuperAdminDashboard::class)->name('dashboard.super-admin');

    // Root dashboard path - redirect to router for role-based redirect
    Route::get('/', function () {
        $branchId = request()->query('b_id') ?? current_branch_id();
        if ($branchId) {
            return redirect()->route('branch-dashboard.dashboards.router', ['b_id' => $branchId]);
        }

        return redirect()->route('branch-dashboard.dashboards.router');
    })->name('index');

    // ====== ORGANIZATION SECTION (HR Manager, HR Officer, Admin) ======
    Route::middleware('role_or_permission:manage_organization')->group(function () {
        // Employee Management
        Route::get('/employees', App\Livewire\BranchDashboard\EmployeeModule\Index::class)->name('employee.index');
        Route::get('employee/create', App\Livewire\BranchDashboard\EmployeeModule\Create::class)->name('employee.create');
        Route::get('employee/{id}/edit', \App\Livewire\BranchDashboard\EmployeeModule\Edit::class)->name('employee.edit');
        Route::get('/employee/{employee_number?}/{id}/', \App\Livewire\BranchDashboard\EmployeeModule\Details::class)->name('employee.details');

        // Employee Appraisals
        Route::get('/employee-appraisals', App\Livewire\BranchDashboard\EmployeeAppraisals::class)->name('employee-appraisals');
        Route::get('/employee-appraisal-history/{employee}', App\Livewire\BranchDashboard\EmployeeAppraisalHistory::class)->name('employee-appraisal-history');
        Route::get('/appraise-employee/{employee}', App\Livewire\BranchDashboard\AppraiseEmployee::class)->name('appraise-employee');

        // Clock-In Board Routes
        Route::prefix('clock-in-board')->name('clock-in-board.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\TodayIndex::class)->name('today');
            Route::get('all', \App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\GeneralClockInBoard::class)->name('all');
            Route::get('employee/{employee}/history', \App\Livewire\BranchDashboard\EmployeeModule\ClockInModule\EmployeeHistory::class)->name('employee-history');
        });

        // Employee Appraisals
        Route::get('/employee-appraisals', App\Livewire\BranchDashboard\EmployeeAppraisals::class)->name('employee-appraisals');
        Route::get('/appraise-employee/{employee}', App\Livewire\BranchDashboard\AppraiseEmployee::class)->name('appraise-employee');

        // HR Appraisal Management
        Route::prefix('hr')->name('hr.')->group(function () {
            Route::prefix('appraisals')->name('appraisals.')->group(function () {
                Route::get('/cycles', App\Livewire\BranchDashboard\HR\AppraisalCycles::class)->name('cycles');
                // Route::get('/analytics', App\Livewire\BranchDashboard\HR\AppraisalAnalytics::class)->name('analytics');
            });
            // Route::get('/performance-goals', App\Livewire\BranchDashboard\PerformanceGoals::class)->name('performance-goals');
            // Route::get('/feedback-requests', App\Livewire\BranchDashboard\FeedbackRequests::class)->name('feedback-requests');
        });

        // Leave Management routes (restricted to branch users)
        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('/apply', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ApplyLeave::class)->name('apply');
            Route::get('/types', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\LeaveTypes::class)->name('types');
            Route::get('/approve', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ApproveLeave::class)->name('approve');
            Route::get('/manage-allocations', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\ManageAllocations::class)->name('manage-allocations');
            Route::get('/my-leaves', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\MyLeaves::class)->name('my-leaves');
            Route::get('/balance', \App\Livewire\BranchDashboard\EmployeeModule\LeaveManagement\LeaveBalance::class)->name('balance');
        });

        // DEPARTMENT / DEPARTMENT CATEGORY SECTION
        Route::get('departments', App\Livewire\BranchDashboard\DepartmentModule\Index::class)->name('branch.departments.index');
        Route::get('department/create', \App\Livewire\BranchDashboard\DepartmentModule\Department\CreateOrUpdate::class)->name('department.create');
        Route::get('department/{id}/edit', \App\Livewire\BranchDashboard\DepartmentModule\Department\CreateOrUpdate::class)->name('department.edit');
        Route::get('departments/category', \App\Livewire\BranchDashboard\DepartmentModule\Category::class)->name('branch.departments.category');
        Route::get('/department/category/create', \App\Livewire\BranchDashboard\DepartmentModule\Cartegory\Create::class)->name('department.category.create');
        Route::get('department/category/{id}/edit', \App\Livewire\BranchDashboard\DepartmentModule\Cartegory\Edit::class)->name('department.category.edit');

        // ROLE ASSIGNMENT (HR can manage roles within their branch)
        Route::get('role-assignments', \App\Livewire\BranchDashboard\EmployeeModule\RolePermission\AssignRole::class)->name('role-assignments.index');
        Route::get('/role-permisssion', \App\Livewire\BranchDashboard\EmployeeModule\RolePermission\Index::class)->name('role-permission');
    });

    // ROLE MANAGEMENT (Super Admin Only - Level 5)
    Route::middleware(['role_or_permission:manage_roles', 'role.level:5'])
        ->get('roles', \App\Livewire\BranchDashboard\Roles\Index::class)
        ->name('roles.index');

    // BRANCH MANAGEMENT (Super Admin Only - Level 5)
    Route::middleware(['role_or_permission:manage_branches', 'role.level:5'])->group(function () {
        Route::get('branches', \App\Livewire\BranchDashboard\Branches\Index::class)->name('branches.index');
        Route::get('deleted-branches', \App\Livewire\BranchDashboard\Branches\DeleteBranch::class)->name('branches.deleted');
    });

    // SETTINGS (Super Admin Only - Level 5)
    Route::middleware(['role_or_permission:manage_settings', 'role.level:5'])
        ->get('settings', \App\Livewire\BranchDashboard\Settings\Index::class)
        ->name('settings.index');

    // MD REPORTS (Super Admin Only)
    Route::middleware('role_or_permission:view_reports')->prefix('md-reports')->name('md-reports.')->group(function () {
        Route::get('dashboard', \App\Livewire\BranchDashboard\MDReports\Dashboard\Index::class)->name('dashboard');
        Route::get('view/{id}', \App\Livewire\BranchDashboard\MDReports\ViewReport\Index::class)->name('view');
    });

    // Organization Helper
    Route::get('organization/helper', \App\Livewire\BranchDashboard\Organization\Helper::class)->name('organization.helper');

    // Inventory Helper
    Route::get('inventory/helper', \App\Livewire\BranchDashboard\Inventory\Helper::class)->name('inventory.helper');

    // Sales Helper
    Route::get('sales-dashboard/helper', \App\Livewire\BranchDashboard\SalesDashboard\Helper::class)->name('sales-dashboard.helper');

    // Production Helper
    Route::get('production/helper', \App\Livewire\BranchDashboard\Production\Helper::class)->name('production.helper');

    // Shift Selection functionality
    Route::get('auth/shift', \App\Livewire\Auth\Shift::class)->name('select_shift');

    // SHIFT MANAGEMENT (Admin and Super Admin Only)
    Route::middleware(['role_or_permission:manage_shifts'])->prefix('shift-management')->name('shift-management.')->group(function () {
        Route::get('/', \App\Livewire\BranchDashboard\ShiftManagement\Index::class)->name('index');
        Route::get('/configuration', \App\Livewire\BranchDashboard\ShiftManagement\ShiftConfiguration::class)->name('configuration');
        Route::get('/assignment', \App\Livewire\BranchDashboard\ShiftManagement\ShiftAssignment::class)->name('assignment');
        Route::get('/overrides', \App\Livewire\BranchDashboard\ShiftManagement\ShiftOverrides::class)->name('overrides');
    });

    // Routes requiring active shift (all work functions)
    Route::middleware(['require_active_shift'])->group(function () {
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

        // Supplier Management routes
        Route::middleware('role_or_permission:view-suppliers|manage-suppliers')->prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/', \App\Livewire\BranchDashboard\Supplier\SupplierIndex::class)->name('index');
            Route::middleware('role_or_permission:create-suppliers|manage-suppliers')->get('/create', \App\Livewire\BranchDashboard\Supplier\CreateSupplier::class)->name('create');
            Route::get('/{supplier}', \App\Livewire\BranchDashboard\Supplier\SupplierDetails::class)->name('show');
            Route::get('/{supplier}/performance', \App\Livewire\BranchDashboard\Supplier\SupplierPerformance::class)->name('performance');
        });

        // Production routes - Modular System
        // Protected by department.scope middleware for department-based access control
        Route::prefix('production')->name('production.')->middleware(['department.scope'])->group(function () {

            // Helper function to register department routes
            $registerProductionDepartmentRoutes = function () {
                // Products Management
                Route::get('product-types/{deptSlug?}', \App\Livewire\BranchDashboard\Production\ProductTypes::class)->name('product-types');
                Route::get('products/{deptSlug?}', \App\Livewire\BranchDashboard\Production\Products::class)->name('products');

                // Request Management
                Route::prefix('request')->name('request.')->group(function () {
                    Route::get('/{deptSlug?}', \App\Livewire\BranchDashboard\Production\Request\Index::class)->name('index');
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
                Route::get('recipes/{deptSlug?}', App\Livewire\BranchDashboard\Production\Recipes::class)->name('recipes.index');
                Route::get('recipes/{deptSlug}/add', App\Livewire\BranchDashboard\Production\Recipes\Add::class)->name('recipes.add');
                Route::get('recipes/{deptSlug}/{id}/edit', App\Livewire\BranchDashboard\Production\Recipes\Edit::class)->name('recipes.edit');
                Route::get('recipes/{deptSlug}/{id}', App\Livewire\BranchDashboard\Production\RecipeDetail::class)->name('recipes.detail');


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

            // Production Reports - Grouped
            Route::prefix('reports')->name('reports.')->group(function () {
                // Grouped report pages
                Route::get('/operations', \App\Livewire\BranchDashboard\Production\Reports\OperationsReports::class)->name('operations');
                Route::get('/performance', \App\Livewire\BranchDashboard\Production\Reports\PerformanceReports::class)->name('performance');
                Route::get('/planning', \App\Livewire\BranchDashboard\Production\Reports\PlanningReports::class)->name('planning');

                // Individual reports (kept for backward compatibility)
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

        // Export routes
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('stock-level-analytics', [\App\Http\Controllers\ExportController::class, 'stockLevelAnalytics'])->name('stock-level-analytics');
            Route::get('health-checks', [\App\Http\Controllers\ExportController::class, 'healthChecks'])->name('health-checks');
            Route::get('item-requests', [\App\Http\Controllers\ExportController::class, 'itemRequests'])->name('item-requests');
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

        // Accounting Routes - Role Based Access (Super Admin, MD, Accountant)
        Route::prefix('accounting')->name('accounting.')->middleware('role_or_permission:access_accounting,view_financial_reports')->group(function () {
            // Accounting Dashboard
            Route::get('/dashboard', \App\Livewire\BranchDashboard\Accounting\Dashboard::class)->name('dashboard');

            // Accounting Overview
            Route::get('/overview', \App\Livewire\BranchDashboard\Accounting\Overview::class)->name('overview');

            // Chart of Accounts Management (Super Admin, MD, Admin)
            Route::middleware('role_or_permission:manage_accounts')->group(function () {
                Route::get('/accounts', \App\Livewire\BranchDashboard\Accounting\GlAccountList::class)->name('accounts');
            });

            // Accounting Period Management (Super Admin, MD, Admin)
            Route::middleware('role_or_permission:manage_periods')->group(function () {
                Route::get('/periods', \App\Livewire\BranchDashboard\Accounting\PeriodManagement::class)->name('periods');
            });

            // Manual Journal Entry (Super Admin, MD, Accountant, Admin)
            Route::middleware('role_or_permission:create_journal_entries')->group(function () {
                Route::get('/journal-entry', \App\Livewire\BranchDashboard\Accounting\ManualJournalEntry::class)->name('journal-entry');
            });

            // Posting Status Monitor
            Route::get('/posting-status', \App\Livewire\BranchDashboard\Accounting\PostingStatusMonitor::class)->name('posting-status');

            // Inventory Valuation to GL
            Route::get('/inventory-valuation', \App\Livewire\BranchDashboard\Accounting\InventoryValuationPosting::class)->name('inventory-valuation');

            // Bank Reconciliation
            Route::middleware('role_or_permission:reconcile_bank_accounts')->group(function () {
                Route::get('/bank-reconciliation', \App\Livewire\BranchDashboard\Accounting\BankReconciliation::class)->name('bank-reconciliation');
            });

            // Financial Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/', \App\Livewire\BranchDashboard\Accounting\Report\Index::class)->name('index');
                Route::get('/general-ledger', \App\Livewire\BranchDashboard\Accounting\Report\GeneralLedgerReport::class)->name('general-ledger');
                Route::get('/trial-balance', \App\Livewire\BranchDashboard\Accounting\Report\TrialBalanceReport::class)->name('trial-balance');
                Route::get('/income-statement', \App\Livewire\BranchDashboard\Accounting\Report\IncomeStatementReport::class)->name('income-statement');
                Route::get('/balance-sheet', \App\Livewire\BranchDashboard\Accounting\Report\BalanceSheetReport::class)->name('balance-sheet');
                Route::get('/cash-flow-statement', \App\Livewire\BranchDashboard\Accounting\Report\CashFlowStatementReport::class)->name('cash-flow-statement');
            });
        });

        // Sales Dashboard routes - Modular System
        // Protected by department.scope + workflow middleware for proper step validation
        Route::prefix('sales-dashboard')->name('sales-dashboard.')->middleware([
            'department.scope',
            'validate-sales-department-context',
        ])->group(function () {

            // Helper function to register sales department routes with workflow protection
            $registerSalesDepartmentRoutes = function () {
                // POS Routes - Requires stock verification to be completed
                Route::prefix('pos')->name('pos.')->middleware(['validate-sales-workflow'])->group(function () {
                    Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\Pos\Index::class)->name('index');
                });

                // Analytics - Less restrictive, no workflow validation needed
                Route::prefix('analytics')->name('analytics.')->group(function () {
                    Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\Analytics\Index::class)->name('index');
                });

                // My Sales - Personal Sales Dashboard, less restrictive
                Route::prefix('my-sales')->name('my-sales.')->group(function () {
                    Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\MySales\Index::class)->name('index');
                });

                // Shift Closing - Sales (department-based) - Protected by workflow
                Route::prefix('shift-closing')->name('shift-closing.')->middleware(['validate-sales-workflow'])->group(function () {
                    Route::get('/{salesDeptSlug?}', \App\Livewire\BranchDashboard\SalesDashboard\ShiftClosing\Index::class)->name('index');
                });
            };

            // Expiry Alerts - shown after clock-in
            Route::get('/expiry-alerts', \App\Livewire\BranchDashboard\SalesDashboard\ExpiryAlerts::class)->name('expiry-alerts');

            // Stock Opening - Entry point for workflow, protected by department context
            Route::prefix('stock-opening')->name('stock-opening.')->middleware(['validate-sales-workflow'])->group(function () {
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

            // Execute dynamic sales department routes
            $registerSalesDepartmentRoutes();
        });
    }); // Close require_active_shift middleware group
});
