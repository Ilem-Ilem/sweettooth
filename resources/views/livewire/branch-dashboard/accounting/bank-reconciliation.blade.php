<div class="container-fluid p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Bank Reconciliation</h1>
            <p class="text-muted mb-0">Match GL entries with bank transactions</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <button class="nav-link @if($activeTab === 'select') active @endif" 
                        wire:click="$set('activeTab', 'select')">
                        1️⃣ Select Bank Account
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link @if($activeTab === 'matching') active @endif" 
                        wire:click="$set('activeTab', 'matching')" @if(!$selectedReconciliationId) disabled @endif>
                        2️⃣ Match Transactions
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link @if($activeTab === 'results') active @endif" 
                        wire:click="$set('activeTab', 'results')" @if(!$selectedReconciliationId) disabled @endif>
                        3️⃣ Results
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- STEP 1: SELECT BANK ACCOUNT -->
    @if($activeTab === 'select')
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Start New Reconciliation</h5>
                </div>
                <div class="card-body">
                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Bank Account Selection -->
                    <div class="mb-3">
                        <label for="bankAccount" class="form-label">Select Bank Account</label>
                        <select id="bankAccount" wire:model="selectedBankAccountId" class="form-select">
                            <option value="">-- Select Bank Account --</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedBankAccountId') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Reconciliation Date -->
                    <div class="mb-3">
                        <label for="reconciliationDate" class="form-label">Reconciliation Date</label>
                        <input type="date" id="reconciliationDate" wire:model="reconciliationDate" class="form-control">
                        @error('reconciliationDate') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Bank Balance -->
                    <div class="mb-3">
                        <label for="bankBalance" class="form-label">Bank Balance (from statement)</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $this->getCurrencySymbol() }}</span>
                            <input type="number" id="bankBalance" wire:model="bankBalance" class="form-control" placeholder="0.00" step="0.01">
                        </div>
                        @error('bankBalance') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <button wire:click="startReconciliation" class="btn btn-primary">
                            Start Reconciliation →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- STEP 2: MATCH TRANSACTIONS -->
    @if($activeTab === 'matching' && $selectedReconciliationId)
    <div class="row mb-4">
        <!-- Bank Balance Summary -->
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <div class="card border-left-info">
                        <div class="card-body">
                            <h6 class="text-muted">Bank Balance</h6>
                            <h4>{{ $this->formatCurrency($stats['bank_balance'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <h6 class="text-muted">Book Balance</h6>
                            <h4>{{ $this->formatCurrency($stats['book_balance'] ?? 0) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <h6 class="text-muted">Matched</h6>
                            <h4>{{ $stats['matched_count'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card @if($isBalanced) border-left-success @else border-left-danger @endif">
                        <div class="card-body">
                            <h6 class="text-muted">Difference</h6>
                            <h4 class="@if($isBalanced) text-success @else text-danger @endif">
                                {{ $this->formatCurrency(abs($stats['difference'] ?? 0)) }}
                            </h4>
                            @if($isBalanced)
                                <small class="text-success">✓ Balanced</small>
                            @else
                                <small class="text-danger">✗ Unbalanced</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Matching Interface -->
    <div class="row">
        <div class="col-12">
            <!-- Auto Match Button -->
            <div class="mb-3">
                <button wire:click="performAutoMatch" class="btn btn-info btn-sm">
                    ⚡ Auto-Match Transactions
                </button>
                @if($autoMatchCount > 0)
                    <span class="badge bg-success">{{ $autoMatchCount }} matched</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="row">
        <!-- GL Entries Column -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">GL Entries ({{ count($glEntries) }})</h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @if(count($glEntries) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($glEntries as $entry)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1"><strong>{{ $entry['account_name'] }}</strong></p>
                                            <small class="text-muted">{{ $entry['date'] }} • {{ $entry['reference'] }}</small>
                                            <p class="mb-0 small">{{ $entry['description'] }}</p>
                                        </div>
                                        <div class="text-end">
                                            <h6 class="mb-0">{{ $this->formatCurrency($entry['amount']) }}</h6>
                                            <small class="badge bg-secondary">{{ ucfirst($entry['type']) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted p-3 mb-0">All GL entries have been reconciled!</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bank Transactions Column -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Bank Transactions ({{ count($bankTransactions) }})</h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @if(count($bankTransactions) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($bankTransactions as $transaction)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1"><strong>{{ $transaction['description'] }}</strong></p>
                                            <small class="text-muted">{{ $transaction['date'] }} • {{ $transaction['reference'] }}</small>
                                            <p class="mb-0 small">{{ ucfirst($transaction['type']) }}</p>
                                        </div>
                                        <div class="text-end">
                                            <h6 class="mb-0">{{ $this->formatCurrency($transaction['amount']) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted p-3 mb-0">All bank transactions have been reconciled!</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Matched Pairs Column -->
        <div class="col-lg-2">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Matched ({{ count($matchedPairs) }})</h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @if(count($matchedPairs) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($matchedPairs as $pair)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <small>{{ $this->formatCurrency($pair['amount']) }}</small>
                                            <p class="mb-0 small text-muted">{{ $pair['matched_at'] }}</p>
                                        </div>
                                        <button wire:click="unmatchTransactions({{ $pair['id'] }})" 
                                            class="btn btn-sm btn-outline-danger">✕</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted p-3 mb-0">No matches yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <button wire:click="resetReconciliation" class="btn btn-secondary">
                ← Back
            </button>
            @if($isBalanced)
                <button wire:click="completeReconciliation" class="btn btn-success">
                    Complete Reconciliation ✓
                </button>
            @else
                <button class="btn btn-danger" disabled>
                    ✗ Cannot complete - Not balanced (Diff: {{ $this->formatCurrency(abs($stats['difference'] ?? 0)) }})
                </button>
            @endif
        </div>
    </div>
    @endif

    <!-- STEP 3: RESULTS -->
    @if($activeTab === 'results' && $selectedReconciliationId && $reconciliation)
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">✓ Reconciliation Complete</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Reconciliation Date</h6>
                            <h4>{{ $reconciliation->reconciliation_date->format('M d, Y') }}</h4>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Completed At</h6>
                            <h4>{{ $reconciliation->completed_at->format('M d, Y H:i') }}</h4>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-muted">Bank Balance</h6>
                            <h5>{{ $this->formatCurrency($reconciliation->bank_balance) }}</h5>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Book Balance</h6>
                            <h5>{{ $this->formatCurrency($reconciliation->book_balance) }}</h5>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Difference</h6>
                            <h5 class="text-success">{{ $this->formatCurrency($reconciliation->difference) }}</h5>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Matched Pairs</h6>
                            <h4>{{ $stats['matched_count'] ?? 0 }}</h4>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Matched Amount</h6>
                            <h4>{{ $this->formatCurrency($stats['matched_amount'] ?? 0) }}</h4>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button wire:click="resetReconciliation" class="btn btn-primary">
                            Start New Reconciliation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .border-left-info {
            border-left: 4px solid #0dcaf0;
        }
        .border-left-success {
            border-left: 4px solid #198754;
        }
        .border-left-warning {
            border-left: 4px solid #ffc107;
        }
        .border-left-danger {
            border-left: 4px solid #dc3545;
        }
        .nav-link {
            cursor: pointer;
        }
        .nav-link.disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .list-group-item {
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</div>
