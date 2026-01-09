# Business Logic and Reporting Issues in Accounting System

## 4. Business Logic and Workflow Faults

### Problem Description
The accounting system has incomplete workflow handling where manual journal entries can be created for closed accounting periods, and there's no approval workflow for significant journal entries.

### Specific Code Evidence
- In `ManualJournalEntry.php`, the `submit()` method doesn't properly validate that the entry date falls within the accounting period
- No approval workflow exists for significant journal entries
- Missing validation for account type restrictions
- Incomplete reversal mechanisms for erroneous entries

### Manual Journal Entry Workflow Issues
```php
// From ManualJournalEntry.php - insufficient period validation
public function submit()
{
    $this->validate([
        'reference' => 'required|string|max:100|unique:gl_entries,reference',
        'periodId' => 'required|exists:accounting_periods,id',
        'description' => 'required|string|max:500',
        'entryDate' => 'required|date',
        // ... other validations
    ]);

    // Basic validation but missing business logic checks
    if (! $this->isBalanced) {
        throw ValidationException::withMessages(['lines' => 'Journal entry must be balanced (Debits = Credits)']);
    }

    try {
        $period = AccountingPeriod::findOrFail($this->periodId);

        foreach ($this->lines as $line) {
            if ($line['account_id']) {
                GlEntry::create([
                    // ... entry creation logic
                ]);
            }
        }
        // No approval workflow, no business logic validation
    } catch (\Exception $e) {
        // ... error handling
    }
}
```

### Impact
- Journal entries created for closed periods
- Fraudulent entries without proper approval
- Inconsistent business process execution
- Compliance violations

### Solution: Add Approval Workflow and Business Logic
```php
// Create a journal entry workflow service
class JournalEntryWorkflowService
{
    public function validateAndCreate(array $data, User $user): array
    {
        $errors = [];

        // Validate accounting period
        $period = AccountingPeriod::find($data['period_id']);
        if (!$period) {
            $errors[] = 'Invalid accounting period';
        } elseif ($period->isClosed()) {
            $errors[] = 'Cannot create entries for a closed accounting period';
        } elseif (!$period->isWithinPeriod(Carbon::parse($data['entry_date']))) {
            $errors[] = 'Entry date must fall within the selected accounting period';
        }

        // Validate entry amount thresholds
        $totalAmount = collect($data['lines'])->sum(function($line) {
            return max($line['debit'] ?? 0, $line['credit'] ?? 0);
        });

        if ($totalAmount > config('accounting.threshold_approval_required', 10000)) {
            $data['requires_approval'] = true;
        }

        // Validate account types
        foreach ($data['lines'] as $index => $line) {
            $account = GlAccount::find($line['account_id']);
            if ($account) {
                $validation = AccountTypeValidator::validateEntryForAccountType(
                    $account,
                    $line['debit'] ?? 0,
                    $line['credit'] ?? 0
                );
                
                if (!$validation['valid']) {
                    $errors[] = "Line {$index}: " . implode(', ', $validation['messages']);
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'processed_data' => $data
        ];
    }

    public function createWithApproval(array $data, User $user): GlEntry
    {
        $validation = $this->validateAndCreate($data, $user);
        
        if (!$validation['valid']) {
            throw new ValidationException(
                collect($validation['errors'])->mapWithKeys(function($error, $index) {
                    return ["line_{$index}" => $error];
                })->toArray()
            );
        }

        $processedData = $validation['processed_data'];

        // Determine if approval is required
        $requiresApproval = $processedData['requires_approval'] ?? false;

        DB::transaction(function () use ($processedData, $user, $requiresApproval) {
            $entries = [];
            
            foreach ($processedData['lines'] as $line) {
                if ($line['account_id']) {
                    $entry = GlEntry::create([
                        'accounting_period_id' => $processedData['period_id'],
                        'gl_account_id' => $line['account_id'],
                        'reference_type' => $processedData['reference_type'] ?? 'manual_journal',
                        'reference_id' => $processedData['reference_id'] ?? null,
                        'reference_number' => $processedData['reference'],
                        'description' => $line['description'] ?: $processedData['description'],
                        'debit' => (float) ($line['debit'] ?? 0),
                        'credit' => (float) ($line['credit'] ?? 0),
                        'entry_date' => $processedData['entry_date'],
                        'status' => $requiresApproval ? 'pending_approval' : 'draft',
                        'entered_by_id' => $user->id,
                        'entered_by_type' => get_class($user),
                        'branch_id' => $processedData['branch_id'] ?? session('branch_id'),
                    ]);

                    $entries[] = $entry;
                }
            }

            // If approval is required, create approval request
            if ($requiresApproval) {
                $this->createApprovalRequest(collect($entries), $user, $processedData['approval_reason'] ?? '');
            } elseif ($processedData['auto_post'] ?? false) {
                // Auto-post if user has permission and no approval required
                foreach ($entries as $entry) {
                    $entry->post($user->id);
                }
            }
        });

        return $entries[0] ?? null;
    }

    private function createApprovalRequest(Collection $entries, User $requester, string $reason): void
    {
        $approvalRequest = ApprovalRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'requestable_type' => get_class($entries->first()),
            'requestable_id' => $entries->first()->id,
            'action' => 'approve_journal_entries',
            'status' => 'pending',
            'reason' => $reason,
            'branch_id' => $entries->first()->branch_id,
            'metadata' => [
                'entry_ids' => $entries->pluck('id')->toArray(),
                'total_amount' => $entries->sum('debit'),
                'entry_count' => $entries->count(),
            ],
        ]);

        // Notify appropriate approvers
        $approvers = $this->getAppropriateApprovers($entries->first()->branch_id, $entries->sum('debit'));
        foreach ($approvers as $approver) {
            $approver->notify(new JournalEntryApprovalRequest($approvalRequest));
        }
    }

    private function getAppropriateApprovers(string $branchId, float $amount): Collection
    {
        $query = User::whereHas('roles.permissions', function ($q) {
            $q->where('name', 'approve-journal-entries');
        });

        if ($amount > config('accounting.high_value_threshold', 50000)) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['finance-manager', 'director']);
            });
        } else {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['accountant', 'senior-accountant', 'finance-manager']);
            });
        }

        return $query->get();
    }
}

// Enhanced ManualJournalEntry component
class ManualJournalEntry extends Component
{
    public bool $requiresApproval = false;
    public string $approvalReason = '';

    public function submit()
    {
        $this->validate([
            'reference' => 'required|string|max:100|unique:gl_entries,reference',
            'periodId' => 'required|exists:accounting_periods,id',
            'description' => 'required|string|max:500',
            'entryDate' => 'required|date',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:gl_accounts,id',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'status' => 'in:draft,pending_approval,posted',
        ]);

        if (! $this->isBalanced) {
            throw ValidationException::withMessages(['lines' => 'Journal entry must be balanced (Debits = Credits)']);
        }

        try {
            $workflowService = app(JournalEntryWorkflowService::class);
            
            $data = [
                'reference' => $this->reference,
                'period_id' => $this->periodId,
                'description' => $this->description,
                'entry_date' => $this->entryDate,
                'lines' => $this->lines,
                'branch_id' => session('branch_id'),
                'auto_post' => $this->status === 'posted',
                'approval_reason' => $this->approvalReason,
            ];

            $entry = $workflowService->createWithApproval($data, auth()->user());

            session()->flash('success', 'Journal entry created successfully' . 
                ($this->requiresApproval ? ' and sent for approval' : ''));

            $this->reset();
            $this->redirect(route('branch-dashboard.accounting.index'));

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['general' => $e->getMessage()]);
        }
    }
}
```

### Approval Workflow Implementation
#### Problem Description
No systematic approval process for significant journal entries, creating risk of unauthorized changes.

#### Solution: Implement Multi-Level Approval System
```php
// Create approval models and services
class ApprovalLevel extends Model
{
    protected $fillable = [
        'name',
        'level',
        'min_amount',
        'max_amount',
        'required_approver_role',
        'branch_id',
        'is_active',
    ];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function approvalRequests()
    {
        return $this->hasMany(ApprovalRequest::class);
    }
}

class ApprovalRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'requester_type',
        'approver_id',
        'approver_type',
        'requestable_type',
        'requestable_id',
        'action',
        'status',
        'reason',
        'branch_id',
        'approved_by_id',
        'approved_at',
        'rejected_by_id',
        'rejected_at',
        'rejection_reason',
        'metadata',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function requester()
    {
        return $this->morphTo();
    }

    public function approver()
    {
        return $this->morphTo();
    }

    public function requestable()
    {
        return $this->morphTo();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function approve(User $approver, ?string $comments = null): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Validate that approver has permission
        if (!$approver->can('approve-' . str_replace('_', '-', $this->action))) {
            throw new AuthorizationException('Not authorized to approve this request');
        }

        // Validate segregation of duties
        if ($this->requester_id === $approver->id) {
            throw new AuthorizationException('Cannot approve your own requests');
        }

        $this->update([
            'status' => 'approved',
            'approved_by_id' => $approver->id,
            'approved_at' => now(),
            'comments' => $comments,
        ]);

        // Execute the requested action
        $this->executeApprovedAction($approver);

        return true;
    }

    public function reject(User $approver, string $reason): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'rejected',
            'rejected_by_id' => $approver->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return true;
    }

    private function executeApprovedAction(User $approver): void
    {
        $requestable = $this->requestable;

        switch ($this->action) {
            case 'approve_journal_entries':
                if ($requestable instanceof GlEntry) {
                    $requestable->post($approver->id);
                    
                    // If this was part of a batch, process other entries
                    if (isset($this->metadata['entry_ids'])) {
                        foreach ($this->metadata['entry_ids'] as $entryId) {
                            if ($entryId !== $requestable->id) {
                                $entry = GlEntry::find($entryId);
                                if ($entry && $entry->status === 'pending_approval') {
                                    $entry->post($approver->id);
                                }
                            }
                        }
                    }
                }
                break;
                
            case 'reverse_journal_entry':
                if ($requestable instanceof GlEntry) {
                    $requestable->reverse($approver->id);
                }
                break;
        }
    }
}

// Enhanced approval service
class EnhancedApprovalService
{
    public function getApprovalRequirements(float $amount, string $action, string $branchId): array
    {
        $levels = ApprovalLevel::where('branch_id', $branchId)
            ->where('is_active', true)
            ->where('action', $action)
            ->where('min_amount', '<=', $amount)
            ->where(function ($query) use ($amount) {
                $query->whereNull('max_amount')
                      ->orWhere('max_amount', '>=', $amount);
            })
            ->orderBy('level')
            ->get();

        return [
            'requires_approval' => !$levels->isEmpty(),
            'approval_levels' => $levels,
            'next_approvers' => $this->getNextApprovers($levels, $branchId)
        ];
    }

    private function getNextApprovers(Collection $levels, string $branchId): Collection
    {
        if ($levels->isEmpty()) {
            return collect();
        }

        $topLevel = $levels->sortByDesc('level')->first();
        
        return User::whereHas('roles', function ($q) use ($topLevel) {
            $q->where('name', $topLevel->required_approver_role);
        })
        ->whereRelation('employee', 'branch_id', $branchId)
        ->get();
    }

    public function createApprovalRequest(
        $requestable,
        User $requester,
        string $action,
        string $reason,
        array $metadata = []
    ): ApprovalRequest {
        return ApprovalRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'requestable_type' => get_class($requestable),
            'requestable_id' => $requestable->id,
            'action' => $action,
            'status' => 'pending',
            'reason' => $reason,
            'branch_id' => $requestable->branch_id ?? session('branch_id'),
            'metadata' => $metadata,
        ]);
    }
}
```

## 5. Reporting and Compliance Faults

### Problem Description
The accounting system has inaccurate trial balance calculations, missing comparative reporting capabilities, and insufficient financial statement accuracy.

### Specific Code Evidence
- In `TrialBalanceService.php`, the `getTrialBalance()` method doesn't properly handle account types
- Missing comparative reporting between periods
- No automated reconciliation checks
- Inconsistent financial statement generation

### Trial Balance Calculation Issues
```php
// From TrialBalanceService.php - basic trial balance calculation
public function getTrialBalance(?int $periodId = null): array
{
    $accounts = GlAccount::where('is_active', true)
        ->where('account_type', '!=', 'header')
        ->orderBy('account_number')
        ->get();

    $balances = [];
    $totalDebits = 0;
    $totalCredits = 0;

    foreach ($accounts as $account) {
        $query = GlEntry::where('gl_account_id', $account->id)
            ->where('status', 'posted');

        if ($periodId) {
            $query->where('accounting_period_id', $periodId);
        }

        $debits = (float) $query->sum('debit');
        $credits = (float) $query->sum('credit');

        // Only include accounts with activity
        if ($debits > 0 || $credits > 0) {
            $balances[] = [
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'account_id' => $account->id,
                'debit' => $debits,
                'credit' => $credits,
            ];

            $totalDebits += $debits;
            $totalCredits += $credits;
        }
    }

    return [
        'accounts' => $balances,
        'total_debits' => $totalDebits,
        'total_credits' => $totalCredits,
        'balanced' => abs($totalDebits - $totalCredits) < 0.01,
        'difference' => $totalDebits - $totalCredits,
        'period_id' => $periodId,
    ];
}
```

### Impact
- Inaccurate financial statements
- Compliance violations
- Audit findings
- Poor decision-making based on incorrect data

### Solution: Enhanced Reporting System
```php
// Enhanced TrialBalanceService with proper calculations
class EnhancedTrialBalanceService
{
    public function getDetailedTrialBalance(?int $periodId = null, array $options = []): array
    {
        $includeHeaders = $options['include_headers'] ?? false;
        $groupByType = $options['group_by_type'] ?? false;
        $withComparative = $options['with_comparative'] ?? false;
        $previousPeriodId = $options['previous_period_id'] ?? null;

        $query = DB::table('gl_accounts as accounts')
            ->leftJoin('gl_entries as entries', function ($join) use ($periodId) {
                $join->on('accounts.id', '=', 'entries.gl_account_id')
                     ->where('entries.status', '=', 'posted');
                
                if ($periodId) {
                    $join->where('entries.accounting_period_id', '=', $periodId);
                }
            })
            ->select(
                'accounts.id as account_id',
                'accounts.account_number',
                'accounts.account_name',
                'accounts.account_type',
                'accounts.account_category',
                'accounts.normal_balance',
                DB::raw('COALESCE(SUM(entries.debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(entries.credit), 0) as total_credit')
            )
            ->where('accounts.is_active', true);

        if (!$includeHeaders) {
            $query->where('accounts.account_type', '!=', 'header');
        }

        $query->groupBy(
            'accounts.id',
            'accounts.account_number',
            'accounts.account_name',
            'accounts.account_type',
            'accounts.account_category',
            'accounts.normal_balance'
        )
        ->orderBy('accounts.account_number');

        $results = $query->get();

        $accounts = [];
        $totals = [
            'total_debits' => 0,
            'total_credits' => 0,
            'total_balance' => 0,
        ];

        foreach ($results as $result) {
            $balance = $this->calculateAccountBalance(
                $result->account_type,
                $result->normal_balance,
                $result->total_debit,
                $result->total_credit
            );

            $accountData = [
                'account_id' => $result->account_id,
                'account_number' => $result->account_number,
                'account_name' => $result->account_name,
                'account_type' => $result->account_type,
                'account_category' => $result->account_category,
                'debit' => (float) $result->total_debit,
                'credit' => (float) $result->total_credit,
                'balance' => $balance,
                'normal_balance' => $result->normal_balance,
            ];

            $accounts[] = $accountData;

            $totals['total_debits'] += (float) $result->total_debit;
            $totals['total_credits'] += (float) $result->total_credit;
            $totals['total_balance'] += $balance;
        }

        $response = [
            'accounts' => $accounts,
            'totals' => $totals,
            'balanced' => abs($totals['total_debits'] - $totals['total_credits']) < 0.01,
            'difference' => $totals['total_debits'] - $totals['total_credits'],
            'period_id' => $periodId,
            'generated_at' => now(),
        ];

        // Add comparative data if requested
        if ($withComparative && $previousPeriodId) {
            $response['comparative'] = $this->getComparativeData($periodId, $previousPeriodId, $accounts);
        }

        return $response;
    }

    private function calculateAccountBalance(string $accountType, string $normalBalance, float $debit, float $credit): float
    {
        switch ($accountType) {
            case 'asset':
            case 'expense':
            case 'cogs':
                // These accounts normally have debit balances
                return $debit - $credit;
                
            case 'liability':
            case 'equity':
            case 'revenue':
            case 'tax':
                // These accounts normally have credit balances
                return $credit - $debit;
                
            case 'contra_asset':
            case 'contra_liability':
            case 'contra_equity':
            case 'contra_revenue':
                // Contra accounts have opposite normal balances
                return $credit - $debit;
                
            case 'contra_expense':
                return $debit - $credit;
                
            default:
                // Default to debit balance calculation
                return $debit - $credit;
        }
    }

    private function getComparativeData(?int $currentPeriodId, int $previousPeriodId, array $currentAccounts): array
    {
        $previousData = $this->getDetailedTrialBalance($previousPeriodId);
        
        $comparison = [];
        foreach ($currentAccounts as $currentAccount) {
            $prevAccount = collect($previousData['accounts'])
                ->firstWhere('account_id', $currentAccount['account_id']);

            $comparison[] = [
                'account_number' => $currentAccount['account_number'],
                'account_name' => $currentAccount['account_name'],
                'current_debit' => $currentAccount['debit'],
                'current_credit' => $currentAccount['credit'],
                'current_balance' => $currentAccount['balance'],
                'previous_debit' => $prevAccount['debit'] ?? 0,
                'previous_credit' => $prevAccount['credit'] ?? 0,
                'previous_balance' => $prevAccount['balance'] ?? 0,
                'debit_change' => $currentAccount['debit'] - ($prevAccount['debit'] ?? 0),
                'credit_change' => $currentAccount['credit'] - ($prevAccount['credit'] ?? 0),
                'balance_change' => $currentAccount['balance'] - ($prevAccount['balance'] ?? 0),
            ];
        }

        return [
            'current_period_id' => $currentPeriodId,
            'previous_period_id' => $previousPeriodId,
            'comparisons' => $comparison,
            'summary' => [
                'current_total_debits' => $currentData['totals']['total_debits'],
                'current_total_credits' => $currentData['totals']['total_credits'],
                'previous_total_debits' => $previousData['totals']['total_debits'],
                'previous_total_credits' => $previousData['totals']['total_credits'],
                'debit_change' => $currentData['totals']['total_debits'] - $previousData['totals']['total_debits'],
                'credit_change' => $currentData['totals']['total_credits'] - $previousData['totals']['total_credits'],
            ]
        ];
    }
}

// Enhanced Balance Sheet Service
class EnhancedBalanceSheetService
{
    public function generateBalanceSheet(?int $periodId = null): array
    {
        $trialBalance = app(EnhancedTrialBalanceService::class)->getDetailedTrialBalance($periodId);

        $assets = [];
        $liabilities = [];
        $equity = [];

        foreach ($trialBalance['accounts'] as $account) {
            switch ($account['account_type']) {
                case 'asset':
                    if ($account['balance'] != 0) {
                        $assets[] = $account;
                    }
                    break;
                    
                case 'liability':
                    if ($account['balance'] != 0) {
                        $liabilities[] = $account;
                    }
                    break;
                    
                case 'equity':
                    if ($account['balance'] != 0) {
                        $equity[] = $account;
                    }
                    break;
            }
        }

        $totalAssets = collect($assets)->sum('balance');
        $totalLiabilities = collect($liabilities)->sum('balance');
        $totalEquity = collect($equity)->sum('balance');
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totals' => [
                'total_assets' => $totalAssets,
                'total_liabilities' => $totalLiabilities,
                'total_equity' => $totalEquity,
                'total_liabilities_equity' => $totalLiabilitiesEquity,
            ],
            'is_balanced' => abs($totalAssets - $totalLiabilitiesEquity) < 0.01,
            'difference' => $totalAssets - $totalLiabilitiesEquity,
            'period_id' => $periodId,
            'generated_at' => now(),
        ];
    }

    public function generateComparativeBalanceSheet(int $currentPeriodId, int $previousPeriodId): array
    {
        $currentBs = $this->generateBalanceSheet($currentPeriodId);
        $previousBs = $this->generateBalanceSheet($previousPeriodId);

        return [
            'current_period' => $currentBs,
            'previous_period' => $previousBs,
            'comparisons' => [
                'assets_change' => $currentBs['totals']['total_assets'] - $previousBs['totals']['total_assets'],
                'liabilities_change' => $currentBs['totals']['total_liabilities'] - $previousBs['totals']['total_liabilities'],
                'equity_change' => $currentBs['totals']['total_equity'] - $previousBs['totals']['total_equity'],
            ]
        ];
    }
}

// Enhanced Income Statement Service
class EnhancedIncomeStatementService
{
    public function generateIncomeStatement(?int $periodId = null): array
    {
        $trialBalance = app(EnhancedTrialBalanceService::class)->getDetailedTrialBalance($periodId);

        $revenues = [];
        $expenses = [];
        $cogs = [];

        foreach ($trialBalance['accounts'] as $account) {
            switch ($account['account_type']) {
                case 'revenue':
                    if ($account['balance'] != 0) {
                        $revenues[] = $account;
                    }
                    break;
                    
                case 'expense':
                    if ($account['balance'] != 0) {
                        $expenses[] = $account;
                    }
                    break;
                    
                case 'cogs':
                    if ($account['balance'] != 0) {
                        $cogs[] = $account;
                    }
                    break;
            }
        }

        $totalRevenues = collect($revenues)->sum('balance');
        $totalCogs = collect($cogs)->sum('balance');
        $totalExpenses = collect($expenses)->sum('balance');

        $grossProfit = $totalRevenues - $totalCogs;
        $netIncome = $grossProfit - $totalExpenses;

        return [
            'revenues' => $revenues,
            'cogs' => $cogs,
            'expenses' => $expenses,
            'totals' => [
                'total_revenues' => $totalRevenues,
                'total_cogs' => $totalCogs,
                'total_expenses' => $totalExpenses,
                'gross_profit' => $grossProfit,
                'net_income' => $netIncome,
            ],
            'period_id' => $periodId,
            'generated_at' => now(),
        ];
    }
}
```

### Automated Reconciliation Checks
#### Problem Description
No automated checks to ensure data integrity and reconciliation between different accounting components.

#### Solution: Implement Reconciliation System
```php
// Create a reconciliation service
class AccountingReconciliationService
{
    public function runPeriodEndReconciliation(int $periodId): array
    {
        $checks = [
            'trial_balance_check' => $this->checkTrialBalance($periodId),
            'balance_sheet_check' => $this->checkBalanceSheet($periodId),
            'cash_reconciliation' => $this->checkCashAccounts($periodId),
            'bank_reconciliation' => $this->checkBankAccounts($periodId),
            'intercompany_check' => $this->checkIntercompanyBalances($periodId),
        ];

        $overallStatus = 'pass';
        foreach ($checks as $check) {
            if ($check['status'] !== 'pass') {
                $overallStatus = 'fail';
                break;
            }
        }

        return [
            'overall_status' => $overallStatus,
            'checks' => $checks,
            'generated_at' => now(),
        ];
    }

    public function checkTrialBalance(int $periodId): array
    {
        $trialBalance = app(TrialBalanceService::class)->getTrialBalance($periodId);
        
        $isBalanced = abs($trialBalance['total_debits'] - $trialBalance['total_credits']) < 0.01;
        
        return [
            'name' => 'Trial Balance Check',
            'status' => $isBalanced ? 'pass' : 'fail',
            'details' => [
                'total_debits' => $trialBalance['total_debits'],
                'total_credits' => $trialBalance['total_credits'],
                'difference' => $trialBalance['difference'],
            ],
            'recommendations' => $isBalanced ? [] : [
                'Investigate the difference of ' . $trialBalance['difference'],
                'Review all journal entries for the period',
                'Check for unposted entries',
            ]
        ];
    }

    public function checkBalanceSheet(int $periodId): array
    {
        $balanceSheet = app(BalanceSheetService::class)->generateBalanceSheet($periodId);
        
        $assetsEqualLiabilitiesEquity = abs(
            $balanceSheet['totals']['total_assets'] - 
            ($balanceSheet['totals']['total_liabilities'] + $balanceSheet['totals']['total_equity'])
        ) < 0.01;

        return [
            'name' => 'Balance Sheet Check',
            'status' => $assetsEqualLiabilitiesEquity ? 'pass' : 'fail',
            'details' => [
                'total_assets' => $balanceSheet['totals']['total_assets'],
                'total_liabilities' => $balanceSheet['totals']['total_liabilities'],
                'total_equity' => $balanceSheet['totals']['total_equity'],
                'liabilities_plus_equity' => $balanceSheet['totals']['total_liabilities'] + $balanceSheet['totals']['total_equity'],
                'difference' => $balanceSheet['difference'],
            ],
            'recommendations' => $assetsEqualLiabilitiesEquity ? [] : [
                'Assets do not equal Liabilities + Equity',
                'Review balance sheet account classifications',
                'Check for missing entries',
            ]
        ];
    }

    public function checkCashAccounts(int $periodId): array
    {
        $cashAccounts = GlAccount::where('account_category', 'like', '%Cash%')
            ->orWhere('account_number', 'LIKE', '110_%')
            ->orWhere('account_number', 'LIKE', '111_%')
            ->get();

        $totalCalculatedCash = 0;
        $cashDetails = [];

        foreach ($cashAccounts as $account) {
            $balance = app(GeneralLedgerService::class)->getAccountBalance($account->id, $periodId);
            $totalCalculatedCash += $balance;
            
            $cashDetails[] = [
                'account' => $account->account_number . ' - ' . $account->account_name,
                'balance' => $balance,
            ];
        }

        // Compare with physical cash counts or bank statements
        $expectedCash = $this->getExpectedCashAmount($periodId);
        $variance = abs($totalCalculatedCash - $expectedCash);

        return [
            'name' => 'Cash Account Check',
            'status' => $variance < 10 ? 'pass' : 'warn', // Allow small variances
            'details' => [
                'calculated_cash' => $totalCalculatedCash,
                'expected_cash' => $expectedCash,
                'variance' => $variance,
                'cash_accounts' => $cashDetails,
            ],
            'recommendations' => $variance > 10 ? [
                'Variance of ' . $variance . ' detected in cash accounts',
                'Perform physical cash count verification',
                'Review cash receipts and disbursements',
            ] : []
        ];
    }

    public function checkBankAccounts(int $periodId): array
    {
        $bankAccounts = BankAccount::all();
        $results = [];

        foreach ($bankAccounts as $bankAccount) {
            if ($bankAccount->gl_account_id) {
                $glBalance = app(GeneralLedgerService::class)
                    ->getAccountBalance($bankAccount->gl_account_id, $periodId);
                
                $bookBalance = $bankAccount->balance;
                
                $variance = abs($glBalance - $bookBalance);
                
                $results[] = [
                    'bank_account' => $bankAccount->bank_name . ' - ' . $bankAccount->account_number,
                    'gl_balance' => $glBalance,
                    'book_balance' => $bookBalance,
                    'variance' => $variance,
                    'status' => $variance < 0.01 ? 'reconciled' : 'needs_reconciliation',
                ];
            }
        }

        $unreconciled = collect($results)->filter(fn($r) => $r['status'] !== 'reconciled')->count();

        return [
            'name' => 'Bank Account Reconciliation Check',
            'status' => $unreconciled === 0 ? 'pass' : 'fail',
            'details' => $results,
            'summary' => [
                'total_accounts' => count($results),
                'reconciled' => count($results) - $unreconciled,
                'needs_attention' => $unreconciled,
            ],
            'recommendations' => $unreconciled > 0 ? [
                'Reconcile ' . $unreconciled . ' bank accounts',
                'Review outstanding deposits and checks',
                'Check for bank fees or interest',
            ] : []
        ];
    }

    private function getExpectedCashAmount(int $periodId): float
    {
        // This would integrate with cash management system
        // For now, return a placeholder
        return BranchAccountingCash::where('accounting_period_id', $periodId)
            ->sum('amount');
    }
}
```

## Implementation Checklist

- [ ] Implement journal entry approval workflow
- [ ] Create multi-level approval system
- [ ] Enhance trial balance calculations
- [ ] Add comparative reporting capabilities
- [ ] Implement automated reconciliation checks
- [ ] Create enhanced financial statement services
- [ ] Add proper error handling and validation
- [ ] Update all related components and services