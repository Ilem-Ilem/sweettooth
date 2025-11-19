To incorporate UUIDs as primary keys for the `users`, `branches`, and `employees` tables while keeping the rest of the schema intact, I'll update the migrations and models for these three tables to use UUIDs instead of `BIGINT AUTO_INCREMENT`. The other tables (`departments`, `employee_medical_records`, `employee_certifications`, `leave_requests`, `permit_requests`) will retain their `BIGINT` IDs for simplicity, but their foreign keys referencing `users`, `branches`, or `employees` will be updated to use UUIDs (as `CHAR(36)`). I'll provide the revised Laravel migrations and models, ensuring compatibility with the existing schema and relationships.

### Key Changes
- **UUIDs**: Use `uuid` as the primary key type for `users`, `branches`, and `employees`. In Laravel, this is implemented as `CHAR(36)` with a generated UUID (using `Str::uuid()` or similar).
- **Foreign Keys**: Update foreign keys in related tables (e.g., `branches.manager_user_id`, `employees.branch_id`, `employees.department_id`, etc.) to `CHAR(36)` where they reference UUID-based tables.
- **Models**: Update Eloquent models to reflect UUIDs (set `$keyType` to `string` and `$incrementing` to `false`).
- **Migration Notes**: Ensure migrations run in order: `users`, `branches`, `employees`, then others due to foreign key dependencies.

Below are the updated migrations and models for the entire schema, with UUIDs applied to `users`, `branches`, and `employees` only.

---

### Laravel Migrations

Place these in `database/migrations/` with filenames like `2025_09_30_000001_create_users_table.php` (adjust timestamps). I'll include all tables to ensure consistency, with UUID changes applied to the specified tables.

#### 1. Users Table Migration
Primary key is now a UUID (`CHAR(36)`).

<xaiArtifact artifact_id="6216f248-9fd7-46ed-b005-6209c14c3d77" artifact_version_id="41209bba-ebf4-499c-b390-3dccba8b9cb0" title="create_users_table.php" contentType="text/php">

```php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'md', 'super_admin'])->default('admin');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
</xaiArtifact>

#### 2. Branches Table Migration
Primary key is now a UUID, and `manager_user_id` is updated to `CHAR(36)`.

<xaiArtifact artifact_id="513335fb-5b1e-4f18-8378-54e07d05b767" artifact_version_id="1ffecca5-9b7e-4a65-8cfc-c5c255f90fbf" title="create_branches_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('location');
            $table->uuid('manager_user_id')->nullable();
            $table->foreign('manager_user_id')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
</xaiArtifact>

#### 3. Departments Table Migration
Updated `branch_id` to `CHAR(36)` to reference `branches.id` (UUID).

<xaiArtifact artifact_id="1229be7b-c11a-47e0-bbea-831c84282ac1" artifact_version_id="f7dba65a-52f2-4171-b6bf-b724ee60354d" title="create_departments_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->enum('name', ['hot_kitchen', 'pastry', 'corner_store', 'gelato', 'till']);
            $table->enum('type', ['production', 'sales']);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
</xaiArtifact>

#### 4. Employees Table Migration
Primary key is now a UUID, and `branch_id` and `department_id` are updated where needed (`branch_id` to `CHAR(36)`, `department_id` remains `BIGINT`).

<xaiArtifact artifact_id="653da884-efbd-4cd5-83e5-b596000baab7" artifact_version_id="bcac6664-be6e-4903-b509-8ae1d6a6c79f" title="create_employees_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->string('employee_number', 50)->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 50)->nullable();
            $table->string('position');
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated', 'on_probation', 'on_leave'])->default('active');
            $table->date('probation_end_date')->nullable();
            $table->enum('shift_preference', ['morning', 'afternoon', 'night', 'rotating', 'flexible'])->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->text('allergies')->nullable();
            $table->string('profile_photo')->nullable();
            $table->date('last_performance_review_date')->nullable();
            $table->decimal('performance_rating', 3, 1)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
</xaiArtifact>

#### 5. Employee Medical Records Table Migration
Updated `employee_id` to `CHAR(36)`.

<xaiArtifact artifact_id="ee79e58c-199f-476c-9363-3d27fb192ea9" artifact_version_id="2401cad8-3250-4d8c-aa4f-e9583e7bc474" title="create_employee_medical_records_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_medical_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('condition')->nullable();
            $table->date('checkup_date')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_medical_records');
    }
};
</xaiArtifact>

#### 6. Employee Certifications Table Migration
Updated `employee_id` to `CHAR(36)`.

<xaiArtifact artifact_id="e91cf37b-3b07-43f8-ae4a-0888201b2a3c" artifact_version_id="07c478fc-c14b-4582-a41a-4a3ff4a9d7e7" title="create_employee_certifications_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_certifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'expired', 'pending_renewal'])->default('active');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_certifications');
    }
};
</xaiArtifact>

#### 7. Leave Requests Table Migration
Updated `employee_id` and `approver_user_id` to `CHAR(36)`.

<xaiArtifact artifact_id="ef596107-d564-44c1-bf40-487ac11eb39d" artifact_version_id="a4f763b9-b6d4-40a4-a4ac-caa5c72dcb40" title="create_leave_requests_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->uuid('approver_user_id')->nullable();
            $table->foreign('approver_user_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('type', ['annual', 'sick', 'maternity', 'other'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
</xaiArtifact>

#### 8. Permit Requests Table Migration
Updated `employee_id` and `approver_user_id` to `CHAR(36)`.

<xaiArtifact artifact_id="04f70dbe-8d5a-420b-800e-3617725a6953" artifact_version_id="20c39f08-b09b-4a34-9371-c2ca5b9c22c2" title="create_permit_requests_table.php" contentType="text/php">
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permit_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->uuid('approver_user_id')->nullable();
            $table->foreign('approver_user_id')->references('id')->on('users')->onDelete('set null');
            $table->date('request_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permit_requests');
    }
};
```
</xaiArtifact>

### Laravel Eloquent Models

Updated models reflect UUIDs for `users`, `branches`, and `employees`. Place these in `app/Models/`.

#### 1. User Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $keyType = 'string';
    protected $incrementing = false;

    protected $fillable = ['id', 'name', 'email', 'password', 'role'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'manager_user_id');
    }

    public function approvedLeaves()
    {
        return $this->hasMany(LeaveRequest::class, 'approver_user_id');
    }

    public function approvedPermits()
    {
        return $this->hasMany(PermitRequest::class, 'approver_user_id');
    }
}
```

#### 2. Branch Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    protected $incrementing = false;

    protected $fillable = ['id', 'name', 'location', 'manager_user_id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
```

#### 3. Department Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'name', 'type', 'description'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
```

#### 4. Employee Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    protected $incrementing = false;

    protected $fillable = [
        'id', 'branch_id', 'department_id', 'employee_number', 'name', 'email', 'phone', 'address',
        'date_of_birth', 'gender', 'nationality', 'emergency_contact_name', 'emergency_contact_phone',
        'position', 'hire_date', 'termination_date', 'status', 'probation_end_date', 'shift_preference',
        'salary', 'hourly_rate', 'tax_id', 'bank_account', 'allergies', 'profile_photo',
        'last_performance_review_date', 'performance_rating'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(EmployeeMedicalRecord::class);
    }

    public function certifications()
    {
        return $this->hasMany(EmployeeCertification::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function permitRequests()
    {
        return $this->hasMany(PermitRequest::class);
    }
}
```

#### 5. EmployeeMedicalRecord Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'condition', 'checkup_date', 'doctor_notes', 'attachment'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
```

#### 6. EmployeeCertification Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCertification extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'name', 'issuer', 'issue_date', 'expiry_date', 'status', 'attachment'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
```

#### 7. LeaveRequest Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'approver_user_id', 'type', 'start_date', 'end_date',
        'reason', 'status', 'approved_at', 'rejection_reason'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
```

#### 8. PermitRequest Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'approver_user_id', 'request_date', 'start_time', 'end_time',
        'reason', 'status', 'approved_at', 'check_out_time', 'check_in_time', 'rejection_reason'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
```

### Implementation Notes
- **UUID Generation**: The `boot` method in `User`, `Branch`, and `Employee` models auto-generates UUIDs using `Str::uuid()` on creation.
- **Migration Order**: Run migrations in order: `users`, `branches`, `departments`, `employees`, then others to satisfy foreign key constraints.
- **Database Setup**: In your `.env`, configure the database (e.g., MySQL on Namecheap). Example:
  ```
  DB_CONNECTION=mysql
  DB_HOST=localhost
  DB_PORT=3306
  DB_DATABASE=your_db_name
  DB_USERNAME=your_db_user
  DB_PASSWORD=your_db_password
  ```
- **Seeding**: Create factories for testing. Example for `Employee`:
  ```php
  'id' => \Illuminate\Support\Str::uuid(),
  'employee_number' => 'EMP-' . rand(1000, 9999),
  ```
- **Livewire Integration**: For your app (since you mentioned Livewire), use these models in Livewire components for CRUD (e.g., `<livewire:employee.create>`). Ensure `wire:model` binds to `$fillable` fields.
- **Namecheap Deployment**: Without SSH, upload migrations via cPanel File Manager to `myapp/database/migrations/`. Run migrations using a script like:
  ```php
  <?php
  require __DIR__.'/vendor/autoload.php';
  $app = require_once __DIR__.'/bootstrap/app.php';
  $app->make('Illuminate\Contracts\Console\Kernel')->call('migrate');
  echo "Migrations completed";
  ?>
  ```
  Save as `migrate.php`, access via browser, then delete.

### Testing
- Verify relationships: `$employee = Employee::with(['branch', 'department', 'medicalRecords'])->first();`.
- Test UUIDs: Ensure `id` fields are 36-character strings (e.g., `550e8400-e29b-41d4-a716-446655440000`).
- Check foreign keys: Insert data to confirm cascade deletes work.

If you need controllers, Livewire components, or specific queries (e.g., "list employees on leave in Lagos"), let me know!
