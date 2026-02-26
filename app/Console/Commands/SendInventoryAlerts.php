<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\HealthCheck;
use App\Models\Stock;
use App\Notifications\ExpiredItemsAlert;
use App\Notifications\HealthCheckAlert;
use App\Notifications\LowStockAlert;
use App\Services\NotificationRecipientService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendInventoryAlerts extends Command
{
    protected $signature = 'inventory:send-alerts
                            {--branch= : Limit to a specific branch id}
                            {--days=1 : Look back days for health checks}';

    protected $description = 'Send inventory alerts for low stock, expired items, and health checks';

    public function handle(): int
    {
        $branchId = $this->option('branch');
        $days = (int) $this->option('days');
        $branches = $branchId ? Branch::whereKey($branchId)->get() : Branch::all();

        foreach ($branches as $branch) {
            $this->sendForBranch($branch, $days);
        }

        return 0;
    }

    private function sendForBranch(Branch $branch, int $days): void
    {
        $recipientService = app(NotificationRecipientService::class);
        $recipients = $recipientService->usersForRoles(config('notifications.roles.inventory', []), $branch->id);
        if ($recipients->isEmpty()) {
            return;
        }

        $branchContext = $recipientService->branchContext($branch->id);

        $lowStock = Stock::with('item')
            ->where('branch_id', $branch->id)
            ->get()
            ->filter(fn ($stock) => $stock->isBelowReorderLevel())
            ->map(fn ($stock) => [
                'item_id' => $stock->item_id,
                'item_name' => $stock->item?->name,
                'quantity_available' => $stock->quantity_available,
                'reorder_level' => $stock->item?->reorder_level,
            ])
            ->values()
            ->all();

        if (! empty($lowStock)) {
            Notification::send($recipients, new LowStockAlert($lowStock, $branchContext));
        }

        $expired = Stock::with('item')
            ->where('branch_id', $branch->id)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->toDateString())
            ->get()
            ->map(fn ($stock) => [
                'item_id' => $stock->item_id,
                'item_name' => $stock->item?->name,
                'expiry_date' => optional($stock->expiry_date)->format('Y-m-d'),
                'quantity_available' => $stock->quantity_available,
            ])
            ->values()
            ->all();

        if (! empty($expired)) {
            Notification::send($recipients, new ExpiredItemsAlert($expired, $branchContext));
        }

        $since = now()->subDays($days)->startOfDay();
        $healthChecks = HealthCheck::with('stock.item')
            ->where('check_date', '>=', $since)
            ->get()
            ->filter(fn (HealthCheck $check) => $check->requiresAction())
            ->filter(fn (HealthCheck $check) => $check->stock?->branch_id === $branch->id)
            ->map(fn (HealthCheck $check) => [
                'check_id' => $check->id,
                'item_name' => $check->stock?->item?->name,
                'condition' => $check->condition,
                'quantity_affected' => $check->quantity_affected,
                'check_date' => optional($check->check_date)->format('Y-m-d'),
            ])
            ->values()
            ->all();

        if (! empty($healthChecks)) {
            Notification::send($recipients, new HealthCheckAlert($healthChecks, $branchContext));
        }
    }
}
