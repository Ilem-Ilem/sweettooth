<?php

namespace App\Services;

use App\Models\GlAccount;
use App\Models\GlEntry;
use App\Models\AccountingPeriod;
use App\Models\BranchAccountingDefault;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\AccountTransfer;
use App\Models\ExpenseClaim;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\ProductionOrder;
use App\Models\InventoryAdjustment;
use App\Models\Payroll;
use App\Models\PurchasePayment;
use App\Models\TaxPayment;
use App\Models\FixedAsset;
use App\Models\AssetDepreciation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Exception;
use App\Services\NotificationRecipientService;
use App\Notifications\GlPostingDraftReadyNotification;

class GlPostingService
{
    protected ?AccountingPeriod $currentPeriod = null;
    protected array $accountCache = [];

    /**
     * Get current accounting period
     */
    public function getCurrentPeriod(): ?AccountingPeriod
    {
        if ($this->currentPeriod) {
            return $this->currentPeriod;
        }

        return AccountingPeriod::current()->first();
    }

    /**
     * Get GL account by account number (cached)
     */
    protected function getGlAccount(string $accountNumber, ?string $branchId = null): GlAccount
    {
        $branchId = $branchId ?? current_branch_id();
        $cacheKey = ($branchId ?: 'global') . ':' . $accountNumber;

        if (!isset($this->accountCache[$cacheKey])) {
            $account = GlAccount::forBranch($branchId)
                ->where('account_number', $accountNumber)
                ->first();
            if (!$account) {
                $defaults = $this->defaultAccountDefinition($accountNumber);
                $account = GlAccount::create([
                    'branch_id' => $branchId,
                    'account_number' => $accountNumber,
                    'account_name' => $defaults['name'],
                    'account_type' => $defaults['type'],
                    'normal_balance' => $defaults['normal_balance'],
                    'is_header' => false,
                    'is_active' => true,
                    'allow_manual_entry' => true,
                ]);
            }
            $this->accountCache[$cacheKey] = $account;
        }

        return $this->accountCache[$cacheKey];
    }

    protected function resolveDefaultAccount(string $key, ?string $branchId = null): GlAccount
    {
        $branchId = $branchId ?? current_branch_id();
        $cacheKey = ($branchId ?: 'global') . ':default:' . $key;

        if (! isset($this->accountCache[$cacheKey])) {
            $default = BranchAccountingDefault::forBranch($branchId)
                ->where('key', $key)
                ->with('glAccount')
                ->first();

            if (! $default || ! $default->glAccount) {
                throw new Exception("Missing accounting default [{$key}] for branch {$branchId}.");
            }

            $this->accountCache[$cacheKey] = $default->glAccount;
        }

        return $this->accountCache[$cacheKey];
    }

    protected function createEntry(array $data, ?string $branchId = null): GlEntry
    {
        $branchId = $data['branch_id'] ?? $branchId;

        if (! $branchId && ! empty($data['reference_type']) && ! empty($data['reference_id'])) {
            $modelClass = $data['reference_type'];
            if (class_exists($modelClass)) {
                $model = $modelClass::find($data['reference_id']);
                if ($model && isset($model->branch_id)) {
                    $branchId = $model->branch_id;
                }
            }
        }

        $data['branch_id'] = $branchId ?? current_branch_id();

        return GlEntry::create($data);
    }

    /**
     * Default account definitions for auto-create fallback.
     */
    protected function defaultAccountDefinition(string $accountNumber): array
    {
        return match ($accountNumber) {
            '1100' => ['name' => 'Accounts Receivable', 'type' => 'asset', 'normal_balance' => 'debit'],
            '1200' => ['name' => 'Inventory', 'type' => 'asset', 'normal_balance' => 'debit'],
            '1220' => ['name' => 'Inventory Asset', 'type' => 'asset', 'normal_balance' => 'debit'],
            '2010' => ['name' => 'Accounts Payable', 'type' => 'liability', 'normal_balance' => 'credit'],
            '2020' => ['name' => 'Sales Tax Payable', 'type' => 'tax', 'normal_balance' => 'credit'],
            '4010' => ['name' => 'Sales Revenue', 'type' => 'revenue', 'normal_balance' => 'credit'],
            '5010' => ['name' => 'Cost of Goods Sold', 'type' => 'cost_of_goods_sold', 'normal_balance' => 'debit'],
            default => ['name' => "GL {$accountNumber}", 'type' => 'asset', 'normal_balance' => 'debit'],
        };
    }

    protected function notifyDraftReady(string $referenceType, int $referenceId, ?string $branchId, ?string $referenceNumber = null): void
    {
        $hasDrafts = GlEntry::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('status', 'draft')
            ->exists();

        if (! $hasDrafts) {
            return;
        }

        $recipients = app(NotificationRecipientService::class)
            ->usersForRoles(config('notifications.roles.accounting', []), $branchId);

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send(
            $recipients,
            new GlPostingDraftReadyNotification($referenceType, $referenceId, $referenceNumber, $branchId)
        );
    }

    /**
     * Idempotency guard for posting methods.
     */
    protected function alreadyPosted(string $referenceType, int $referenceId, array $entryTypes): bool
    {
        return GlEntry::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->whereIn('entry_type', $entryTypes)
            ->where('status', 'posted')
            ->exists();
    }

    /**
     * Post a sale transaction (Revenue + COGS)
     * Entry A: Debit Cash/Bank, Credit Sales Revenue
     * Entry B: Debit COGS, Credit Inventory
     */
    public function postSaleTransaction(Sale $sale): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(Sale::class, (int) $sale->id, ['sale', 'sale_cogs', 'sale_tax'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $sale->branch_id;
            $department = $this->getDepartmentForSale($sale);

            // Entry A: Record Sale Revenue
            // Debit: Accounts Receivable, Credit: Sales Revenue
            $revenueAccount = $this->getRevenueAccountForDepartment($department, $branchId);
            $receivableAccount = $this->getReceivableAccountForDepartment($department, $branchId);
            $this->createEntry([
                'gl_account_id' => $receivableAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sale',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reference_number' => $sale->reference_number ?? "SAL-{$sale->id}",
                'description' => "Sale Transaction - {$sale->reference_number}",
                'debit' => $sale->total,
                'credit' => 0,
                'entry_date' => $sale->sale_time,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ]);

            // Credit to Revenue
            $this->createEntry([
                'gl_account_id' => $revenueAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sale',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reference_number' => $sale->reference_number ?? "SAL-{$sale->id}",
                'description' => "Sale Revenue - {$sale->reference_number}",
                'debit' => 0,
                'credit' => $sale->subtotal,
                'entry_date' => $sale->sale_time,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ]);

            // Entry B: Record COGS
            // Debit: COGS, Credit: Inventory
            $cogsAccount = $this->resolveDefaultAccount('cogs', $branchId);
            $inventoryAccount = $this->resolveDefaultAccount('inventory_asset', $branchId);

            $totalCogs = $sale->saleItems->sum(function ($item) {
                if (! empty($item->line_cost)) {
                    return (float) $item->line_cost;
                }

                if (! empty($item->unit_cost)) {
                    return (float) $item->unit_cost * (float) ($item->quantity ?? 0);
                }

                return 0;
            });

            if ($totalCogs > 0) {
            $this->createEntry([
                    'gl_account_id' => $cogsAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale_cogs',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->reference_number ?? "SAL-{$sale->id}",
                    'description' => "COGS - {$sale->reference_number}",
                    'debit' => $totalCogs,
                    'credit' => 0,
                    'entry_date' => $sale->sale_time,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            $this->createEntry([
                    'gl_account_id' => $inventoryAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale_cogs',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->reference_number ?? "SAL-{$sale->id}",
                    'description' => "Inventory Reduction - {$sale->reference_number}",
                    'debit' => 0,
                    'credit' => $totalCogs,
                    'entry_date' => $sale->sale_time,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            }

            // Entry C: Record Sales Tax (if applicable)
            if ($sale->tax > 0) {
                $taxAccount = $this->getTaxAccountForDepartment($department, $branchId);
            $this->createEntry([
                    'gl_account_id' => $receivableAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale_tax',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->reference_number ?? "SAL-{$sale->id}",
                    'description' => "Sales Tax - {$sale->reference_number}",
                    'debit' => $sale->tax,
                    'credit' => 0,
                    'entry_date' => $sale->sale_time,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            $this->createEntry([
                    'gl_account_id' => $taxAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale_tax',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->reference_number ?? "SAL-{$sale->id}",
                    'description' => "Sales Tax Liability - {$sale->reference_number}",
                    'debit' => 0,
                    'credit' => $sale->tax,
                    'entry_date' => $sale->sale_time,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            }

            DB::commit();
            $this->notifyDraftReady(Sale::class, (int) $sale->id, $sale->branch_id, $sale->reference_number ?? "SAL-{$sale->id}");
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Sale Transaction', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post a purchase transaction
     * Debit: Inventory, Credit: Accounts Payable
     */
    public function postPurchaseTransaction(Purchase $purchase): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(Purchase::class, (int) $purchase->id, ['purchase'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $purchase->branch_id;
            $inventoryAccount = $this->resolveDefaultAccount('inventory', $branchId);
            $apAccount = $this->resolveDefaultAccount('accounts_payable', $branchId);

            $landingCost = $purchase->total_fob_ngn + ($purchase->other_costs ?? 0);

            // Debit: Inventory
            $this->createEntry([
                'gl_account_id' => $inventoryAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'purchase',
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'reference_number' => $purchase->reference_number ?? "PUR-{$purchase->id}",
                'description' => "Purchase - {$purchase->reference_number}",
                'debit' => $landingCost,
                'credit' => 0,
                'entry_date' => $purchase->created_at,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: Accounts Payable
            $this->createEntry([
                'gl_account_id' => $apAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'purchase',
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'reference_number' => $purchase->reference_number ?? "PUR-{$purchase->id}",
                'description' => "Accounts Payable - {$purchase->reference_number}",
                'debit' => 0,
                'credit' => $landingCost,
                'entry_date' => $purchase->created_at,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            $this->notifyDraftReady(Purchase::class, (int) $purchase->id, $purchase->branch_id, $purchase->reference_number ?? "PUR-{$purchase->id}");
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Purchase Transaction', [
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post a payment transaction
     * Debit: Cash/Bank, Credit: Accounts Receivable
     */
    public function postPaymentTransaction(Payment $payment): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(Payment::class, (int) $payment->id, ['payment'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $department = $payment->sale?->department ?? $payment->sale?->branch?->departments()?->first();
            $receivableAccount = $this->getReceivableAccountForDepartment($department);
            $cashAccount = null;
            if (strtolower($payment->payment_method) === 'pos') {
                $cashAccount = $this->getCashAccountForPaymentMethod('pos', $payment->branch_id);
            } else {
                $cashAccount = $payment->bankAccount?->glAccount
                    ?? ($department?->cashAccount)
                    ?? $this->getCashAccountForPaymentMethod($payment->payment_method, $payment->branch_id);
            }

            // Debit: Cash/Bank
            $this->createEntry([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "PAY-{$payment->id}",
                'description' => "Payment received - {$payment->reference_number}",
                'debit' => $payment->amount,
                'credit' => 0,
                'entry_date' => $payment->payment_time,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: Accounts Receivable
            $this->createEntry([
                'gl_account_id' => $receivableAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "PAY-{$payment->id}",
                'description' => "AR reduction - {$payment->reference_number}",
                'debit' => 0,
                'credit' => $payment->amount,
                'entry_date' => $payment->payment_time,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            $this->notifyDraftReady(Payment::class, (int) $payment->id, $payment->branch_id, $payment->reference_number ?? "PAY-{$payment->id}");
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Payment Transaction', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post inventory adjustment (damage, shrinkage, etc.)
     */
    public function postInventoryAdjustment(StockMovement $movement): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(StockMovement::class, (int) $movement->id, ['adjustment'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $reason = $movement->adjustment_reason;
            if ($movement->type === 'damaged' && ! $reason) {
                $reason = 'damage';
            }

            if ($movement->type !== 'adjustment' && $movement->type !== 'damaged') {
                return true; // Not an adjustment that needs GL entry
            }

            if (! $reason) {
                return true; // No explicit adjustment reason to post
            }

            $inventoryAccount = $this->resolveDefaultAccount('inventory_asset', $movement->branch_id);
            $adjustmentAccount = $this->getAdjustmentAccountForType($reason, $movement->branch_id);

            $amount = $movement->cost_impact ?? ($movement->quantity * ($movement->unit_cost ?? 0));
            $amount = abs((float) $amount);

            $isIncrease = (float) $movement->quantity_after > (float) $movement->quantity_before;

            if ($isIncrease && $movement->type === 'adjustment') {
                // Debit Inventory, Credit Adjustment (inventory gain)
            $this->createEntry([
                    'gl_account_id' => $inventoryAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => StockMovement::class,
                    'reference_id' => $movement->id,
                    'reference_number' => "ADJ-{$movement->id}",
                    'description' => "Inventory Increase - {$movement->quantity} units",
                    'debit' => $amount,
                    'credit' => 0,
                    'entry_date' => $movement->movement_date ?? $movement->created_at,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            $this->createEntry([
                    'gl_account_id' => $adjustmentAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => StockMovement::class,
                    'reference_id' => $movement->id,
                    'reference_number' => "ADJ-{$movement->id}",
                    'description' => ucfirst($reason) . " Adjustment - {$movement->quantity} units",
                    'debit' => 0,
                    'credit' => $amount,
                    'entry_date' => $movement->movement_date ?? $movement->created_at,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            } else {
                // Debit Loss/Adjustment, Credit Inventory
            $this->createEntry([
                    'gl_account_id' => $adjustmentAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => StockMovement::class,
                    'reference_id' => $movement->id,
                    'reference_number' => "ADJ-{$movement->id}",
                    'description' => ucfirst($reason) . " Loss - {$movement->quantity} units",
                    'debit' => $amount,
                    'credit' => 0,
                    'entry_date' => $movement->movement_date ?? $movement->created_at,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            $this->createEntry([
                    'gl_account_id' => $inventoryAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => StockMovement::class,
                    'reference_id' => $movement->id,
                    'reference_number' => "ADJ-{$movement->id}",
                    'description' => "Inventory Reduction - {$movement->quantity} units",
                    'debit' => 0,
                    'credit' => $amount,
                    'entry_date' => $movement->movement_date ?? $movement->created_at,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ]);
            }

            DB::commit();
            $this->notifyDraftReady(StockMovement::class, (int) $movement->id, $movement->branch_id, "ADJ-{$movement->id}");
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Inventory Adjustment', [
                'movement_id' => $movement->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Validate and post a manual GL entry
     */
    public function postManualEntry(GlEntry $entry): bool
    {
        // Validate entry is balanced
        if (floatval($entry->debit) !== floatval($entry->credit)) {
            throw new Exception('Journal entry must be balanced (debit = credit)');
        }

        // Validate GL account exists and is active
        $account = GlAccount::find($entry->gl_account_id);
        if (!$account || !$account->is_active) {
            throw new Exception('GL account is not active or does not exist');
        }

        // Validate period is open
        $period = AccountingPeriod::find($entry->accounting_period_id);
        if (!$period || $period->status !== 'open') {
            throw new Exception('Accounting period is not open');
        }

        return $entry->post(auth()->id());
    }

    /**
     * Post an account transfer transaction
     * Debit: To Bank Account GL, Credit: From Bank Account GL
     */
    public function postAccountTransfer(AccountTransfer $transfer): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(AccountTransfer::class, (int) $transfer->id, ['transfer'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $fromAccount = $transfer->fromBankAccount;
            $toAccount = $transfer->toBankAccount;

            if (!$fromAccount || !$toAccount) {
                throw new Exception('Bank accounts not found for transfer');
            }

            $fromGlAccount = $fromAccount->glAccount
                ?? $this->resolveDefaultAccount('bank_main', $transfer->branch_id);
            $toGlAccount = $toAccount->glAccount
                ?? $this->resolveDefaultAccount('bank_main', $transfer->branch_id);

            // Debit: To Bank Account
            $this->createEntry([
                'gl_account_id' => $toGlAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'transfer',
                'reference_type' => AccountTransfer::class,
                'reference_id' => $transfer->id,
                'reference_number' => "TRF-{$transfer->id}",
                'description' => "Transfer from {$fromAccount->name} to {$toAccount->name}",
                'debit' => $transfer->amount,
                'credit' => 0,
                'entry_date' => $transfer->transfer_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: From Bank Account
            $this->createEntry([
                'gl_account_id' => $fromGlAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'transfer',
                'reference_type' => AccountTransfer::class,
                'reference_id' => $transfer->id,
                'reference_number' => "TRF-{$transfer->id}",
                'description' => "Transfer from {$fromAccount->name} to {$toAccount->name}",
                'debit' => 0,
                'credit' => $transfer->amount,
                'entry_date' => $transfer->transfer_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            $this->notifyDraftReady(PurchasePayment::class, (int) $payment->id, $payment->branch_id, $payment->reference_number ?? "PPY-{$payment->id}");
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Account Transfer', [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post an expense claim transaction
     * Debit: Expense Accounts, Credit: Cash/Bank
     */
    public function postExpenseClaim(ExpenseClaim $claim): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(ExpenseClaim::class, (int) $claim->id, ['expense_claim'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            // Determine payment account
            $cashAccount = $claim->paidViaBankAccount?->glAccount
                ?? $this->resolveDefaultAccount('cash_on_hand', $claim->branch_id);

            // Post each expense item to its respective expense account
            foreach ($claim->items as $item) {
                $expenseAccount = $item->glAccount ?? $this->getExpenseAccountForCategory($item->category, $claim->branch_id);

                // Debit: Expense Account
            $this->createEntry([
                    'gl_account_id' => $expenseAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'expense_claim',
                    'reference_type' => ExpenseClaim::class,
                    'reference_id' => $claim->id,
                    'reference_number' => "EXP-{$claim->id}",
                    'description' => "Expense Claim - {$item->description}",
                    'debit' => $item->amount,
                    'credit' => 0,
                    'entry_date' => $claim->claim_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            }

            // Credit: Cash/Bank for total
            $this->createEntry([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'expense_claim',
                'reference_type' => ExpenseClaim::class,
                'reference_id' => $claim->id,
                'reference_number' => "EXP-{$claim->id}",
                'description' => "Expense Claim Payment - {$claim->employee?->name}",
                'debit' => 0,
                'credit' => $claim->total_amount,
                'entry_date' => $claim->claim_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Expense Claim', [
                'claim_id' => $claim->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post a credit note transaction (sales return/refund)
     * Debit: Sales Revenue, Credit: Customer/Cash
     * If inventory returned: Debit: Inventory, Credit: COGS
     */
    public function postCreditNote(CreditNote $creditNote): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(CreditNote::class, (int) $creditNote->id, ['credit_note', 'credit_note_cogs'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $creditNote->branch_id;
            $revenueAccount = $this->resolveDefaultAccount('sales_revenue', $branchId);
            $receivableAccount = $this->resolveDefaultAccount('accounts_receivable', $branchId);
            $inventoryAccount = $this->resolveDefaultAccount('inventory_asset', $branchId);
            $cogsAccount = $this->resolveDefaultAccount('cogs', $branchId);

            // Debit: Sales Revenue (reducing revenue)
            $this->createEntry([
                'gl_account_id' => $revenueAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'credit_note',
                'reference_type' => CreditNote::class,
                'reference_id' => $creditNote->id,
                'reference_number' => $creditNote->credit_note_number,
                'description' => "Credit Note - {$creditNote->credit_note_number}",
                'debit' => $creditNote->subtotal,
                'credit' => 0,
                'entry_date' => $creditNote->credit_note_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: Accounts Receivable (reducing what customer owes)
            $this->createEntry([
                'gl_account_id' => $receivableAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'credit_note',
                'reference_type' => CreditNote::class,
                'reference_id' => $creditNote->id,
                'reference_number' => $creditNote->credit_note_number,
                'description' => "Credit Note - {$creditNote->credit_note_number}",
                'debit' => 0,
                'credit' => $creditNote->total,
                'entry_date' => $creditNote->credit_note_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Reverse COGS if items are returned to inventory
            $totalCogs = $creditNote->items->sum(function ($item) {
                return $item->quantity * ($item->product?->cost_price ?? 0);
            });

            if ($totalCogs > 0) {
                // Debit: Inventory (adding back)
            $this->createEntry([
                    'gl_account_id' => $inventoryAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'credit_note_cogs',
                    'reference_type' => CreditNote::class,
                    'reference_id' => $creditNote->id,
                    'reference_number' => $creditNote->credit_note_number,
                    'description' => "Credit Note Inventory Return - {$creditNote->credit_note_number}",
                    'debit' => $totalCogs,
                    'credit' => 0,
                    'entry_date' => $creditNote->credit_note_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());

                // Credit: COGS (reducing cost)
            $this->createEntry([
                    'gl_account_id' => $cogsAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'credit_note_cogs',
                    'reference_type' => CreditNote::class,
                    'reference_id' => $creditNote->id,
                    'reference_number' => $creditNote->credit_note_number,
                    'description' => "Credit Note COGS Reversal - {$creditNote->credit_note_number}",
                    'debit' => 0,
                    'credit' => $totalCogs,
                    'entry_date' => $creditNote->credit_note_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Credit Note', [
                'credit_note_id' => $creditNote->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post a debit note transaction (purchase return)
     * Debit: Accounts Payable, Credit: Inventory
     */
    public function postDebitNote(DebitNote $debitNote): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(DebitNote::class, (int) $debitNote->id, ['debit_note'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $debitNote->branch_id;
            $apAccount = $this->resolveDefaultAccount('accounts_payable', $branchId);
            $inventoryAccount = $this->resolveDefaultAccount('inventory', $branchId);

            // Debit: Accounts Payable (reducing what we owe)
            $this->createEntry([
                'gl_account_id' => $apAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'debit_note',
                'reference_type' => DebitNote::class,
                'reference_id' => $debitNote->id,
                'reference_number' => $debitNote->debit_note_number,
                'description' => "Debit Note - {$debitNote->debit_note_number}",
                'debit' => $debitNote->total,
                'credit' => 0,
                'entry_date' => $debitNote->debit_note_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: Inventory (reducing inventory)
            $this->createEntry([
                'gl_account_id' => $inventoryAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'debit_note',
                'reference_type' => DebitNote::class,
                'reference_id' => $debitNote->id,
                'reference_number' => $debitNote->debit_note_number,
                'description' => "Debit Note Inventory Return - {$debitNote->debit_note_number}",
                'debit' => 0,
                'credit' => $debitNote->subtotal,
                'entry_date' => $debitNote->debit_note_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Debit Note', [
                'debit_note_id' => $debitNote->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post a production order transaction
     * Debit: Finished Goods Inventory, Credit: Raw Materials + Labor + Overhead
     */
    public function postProductionOrder(ProductionOrder $order): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(ProductionOrder::class, (int) $order->id, ['production'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $order->branch_id;
            $finishedGoodsAccount = $this->resolveDefaultAccount('inventory_finished_goods', $branchId);
            $rawMaterialsAccount = $this->resolveDefaultAccount('inventory_raw_materials', $branchId);
            $wipAccount = $this->resolveDefaultAccount('inventory_wip', $branchId);
            $laborAccount = $this->resolveDefaultAccount('labor_direct', $branchId);
            $overheadAccount = $this->resolveDefaultAccount('overhead_manufacturing', $branchId);

            // Debit: Finished Goods Inventory
            $this->createEntry([
                'gl_account_id' => $finishedGoodsAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'production',
                'reference_type' => ProductionOrder::class,
                'reference_id' => $order->id,
                'reference_number' => $order->order_number,
                'description' => "Production Output - {$order->order_number}",
                'debit' => $order->total_cost,
                'credit' => 0,
                'entry_date' => $order->completed_date ?? now(),
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: Raw Materials (material cost)
            if ($order->total_material_cost > 0) {
            $this->createEntry([
                    'gl_account_id' => $rawMaterialsAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'production',
                    'reference_type' => ProductionOrder::class,
                    'reference_id' => $order->id,
                    'reference_number' => $order->order_number,
                    'description' => "Production Materials Used - {$order->order_number}",
                    'debit' => 0,
                    'credit' => $order->total_material_cost,
                    'entry_date' => $order->completed_date ?? now(),
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            }

            // Credit: Labor (if applicable)
            if ($order->total_labor_cost > 0) {
                try {
            $this->createEntry([
                        'gl_account_id' => $laborAccount->id,
                        'accounting_period_id' => $period->id,
                        'entry_type' => 'production',
                        'reference_type' => ProductionOrder::class,
                        'reference_id' => $order->id,
                        'reference_number' => $order->order_number,
                        'description' => "Production Labor - {$order->order_number}",
                        'debit' => 0,
                        'credit' => $order->total_labor_cost,
                        'entry_date' => $order->completed_date ?? now(),
                        'status' => 'draft',
                        'entered_by_id' => auth()->id(),
                    ])->post(auth()->id());
                } catch (Exception $e) {
                    // If labor account doesn't exist, add to materials
                    \Log::warning('Labor account not found, adding to materials', ['order_id' => $order->id]);
                }
            }

            // Credit: Overhead (if applicable)
            if ($order->total_overhead_cost > 0) {
                try {
            $this->createEntry([
                        'gl_account_id' => $overheadAccount->id,
                        'accounting_period_id' => $period->id,
                        'entry_type' => 'production',
                        'reference_type' => ProductionOrder::class,
                        'reference_id' => $order->id,
                        'reference_number' => $order->order_number,
                        'description' => "Production Overhead - {$order->order_number}",
                        'debit' => 0,
                        'credit' => $order->total_overhead_cost,
                        'entry_date' => $order->completed_date ?? now(),
                        'status' => 'draft',
                        'entered_by_id' => auth()->id(),
                    ])->post(auth()->id());
                } catch (Exception $e) {
                    \Log::warning('Overhead account not found', ['order_id' => $order->id]);
                }
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Production Order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post an inventory adjustment transaction
     */
    public function postInventoryAdjustmentEntry(InventoryAdjustment $adjustment): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(InventoryAdjustment::class, (int) $adjustment->id, ['adjustment'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $inventoryAccount = $this->resolveDefaultAccount('inventory_asset', $adjustment->branch_id);
            $adjustmentAccount = $this->getAdjustmentAccountForType($adjustment->type, $adjustment->branch_id);

            $amount = abs($adjustment->cost_impact);

            if ($adjustment->isDecrease()) {
                // Inventory decreased: Debit Adjustment Account, Credit Inventory
            $this->createEntry([
                    'gl_account_id' => $adjustmentAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => InventoryAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'reference_number' => $adjustment->adjustment_number,
                    'description' => ucfirst($adjustment->type) . " Adjustment - {$adjustment->adjustment_number}",
                    'debit' => $amount,
                    'credit' => 0,
                    'entry_date' => $adjustment->adjustment_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            $this->createEntry([
                    'gl_account_id' => $inventoryAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => InventoryAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'reference_number' => $adjustment->adjustment_number,
                    'description' => "Inventory Reduction - {$adjustment->adjustment_number}",
                    'debit' => 0,
                    'credit' => $amount,
                    'entry_date' => $adjustment->adjustment_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            } else {
                // Inventory increased: Debit Inventory, Credit Adjustment Account
            $this->createEntry([
                    'gl_account_id' => $inventoryAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => InventoryAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'reference_number' => $adjustment->adjustment_number,
                    'description' => "Inventory Increase - {$adjustment->adjustment_number}",
                    'debit' => $amount,
                    'credit' => 0,
                    'entry_date' => $adjustment->adjustment_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            $this->createEntry([
                    'gl_account_id' => $adjustmentAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'adjustment',
                    'reference_type' => InventoryAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'reference_number' => $adjustment->adjustment_number,
                    'description' => ucfirst($adjustment->type) . " Adjustment - {$adjustment->adjustment_number}",
                    'debit' => 0,
                    'credit' => $amount,
                    'entry_date' => $adjustment->adjustment_date,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Inventory Adjustment', [
                'adjustment_id' => $adjustment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get expense account based on category
     */
    protected function getExpenseAccountForCategory(string $category, ?string $branchId = null): GlAccount
    {
        $mapping = [
            'travel' => 'expense_travel',
            'meals' => 'expense_meals',
            'supplies' => 'expense_supplies',
            'communication' => 'expense_communication',
            'accommodation' => 'expense_accommodation',
            'professional' => 'expense_professional',
            'other' => 'expense_other',
        ];

        $key = $mapping[$category] ?? 'expense_other';

        return $this->resolveDefaultAccount($key, $branchId);
    }

    /**
     * Get adjustment account based on adjustment type
     */
    protected function getAdjustmentAccountForType(string $type, ?string $branchId = null): GlAccount
    {
        $mapping = [
            'damage' => 'adjustment_damage',
            'shrinkage' => 'adjustment_shrinkage',
            'write_off' => 'adjustment_write_off',
            'adjustment' => 'adjustment_inventory',
            'count' => 'adjustment_count',
            'transfer' => 'adjustment_transfer',
            'production' => 'adjustment_production',
        ];

        $key = $mapping[$type] ?? 'adjustment_inventory';

        return $this->resolveDefaultAccount($key, $branchId);
    }

    /**
     * Get appropriate cash/bank account based on payment method
     */
    protected function getCashAccountForPaymentMethod(string $paymentMethod, ?string $branchId = null): GlAccount
    {
        $mapping = [
            'cash' => 'cash_on_hand',
            'transfer' => 'bank_main',
            'bank_transfer' => 'bank_main',
            'card' => 'bank_main',
            'pos' => 'pos_clearing',
            'cheque' => 'bank_main',
        ];

        $key = $mapping[$paymentMethod] ?? 'cash_on_hand';

        return $this->resolveDefaultAccount($key, $branchId);
    }

    /**
     * Post payroll accrual (approve payroll)
     * Debit: Salaries Expense, Credit: Payroll Payable
     */
    public function postPayrollAccrual(Payroll $payroll): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(Payroll::class, (int) $payroll->id, ['payroll_accrual'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (! $period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $payroll->branch_id;
            $expenseAccount = $this->resolveDefaultAccount('payroll_expense', $branchId);
            $payableAccount = $this->resolveDefaultAccount('payroll_payable', $branchId);
            $taxPayableAccount = $this->resolveDefaultAccount('payroll_tax_payable', $branchId);
            $deductionPayableAccount = $this->resolveDefaultAccount('payroll_deduction_payable', $branchId);

            $gross = (float) $payroll->gross_salary;
            $net = (float) $payroll->net_salary;
            $tax = (float) $payroll->tax_deductions;
            $other = (float) $payroll->other_deductions;
            $this->createEntry([
                'gl_account_id' => $expenseAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payroll_accrual',
                'reference_type' => Payroll::class,
                'reference_id' => $payroll->id,
                'reference_number' => "PAYROLL-{$payroll->id}",
                'description' => "Payroll Accrual - {$payroll->employee?->name}",
                'debit' => $gross,
                'credit' => 0,
                'entry_date' => $payroll->pay_period_end,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());
            $this->createEntry([
                'gl_account_id' => $payableAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payroll_accrual',
                'reference_type' => Payroll::class,
                'reference_id' => $payroll->id,
                'reference_number' => "PAYROLL-{$payroll->id}",
                'description' => "Payroll Payable - {$payroll->employee?->name}",
                'debit' => 0,
                'credit' => $net,
                'entry_date' => $payroll->pay_period_end,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            if ($tax > 0) {
            $this->createEntry([
                    'gl_account_id' => $taxPayableAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'payroll_accrual',
                    'reference_type' => Payroll::class,
                    'reference_id' => $payroll->id,
                    'reference_number' => "PAYROLL-{$payroll->id}",
                    'description' => "Payroll Tax Payable - {$payroll->employee?->name}",
                    'debit' => 0,
                    'credit' => $tax,
                    'entry_date' => $payroll->pay_period_end,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            }

            if ($other > 0) {
            $this->createEntry([
                    'gl_account_id' => $deductionPayableAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'payroll_accrual',
                    'reference_type' => Payroll::class,
                    'reference_id' => $payroll->id,
                    'reference_number' => "PAYROLL-{$payroll->id}",
                    'description' => "Other Deductions Payable - {$payroll->employee?->name}",
                    'debit' => 0,
                    'credit' => $other,
                    'entry_date' => $payroll->pay_period_end,
                    'status' => 'draft',
                    'entered_by_id' => auth()->id(),
                ])->post(auth()->id());
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Payroll Accrual', [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post payroll payment
     * Debit: Payroll Payable, Credit: Cash/Bank
     */
    public function postPayrollPayment(Payroll $payroll): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(Payroll::class, (int) $payroll->id, ['payroll_payment'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (! $period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $payroll->branch_id;
            $payableAccount = $this->resolveDefaultAccount('payroll_payable', $branchId);
            $cashAccount = $payroll->bankAccount?->glAccount
                ?? $this->resolveDefaultAccount('bank_main', $branchId);

            $amount = (float) $payroll->net_salary;
            $this->createEntry([
                'gl_account_id' => $payableAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payroll_payment',
                'reference_type' => Payroll::class,
                'reference_id' => $payroll->id,
                'reference_number' => "PAYROLL-{$payroll->id}",
                'description' => "Payroll Payment - {$payroll->employee?->name}",
                'debit' => $amount,
                'credit' => 0,
                'entry_date' => $payroll->payment_date ?? now(),
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());
            $this->createEntry([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payroll_payment',
                'reference_type' => Payroll::class,
                'reference_id' => $payroll->id,
                'reference_number' => "PAYROLL-{$payroll->id}",
                'description' => "Payroll Cash/Bank - {$payroll->employee?->name}",
                'debit' => 0,
                'credit' => $amount,
                'entry_date' => $payroll->payment_date ?? now(),
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Payroll Payment', [
                'payroll_id' => $payroll->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post purchase payment (settle AP)
     * Debit: Accounts Payable, Credit: Cash/Bank
     */
    public function postPurchasePayment(PurchasePayment $payment): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(PurchasePayment::class, (int) $payment->id, ['purchase_payment'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (! $period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $payment->branch_id;
            $apAccount = $this->resolveDefaultAccount('accounts_payable', $branchId);
            $cashAccount = $payment->bankAccount?->glAccount
                ?? $this->resolveDefaultAccount('bank_main', $branchId);
            $this->createEntry([
                'gl_account_id' => $apAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'purchase_payment',
                'reference_type' => PurchasePayment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "PPAY-{$payment->id}",
                'description' => "Purchase Payment - {$payment->purchase?->purchase_number}",
                'debit' => $payment->amount,
                'credit' => 0,
                'entry_date' => $payment->payment_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());
            $this->createEntry([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'purchase_payment',
                'reference_type' => PurchasePayment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "PPAY-{$payment->id}",
                'description' => "Purchase Payment Cash/Bank - {$payment->purchase?->purchase_number}",
                'debit' => 0,
                'credit' => $payment->amount,
                'entry_date' => $payment->payment_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Purchase Payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post tax payment
     * Debit: Tax Payable, Credit: Cash/Bank
     */
    public function postTaxPayment(TaxPayment $payment): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(TaxPayment::class, (int) $payment->id, ['tax_payment'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (! $period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $payment->branch_id;
            $taxAccount = $this->resolveDefaultAccount('tax_payable', $branchId);
            $cashAccount = $payment->bankAccount?->glAccount
                ?? $this->resolveDefaultAccount('bank_main', $branchId);
            $this->createEntry([
                'gl_account_id' => $taxAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'tax_payment',
                'reference_type' => TaxPayment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "TAX-{$payment->id}",
                'description' => "Tax Payment - {$payment->tax_type}",
                'debit' => $payment->amount,
                'credit' => 0,
                'entry_date' => $payment->payment_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());
            $this->createEntry([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'tax_payment',
                'reference_type' => TaxPayment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "TAX-{$payment->id}",
                'description' => "Tax Payment Cash/Bank - {$payment->tax_type}",
                'debit' => 0,
                'credit' => $payment->amount,
                'entry_date' => $payment->payment_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Tax Payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post fixed asset acquisition
     * Debit: Fixed Assets, Credit: Cash/Bank or Accounts Payable
     */
    public function postFixedAssetAcquisition(FixedAsset $asset): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(FixedAsset::class, (int) $asset->id, ['asset_acquisition'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (! $period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $asset->branch_id;
            $assetAccount = $this->resolveDefaultAccount('fixed_asset', $branchId);
            $creditAccount = $asset->funding_source === 'ap'
                ? $this->resolveDefaultAccount('accounts_payable', $branchId)
                : ($asset->bankAccount?->glAccount ?? $this->resolveDefaultAccount('bank_main', $branchId));
            $this->createEntry([
                'gl_account_id' => $assetAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'asset_acquisition',
                'reference_type' => FixedAsset::class,
                'reference_id' => $asset->id,
                'reference_number' => $asset->asset_tag ?? "ASSET-{$asset->id}",
                'description' => "Asset Acquisition - {$asset->asset_name}",
                'debit' => $asset->asset_cost,
                'credit' => 0,
                'entry_date' => $asset->acquisition_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());
            $this->createEntry([
                'gl_account_id' => $creditAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'asset_acquisition',
                'reference_type' => FixedAsset::class,
                'reference_id' => $asset->id,
                'reference_number' => $asset->asset_tag ?? "ASSET-{$asset->id}",
                'description' => "Asset Funding - {$asset->asset_name}",
                'debit' => 0,
                'credit' => $asset->asset_cost,
                'entry_date' => $asset->acquisition_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Asset Acquisition', [
                'asset_id' => $asset->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Post asset depreciation
     * Debit: Depreciation Expense, Credit: Accumulated Depreciation
     */
    public function postAssetDepreciation(AssetDepreciation $depreciation): bool
    {
        try {
            DB::beginTransaction();

            if ($this->alreadyPosted(AssetDepreciation::class, (int) $depreciation->id, ['asset_depreciation'])) {
                DB::commit();
                return true;
            }

            $period = $this->getCurrentPeriod();
            if (! $period) {
                throw new Exception('No open accounting period found');
            }

            $branchId = $depreciation->branch_id;
            $expenseAccount = $this->resolveDefaultAccount('depreciation_expense', $branchId);
            $accumAccount = $this->resolveDefaultAccount('accumulated_depreciation', $branchId);
            $this->createEntry([
                'gl_account_id' => $expenseAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'asset_depreciation',
                'reference_type' => AssetDepreciation::class,
                'reference_id' => $depreciation->id,
                'reference_number' => "DEP-{$depreciation->id}",
                'description' => "Depreciation - {$depreciation->asset?->asset_name}",
                'debit' => $depreciation->depreciation_amount,
                'credit' => 0,
                'entry_date' => $depreciation->depreciation_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());
            $this->createEntry([
                'gl_account_id' => $accumAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'asset_depreciation',
                'reference_type' => AssetDepreciation::class,
                'reference_id' => $depreciation->id,
                'reference_number' => "DEP-{$depreciation->id}",
                'description' => "Accumulated Depreciation - {$depreciation->asset?->asset_name}",
                'debit' => 0,
                'credit' => $depreciation->depreciation_amount,
                'entry_date' => $depreciation->depreciation_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('GL Posting Error - Asset Depreciation', [
                'depreciation_id' => $depreciation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Select a department for a sale, falling back to the branch default.
     */
    protected function getDepartmentForSale(Sale $sale)
    {
        if ($sale->department) {
            return $sale->department;
        }

        return $sale->branch?->departments()?->first();
    }

    /**
     * Department-specific revenue account with fallback to default.
     */
    protected function getRevenueAccountForDepartment($department, ?string $branchId = null): GlAccount
    {
        if ($department && $department->revenueAccount) {
            return $department->revenueAccount;
        }

        return $this->resolveDefaultAccount('sales_revenue', $branchId);
    }

    /**
     * Department-specific tax account with fallback to default.
     */
    protected function getTaxAccountForDepartment($department, ?string $branchId = null): GlAccount
    {
        if ($department && $department->taxAccount) {
            return $department->taxAccount;
        }

        return $this->resolveDefaultAccount('sales_tax_payable', $branchId);
    }

    /**
     * Department-specific receivable account with fallback to default.
     */
    protected function getReceivableAccountForDepartment($department, ?string $branchId = null): GlAccount
    {
        if ($department && $department->receivableAccount) {
            return $department->receivableAccount;
        }

        return $this->resolveDefaultAccount('accounts_receivable', $branchId);
    }

    /**
     * Cash/bank account for a sale using explicit bank account, department defaults,
     * or payment method mapping as last resort.
     */
    protected function getCashAccountForSale(Sale $sale, $department, string $paymentMethod): GlAccount
    {
        if ($sale->bankAccount && $sale->bankAccount->glAccount) {
            return $sale->bankAccount->glAccount;
        }

        if ($department && $department->cashAccount) {
            return $department->cashAccount;
        }

        return $this->getCashAccountForPaymentMethod($paymentMethod, $sale->branch_id);
    }
}
