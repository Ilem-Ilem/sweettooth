<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Services\AccountingService;
use App\Services\InventoryAccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class PostSaleToGl implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private AccountingService $accountingService,
        private InventoryAccountingService $inventoryService,
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(SaleCreated $event): void
    {
        $sale = $event->sale;

        // Post revenue and cash/AR entry
        $this->accountingService->postSaleTransaction($sale);

        // Calculate and post COGS
        // This would require calculating actual cost of goods sold
        // For now, you'd determine COGS based on inventory valuation method
        $totalCogsAmount = $this->calculateSaleCogs($sale);
        if ($totalCogsAmount > 0) {
            $this->accountingService->postCogsSale($sale, $totalCogsAmount);
        }
    }

    /**
     * Calculate COGS for a sale
     */
    private function calculateSaleCogs($sale): float
    {
        // This would be calculated based on the items in the sale
        // and the costing method (FIFO, weighted average, etc.)
        // For now, return 0
        return 0;
    }
}
