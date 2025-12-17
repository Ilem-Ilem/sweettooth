# 07_DATABASE_MIGRATIONS.md

## Database Migrations Implementation

**Date**: December 2025
**Version**: 1.0
**Status**: Ready for Implementation

---

## Overview

This document outlines the complete database migration strategy for implementing the enhanced clock in/out system. All migrations are designed for zero-downtime deployment with comprehensive rollback procedures.

---

## Migration Files

### 2024_01_15_000001_create_shift_configurations_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateShiftConfigurationsTable extends Migration
{
    public function up()
    {
        Schema::create('shift_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->enum('shift_type', ['morning', 'afternoon', 'night', 'full_time']);
            $table->string('name', 100);
            $table->time('start_time');
            $table->time('end_time');
            $table->time('clock_in_start');
            $table->time('clock_in_end');
            $table->unsignedInteger('auto_clock_out_minutes')->default(15);
            $table->decimal('max_overtime_hours', 4, 2)->default(2.00);
            $table->unsignedInteger('break_duration_minutes')->default(60);
            $table->string('timezone', 50)->default('UTC');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Constraints
            $table->check('clock_in_end > clock_in_start');
            $table->check('end_time > start_time');
            $table->check('auto_clock_out_minutes >= 0');
            $table->check('max_overtime_hours >= 0');
            $table->check('break_duration_minutes >= 0');

            // Indexes
            $table->unique(['branch_id', 'shift_type', 'is_active'], 'unique_active_shift_config');
            $table->index(['branch_id', 'is_active'], 'idx_branch_active');
            $table->index('shift_type', 'idx_shift_type');
            $table->index('timezone', 'idx_timezone');
        });

        // Seed default configurations
        $this->seedDefaultConfigurations();
    }

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

    private function seedDefaultConfigurations()
    {
        $branches = DB::table('branches')->pluck('id');

        foreach ($branches as $branchId) {
            // Morning shift with STRICT time windows
            DB::table('shift_configurations')->insert([
                'branch_id' => $branchId,
                'shift_type' => 'morning',
                'name' => 'Morning Shift',
                'start_time' => '06:00:00',
                'end_time' => '14:00:00',
                'clock_in_start' => '06:00:00', // STRICT: 6 AM
                'clock_in_end' => '12:00:00',   // STRICT: 12 PM
                'auto_clock_out_minutes' => 15,
                'max_overtime_hours' => 2.00,
                'break_duration_minutes' => 60,
                'timezone' => 'UTC',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Afternoon shift with STRICT time windows
            DB::table('shift_configurations')->insert([
                'branch_id' => $branchId,
                'shift_type' => 'afternoon',
                'name' => 'Afternoon Shift',
                'start_time' => '12:00:00',
                'end_time' => '20:00:00',
                'clock_in_start' => '12:00:00', // STRICT: 12 PM
                'clock_in_end' => '20:00:00',   // STRICT: 8 PM
                'auto_clock_out_minutes' => 15,
                'max_overtime_hours' => 2.00,
                'break_duration_minutes' => 60,
                'timezone' => 'UTC',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Full time (flexible)
            DB::table('shift_configurations')->insert([
                'branch_id' => $branchId,
                'shift_type' => 'full_time',
                'name' => 'Full Time',
                'start_time' => '00:00:00',
                'end_time' => '23:59:59',
                'clock_in_start' => '00:00:00',
                'clock_in_end' => '23:59:59',
                'auto_clock_out_minutes' => 480, // 8 hours
                'max_overtime_hours' => 4.00,
                'break_duration_minutes' => 60,
                'timezone' => 'UTC',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
```

### 2024_01_15_000002_add_shift_enhancements_to_shifts_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftEnhancementsToShiftsTable extends Migration
{
    public function up()
    {
        Schema::table('shifts', function (Blueprint $table) {
            // Auto clock out tracking
            $table->timestamp('auto_clocked_out_at')->nullable()->after('clock_out');
            $table->string('auto_clock_out_reason', 255)->nullable()->after('auto_clocked_out_at');

            // Notification tracking
            $table->timestamp('notified_at')->nullable()->after('auto_clock_out_reason');

            // Enhanced metadata (extend existing JSON column)
            // Note: metadata column should already exist from previous migrations

            // Performance indexes
            $table->index(['status', 'shift_date', 'clock_in'], 'idx_shifts_status_date_clockin');
            $table->index(['employee_id', 'shift_date', 'status'], 'idx_shifts_employee_date_status');
            $table->index(['branch_id', 'shift_date', 'status'], 'idx_shifts_branch_date_status');
        });

        // Add check constraints
        DB::statement("ALTER TABLE shifts ADD CONSTRAINT chk_clock_out_after_clock_in CHECK (clock_out IS NULL OR clock_out > clock_in)");
        DB::statement("ALTER TABLE shifts ADD CONSTRAINT chk_valid_status CHECK (status IN ('active', 'closed', 'auto_clocked_out', 'cancelled'))");
    }

    public function down()
    {
        // Remove check constraints
        DB::statement("ALTER TABLE shifts DROP CONSTRAINT IF EXISTS chk_clock_out_after_clock_in");
        DB::statement("ALTER TABLE shifts DROP CONSTRAINT IF EXISTS chk_valid_status");

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropIndex('idx_shifts_status_date_clockin');
            $table->dropIndex('idx_shifts_employee_date_status');
            $table->dropIndex('idx_shifts_branch_date_status');

            $table->dropColumn([
                'auto_clocked_out_at',
                'auto_clock_out_reason',
                'notified_at'
            ]);
        });
    }
}
```

### 2024_01_15_000003_create_time_violations_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeViolationsTable extends Migration
{
    public function up()
    {
        Schema::create('time_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('shift_type');
            $table->timestamp('attempted_time');
            $table->enum('violation_type', ['too_early', 'too_late', 'invalid_shift', 'outside_window']);
            $table->text('details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('requires_attention')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'created_at'], 'idx_violations_employee_date');
            $table->index(['branch_id', 'created_at'], 'idx_violations_branch_date');
            $table->index(['violation_type', 'severity'], 'idx_violations_type_severity');
            $table->index('requires_attention', 'idx_violations_attention');
            $table->index('resolved_at', 'idx_violations_resolved');
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_violations');
    }
}
```

### 2024_01_15_000004_create_shift_notifications_table.php

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('shift_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // reminder, warning, alert, auto_clock_out
            $table->string('title');
            $table->text('message');
            $table->json('metadata')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['employee_id', 'is_read', 'created_at'], 'idx_notifications_employee_read_date');
            $table->index(['shift_id', 'type'], 'idx_notifications_shift_type');
            $table->index(['priority', 'created_at'], 'idx_notifications_priority_date');
            $table->index('expires_at', 'idx_notifications_expires');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift_notifications');
    }
}
```

---

## Migration Execution Strategy

### Phase 1: Infrastructure Setup (Zero Downtime)

```bash
# Run migrations in order
php artisan migrate --step

# Verify table creation
php artisan tinker
>>> Schema::hasTable('shift_configurations')
=> true
>>> Schema::hasTable('time_violations')
=> true
```

### Phase 2: Data Migration (Background Process)

```php
// Run seeder for existing branches
php artisan db:seed --class=ShiftConfigurationSeeder

// Update existing shifts with configuration references
php artisan tinker
>>> app(UpdateExistingShiftsWithConfigurations::class)->handle()
```

### Phase 3: Index Optimization

```sql
-- Add performance indexes after data migration
ALTER TABLE shifts ADD INDEX idx_shifts_metadata_config_id ((JSON_EXTRACT(metadata, '$.config_id')));
ALTER TABLE shifts ADD INDEX idx_shifts_auto_clock_out ((JSON_EXTRACT(metadata, '$.auto_clocked_out_at')));

-- Analyze query performance
EXPLAIN SELECT * FROM shifts WHERE employee_id = 1 AND shift_date = CURDATE() AND status = 'active';
```

---

## Data Integrity Checks

### Pre-Migration Validation

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateShiftDataBeforeMigration extends Command
{
    protected $signature = 'shifts:validate-before-migration';
    protected $description = 'Validate existing shift data before running migrations';

    public function handle()
    {
        $this->info('Validating existing shift data...');

        // Check for invalid clock_out times
        $invalidClockOuts = DB::table('shifts')
            ->whereNotNull('clock_out')
            ->whereColumn('clock_out', '<=', 'clock_in')
            ->count();

        if ($invalidClockOuts > 0) {
            $this->error("Found {$invalidClockOuts} shifts with invalid clock_out times");
            return 1;
        }

        // Check for orphaned shifts
        $orphanedShifts = DB::table('shifts')
            ->leftJoin('users', 'shifts.employee_id', '=', 'users.id')
            ->whereNull('users.id')
            ->count();

        if ($orphanedShifts > 0) {
            $this->error("Found {$orphanedShifts} orphaned shifts");
            return 1;
        }

        // Check for duplicate active shifts
        $duplicateActive = DB::table('shifts')
            ->select('employee_id', 'shift_date', DB::raw('COUNT(*) as count'))
            ->where('status', 'active')
            ->groupBy('employee_id', 'shift_date')
            ->having('count', '>', 1)
            ->get();

        if ($duplicateActive->count() > 0) {
            $this->error('Found duplicate active shifts:');
            foreach ($duplicateActive as $dup) {
                $this->error("Employee {$dup->employee_id} on {$dup->shift_date}: {$dup->count} active shifts");
            }
            return 1;
        }

        $this->info('✅ All validations passed');
        return 0;
    }
}
```

### Post-Migration Validation

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ValidateShiftDataAfterMigration extends Command
{
    protected $signature = 'shifts:validate-after-migration';
    protected $description = 'Validate shift data after migrations complete';

    public function handle()
    {
        $this->info('Validating shift data after migration...');

        // Check configuration seeding
        $configCount = DB::table('shift_configurations')->count();
        $branchCount = DB::table('branches')->count();
        $expectedConfigs = $branchCount * 3; // 3 configs per branch

        if ($configCount < $expectedConfigs) {
            $this->error("Expected {$expectedConfigs} configurations, found {$configCount}");
            return 1;
        }

        // Check table constraints
        $constraintViolations = DB::select("
            SELECT COUNT(*) as violations
            FROM shift_configurations
            WHERE NOT (clock_in_end > clock_in_start AND end_time > start_time)
        ");

        if ($constraintViolations[0]->violations > 0) {
            $this->error("Found {$constraintViolations[0]->violations} constraint violations");
            return 1;
        }

        // Check index existence
        $indexes = DB::select("SHOW INDEX FROM shift_configurations WHERE Key_name = 'unique_active_shift_config'");
        if (empty($indexes)) {
            $this->error('Missing unique index on shift_configurations');
            return 1;
        }

        $this->info('✅ All post-migration validations passed');
        return 0;
    }
}
```

---

## Rollback Procedures

### Emergency Rollback Script

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EmergencyShiftSystemRollback extends Command
{
    protected $signature = 'shifts:emergency-rollback
                          {--confirm : Confirm the rollback operation}';
    protected $description = 'Emergency rollback of shift system changes';

    public function handle()
    {
        if (!$this->option('confirm')) {
            $this->error('This will remove all shift configuration data. Use --confirm to proceed.');
            return 1;
        }

        $this->warn('⚠️  EMERGENCY ROLLBACK STARTING ⚠️');
        $this->warn('This will:');
        $this->warn('- Delete all shift configurations');
        $this->warn('- Remove new columns from shifts table');
        $this->warn('- Delete time violations and notifications');
        $this->warn('- Restore original system state');

        if (!$this->confirm('Are you absolutely sure you want to proceed?')) {
            return 0;
        }

        try {
            DB::beginTransaction();

            // Drop new tables
            Schema::dropIfExists('shift_notifications');
            Schema::dropIfExists('time_violations');

            // Remove new columns from shifts table
            Schema::table('shifts', function ($table) {
                $table->dropColumn([
                    'auto_clocked_out_at',
                    'auto_clock_out_reason',
                    'notified_at'
                ]);

                $table->dropIndex('idx_shifts_status_date_clockin');
                $table->dropIndex('idx_shifts_employee_date_status');
                $table->dropIndex('idx_shifts_branch_date_status');
            });

            // Drop shift configurations (this will cascade to any related data)
            Schema::dropIfExists('shift_configurations');

            // Remove constraints
            DB::statement("ALTER TABLE shifts DROP CONSTRAINT IF EXISTS chk_clock_out_after_clock_in");
            DB::statement("ALTER TABLE shifts DROP CONSTRAINT IF EXISTS chk_valid_status");

            DB::commit();

            $this->info('✅ Emergency rollback completed successfully');
            $this->info('System has been restored to pre-enhancement state');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Rollback failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
```

### Partial Rollback Options

```php
// Disable features without removing data
config(['clock-in-out.strict_time_validation' => false]);
config(['clock-in-out.auto_clock_out' => false]);
config(['clock-in-out.require_active_shift' => false]);

// Re-enable specific features
config(['clock-in-out.strict_time_validation' => true]);
```

---

## Performance Optimization

### Index Strategy

```sql
-- Composite indexes for common queries
CREATE INDEX idx_shifts_active_employee_date
ON shifts(employee_id, shift_date, status)
WHERE status = 'active';

-- Partial indexes for better performance
CREATE INDEX idx_shift_configs_active_only
ON shift_configurations(branch_id, shift_type)
WHERE is_active = true;

-- Covering indexes for dashboard queries
CREATE INDEX idx_shifts_dashboard_covering
ON shifts(employee_id, shift_date, status, clock_in, clock_out, shift_type);
```

### Partitioning Strategy (Future)

```sql
-- Partition shifts table by month for better performance
ALTER TABLE shifts PARTITION BY RANGE (YEAR(shift_date) * 100 + MONTH(shift_date)) (
    PARTITION p202401 VALUES LESS THAN (202402),
    PARTITION p202402 VALUES LESS THAN (202403),
    PARTITION p202403 VALUES LESS THAN (202404),
    PARTITION p202404 VALUES LESS THAN (202405)
);
```

---

## Backup Strategy

### Pre-Migration Backup

```bash
# Full database backup
mysqldump -u username -p database_name > pre_shift_migration_backup.sql

# Specific table backups
mysqldump -u username -p database_name shifts > shifts_backup.sql
mysqldump -u username -p database_name branches > branches_backup.sql
```

### Incremental Backups

```bash
# Daily backup during testing phase
0 2 * * * mysqldump -u username -p database_name shifts shift_configurations time_violations > daily_shift_backup_$(date +\%Y\%m\%d).sql
```

---

## Testing Strategy

### Migration Testing

```php
<?php

namespace Tests\Database\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShiftConfigurationMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_shift_configurations_table_created()
    {
        // Run migration
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2024_01_15_000001_create_shift_configurations_table.php'
        ]);

        // Verify table exists
        $this->assertTrue(Schema::hasTable('shift_configurations'));

        // Verify columns exist
        $columns = Schema::getColumnListing('shift_configurations');
        $this->assertContains('branch_id', $columns);
        $this->assertContains('shift_type', $columns);
        $this->assertContains('clock_in_start', $columns);
        $this->assertContains('clock_in_end', $columns);
    }

    public function test_default_configurations_seeded()
    {
        // Create test branch
        $branch = Branch::factory()->create();

        // Run migration
        $this->artisan('migrate', [
            '--path' => 'database/migrations/2024_01_15_000001_create_shift_configurations_table.php'
        ]);

        // Verify configurations created
        $configs = DB::table('shift_configurations')
            ->where('branch_id', $branch->id)
            ->get();

        $this->assertCount(3, $configs); // morning, afternoon, full_time

        // Verify morning shift strict times
        $morning = $configs->where('shift_type', 'morning')->first();
        $this->assertEquals('06:00:00', $morning->clock_in_start);
        $this->assertEquals('12:00:00', $morning->clock_in_end);
    }
}
```

---

## Monitoring & Alerting

### Migration Health Checks

```php
// Add to existing health check endpoint
public function databaseHealth()
{
    $checks = [
        'shift_configurations_table' => Schema::hasTable('shift_configurations'),
        'time_violations_table' => Schema::hasTable('time_violations'),
        'shift_notifications_table' => Schema::hasTable('shift_notifications'),
        'new_shift_columns' => Schema::hasColumn('shifts', 'auto_clocked_out_at'),
        'shift_constraints' => $this->checkConstraints(),
        'default_configurations' => $this->checkDefaultConfigurations(),
    ];

    $allHealthy = !in_array(false, $checks, true);

    return response()->json([
        'status' => $allHealthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'timestamp' => now()->toIso8601String()
    ], $allHealthy ? 200 : 503);
}

private function checkConstraints()
{
    try {
        // Test constraint violations
        $violations = DB::table('shift_configurations')
            ->whereRaw('NOT (clock_in_end > clock_in_start AND end_time > start_time)')
            ->count();

        return $violations === 0;
    } catch (\Exception $e) {
        return false;
    }
}
```

---

**Document Information**
- **Prepared By**: Database Team
- **Reviewed By**: DevOps & QA Teams
- **Approved By**: Database Administrator
- **Next Review Date**: Implementation completion</content>
<parameter name="filePath">md/clockInOutSystem/07_DATABASE_MIGRATIONS.md