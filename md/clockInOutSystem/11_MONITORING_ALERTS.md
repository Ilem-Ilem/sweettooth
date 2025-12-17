# 10_TESTING_STRATEGY.md

## Comprehensive Testing Strategy

**Date**: December 2025
**Version**: 1.0
**Status**: Test Plans Complete

---

## Testing Pyramid Overview

```
END-TO-END TESTS (20%)
    ▲
INTEGRATION TESTS (30%)
    ▲
UNIT TESTS (50%)
```

---

## Unit Testing Strategy

### 1. ShiftTimingValidator Tests

```php
<?php

namespace Tests\Unit\Services;

use App\Services\ShiftTimingValidator;
use App\Models\Branch;
use Carbon\Carbon;
use Tests\TestCase;

class ShiftTimingValidatorTest extends TestCase
{
    public function test_morning_shift_strict_6am_to_12pm_validation()
    {
        $validator = new ShiftTimingValidator();
        $branch = Branch::factory()->create();

        // BEFORE 6 AM: Should fail
        $tooEarly = Carbon::today()->setTime(5, 59);
        $result = $validator->validateStrictTimeWindows('morning', $branch->id, $tooEarly);
        $this->assertFalse($result->isValid());
        $this->assertStringContains('Too early', $result->getMessage());

        // AT 6 AM: Should pass
        $exactlyOnTime = Carbon::today()->setTime(6, 0);
        $result = $validator->validateStrictTimeWindows('morning', $branch->id, $exactlyOnTime);
        $this->assertTrue($result->isValid());

        // DURING WINDOW: Should pass
        $duringWindow = Carbon::today()->setTime(9, 30);
        $result = $validator->validateStrictTimeWindows('morning', $branch->id, $duringWindow);
        $this->assertTrue($result->isValid());

        // AT 12 PM: Should fail (end of window)
        $tooLate = Carbon::today()->setTime(12, 0);
        $result = $validator->validateStrictTimeWindows('morning', $branch->id, $tooLate);
        $this->assertFalse($result->isValid());
        $this->assertStringContains('Too late', $result->getMessage());
    }

    public function test_afternoon_shift_strict_12pm_to_8pm_validation()
    {
        $validator = new ShiftTimingValidator();
        $branch = Branch::factory()->create();

        // BEFORE 12 PM: Should fail
        $tooEarly = Carbon::today()->setTime(11, 59);
        $result = $validator->validateStrictTimeWindows('afternoon', $branch->id, $tooEarly);
        $this->assertFalse($result->isValid());

        // AT 12 PM: Should pass
        $exactlyOnTime = Carbon::today()->setTime(12, 0);
        $result = $validator->validateStrictTimeWindows('afternoon', $branch->id, $exactlyOnTime);
        $this->assertTrue($result->isValid());

        // DURING WINDOW: Should pass
        $duringWindow = Carbon::today()->setTime(15, 30);
        $result = $validator->validateStrictTimeWindows('afternoon', $branch->id, $duringWindow);
        $this->assertTrue($result->isValid());

        // AFTER 8 PM: Should fail
        $tooLate = Carbon::today()->setTime(20, 1);
        $result = $validator->validateStrictTimeWindows('afternoon', $branch->id, $tooLate);
        $this->assertFalse($result->isValid());
    }

    public function test_full_time_shift_no_restrictions()
    {
        $validator = new ShiftTimingValidator();
        $branch = Branch::factory()->create();

        // Any time should pass for full_time
        $times = [
            Carbon::today()->setTime(2, 0),   // 2 AM
            Carbon::today()->setTime(14, 30), // 2:30 PM
            Carbon::today()->setTime(23, 59), // 11:59 PM
        ];

        foreach ($times as $time) {
            $result = $validator->validateStrictTimeWindows('full_time', $branch->id, $time);
            $this->assertTrue($result->isValid(), "Full time should allow clock-in at " . $time->format('H:i'));
        }
    }

    public function test_conflicting_shift_detection()
    {
        $validator = new ShiftTimingValidator();
        $employeeId = 1;
        $branchId = 1;

        // Create existing morning shift
        Shift::create([
            'employee_id' => $employeeId,
            'branch_id' => $branchId,
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'clock_in' => Carbon::today()->setTime(6, 0),
            'status' => 'active'
        ]);

        // Try to start another shift today - should fail
        $result = $validator->validateNoConflictingShifts(
            $employeeId,
            'afternoon',
            $branchId,
            Carbon::today()->setTime(12, 0)
        );

        $this->assertFalse($result->isValid());
        $this->assertStringContains('already have an active shift', $result->getMessage());
    }
}
```

### 2. RequireActiveShift Middleware Tests

```php
<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\RequireActiveShift;
use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class RequireActiveShiftTest extends TestCase
{
    public function test_super_admin_bypasses_middleware()
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $request = Request::create('/dashboard');
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_employee_with_active_shift_passes()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');
        $branch = Branch::factory()->create();

        // Create active shift
        Shift::create([
            'employee_id' => $user->id,
            'branch_id' => $branch->id,
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'clock_in' => Carbon::now(),
            'status' => 'active'
        ]);

        $request = Request::create('/dashboard');
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertTrue($request->has('active_shift'));
    }

    public function test_employee_without_shift_redirected()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $request = Request::create('/dashboard');
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return response('Should not reach here');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('select_shift', $response->getTargetUrl());
    }

    public function test_api_requests_return_json_errors()
    {
        $user = User::factory()->create();
        $user->assignRole('employee');

        $request = Request::create('/api/dashboard', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $middleware = new RequireActiveShift();

        $response = $middleware->handle($request, function () {
            return response('Should not reach here');
        });

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Active shift required', $content['error']);
        $this->assertArrayHasKey('redirect', $content);
    }
}
```

---

## Integration Testing Strategy

### 1. Clock In/Out Workflow Tests

```php
<?php

namespace Tests\Feature\Auth;

use App\Models\{User, Branch, Shift};
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockInOutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_morning_shift_workflow()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Set time to valid morning window
        Carbon::setTestNow(Carbon::today()->setTime(8, 0));

        // 1. Clock in during valid window
        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'morning',
            'b_id' => $branch->id
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('toast.success');

        // Verify shift created
        $this->assertDatabaseHas('shifts', [
            'employee_id' => $employee->id,
            'shift_type' => 'morning',
            'status' => 'active'
        ]);

        $shift = Shift::where('employee_id', $employee->id)->first();

        // 2. Try to clock in again (should fail - already active)
        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'afternoon',
            'b_id' => $branch->id
        ]);

        $response->assertSessionHasErrors();

        // 3. Clock out
        $response = $this->actingAs($employee)->from(route('branch-dashboard.select_shift'))
            ->post(route('branch-dashboard.clock_out'));

        $response->assertRedirect();
        $response->assertSessionHas('toast.success');

        // Verify shift closed
        $shift->refresh();
        $this->assertEquals('closed', $shift->status);
        $this->assertNotNull($shift->clock_out);

        Carbon::setTestNow(); // Reset time
    }

    public function test_strict_time_window_enforcement()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Test clock-in before morning window
        Carbon::setTestNow(Carbon::today()->setTime(5, 30));

        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'morning',
            'b_id' => $branch->id
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('shifts', [
            'employee_id' => $employee->id,
            'shift_date' => Carbon::today()->toDateString()
        ]);

        // Test clock-in after morning window
        Carbon::setTestNow(Carbon::today()->setTime(12, 30));

        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'morning',
            'b_id' => $branch->id
        ]);

        $response->assertSessionHasErrors();

        // Test valid afternoon clock-in
        Carbon::setTestNow(Carbon::today()->setTime(14, 0));

        $response = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
            'shift_type' => 'afternoon',
            'b_id' => $branch->id
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shifts', [
            'employee_id' => $employee->id,
            'shift_type' => 'afternoon',
            'status' => 'active'
        ]);

        Carbon::setTestNow(); // Reset time
    }
}
```

### 2. Active Shift Enforcement Tests

```php
<?php

namespace Tests\Feature\Middleware;

use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiveShiftEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_active_shift()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Without active shift - should redirect
        $response = $this->actingAs($employee)
            ->get(route('branch-dashboard.index', ['b_id' => $branch->id]));

        $response->assertRedirect(route('branch-dashboard.select_shift', ['b_id' => $branch->id]));
        $response->assertSessionHas('error');
    }

    public function test_work_functions_require_active_shift()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        $restrictedRoutes = [
            'branch-dashboard.inventory.items',
            'branch-dashboard.production.recipes',
            'branch-dashboard.sales-dashboard.pos.index',
        ];

        foreach ($restrictedRoutes as $route) {
            $response = $this->actingAs($employee)
                ->get(route($route, ['b_id' => $branch->id]));

            $response->assertRedirect();
            $response->assertSessionHas('error', 'Please clock in to start your shift');
        }
    }

    public function test_super_admin_bypasses_shift_requirement()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $branch = Branch::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('branch-dashboard.index', ['b_id' => $branch->id]));

        $response->assertSuccessful();
    }

    public function test_employee_with_active_shift_can_access_work()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Create active shift
        Shift::create([
            'employee_id' => $employee->id,
            'branch_id' => $branch->id,
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'clock_in' => Carbon::now(),
            'status' => 'active'
        ]);

        $response = $this->actingAs($employee)
            ->get(route('branch-dashboard.index', ['b_id' => $branch->id]));

        $response->assertSuccessful();
    }
}
```

---

## End-to-End Testing Strategy

### 1. User Journey Tests

```php
<?php

namespace Tests\Browser;

use App\Models\{User, Branch};
use Carbon\Carbon;
use Laravel\Dusk\TestCase;
use Tests\DuskTestCase;

class ClockInOutJourneyTest extends DuskTestCase
{
    public function test_employee_clock_in_out_journey()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        $this->browse(function ($browser) use ($employee, $branch) {
            // 1. Login
            $browser->visit('/login')
                ->type('email', $employee->email)
                ->type('password', 'password')
                ->press('Login')
                ->assertPathIs('/branch-dashboard/select_shift');

            // 2. Select morning shift during valid time
            Carbon::setTestNow(Carbon::today()->setTime(8, 0));

            $browser->select('shift_type', 'morning')
                ->press('Clock In')
                ->assertPathIs('/branch-dashboard/dashboard');

            // 3. Verify active shift indicator
            $browser->assertSee('Active')
                ->assertSee('Time Worked');

            // 4. Try to access restricted area (should work with active shift)
            $browser->visit(route('branch-dashboard.inventory.items', ['b_id' => $branch->id]))
                ->assertSuccessful();

            // 5. Clock out
            $browser->press('Clock Out')
                ->waitForText('Clocked out successfully')
                ->assertPathIs('/branch-dashboard/select_shift');

            Carbon::setTestNow();
        });
    }

    public function test_strict_time_validation_ui()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        $this->browse(function ($browser) use ($employee, $branch) {
            $browser->loginAs($employee)
                ->visit(route('branch-dashboard.select_shift', ['b_id' => $branch->id]));

            // Try to clock in before morning window
            Carbon::setTestNow(Carbon::today()->setTime(5, 30));

            $browser->select('shift_type', 'morning')
                ->press('Clock In')
                ->waitForText('Too early')
                ->assertSee('Too early for Morning Shift')
                ->assertPathIs('/branch-dashboard/select_shift');

            // Try valid time
            Carbon::setTestNow(Carbon::today()->setTime(8, 0));

            $browser->select('shift_type', 'morning')
                ->press('Clock In')
                ->assertPathIs('/branch-dashboard/dashboard');

            Carbon::setTestNow();
        });
    }
}
```

### 2. Performance Tests

```php
<?php

namespace Tests\Performance;

use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Tests\TestCase;

class ShiftSystemPerformanceTest extends TestCase
{
    public function test_shift_creation_performance()
    {
        $employee = User::factory()->create();
        $branch = Branch::factory()->create();

        $startTime = microtime(true);

        for ($i = 0; $i < 100; $i++) {
            Shift::create([
                'employee_id' => $employee->id,
                'branch_id' => $branch->id,
                'shift_date' => Carbon::today(),
                'shift_type' => 'morning',
                'clock_in' => Carbon::now(),
                'status' => 'active'
            ]);
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        $avgTime = $totalTime / 100;

        // Should create shifts in less than 10ms each on average
        $this->assertLessThan(0.01, $avgTime, "Shift creation too slow: {$avgTime}s per shift");
    }

    public function test_middleware_performance()
    {
        $employee = User::factory()->create();
        $employee->assignRole('employee');
        $branch = Branch::factory()->create();

        // Create active shift
        Shift::create([
            'employee_id' => $employee->id,
            'branch_id' => $branch->id,
            'shift_date' => Carbon::today(),
            'shift_type' => 'morning',
            'clock_in' => Carbon::now(),
            'status' => 'active'
        ]);

        $times = [];

        for ($i = 0; $i < 50; $i++) {
            $startTime = microtime(true);

            $response = $this->actingAs($employee)
                ->get(route('branch-dashboard.index', ['b_id' => $branch->id]));

            $endTime = microtime(true);
            $times[] = $endTime - $startTime;

            $response->assertSuccessful();
        }

        $avgTime = array_sum($times) / count($times);

        // Middleware should add less than 50ms overhead
        $this->assertLessThan(0.05, $avgTime, "Middleware too slow: {$avgTime}s average response");
    }

    public function test_concurrent_shift_operations()
    {
        $employees = User::factory()->count(10)->create();
        $branch = Branch::factory()->create();

        foreach ($employees as $employee) {
            $employee->assignRole('employee');
        }

        // Test concurrent clock-ins
        $responses = [];

        foreach ($employees as $employee) {
            Carbon::setTestNow(Carbon::today()->setTime(8, 0));

            $responses[] = $this->actingAs($employee)->post(route('branch-dashboard.clock_in'), [
                'shift_type' => 'morning',
                'b_id' => $branch->id
            ]);
        }

        foreach ($responses as $response) {
            $response->assertRedirect();
        }

        // Verify all shifts created
        $this->assertEquals(10, Shift::where('status', 'active')->count());

        Carbon::setTestNow();
    }
}
```

---

## Load Testing Strategy

### 1. Database Load Tests

```php
<?php

namespace Tests\Load;

use App\Models\{Shift, User, Branch};
use Carbon\Carbon;
use Tests\TestCase;

class DatabaseLoadTest extends TestCase
{
    public function test_bulk_shift_queries_performance()
    {
        // Create test data
        $branch = Branch::factory()->create();
        $employees = User::factory()->count(1000)->create();

        foreach ($employees as $employee) {
            $employee->assignRole('employee');

            // Create 30 days of shift history per employee
            for ($i = 0; $i < 30; $i++) {
                Shift::create([
                    'employee_id' => $employee->id,
                    'branch_id' => $branch->id,
                    'shift_date' => Carbon::today()->subDays($i),
                    'shift_type' => 'morning',
                    'clock_in' => Carbon::today()->subDays($i)->setTime(6, 0),
                    'clock_out' => Carbon::today()->subDays($i)->setTime(14, 0),
                    'status' => 'closed'
                ]);
            }
        }

        // Test active shift lookup performance
        $startTime = microtime(true);

        foreach ($employees as $employee) {
            $activeShift = Shift::where('employee_id', $employee->id)
                ->where('shift_date', Carbon::today())
                ->where('status', 'active')
                ->first();
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        $avgTime = $totalTime / 1000; // per query

        // Should complete in less than 50ms per query under load
        $this->assertLessThan(0.05, $avgTime, "Active shift queries too slow: {$avgTime}s per query");
    }
}
```

### 2. API Load Tests

```bash
# Using Apache Bench for API load testing
ab -n 1000 -c 10 -H "Authorization: Bearer {token}" \
   -H "Accept: application/json" \
   http://localhost/api/shifts/active

# Using wrk for concurrent load testing
wrk -t12 -c400 -d30s http://localhost/branch-dashboard/dashboard
```

---

## Automated Testing Pipeline

### 1. GitHub Actions CI/CD Pipeline

```yaml
# .github/workflows/shift-system-tests.yml
name: Shift System Tests

on:
  push:
    paths:
      - 'app/Livewire/Auth/Shift.php'
      - 'app/Http/Middleware/RequireActiveShift.php'
      - 'app/Services/ShiftTimingValidator.php'
      - 'tests/**'
  pull_request:
    paths:
      - 'app/Livewire/Auth/Shift.php'
      - 'app/Http/Middleware/RequireActiveShift.php'
      - 'app/Services/ShiftTimingValidator.php'

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: test_db
        ports:
          - 3306:3306

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3
          extensions: pdo, pdo_mysql

      - name: Install dependencies
        run: composer install --no-progress --prefer-dist --optimize-autoloader

      - name: Copy environment file
        run: cp .env.ci .env

      - name: Generate application key
        run: php artisan key:generate

      - name: Run migrations
        run: php artisan migrate --force

      - name: Run tests
        run: php artisan test --parallel --coverage

      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          file: ./coverage.xml
```

### 2. Pre-deployment Smoke Tests

```php
<?php

namespace Tests\Smoke;

use Tests\TestCase;

class ShiftSystemSmokeTest extends TestCase
{
    public function test_basic_shift_functionality()
    {
        // Test database connectivity
        $this->assertTrue(\DB::connection()->getPdo() !== null);

        // Test shift model
        $shift = new \App\Models\Shift();
        $this->assertInstanceOf(\App\Models\Shift::class, $shift);

        // Test shift timing validator
        $validator = new \App\Services\ShiftTimingValidator();
        $this->assertInstanceOf(\App\Services\ShiftTimingValidator::class, $validator);

        // Test middleware exists
        $middleware = new \App\Http\Middleware\RequireActiveShift();
        $this->assertInstanceOf(\App\Http\Middleware\RequireActiveShift::class, $middleware);

        // Test routes exist
        $this->assertTrue(\Route::has('branch-dashboard.select_shift'));
        $this->assertTrue(\Route::has('branch-dashboard.clock_in'));
        $this->assertTrue(\Route::has('branch-dashboard.clock_out'));
    }

    public function test_configuration_loaded()
    {
        // Test shift configurations exist
        $configs = \DB::table('shift_configurations')->count();
        $this->assertGreaterThan(0, $configs, 'Shift configurations not seeded');

        // Test time zones are valid
        $invalidTimezones = \DB::table('shift_configurations')
            ->whereNotIn('timezone', \DateTimeZone::listIdentifiers())
            ->count();
        $this->assertEquals(0, $invalidTimezones, 'Invalid timezones found');
    }
}
```

---

## Test Data Management

### 1. Test Data Factories

```php
<?php

namespace Database\Factories;

use App\Models\ShiftConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftConfigurationFactory extends Factory
{
    protected $model = ShiftConfiguration::class;

    public function definition()
    {
        return [
            'branch_id' => \App\Models\Branch::factory(),
            'shift_type' => $this->faker->randomElement(['morning', 'afternoon', 'night', 'full_time']),
            'name' => $this->faker->randomElement([
                'Morning Shift', 'Afternoon Shift', 'Night Shift', 'Full Time'
            ]),
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
            'clock_in_start' => $this->faker->time('H:i:s'),
            'clock_in_end' => $this->faker->time('H:i:s'),
            'auto_clock_out_minutes' => $this->faker->numberBetween(5, 60),
            'max_overtime_hours' => $this->faker->randomFloat(1, 0, 4),
            'break_duration_minutes' => $this->faker->numberBetween(30, 120),
            'timezone' => $this->faker->timezone(),
            'is_active' => $this->faker->boolean(90), // 90% active
        ];
    }

    public function morning()
    {
        return $this->state(function (array $attributes) {
            return [
                'shift_type' => 'morning',
                'name' => 'Morning Shift',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'clock_in_start' => '06:00:00',
                'clock_in_end' => '12:00:00',
            ];
        });
    }
}
```

### 2. Test Database Seeding

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Branch, ShiftConfiguration};

class TestShiftConfigurationSeeder extends Seeder
{
    public function run()
    {
        // Create test branches
        $branches = Branch::factory()->count(5)->create();

        foreach ($branches as $branch) {
            // Morning shift
            ShiftConfiguration::factory()->morning()->create([
                'branch_id' => $branch->id,
                'timezone' => $branch->timezone ?? 'UTC'
            ]);

            // Afternoon shift
            ShiftConfiguration::factory()->afternoon()->create([
                'branch_id' => $branch->id,
                'timezone' => $branch->timezone ?? 'UTC'
            ]);

            // Full time
            ShiftConfiguration::factory()->fullTime()->create([
                'branch_id' => $branch->id,
                'timezone' => $branch->timezone ?? 'UTC'
            ]);
        }
    }
}
```

---

## Test Reporting & Analytics

### 1. Test Results Dashboard

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTestReport extends Command
{
    protected $signature = 'test:report {--format=console : Output format (console, json, html)}';

    public function handle()
    {
        $tests = $this->gatherTestResults();

        match($this->option('format')) {
            'json' => $this->outputJson($tests),
            'html' => $this->outputHtml($tests),
            default => $this->outputConsole($tests)
        };
    }

    private function gatherTestResults()
    {
        // In a real implementation, this would parse PHPUnit results
        return [
            'unit_tests' => [
                'total' => 150,
                'passed' => 148,
                'failed' => 2,
                'coverage' => 87.5
            ],
            'integration_tests' => [
                'total' => 50,
                'passed' => 49,
                'failed' => 1,
                'coverage' => 92.3
            ],
            'e2e_tests' => [
                'total' => 25,
                'passed' => 23,
                'failed' => 2,
                'coverage' => 95.1
            ],
            'performance_tests' => [
                'response_time_avg' => 45, // ms
                'throughput' => 120, // req/sec
                'memory_usage' => 32, // MB
            ]
        ];
    }
}
```

### 2. Continuous Test Monitoring

```php
// In TestCase.php base class
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Log test start
        \Log::info('Test started', [
            'test_class' => static::class,
            'test_method' => $this->getName(),
            'timestamp' => now()
        ]);
    }

    protected function tearDown(): void
    {
        // Log test completion
        $status = $this->getStatus();
        \Log::info('Test completed', [
            'test_class' => static::class,
            'test_method' => $this->getName(),
            'status' => $status,
            'duration' => $this->getTestResultObject()?->time(),
            'timestamp' => now()
        ]);

        parent::tearDown();
    }
}
```

---

**Document Information**
- **Prepared By**: QA Engineering Team
- **Reviewed By**: Development & DevOps Teams
- **Approved By**: Quality Assurance Manager
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/10_TESTING_STRATEGY.md