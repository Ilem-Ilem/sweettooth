# Observers for HR and Payroll Integration

This guide explains how observers will automate HR and payroll processes, including time tracking, payslip generation, and financial integration.

## HR/Payroll Observers

### 1. TimeEntryObserver

Handles time tracking and project allocation.

```php
class TimeEntryObserver
{
    public function created(TimeEntry $entry): void
    {
        $this->updateProjectTime($entry);
        $this->checkOvertime($entry);
    }

    private function updateProjectTime(TimeEntry $entry): void
    {
        if ($entry->project_id) {
            // Update project time totals
        }
    }

    private function checkOvertime(TimeEntry $entry): void
    {
        $weeklyHours = $this->calculateWeeklyHours($entry->employee_id);
        if ($weeklyHours > 40) {
            $entry->update(['type' => 'overtime']);
        }
    }
}
```

### 2. PayslipObserver

Manages payslip processing and GL posting.

```php
class PayslipObserver
{
    protected GlPostingService $glPostingService;

    public function updated(Payslip $payslip): void
    {
        if ($payslip->wasChanged('status') && $payslip->status === 'approved') {
            $this->postToGL($payslip);
            $this->updateEmployeeBalances($payslip);
        }
    }

    private function postToGL(Payslip $payslip): void
    {
        try {
            if ($payslip->gl_posting_status !== 'pending') return;
            
            $this->glPostingService->postPayrollExpense($payslip);
            $payslip->update(['gl_posting_status' => 'posted']);
        } catch (Exception $e) {
            $payslip->update([
                'gl_posting_status' => 'failed',
                'gl_posting_error' => $e->getMessage(),
            ]);
        }
    }

    private function updateEmployeeBalances(Payslip $payslip): void
    {
        // Update employee payroll balances
    }
}
```

### 3. EmployeeObserver

Handles employee lifecycle events.

```php
class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        $this->assignEmployeeNumber($employee);
        $this->setupPayrollDefaults($employee);
    }

    public function updated(Employee $employee): void
    {
        if ($employee->wasChanged('active') && !$employee->active) {
            $this->processTermination($employee);
        }
    }

    private function assignEmployeeNumber(Employee $employee): void
    {
        $nextNumber = Employee::max('employee_number') + 1;
        $employee->update(['employee_number' => str_pad($nextNumber, 4, '0', STR_PAD_LEFT)]);
    }

    private function processTermination(Employee $employee): void
    {
        // Finalize payslips, update records
    }
}
```

### 4. ExpenseClaimObserver (Enhanced)

Links expense claims to payroll.

```php
class ExpenseClaimObserver
{
    public function updated(ExpenseClaim $claim): void
    {
        if ($claim->wasChanged('status') && $claim->status === 'approved') {
            $this->addToNextPayslip($claim);
        }
    }

    private function addToNextPayslip(ExpenseClaim $claim): void
    {
        // Create payslip item for reimbursement
    }
}
```

## Automated Payroll Processing

### Scheduled Commands

```php
// In App\Console\Commands\GeneratePayslips.php
class GeneratePayslips extends Command
{
    public function handle()
    {
        $employees = Employee::active()->get();
        
        foreach ($employees as $employee) {
            $this->generatePayslip($employee);
        }
    }

    private function generatePayslip(Employee $employee)
    {
        $payPeriod = $this->calculatePayPeriod();
        $grossPay = $this->calculateGrossPay($employee, $payPeriod);
        
        Payslip::create([
            'employee_id' => $employee->id,
            'pay_period_start' => $payPeriod['start'],
            'pay_period_end' => $payPeriod['end'],
            'gross_pay' => $grossPay,
            // Calculate deductions, net pay
        ]);
    }
}
```

## Registration

```php
public function boot()
{
    TimeEntry::observe(TimeEntryObserver::class);
    Payslip::observe(PayslipObserver::class);
    Employee::observe(EmployeeObserver::class);
    ExpenseClaim::observe(ExpenseClaimObserver::class);
}
```

## Benefits

- Automated payroll calculations
- Real-time time tracking integration
- Consistent GL posting for labor costs
- Streamlined employee management

This creates an efficient HR and payroll system with minimal manual processing.