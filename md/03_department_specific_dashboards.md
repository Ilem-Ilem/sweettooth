# 03 - Department-Specific Production Dashboards

## Overview

This document outlines the implementation of specialized dashboards for each production department (Kitchen, Gelato Production, Confectionaries Production). Each department has unique requirements for progress tracking, resource management, and workflow visualization.

## Current Department Structure

Based on the database seeders, the production departments are:

### 1. Kitchen (`name: 'Kitchen'`)
- **Purpose**: Prepares food for Till, Confectionaries, Corner Store
- **Characteristics**: High volume, time-sensitive, multiple recipes per shift
- **Key Metrics**: Speed, consistency, portion control

### 2. Gelato Production (`name: 'Gelato Production'`)
- **Purpose**: Makes gelato/ice cream
- **Characteristics**: Temperature-sensitive, quality-focused, batch processing
- **Key Metrics**: Temperature control, texture, flavor consistency

### 3. Confectionaries Production (`name: 'Confectionaries Production'`)
- **Purpose**: Makes confectionery items
- **Characteristics**: Precision work, decoration-focused, varied product types
- **Key Metrics**: Visual appeal, weight accuracy, shelf life

## Dashboard Architecture

### Base Department Dashboard Component

```php
<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Livewire\BranchDashboard\BaseBranchDashboard;
use App\Services\ProductionProgressService;
use App\Services\DepartmentProgressService;

abstract class BaseDepartmentDashboard extends BaseBranchDashboard
{
    public ?int $departmentId = null;
    public ?string $departmentSlug = null;
    protected ProductionProgressService $progressService;
    protected DepartmentProgressService $departmentService;

    public function mount(?string $deptSlug = null)
    {
        parent::mount();

        $this->departmentSlug = $deptSlug;
        $this->resolveDepartment();

        $this->progressService = app(ProductionProgressService::class);
        $this->departmentService = app(DepartmentProgressService::class);
    }

    protected function resolveDepartment(): void
    {
        if ($this->departmentSlug) {
            $department = Department::where('slug', $this->departmentSlug)->first();
            if ($department) {
                $this->departmentId = $department->id;
            }
        }
    }

    abstract protected function getDepartmentSpecificMetrics(): array;
    abstract protected function getDepartmentSpecificAlerts(): array;
    abstract protected function getDepartmentWorkflow(): array;

    public function getSharedData(): array
    {
        return [
            'currentShift' => $this->getCurrentShift(),
            'shiftSummary' => $this->getShiftSummary(),
            'productionRequests' => $this->getProductionRequests(),
            'itemsToCollect' => $this->getItemsToCollect(),
            'dailyProduces' => $this->getDailyProduces(),
            'progressMetrics' => $this->progressService->calculateDepartmentProgress($this->departmentId),
            'departmentMetrics' => $this->getDepartmentSpecificMetrics(),
            'departmentAlerts' => $this->getDepartmentSpecificAlerts(),
            'workflow' => $this->getDepartmentWorkflow(),
        ];
    }
}
```

## Department-Specific Implementations

### 1. Kitchen Dashboard

#### Key Features
- **Real-time Order Tracking**: Monitor preparation times for hot foods
- **Station Management**: Track progress at different kitchen stations
- **Temperature Monitoring**: Critical for food safety
- **Rush Order Alerts**: Priority handling for time-sensitive items

#### KitchenDashboard Implementation

```php
<?php

namespace App\Livewire\BranchDashboard\Production;

use App\Models\Department;
use Carbon\Carbon;

class KitchenDashboard extends BaseDepartmentDashboard
{
    protected function getDepartmentSpecificMetrics(): array
    {
        $department = Department::find($this->departmentId);

        return [
            'avg_preparation_time' => $this->calculateAvgPreparationTime(),
            'temperature_alerts' => $this->getTemperatureAlerts(),
            'station_utilization' => $this->getStationUtilization(),
            'quality_score' => $this->getQualityScore(),
            'on_time_delivery_rate' => $this->getOnTimeDeliveryRate(),
        ];
    }

    protected function getDepartmentSpecificAlerts(): array
    {
        $alerts = [];

        // Rush orders (less than 30 minutes to completion)
        $rushOrders = $this->getRushOrders();
        if ($rushOrders->count() > 0) {
            $alerts[] = [
                'type' => 'rush_order',
                'priority' => 'high',
                'message' => "⚡ {$rushOrders->count()} rush orders requiring immediate attention",
                'items' => $rushOrders
            ];
        }

        // Temperature alerts
        $tempAlerts = $this->getTemperatureAlerts();
        if ($tempAlerts->count() > 0) {
            $alerts[] = [
                'type' => 'temperature',
                'priority' => 'critical',
                'message' => "🌡️ {$tempAlerts->count()} temperature deviations detected",
                'items' => $tempAlerts
            ];
        }

        // Station bottlenecks
        $bottlenecks = $this->getStationBottlenecks();
        if ($bottlenecks->count() > 0) {
            $alerts[] = [
                'type' => 'bottleneck',
                'priority' => 'medium',
                'message' => "⚠️ {$bottlenecks->count()} station bottlenecks detected",
                'items' => $bottlenecks
            ];
        }

        return $alerts;
    }

    protected function getDepartmentWorkflow(): array
    {
        return [
            'stations' => [
                ['name' => 'Prep Station', 'color' => 'blue', 'icon' => '🥬'],
                ['name' => 'Cooking Station', 'color' => 'red', 'icon' => '👨‍🍳'],
                ['name' => 'Assembly Station', 'color' => 'green', 'icon' => '🍽️'],
                ['name' => 'Quality Check', 'color' => 'purple', 'icon' => '✅'],
                ['name' => 'Packaging', 'color' => 'orange', 'icon' => '📦'],
            ],
            'critical_path' => [
                'max_prep_time' => 45, // minutes
                'max_cook_time' => 30, // minutes
                'quality_check_required' => true,
                'temperature_monitoring' => true,
            ]
        ];
    }

    private function calculateAvgPreparationTime(): float
    {
        // Calculate average preparation time for completed items today
        return DailyProduce::whereHas('shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('produce_date', today())
        ->where('status', 'completed')
        ->avg('production_time_minutes') ?? 0;
    }

    private function getTemperatureAlerts(): Collection
    {
        // Check for temperature monitoring alerts
        return ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->where('temperature_alert', true)
        ->whereDate('created_at', today())
        ->get();
    }

    private function getStationUtilization(): array
    {
        // Calculate utilization for each kitchen station
        $stations = ['prep', 'cook', 'assembly', 'quality', 'packaging'];
        $utilization = [];

        foreach ($stations as $station) {
            $active = DailyProduce::whereHas('shift', function($q) {
                $q->where('department_id', $this->departmentId);
            })
            ->where('station', $station)
            ->where('status', 'in_progress')
            ->count();

            $capacity = $this->getStationCapacity($station);
            $utilization[$station] = $capacity > 0 ? ($active / $capacity) * 100 : 0;
        }

        return $utilization;
    }

    private function getRushOrders(): Collection
    {
        // Orders that need to be completed within 30 minutes
        return DailyProduce::whereHas('shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->where('status', 'in_progress')
        ->where('estimated_completion_time', '<=', now()->addMinutes(30))
        ->where('priority', 'high')
        ->get();
    }

    private function getStationBottlenecks(): Collection
    {
        // Stations with high queue and low utilization
        return collect($this->getStationUtilization())
            ->filter(function($utilization, $station) {
                $queue = $this->getStationQueue($station);
                return $utilization < 50 && $queue > 3; // Low utilization, high queue
            });
    }
}
```

### 2. Gelato Production Dashboard

#### Key Features
- **Temperature Control**: Continuous monitoring of freezing temperatures
- **Batch Tracking**: Detailed batch-by-batch progress
- **Texture Monitoring**: Quality control for product consistency
- **Flavor Rotation**: Track different flavors in production

#### GelatoDashboard Implementation

```php
<?php

namespace App\Livewire\BranchDashboard\Production;

class GelatoDashboard extends BaseDepartmentDashboard
{
    protected function getDepartmentSpecificMetrics(): array
    {
        return [
            'freezer_temperature' => $this->getCurrentFreezerTemp(),
            'batch_quality_score' => $this->getBatchQualityScore(),
            'flavor_inventory' => $this->getFlavorInventory(),
            'aging_status' => $this->getAgingStatus(),
            'overrun_percentage' => $this->getOverrunPercentage(),
        ];
    }

    protected function getDepartmentSpecificAlerts(): array
    {
        $alerts = [];

        // Temperature alerts (critical for gelato)
        $tempAlerts = $this->getTemperatureAlerts();
        if ($tempAlerts->count() > 0) {
            $alerts[] = [
                'type' => 'freezer_temp',
                'priority' => 'critical',
                'message' => "🧊 Freezer temperature out of range: {$tempAlerts->count()} alerts",
                'items' => $tempAlerts
            ];
        }

        // Aging alerts (gelato needs proper maturation time)
        $agingAlerts = $this->getAgingAlerts();
        if ($agingAlerts->count() > 0) {
            $alerts[] = [
                'type' => 'aging',
                'priority' => 'high',
                'message' => "⏰ {$agingAlerts->count()} batches need aging attention",
                'items' => $agingAlerts
            ];
        }

        // Flavor stock alerts
        $lowStockFlavors = $this->getLowStockFlavors();
        if ($lowStockFlavors->count() > 0) {
            $alerts[] = [
                'type' => 'flavor_stock',
                'priority' => 'medium',
                'message' => "🍦 {$lowStockFlavors->count()} flavors running low",
                'items' => $lowStockFlavors
            ];
        }

        return $alerts;
    }

    protected function getDepartmentWorkflow(): array
    {
        return [
            'stages' => [
                ['name' => 'Mixing', 'duration' => 15, 'color' => 'blue', 'icon' => '🥄'],
                ['name' => 'Pasteurization', 'duration' => 30, 'color' => 'red', 'icon' => '🔥'],
                ['name' => 'Aging', 'duration' => 240, 'color' => 'purple', 'icon' => '⏰'], // 4 hours
                ['name' => 'Freezing', 'duration' => 45, 'color' => 'cyan', 'icon' => '🧊'],
                ['name' => 'Extraction', 'duration' => 10, 'color' => 'green', 'icon' => '📦'],
            ],
            'critical_parameters' => [
                'mixing_temp' => [2, 4], // Celsius
                'aging_temp' => [2, 6],
                'freezing_temp' => [-8, -12],
                'overrun_target' => 30, // 30% air incorporation
            ]
        ];
    }

    private function getCurrentFreezerTemp(): float
    {
        // Get latest temperature reading from sensors
        return TemperatureReading::where('department_id', $this->departmentId)
            ->where('sensor_type', 'freezer')
            ->latest()
            ->value('temperature') ?? 0;
    }

    private function getBatchQualityScore(): float
    {
        // Calculate average quality score for today's batches
        return ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('created_at', today())
        ->avg('quality_score') ?? 0;
    }

    private function getAgingStatus(): array
    {
        $agingBatches = DailyProduce::whereHas('shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->where('status', 'aging')
        ->get();

        return [
            'total_aging' => $agingBatches->count(),
            'ready_for_freezing' => $agingBatches->filter(function($batch) {
                return $batch->aging_start_time && $batch->aging_start_time->addHours(4)->isPast();
            })->count(),
            'still_aging' => $agingBatches->filter(function($batch) {
                return $batch->aging_start_time && $batch->aging_start_time->addHours(4)->isFuture();
            })->count(),
        ];
    }

    private function getOverrunPercentage(): float
    {
        // Calculate average overrun (air incorporation) percentage
        return ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('created_at', today())
        ->avg('overrun_percentage') ?? 0;
    }
}
```

### 3. Confectionaries Production Dashboard

#### Key Features
- **Precision Weighing**: Accurate portion control
- **Decoration Tracking**: Quality control for visual presentation
- **Shelf Life Monitoring**: Track expiration dates
- **Ingredient Temperature**: Monitor chocolate tempering, etc.

#### ConfectionariesDashboard Implementation

```php
<?php

namespace App\Livewire\BranchDashboard\Production;

class ConfectionariesDashboard extends BaseDepartmentDashboard
{
    protected function getDepartmentSpecificMetrics(): array
    {
        return [
            'weight_accuracy' => $this->getWeightAccuracy(),
            'decoration_quality' => $this->getDecorationQuality(),
            'shelf_life_remaining' => $this->getShelfLifeRemaining(),
            'tempering_temp' => $this->getTemperingTemperature(),
            'batch_uniformity' => $this->getBatchUniformity(),
        ];
    }

    protected function getDepartmentSpecificAlerts(): array
    {
        $alerts = [];

        // Weight accuracy alerts
        $weightIssues = $this->getWeightIssues();
        if ($weightIssues->count() > 0) {
            $alerts[] = [
                'type' => 'weight_accuracy',
                'priority' => 'high',
                'message' => "⚖️ {$weightIssues->count()} batches with weight deviations",
                'items' => $weightIssues
            ];
        }

        // Shelf life alerts
        $expiringSoon = $this->getExpiringSoon();
        if ($expiringSoon->count() > 0) {
            $alerts[] = [
                'type' => 'shelf_life',
                'priority' => 'medium',
                'message' => "📅 {$expiringSoon->count()} items expiring within 24 hours",
                'items' => $expiringSoon
            ];
        }

        // Tempering alerts
        $tempAlerts = $this->getTemperingAlerts();
        if ($tempAlerts->count() > 0) {
            $alerts[] = [
                'type' => 'tempering',
                'priority' => 'critical',
                'message' => "🌡️ Chocolate tempering out of range: {$tempAlerts->count()} alerts",
                'items' => $tempAlerts
            ];
        }

        return $alerts;
    }

    protected function getDepartmentWorkflow(): array
    {
        return [
            'production_types' => [
                ['name' => 'Chocolate Molding', 'color' => 'brown', 'icon' => '🍫'],
                ['name' => 'Hard Candy', 'color' => 'red', 'icon' => '🍬'],
                ['name' => 'Gummy Candies', 'color' => 'yellow', 'icon' => '🍭'],
                ['name' => 'Decorated Cookies', 'color' => 'orange', 'icon' => '🍪'],
                ['name' => 'Packaging', 'color' => 'green', 'icon' => '📦'],
            ],
            'quality_checks' => [
                'weight_tolerance' => 0.5, // grams
                'decoration_standard' => 'A', // grade
                'shelf_life_minimum' => 7, // days
                'temperature_control' => true,
            ]
        ];
    }

    private function getWeightAccuracy(): float
    {
        // Calculate weight accuracy as percentage within tolerance
        $totalBatches = ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('created_at', today())
        ->count();

        $accurateBatches = ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('created_at', today())
        ->where('weight_deviation', '<=', 0.5) // within 0.5g tolerance
        ->count();

        return $totalBatches > 0 ? ($accurateBatches / $totalBatches) * 100 : 0;
    }

    private function getDecorationQuality(): string
    {
        // Get average decoration quality grade
        $grades = ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('created_at', today())
        ->pluck('decoration_grade');

        if ($grades->isEmpty()) return 'N/A';

        $gradeValues = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        $avgScore = $grades->map(fn($g) => $gradeValues[$g] ?? 0)->avg();

        return collect($gradeValues)->search($avgScore) ?: 'B';
    }

    private function getShelfLifeRemaining(): float
    {
        // Calculate average shelf life remaining in days
        return ProductionRecord::whereHas('dailyProduce.shift', function($q) {
            $q->where('department_id', $this->departmentId);
        })
        ->whereDate('created_at', today())
        ->avg('shelf_life_days') ?? 0;
    }
}
```

## Shared Dashboard Components

### Progress Visualization Component

```php
<!-- resources/views/livewire/branch-dashboard/production/components/progress-visualizer.blade.php -->
<div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $title }}</h4>
        <span class="text-xs px-2 py-1 rounded-full {{ $statusColor }} font-medium">
            {{ $status }}
        </span>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3 mb-2">
        <div class="bg-{{ $color }}-500 h-3 rounded-full transition-all duration-500"
             style="width: {{ $progress }}%"></div>
    </div>

    <!-- Progress Details -->
    <div class="flex justify-between text-xs text-zinc-600 dark:text-zinc-400">
        <span>{{ $current }} / {{ $total }} {{ $unit }}</span>
        <span>{{ number_format($progress, 1) }}% Complete</span>
    </div>

    <!-- Time Estimate -->
    @if($estimatedTime)
    <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
        Est. completion: {{ $estimatedTime->format('g:i A') }}
    </div>
    @endif

    <!-- Alerts -->
    @if(!empty($alerts))
    <div class="mt-2 space-y-1">
        @foreach($alerts as $alert)
        <div class="text-xs px-2 py-1 rounded {{ $alert['color'] }}">
            {{ $alert['message'] }}
        </div>
        @endforeach
    </div>
    @endif
</div>
```

### Real-time Updates

```javascript
// resources/js/production-dashboard.js
document.addEventListener('livewire:initialized', () => {
    // Subscribe to progress updates
    Echo.channel('production-progress-{{ $departmentId }}')
        .listen('ProgressUpdated', (e) => {
            // Update progress bars in real-time
            updateProgressBar(e.data.itemId, e.data.progress);
            showNotification(e.data.message, e.data.type);
        });

    // Subscribe to alerts
    Echo.channel('production-alerts-{{ $departmentId }}')
        .listen('ProductionAlert', (e) => {
            showAlert(e.data.message, e.data.priority);
        });
});
```

## Mobile Responsiveness

All department dashboards must be fully responsive:

- **Desktop**: Full feature set with detailed metrics
- **Tablet**: Condensed view with essential information
- **Mobile**: Critical alerts and progress summaries only

## Performance Optimization

### Caching Strategy
- Cache department-specific data for 2-5 minutes
- Use Redis for real-time progress updates
- Cache user permissions and department access

### Lazy Loading
- Load detailed progress data on demand
- Implement pagination for large production lists
- Use virtual scrolling for timeline views

### Background Processing
- Queue non-critical calculations
- Use WebSockets for real-time updates
- Implement progressive data loading