<?php


// app/Helpers/AuditHelper.php
function auditAction(
    string $action,           // 'stock_adjustment', 'price_change', 'bom_update', etc.
    $model,                   // Eloquent model being changed (Product, StockMovement, etc.)
    array $extra = [],        // any custom data: reason, reference, etc.
    ?string $reason = null,   // mandatory for sensitive actions
    bool $requiresApproval = false
) {
    // 1. Check if approval is needed and not yet approved
    if ($requiresApproval && ! session('action_approved_' . $action . '_' . $model->id)) {
        throw new \Exception("This action requires supervisor approval.");
    }

    // 2. Log to custom audit trail table
    \App\Models\Audit::create([
        'user_id'       => auth()->id() ?? null,
        'action'        => $action,
        'auditable_type'=> get_class($model),
        'auditable_id'  => $model->id,
        'old_values'    => $model->getOriginal(),
        'new_values'    => $model->getAttributes(),
        'reason'        => $reason,
        'ip_address'    => request()->ip(),
        'user_agent'    => request()->userAgent(),
        'url'           => request()->fullUrl(),
        'metadata'      => $extra, // JSON: reference order, batch number, etc.
        'approved_at'   => $requiresApproval ? now() : null,
        'approved_by'   => $requiresApproval ? auth()->id() : null,
    ]);

    // 3. Also let owen-it/laravel-auditing do its thing automatically (optional bonus)
}