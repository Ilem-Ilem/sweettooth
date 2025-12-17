# 02 - Progress Calculation Logic for Production Tracking

## Overview

This document outlines the algorithms and business logic for calculating production progress across different levels of the system. Progress tracking happens at multiple granularities: item level, batch level, production request level, and department level.

## Current Progress Calculation

### Basic Progress Calculation (Existing)
The current system calculates progress based on produced vs requested quantities:

```php
// Basic progress percentage
$progressPercentage = ($producedQuantity / $requestedQuantity) * 100;

// Status determination
if ($producedQuantity >= $requestedQuantity) {
    $status = 'completed';
} elseif ($producedQuantity > 0) {
    $status = 'in_progress';
} else {
    $status = 'pending';
}
```

## Enhanced Progress Calculation System

### 1. Multi-Level Progress Tracking

#### 1.1 Item Request Level Progress
**Purpose**: Track progress of individual items requested by departments

**Calculation Logic**:
```php
class ItemRequestProgressCalculator
{
    public function calculateProgress(ItemRequest $itemRequest): array
    {
        $details = $itemRequest->requestDetails;
        $totalItems = $details->count();
        $completedItems = 0;
        $totalRequested = 0;
        $totalProduced = 0;

        foreach ($details as $detail) {
            $requested = (float) $detail->quantity_requested;
            $approved = (float) $detail->quantity_approved;
            $dispatched = (float) $detail->quantity_dispatched;

            $totalRequested += $requested;

            // Calculate produced quantity from production records
            $produced = $this->getProducedQuantity($detail);
            $totalProduced += $produced;

            // Item is complete if dispatched >= approved >= requested
            if ($dispatched >= $approved && $approved >= $requested && $requested > 0) {
                $completedItems++;
            }
        }

        $overallProgress = $totalRequested > 0 ? ($totalProduced / $totalRequested) * 100 : 0;
        $itemsProgress = ($completedItems / $totalItems) * 100;

        return [
            'overall_progress' => min(100, $overallProgress),
            'items_progress' => min(100, $itemsProgress),
            'total_items' => $totalItems,
            'completed_items' => $completedItems,
            'total_requested' => $totalRequested,
            'total_produced' => $totalProduced,
            'status' => $this->determineStatus($overallProgress, $itemsProgress, $itemRequest->status)
        ];
    }

    private function getProducedQuantity(ItemRequestDetail $detail): float
    {
        // Sum quantities from production records linked to this item
        return ProductionRecord::whereHas('dailyProduce.productionRequest.itemRequest', function($q) use ($detail) {
            $q->where('id', $detail->request_id);
        })
        ->where('item_id', $detail->item_id)
        ->sum('quantity_produced');
    }

    private function determineStatus(float $overallProgress, float $itemsProgress, string $requestStatus): string
    {
        if ($requestStatus === 'cancelled') return 'cancelled';
        if ($requestStatus === 'completed') return 'completed';
        if ($overallProgress >= 100 && $itemsProgress >= 100) return 'completed';
        if ($overallProgress > 0 || $itemsProgress > 0) return 'in_progress';
        return 'pending';
    }
}
```

#### 1.2 Production Request Level Progress
**Purpose**: Track progress of production requests assigned to shifts

**Calculation Logic**:
```php
class ProductionRequestProgressCalculator
{
    public function calculateProgress(ProductionRequest $productionRequest): array
    {
        $plannedQuantity = (float) $productionRequest->planned_production_quantity;

        // Get actual produced quantity from daily produces
        $producedQuantity = DailyProduce::whereHas('productionRequest', function($q) use ($productionRequest) {
            $q->where('id', $productionRequest->id);
        })->sum('produced_quantity');

        $progressPercentage = $plannedQuantity > 0 ? ($producedQuantity / $plannedQuantity) * 100 : 0;

        // Calculate stage progress based on milestones
        $stageProgress = $this->calculateStageProgress($productionRequest);

        return [
            'progress_percentage' => min(100, $progressPercentage),
            'planned_quantity' => $plannedQuantity,
            'produced_quantity' => $producedQuantity,
            'stage_progress' => $stageProgress,
            'status' => $this->determineProductionStatus($progressPercentage, $productionRequest),
            'estimated_completion' => $this->calculateEstimatedCompletion($productionRequest, $stageProgress)
        ];
    }

    private function calculateStageProgress(ProductionRequest $request): array
    {
        $stages = ['ingredients_collected', 'production_started', 'quality_check_passed', 'packaging_completed', 'dispatch_ready'];
        $completedStages = 0;

        foreach ($stages as $stage) {
            if ($this->isStageCompleted($request, $stage)) {
                $completedStages++;
            }
        }

        return [
            'total_stages' => count($stages),
            'completed_stages' => $completedStages,
            'current_stage' => $this->getCurrentStage($request),
            'stage_percentage' => (count($stages) > 0) ? ($completedStages / count($stages)) * 100 : 0
        ];
    }

    private function determineProductionStatus(float $progressPercentage, ProductionRequest $request): string
    {
        if ($progressPercentage >= 100) return 'completed';
        if ($progressPercentage > 0) return 'in_progress';
        if ($request->shift_id && $request->recipe_id) return 'ready';
        return 'pending';
    }
}
```

#### 1.3 Batch Level Progress (ProductionRecord)
**Purpose**: Track progress of individual production batches

**Calculation Logic**:
```php
class ProductionBatchProgressCalculator
{
    public function calculateProgress(ProductionRecord $record): array
    {
        $produced = (float) $record->quantity_produced;
        $approved = (float) $record->quantity_approved;
        $rejected = (float) $record->quantity_rejected;
        $sentOut = (float) $record->quantity_sent_out;
        $forOrder = (float) $record->quantity_for_order;

        // Quality progress
        $qualityProgress = $produced > 0 ? (($approved + $rejected) / $produced) * 100 : 0;

        // Dispatch progress
        $dispatchable = $approved - $rejected;
        $dispatched = $sentOut + $forOrder;
        $dispatchProgress = $dispatchable > 0 ? ($dispatched / $dispatchable) * 100 : 0;

        // Stage progress based on milestones
        $stageProgress = $this->calculateStageProgress($record);

        // Overall progress (weighted average)
        $overallProgress = $this->calculateOverallProgress([
            'quality' => $qualityProgress,
            'dispatch' => $dispatchProgress,
            'stage' => $stageProgress['percentage']
        ]);

        return [
            'overall_progress' => $overallProgress,
            'quality_progress' => $qualityProgress,
            'dispatch_progress' => $dispatchProgress,
            'stage_progress' => $stageProgress,
            'produced_quantity' => $produced,
            'approved_quantity' => $approved,
            'rejected_quantity' => $rejected,
            'remaining_quantity' => $approved - $sentOut - $forOrder,
            'status' => $this->determineBatchStatus($record, $overallProgress)
        ];
    }

    private function calculateStageProgress(ProductionRecord $record): array
    {
        $milestones = ProductionMilestone::where('production_record_id', $record->id)
            ->orderBy('achieved_at')
            ->get();

        $stages = ['started', 'ingredients_collected', 'processing', 'quality_check', 'packaging', 'ready_for_dispatch'];
        $completedStages = $milestones->count();
        $currentStage = $stages[min($completedStages, count($stages) - 1)];

        return [
            'total_stages' => count($stages),
            'completed_stages' => $completedStages,
            'current_stage' => $currentStage,
            'percentage' => (count($stages) > 0) ? ($completedStages / count($stages)) * 100 : 0,
            'milestones' => $milestones
        ];
    }

    private function calculateOverallProgress(array $components): float
    {
        // Weighted average: Quality 40%, Dispatch 30%, Stage 30%
        return (
            ($components['quality'] * 0.4) +
            ($components['dispatch'] * 0.3) +
            ($components['stage'] * 0.3)
        );
    }

    private function determineBatchStatus(ProductionRecord $record, float $progress): string
    {
        if ($progress >= 100) return 'completed';
        if ($record->quality_status === 'rejected') return 'rejected';
        if ($progress > 0) return 'in_progress';
        return 'pending';
    }
}
```

### 2. Department-Level Progress Aggregation

#### 2.1 Shift-Level Department Progress
```php
class DepartmentProgressCalculator
{
    public function calculateShiftProgress(int $departmentId, int $shiftId, string $date): array
    {
        $productions = DailyProduce::whereHas('shift', function($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })
        ->where('shift_id', $shiftId)
        ->whereDate('produce_date', $date)
        ->with('productionRecords')
        ->get();

        $totalItems = $productions->count();
        $completedItems = $productions->where('status', 'completed')->count();
        $inProgressItems = $productions->where('status', 'in_progress')->count();

        $totalRequested = $productions->sum('requested_quantity');
        $totalProduced = $productions->sum('produced_quantity');

        // Calculate efficiency metrics
        $efficiency = $this->calculateEfficiency($productions);

        return [
            'department_id' => $departmentId,
            'shift_id' => $shiftId,
            'date' => $date,
            'total_items' => $totalItems,
            'completed_items' => $completedItems,
            'in_progress_items' => $inProgressItems,
            'pending_items' => $totalItems - $completedItems - $inProgressItems,
            'total_requested' => $totalRequested,
            'total_produced' => $totalProduced,
            'completion_percentage' => $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0,
            'production_percentage' => $totalRequested > 0 ? ($totalProduced / $totalRequested) * 100 : 0,
            'efficiency_metrics' => $efficiency,
            'alerts' => $this->generateAlerts($productions)
        ];
    }

    private function calculateEfficiency(Collection $productions): array
    {
        $totalTime = 0;
        $totalProduced = 0;

        foreach ($productions as $production) {
            $startTime = $production->shift->start_time ?? now()->startOfDay();
            $endTime = $production->shift->end_time ?? now()->endOfDay();
            $duration = $startTime->diffInMinutes($endTime);

            $totalTime += $duration;
            $totalProduced += $production->produced_quantity;
        }

        return [
            'production_rate_per_hour' => $totalTime > 0 ? ($totalProduced / ($totalTime / 60)) : 0,
            'average_completion_time' => $this->calculateAverageCompletionTime($productions),
            'quality_rate' => $this->calculateQualityRate($productions)
        ];
    }

    private function generateAlerts(Collection $productions): array
    {
        $alerts = [];

        // Check for overdue items
        $overdue = $productions->filter(function($p) {
            return $p->status === 'pending' &&
                   $p->produce_date < now()->startOfDay() &&
                   $p->requested_quantity > 0;
        });

        if ($overdue->count() > 0) {
            $alerts[] = [
                'type' => 'overdue',
                'severity' => 'high',
                'message' => "{$overdue->count()} items are overdue",
                'count' => $overdue->count()
            ];
        }

        // Check for low efficiency
        $lowEfficiency = $productions->filter(function($p) {
            return $p->produced_quantity < ($p->requested_quantity * 0.5);
        });

        if ($lowEfficiency->count() > 0) {
            $alerts[] = [
                'type' => 'low_efficiency',
                'severity' => 'medium',
                'message' => "{$lowEfficiency->count()} items have low production efficiency",
                'count' => $lowEfficiency->count()
            ];
        }

        return $alerts;
    }
}
```

### 3. Time-Based Progress Calculations

#### 3.1 Estimated Completion Time
```php
class TimeProgressCalculator
{
    public function calculateEstimatedCompletion(DailyProduce $production): ?Carbon
    {
        if ($production->status === 'completed') {
            return $production->actual_completion_time;
        }

        $remainingQuantity = $production->requested_quantity - $production->produced_quantity;
        if ($remainingQuantity <= 0) {
            return now();
        }

        // Calculate production rate (quantity per hour)
        $productionRate = $this->calculateProductionRate($production);

        if ($productionRate <= 0) {
            return null; // Cannot estimate
        }

        $hoursNeeded = $remainingQuantity / $productionRate;
        $estimatedCompletion = now()->addHours($hoursNeeded);

        // Don't estimate beyond shift end
        $shiftEnd = $production->shift->end_time ?? now()->endOfDay();
        if ($estimatedCompletion->greaterThan($shiftEnd)) {
            $estimatedCompletion = $shiftEnd;
        }

        return $estimatedCompletion;
    }

    private function calculateProductionRate(DailyProduce $production): float
    {
        // Calculate based on historical data for this recipe/department
        $historicalProductions = DailyProduce::where('recipe_id', $production->recipe_id)
            ->whereHas('shift', function($q) use ($production) {
                $q->where('department_id', $production->shift->department_id);
            })
            ->where('status', 'completed')
            ->whereDate('produce_date', '>=', now()->subDays(30))
            ->get();

        if ($historicalProductions->isEmpty()) {
            return 0;
        }

        $totalProduced = $historicalProductions->sum('produced_quantity');
        $totalHours = $historicalProductions->sum(function($p) {
            $shiftDuration = $p->shift->start_time->diffInHours($p->shift->end_time);
            return $shiftDuration;
        });

        return $totalHours > 0 ? $totalProduced / $totalHours : 0;
    }
}
```

### 4. Progress Update Triggers

#### 4.1 Automatic Progress Updates
Progress should be recalculated when:
- Production quantities are updated
- New production records are created
- Milestones are achieved
- Status changes occur
- Time-based events (shift start/end)

#### 4.2 Service Implementation
```php
class ProgressUpdateService
{
    public function updateProgress($model): void
    {
        if ($model instanceof ProductionRecord) {
            $this->updateBatchProgress($model);
        } elseif ($model instanceof DailyProduce) {
            $this->updateProductionProgress($model);
        } elseif ($model instanceof ItemRequest) {
            $this->updateRequestProgress($model);
        }

        // Update department summaries
        $this->updateDepartmentSummaries($model);
    }

    private function updateBatchProgress(ProductionRecord $record): void
    {
        $calculator = new ProductionBatchProgressCalculator();
        $progress = $calculator->calculateProgress($record);

        $record->update([
            'progress_percentage' => $progress['overall_progress'],
            'dispatch_status' => $this->mapProgressToStatus($progress['overall_progress'])
        ]);
    }

    private function mapProgressToStatus(float $progress): string
    {
        if ($progress >= 100) return 'fully_dispatched';
        if ($progress > 50) return 'partial';
        if ($progress > 0) return 'available';
        return 'pending';
    }
}
```

## Performance Optimization

### 1. Caching Strategy
- Cache progress calculations for 5-15 minutes
- Use Redis for real-time progress data
- Cache department summaries for dashboard performance

### 2. Background Processing
- Queue progress updates for bulk operations
- Use database triggers for automatic updates
- Implement progressive loading for large datasets

### 3. Database Optimization
- Add indexes on frequently queried progress fields
- Use database views for complex calculations
- Implement data partitioning for historical data