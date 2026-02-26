<?php

namespace App\Notifications;

use App\Models\Department;
use App\Models\SalesProductionRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SalesProductionRequestCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public SalesProductionRequest $request;
    public array $productionDepartmentIds;

    public function __construct(SalesProductionRequest $request, array $productionDepartmentIds = [])
    {
        $this->request = $request;
        $this->productionDepartmentIds = array_values(array_unique(array_filter($productionDepartmentIds)));
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->request->loadMissing([
            'salesDepartment',
            'requestedBy',
            'items.product',
            'items.productionDepartment',
        ]);

        $departments = [];
        if (! empty($this->productionDepartmentIds)) {
            $departments = Department::query()
                ->whereIn('id', $this->productionDepartmentIds)
                ->pluck('name')
                ->values()
                ->toArray();
        }

        $items = $this->request->items
            ->map(function ($item) {
                $productName = $item->product?->name ?? 'Unknown Product';
                $qty = number_format((float) $item->quantity_requested, 2);
                $uom = $item->product?->effectiveSalesUomSymbol ?? $item->product?->uomSymbol ?? null;
                $deptName = $item->productionDepartment?->name;

                $line = $uom ? "{$productName} x {$qty} {$uom}" : "{$productName} x {$qty}";

                return $deptName
                    ? "{$line} ({$deptName})"
                    : $line;
            })
            ->values()
            ->toArray();

        $requestedByRole = null;
        if ($this->request->requestedBy && method_exists($this->request->requestedBy, 'getRoleNames')) {
            $requestedByRole = $this->request->requestedBy->getRoleNames()->implode(', ');
        }

        return [
            'type' => 'sales_production_request_created',
            'sales_production_request_id' => $this->request->id,
            'branch_id' => $this->request->branch_id,
            'display_type' => 'Converted from sales to production',
            'sales_request_number' => $this->request->request_number,
            'title' => 'Sales Request Sent to Production',
            'message' => "Sales request {$this->request->request_number} was sent to production.",
            'summary' => $this->request->salesDepartment?->name
                ? "From {$this->request->salesDepartment->name}."
                : 'Production request created.',
            'context' => [
                'request_number' => $this->request->request_number,
                'requested_by' => $this->request->requestedBy?->name,
                'requested_by_role' => $requestedByRole,
                'sales_department' => $this->request->salesDepartment?->name,
                'production_departments' => $departments,
                'status' => 'Pending',
            ],
            'requested_by' => $this->request->requestedBy?->name,
            'requested_by_role' => $requestedByRole,
            'sales_department' => $this->request->salesDepartment?->name,
            'production_departments' => $departments,
            'items' => $items,
            'items_preview' => $items,
        ];
    }
}
