<?php

namespace App\Services;

use App\Models\GlAccount;
use App\Models\GlEntry;
use App\Models\AccountingPeriod;
use App\Models\Sale;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    /**
     * Post a sale transaction to GL
     * Creates revenue entry and AR/Cash entry
     */
    public function postSaleTransaction(Sale $sale): bool
    {
        try {
            DB::beginTransaction();

            $period = $this->getCurrentPeriod();
            if (!$period || $period->status !== 'open') {
                throw new Exception('No open accounting period found');
            }

            // Get GL accounts
            $revenueAccount = $this->getRevenueAccount($sale);
            $arAccount = GlAccount::where('account_number', '1200')->firstOrFail();
            $cashAccount = GlAccount::where('account_number', '1110')->firstOrFail();

            $amount = (float) $sale->total;

            // Entry 1: Debit AR or Cash, Credit Revenue
            $entryType = $sale->isFullyPaid() ? 'sale' : 'sale';

            if ($sale->isFullyPaid()) {
                // Cash sale
                $this->createEntry([
                    'gl_account_id' => $cashAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->sale_number,
                    'description' => "Sale #{$sale->sale_number}",
                    'debit' => $amount,
                    'credit' => 0,
                    'entry_date' => $sale->sale_time,
                    'branch_id' => $sale->branch_id,
                ]);

                $this->createEntry([
                    'gl_account_id' => $revenueAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->sale_number,
                    'description' => "Sale Revenue #{$sale->sale_number}",
                    'debit' => 0,
                    'credit' => $amount,
                    'entry_date' => $sale->sale_time,
                    'branch_id' => $sale->branch_id,
                ]);
            } else {
                // Credit sale
                $this->createEntry([
                    'gl_account_id' => $arAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->sale_number,
                    'description' => "Credit Sale #{$sale->sale_number}",
                    'debit' => $amount,
                    'credit' => 0,
                    'entry_date' => $sale->sale_time,
                    'branch_id' => $sale->branch_id,
                ]);

                $this->createEntry([
                    'gl_account_id' => $revenueAccount->id,
                    'accounting_period_id' => $period->id,
                    'entry_type' => 'sale',
                    'reference_type' => Sale::class,
                    'reference_id' => $sale->id,
                    'reference_number' => $sale->sale_number,
                    'description' => "Sale Revenue #{$sale->sale_number}",
                    'debit' => 0,
                    'credit' => $amount,
                    'entry_date' => $sale->sale_time,
                    'branch_id' => $sale->branch_id,
                ]);
            }

            // Update sale record with GL posting status
            $sale->update([
                'gl_posting_status' => 'posted',
                'gl_posted_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Sale GL posting failed: ' . $e->getMessage(), [
                'sale_id' => $sale->id,
                'error' => $e,
            ]);

            $sale->update([
                'gl_posting_status' => 'failed',
                'gl_posting_error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Post a payment transaction to GL
     * Creates cash/bank entry and AR reduction or revenue entry
     */
    public function postPaymentTransaction(Payment $payment): bool
    {
        try {
            DB::beginTransaction();

            $period = $this->getCurrentPeriod();
            if (!$period || $period->status !== 'open') {
                throw new Exception('No open accounting period found');
            }

            $amount = (float) $payment->amount;
            $paymentMethod = strtolower($payment->payment_method);

            // Determine which account to debit based on payment method
            if ($paymentMethod === 'cash') {
                $cashAccount = GlAccount::where('account_number', '1101')->firstOrFail();
            } elseif ($paymentMethod === 'pos' || $paymentMethod === 'transfer') {
                $cashAccount = GlAccount::where('account_number', '1110')->firstOrFail();
            } else {
                throw new Exception('Unknown payment method: ' . $payment->payment_method);
            }

            // AR reduction
            $arAccount = GlAccount::where('account_number', '1200')->firstOrFail();

            // Entry: Debit Cash, Credit AR
            $this->createEntry([
                'gl_account_id' => $cashAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number,
                'description' => "Payment received - {$payment->payment_method}",
                'debit' => $amount,
                'credit' => 0,
                'entry_date' => $payment->payment_time,
                'branch_id' => $payment->branch_id,
            ]);

            $this->createEntry([
                'gl_account_id' => $arAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'payment',
                'reference_type' => Payment::class,
                'reference_id' => $payment->id,
                'reference_number' => $payment->reference_number,
                'description' => "AR reduction - {$payment->payment_method}",
                'debit' => 0,
                'credit' => $amount,
                'entry_date' => $payment->payment_time,
                'branch_id' => $payment->branch_id,
            ]);

            // Update payment record
            $payment->update([
                'gl_posting_status' => 'posted',
                'gl_posted_at' => now(),
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment GL posting failed: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
                'error' => $e,
            ]);

            $payment->update([
                'gl_posting_status' => 'failed',
                'gl_posting_error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Post COGS for sold inventory
     */
    public function postCogsSale(Sale $sale, $cogsAmount): bool
    {
        try {
            DB::beginTransaction();

            $period = $this->getCurrentPeriod();
            if (!$period || $period->status !== 'open') {
                throw new Exception('No open accounting period found');
            }

            $cogsAccount = GlAccount::where('account_number', '5110')->firstOrFail();
            $fgAccount = GlAccount::where('account_number', '1330')->firstOrFail();

            $amount = (float) $cogsAmount;

            // Debit COGS, Credit FG Inventory
            $this->createEntry([
                'gl_account_id' => $cogsAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sale_cogs',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reference_number' => $sale->sale_number,
                'description' => "COGS for Sale #{$sale->sale_number}",
                'debit' => $amount,
                'credit' => 0,
                'entry_date' => $sale->sale_time,
                'branch_id' => $sale->branch_id,
            ]);

            $this->createEntry([
                'gl_account_id' => $fgAccount->id,
                'accounting_period_id' => $period->id,
                'entry_type' => 'sale_cogs',
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reference_number' => $sale->sale_number,
                'description' => "FG reduction for Sale #{$sale->sale_number}",
                'debit' => 0,
                'credit' => $amount,
                'entry_date' => $sale->sale_time,
                'branch_id' => $sale->branch_id,
            ]);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('COGS GL posting failed: ' . $e->getMessage(), [
                'sale_id' => $sale->id,
                'error' => $e,
            ]);
            return false;
        }
    }

    /**
     * Create a GL entry and post it
     */
    private function createEntry(array $data): GlEntry
    {
        $entry = GlEntry::create([
            ...$data,
            'status' => 'draft',
            'entered_by_id' => auth()->id() ?? null,
            'entered_by_type' => auth()->user() ? get_class(auth()->user()) : null,
        ]);

        // Auto-post the entry
        $entry->post(auth()->id() ?? 1);

        return $entry;
    }

    /**
     * Get current open accounting period
     */
    public function getCurrentPeriod(): ?AccountingPeriod
    {
        return AccountingPeriod::where('status', 'open')
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now())
            ->first();
    }

    /**
     * Get revenue GL account based on sale attributes
     */
    private function getRevenueAccount(Sale $sale): GlAccount
    {
        // Default to 4110 - Product Sales Main
        return GlAccount::where('account_number', '4110')->firstOrFail();
    }

    /**
     * Get all GL accounts for a period
     */
    public function getAccountsForPeriod(AccountingPeriod $period): array
    {
        return GlAccount::where('is_active', true)
            ->where('is_header', false)
            ->get()
            ->map(function (GlAccount $account) use ($period) {
                $entries = GlEntry::where('gl_account_id', $account->id)
                    ->where('accounting_period_id', $period->id)
                    ->where('status', 'posted')
                    ->get();

                return [
                    'account' => $account,
                    'debit_total' => $entries->sum('debit'),
                    'credit_total' => $entries->sum('credit'),
                    'balance' => $account->getBalance(),
                ];
            })
            ->toArray();
    }

    /**
     * Get trial balance for a period
     */
    public function getTrialBalance(AccountingPeriod $period): array
    {
        $accounts = $this->getAccountsForPeriod($period);

        return [
            'total_debits' => collect($accounts)->sum('debit_total'),
            'total_credits' => collect($accounts)->sum('credit_total'),
            'accounts' => $accounts,
        ];
    }
}
