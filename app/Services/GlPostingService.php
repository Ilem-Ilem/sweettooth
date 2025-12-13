<?php

namespace App\Services;

use App\Models\GlAccount;
use App\Models\GlEntry;
use App\Models\AccountingPeriod;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class GlPostingService
{
    protected ?AccountingPeriod $currentPeriod = null;

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
     * Post a sale transaction (Revenue + COGS)
     * Entry A: Debit Cash/Bank, Credit Sales Revenue
     * Entry B: Debit COGS, Credit Inventory
     */
    public function postSaleTransaction(Sale $sale): bool
    {
        try {
            DB::beginTransaction();

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            // Determine which cash/bank account was used
            $paymentMethod = $sale->payments->first()?->payment_method ?? 'cash';
            $cashAccountId = $this->getCashAccountForPaymentMethod($paymentMethod);

            // Entry A: Record Sale Revenue
            // Debit: Cash/Bank, Credit: Sales Revenue
            $revenueAccount = GlAccount::where('account_number', '4010')->firstOrFail();
            $cashAccount = GlAccount::find($cashAccountId);

            GlEntry::create([
                'gl_account_id' => $cashAccount->id,
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
            ])->post(auth()->id());

            // Credit to Revenue
            GlEntry::create([
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
            ])->post(auth()->id());

            // Entry B: Record COGS
            // Debit: COGS, Credit: Inventory
            $cogsAccount = GlAccount::where('account_number', '5010')->firstOrFail();
            $inventoryAccount = GlAccount::where('account_number', '1220')->firstOrFail();

            $totalCogs = $sale->saleItems->sum(function ($item) {
                return ($item->quantity ?? 0) * ($item->average_cost ?? 0);
            });

            if ($totalCogs > 0) {
                GlEntry::create([
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
                ])->post(auth()->id());

                GlEntry::create([
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
                ])->post(auth()->id());
            }

            // Entry C: Record Sales Tax (if applicable)
            if ($sale->tax > 0) {
                $taxAccount = GlAccount::where('account_number', '2020')->firstOrFail();

                GlEntry::create([
                    'gl_account_id' => $cashAccount->id,
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
                ])->post(auth()->id());

                GlEntry::create([
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
                ])->post(auth()->id());
            }

            DB::commit();
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

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $inventoryAccount = GlAccount::where('account_number', '1200')->firstOrFail();
            $apAccount = GlAccount::where('account_number', '2010')->firstOrFail();

            $landingCost = $purchase->total_fob_ngn + ($purchase->other_costs ?? 0);

            // Debit: Inventory
            GlEntry::create([
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
            GlEntry::create([
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
     * Debit: Accounts Payable, Credit: Cash/Bank
     */
    public function postPaymentTransaction(Payment $payment): bool
    {
        try {
            DB::beginTransaction();

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            $apAccount = GlAccount::where('account_number', '2010')->firstOrFail();
            $cashAccountId = $this->getCashAccountForPaymentMethod($payment->payment_method);
            $cashAccount = GlAccount::find($cashAccountId);

            // Debit: Accounts Payable
            GlEntry::create([
                'gl_account_id' => $apAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "PAY-{$payment->id}",
                'description' => "Payment - {$payment->reference_number}",
                'debit' => $payment->amount,
                'credit' => 0,
                'entry_date' => $payment->payment_date,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            // Credit: Cash/Bank
            GlEntry::create([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number ?? "PAY-{$payment->id}",
                'description' => "Cash Outflow - {$payment->reference_number}",
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

            $period = $this->getCurrentPeriod();
            if (!$period) {
                throw new Exception('No open accounting period found');
            }

            if ($movement->movement_type !== 'damage' && $movement->movement_type !== 'shrinkage') {
                return true; // Not an adjustment that needs GL entry
            }

            $inventoryAccount = GlAccount::where('account_number', '1220')->firstOrFail();
            $lossAccount = $movement->movement_type === 'damage'
                ? GlAccount::where('account_number', '5020')->firstOrFail()
                : GlAccount::where('account_number', '5030')->firstOrFail();

            $amount = $movement->quantity * ($movement->unit_cost ?? 0);

            // Debit: Loss, Credit: Inventory
            GlEntry::create([
                'gl_account_id' => $lossAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'adjustment',
                'reference_type' => StockMovement::class,
                'reference_id' => $movement->id,
                'reference_number' => "ADJ-{$movement->id}",
                'description' => ucfirst($movement->movement_type) . " Loss - {$movement->quantity} units",
                'debit' => $amount,
                'credit' => 0,
                'entry_date' => $movement->created_at,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            GlEntry::create([
                'gl_account_id' => $inventoryAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'adjustment',
                'reference_type' => StockMovement::class,
                'reference_id' => $movement->id,
                'reference_number' => "ADJ-{$movement->id}",
                'description' => "Inventory Reduction - {$movement->quantity} units",
                'debit' => 0,
                'credit' => $amount,
                'entry_date' => $movement->created_at,
                'status' => 'draft',
                'entered_by_id' => auth()->id(),
            ])->post(auth()->id());

            DB::commit();
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
     * Get appropriate cash account based on payment method
     */
    protected function getCashAccountForPaymentMethod(string $paymentMethod): int
    {
        $mapping = [
            'cash' => 1010,              // Cash - Head Office
            'bank_transfer' => 1050,     // Bank Account - Main
            'card' => 1050,              // Bank Account - Main
            'pos' => 1050,               // Bank Account - Main
            'cheque' => 1050,            // Bank Account - Main
        ];

        $accountNumber = $mapping[$paymentMethod] ?? 1010;
        return GlAccount::where('account_number', (string)$accountNumber)->firstOrFail()->id;
    }
}
