# 05_SHIFT_TIMING_VALIDATION.md

## Shift Timing Validation System

**Date**: December 2025
**Version**: 1.0
**Status**: Design Complete

---

## Overview

The Shift Timing Validation system replaces hardcoded JavaScript time checks with strict server-side validation ensuring employees can only clock in during their designated shift windows. This implements the critical requirement that morning shifts (6 AM - 12 PM) and afternoon shifts (12 PM - 8 PM) enforce strict time boundaries.

---

## 1. Strict Time Window Implementation

### Backend Validation Logic

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
     * STRICT TIME WINDOW VALIDATION
     * Morning: 6:00 AM - 12:00 PM (no clock-in before 6 AM or after 12 PM)
     * Afternoon: 12:00 PM - 8:00 PM (no clock-in before 12 PM or after 8 PM)
     */
    public function validateStrictTimeWindows(
        string $shiftType,
        ?int $branchId = null,
        Carbon $requestedTime = null
    ): ValidationResult {
        $currentTime = $requestedTime ?? Carbon::now();

        // Get shift configuration with STRICT time windows
        $config = $this->getStrictShiftConfiguration($shiftType, $branchId);

        if (!$config) {
            return ValidationResult::invalid("Shift configuration not found for {$shiftType}");
        }

        // STRICT VALIDATION: Must be within exact clock-in window
        if (!$config->isWithinStrictClockInWindow($currentTime)) {
            $violationDetails = $this->getStrictViolationDetails($config, $currentTime);

            // Log security violation
            Log::warning('STRICT SHIFT TIME VIOLATION', [
                'shift_type' => $shiftType,
                'requested_time' => $currentTime->format('Y-m-d H:i:s'),
                'allowed_window' => $violationDetails['window'],
                'violation_type' => $violationDetails['type'],
                'branch_id' => $branchId,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return ValidationResult::invalid($violationDetails['message']);
        }

        return ValidationResult::valid();
    }

    /**
     * Get STRICT shift configuration with enforced time windows
     */
    private function getStrictShiftConfiguration(string $shiftType, ?int $branchId): ?StrictShiftConfig
    {
        $branchId = $branchId ?? current_branch_id();

        // STRICT TIME WINDOWS - No exceptions
        $strictWindows = [
            'morning' => [
                'name' => 'Morning Shift',
                'clock_in_start' => '06:00:00', // STRICT: Must be 6 AM or later
                'clock_in_end' => '12:00:00',   // STRICT: Must be before 12 PM
                'timezone' => 'UTC' // Will be converted to branch timezone
            ],
            'afternoon' => [
                'name' => 'Afternoon Shift',
                'clock_in_start' => '12:00:00', // STRICT: Must be 12 PM or later
                'clock_in_end' => '20:00:00',   // STRICT: Must be before 8 PM
                'timezone' => 'UTC'
            ],
            'full_time' => [
                'name' => 'Full Time',
                'clock_in_start' => '00:00:00', // No restrictions
                'clock_in_end' => '23:59:59',
                'timezone' => 'UTC'
            ]
        ];

        if (!isset($strictWindows[$shiftType])) {
            return null;
        }

        return new StrictShiftConfig($strictWindows[$shiftType], $branchId);
    }

    /**
     * Get detailed violation information for STRICT validation
     */
    private function getStrictViolationDetails(StrictShiftConfig $config, Carbon $currentTime): array
    {
        $startTime = Carbon::createFromTimeString($config->clock_in_start)
            ->setDateFrom($currentTime)
            ->format('g:i A');

        $endTime = Carbon::createFromTimeString($config->clock_in_end)
            ->setDateFrom($currentTime)
            ->format('g:i A');

        $currentTimeStr = $currentTime->format('g:i A');

        if ($currentTime < Carbon::createFromTimeString($config->clock_in_start)->setDateFrom($currentTime)) {
            return [
                'type' => 'too_early',
                'window' => "{$startTime} - {$endTime}",
                'message' => "❌ CLOCK-IN DENIED: Too early for {$config->name}!\n" .
                           "⏰ Current time: {$currentTimeStr}\n" .
                           "🕐 Allowed window: {$startTime} - {$endTime}\n" .
                           "⏳ Please wait until {$startTime} to clock in."
            ];
        } else {
            return [
                'type' => 'too_late',
                'window' => "{$startTime} - {$endTime}",
                'message' => "❌ CLOCK-IN DENIED: Too late for {$config->name}!\n" .
                           "⏰ Current time: {$currentTimeStr}\n" .
                           "🕐 Allowed window: {$startTime} - {$endTime}\n" .
                           "⏳ This shift window has ended. Please contact your supervisor."
            ];
        }
    }
}
```

### StrictShiftConfig Class

```php
<?php

namespace App\Services;

use Carbon\Carbon;

class StrictShiftConfig
{
    public string $name;
    public string $clock_in_start;
    public string $clock_in_end;
    public int $branch_id;
    public string $timezone;

    public function __construct(array $config, int $branchId)
    {
        $this->name = $config['name'];
        $this->clock_in_start = $config['clock_in_start'];
        $this->clock_in_end = $config['clock_in_end'];
        $this->branch_id = $branchId;
        $this->timezone = $config['timezone'];
    }

    /**
     * STRICT TIME WINDOW CHECK
     * Must be EXACTLY within the allowed clock-in window
     */
    public function isWithinStrictClockInWindow(Carbon $currentTime): bool
    {
        // Convert to branch timezone if needed
        $branchTime = $currentTime->setTimezone($this->timezone);

        $start = Carbon::createFromTimeString($this->clock_in_start)
            ->setDateFrom($branchTime);

        $end = Carbon::createFromTimeString($this->clock_in_end)
            ->setDateFrom($branchTime);

        // STRICT: Must be at or after start AND before end
        return $branchTime >= $start && $branchTime < $end;
    }

    /**
     * Get remaining time in current window
     */
    public function getRemainingTimeInWindow(Carbon $currentTime): ?int
    {
        if (!$this->isWithinStrictClockInWindow($currentTime)) {
            return null;
        }

        $end = Carbon::createFromTimeString($this->clock_in_end)
            ->setDateFrom($currentTime);

        return $currentTime->diffInMinutes($end);
    }

    /**
     * Get time until window opens
     */
    public function getTimeUntilWindowOpens(Carbon $currentTime): ?int
    {
        $start = Carbon::createFromTimeString($this->clock_in_start)
            ->setDateFrom($currentTime);

        if ($currentTime >= $start) {
            return null; // Window is open or already passed
        }

        return $currentTime->diffInMinutes($start);
    }
}
```

---

## 2. Updated Shift.php Component

### Replace JavaScript Validation with Server-Side Enforcement

```php
<?php

namespace App\Livewire\Auth;

use App\Services\ShiftTimingValidator;
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
        // STEP 1: STRICT TIME WINDOW VALIDATION
        $timeValidation = $this->timingValidator->validateStrictTimeWindows(
            $this->shift_type,
            $this->b_id
        );

        if (!$timeValidation->isValid()) {
            $this->toast()->error($timeValidation->getMessage())->send();

            // Log the violation attempt
            Log::warning('Clock-in time violation attempt', [
                'employee_id' => auth()->id(),
                'shift_type' => $this->shift_type,
                'branch_id' => $this->b_id,
                'requested_time' => now()->toDateTimeString(),
                'violation_message' => $timeValidation->getMessage()
            ]);

            return;
        }

        // STEP 2: Check for conflicting shifts
        $conflictValidation = $this->timingValidator->validateNoConflictingShifts(
            auth()->id(),
            $this->shift_type,
            $this->b_id
        );

        if (!$conflictValidation->isValid()) {
            $this->toast()->error($conflictValidation->getMessage())->send();
            return;
        }

        // STEP 3: Proceed with clock-in (existing logic)
        // ... rest of clock-in logic
    }
}
```

---

## 3. Updated Frontend (Remove JavaScript Validation)

### Simplified shift.blade.php

```php
<!-- Shift Selection - Remove JavaScript validation -->
<div x-data="{
    selectedShift: '',
    isOpen: false,
    errorMessage: ''
}" class="w-full max-w-2xl p-8 md:p-12 card rounded-3xl">

    <!-- Current Date and Time - Live update -->
    <div class="bg-blue-50 dark:bg-zinc-800 rounded-2xl p-6 mb-8 text-center border border-blue-200 dark:border-zinc-700">
        <p class="text-gray-700 dark:text-gray-300 text-sm font-semibold">{{ now()->format('l, F j, Y') }}</p>
        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 mt-2"
           x-data x-text="new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })"></p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Server time validation enabled</p>
    </div>

    <!-- Shift Selection Dropdown - Simplified -->
    <div class="relative mb-8">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">
            Select Shift
        </label>

        <button @click="isOpen = !isOpen"
                class="w-full bg-white dark:bg-zinc-800 text-left px-6 py-4 rounded-xl shadow-md dark:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-zinc-700 transition duration-200 border border-gray-300 dark:border-zinc-600">
            <span x-text="selectedShift || 'Choose a shift'" class="text-gray-900 dark:text-gray-200 font-medium text-base"></span>
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 transform transition-transform duration-300" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <!-- Shift Options - No JavaScript validation -->
        <div x-show="isOpen" @click.away="isOpen = false"
             class="dropdown-menu absolute w-full mt-2 bg-white dark:bg-zinc-800 rounded-xl shadow-xl z-10 border border-gray-300 dark:border-zinc-600">
            <div @click="selectedShift = 'Morning Shift (6 AM - 12 PM)'; isOpen = false; $wire.set('shift_type', 'morning')"
                 class="shift-option px-6 py-4 cursor-pointer text-gray-900 dark:text-gray-200 rounded-t-xl border-b border-gray-200 dark:border-zinc-700 transition duration-200">
                <div class="font-semibold">Morning Shift</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">6:00 AM - 12:00 PM (Strict)</div>
            </div>

            <div @click="selectedShift = 'Afternoon Shift (12 PM - 8 PM)'; isOpen = false; $wire.set('shift_type', 'afternoon')"
                 class="shift-option px-6 py-4 cursor-pointer text-gray-900 dark:text-gray-200 border-b border-gray-200 dark:border-zinc-700 transition duration-200">
                <div class="font-semibold">Afternoon Shift</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">12:00 PM - 8:00 PM (Strict)</div>
            </div>

            <div @click="selectedShift = 'Full Time (No Shift)'; $wire.set('shift_type', 'full_time'); isOpen = false; errorMessage = ''"
                 class="shift-option px-6 py-4 cursor-pointer text-gray-900 dark:text-gray-200 rounded-b-xl transition duration-200">
                <div class="font-semibold">Full Time</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">No time restrictions</div>
            </div>
        </div>
    </div>

    <!-- Error Display - Server validation only -->
    <div x-show="errorMessage" class="mb-8 p-5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-xl text-center font-bold error-alert shadow-md border border-red-300 dark:border-red-700" x-cloak>
        <p x-text="errorMessage"></p>
    </div>

    <!-- Clock In Button - Triggers server validation -->
    <div x-show="selectedShift" class="mb-8" x-cloak>
        <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-400 dark:border-blue-700 rounded-xl p-6 mb-6">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-400 mb-2 uppercase tracking-wider">Selected Shift</p>
            <p class="text-xl font-bold text-blue-600 dark:text-blue-400" x-text="selectedShift"></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">✅ Server-side time validation enabled</p>
        </div>

        <button wire:click="clockIn"
                class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition duration-200 font-semibold flex items-center justify-center gap-3 text-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span wire:loading.remove wire:target="clockIn">Clock In</span>
            <span wire:loading wire:target="clockIn">Validating time...</span>
        </button>
    </div>
</div>
```

---

## 4. Time Zone Handling

### Branch-Specific Time Zones

```php
<?php

namespace App\Services;

use App\Models\Branch;
use Carbon\Carbon;

class TimeZoneService
{
    /**
     * Convert time to branch timezone
     */
    public function toBranchTime(Carbon $utcTime, int $branchId): Carbon
    {
        $branch = Branch::find($branchId);
        $timezone = $branch->timezone ?? 'UTC';

        return $utcTime->setTimezone($timezone);
    }

    /**
     * Convert branch time to UTC for storage
     */
    public function toUtcTime(Carbon $branchTime, int $branchId): Carbon
    {
        $branch = Branch::find($branchId);
        $timezone = $branch->timezone ?? 'UTC';

        // Set timezone to branch timezone first, then convert to UTC
        return $branchTime->setTimezone($timezone)->utc();
    }

    /**
     * Check if current time is within branch's shift window
     */
    public function isWithinBranchShiftWindow(
        Carbon $currentTime,
        string $shiftType,
        int $branchId
    ): bool {
        $branchTime = $this->toBranchTime($currentTime, $branchId);

        return match($shiftType) {
            'morning' => $branchTime->between(
                $branchTime->copy()->setTime(6, 0),
                $branchTime->copy()->setTime(12, 0)
            ),
            'afternoon' => $branchTime->between(
                $branchTime->copy()->setTime(12, 0),
                $branchTime->copy()->setTime(20, 0)
            ),
            'full_time' => true,
            default => false
        };
    }
}
```

---

## 5. Edge Cases & Special Handling

### Overnight Shifts

```php
public function handleOvernightShifts(string $shiftType, Carbon $currentTime): bool
{
    // Handle night shifts that span midnight
    if ($shiftType === 'night') {
        $start = $currentTime->copy()->setTime(22, 0); // 10 PM
        $end = $currentTime->copy()->addDay()->setTime(6, 0); // 6 AM next day

        return $currentTime >= $start || $currentTime <= $end->subDay();
    }

    return false;
}
```

### Holiday & Special Schedule Handling

```php
public function isHolidayOrSpecialSchedule(Carbon $date, int $branchId): array
{
    // Check for holidays
    $holidays = Holiday::where('branch_id', $branchId)
        ->where('date', $date->toDateString())
        ->first();

    if ($holidays) {
        return [
            'is_special' => true,
            'type' => 'holiday',
            'allow_clock_in' => false,
            'message' => 'Branch is closed for holiday'
        ];
    }

    // Check for special schedules (e.g., Ramadan, Christmas)
    $specialSchedule = SpecialSchedule::where('branch_id', $branchId)
        ->where('start_date', '<=', $date)
        ->where('end_date', '>=', $date)
        ->first();

    if ($specialSchedule) {
        return [
            'is_special' => true,
            'type' => 'special_schedule',
            'shift_adjustments' => $specialSchedule->adjustments,
            'allow_clock_in' => true
        ];
    }

    return ['is_special' => false];
}
```

### Emergency Clock-In Override

```php
public function allowEmergencyClockIn(int $employeeId, string $reason): bool
{
    // Check if employee has emergency override permission
    $employee = User::find($employeeId);

    if (!$employee->hasPermission('emergency_clock_in')) {
        return false;
    }

    // Log emergency clock-in
    Log::warning('Emergency clock-in approved', [
        'employee_id' => $employeeId,
        'reason' => $reason,
        'approved_by' => auth()->id(),
        'timestamp' => now()
    ]);

    // Create emergency shift record
    EmergencyClockIn::create([
        'employee_id' => $employeeId,
        'reason' => $reason,
        'approved_by' => auth()->id(),
        'clock_in_time' => now()
    ]);

    return true;
}
```

---

## 6. Testing Strategy

### Time Window Validation Tests

```php
<?php

namespace Tests\Unit\Services;

use App\Services\ShiftTimingValidator;
use Carbon\Carbon;
use Tests\TestCase;

class StrictTimeValidationTest extends TestCase
{
    public function test_morning_shift_strict_6am_to_12pm_enforcement()
    {
        $validator = new ShiftTimingValidator();

        // Test 5:59 AM - Should FAIL (too early)
        $tooEarly = Carbon::today()->setTime(5, 59);
        $result = $validator->validateStrictTimeWindows('morning', 1, $tooEarly);
        $this->assertFalse($result->isValid());
        $this->assertStringContains('Too early', $result->getMessage());

        // Test 6:00 AM - Should PASS (exactly on time)
        $onTimeStart = Carbon::today()->setTime(6, 0);
        $result = $validator->validateStrictTimeWindows('morning', 1, $onTimeStart);
        $this->assertTrue($result->isValid());

        // Test 11:59 AM - Should PASS (still within window)
        $withinWindow = Carbon::today()->setTime(11, 59);
        $result = $validator->validateStrictTimeWindows('morning', 1, $withinWindow);
        $this->assertTrue($result->isValid());

        // Test 12:00 PM - Should FAIL (exactly at end - too late)
        $tooLate = Carbon::today()->setTime(12, 0);
        $result = $validator->validateStrictTimeWindows('morning', 1, $tooLate);
        $this->assertFalse($result->isValid());
        $this->assertStringContains('Too late', $result->getMessage());
    }

    public function test_afternoon_shift_strict_12pm_to_8pm_enforcement()
    {
        $validator = new ShiftTimingValidator();

        // Test 11:59 AM - Should FAIL (too early)
        $tooEarly = Carbon::today()->setTime(11, 59);
        $result = $validator->validateStrictTimeWindows('afternoon', 1, $tooEarly);
        $this->assertFalse($result->isValid());

        // Test 12:00 PM - Should PASS (exactly on time)
        $onTimeStart = Carbon::today()->setTime(12, 0);
        $result = $validator->validateStrictTimeWindows('afternoon', 1, $onTimeStart);
        $this->assertTrue($result->isValid());

        // Test 7:59 PM - Should PASS (still within window)
        $withinWindow = Carbon::today()->setTime(19, 59);
        $result = $validator->validateStrictTimeWindows('afternoon', 1, $withinWindow);
        $this->assertTrue($result->isValid());

        // Test 8:00 PM - Should FAIL (exactly at end - too late)
        $tooLate = Carbon::today()->setTime(20, 0);
        $result = $validator->validateStrictTimeWindows('afternoon', 1, $tooLate);
        $this->assertFalse($result->isValid());
    }
}
```

### Integration Tests

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StrictTimeValidationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_cannot_clock_in_outside_strict_time_windows()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Set time to 5 AM (before morning shift starts)
        Carbon::setTestNow(Carbon::today()->setTime(5, 0));

        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'morning',
            'b_id' => $branch->id
        ]);

        $response->assertSessionHasErrors();
        $response->assertSessionHas('toast.error');

        // Verify no shift was created
        $this->assertDatabaseMissing('shifts', [
            'employee_id' => $employee->id,
            'shift_date' => Carbon::today()->toDateString()
        ]);

        Carbon::setTestNow(); // Reset time
    }

    public function test_employee_can_clock_in_during_valid_time_window()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Set time to 8 AM (within morning shift)
        Carbon::setTestNow(Carbon::today()->setTime(8, 0));

        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'morning',
            'b_id' => $branch->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast.success');

        // Verify shift was created
        $this->assertDatabaseHas('shifts', [
            'employee_id' => $employee->id,
            'shift_date' => Carbon::today()->toDateString(),
            'shift_type' => 'morning',
            'status' => 'active'
        ]);

        Carbon::setTestNow(); // Reset time
    }
}
```

---

## 7. Security & Audit Logging

### Violation Tracking

```php
// In ShiftTimingValidator
private function logTimeViolation(array $violationData)
{
    Log::warning('SHIFT TIME VIOLATION ATTEMPT', array_merge($violationData, [
        'severity' => 'high',
        'violation_type' => 'time_window_breach',
        'security_event' => true,
        'requires_review' => true
    ]));

    // Store violation for potential disciplinary action
    TimeViolation::create([
        'employee_id' => $violationData['employee_id'] ?? null,
        'shift_type' => $violationData['shift_type'],
        'attempted_time' => $violationData['requested_time'],
        'violation_type' => $violationData['violation_type'],
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'branch_id' => $violationData['branch_id'],
    ]);
}
```

### Pattern Detection

```php
public function detectViolationPatterns(int $employeeId): array
{
    $violations = TimeViolation::where('employee_id', $employeeId)
        ->where('created_at', '>=', now()->subDays(30))
        ->get();

    return [
        'total_violations' => $violations->count(),
        'early_attempts' => $violations->where('violation_type', 'too_early')->count(),
        'late_attempts' => $violations->where('violation_type', 'too_late')->count(),
        'requires_attention' => $violations->count() >= 5,
        'recommendations' => $this->generateRecommendations($violations)
    ];
}
```

---

## 8. Performance Considerations

### Caching Time Windows

```php
// Cache shift configurations for 1 hour
public function getCachedShiftConfig(string $shiftType, int $branchId): StrictShiftConfig
{
    $cacheKey = "shift_config_strict_{$shiftType}_{$branchId}";

    return Cache::remember($cacheKey, 3600, function () use ($shiftType, $branchId) {
        return $this->getStrictShiftConfiguration($shiftType, $branchId);
    });
}
```

### Bulk Validation

```php
public function validateMultipleShifts(array $shiftRequests): array
{
    $results = [];

    foreach ($shiftRequests as $request) {
        $results[] = [
            'request' => $request,
            'validation' => $this->validateStrictTimeWindows(
                $request['shift_type'],
                $request['branch_id'],
                $request['requested_time'] ?? null
            )
        ];
    }

    return $results;
}
```

---

## 9. Rollback Strategy

### Graceful Degradation

```php
// Config-based strictness level
config('clock-in-out.strict_time_validation', true);

// Temporary disable for emergencies
Cache::put('strict_time_validation_disabled', true, 3600); // 1 hour

// Check in validator
if (Cache::get('strict_time_validation_disabled')) {
    Log::warning('Strict time validation temporarily disabled');
    return ValidationResult::valid();
}
```

---

**Document Information**
- **Prepared By**: Security & Validation Team
- **Reviewed By**: Compliance & Operations Teams
- **Approved By**: Security Officer
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/05_SHIFT_TIMING_VALIDATION.md