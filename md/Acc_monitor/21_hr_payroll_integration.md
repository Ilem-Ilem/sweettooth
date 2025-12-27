# HR and Payroll Features Integration to Laravel System

This guide explains how to integrate HR and payroll features from Manager.io into the existing Laravel-based accounting system.

## Features Covered
- Employees
- Payslips

## Implementation Steps

### 1. Database Migrations

Add employee and payroll tables:

```php
// Employees
Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('employee_number')->unique();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email')->unique();
    $table->date('hire_date');
    $table->date('birth_date')->nullable();
    $table->string('department')->nullable();
    $table->string('position')->nullable();
    $table->decimal('salary', 15, 2)->nullable();
    $table->decimal('hourly_rate', 15, 2)->nullable();
    $table->string('employment_type')->default('full_time'); // full_time, part_time, contract
    $table->boolean('active')->default(true);
    $table->timestamps();
});

// Payslips
Schema::create('payslips', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
    $table->date('pay_period_start');
    $table->date('pay_period_end');
    $table->date('pay_date');
    $table->decimal('gross_pay', 15, 2);
    $table->decimal('net_pay', 15, 2);
    $table->decimal('total_deductions', 15, 2)->default(0);
    $table->decimal('total_allowances', 15, 2)->default(0);
    $table->string('status')->default('draft'); // draft, approved, paid
    $table->timestamps();
});

// Payslip Items
Schema::create('payslip_items', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('name');
    $table->string('type'); // earning, deduction, tax
    $table->boolean('taxable')->default(true);
    $table->boolean('active')->default(true);
    $table->timestamps();
});

Schema::create('payslip_item_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('payslip_id')->constrained()->cascadeOnDelete();
    $table->foreignId('payslip_item_id')->constrained();
    $table->decimal('amount', 15, 2);
    $table->decimal('quantity', 15, 4)->nullable(); // for hourly calculations
    $table->text('notes')->nullable();
    $table->timestamps();
});

// Time Tracking for payroll
Schema::create('time_entries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained();
    $table->date('entry_date');
    $table->decimal('hours_worked', 8, 2);
    $table->string('type')->default('regular'); // regular, overtime
    $table->text('description')->nullable();
    $table->timestamps();
});
```

### 2. Models and Relationships

```php
class Employee extends Model {
    public function payslips() { return $this->hasMany(Payslip::class); }
    public function timeEntries() { return $this->hasMany(TimeEntry::class); }
    public function expenseClaims() { return $this->hasMany(ExpenseClaim::class); } // from earlier
}

class Payslip extends Model {
    public function employee() { return $this->belongsTo(Employee::class); }
    public function itemLines() { return $this->hasMany(PayslipItemLine::class); }
}

class PayslipItem extends Model {
    public function lines() { return $this->hasMany(PayslipItemLine::class); }
}

class PayslipItemLine extends Model {
    public function payslip() { return $this->belongsTo(Payslip::class); }
    public function payslipItem() { return $this->belongsTo(PayslipItem::class); }
}

class TimeEntry extends Model {
    public function employee() { return $this->belongsTo(Employee::class); }
}
```

### 3. Controllers and Automation

- `EmployeeController` for CRUD operations
- `PayslipController` for payroll processing
- `TimeEntryController` for time tracking

Implement automated payslip generation based on salary, time entries, and payslip items.

### 4. Integration Points

- **Accounting**: Auto-create journal entries for payroll expenses
- **Time Tracking**: Integrate with billable time for employee utilization
- **Expense Claims**: Link to employee reimbursements
- **Tax Calculations**: Automated tax withholding and reporting

### 5. Additional Features

- Leave management (vacation, sick days)
- Performance reviews
- Employee self-service portal
- Payroll tax calculations
- Integration with government reporting

This adds comprehensive HR and payroll capabilities to track employee data and process compensation.