<?php

namespace Database\Seeders;

use App\Models\AccountingPeriod;
use App\Models\GlAccount;
use App\Models\GlEntry;
use App\Models\BankAccount;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Item;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\DailyProduce;
use App\Models\ProductionRecord;
use App\Models\RawMaterialUtilization;
use App\Models\StockMovement;
use App\Models\StockTake;
use App\Models\ProductStock;
use App\Models\ItemRequest;
use App\Models\LeaveApplication;
use App\Models\EmployeeLeaveBalance;
use App\Models\SalaryHistory;
use App\Models\ProbationReview;
use App\Models\ClockIn;
use App\Models\AuditLog;
use App\Models\ApprovalRequest;
use App\Models\Receipt;
use App\Models\HealthCheck;
use App\Models\ExpiryConfirmation;
use App\Models\BranchAccountingCash;
use App\Models\GlobalAccountingCash;
use App\Models\AccountTransfer;
use App\Models\Supplier;
use App\Models\SupplierContact;
use App\Models\SupplierDocument;
use App\Models\ProductionRequest;
use App\Models\UnitOfMeasure;
use App\Models\DepartmentProduct;
use App\Models\ProductType;
use App\Models\DailyBankPosition;
use App\Models\DailyBankTransaction;
use App\Models\BankReconciliation;
use App\Models\BankReconciliationDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('🚀 Starting comprehensive dummy data seeding...');

        // 1. Create accounting periods
        $this->command->info('📅 Creating accounting periods...');
        $this->createAccountingPeriods();

        // 2. Create GL accounts
        $this->command->info('📊 Creating GL accounts...');
        $this->createGlAccounts();

        // 3. Create bank accounts
        $this->command->info('🏦 Creating bank accounts...');
        $this->createBankAccounts();

        // 4. Create branches
        $this->command->info('🏭 Creating branches...');
        $branches = $this->createBranches();

        // 5. Create departments
        $this->command->info('🏢 Creating departments...');
        $departments = $this->createDepartments($branches);

        // 6. Create employees
        $this->command->info('👥 Creating employees...');
        $employees = $this->createEmployees($branches, $departments);

        // 7. Create units of measure
        $this->command->info('📏 Creating units of measure...');
        $this->createUnitsOfMeasure();

        // 8. Create items
        $this->command->info('📦 Creating items...');
        $items = $this->createItems($branches);

        // 9. Create stocks
        $this->command->info('📦 Creating stocks...');
        $this->createStocks($branches, $items);

        // 10. Create product types
        $this->command->info('🏷️ Creating product types...');
        $productTypes = $this->createProductTypes($departments);

        // 11. Create products
        $this->command->info('🍰 Creating products...');
        $products = $this->createProducts($branches, $productTypes);

        // 12. Create department-product relationships
        $this->command->info('🔗 Creating department-product relationships...');
        $this->createDepartmentProducts($departments, $products);

        // 13. Create recipes
        $this->command->info('📖 Creating recipes...');
        $recipes = $this->createRecipes($branches, $departments, $items);

        // 14. Create daily produces
        $this->command->info('🏭 Creating daily produces...');
        $this->createDailyProduces($departments, $recipes);

        // 15. Create production records
        $this->command->info('🏭 Creating production records...');
        $this->createProductionRecords($employees, $recipes);

        // 16. Create raw material utilization
        $this->command->info('🏭 Creating raw material utilization...');
        $this->createRawMaterialUtilization($departments, $recipes, $items);

        // 17. Create sales
        $this->command->info('💰 Creating sales...');
        $this->createSales($branches, $employees, $products);

        // 18. Create purchases
        $this->command->info('🛒 Creating purchases...');
        $this->createPurchases($branches, $employees, $items);

        // 19. Create payments
        $this->command->info('💳 Creating payments...');
        $this->createPayments();

        // 20. Create stock movements
        $this->command->info('📊 Creating stock movements...');
        $this->createStockMovements($branches, $employees, $items);

        // 21. Create stock takes
        $this->command->info('📊 Creating stock takes...');
        $this->createStockTakes($branches, $employees, $items);

        // 22. Create product stocks
        $this->command->info('📊 Creating product stocks...');
        $this->createProductStocks($branches, $products);

        // 23. Create item requests
        $this->command->info('📋 Creating item requests...');
        $this->createItemRequests($branches, $employees);

        // 24. Create leave applications
        $this->command->info('🏖️ Creating leave applications...');
        $this->createLeaveApplications($employees);

        // 25. Create employee leave balances
        $this->command->info('📊 Creating employee leave balances...');
        $this->createEmployeeLeaveBalances($employees);

        // 26. Create salary histories
        $this->command->info('💰 Creating salary histories...');
        $this->createSalaryHistories($employees);

        // 27. Create probation reviews
        $this->command->info('📝 Creating probation reviews...');
        $this->createProbationReviews($employees);

        // 28. Create clock ins
        $this->command->info('⏰ Creating clock ins...');
        $this->createClockIns($employees, $branches);

        // 29. Create audit logs
        $this->command->info('🔍 Creating audit logs...');
        $this->createAuditLogs($branches, $employees);

        // 30. Create approval requests
        $this->command->info('✅ Creating approval requests...');
        $this->createApprovalRequests($employees);

        // 31. Create receipts
        $this->command->info('🧾 Creating receipts...');
        $this->createReceipts();

        // 32. Create health checks
        $this->command->info('🏥 Creating health checks...');
        $this->createHealthChecks();

        // 33. Create expiry confirmations
        $this->command->info('📅 Creating expiry confirmations...');
        $this->createExpiryConfirmations($branches, $items);

        // 34. Create suppliers
        $this->command->info('🏢 Creating suppliers...');
        $this->createSuppliers();

        // 35. Create supplier contacts
        $this->command->info('📞 Creating supplier contacts...');
        $this->createSupplierContacts();

        // 36. Create supplier documents
        $this->command->info('📄 Creating supplier documents...');
        $this->createSupplierDocuments();

        // 37. Create production requests
        $this->command->info('📋 Creating production requests...');
        $this->createProductionRequests($branches, $employees);

        // 38. Create daily bank positions
        $this->command->info('🏦 Creating daily bank positions...');
        $this->createDailyBankPositions();

        // 39. Create daily bank transactions
        $this->command->info('💳 Creating daily bank transactions...');
        $this->createDailyBankTransactions();

        // 40. Create bank reconciliations
        $this->command->info('📊 Creating bank reconciliations...');
        $this->createBankReconciliations();

        // 41. Create GL entries
        $this->command->info('📝 Creating GL entries...');
        $this->createGlEntries();

        // 42. Create accounting cash records
        $this->command->info('💰 Creating accounting cash records...');
        $this->createAccountingCashRecords();

        // 43. Create account transfers
        $this->command->info('🔄 Creating account transfers...');
        $this->createAccountTransfers();

        $this->command->info('✅ Comprehensive dummy data seeding completed successfully!');
    }

    private function createAccountingPeriods()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Create periods for current year and previous year
        for ($year = $currentYear - 1; $year <= $currentYear + 1; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $startDate = Carbon::createFromDate($year, $month, 1);
                $endDate = $startDate->clone()->endOfMonth();

                // Determine status
                $today = Carbon::now();
                if ($endDate < $today) {
                    $status = 'closed'; // Past periods are closed
                } elseif ($startDate <= $today && $endDate >= $today) {
                    $status = 'open'; // Current month is open
                } else {
                    $status = 'open'; // Future months are open
                }

                AccountingPeriod::create([
                    'year' => $year,
                    'month' => $month,
                    'period_start' => $startDate,
                    'period_end' => $endDate,
                    'status' => $status,
                ]);
            }
        }
    }

    private function createGlAccounts()
    {
        $accounts = [
            // ASSETS (1000-1900)
            ['account_number' => '1010', 'account_name' => 'Cash - Head Office', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1020', 'account_name' => 'Cash - Branch A', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1030', 'account_name' => 'Cash - Branch B', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1040', 'account_name' => 'Petty Cash', 'account_type' => 'asset', 'account_category' => 'cash', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1050', 'account_name' => 'Bank Account - Main', 'account_type' => 'asset', 'account_category' => 'bank', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1060', 'account_name' => 'Bank Account - Branch A', 'account_type' => 'asset', 'account_category' => 'bank', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1070', 'account_name' => 'Bank Account - Branch B', 'account_type' => 'asset', 'account_category' => 'bank', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1100', 'account_name' => 'Accounts Receivable', 'account_type' => 'asset', 'account_category' => 'receivables', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1200', 'account_name' => 'Inventory - Raw Materials', 'account_type' => 'asset', 'account_category' => 'inventory', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1210', 'account_name' => 'Inventory - Work in Progress', 'account_type' => 'asset', 'account_category' => 'inventory', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1220', 'account_name' => 'Inventory - Finished Goods', 'account_type' => 'asset', 'account_category' => 'inventory', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1300', 'account_name' => 'Fixed Assets - Equipment', 'account_type' => 'asset', 'account_category' => 'fixed_assets', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1310', 'account_name' => 'Fixed Assets - Building', 'account_type' => 'asset', 'account_category' => 'fixed_assets', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1400', 'account_name' => 'Accumulated Depreciation', 'account_type' => 'asset', 'account_category' => 'depreciation', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '1500', 'account_name' => 'Prepaid Expenses', 'account_type' => 'asset', 'account_category' => 'receivables', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // LIABILITIES (2000-2900)
            ['account_number' => '2010', 'account_name' => 'Accounts Payable', 'account_type' => 'liability', 'account_category' => 'payables', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '2020', 'account_name' => 'Sales Tax Payable', 'account_type' => 'liability', 'account_category' => 'tax_payable', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '2030', 'account_name' => 'Income Tax Payable', 'account_type' => 'liability', 'account_category' => 'tax_payable', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '2040', 'account_name' => 'Employee Withholding Payable', 'account_type' => 'liability', 'account_category' => 'payables', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '2100', 'account_name' => 'Short-term Loan', 'account_type' => 'liability', 'account_category' => 'loans', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '2200', 'account_name' => 'Long-term Loan', 'account_type' => 'liability', 'account_category' => 'loans', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '2300', 'account_name' => 'Accrued Expenses', 'account_type' => 'liability', 'account_category' => 'payables', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],

            // EQUITY (3000-3900)
            ['account_number' => '3010', 'account_name' => 'Capital Stock / Owner\'s Capital', 'account_type' => 'equity', 'account_category' => 'capital', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '3020', 'account_name' => 'Retained Earnings', 'account_type' => 'equity', 'account_category' => 'retained_earnings', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '3030', 'account_name' => 'Dividends', 'account_type' => 'equity', 'account_category' => 'capital', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // REVENUE (4000-4900)
            ['account_number' => '4010', 'account_name' => 'Sales Revenue - Retail', 'account_type' => 'revenue', 'account_category' => 'sales_revenue', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '4020', 'account_name' => 'Sales Revenue - Production', 'account_type' => 'revenue', 'account_category' => 'sales_revenue', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '4030', 'account_name' => 'Service Revenue', 'account_type' => 'revenue', 'account_category' => 'service_revenue', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '4040', 'account_name' => 'Other Income', 'account_type' => 'revenue', 'account_category' => 'other_income', 'normal_balance' => 'credit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '4050', 'account_name' => 'Discount Given (Contra Revenue)', 'account_type' => 'revenue', 'account_category' => 'sales_revenue', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // COST OF GOODS SOLD (5000-5900)
            ['account_number' => '5010', 'account_name' => 'Cost of Goods Sold', 'account_type' => 'cost_of_goods_sold', 'account_category' => 'cogs', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '5020', 'account_name' => 'Inventory Write-down / Damage Loss', 'account_type' => 'cost_of_goods_sold', 'account_category' => 'inventory_adjustment', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '5030', 'account_name' => 'Shrinkage Loss', 'account_type' => 'cost_of_goods_sold', 'account_category' => 'inventory_adjustment', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // OPERATING EXPENSES (6000-6900)
            ['account_number' => '6010', 'account_name' => 'Salary Expense - Management', 'account_type' => 'expense', 'account_category' => 'salary_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6020', 'account_name' => 'Salary Expense - Staff', 'account_type' => 'expense', 'account_category' => 'salary_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6030', 'account_name' => 'Utilities (Electricity, Water)', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6040', 'account_name' => 'Rent', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6050', 'account_name' => 'Advertising & Marketing', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6060', 'account_name' => 'Office Supplies', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6070', 'account_name' => 'Maintenance & Repairs', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6080', 'account_name' => 'Transportation & Logistics', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '6090', 'account_name' => 'Insurance', 'account_type' => 'expense', 'account_category' => 'operating_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // ADMINISTRATIVE EXPENSES (7000-7900)
            ['account_number' => '7010', 'account_name' => 'Professional Fees (Accounting, Legal)', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '7020', 'account_name' => 'Audit Fees', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '7030', 'account_name' => 'Bank Charges', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '7040', 'account_name' => 'Software & IT', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '7050', 'account_name' => 'Office Equipment', 'account_type' => 'expense', 'account_category' => 'admin_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '7060', 'account_name' => 'Depreciation Expense', 'account_type' => 'expense', 'account_category' => 'depreciation', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // FINANCE COSTS (8000-8900)
            ['account_number' => '8010', 'account_name' => 'Interest Expense', 'account_type' => 'expense', 'account_category' => 'finance_cost', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '8020', 'account_name' => 'Exchange Loss/Gain', 'account_type' => 'expense', 'account_category' => 'finance_cost', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '8030', 'account_name' => 'Finance Charges', 'account_type' => 'expense', 'account_category' => 'finance_cost', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],

            // TAX ACCOUNTS (9000-9900)
            ['account_number' => '9010', 'account_name' => 'Income Tax Expense', 'account_type' => 'tax', 'account_category' => 'salary_expense', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
            ['account_number' => '9020', 'account_name' => 'VAT Expense (Input VAT)', 'account_type' => 'tax', 'account_category' => 'receivables', 'normal_balance' => 'debit', 'is_header' => false, 'is_active' => true],
        ];

        foreach ($accounts as $account) {
            GlAccount::create($account);
        }
    }

    private function createBankAccounts()
    {
        $glAccounts = GlAccount::where('account_category', 'bank')->get();
        $glAccount = $glAccounts->first() ?: GlAccount::create([
            'account_number' => '1050',
            'account_name' => 'Main Bank Account',
            'account_type' => 'asset',
            'account_category' => 'bank',
            'description' => 'Main bank account for the business',
            'normal_balance' => 'debit',
            'is_header' => false,
            'is_active' => true,
        ]);

        $banks = [
            ['name' => 'Access Bank', 'code' => '044', 'account' => '0012345678', 'type' => 'checking'],
            ['name' => 'GTBank', 'code' => '057', 'account' => '0087654321', 'type' => 'savings'],
            ['name' => 'First Bank', 'code' => '011', 'account' => '0054321678', 'type' => 'checking'],
            ['name' => 'UBA', 'code' => '033', 'account' => '2345678901', 'type' => 'checking'],
            ['name' => 'Zenith Bank', 'code' => '057', 'account' => '3456789012', 'type' => 'savings'],
        ];

        foreach ($banks as $bank) {
            BankAccount::create([
                'bank_name' => $bank['name'],
                'bank_code' => $bank['code'],
                'account_number' => $bank['account'],
                'account_type' => $bank['type'],
                'gl_account_id' => $glAccount->id,
                'opening_balance' => rand(500000, 2000000),
                'is_active' => true,
            ]);
        }
    }

    private function createBranches()
    {
        $faker = Faker::create();
        $branches = [];
        $cities = ['Lagos', 'Abuja', 'Port Harcourt', 'Calabar', 'Enugu', 'Kano', 'Ibadan', 'Benin City', 'Warri', 'Jos'];

        for ($i = 1; $i <= 5; $i++) {
            $city = $faker->randomElement($cities);
            $branches[] = Branch::create([
                'name' => "SweetTooth {$city} Branch {$i}",
                'code' => strtoupper(substr($city, 0, 3)).'-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'location' => $faker->streetAddress().', '.$city,
                'phone' => '+234-'.$faker->numberBetween(800, 909).'-'.$faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000, 9999),
                'email' => strtolower($city).$i.'@sweettooth.com',
                'description' => "SweetTooth branch in {$city}",
                'country' => 'Nigeria',
                'state' => $city,
                'city' => $city,
                'postal_code' => $faker->numberBetween(100000, 999999),
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ]);
        }

        return collect($branches);
    }

    private function createDepartments($branches)
    {
        $production = \App\Models\DepartmentCategory::firstOrCreate(['name' => 'Production']);
        $sales = \App\Models\DepartmentCategory::firstOrCreate(['name' => 'Sales']);
        $support = \App\Models\DepartmentCategory::firstOrCreate(['name' => 'Support']);

        $departments = [
            // Production
            ['name' => 'Kitchen', 'category_id' => $production->id],
            ['name' => 'Gelato Production', 'category_id' => $production->id],
            ['name' => 'Confectionaries Production', 'category_id' => $production->id],
            ['name' => 'Bakery', 'category_id' => $production->id],
            ['name' => 'Beverage Production', 'category_id' => $production->id],

            // Sales
            ['name' => 'Till', 'category_id' => $sales->id],
            ['name' => 'Corner Store', 'category_id' => $sales->id],
            ['name' => 'Confectionaries Sales', 'category_id' => $sales->id],
            ['name' => 'Dine-in Service', 'category_id' => $sales->id],
            ['name' => 'Online Orders', 'category_id' => $sales->id],

            // Support
            ['name' => 'Inventory/Store', 'category_id' => $support->id],
            ['name' => 'HR', 'category_id' => $support->id],
            ['name' => 'Finance', 'category_id' => $support->id],
            ['name' => 'IT', 'category_id' => $support->id],
            ['name' => 'Maintenance', 'category_id' => $support->id],
        ];

        $createdDepartments = [];
        foreach ($departments as $dept) {
            $branch = $branches->random();
            $createdDepartments[] = Department::create(array_merge($dept, [
                'description' => ucfirst($dept['name']).' department',
                'branch_id' => $branch->id,
            ]));
        }

        return collect($createdDepartments);
    }

    private function createEmployees($branches, $departments)
    {
        $faker = Faker::create();
        $employees = [];

        $nigerianNames = [
            'male' => ['Chukwuemeka', 'Oluwaseun', 'Abubakar', 'Emeka', 'Tunde', 'Chigozie', 'Ibrahim', 'Kunle', 'Obinna', 'Yusuf'],
            'female' => ['Ngozi', 'Amina', 'Chioma', 'Folake', 'Kemi', 'Blessing', 'Hauwa', 'Ada', 'Fatima', 'Nneka'],
        ];
        $surnames = ['Okafor', 'Adebayo', 'Mohammed', 'Nwankwo', 'Ogunleye', 'Chukwu', 'Bello', 'Okoro', 'Aliyu', 'Eze'];

        for ($i = 0; $i < 50; $i++) {
            $gender = $faker->randomElement(['male', 'female']);
            $firstName = $faker->randomElement($nigerianNames[$gender]);
            $lastName = $faker->randomElement($surnames);
            $branch = $branches->random();
            $department = $departments->random();

            $employees[] = Employee::create([
                'id' => $faker->uuid(),
                'branch_id' => $branch->id,
                'department_id' => $department->id,
                'employee_number' => 'EMP-'.str_replace(['-', ' '], '', strtoupper($branch->code)).'-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name' => $firstName.' '.$lastName,
                'email' => strtolower($firstName.'.'.$lastName.$i).'@sweettooth.com',
                'phone' => '+234-'.$faker->numberBetween(800, 909).'-'.$faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000, 9999),
                'address' => $faker->streetAddress().', '.$branch->city,
                'date_of_birth' => $faker->dateTimeBetween('-45 years', '-22 years')->format('Y-m-d'),
                'gender' => $gender,
                'nationality' => 'Nigerian',
                'emergency_contact_name' => $faker->randomElement($nigerianNames[$gender === 'male' ? 'female' : 'male']).' '.$faker->randomElement($surnames),
                'emergency_contact_phone' => '+234-'.$faker->numberBetween(800, 909).'-'.$faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000, 9999),
                'hire_date' => $faker->dateTimeBetween('-3 years', '-1 month')->format('Y-m-d'),
                'termination_date' => null,
                'status' => $faker->randomElement(['active', 'active', 'active', 'on_probation']),
                'probation_end_date' => null,
                'shift_preference' => $faker->randomElement(['morning', 'afternoon', 'rotating', 'flexible']),
                'salary' => $faker->randomFloat(2, 80000, 350000),
                'hourly_rate' => null,
                'tax_id' => 'TIN-'.$faker->numberBetween(10000000, 99999999),
                'bank_account' => $faker->numerify('##########'),
                'allergies' => $faker->boolean(15) ? $faker->randomElement(['None', 'Peanuts', 'Shellfish', 'Lactose']) : null,
                'profile_photo' => null,
                'last_performance_review_date' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'performance_rating' => $faker->randomFloat(1, 3.5, 5.0),
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return collect($employees);
    }

    private function createUnitsOfMeasure()
    {
        $measures = [
            ['name' => 'Pieces', 'abbreviation' => 'pcs', 'category' => 'count'],
            ['name' => 'Kilograms', 'abbreviation' => 'kg', 'category' => 'weight'],
            ['name' => 'Grams', 'abbreviation' => 'g', 'category' => 'weight'],
            ['name' => 'Liters', 'abbreviation' => 'L', 'category' => 'volume'],
            ['name' => 'Milliliters', 'abbreviation' => 'ml', 'category' => 'volume'],
            ['name' => 'Units', 'abbreviation' => 'units', 'category' => 'count'],
            ['name' => 'Cartons', 'abbreviation' => 'cartons', 'category' => 'count'],
            ['name' => 'Boxes', 'abbreviation' => 'boxes', 'category' => 'count'],
        ];

        foreach ($measures as $measure) {
            UnitOfMeasure::create($measure);
        }
    }

    private function createItems($branches)
    {
        $faker = Faker::create();
        $allItems = [];

        $itemTemplates = [
            ['name' => 'Sugar - White Granulated', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 50, 'max_stock_level' => 500],
            ['name' => 'Flour - All Purpose', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Cocoa Powder - Premium Dark', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 20, 'max_stock_level' => 200],
            ['name' => 'Butter - Salted', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 30, 'max_stock_level' => 300],
            ['name' => 'Eggs - Large Grade A', 'category' => 'raw_material', 'uom' => 'cartons', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Vanilla Extract - Pure', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 5, 'max_stock_level' => 30],
            ['name' => 'Chocolate Chips - Dark', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 15, 'max_stock_level' => 150],
            ['name' => 'Milk - Fresh Whole', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 20, 'max_stock_level' => 100],
            ['name' => 'Cream - Heavy Whipping', 'category' => 'raw_material', 'uom' => 'liters', 'reorder_level' => 10, 'max_stock_level' => 50],
            ['name' => 'Yeast - Active Dry', 'category' => 'raw_material', 'uom' => 'kg', 'reorder_level' => 5, 'max_stock_level' => 25],
            ['name' => 'Cake Boxes - 10 inch', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 100, 'max_stock_level' => 1000],
            ['name' => 'Paper Bags - Brown', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 500, 'max_stock_level' => 5000],
            ['name' => 'Plastic Containers', 'category' => 'packaging', 'uom' => 'pcs', 'reorder_level' => 150, 'max_stock_level' => 1500],
            ['name' => 'Dishwashing Liquid', 'category' => 'consumable', 'uom' => 'liters', 'reorder_level' => 20, 'max_stock_level' => 100],
            ['name' => 'Paper Towels', 'category' => 'consumable', 'uom' => 'units', 'reorder_level' => 50, 'max_stock_level' => 200],
        ];

        foreach ($branches as $branch) {
            $branchCode = strtoupper(substr($branch->code, 0, 3));

            for ($i = 0; $i < 20; $i++) {
                $template = $faker->randomElement($itemTemplates);
                $sku = sprintf('%s-ITM-%05d', $branchCode, $i + 1);

                $item = Item::create([
                    'branch_id' => $branch->id,
                    'name' => $template['name'].' - '.$faker->randomElement(['Premium', 'Standard', 'Bulk', 'Organic']),
                    'sku' => $sku,
                    'category' => $template['category'],
                    'uom' => $template['uom'],
                    'description' => $faker->sentence,
                    'reorder_level' => $template['reorder_level'],
                    'max_stock_level' => $template['max_stock_level'],
                    'status' => 'active',
                ]);

                $allItems[] = $item;
            }
        }

        return $allItems;
    }

    private function createStocks($branches, $items)
    {
        foreach ($branches as $branch) {
            foreach ($items as $item) {
                if ($item->branch_id === $branch->id) {
                    $quantityAvailable = rand(50, 400);
                    $quantityReserved = rand(0, (int) ($quantityAvailable * 0.1));
                    $quantityDamaged = rand(0, (int) ($quantityAvailable * 0.05));

                    $averageCost = match ($item->category) {
                        'raw_material' => rand(500, 5000) / 10,
                        'packaging' => rand(50, 500) / 10,
                        'consumable' => rand(300, 3000) / 10,
                        default => rand(500, 5000) / 10
                    };

                    $healthStatuses = ['good', 'good', 'good', 'warning', 'critical'];
                    $healthStatus = $faker->randomElement($healthStatuses);

                    $expiryDate = null;
                    if (in_array($item->category, ['raw_material', 'consumable'])) {
                        $daysToExpiry = match ($healthStatus) {
                            'good' => rand(90, 365),
                            'warning' => rand(30, 89),
                            'critical' => rand(7, 29),
                            default => rand(60, 180)
                        };
                        $expiryDate = now()->addDays($daysToExpiry);
                    }

                    Stock::create([
                        'branch_id' => $branch->id,
                        'item_id' => $item->id,
                        'quantity_available' => $quantityAvailable,
                        'quantity_reserved' => $quantityReserved,
                        'quantity_damaged' => $quantityDamaged,
                        'average_cost' => $averageCost,
                        'last_stock_take_date' => now()->subDays(rand(1, 30)),
                        'health_status' => $healthStatus,
                        'expiry_date' => $expiryDate,
                    ]);
                }
            }
        }
    }

    private function createProductTypes($departments)
    {
        $faker = Faker::create();
        $productTypes = [];

        $productionDepts = $departments->filter(function($dept) {
            return strpos(strtolower($dept->name), 'production') !== false;
        });

        $typeTemplates = [
            ['name' => 'Pastries', 'code' => 'PT'],
            ['name' => 'Breads', 'code' => 'BR'],
            ['name' => 'Cakes', 'code' => 'CK'],
            ['name' => 'Cookies', 'code' => 'CO'],
            ['name' => 'Gelato Base', 'code' => 'GB'],
            ['name' => 'Gelato Flavors', 'code' => 'GF'],
            ['name' => 'Chocolates', 'code' => 'CH'],
            ['name' => 'Candies', 'code' => 'CD'],
            ['name' => 'Beverages', 'code' => 'BV'],
            ['name' => 'Desserts', 'code' => 'DS'],
        ];

        foreach ($productionDepts as $dept) {
            for ($i = 0; $i < 5; $i++) {
                $template = $faker->randomElement($typeTemplates);
                
                $productTypes[] = ProductType::create([
                    'department_id' => $dept->id,
                    'name' => $template['name'].' '.$faker->randomElement(['Premium', 'Standard', 'Mini', 'Large']),
                    'code' => $template['code'].$i,
                    'description' => $faker->sentence,
                    'status' => 'active',
                    'sort_order' => $i + 1,
                ]);
            }
        }

        return collect($productTypes);
    }

    private function createProducts($branches, $productTypes)
    {
        $faker = Faker::create();
        $products = [];

        foreach ($branches as $branch) {
            foreach ($productTypes as $productType) {
                for ($i = 0; $i < 10; $i++) {
                    $products[] = Product::create([
                        'branch_id' => $branch->id,
                        'name' => $faker->randomElement(['Chocolate Cake', 'Vanilla Ice Cream', 'Strawberry Pastry', 'Blueberry Muffin', 'Dark Chocolate Truffle']).' '.$faker->randomElement(['Slice', 'Scoop', 'Piece', 'Box']),
                        'sku' => 'PRD-'.strtoupper(substr($branch->code, 0, 3)).'-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                        'product_type_id' => $productType->id,
                        'description' => $faker->sentence,
                        'price' => $faker->randomFloat(2, 2.50, 25.00),
                        'cost' => $faker->randomFloat(2, 0.80, 15.00),
                        'shelf_life_days' => $faker->numberBetween(1, 30),
                        'uom' => $faker->randomElement(['pcs', 'grams', 'kg', 'liters']),
                        'unit_weight' => $faker->numberBetween(50, 1000),
                        'is_active' => true,
                        'is_available' => $faker->boolean(90), // 90% available
                        'allergens' => $faker->randomElements(['gluten', 'dairy', 'eggs', 'nuts'], $faker->numberBetween(0, 3)),
                        'tags' => $faker->randomElements(['popular', 'seasonal', 'premium', 'diet'], $faker->numberBetween(0, 2)),
                    ]);
                }
            }
        }

        return collect($products);
    }

    private function createDepartmentProducts($departments, $products)
    {
        foreach ($departments as $department) {
            $deptProducts = $products->where('branch_id', $department->branch_id)->take(20);
            foreach ($deptProducts as $product) {
                DepartmentProduct::create([
                    'department_id' => $department->id,
                    'product_id' => $product->id,
                    'is_available' => true,
                    'display_order' => rand(1, 100),
                ]);
            }
        }
    }

    private function createRecipes($branches, $departments, $items)
    {
        $faker = Faker::create();
        $recipes = [];

        foreach ($branches as $branch) {
            $branchDepts = $departments->filter(function($dept) use ($branch) {
                return $dept->branch_id === $branch->id;
            });

            foreach ($branchDepts as $department) {
                if (strpos(strtolower($department->name), 'production') !== false) {
                    for ($i = 0; $i < 10; $i++) {
                        $recipe = Recipe::create([
                            'branch_id' => $branch->id,
                            'department_id' => $department->id,
                            'product_name' => $faker->randomElement(['Chocolate Cake', 'Vanilla Gelato', 'Butter Croissant', 'Strawberry Ice Cream', 'Blueberry Muffin']),
                            'sku' => 'REC-'.strtoupper(substr($branch->code, 0, 3)).'-'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                            'product_type' => $faker->randomElement(['gelato_base', 'gelato_flavor', 'pastry', 'hot_kitchen', 'beverage']),
                            'cost_per_unit' => $faker->randomFloat(4, 0.5, 50),
                            'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
                            'yield_quantity' => $faker->randomFloat(2, 1, 100),
                            'preparation_time' => $faker->numberBetween(5, 120),
                            'instructions' => $faker->paragraph,
                            'status' => $faker->randomElement(['active', 'inactive', 'testing']),
                            'created_by' => $faker->randomElement($employees)->id ?? 1,
                        ]);

                        // Add ingredients (3-8 per recipe)
                        $ingredientCount = $faker->numberBetween(3, 8);
                        $selectedItems = $faker->randomElements($items, $ingredientCount);

                        foreach ($selectedItems as $index => $item) {
                            RecipeIngredient::create([
                                'recipe_id' => $recipe->id,
                                'item_id' => $item->id,
                                'quantity' => $faker->randomFloat(4, 0.1, 10),
                                'uom' => $faker->randomElement(['grams', 'kg', 'liters', 'ml', 'pcs', 'units']),
                                'sort_order' => $index + 1,
                                'notes' => $faker->optional()->sentence,
                            ]);
                        }

                        $recipes[] = $recipe;
                    }
                }
            }
        }

        return collect($recipes);
    }

    private function createDailyProduces($departments, $recipes)
    {
        $faker = Faker::create();

        foreach ($departments as $department) {
            $deptRecipes = $recipes->filter(function($recipe) use ($department) {
                return $recipe->department_id === $department->id;
            });

            foreach ($deptRecipes as $recipe) {
                for ($i = 0; $i < 5; $i++) {
                    DailyProduce::create([
                        'recipe_id' => $recipe->id,
                        'produce_date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                        'shift_type' => $faker->randomElement(['morning', 'afternoon']),
                        'opening_quantity' => $faker->randomFloat(2, 0, 100),
                        'requested_quantity' => $faker->randomFloat(2, 0, 50),
                        'produced_quantity' => $faker->randomFloat(2, 0, 50),
                        'sent_out_quantity' => $faker->randomFloat(2, 0, 40),
                        'order_quantity' => $faker->randomFloat(2, 0, 30),
                        'callback_quantity' => $faker->randomFloat(2, 0, 10),
                        'closing_quantity' => $faker->randomFloat(2, 0, 50),
                        'expected_closing' => $faker->randomFloat(2, 0, 50),
                        'variance' => $faker->randomFloat(2, -10, 10),
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createProductionRecords($employees, $recipes)
    {
        $faker = Faker::create();

        foreach ($recipes as $recipe) {
            for ($i = 0; $i < 10; $i++) {
                $employee = $faker->randomElement($employees);
                
                ProductionRecord::create([
                    'recipe_id' => $recipe->id,
                    'produced_by' => $employee->id,
                    'quantity_produced' => $faker->randomFloat(2, 1, 50),
                    'quantity_approved' => $faker->randomFloat(2, 0, 50),
                    'quantity_rejected' => $faker->randomFloat(2, 0, 10),
                    'production_time' => $faker->dateTimeBetween('-1 day', 'now'),
                    'quality_status' => $faker->randomElement(['excellent', 'good', 'acceptable', 'rejected']),
                    'rejection_reason' => $faker->optional()->sentence,
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createRawMaterialUtilization($departments, $recipes, $items)
    {
        $faker = Faker::create();

        foreach ($departments as $department) {
            $deptRecipes = $recipes->filter(function($recipe) use ($department) {
                return $recipe->department_id === $department->id;
            });

            foreach ($deptRecipes as $recipe) {
                for ($i = 0; $i < 5; $i++) {
                    $item = $faker->randomElement($items);
                    $quantityRequired = $faker->randomFloat(4, 0.1, 10);
                    $quantityUsed = $quantityRequired + $faker->randomFloat(4, -0.5, 0.5);

                    RawMaterialUtilization::create([
                        'recipe_id' => $recipe->id,
                        'item_id' => $item->id,
                        'quantity_required' => $quantityRequired,
                        'quantity_used' => $quantityUsed,
                        'units_produced' => $faker->randomFloat(2, 1, 50),
                        'variance' => $quantityUsed - $quantityRequired,
                        'variance_type' => $faker->randomElement(['within_tolerance', 'over_used', 'under_used']),
                        'cost_impact' => $faker->randomFloat(2, 0, 100),
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createSales($branches, $employees, $products)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            $branchProducts = $products->filter(function($product) use ($branch) {
                return $product->branch_id === $branch->id;
            });

            for ($i = 0; $i < 50; $i++) {
                $employee = $faker->randomElement($employees);
                
                $sale = Sale::create([
                    'branch_id' => $branch->id,
                    'employee_id' => $employee->id,
                    'sale_number' => 'SALE-'.strtoupper(substr($branch->code, 0, 3)).'-'.str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'sale_date' => $faker->dateTimeBetween('-30 days', 'now'),
                    'total_amount' => 0, // Will be calculated
                    'discount_amount' => $faker->randomFloat(2, 0, 100),
                    'tax_amount' => 0, // Will be calculated
                    'grand_total' => 0, // Will be calculated
                    'payment_status' => $faker->randomElement(['paid', 'pending', 'partial']),
                    'sale_type' => $faker->randomElement(['dine_in', 'takeaway', 'delivery']),
                    'customer_name' => $faker->optional()->name,
                    'customer_phone' => $faker->optional()->phoneNumber,
                    'notes' => $faker->optional()->sentence,
                ]);

                // Sale Items (2-5 items per sale)
                $itemCount = $faker->numberBetween(2, 5);
                $selectedProducts = $faker->randomElements($branchProducts->toArray(), $itemCount);
                $totalAmount = 0;
                $taxAmount = 0;

                foreach ($selectedProducts as $product) {
                    $quantity = $faker->numberBetween(1, 5);
                    $unitPrice = $product->price;
                    $subtotal = $quantity * $unitPrice;
                    $tax = $subtotal * 0.075; // 7.5% tax

                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'tax_amount' => $tax,
                        'discount_amount' => $faker->randomFloat(2, 0, $subtotal * 0.1),
                        'total_amount' => $subtotal + $tax,
                        'notes' => $faker->optional()->sentence,
                    ]);

                    $totalAmount += $subtotal;
                    $taxAmount += $tax;
                }

                // Update sale totals
                $sale->update([
                    'total_amount' => $totalAmount,
                    'tax_amount' => $taxAmount,
                    'grand_total' => $totalAmount + $taxAmount - $sale->discount_amount,
                ]);

                // Payment
                if ($sale->payment_status !== 'pending') {
                    Payment::create([
                        'sale_id' => $sale->id,
                        'payment_method' => $faker->randomElement(['cash', 'card', 'transfer']),
                        'amount' => $sale->grand_total,
                        'payment_date' => $sale->sale_date,
                        'reference_number' => $faker->optional()->uuid,
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createPurchases($branches, $employees, $items)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            $branchItems = array_filter($items, function($item) use ($branch) {
                return $item->branch_id === $branch->id;
            });

            for ($i = 0; $i < 30; $i++) {
                $employee = $faker->randomElement($employees);

                $purchase = Purchase::create([
                    'branch_id' => $branch->id,
                    'supplier_name' => $faker->company,
                    'supplier_contact' => $faker->phoneNumber,
                    'purchase_order_number' => 'PO-'.strtoupper(substr($branch->code, 0, 3)).'-'.str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'purchase_date' => $faker->dateTimeBetween('-30 days', 'now'),
                    'expected_delivery_date' => $faker->dateTimeBetween('now', '+7 days'),
                    'total_amount' => 0, // Will be calculated
                    'status' => $faker->randomElement(['ordered', 'received', 'cancelled']),
                    'approved_by' => $employee->id,
                    'received_by' => $faker->optional()->randomElement($employees)->id,
                    'notes' => $faker->optional()->sentence,
                ]);

                // Purchase Items (3-8 items per purchase)
                $itemCount = $faker->numberBetween(3, 8);
                $selectedItems = $faker->randomElements($branchItems, $itemCount);
                $totalAmount = 0;

                foreach ($selectedItems as $item) {
                    $quantity = $faker->numberBetween(10, 100);
                    $unitPrice = $faker->randomFloat(2, 50, 500);
                    $subtotal = $quantity * $unitPrice;

                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'item_id' => $item->id,
                        'quantity_ordered' => $quantity,
                        'quantity_received' => $purchase->status === 'received' ? $quantity : 0,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'notes' => $faker->optional()->sentence,
                    ]);

                    $totalAmount += $subtotal;
                }

                $purchase->update(['total_amount' => $totalAmount]);
            }
        }
    }

    private function createPayments()
    {
        // Payments are already created with sales, but let's add some standalone payments
        $faker = Faker::create();
        $saleIds = Sale::pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            if (!empty($saleIds)) {
                $saleId = $faker->randomElement($saleIds);
                $sale = Sale::find($saleId);
                
                if ($sale && $sale->payment_status === 'pending') {
                    Payment::create([
                        'sale_id' => $saleId,
                        'payment_method' => $faker->randomElement(['cash', 'card', 'transfer']),
                        'amount' => $sale->grand_total,
                        'payment_date' => $faker->dateTimeBetween('-30 days', 'now'),
                        'reference_number' => $faker->optional()->uuid,
                        'notes' => $faker->optional()->sentence,
                    ]);
                    
                    $sale->update(['payment_status' => 'paid']);
                }
            }
        }
    }

    private function createStockMovements($branches, $employees, $items)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            $branchItems = array_filter($items, function($item) use ($branch) {
                return $item->branch_id === $branch->id;
            });

            foreach ($branchItems as $item) {
                for ($i = 0; $i < 5; $i++) {
                    StockMovement::create([
                        'branch_id' => $branch->id,
                        'item_id' => $item->id,
                        'movement_type' => $faker->randomElement(['in', 'out', 'adjustment']),
                        'quantity' => $faker->numberBetween(-50, 100),
                        'reason' => $faker->randomElement(['purchase', 'sale', 'adjustment', 'waste', 'transfer']),
                        'reference_number' => $faker->optional()->uuid,
                        'performed_by' => $faker->randomElement($employees)->id ?? 1,
                        'movement_date' => $faker->dateTimeBetween('-30 days', 'now'),
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createStockTakes($branches, $employees, $items)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            $branchItems = array_filter($items, function($item) use ($branch) {
                return $item->branch_id === $branch->id;
            });

            foreach ($branchItems as $item) {
                for ($i = 0; $i < 3; $i++) {
                    StockTake::create([
                        'branch_id' => $branch->id,
                        'item_id' => $item->id,
                        'counted_quantity' => $faker->numberBetween(0, 500),
                        'system_quantity' => $faker->numberBetween(0, 500),
                        'variance' => $faker->numberBetween(-50, 50),
                        'performed_by' => $faker->randomElement($employees)->id ?? 1,
                        'stock_take_date' => $faker->dateTimeBetween('-30 days', 'now'),
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createProductStocks($branches, $products)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            $branchProducts = $products->filter(function($product) use ($branch) {
                return $product->branch_id === $branch->id;
            });

            foreach ($branchProducts as $product) {
                ProductStock::create([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'quantity_available' => $faker->numberBetween(0, 200),
                    'quantity_reserved' => $faker->numberBetween(0, 20),
                    'quantity_damaged' => $faker->numberBetween(0, 10),
                    'last_updated' => $faker->dateTimeBetween('-7 days', 'now'),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createItemRequests($branches, $employees)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            for ($i = 0; $i < 15; $i++) {
                ItemRequest::create([
                    'branch_id' => $branch->id,
                    'requested_by' => $faker->randomElement($employees)->id ?? 1,
                    'approved_by' => $faker->optional()->randomElement($employees)->id,
                    'request_date' => $faker->dateTimeBetween('-30 days', 'now'),
                    'required_date' => $faker->dateTimeBetween('now', '+7 days'),
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected', 'fulfilled']),
                    'priority' => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createLeaveApplications($employees)
    {
        $faker = Faker::create();
        $leaveTypes = \App\Models\LeaveType::all();

        foreach ($employees as $employee) {
            for ($i = 0; $i < 3; $i++) {
                $leaveType = $faker->randomElement($leaveTypes);
                
                if ($leaveType) {
                    LeaveApplication::create([
                        'employee_id' => $employee->id,
                        'leave_type_id' => $leaveType->id,
                        'start_date' => $faker->dateTimeBetween('now', '+30 days'),
                        'end_date' => $faker->dateTimeBetween('+31 days', '+60 days'),
                        'days_requested' => $faker->numberBetween(1, 14),
                        'reason' => $faker->sentence,
                        'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                        'approved_by' => $faker->optional()->randomElement($employees)->id,
                        'approved_date' => $faker->optional()->dateTimeBetween('-30 days', 'now'),
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createEmployeeLeaveBalances($employees)
    {
        $faker = Faker::create();
        $leaveTypes = \App\Models\LeaveType::all();

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                EmployeeLeaveBalance::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => date('Y'),
                    'allocated_days' => $faker->numberBetween(10, 30),
                    'used_days' => $faker->numberBetween(0, 15),
                    'remaining_days' => $faker->numberBetween(5, 25),
                    'carried_forward' => $faker->numberBetween(0, 5),
                ]);
            }
        }
    }

    private function createSalaryHistories($employees)
    {
        $faker = Faker::create();

        foreach ($employees as $employee) {
            SalaryHistory::create([
                'employee_id' => $employee->id,
                'old_salary' => $faker->randomFloat(2, 50000, 200000),
                'new_salary' => $faker->randomFloat(2, 60000, 300000),
                'effective_date' => $faker->dateTimeBetween('-365 days', 'now'),
                'reason' => $faker->randomElement(['annual_review', 'promotion', 'cost_of_living', 'performance']),
                'approved_by' => $faker->optional()->randomElement($employees)->id,
                'notes' => $faker->optional()->sentence,
            ]);
        }
    }

    private function createProbationReviews($employees)
    {
        $faker = Faker::create();

        foreach ($employees as $employee) {
            if ($employee->status === 'on_probation') {
                ProbationReview::create([
                    'employee_id' => $employee->id,
                    'review_date' => $faker->dateTimeBetween('-180 days', 'now'),
                    'reviewer_id' => $faker->optional()->randomElement($employees)->id,
                    'performance_rating' => $faker->randomFloat(1, 1.0, 5.0),
                    'comments' => $faker->paragraph,
                    'recommendation' => $faker->randomElement(['extend_probation', 'confirm_employment', 'terminate']),
                    'next_review_date' => $faker->optional()->dateTimeBetween('now', '+180 days'),
                    'status' => $faker->randomElement(['completed', 'pending', 'overdue']),
                ]);
            }
        }
    }

    private function createClockIns($employees, $branches)
    {
        $faker = Faker::create();

        foreach ($employees as $employee) {
            for ($i = 0; $i < 10; $i++) {
                ClockIn::create([
                    'employee_id' => $employee->id,
                    'branch_id' => $faker->randomElement($branches)->id,
                    'clock_in_time' => $faker->dateTimeBetween('-8 hours', '-1 hour'),
                    'clock_out_time' => $faker->optional()->dateTimeBetween('-1 hour', 'now'),
                    'total_hours' => $faker->randomFloat(2, 4, 12),
                    'status' => $faker->randomElement(['active', 'completed', 'missed']),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createAuditLogs($branches, $employees)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            for ($i = 0; $i < 20; $i++) {
                AuditLog::create([
                    'branch_id' => $branch->id,
                    'causer_type' => \App\Models\Employee::class,
                    'causer_id' => $faker->randomElement($employees)->id ?? 1,
                    'auditable_type' => $faker->randomElement(['App\Models\Employee', 'App\Models\Product', 'App\Models\Sale', 'App\Models\Purchase']),
                    'auditable_id' => $faker->numberBetween(1, 1000),
                    'action' => $faker->randomElement(['create', 'update', 'delete', 'view']),
                    'description' => $faker->sentence,
                    'old_values' => ['field' => 'old_value'],
                    'new_values' => ['field' => 'new_value'],
                    'ip_address' => $faker->ipv4,
                    'user_agent' => $faker->userAgent,
                    'status' => $faker->randomElement(['completed', 'pending']),
                    'logged_at' => $faker->dateTimeBetween('-30 days', 'now'),
                    'details' => ['seeded' => true],
                ]);
            }
        }
    }

    private function createApprovalRequests($employees)
    {
        $faker = Faker::create();

        for ($i = 0; $i < 30; $i++) {
            ApprovalRequest::create([
                'request_type' => $faker->randomElement(['leave', 'purchase', 'expense', 'promotion']),
                'request_id' => $faker->numberBetween(1, 1000),
                'requested_by' => $faker->randomElement($employees)->id ?? 1,
                'approved_by' => $faker->optional()->randomElement($employees)->id,
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'approved_at' => $faker->optional()->dateTimeBetween('-30 days', 'now'),
                'comments' => $faker->optional()->sentence,
                'priority' => $faker->randomElement(['low', 'medium', 'high']),
            ]);
        }
    }

    private function createReceipts()
    {
        $faker = Faker::create();
        $saleIds = Sale::pluck('id')->toArray();

        foreach ($saleIds as $saleId) {
            Receipt::create([
                'sale_id' => $saleId,
                'receipt_number' => 'RCP-'.strtoupper(Str::random(3)).'-'.str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'printed_at' => $faker->dateTimeBetween('-30 days', 'now'),
                'printed_by' => 1, // Using default user
                'receipt_data' => json_encode(['total' => rand(1000, 50000)]),
            ]);
        }
    }

    private function createHealthChecks()
    {
        $services = ['database', 'cache', 'queue', 'storage'];
        
        foreach ($services as $service) {
            HealthCheck::create([
                'service_name' => $service,
                'status' => 'healthy',
                'response_time' => rand(100, 5000),
                'memory_usage' => rand(50, 1000),
                'checked_at' => now(),
                'details' => json_encode(['cpu' => rand(10, 90)]),
            ]);
        }
    }

    private function createExpiryConfirmations($branches, $items)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            $branchItems = array_filter($items, function($item) use ($branch) {
                return $item->branch_id === $branch->id;
            });

            foreach ($branchItems as $item) {
                if ($item->category === 'raw_material' || $item->category === 'consumable') {
                    ExpiryConfirmation::create([
                        'branch_id' => $branch->id,
                        'item_id' => $item->id,
                        'expiry_date' => $faker->dateTimeBetween('now', '+365 days'),
                        'quantity_affected' => $faker->numberBetween(1, 50),
                        'action_taken' => $faker->randomElement(['dispose', 'sell_at_discount', 'return_to_supplier']),
                        'confirmed_by' => 1, // Using default user
                        'confirmation_date' => $faker->dateTimeBetween('-30 days', 'now'),
                        'notes' => $faker->optional()->sentence,
                    ]);
                }
            }
        }
    }

    private function createSuppliers()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            Supplier::create([
                'name' => $faker->company,
                'email' => $faker->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'contact_person' => $faker->name,
                'tin' => 'TIN-'.$faker->numberBetween(10000000, 99999999),
                'bank_name' => $faker->randomElement(['Access Bank', 'GTBank', 'First Bank', 'UBA', 'Zenith Bank']),
                'account_number' => $faker->numerify('####################'),
                'account_name' => $faker->name,
                'payment_terms' => $faker->randomElement(['Net 30', 'Net 60', 'Due on Receipt', '2% 10 Net 30']),
                'credit_limit' => $faker->randomFloat(2, 10000, 1000000),
                'status' => $faker->randomElement(['active', 'inactive', 'suspended']),
                'notes' => $faker->optional()->sentence,
            ]);
        }
    }

    private function createSupplierContacts()
    {
        $faker = Faker::create();
        $suppliers = Supplier::all();

        foreach ($suppliers as $supplier) {
            for ($i = 0; $i < 2; $i++) {
                SupplierContact::create([
                    'supplier_id' => $supplier->id,
                    'name' => $faker->name,
                    'email' => $faker->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'position' => $faker->jobTitle,
                    'is_primary' => $i === 0,
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createSupplierDocuments()
    {
        $faker = Faker::create();
        $suppliers = Supplier::all();

        foreach ($suppliers as $supplier) {
            for ($i = 0; $i < 3; $i++) {
                SupplierDocument::create([
                    'supplier_id' => $supplier->id,
                    'document_type' => $faker->randomElement(['contract', 'certificate', 'insurance', 'license', 'agreement']),
                    'document_name' => $faker->word.'_document_'.$i.'.pdf',
                    'file_path' => 'documents/'.$faker->word.'_doc_'.$i.'.pdf',
                    'uploaded_by' => 1, // Using default user
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createProductionRequests($branches, $employees)
    {
        $faker = Faker::create();

        foreach ($branches as $branch) {
            for ($i = 0; $i < 15; $i++) {
                ProductionRequest::create([
                    'branch_id' => $branch->id,
                    'requested_by' => $faker->randomElement($employees)->id ?? 1,
                    'department_id' => $faker->randomElement($branch->departments)->id ?? 1,
                    'product_name' => $faker->randomElement(['Chocolate Cake', 'Vanilla Ice Cream', 'Butter Croissant']),
                    'quantity' => $faker->numberBetween(10, 100),
                    'priority' => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
                    'status' => $faker->randomElement(['pending', 'approved', 'in_progress', 'completed', 'cancelled']),
                    'request_date' => $faker->dateTimeBetween('-30 days', 'now'),
                    'required_date' => $faker->dateTimeBetween('now', '+7 days'),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createDailyBankPositions()
    {
        $faker = Faker::create();
        $bankAccounts = BankAccount::all();

        foreach ($bankAccounts as $account) {
            for ($i = 0; $i < 30; $i++) {
                DailyBankPosition::create([
                    'bank_account_id' => $account->id,
                    'position_date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                    'opening_balance' => $faker->randomFloat(2, 100000, 2000000),
                    'closing_balance' => $faker->randomFloat(2, 100000, 2000000),
                    'available_balance' => $faker->randomFloat(2, 100000, 2000000),
                    'book_balance' => $faker->randomFloat(2, 100000, 2000000),
                    'inflow_amount' => $faker->randomFloat(2, 0, 500000),
                    'outflow_amount' => $faker->randomFloat(2, 0, 500000),
                    'created_by' => 1, // Using default user
                ]);
            }
        }
    }

    private function createDailyBankTransactions()
    {
        $faker = Faker::create();
        $bankAccounts = BankAccount::all();

        foreach ($bankAccounts as $account) {
            for ($i = 0; $i < 50; $i++) {
                DailyBankTransaction::create([
                    'bank_account_id' => $account->id,
                    'transaction_date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                    'transaction_type' => $faker->randomElement(['inflow', 'outflow']),
                    'amount' => $faker->randomFloat(2, 1000, 100000),
                    'reference_number' => $faker->swiftBicNumber,
                    'description' => $faker->sentence,
                    'status' => $faker->randomElement(['pending', 'cleared', 'reconciled']),
                    'created_by' => 1, // Using default user
                ]);
            }
        }
    }

    private function createBankReconciliations()
    {
        $faker = Faker::create();
        $bankAccounts = BankAccount::all();
        $currentPeriod = AccountingPeriod::current()->first();

        foreach ($bankAccounts as $account) {
            if ($currentPeriod) {
                BankReconciliation::create([
                    'bank_account_id' => $account->id,
                    'accounting_period_id' => $currentPeriod->id,
                    'reconciliation_date' => $faker->dateTimeBetween('-30 days', 'now'),
                    'bank_balance' => $faker->randomFloat(2, 100000, 2000000),
                    'book_balance' => $faker->randomFloat(2, 100000, 2000000),
                    'difference' => $faker->randomFloat(2, -1000, 1000),
                    'status' => $faker->randomElement(['in_progress', 'completed']),
                    'reconciled_by' => 1, // Using default user
                    'completed_at' => $faker->optional()->dateTimeBetween('-30 days', 'now'),
                    'notes' => $faker->optional()->sentence,
                ]);
            }
        }
    }

    private function createGlEntries()
    {
        $faker = Faker::create();
        $glAccounts = GlAccount::all();
        $accountingPeriods = AccountingPeriod::all();
        $currentPeriod = $accountingPeriods->first();

        if (!$currentPeriod) {
            return; // Need accounting periods to create entries
        }

        // Create sample entries for different account types
        for ($i = 0; $i < 200; $i++) {
            $account = $faker->randomElement($glAccounts);
            $isDebit = $faker->boolean();

            // Determine if this should be a debit or credit based on account type
            $normalBalance = $account->normal_balance;
            $isDebit = ($normalBalance === 'debit') ? $faker->boolean(70) : $faker->boolean(30); // Higher chance of correct balance

            GlEntry::create([
                'gl_account_id' => $account->id,
                'accounting_period_id' => $currentPeriod->id,
                'entry_type' => $faker->randomElement(['sale', 'purchase', 'payment', 'adjustment', 'manual']),
                'reference_type' => 'sale',
                'reference_id' => $faker->numberBetween(1, 50),
                'reference_number' => 'REF-'.str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'description' => $faker->sentence,
                'debit' => $isDebit ? $faker->randomFloat(2, 1000, 50000) : 0,
                'credit' => !$isDebit ? $faker->randomFloat(2, 1000, 50000) : 0,
                'entry_date' => $faker->dateTimeBetween('-30 days', 'now'),
                'status' => $faker->randomElement(['draft', 'posted']),
                'entered_by_id' => 1, // Using default user
                'branch_id' => Branch::first()->id ?? 1,
            ]);
        }
    }

    private function createAccountingCashRecords()
    {
        $faker = Faker::create();
        $branches = Branch::all();

        foreach ($branches as $branch) {
            BranchAccountingCash::create([
                'branch_id' => $branch->id,
                'date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                'opening_cash' => $faker->randomFloat(2, 50000, 200000),
                'closing_cash' => $faker->randomFloat(2, 50000, 200000),
                'total_sales' => $faker->randomFloat(2, 10000, 100000),
                'total_expenses' => $faker->randomFloat(2, 5000, 50000),
                'cash_variance' => $faker->randomFloat(2, -1000, 1000),
                'notes' => $faker->optional()->sentence,
            ]);
        }

        GlobalAccountingCash::create([
            'date' => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'total_cash' => $faker->randomFloat(2, 500000, 2000000),
            'total_bank' => $faker->randomFloat(2, 1000000, 5000000),
            'total_cash_equivalent' => $faker->randomFloat(2, 1500000, 7000000),
            'notes' => $faker->optional()->sentence,
        ]);
    }

    private function createAccountTransfers()
    {
        $faker = Faker::create();
        $glAccounts = GlAccount::all();
        $branches = Branch::all();

        for ($i = 0; $i < 20; $i++) {
            $fromAccount = $faker->randomElement($glAccounts);
            $toAccount = $faker->randomElement($glAccounts->where('id', '!=', $fromAccount->id));

            if ($fromAccount && $toAccount) {
                AccountTransfer::create([
                    'from_account_id' => $fromAccount->id,
                    'to_account_id' => $toAccount->id,
                    'from_branch_id' => $faker->randomElement($branches)->id ?? 1,
                    'to_branch_id' => $faker->randomElement($branches)->id ?? 1,
                    'amount' => $faker->randomFloat(2, 10000, 100000),
                    'transfer_date' => $faker->dateTimeBetween('-30 days', 'now'),
                    'reference_number' => 'TRANSFER-'.str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                    'description' => $faker->sentence,
                    'status' => $faker->randomElement(['pending', 'completed']),
                    'initiated_by' => 1, // Using default user
                    'approved_by' => 1, // Using default user
                ]);
            }
        }
    }
}