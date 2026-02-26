<?php

namespace App\Services;

use App\Models\DailyProduce;
use App\Models\ProductionRequest;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductionShiftRolloverService
{
    public function carryForwardPendingBatches(Shift $currentShift, ?array $productionRequestIds = null): array
    {
        $requestsQuery = ProductionRequest::where('shift_id', $currentShift->id)
            ->with('recipe');

        if ($productionRequestIds && count($productionRequestIds) > 0) {
            $requestsQuery->whereIn('id', $productionRequestIds);
        }

        /** @var Collection<int, ProductionRequest> $requests */
        $requests = $requestsQuery->get();
        if ($requests->isEmpty()) {
            return [
                'carried_forward_count' => 0,
                'next_shift_id' => null,
            ];
        }

        $nextShift = null;
        $carriedForward = 0;

        foreach ($requests as $request) {
            $request->syncBatchFulfillmentFromDailyProduces(true);
            if ($request->getPendingBatches() <= 0) {
                continue;
            }

            if (!$nextShift) {
                $nextShift = $this->resolveNextShift($currentShift);
            }

            $request->shift_id = $nextShift->id;
            $request->saveQuietly();

            $openingQty = DailyProduce::getOpeningQuantityFromPreviousShift(
                $request->recipe_id,
                $nextShift->branch_id,
                $nextShift->shift_date,
                $nextShift->shift_type
            );

            DailyProduce::firstOrCreate([
                'shift_id' => $nextShift->id,
                'recipe_id' => $request->recipe_id,
                'production_request_id' => $request->id,
            ], [
                'produce_date' => $nextShift->shift_date,
                'shift_type' => $nextShift->shift_type,
                'opening_quantity' => $openingQty,
                'requested_quantity' => $request->planned_production_quantity,
                'produced_quantity' => 0,
                'batches_produced_this_shift' => 0,
                'batches_remaining' => $request->batches_pending,
                'fulfillment_status' => 'pending',
                'sent_out_quantity' => 0,
                'order_quantity' => 0,
                'callback_quantity' => 0,
                'closing_quantity' => $openingQty,
                'expected_closing' => $openingQty,
                'variance' => 0,
            ]);

            $carriedForward++;
        }

        return [
            'carried_forward_count' => $carriedForward,
            'next_shift_id' => $nextShift?->id,
        ];
    }

    protected function resolveNextShift(Shift $currentShift): Shift
    {
        $currentDate = Carbon::parse($currentShift->shift_date);
        $currentType = (string) $currentShift->shift_type;

        if ($currentType === 'morning') {
            $nextType = 'afternoon';
            $nextDate = $currentDate->copy();
        } else {
            $nextType = 'morning';
            $nextDate = $currentDate->copy()->addDay();
        }

        $existing = Shift::where('branch_id', $currentShift->branch_id)
            ->where('department_id', $currentShift->department_id)
            ->whereDate('shift_date', $nextDate)
            ->where('shift_type', $nextType)
            ->first();

        if ($existing) {
            return $existing;
        }

        $deptKey = (string) ($currentShift->department?->slug ?? $currentShift->department_id ?? 'dept');
        $userId = (string) (Auth::id() ?? $currentShift->employee_id ?? 'system');

        return Shift::create([
            'branch_id' => $currentShift->branch_id,
            'department_id' => $currentShift->department_id,
            'shift_date' => $nextDate->toDateString(),
            'shift_type' => $nextType,
            'status' => 'active',
            'shift_number' => Shift::generateShiftNumber($deptKey, $userId, now(), $nextType),
        ]);
    }
}
