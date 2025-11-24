# Where "_by" Actor Models Are Used in the Project

This document tracks all locations where the 25 models using "_by" morphic relationships are instantiated, imported, or used throughout the application.

---

## 1. EmployeeLeaveAllocation
**Purpose:** Track leave allocations with morphic "allocated_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/EmployeeLeaveAllocation.php:8`
- **Employee Relationship:** `app/Models/Employee.php:94` (hasMany)
- **Leave Balance Integration:** `app/Models/EmployeeLeaveBalance.php:51,86`
- **Management Interface:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ManageAllocations.php:8,95,128,159,165`
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:45`

---

## 2. ProductDispatch
**Purpose:** Track product dispatches with morphic "dispatched_by" and "received_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/ProductDispatch.php:10`
- **Production Interface:** `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php:8,343,689,754,773`
- **Sales Dispatch Management:** `app/Livewire/BranchDashboard/SalesDashboard/Dispatches/Index.php:6,60,130,154,285`
- **Callback Creation:** `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback.php:6,63,76,79,165,168,185,209,246`
- **Dispatch Callbacks:** `app/Models/ProductDispatchCallback.php:39` (belongsTo)
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:26`

---

## 3. DepartmentReport
**Purpose:** Track department reports with morphic "generated_by" and "reviewed_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/DepartmentReport.php:11`
- **Report Review Interface:** `app/Livewire/BranchDashboard/ReportingDepartment/ReviewReports/Index.php:5,40,112,132,135,138`
- **Reporting Dashboard:** `app/Livewire/BranchDashboard/ReportingDepartment/Dashboard/Index.php:5,40,44,56,61,66`
- **Waste Analysis Reports:** `app/Livewire/BranchDashboard/Production/Reports/WasteAnalysis/Index.php:5,126`
- **Inventory Reports:** Various `app/Livewire/BranchDashboard/Inventory/Reports/*/Index.php:5,95,120,124`
- **Compiled Report Relationships:** `app/Models/CompiledReport.php:89` (belongsToMany)
- **Report Service:** `app/Services/Reports/ReportService.php:5,51,59`
- **Report Compilation Service:** `app/Services/Reports/ReportCompilationService.php:6,26,27,32,35,38,41,60`
- **Data Seeding:** `database/seeders/ReportingSystemSeeder.php:7,100,160,228`

---

## 4. HealthCheck
**Purpose:** Track health checks with morphic "checked_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/HealthCheck.php:10`
- **Super Admin Interface:** `app/Livewire/SuperAdmin/Inventory/HealthChecks.php:6,23,49`
- **Branch Dashboard Interface:** `app/Livewire/BranchDashboard/Inventory/HealthChecks.php:5,66,90,96,122,147`
- **Stock Relationship:** `app/Models/Stock.php:88` (hasMany)
- **Data Seeding:** `database/seeders/InventorySeeder.php:8,70,71,426,438`

---

## 5. Purchase
**Purpose:** Track purchases with morphic "recorded_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/Purchase.php`
- **Purchase Management:** `app/Livewire/SuperAdmin/Inventory/Purchases.php:10,204`
- **Item Relationships:** `app/Models/Item.php` (relationship references)
- **Data Seeding:** `database/seeders/InventorySeeder.php:16,54`

---

## 6. StockMovement
**Purpose:** Track stock movements with morphic "moved_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/StockMovement.php`
- **Movement Tracking Interface:** `app/Livewire/SuperAdmin/Inventory/StockMovements.php:9,26`
- **Dispatch Movements:** `app/Livewire/SuperAdmin/Inventory/ItemDispatches.php:10,256`
- **Stock Adjustments:** `app/Livewire/SuperAdmin/Inventory/Stocks.php:9,258,276,294`
- **Item Movements:** `app/Livewire/SuperAdmin/Inventory/Items.php:375,447`
- **Purchase Movements:** `app/Livewire/SuperAdmin/Inventory/Purchases.php:10,204`
- **Analytics:** `app/Livewire/SuperAdmin/Analytics/StockMovementAnalytics.php:12`
- **Data Seeding:** `database/seeders/InventorySeeder.php:16,54,55,226,241`

---

## 7. ItemRequest
**Purpose:** Track item requests with morphic relationships (requested_by, approved_by, cancelled_by, dispatched_by)

**File Locations:**
- **Model Definition:** `app/Models/ItemRequest.php`
- **Request Processing:** `app/Livewire/SuperAdmin/Inventory/ItemDispatches.php:7,47,68,92,142,153,207`
- **Callback Handling:** `app/Livewire/BranchDashboard/Production/Callbacks/CreateInventoryCallback.php:179,180,223`
- **Item Request Details:** `app/Models/ItemRequestDetail.php` (relationship references)
- **Data Seeding:** `database/seeders/InventorySeeder.php:11,12,58,59,62,274,298,318`

---

## 8. EmployeeStepout
**Purpose:** Track employee step-outs with morphic "approved_by" and "rejected_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/EmployeeStepout.php:8`
- **Employee Relationship:** `app/Models/Employee.php:99` (hasMany)
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:48`

---

## 9. ProbationReview
**Purpose:** Track probation reviews with morphic "acknowledged_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/ProbationReview.php:9`
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:21,839`
- **Alternate Seeding:** `amp-codes/ComprehensiveSeeder.php:47,849`

---

## 10. CompiledReport
**Purpose:** Track compiled reports with morphic "compiled_by" and "approved_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/CompiledReport.php:11`
- **MD Report View:** `app/Livewire/SuperAdmin/MDReports/ViewReport/Index.php:5,13,18`
- **MD Dashboard:** `app/Livewire/SuperAdmin/MDReports/Dashboard/Index.php:5,29,41,54,58,59,60,61`
- **Report Submission to MD:** `app/Livewire/BranchDashboard/ReportingDepartment/SendToMD/Index.php:5,40,90,109`
- **Compiled Report Viewer:** `app/Livewire/BranchDashboard/ReportingDepartment/ViewCompiled/Index.php:5,16,35,49,54,59`
- **Report Compilation:** `app/Livewire/BranchDashboard/ReportingDepartment/CompileReports/Index.php:6,103,118`
- **Reporting Dashboard:** `app/Livewire/BranchDashboard/ReportingDepartment/Dashboard/Index.php:6,48,52`
- **Compilation Service:** `app/Services/Reports/ReportCompilationService.php:5,23,44,60,67,405,415,421`

---

## 11. ProductionCallback
**Purpose:** Track production callbacks with morphic "recorded_by" and "approved_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/ProductionCallback.php:8`
- **Callback Management:** `app/Livewire/BranchDashboard/Production/Callbacks/Index.php:6,52,62,121,161`
- **Callback Creation:** `app/Livewire/BranchDashboard/Production/Callbacks/CreateInventoryCallback.php:6,82,95,98,326`
- **Callback Approval:** `app/Livewire/BranchDashboard/Inventory/Callbacks/ApproveCallbacks.php:6,67,77,99,150,177,204,247,290,340,378`
- **Dashboard Summary:** `app/Livewire/BranchDashboard/Index.php:17,214`
- **Shift Relationship:** `app/Models/Shift.php:59` (hasMany)
- **Quality Reports Service:** `app/Services/Reports/ProductionQualityReportService.php:6,40`
- **Waste Analysis Service:** `app/Services/Reports/WasteAnalysisReportService.php:6,43`

---

## 12. SalaryHistory
**Purpose:** Track salary history with morphic "approved_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/SalaryHistory.php:9`
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:32,828`
- **Alternate Seeding:** `amp-codes/ComprehensiveSeeder.php:46,838`

---

## 13. LeaveApplication
**Purpose:** Track leave applications with morphic "approved_by", "rejected_by", "cancelled_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/LeaveApplication.php:9`
- **Leave Tracking:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/LeaveBalance.php:7,77`
- **Employee Leaves:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/MyLeaves.php:6,42,54,65,96,127`
- **Leave Approval:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ApproveLeave.php:6,48,58,68,92,110,136,158,184`
- **Employee Details:** `app/Livewire/BranchDashboard/EmployeeModule/Details.php:7,16,52,65,68`
- **Leave Application Form:** `app/Livewire/BranchDashboard/EmployeeModule/LeaveManagement/ApplyLeave.php:7,38,191,192,215`
- **Employee Relationships:** `app/Models/Employee.php:82,84,87,89`
- **Leave Type Relationships:** `app/Models/LeaveType.php:36,38`
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:18,803`

---

## 14. CallBack
**Purpose:** Track callbacks with morphic "reported_by" relationship

**File Locations:**
- **Model Definition:** Found in schema migrations

---

## 15. ExpiryConfirmation
**Purpose:** Track expiry confirmations with morphic "confirmed_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/ExpiryConfirmation.php:9`
- **Expiry Check Service:** `app/Services/CheckExpiredProducts.php:5,98,150,152,165,168`
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:14,782`
- **Alternate Seeding:** `amp-codes/ComprehensiveSeeder.php:38,792`

---

## 16. ReportTemplate
**Purpose:** Track report templates with morphic "created_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/ReportTemplate.php:11`

---

## 17. Recipe
**Purpose:** Track recipes with morphic "created_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/Recipe.php`
- **Daily Produce:** `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php` (recipe references)
- **Kitchen Module:** `app/Livewire/BranchDashboard/Production/KitchenModule/Index.php:126`
- **Shift Closing:** `app/Livewire/BranchDashboard/Production/ShiftClosing/Index.php:127,172`
- **Daily Produce Relationships:** `app/Models/DailyProduce.php` (hasMany)
- **Item Relationships:** `app/Models/Item.php` (relationship references)

---

## 18. StockTake
**Purpose:** Track stock takes with morphic "conducted_by" and "verified_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/StockTake.php:11`
- **Super Admin Management:** `app/Livewire/SuperAdmin/Inventory/StockTakes.php:6,10,25,37,41,49,54,58`
- **Branch Dashboard Management:** `app/Livewire/BranchDashboard/Inventory/StockTakes.php:5,6,16,26,31,62,74,77,96,98`
- **Data Seeding:** `database/seeders/InventorySeeder.php:17,18,66,67,367,369,370`

---

## 19. ItemDispatch
**Purpose:** Track item dispatches with morphic "dispatched_by" and "received_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/ItemDispatch.php:10`
- **Super Admin Dispatches:** `app/Livewire/SuperAdmin/Inventory/ItemDispatches.php:6,7,46,242,256`
- **Branch Dashboard Dispatches:** `app/Livewire/BranchDashboard/Inventory/ItemDispatches.php:5,68,314`
- **Request Dispatch Analytics:** `app/Livewire/BranchDashboard/Analytics/RequestDispatchAnalytics.php:6,105`
- **Item Request Relationship:** `app/Models/ItemRequest.php:108` (hasMany)
- **Data Seeding:** `database/seeders/InventorySeeder.php:10,62,63,318,333`

---

## 20. SalesShift
**Purpose:** Track sales shifts with morphic "verified_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/SalesShift.php`
- **Shift Creation:** `app/Livewire/Auth/Shift.php:7,76,77,79,88,201,205,210`
- **POS System:** `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php:11,259`
- **Callback Dispatch Creation:** `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback.php:8,27,28,93,96,97,101,110,118`
- **Callback Management:** `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/Index.php:7,35,99,107,115,120,128,136`
- **Dispatch Callback Relationship:** `app/Models/ProductDispatchCallback.php` (belongsTo)
- **Expiry Confirmation Relationship:** `app/Models/ExpiryConfirmation.php` (belongs-to)

---

## 21. Sale
**Purpose:** Track sales with morphic "sold_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/Sale.php`
- **Extensive usage throughout sales-related components and reporting services**

---

## 22. ProductCallback
**Purpose:** Track product callbacks with morphic "recorded_by" relationship

**File Locations:**
- **Referenced in schema and services**
- **Waste Analysis Service:** `app/Services/Reports/WasteAnalysisReportService.php:81,88,209,210`
- **Callback Modal Handling:** `app/Livewire/BranchDashboard/Production/Callbacks/CreateInventoryCallback.php:240`

---

## 23. ProductDispatchCallback
**Purpose:** Track product dispatch callbacks with morphic "recorded_by", "approved_by", "received_by" relationships

**File Locations:**
- **Model Definition:** `app/Models/ProductDispatchCallback.php:8`
- **Approval Management:** `app/Livewire/BranchDashboard/Production/Callbacks/ApproveCallbacks.php:6,7,54,64,65,66,86,89,94,129`
- **Callback Creation:** `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/CreateDispatchCallback.php:7,168,185,209,246`
- **Callback Index:** `app/Livewire/BranchDashboard/SalesDashboard/Callbacks/Index.php:6,70,80,185,212,248,250`
- **Dashboard Summary:** `app/Livewire/BranchDashboard/Index.php:18`
- **Dispatch Relationship:** `app/Models/ProductDispatch.php:85` (hasMany)
- **Blade Views:** `resources/views/livewire/branch-dashboard/sales-dashboard/callbacks/index.blade.php:41,45,49,53`
- **Create View:** `resources/views/livewire/branch-dashboard/sales-dashboard/callbacks/create-dispatch-callback.blade.php:136`

---

## 24. ApprovedItem
**Purpose:** Track approved items with morphic "approved_by" relationship

**File Locations:**
- **Referenced in IDE Helper**
- **Data Seeding:** `database/seeders/ComprehensiveSeeder.php:37`

---

## 25. ProductionRecord
**Purpose:** Track production records with morphic "produced_by" relationship

**File Locations:**
- **Model Definition:** `app/Models/ProductionRecord.php:9`
- **Production Recording:** `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php:175,223,257,541,545,546,547,548,549,550`
- **Kitchen Module:** `app/Livewire/BranchDashboard/Production/KitchenModule/Index.php:126,140`
- **Shift Closing:** `app/Livewire/BranchDashboard/Production/ShiftClosing/Index.php:7,127,132,133,134,135,140,172,184`
- **Branch Dashboard:** `app/Livewire/BranchDashboard/Index.php:15,197,206,207,208,230`
- **Stock Opening:** `app/Livewire/BranchDashboard/SalesDashboard/StockOpening/Index.php:258,277`
- **Daily Produce Relationships:** `app/Models/DailyProduce.php:53,55,186`
- **Recipe Relationships:** `app/Models/Recipe.php:72,74`
- **Quality Reports Service:** `app/Services/Reports/ProductionQualityReportService.php:5,29,52,53,54,56,57,70,72,73`
- **Efficiency Reports Service:** `app/Services/Reports/ProductionEfficiencyReportService.php:6,41,57,164,166`
- **Waste Analysis Service:** `app/Services/Reports/WasteAnalysisReportService.php:5,30,55,56,58,59,60`
- **Data Seeding:** `database/seeders/ProductionSeeder.php:6,121`
- **Comprehensive Seeding:** `database/seeders/ComprehensiveSeeder.php:23,568`

---

## Summary Statistics

| Category | Count |
|----------|-------|
| Models Using "_by" Relationships | 25 |
| Primary Livewire Components | 30+ |
| Service Classes | 8+ |
| Database Seeder Files | 5+ |
| Blade View Files | 5+ |
| Total Relational References | 100+ |

## Usage Pattern Overview

### Most Heavily Used Models
1. **ProductionRecord** - Used in 8+ Livewire components
2. **LeaveApplication** - Used in 5+ Livewire components
3. **ProductDispatchCallback** - Used in 3+ Livewire components with views
4. **DepartmentReport** - Used in 6+ Livewire components

### Usage Categories
- **Employee Management:** EmployeeLeaveAllocation, EmployeeStepout, ProbationReview, SalaryHistory
- **Inventory Management:** Purchase, StockMovement, ItemRequest, StockTake, ItemDispatch, HealthCheck
- **Production Management:** ProductDispatch, ProductionCallback, ProductDispatchCallback, ProductionRecord, Recipe
- **Reporting:** DepartmentReport, CompiledReport, ReportTemplate
- **Sales Management:** Sale, SalesShift, ProductDispatchCallback, ExpiryConfirmation
- **Leave Management:** LeaveApplication, EmployeeLeaveAllocation

### Service Layer Integration
- Quality and efficiency reports depend on ProductionRecord
- Waste analysis depends on ProductionCallback and ProductCallback
- Expiry checking depends on ExpiryConfirmation
- Report compilation depends on DepartmentReport and CompiledReport

---

## Key Observations

1. **High Integration with Livewire Components:** Most "_by" models are actively used in Livewire components for real-time data management
2. **Service Layer Dependencies:** Multiple service classes depend on these models for report generation and business logic
3. **Seeding Infrastructure:** All models are supported by seeding infrastructure for development and testing
4. **View Layer:** Several models have dedicated Blade views for display and interaction
5. **Morphic Relationships:** All models successfully implement UUID-based morphic relationships as documented in _by.md

---

Generated: November 23, 2025
Related Documentation: `MDs/_by.md`
