<?php

// app/Helpers/ActionRequestHelper.php
use App\Models\AuditRequest;

function requestSensitiveAction(string $action, array $payload, string $reason): ActionRequest
{
    return AuditRequest::create([
        'requested_by' => auth()->id(),
        'action'       => $action,
        'payload'      => $payload,
        'reason'       => $reason,
        'status'       => 'pending',
    ]);
}

function executeApprovedAction(AuditRequest $request)
{
    if ($request->status !== 'approved' || $request->executed_at) {
        return false;
    }

    DB::transaction(function () use ($request) {
        match ($request->action) {
            'negative_stock_adjustment' => executeNegativeAdjustment($request->payload),
            'price_drop_below_cost'     => executePriceChange($request->payload),
            'delete_product'            => executeProductDeletion($request->payload),
            'mass_stock_reset'          => executeMassStockReset($request->payload),
            default => throw new \Exception("Unknown action: {$request->action}"),
        };

        $request->update([
            'executed_at' => now(),
        ]);
    });

    // Optional: dispatch Livewire refresh globally
    \Livewire\Livewire::dispatch('action-executed');
}