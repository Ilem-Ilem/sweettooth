<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\AccountingPeriod;
use App\Services\GlPostingService;
use Illuminate\Console\Command;
use Exception;

class BackfillGlEntries extends Command
{
    protected $signature = 'accounting:backfill-gl {--type=all} {--period-id=}';
    protected $description = 'Backfill GL entries from existing sales, purchases, and inventory movements';

    public function handle()
    {
        $glPostingService = app(GlPostingService::class);
        $type = $this->option('type');
        $periodId = $this->option('period-id');

        // Verify accounting period
        if (!$periodId) {
            $period = AccountingPeriod::current()->first();
            if (!$period) {
                $this->error('❌ No open accounting period. Create one first with: php artisan accounting:create-period');
                return 1;
            }
            $periodId = $period->id;
            $this->info("Using current period: {$period->name} ({$period->month}/{$period->year})");
        }

        if ($type === 'sales' || $type === 'all') {
            $this->backfillSales($glPostingService, $periodId);
        }

        if ($type === 'purchases' || $type === 'all') {
            $this->backfillPurchases($glPostingService, $periodId);
        }

        if ($type === 'payments' || $type === 'all') {
            $this->backfillPayments($glPostingService, $periodId);
        }

        if ($type === 'adjustments' || $type === 'all') {
            $this->backfillAdjustments($glPostingService, $periodId);
        }

        $this->info('✓ Backfill complete');
        return 0;
    }

    private function backfillSales(GlPostingService $glPostingService, int $periodId)
    {
        $this->info("\n📊 Processing Sales...");

        $sales = Sale::where('status', 'completed')
            ->where('gl_posting_status', '!=', 'posted')
            ->get();

        if ($sales->isEmpty()) {
            $this->info('  No pending sales found');
            return;
        }

        $bar = $this->output->createProgressBar($sales->count());
        $successCount = 0;
        $failCount = 0;

        foreach ($sales as $sale) {
            try {
                $glPostingService->postSaleTransaction($sale);
                $sale->update([
                    'gl_posting_status' => 'posted',
                    'gl_posted_at' => now(),
                ]);
                $successCount++;
            } catch (Exception $e) {
                $sale->update([
                    'gl_posting_status' => 'failed',
                    'gl_posting_error' => $e->getMessage(),
                ]);
                $failCount++;
                $this->warn("  ⚠ Sale {$sale->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n  ✓ Posted: $successCount | ✗ Failed: $failCount");
    }

    private function backfillPurchases(GlPostingService $glPostingService, int $periodId)
    {
        $this->info("\n📦 Processing Purchases...");

        $purchases = Purchase::where('status', 'approved')
            ->where('gl_posting_status', '!=', 'posted')
            ->get();

        if ($purchases->isEmpty()) {
            $this->info('  No pending purchases found');
            return;
        }

        $bar = $this->output->createProgressBar($purchases->count());
        $successCount = 0;
        $failCount = 0;

        foreach ($purchases as $purchase) {
            try {
                $glPostingService->postPurchaseTransaction($purchase);
                $purchase->update([
                    'gl_posting_status' => 'posted',
                    'gl_posted_at' => now(),
                ]);
                $successCount++;
            } catch (Exception $e) {
                $purchase->update([
                    'gl_posting_status' => 'failed',
                    'gl_posting_error' => $e->getMessage(),
                ]);
                $failCount++;
                $this->warn("  ⚠ Purchase {$purchase->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n  ✓ Posted: $successCount | ✗ Failed: $failCount");
    }

    private function backfillPayments(GlPostingService $glPostingService, int $periodId)
    {
        $this->info("\n💳 Processing Payments...");

        $payments = Payment::where('status', 'completed')
            ->where('gl_posting_status', '!=', 'posted')
            ->get();

        if ($payments->isEmpty()) {
            $this->info('  No pending payments found');
            return;
        }

        $bar = $this->output->createProgressBar($payments->count());
        $successCount = 0;
        $failCount = 0;

        foreach ($payments as $payment) {
            try {
                $glPostingService->postPaymentTransaction($payment);
                $payment->update([
                    'gl_posting_status' => 'posted',
                    'gl_posted_at' => now(),
                ]);
                $successCount++;
            } catch (Exception $e) {
                $payment->update([
                    'gl_posting_status' => 'failed',
                    'gl_posting_error' => $e->getMessage(),
                ]);
                $failCount++;
                $this->warn("  ⚠ Payment {$payment->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n  ✓ Posted: $successCount | ✗ Failed: $failCount");
    }

    private function backfillAdjustments(GlPostingService $glPostingService, int $periodId)
    {
        $this->info("\n🔧 Processing Inventory Adjustments...");

        $adjustments = StockMovement::whereIn('type', ['damage', 'shrinkage'])
            ->where('gl_posting_status', '!=', 'posted')
            ->get();

        if ($adjustments->isEmpty()) {
            $this->info('  No pending adjustments found');
            return;
        }

        $bar = $this->output->createProgressBar($adjustments->count());
        $successCount = 0;
        $failCount = 0;

        foreach ($adjustments as $adjustment) {
            try {
                $glPostingService->postInventoryAdjustment($adjustment);
                $adjustment->update([
                    'gl_posting_status' => 'posted',
                    'gl_posted_at' => now(),
                ]);
                $successCount++;
            } catch (Exception $e) {
                $adjustment->update([
                    'gl_posting_status' => 'failed',
                    'gl_posting_error' => $e->getMessage(),
                ]);
                $failCount++;
                $this->warn("  ⚠ Adjustment {$adjustment->id}: {$e->getMessage()}");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n  ✓ Posted: $successCount | ✗ Failed: $failCount");
    }
}
