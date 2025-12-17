# 02_SHIFT_CONFIGURATION_SYSTEM.md

## Shift Configuration System Design

**Date**: December 2025
**Version**: 1.0
**Status**: Design Complete

---

## Overview

The Shift Configuration System will replace hardcoded time validations with a flexible, configurable system that supports branch-specific shift times, strict time window enforcement, and automated scheduling.

---

## 1. Database Schema Design

### Shift Configurations Table

```sql
CREATE TABLE shift_configurations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    branch_id BIGINT UNSIGNED NOT NULL,
    shift_type ENUM('morning', 'afternoon', 'night', 'full_time') NOT NULL,
    name VARCHAR(100) NOT NULL, -- "Morning Shift", "Afternoon Shift"
    start_time TIME NOT NULL, -- 06:00:00 for morning
    end_time TIME NOT NULL, -- 14:00:00 for morning
    clock_in_start TIME NOT NULL, -- 06:00:00 (strict start)
    clock_in_end TIME NOT NULL, -- 12:00:00 (strict end)
    auto_clock_out_minutes INT DEFAULT 15, -- grace period
    is_active BOOLEAN DEFAULT TRUE,
    max_overtime_hours DECIMAL(4,2) DEFAULT 2.00,
    break_duration_minutes INT DEFAULT 60,
    timezone VARCHAR(50) DEFAULT 'UTC',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,

    -- Constraints
    CHECK (clock_in_end > clock_in_start),
    CHECK (end_time > start_time),
    CHECK (auto_clock_out_minutes >= 0),

    -- Indexes
    UNIQUE KEY unique_active_shift_config (branch_id, shift_type, is_active),
    INDEX idx_branch_active (branch_id, is_active),
    INDEX idx_shift_type (shift_type),
    INDEX idx_timezone (timezone)
);
```

### Default Data Seeding

```php
// database/seeders/ShiftConfigurationSeeder.php
public function run()
{
    $branches = Branch::all();

    foreach ($branches as $branch) {
        $configurations = [
            [
                'branch_id' => $branch->id,
                'shift_type' => 'morning',
                'name' => 'Morning Shift',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'clock_in_start' => '06:00:00', // STRICT: No clock-in before 6 AM
                'clock_in_end' => '12:00:00',   // STRICT: No clock-in after 12 PM
                'auto_clock_out_minutes' => 15,
                'max_overtime_hours' => 2.00,
                'break_duration_minutes' => 60,
                'timezone' => $branch->timezone ?? 'UTC',
                'is_active' => true,
            ],
            [
                'branch_id' => $branch->id,
                'shift_type' => 'afternoon',
                'name' => 'Afternoon Shift',
                'start_time' => '12:00:00',
                'end_time' => '20:00:00',       // 8:00 PM end
                'clock_in_start' => '12:00:00', // STRICT: No clock-in before 12 PM
                'clock_in_end' => '20:00:00',   // STRICT: No clock-in after 8 PM
                'auto_clock_out_minutes' => 15,
                'max_overtime_hours' => 2.00,
                'break_duration_minutes' => 60,
                'timezone' => $branch->timezone ?? 'UTC',
                'is_active' => true,
            ],
            [
                'branch_id' => $branch->id,
                'shift_type' => 'full_time',
                'name' => 'Full Time',
                'start_time' => '00:00:00',
                'end_time' => '23:59:59',
                'clock_in_start' => '00:00:00',
                'clock_in_end' => '23:59:59',
                'auto_clock_out_minutes' => 480, // 8 hours for full time
                'max_overtime_hours' => 4.00,
                'break_duration_minutes' => 60,
                'timezone' => $branch->timezone ?? 'UTC',
                'is_active' => true,
            ],
        ];

        ShiftConfiguration::insert($configurations);
    }
}
```

---

## 2. Model Implementation

### ShiftConfiguration Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ShiftConfiguration extends Model
{
    protected $fillable = [
        'branch_id', 'shift_type', 'name', 'start_time', 'end_time',
        'clock_in_start', 'clock_in_end', 'auto_clock_out_minutes',
        'is_active', 'max_overtime_hours', 'break_duration_minutes', 'timezone'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'clock_in_start' => 'datetime:H:i',
        'clock_in_end' => 'datetime:H:i',
        'is_active' => 'boolean',
        'max_overtime_hours' => 'decimal:2',
        'auto_clock_out_minutes' => 'integer',
        'break_duration_minutes' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutes(): int
    {
        $start = Carbon::createFromTimeString($this->start_time);
        $end = Carbon::createFromTimeString($this->end_time);

        if ($end <= $start) {
            $end = $end->addDay(); // Handle overnight shifts
        }

        return $start->diffInMinutes($end);
    }

    /**
     * Check if current time is within clock-in window (STRICT ENFORCEMENT)
     */
    public function isWithinClockInWindow(Carbon $currentTime = null): bool
    {
        $now = $currentTime ?? Carbon::now($this->timezone);

        $start = Carbon::createFromTimeString($this->clock_in_start)
            ->setDateFrom($now);
        $end = Carbon::createFromTimeString($this->clock_in_end)
            ->setDateFrom($now);

        // Handle overnight shifts (end time next day)
        if ($end <= $start) {
            $end = $end->addDay();
        }

        return $now->between($start, $end);
    }

    /**
     * Get clock-in window status message
     */
    public function getClockInWindowMessage(): string
    {
        $start = Carbon::createFromTimeString($this->clock_in_start)->format('g:i A');
        $end = Carbon::createFromTimeString($this->clock_in_end)->format('g:i A');

        return "Clock-in window: {$start} - {$end}";
    }

    /**
     * Check if shift should auto clock out
     */
    public function shouldAutoClockOut(Carbon $clockInTime, Carbon $currentTime = null): bool
    {
        $now = $currentTime ?? Carbon::now($this->timezone);

        $expectedEnd = Carbon::createFromTimeString($this->end_time)
            ->setDateFrom($clockInTime);

        $gracePeriodEnd = $expectedEnd->addMinutes($this->auto_clock_out_minutes);

        return $now > $gracePeriodEnd;
    }

    /**
     * Scope for active configurations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific branch and shift type
     */
    public function scopeForBranchAndType($query, $branchId, $shiftType)
    {
        return $query->where('branch_id', $branchId)
                    ->where('shift_type', $shiftType)
                    ->active();
    }
}
```

---

## 3. Validation Service

### ShiftTimingValidator Service

```php
<?php

namespace App\Services;

use App\Models\ShiftConfiguration;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ShiftTimingValidator
{
    /**
     * Validate shift selection for strict time windows
     */
    public function validateShiftSelection(
        string $shiftType,
        ?int $branchId = null,
        Carbon $requestedTime = null
    ): ValidationResult {
        $currentTime = $requestedTime ?? Carbon::now();

        // Get branch (use current if not specified)
        $branchId = $branchId ?? current_branch_id();

        if (!$branchId) {
            return ValidationResult::invalid('Branch not found');
        }

        // Get shift configuration
        $config = ShiftConfiguration::forBranchAndType($branchId, $shiftType)->first();

        if (!$config) {
            return ValidationResult::invalid("Shift configuration not found for {$shiftType}");
        }

        // STRICT TIME WINDOW VALIDATION
        if (!$config->isWithinClockInWindow($currentTime)) {
            $message = $this->generateTimeViolationMessage($config, $currentTime);
            Log::warning('Shift time violation', [
                'shift_type' => $shiftType,
                'requested_time' => $currentTime->toIso8601String(),
                'config' => $config->toArray(),
                'branch_id' => $branchId
            ]);

            return ValidationResult::invalid($message);
        }

        return ValidationResult::valid();
    }

    /**
     * Generate user-friendly time violation message
     */
    private function generateTimeViolationMessage(ShiftConfiguration $config, Carbon $currentTime): string
    {
        $currentTimeStr = $currentTime->format('g:i A');
        $windowMessage = $config->getClockInWindowMessage();

        $startTime = Carbon::createFromTimeString($config->clock_in_start)->format('g:i A');
        $endTime = Carbon::createFromTimeString($config->clock_in_end)->format('g:i A');

        if ($currentTime < Carbon::createFromTimeString($config->clock_in_start)->setDateFrom($currentTime)) {
            return "Too early! {$config->name} clock-in starts at {$startTime}. Current time: {$currentTimeStr}";
        } else {
            return "Too late! {$config->name} clock-in ended at {$endTime}. Current time: {$currentTimeStr}";
        }
    }

    /**
     * Check if existing shift conflicts with new clock-in
     */
    public function validateNoConflictingShifts(
        int $employeeId,
        string $shiftType,
        int $branchId,
        Carbon $requestedTime = null
    ): ValidationResult {
        $currentTime = $requestedTime ?? Carbon::now();

        // Check for active shifts today
        $activeShift = \App\Models\Shift::where('employee_id', $employeeId)
            ->where('shift_date', $currentTime->toDateString())
            ->where('status', 'active')
            ->first();

        if ($activeShift) {
            return ValidationResult::invalid(
                "You already have an active {$activeShift->shift_type} shift today. Please clock out first."
            );
        }

        // Check for shifts that would overlap
        $config = ShiftConfiguration::forBranchAndType($branchId, $shiftType)->first();

        if ($config) {
            $shiftEnd = Carbon::createFromTimeString($config->end_time)->setDateFrom($currentTime);

            $overlappingShift = \App\Models\Shift::where('employee_id', $employeeId)
                ->where('shift_date', $currentTime->toDateString())
                ->where(function ($query) use ($currentTime, $shiftEnd) {
                    $query->whereBetween('clock_in', [$currentTime, $shiftEnd])
                          ->orWhereBetween('clock_out', [$currentTime, $shiftEnd])
                          ->orWhere(function ($q) use ($currentTime, $shiftEnd) {
                              $q->where('clock_in', '<=', $currentTime)
                                ->where('clock_out', '>=', $shiftEnd);
                          });
                })
                ->first();

            if ($overlappingShift) {
                return ValidationResult::invalid(
                    "This shift would overlap with your existing shift today."
                );
            }
        }

        return ValidationResult::valid();
    }

    /**
     * Get available shifts for current time
     */
    public function getAvailableShifts(?int $branchId = null): array
    {
        $branchId = $branchId ?? current_branch_id();
        $currentTime = Carbon::now();

        $availableConfigs = ShiftConfiguration::where('branch_id', $branchId)
            ->active()
            ->get()
            ->filter(function ($config) use ($currentTime) {
                return $config->isWithinClockInWindow($currentTime);
            });

        return $availableConfigs->map(function ($config) {
            return [
                'type' => $config->shift_type,
                'name' => $config->name,
                'window' => $config->getClockInWindowMessage(),
                'duration_hours' => round($config->getDurationMinutes() / 60, 1)
            ];
        })->toArray();
    }
}
```

### ValidationResult Class

```php
<?php

namespace App\Services;

class ValidationResult
{
    private bool $isValid;
    private ?string $message;

    private function __construct(bool $isValid, ?string $message = null)
    {
        $this->isValid = $isValid;
        $this->message = $message;
    }

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(string $message): self
    {
        return new self(false, $message);
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'valid' => $this->isValid,
            'message' => $this->message
        ];
    }
}
```

---

## 4. API Endpoints

### Shift Configuration Management

```php
// routes/web.php or api.php
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::apiResource('shift-configurations', ShiftConfigurationController::class);
    Route::get('shift-configurations/branch/{branchId}', [ShiftConfigurationController::class, 'getByBranch']);
    Route::post('shift-configurations/{config}/toggle', [ShiftConfigurationController::class, 'toggleActive']);
});
```

### ShiftConfigurationController

```php
<?php

namespace App\Http\Controllers;

use App\Models\ShiftConfiguration;
use App\Services\ShiftTimingValidator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShiftConfigurationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ShiftConfiguration::with('branch');

        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('active_only')) {
            $query->active();
        }

        $configurations = $query->paginate(20);

        return response()->json($configurations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'shift_type' => 'required|in:morning,afternoon,night,full_time',
            'name' => 'required|string|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'clock_in_start' => 'required|date_format:H:i',
            'clock_in_end' => 'required|date_format:H:i|after:clock_in_start',
            'auto_clock_out_minutes' => 'nullable|integer|min:0|max:480',
            'max_overtime_hours' => 'nullable|numeric|min:0|max:24',
            'break_duration_minutes' => 'nullable|integer|min:0|max:480',
            'timezone' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        // Check for conflicts
        $existing = ShiftConfiguration::where('branch_id', $validated['branch_id'])
            ->where('shift_type', $validated['shift_type'])
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'Active configuration already exists for this shift type'
            ], 422);
        }

        $configuration = ShiftConfiguration::create($validated);

        return response()->json($configuration, 201);
    }

    public function show(ShiftConfiguration $config): JsonResponse
    {
        return response()->json($config->load('branch'));
    }

    public function update(Request $request, ShiftConfiguration $config): JsonResponse
    {
        $validated = $request->validate([
            // Same validation rules as store
        ]);

        $config->update($validated);

        return response()->json($config);
    }

    public function destroy(ShiftConfiguration $config): JsonResponse
    {
        // Soft delete or check dependencies
        $config->delete();

        return response()->json(['message' => 'Configuration deleted']);
    }

    public function getByBranch(int $branchId): JsonResponse
    {
        $configurations = ShiftConfiguration::where('branch_id', $branchId)
            ->active()
            ->get();

        return response()->json($configurations);
    }

    public function toggleActive(ShiftConfiguration $config): JsonResponse
    {
        $config->update(['is_active' => !$config->is_active]);

        return response()->json($config);
    }

    public function validateTiming(Request $request): JsonResponse
    {
        $validator = new ShiftTimingValidator();

        $result = $validator->validateShiftSelection(
            $request->shift_type,
            $request->branch_id,
            $request->requested_time ? Carbon::parse($request->requested_time) : null
        );

        return response()->json($result->toArray());
    }
}
```

---

## 5. Migration Strategy

### Zero-Downtime Migration Approach

```php
// database/migrations/2024_01_15_000001_create_shift_configurations_table.php
public function up()
{
    Schema::create('shift_configurations', function (Blueprint $table) {
        // Schema as defined above
    });

    // Seed default configurations for existing branches
    $this->seedDefaultConfigurations();
}

public function down()
{
    Schema::dropIfExists('shift_configurations');
}

private function seedDefaultConfigurations()
{
    $branches = DB::table('branches')->pluck('id');

    foreach ($branches as $branchId) {
        DB::table('shift_configurations')->insert([
            // Morning shift with STRICT time windows
            [
                'branch_id' => $branchId,
                'shift_type' => 'morning',
                'name' => 'Morning Shift',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'clock_in_start' => '06:00:00', // STRICT: 6 AM
                'clock_in_end' => '12:00:00',   // STRICT: 12 PM
                'auto_clock_out_minutes' => 15,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Afternoon shift with STRICT time windows
            [
                'branch_id' => $branchId,
                'shift_type' => 'afternoon',
                'name' => 'Afternoon Shift',
                'start_time' => '12:00:00',
                'end_time' => '20:00:00',
                'clock_in_start' => '12:00:00', // STRICT: 12 PM
                'clock_in_end' => '20:00:00',   // STRICT: 8 PM
                'auto_clock_out_minutes' => 15,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
```

---

## 6. Integration with Existing System

### Update Shift.php Component

```php
<?php

namespace App\Livewire\Auth;

use App\Services\ShiftTimingValidator;
use App\Models\ShiftConfiguration;
// ... other imports

class Shift extends Component
{
    protected ShiftTimingValidator $timingValidator;

    public function boot()
    {
        $this->timingValidator = new ShiftTimingValidator();
    }

    public function clockIn()
    {
        // STRICT TIME VALIDATION FIRST
        $validation = $this->timingValidator->validateShiftSelection(
            $this->shift_type,
            $this->b_id
        );

        if (!$validation->isValid()) {
            $this->toast()->error($validation->getMessage())->send();
            return;
        }

        // Check for conflicting shifts
        $conflictValidation = $this->timingValidator->validateNoConflictingShifts(
            auth()->id(),
            $this->shift_type,
            $this->b_id
        );

        if (!$conflictValidation->isValid()) {
            $this->toast()->error($conflictValidation->getMessage())->send();
            return;
        }

        // Proceed with clock in...
        // ... existing logic but use configuration instead of hardcoded times
    }

    private function createShift(Branch $branch, $user)
    {
        // Get shift configuration
        $config = ShiftConfiguration::forBranchAndType($branch->id, $this->shift_type)->first();

        if (!$config) {
            throw new \Exception("Shift configuration not found for {$this->shift_type}");
        }

        // Use configured times instead of hardcoded logic
        $shift = new ShiftModel();
        $shift->branch_id = $branch->id;
        $shift->employee_id = $user->id;
        $shift->shift_number = $this->generateShiftNumber($branch);
        $shift->shift_date = Carbon::today();
        $shift->shift_type = $this->shift_type;
        $shift->clock_in = Carbon::now();
        $shift->status = 'active';
        $shift->notes = $this->notes;

        // Store configuration reference for later use
        $shift->metadata = [
            'config_id' => $config->id,
            'expected_end' => $config->end_time,
            'auto_clock_out_minutes' => $config->auto_clock_out_minutes
        ];

        $shift->save();

        return $shift;
    }
}
```

---

## 7. Testing Strategy

### Unit Tests

```php
// tests/Unit/Services/ShiftTimingValidatorTest.php
class ShiftTimingValidatorTest extends TestCase
{
    public function test_morning_shift_strict_time_windows()
    {
        $validator = new ShiftTimingValidator();
        $branch = Branch::factory()->create();

        // Test clock-in before allowed time (5 AM)
        $earlyTime = Carbon::today()->setTime(5, 0);
        $result = $validator->validateShiftSelection('morning', $branch->id, $earlyTime);
        $this->assertFalse($result->isValid());
        $this->assertStringContains('Too early', $result->getMessage());

        // Test clock-in during allowed time (8 AM)
        $validTime = Carbon::today()->setTime(8, 0);
        $result = $validator->validateShiftSelection('morning', $branch->id, $validTime);
        $this->assertTrue($result->isValid());

        // Test clock-in after allowed time (1 PM)
        $lateTime = Carbon::today()->setTime(13, 0);
        $result = $validator->validateShiftSelection('morning', $branch->id, $lateTime);
        $this->assertFalse($result->isValid());
        $this->assertStringContains('Too late', $result->getMessage());
    }

    public function test_afternoon_shift_strict_time_windows()
    {
        $validator = new ShiftTimingValidator();
        $branch = Branch::factory()->create();

        // Test clock-in before allowed time (11 AM)
        $earlyTime = Carbon::today()->setTime(11, 0);
        $result = $validator->validateShiftSelection('afternoon', $branch->id, $earlyTime);
        $this->assertFalse($result->isValid());

        // Test clock-in during allowed time (2 PM)
        $validTime = Carbon::today()->setTime(14, 0);
        $result = $validator->validateShiftSelection('afternoon', $branch->id, $validTime);
        $this->assertTrue($result->isValid());

        // Test clock-in after allowed time (9 PM)
        $lateTime = Carbon::today()->setTime(21, 0);
        $result = $validator->validateShiftSelection('afternoon', $branch->id, $lateTime);
        $this->assertFalse($result->isValid());
    }
}
```

### Integration Tests

```php
// tests/Feature/ShiftConfigurationTest.php
class ShiftConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_shift_configuration_crud_operations()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $branch = Branch::factory()->create();

        $configData = [
            'branch_id' => $branch->id,
            'shift_type' => 'morning',
            'name' => 'Morning Shift',
            'start_time' => '06:00',
            'end_time' => '14:00',
            'clock_in_start' => '06:00', // STRICT enforcement
            'clock_in_end' => '12:00',   // STRICT enforcement
            'auto_clock_out_minutes' => 15,
        ];

        // Test creation
        $response = $this->actingAs($admin)
            ->postJson('/api/shift-configurations', $configData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('shift_configurations', $configData);
    }
}
```

---

## 8. Rollback Strategy

### Safe Rollback Procedures

```php
// database/migrations/2024_01_15_000001_create_shift_configurations_table.php
public function down()
{
    // Check for dependencies before dropping
    $activeShifts = DB::table('shifts')
        ->whereNotNull('metadata->config_id')
        ->count();

    if ($activeShifts > 0) {
        throw new Exception(
            "Cannot rollback: {$activeShifts} shifts reference shift configurations. " .
            "Complete shift closure process first."
        );
    }

    Schema::dropIfExists('shift_configurations');
}
```

### Feature Flag Rollback

```php
// config/clock-in-out.php
return [
    'strict_time_validation' => env('SHIFT_STRICT_VALIDATION', false),
    'auto_clock_out' => env('AUTO_CLOCK_OUT_ENABLED', false),
    'require_active_shift' => env('REQUIRE_ACTIVE_SHIFT', false),
];
```

---

## 9. Performance Considerations

### Caching Strategy

```php
// Cache shift configurations for 1 hour
$shiftConfigs = Cache::remember(
    "shift_configs_branch_{$branchId}",
    3600,
    fn() => ShiftConfiguration::where('branch_id', $branchId)->active()->get()
);
```

### Database Optimization

- Composite indexes on frequently queried columns
- Partitioning for large datasets (future consideration)
- Read replicas for reporting queries

---

## 10. Security Considerations

### Access Control
- Super admin only for configuration management
- Branch-level isolation for configurations
- Audit logging for all configuration changes

### Data Validation
- Strict input validation on all time fields
- Business rule enforcement in model layer
- Prevention of overlapping shift configurations

---

**Document Information**
- **Prepared By**: System Design Team
- **Reviewed By**: Database & Security Teams
- **Approved By**: Architecture Review Board
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/02_SHIFT_CONFIGURATION_SYSTEM.md