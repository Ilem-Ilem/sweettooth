# Core Accounting Features Integration to Laravel System

This guide explains how to integrate core accounting features from Manager.io into the existing Laravel-based accounting system, building on the current inventory and basic invoicing setup.

## Features Covered
- Summary (Dashboard overview)
- Bank and Cash Accounts
- Receipts and Payments
- Inter Account Transfers
- Bank Reconciliations
- Expense Claims
- Journal Entries
- Reports
- Settings

## Implementation Steps

### 1. Database Migrations

Add the following migrations to support core accounting structures:

```php
// Bank and Cash Accounts
Schema::create('bank_accounts', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('account_number')->nullable();
    $table->string('bank_name')->nullable();
    $table->decimal('balance', 15, 2)->default(0);
    $table->timestamps();
});

// Receipts and Payments
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->string('type'); // receipt, payment, transfer
    $table->foreignId('bank_account_id')->constrained();
    $table->date('transaction_date');
    $table->decimal('amount', 15, 2);
    $table->string('reference')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});

// Inter Account Transfers
Schema::create('account_transfers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('from_account_id')->constrained('bank_accounts');
    $table->foreignId('to_account_id')->constrained('bank_accounts');
    $table->date('transfer_date');
    $table->decimal('amount', 15, 2);
    $table->text('notes')->nullable();
    $table->timestamps();
});

// Bank Reconciliations
Schema::create('bank_reconciliations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('bank_account_id')->constrained();
    $table->date('statement_date');
    $table->decimal('statement_balance', 15, 2);
    $table->decimal('book_balance', 15, 2);
    $table->json('reconciled_transaction_ids')->nullable(); // IDs of reconciled transactions
    $table->timestamps();
});

// Expense Claims
Schema::create('expense_claims', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained(); // Assuming employees table exists
    $table->date('claim_date');
    $table->decimal('total_amount', 15, 2);
    $table->string('status')->default('pending'); // pending, approved, rejected
    $table->text('description')->nullable();
    $table->timestamps();
});

// Journal Entries
Schema::create('journal_entries', function (Blueprint $table) {
    $table->id();
    $table->date('entry_date');
    $table->string('reference')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});

Schema::create('journal_lines', function (Blueprint $table) {
    $table->id();
    $table->foreignId('journal_entry_id')->constrained()->cascadeOnDelete();
    $table->foreignId('account_id')->constrained(); // Assuming chart of accounts exists
    $table->decimal('debit', 15, 2)->default(0);
    $table->decimal('credit', 15, 2)->default(0);
    $table->timestamps();
});
```

Run `php artisan migrate` after creating these.

### 2. Models and Relationships

Create Eloquent models with appropriate relationships:

```php
class BankAccount extends Model {
    public function transactions() { return $this->hasMany(Transaction::class); }
    public function transfersFrom() { return $this->hasMany(AccountTransfer::class, 'from_account_id'); }
    public function transfersTo() { return $this->hasMany(AccountTransfer::class, 'to_account_id'); }
}

class Transaction extends Model {
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
}

class AccountTransfer extends Model {
    public function fromAccount() { return $this->belongsTo(BankAccount::class, 'from_account_id'); }
    public function toAccount() { return $this->belongsTo(BankAccount::class, 'to_account_id'); }
}

class ExpenseClaim extends Model {
    public function employee() { return $this->belongsTo(Employee::class); }
}

class JournalEntry extends Model {
    public function lines() { return $this->hasMany(JournalLine::class); }
}

class JournalLine extends Model {
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
    public function account() { return $this->belongsTo(Account::class); }
}
```

### 3. Controllers and Routes

Create controllers for each feature:

- `BankAccountController` for managing accounts
- `TransactionController` for receipts/payments
- `AccountTransferController` for transfers
- `BankReconciliationController` for reconciliations
- `ExpenseClaimController` for claims
- `JournalEntryController` for manual entries
- `ReportController` for generating reports

Add routes in `routes/web.php` or `routes/accounting.php`.

### 4. Views and Forms

Create Blade views for:
- Dashboard summary (charts showing account balances, recent transactions)
- Account management forms
- Transaction entry forms
- Transfer forms
- Reconciliation interfaces
- Expense claim submissions
- Journal entry forms
- Report generators

Use Laravel Collective forms or Livewire for dynamic forms.

### 5. Integration Points

- **Summary/Dashboard**: Aggregate data from all tables for financial overview
- **Bank Accounts**: Link to transactions and transfers for balance calculations
- **Transactions**: Auto-update account balances via model observers
- **Transfers**: Create paired transactions for both accounts
- **Reconciliations**: Mark transactions as reconciled and calculate adjustments
- **Expense Claims**: Integrate with approval workflows and payment processing
- **Journal Entries**: Manual double-entry bookkeeping linked to chart of accounts
- **Reports**: Use Laravel queries or packages like Laravel Excel for financial reports
- **Settings**: Admin panel for configuring tax codes, currencies, etc.

### 6. Additional Enhancements

- Use Laravel observers to maintain account balances automatically
- Implement audit trails for all financial transactions
- Add multi-currency support if needed
- Integrate with existing invoice system for automatic transaction creation
- Use Laravel Cashier or similar for advanced reconciliation features

This provides a solid foundation for core accounting functionality, extending the existing Laravel system to match Manager.io's capabilities.