<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Accounting Routes
|--------------------------------------------------------------------------
|
| Here are all the routes related to the accounting system
|
*/

Route::middleware(['auth', 'accounting'])->prefix('accounting')->group(function () {
    
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Accounting\DashboardController::class, 'index'])
        ->name('accounting.dashboard');
    
    // GL Accounts Management
    Route::prefix('gl-accounts')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\GlAccountController::class, 'index'])
            ->name('accounting.gl-accounts.index');
        
        Route::get('{account}', [\App\Http\Controllers\Accounting\GlAccountController::class, 'show'])
            ->name('accounting.gl-accounts.show');
        
        Route::get('{account}/ledger', [\App\Http\Controllers\Accounting\GlAccountController::class, 'ledger'])
            ->name('accounting.gl-accounts.ledger');
        
        Route::put('{account}/toggle-active', [\App\Http\Controllers\Accounting\GlAccountController::class, 'toggleActive'])
            ->name('accounting.gl-accounts.toggle-active');
        
        Route::get('{account}/audit-trail', [\App\Http\Controllers\Accounting\GlAccountController::class, 'auditTrail'])
            ->name('accounting.gl-accounts.audit-trail');
    });
    
    // Journal Entries
    Route::prefix('journal-entries')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'index'])
            ->name('accounting.journal-entries.index');
        
        Route::get('create', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'create'])
            ->name('accounting.journal-entries.create');
        
        Route::post('/', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'store'])
            ->name('accounting.journal-entries.store');
        
        Route::get('{entry}', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'show'])
            ->name('accounting.journal-entries.show');
        
        Route::post('{entry}/post', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'post'])
            ->name('accounting.journal-entries.post');
        
        Route::post('{entry}/reverse', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'reverse'])
            ->name('accounting.journal-entries.reverse');
        
        Route::get('{entry}/audit-trail', [\App\Http\Controllers\Accounting\JournalEntryController::class, 'auditTrail'])
            ->name('accounting.journal-entries.audit-trail');
    });
    
    // Accounting Periods
    Route::prefix('periods')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\PeriodController::class, 'index'])
            ->name('accounting.periods.index');
        
        Route::get('{period}', [\App\Http\Controllers\Accounting\PeriodController::class, 'show'])
            ->name('accounting.periods.show');
        
        Route::post('{period}/close', [\App\Http\Controllers\Accounting\PeriodController::class, 'close'])
            ->name('accounting.periods.close');
        
        Route::post('{period}/reopen', [\App\Http\Controllers\Accounting\PeriodController::class, 'reopen'])
            ->name('accounting.periods.reopen');
        
        Route::post('{period}/lock', [\App\Http\Controllers\Accounting\PeriodController::class, 'lock'])
            ->name('accounting.periods.lock');
    });
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\ReportController::class, 'index'])
            ->name('accounting.reports.index');
        
        Route::get('balance-sheet', [\App\Http\Controllers\Accounting\ReportController::class, 'balanceSheet'])
            ->name('accounting.reports.balance-sheet');
        
        Route::get('income-statement', [\App\Http\Controllers\Accounting\ReportController::class, 'incomeStatement'])
            ->name('accounting.reports.income-statement');
        
        Route::get('trial-balance', [\App\Http\Controllers\Accounting\ReportController::class, 'trialBalance'])
            ->name('accounting.reports.trial-balance');
        
        Route::get('general-ledger', [\App\Http\Controllers\Accounting\ReportController::class, 'generalLedger'])
            ->name('accounting.reports.general-ledger');
        
        Route::get('cash-flow', [\App\Http\Controllers\Accounting\ReportController::class, 'cashFlow'])
            ->name('accounting.reports.cash-flow');
        
        Route::get('ar-aging', [\App\Http\Controllers\Accounting\ReportController::class, 'arAging'])
            ->name('accounting.reports.ar-aging');
        
        Route::post('{report}/export', [\App\Http\Controllers\Accounting\ReportController::class, 'export'])
            ->name('accounting.reports.export');
        
        Route::post('{report}/print', [\App\Http\Controllers\Accounting\ReportController::class, 'print'])
            ->name('accounting.reports.print');
    });
    
    // Bank Reconciliation
    Route::prefix('bank-reconciliation')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\BankReconciliationController::class, 'index'])
            ->name('accounting.bank-reconciliation.index');
        
        Route::get('{account}/reconcile', [\App\Http\Controllers\Accounting\BankReconciliationController::class, 'reconcile'])
            ->name('accounting.bank-reconciliation.reconcile');
        
        Route::post('{account}/match', [\App\Http\Controllers\Accounting\BankReconciliationController::class, 'match'])
            ->name('accounting.bank-reconciliation.match');
        
        Route::post('{account}/mark-reconciled', [\App\Http\Controllers\Accounting\BankReconciliationController::class, 'markReconciled'])
            ->name('accounting.bank-reconciliation.mark-reconciled');
        
        Route::get('{account}/summary', [\App\Http\Controllers\Accounting\BankReconciliationController::class, 'summary'])
            ->name('accounting.bank-reconciliation.summary');
    });
    
    // Audit & Compliance
    Route::middleware(['accounting.audit'])->prefix('audit')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\AuditController::class, 'index'])
            ->name('accounting.audit.index');
        
        Route::get('trail', [\App\Http\Controllers\Accounting\AuditController::class, 'auditTrail'])
            ->name('accounting.audit.trail');
        
        Route::get('user-activity', [\App\Http\Controllers\Accounting\AuditController::class, 'userActivity'])
            ->name('accounting.audit.user-activity');
        
        Route::get('compliance', [\App\Http\Controllers\Accounting\AuditController::class, 'compliance'])
            ->name('accounting.audit.compliance');
        
        Route::get('changes', [\App\Http\Controllers\Accounting\AuditController::class, 'changes'])
            ->name('accounting.audit.changes');
        
        Route::post('export', [\App\Http\Controllers\Accounting\AuditController::class, 'export'])
            ->name('accounting.audit.export');
    });
    
    // Bank Accounts
    Route::prefix('bank-accounts')->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\BankAccountController::class, 'index'])
            ->name('accounting.bank-accounts.index');
        
        Route::get('create', [\App\Http\Controllers\Accounting\BankAccountController::class, 'create'])
            ->name('accounting.bank-accounts.create');
        
        Route::post('/', [\App\Http\Controllers\Accounting\BankAccountController::class, 'store'])
            ->name('accounting.bank-accounts.store');
        
        Route::get('{account}', [\App\Http\Controllers\Accounting\BankAccountController::class, 'show'])
            ->name('accounting.bank-accounts.show');
        
        Route::get('{account}/edit', [\App\Http\Controllers\Accounting\BankAccountController::class, 'edit'])
            ->name('accounting.bank-accounts.edit');
        
        Route::put('{account}', [\App\Http\Controllers\Accounting\BankAccountController::class, 'update'])
            ->name('accounting.bank-accounts.update');
        
        Route::delete('{account}', [\App\Http\Controllers\Accounting\BankAccountController::class, 'destroy'])
            ->name('accounting.bank-accounts.destroy');
    });
    
    // Settings
    Route::prefix('settings')->middleware(['accounting.admin'])->group(function () {
        Route::get('/', [\App\Http\Controllers\Accounting\SettingsController::class, 'index'])
            ->name('accounting.settings.index');
        
        Route::post('/update', [\App\Http\Controllers\Accounting\SettingsController::class, 'update'])
            ->name('accounting.settings.update');
        
        Route::post('/coa/reset', [\App\Http\Controllers\Accounting\SettingsController::class, 'resetCoa'])
            ->name('accounting.settings.coa.reset');
        
        Route::get('/audit-logs', [\App\Http\Controllers\Accounting\SettingsController::class, 'auditLogs'])
            ->name('accounting.settings.audit-logs');
    });
});

// API Routes for Accounting
Route::middleware(['auth', 'api', 'accounting'])->prefix('api/accounting')->group(function () {
    
    // GL Accounts API
    Route::get('/gl-accounts', [\App\Http\Controllers\Api\GlAccountController::class, 'index']);
    Route::get('/gl-accounts/{account}', [\App\Http\Controllers\Api\GlAccountController::class, 'show']);
    Route::get('/gl-accounts/{account}/balance', [\App\Http\Controllers\Api\GlAccountController::class, 'balance']);
    
    // Journal Entries API
    Route::get('/journal-entries', [\App\Http\Controllers\Api\JournalEntryController::class, 'index']);
    Route::post('/journal-entries', [\App\Http\Controllers\Api\JournalEntryController::class, 'store']);
    Route::get('/journal-entries/{entry}', [\App\Http\Controllers\Api\JournalEntryController::class, 'show']);
    Route::post('/journal-entries/{entry}/post', [\App\Http\Controllers\Api\JournalEntryController::class, 'post']);
    
    // Accounting Periods API
    Route::get('/periods', [\App\Http\Controllers\Api\PeriodController::class, 'index']);
    Route::get('/periods/{period}', [\App\Http\Controllers\Api\PeriodController::class, 'show']);
    Route::get('/periods/current', [\App\Http\Controllers\Api\PeriodController::class, 'current']);
    
    // Reports API
    Route::get('/reports/balance-sheet', [\App\Http\Controllers\Api\ReportController::class, 'balanceSheet']);
    Route::get('/reports/income-statement', [\App\Http\Controllers\Api\ReportController::class, 'incomeStatement']);
    Route::get('/reports/trial-balance', [\App\Http\Controllers\Api\ReportController::class, 'trialBalance']);
    Route::get('/reports/general-ledger', [\App\Http\Controllers\Api\ReportController::class, 'generalLedger']);
    
    // Bank Reconciliation API
    Route::get('/bank-reconciliation/{account}', [\App\Http\Controllers\Api\BankReconciliationController::class, 'reconcile']);
    Route::post('/bank-reconciliation/{account}/match', [\App\Http\Controllers\Api\BankReconciliationController::class, 'match']);
    
    // Audit Trail API
    Route::get('/audit-trail', [\App\Http\Controllers\Api\AuditController::class, 'trail']);
    Route::get('/audit-trail/{model}', [\App\Http\Controllers\Api\AuditController::class, 'modelTrail']);
});
