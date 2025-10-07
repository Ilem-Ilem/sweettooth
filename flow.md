restaurant-management-system/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       ├── GenerateDailyReports.php
│   │       ├── CheckExpiredCertifications.php
│   │       └── AutoCalculateClosingStock.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginController.php
│   │   │   │   └── LogoutController.php
│   │   │   │
│   │   │   ├── Admin/
│   │   │   │   ├── UserController.php
│   │   │   │   ├── BranchController.php
│   │   │   │   └── DashboardController.php
│   │   │   │
│   │   │   ├── HR/
│   │   │   │   ├── EmployeeController.php
│   │   │   │   ├── LeaveRequestController.php
│   │   │   │   ├── PermitRequestController.php
│   │   │   │   ├── MedicalRecordController.php
│   │   │   │   └── CertificationController.php
│   │   │   │
│   │   │   ├── Inventory/
│   │   │   │   ├── PurchaseController.php
│   │   │   │   ├── StockController.php
│   │   │   │   ├── RequestController.php
│   │   │   │   ├── DispatchController.php
│   │   │   │   └── ReportController.php
│   │   │   │
│   │   │   ├── Production/
│   │   │   │   ├── Kitchen/
│   │   │   │   │   ├── RecipeController.php
│   │   │   │   │   ├── ShiftController.php
│   │   │   │   │   ├── ProductionController.php
│   │   │   │   │   └── DailyProduceController.php
│   │   │   │   │
│   │   │   │   ├── Gelato/
│   │   │   │   │   ├── BaseProductionController.php
│   │   │   │   │   ├── FlavorController.php
│   │   │   │   │   └── ProductionController.php
│   │   │   │   │
│   │   │   │   ├── Pastry/
│   │   │   │   │   ├── RecipeController.php
│   │   │   │   │   └── ProductionController.php
│   │   │   │   │
│   │   │   │   └── HotKitchen/
│   │   │   │       ├── RecipeController.php
│   │   │   │       └── ProductionController.php
│   │   │   │
│   │   │   └── Sales/
│   │   │       ├── CornerStore/
│   │   │       │   ├── ShiftController.php
│   │   │       │   ├── SalesController.php
│   │   │       │   ├── TableController.php
│   │   │       │   └── OrderController.php
│   │   │       │
│   │   │       ├── Till/
│   │   │       │   ├── ShiftController.php
│   │   │       │   ├── SalesController.php
│   │   │       │   └── PaymentController.php
│   │   │       │
│   │   │       ├── Confectionery/
│   │   │       │   ├── SalesController.php
│   │   │       │   └── StockController.php
│   │   │       │
│   │   │       └── Reports/
│   │   │           ├── DailySalesController.php
│   │   │           ├── ProductAnalysisController.php
│   │   │           └── RevenueController.php
│   │   │
│   │   └── Middleware/
│   │       ├── CheckBranchAccess.php
│   │       ├── CheckRole.php
│   │       └── CheckShiftActive.php
│   │
│   ├── Livewire/
│   │   ├── Auth/
│   │   │   └── Login.php
│   │   │
│   │   ├── Admin/
│   │   │   ├── Users/
│   │   │   │   ├── Index.php
│   │   │   │   ├── Create.php
│   │   │   │   └── Edit.php
│   │   │   │
│   │   │   └── Branches/
│   │   │       ├── Index.php
│   │   │       ├── Create.php
│   │   │       └── Edit.php
│   │   │
│   │   ├── HR/
│   │   │   ├── Employees/
│   │   │   │   ├── Index.php
│   │   │   │   ├── Create.php
│   │   │   │   ├── Edit.php
│   │   │   │   └── Profile.php
│   │   │   │
│   │   │   ├── Leaves/
│   │   │   │   ├── Index.php
│   │   │   │   ├── Request.php
│   │   │   │   └── Approve.php
│   │   │   │
│   │   │   └── Permits/
│   │   │       ├── Index.php
│   │   │       ├── Request.php
│   │   │       └── Tracking.php
│   │   │
│   │   ├── Inventory/
│   │   │   ├── Purchases/
│   │   │   │   ├── Index.php
│   │   │   │   ├── Create.php
│   │   │   │   └── View.php
│   │   │   │
│   │   │   ├── Stock/
│   │   │   │   ├── Index.php
│   │   │   │   ├── StockTake.php
│   │   │   │   └── HealthCheck.php
│   │   │   │
│   │   │   ├── Requests/
│   │   │   │   ├── Pending.php
│   │   │   │   ├── Process.php
│   │   │   │   └── History.php
│   │   │   │
│   │   │   └── Reports/
│   │   │       ├── Monthly.php
│   │   │       ├── Weekly.php
│   │   │       └── Movement.php
│   │   │
│   │   ├── Production/
│   │   │   ├── Kitchen/
│   │   │   │   ├── Dashboard.php
│   │   │   │   ├── ShiftLogin.php
│   │   │   │   ├── Recipes/
│   │   │   │   │   ├── Index.php
│   │   │   │   │   ├── Create.php
│   │   │   │   │   └── Calculator.php
│   │   │   │   │
│   │   │   │   ├── Production/
│   │   │   │   │   ├── DailyProduce.php
│   │   │   │   │   ├── RequestItems.php
│   │   │   │   │   └── Closing.php
│   │   │   │   │
│   │   │   │   └── Reports/
│   │   │   │       ├── ShiftReport.php
│   │   │   │       └── ProductionAnalysis.php
│   │   │   │
│   │   │   ├── Gelato/
│   │   │   │   ├── Dashboard.php
│   │   │   │   ├── BaseProduction.php
│   │   │   │   ├── FlavorProduction.php
│   │   │   │   └── Reports.php
│   │   │   │
│   │   │   ├── Pastry/
│   │   │   │   ├── Dashboard.php
│   │   │   │   ├── Production.php
│   │   │   │   └── Reports.php
│   │   │   │
│   │   │   └── HotKitchen/
│   │   │       ├── Dashboard.php
│   │   │       ├── Production.php
│   │   │       └── Reports.php
│   │   │
│   │   └── Sales/
│   │       ├── CornerStore/
│   │       │   ├── Dashboard.php
│   │       │   ├── ShiftLogin.php
│   │       │   ├── Sales/
│   │       │   │   ├── NewSale.php
│   │       │   │   ├── TableOrders.php
│   │       │   │   └── History.php
│   │       │   │
│   │       │   ├── Tables/
│   │       │   │   ├── Monitor.php
│   │       │   │   └── Manage.php
│   │       │   │
│   │       │   └── Reports/
│   │       │       ├── ShiftReport.php
│   │       │       └── DailySales.php
│   │       │
│   │       ├── Till/
│   │       │   ├── Dashboard.php
│   │       │   ├── Sales.php
│   │       │   └── Reports.php
│   │       │
│   │       └── Confectionery/
│   │           ├── Sales.php
│   │           └── Reports.php
│   │
│   └── Models/
│       ├── User.php
│       ├── Branch.php
│       ├── Department.php
│       ├── Employee.php
│       ├── EmployeeMedicalRecord.php
│       ├── EmployeeCertification.php
│       ├── LeaveRequest.php
│       ├── PermitRequest.php
│       │
│       ├── Inventory/
│       │   ├── Item.php
│       │   ├── Purchase.php
│       │   ├── PurchaseItem.php
│       │   ├── Stock.php
│       │   ├── StockMovement.php
│       │   ├── ItemRequest.php
│       │   ├── ItemDispatch.php
│       │   ├── StockTake.php
│       │   └── HealthCheck.php
│       │
│       ├── Production/
│       │   ├── Recipe.php
│       │   ├── RecipeIngredient.php
│       │   ├── Shift.php
│       │   ├── DailyProduce.php
│       │   ├── ProductionRecord.php
│       │   ├── ProductionRequest.php
│       │   ├── CallBack.php
│       │   └── RawMaterialUtilization.php
│       │
│       └── Sales/
│           ├── Product.php
│           ├── ProductCategory.php
│           ├── Sale.php
│           ├── SaleItem.php
│           ├── Table.php
│           ├── TableOrder.php
│           ├── Payment.php
│           ├── ShiftSummary.php
│           └── Transfer.php
│
├── database/
│   ├── migrations/
│   │   ├── 2025_09_30_000001_create_users_table.php
│   │   ├── 2025_09_30_000002_create_branches_table.php
│   │   ├── 2025_09_30_000003_create_departments_table.php
│   │   ├── 2025_09_30_000004_create_employees_table.php
│   │   ├── 2025_09_30_000005_create_employee_medical_records_table.php
│   │   ├── 2025_09_30_000006_create_employee_certifications_table.php
│   │   ├── 2025_09_30_000007_create_leave_requests_table.php
│   │   ├── 2025_09_30_000008_create_permit_requests_table.php
│   │   │
│   │   ├── Inventory/
│   │   │   ├── 2025_09_30_100001_create_items_table.php
│   │   │   ├── 2025_09_30_100002_create_purchases_table.php
│   │   │   ├── 2025_09_30_100003_create_purchase_items_table.php
│   │   │   ├── 2025_09_30_100004_create_stocks_table.php
│   │   │   ├── 2025_09_30_100005_create_stock_movements_table.php
│   │   │   ├── 2025_09_30_100006_create_item_requests_table.php
│   │   │   ├── 2025_09_30_100007_create_item_dispatches_table.php
│   │   │   ├── 2025_09_30_100008_create_stock_takes_table.php
│   │   │   └── 2025_09_30_100009_create_health_checks_table.php
│   │   │
│   │   ├── Production/
│   │   │   ├── 2025_09_30_200001_create_recipes_table.php
│   │   │   ├── 2025_09_30_200002_create_recipe_ingredients_table.php
│   │   │   ├── 2025_09_30_200003_create_shifts_table.php
│   │   │   ├── 2025_09_30_200004_create_daily_produces_table.php
│   │   │   ├── 2025_09_30_200005_create_production_records_table.php
│   │   │   ├── 2025_09_30_200006_create_production_requests_table.php
│   │   │   ├── 2025_09_30_200007_create_call_backs_table.php
│   │   │   └── 2025_09_30_200008_create_raw_material_utilizations_table.php
│   │   │
│   │   └── Sales/
│   │       ├── 2025_09_30_300001_create_products_table.php
│   │       ├── 2025_09_30_300002_create_product_categories_table.php
│   │       ├── 2025_09_30_300003_create_sales_table.php
│   │       ├── 2025_09_30_300004_create_sale_items_table.php
│   │       ├── 2025_09_30_300005_create_tables_table.php
│   │       ├── 2025_09_30_300006_create_table_orders_table.php
│   │       ├── 2025_09_30_300007_create_payments_table.php
│   │       ├── 2025_09_30_300008_create_shift_summaries_table.php
│   │       └── 2025_09_30_300009_create_transfers_table.php
│   │
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── BranchFactory.php
│   │   ├── EmployeeFactory.php
│   │   └── ProductFactory.php
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── BranchSeeder.php
│       ├── DepartmentSeeder.php
│       └── ProductCategorySeeder.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── guest.blade.php
│   │   │   └── components/
│   │   │       ├── sidebar.blade.php
│   │   │       ├── header.blade.php
│   │   │       └── footer.blade.php
│   │   │
│   │   ├── livewire/
│   │   │   ├── admin/
│   │   │   ├── hr/
│   │   │   ├── inventory/
│   │   │   ├── production/
│   │   │   └── sales/
│   │   │
│   │   ├── auth/
│   │   │   └── login.blade.php
│   │   │
│   │   ├── dashboard/
│   │   │   ├── admin.blade.php
│   │   │   ├── branch-manager.blade.php
│   │   │   └── department.blade.php
│   │   │
│   │   └── reports/
│   │       ├── inventory/
│   │       ├── production/
│   │       └── sales/
│   │
│   ├── css/
│   │   └── app.css
│   │
│   └── js/
│       └── app.js
│
├── routes/
│   ├── web.php
│   ├── auth.php
│   ├── admin.php
│   ├── inventory.php
│   ├── production.php
│   └── sales.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── livewire.php
│   └── restaurant.php (custom config for business logic)
│
├── storage/
│   ├── app/
│   │   ├── public/
│   │   │   ├── employees/
│   │   │   │   ├── photos/
│   │   │   │   └── documents/
│   │   │   │
│   │   │   ├── inventory/
│   │   │   │   └── purchase-receipts/
│   │   │   │
│   │   │   └── reports/
│   │   │       ├── daily/
│   │   │       ├── weekly/
│   │   │       └── monthly/
│   │   │
│   │   └── exports/
│   │
│   └── logs/
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── HR/
│   │   ├── Inventory/
│   │   ├── Production/
│   │   └── Sales/
│   │
│   └── Unit/
│       ├── Models/
│       └── Services/
│
├── .env
├── .env.example
├── composer.json
├── package.json
├── artisan
└── README.md