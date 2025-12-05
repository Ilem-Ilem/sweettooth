<?php

namespace App\Services;

use App\Models\ApprovalAuditRequest;
use App\Models\Employee;
use App\Models\Purchase;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseAuditApprovalService
{
    /**
     * Create a pending purchase creation request via the audit approval system
     */
    public static function requestPurchaseCreation(Employee $requester, array $purchaseData, string $reason): ApprovalAuditRequest
    {
        $request = ApprovalAuditRequest::create([
            'branch_id' => $requester->branch_id,
            'requester_id' => $requester->id,
            'requester_type' => Employee::class,
            'action' => 'create_purchase',
            'description' => $reason,
            'payload' => $purchaseData,
            'status' => 'pending',
        ]);

        // Log the approval request
        AuditService::log(
            $requester,
            'create',
            $request,
            "Requested purchase creation approval from '{$purchaseData['supplier_name']}'. " .
            "Total: {$purchaseData['landing_cost']}. Items: " . count($purchaseData['items'] ?? []) . ". " .
            "Reason: {$reason}",
            'pending'
        );

        return $request;
    }

    /**
     * Execute a pending purchase creation after approval
     */
    public static function executePurchaseCreation(ApprovalAuditRequest $request, Employee $approver): Purchase
    {
        return DB::transaction(function () use ($request, $approver) {
            $payload = $request->payload;

            // Create the purchase
            $purchase = Purchase::create([
                'branch_id' => $request->branch_id,
                'recorded_by_id' => $approver->id,
                'recorded_by_type' => get_class($approver),
                'supplier_name' => $payload['supplier_name'],
                'purchase_number' => $payload['purchase_number'] ?? Purchase::generatePurchaseNumber($request->branch_id),
                'purchase_date' => $payload['purchase_date'],
                'total_fob_fc' => (float) ($payload['total_fob_fc'] ?? 0),
                'total_fob_ngn' => (float) ($payload['total_fob_ngn'] ?? 0),
                'other_costs' => (float) ($payload['other_costs'] ?? 0),
                'landing_cost' => (float) $payload['landing_cost'],
                'payment_status' => $payload['payment_status'] ?? 'pending',
                'status' => 'approved',
            ]);

            // Create purchase items and stock movements if provided
            if (!empty($payload['items'])) {
                foreach ($payload['items'] as $item) {
                    $quantity = (float) $item['quantity'];
                    $costPerUnit = (float) ($item['cost_per_unit'] ?? $item['unit_cost'] ?? 0);
                    $totalCost = $quantity * $costPerUnit;

                    // Create purchase item
                    $purchaseItem = $purchase->purchaseItems()->create([
                        'item_id' => $item['item_id'],
                        'quantity' => $quantity,
                        'uom' => $item['uom'] ?? null,
                        'fob_fc' => (float) ($item['fob_fc'] ?? 0),
                        'fob_ngn' => (float) ($item['fob_ngn'] ?? 0),
                        'other_costs' => (float) ($item['other_costs'] ?? 0),
                        'total_cost' => $totalCost,
                        'cost_per_unit' => $costPerUnit,
                    ]);

                    // Create or get stock
                    $stock = Stock::firstOrCreate(
                        [
                            'branch_id' => $request->branch_id,
                            'item_id' => $item['item_id'],
                        ],
                        [
                            'quantity_available' => 0,
                            'quantity_reserved' => 0,
                            'average_cost' => 0,
                        ]
                    );

                    // Update average cost
                    $stock->updateAverageCost($quantity, $costPerUnit);
                    
                    // Record quantity before update
                    $quantity_before = $stock->quantity_available;
                    
                    // Update stock quantity
                    $stock->quantity_available += $quantity;
                    $stock->last_stock_take_date = now();
                    $stock->save();

                    // Create stock movement record
                    StockMovement::create([
                        'stock_id' => $stock->id,
                        'type' => 'in',
                        'quantity_before' => $quantity_before,
                        'quantity_after' => $stock->quantity_available,
                        'quantity' => $quantity,
                        'reference_type' => 'App\Models\Purchase',
                        'reference_id' => $purchase->id,
                        'moved_by_type' => get_class($approver),
                        'moved_by_id' => $approver->id,
                        'movement_date' => $purchase->purchase_date,
                        'notes' => 'Purchase: ' . $purchase->purchase_number,
                    ]);
                }
            }

            // Mark request as approved
            $request->update([
                'approver_id' => $approver->id,
                'approver_type' => Employee::class,
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Log the completion
            AuditService::log(
                $approver,
                'create',
                $purchase,
                "Purchase #{$purchase->purchase_number} created via approval: " . count($payload['items'] ?? []) . " items. " .
                "Total FOB FC: {$payload['total_fob_fc']}, Total FOB NGN: {$payload['total_fob_ngn']}, Landing Cost: {$payload['landing_cost']}",
                'completed'
            );

            return $purchase;
        });
    }

    /**
     * Create a pending purchase deletion request via the audit approval system
     */
    public static function requestPurchaseDeletion(Employee $requester, int $purchaseId, string $reason): ApprovalAuditRequest
    {
        $purchase = Purchase::findOrFail($purchaseId);

        $request = ApprovalAuditRequest::create([
            'branch_id' => $requester->branch_id,
            'requester_id' => $requester->id,
            'requester_type' => Employee::class,
            'action' => 'delete_purchase:' . $purchaseId,
            'description' => $reason,
            'payload' => ['purchase_id' => $purchaseId],
            'status' => 'pending',
        ]);

        // Log the approval request
        AuditService::log(
            $requester,
            'create',
            $request,
            "Requested purchase deletion approval for #{$purchase->purchase_number} from {$purchase->supplier_name}. " .
            "Reason: {$reason}",
            'pending'
        );

        return $request;
    }

    /**
     * Execute a pending purchase deletion after approval
     */
    public static function executePurchaseDeletion(ApprovalAuditRequest $request, Employee $approver): void
    {
        DB::transaction(function () use ($request, $approver) {
            $payload = $request->payload;
            $purchaseId = $payload['purchase_id'];

            $purchase = Purchase::where('id', $purchaseId)
                ->where('branch_id', $request->branch_id)
                ->firstOrFail();

            $purchaseNumber = $purchase->purchase_number;
            $supplierName = $purchase->supplier_name;

            $purchase->delete();

            // Mark request as approved
            $request->update([
                'approver_id' => $approver->id,
                'approver_type' => Employee::class,
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Log the deletion
            AuditService::log(
                $approver,
                'delete',
                $purchase,
                "Deleted purchase #{$purchaseNumber} from {$supplierName} via approval.",
                'completed'
            );
        });
    }

    /**
     * Create a pending purchase approval request from draft
     */
    public static function requestPurchaseApproval(Employee $requester, int $purchaseId, string $reason): ApprovalAuditRequest
    {
        $purchase = Purchase::findOrFail($purchaseId);

        if ($purchase->status !== 'draft') {
            throw new \Exception('Purchase must be in draft status to request approval');
        }

        $request = ApprovalAuditRequest::create([
            'branch_id' => $requester->branch_id,
            'requester_id' => $requester->id,
            'requester_type' => Employee::class,
            'action' => 'approve_purchase:' . $purchaseId,
            'description' => $reason,
            'payload' => ['purchase_id' => $purchaseId],
            'status' => 'pending',
        ]);

        // Update purchase status
        $purchase->update(['status' => 'pending_approval']);

        // Log the approval request
        AuditService::log(
            $requester,
            'update',
            $purchase,
            "Requested approval for purchase draft #{$purchase->purchase_number} from {$purchase->supplier_name}. " .
            "Total: {$purchase->landing_cost}. Reason: {$reason}",
            'pending'
        );

        return $request;
    }

    /**
     * Execute a pending purchase approval request
     */
    public static function approvePurchase(ApprovalAuditRequest $request, Employee $approver): Purchase
    {
        return DB::transaction(function () use ($request, $approver) {
            $payload = $request->payload;
            $purchaseId = $payload['purchase_id'];

            $purchase = Purchase::where('id', $purchaseId)
                ->where('branch_id', $request->branch_id)
                ->with('purchaseItems')
                ->firstOrFail();

            // Update purchase status to approved
            $purchase->update(['status' => 'approved']);

            // Create stock movements for each purchase item
            foreach ($purchase->purchaseItems as $purchaseItem) {
                $stock = Stock::firstOrCreate(
                    [
                        'branch_id' => $request->branch_id,
                        'item_id' => $purchaseItem->item_id,
                    ],
                    [
                        'quantity_available' => 0,
                        'quantity_reserved' => 0,
                        'average_cost' => 0,
                    ]
                );

                $quantity = $purchaseItem->quantity;
                $costPerUnit = $purchaseItem->cost_per_unit ?? 0;

                // Update average cost
                $stock->updateAverageCost($quantity, $costPerUnit);
                
                // Record quantity before update
                $quantity_before = $stock->quantity_available;
                
                // Update stock quantity
                $stock->quantity_available += $quantity;
                $stock->last_stock_take_date = now();
                $stock->save();

                // Create stock movement record
                StockMovement::create([
                    'stock_id' => $stock->id,
                    'type' => 'in',
                    'quantity_before' => $quantity_before,
                    'quantity_after' => $stock->quantity_available,
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Purchase',
                    'reference_id' => $purchase->id,
                    'moved_by_type' => get_class($approver),
                    'moved_by_id' => $approver->id,
                    'movement_date' => $purchase->purchase_date,
                    'notes' => 'Purchase: ' . $purchase->purchase_number,
                ]);
            }

            // Mark request as approved
            $request->update([
                'approver_id' => $approver->id,
                'approver_type' => Employee::class,
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            // Log the approval
            AuditService::log(
                $approver,
                'update',
                $purchase,
                "Approved purchase #{$purchase->purchase_number} from {$purchase->supplier_name}. " .
                "Created stock movements for " . count($purchase->purchaseItems) . " items.",
                'completed'
            );

            return $purchase;
        });
    }

    /**
     * Reject a pending purchase approval request
     */
    public static function rejectPurchase(ApprovalAuditRequest $request, Employee $approver, string $comment): Purchase
    {
        return DB::transaction(function () use ($request, $approver, $comment) {
            $payload = $request->payload;
            $purchaseId = $payload['purchase_id'];

            $purchase = Purchase::where('id', $purchaseId)
                ->where('branch_id', $request->branch_id)
                ->firstOrFail();

            // Update purchase status back to draft
            $purchase->update(['status' => 'draft']);

            // Mark request as rejected
            $request->update([
                'approver_id' => $approver->id,
                'approver_type' => Employee::class,
                'status' => 'rejected',
                'comment' => $comment,
                'denied_at' => now(),
            ]);

            // Log the rejection
            AuditService::log(
                $approver,
                'update',
                $purchase,
                "Rejected purchase #{$purchase->purchase_number} from {$purchase->supplier_name}. Reason: {$comment}",
                'completed'
            );

            return $purchase;
        });
    }
}
