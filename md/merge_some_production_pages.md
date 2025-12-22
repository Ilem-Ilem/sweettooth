# Production Pages Merge Analysis

## Current Production Pages (25 total)

## Route Structure Analysis

Routes are **not uniformly department-specific**:

### Department-Specific Routes (with {deptSlug}):
- Products, Product Types, Recipes, Requests, Daily Produce, Shift Closing

### Non-Department-Specific Routes (global for production):
- Kitchen Module (Dashboard + Stock Monitor)
- Raw Material Tracking
- Callbacks (View, Create, Approve)
- All Reports

### Recipe Management Section (4 pages)
- **Recipes** (`branch-dashboard.production.recipes.index`) - List recipes
- **Add Recipe** (`branch-dashboard.production.recipes.add`) - Add new recipe
- **Edit Recipe** (`branch-dashboard.production.recipes.edit`) - Edit existing recipe
- **Recipe Detail** (`branch-dashboard.production.recipes.detail`) - View recipe details

### Callback Management Section (3 pages)
- **View Callbacks** (`branch-dashboard.production.callbacks.index`) - List production callbacks
- **Create Inventory Callback** (`branch-dashboard.production.callbacks.create-inventory`) - Create callbacks for inventory
- **Approve Sales Callbacks** (`branch-dashboard.production.callbacks.approve-sales-callbacks`) - Approve sales callbacks

### Kitchen Module Section (2 pages)
- **Kitchen Dashboard** (`branch-dashboard.production.module.index`) - Kitchen operations overview
- **Stock Monitor** (`branch-dashboard.production.module.stock-monitor`) - Monitor stock levels

### Reports Section (9 pages)
- **Production Efficiency Report** (`branch-dashboard.production.reports.efficiency`)
- **Quality Metrics Report** (`branch-dashboard.production.reports.quality`)
- **Waste Analysis Report** (`branch-dashboard.production.reports.waste`)
- **Cost Analysis Report** (`branch-dashboard.production.reports.cost`)
- **Recipe Performance Report** (`branch-dashboard.production.reports.recipe-performance`)
- **Shift Summary Report** (`branch-dashboard.production.reports.shift-summary`)
- **Ingredient Utilization Report** (`branch-dashboard.production.reports.ingredient-utilization`)
- **Pipeline Status Report** (`branch-dashboard.production.reports.pipeline`)
- **Capacity Planning Report** (`branch-dashboard.production.reports.capacity`)

## Pages That Can Be Merged

### 1. Recipe Management Pages (Merge 3 pages into 1)

**Pages to merge:**
- Add Recipe
- Edit Recipe  
- Recipe Detail

**Into:** Single "Recipe Management" page

**Rationale:**
- All three pages perform CRUD operations on the same Recipe model
- Add/Edit/Detail are essentially different views of the same entity
- Current separation creates navigation friction
- Recipe Index already exists as the main listing page

**Implementation:**
- Keep Recipes Index as the main listing page
- Create a single Recipe Management component with modals for Add/Edit/View
- Use URL parameters or modal state to determine operation
- Reduce from 4 pages to 2 pages

**Code Structure:**
```php
// app/Livewire/BranchDashboard/Production/RecipeManagement.php
class RecipeManagement extends BaseComponent
{
    public $showModal = false;
    public $mode = 'view'; // 'view', 'add', 'edit'
    public $recipeId = null;

    // Include all form fields from Add/Edit components
    public $product_id = '';
    public $sku = '';
    // ... other fields

    public function openModal($mode, $recipeId = null)
    {
        $this->mode = $mode;
        $this->recipeId = $recipeId;
        $this->showModal = true;

        if ($mode === 'edit' || $mode === 'view') {
            $this->loadRecipe($recipeId);
        } elseif ($mode === 'add') {
            $this->resetForm();
        }
    }

    public function save()
    {
        if ($this->mode === 'add') {
            $this->createRecipe();
        } elseif ($this->mode === 'edit') {
            $this->updateRecipe();
        }
    }

    // Include methods from Add.php and Edit.php components
}
```

**Route Changes:**
```php
// Remove these routes:
Route::get('recipes/{deptSlug}/add', ...);
Route::get('recipes/{deptSlug}/{id}/edit', ...);
Route::get('recipes/{deptSlug}/{id}', ...);

// Update to single route with parameters:
Route::get('recipes/{deptSlug}/manage/{id?}', RecipeManagement::class)
    ->name('recipes.manage');
```

### 2. Request Management Pages (Merge 2 pages into 1)

**Pages to merge:**
- Production Requests (list)
- Create Production Request

**Into:** Single "Production Request Management" page

**Rationale:**
- Both pages work with ProductionRequest model
- Creating and viewing requests are closely related workflows
- Users often need to create requests while viewing existing ones
- Can be combined into tabs or sections

**Implementation:**
- Combine list view and creation form in one page
- Use tabs or collapsible sections

### 3. Callback Management Pages (Merge 2 pages into 1)

**Pages to merge:**
- View Callbacks
- Create Inventory Callback

**Into:** Single "Callback Management" page

**Rationale:**
- Both pages work with ProductionCallback model
- Creating and viewing callbacks are closely related workflows
- Users often need to create callbacks while viewing existing ones
- Approve Sales Callbacks can remain separate as it handles different callback types

**Implementation:**
- Combine list view and creation form in one page
- Use tabs or collapsible sections
- Keep Approve Sales Callbacks separate due to different workflow

**Code Structure:**
```php
// app/Livewire/BranchDashboard/Production/CallbackManagement.php
class CallbackManagement extends BaseComponent
{
    public $activeView = 'list'; // 'list', 'create'

    // List properties (from Index.php)
    public $search = '';
    public $filterStatus = null;

    // Create properties (from CreateInventoryCallback.php)
    public $callbackType = 'raw_material';
    public $selectedItemId = null;
    public $callbackQuantity = 0;

    public function switchView($view)
    {
        $this->activeView = $view;
        if ($view === 'create') {
            $this->resetCreateForm();
        }
    }

    public function submitCallback()
    {
        // Include validation and creation logic
        $this->validateCallback();
        $this->createCallback();
        $this->switchView('list');
        $this->toast()->success('Callback created successfully!');
    }

    // Include methods from both Index.php and CreateInventoryCallback.php
}
```

**Route Changes:**
```php
// Keep the existing route structure but replace components:
Route::get('/{deptSlug}', RequestManagement::class)->name('index');
// Remove this route entirely:
Route::get('/{deptSlug}/create', Create::class)->name('create');
```

### 4. Kitchen Module Pages ✅ **COMPLETED**

**Pages merged:**
- Kitchen Dashboard (`branch-dashboard.production.module.index`)
- Stock Monitor (`branch-dashboard.production.module.stock-monitor`)

**Into:** Enhanced Kitchen Module with tabs

**Implementation completed:**
- ✅ Added tab navigation (Dashboard/Stock Monitor)
- ✅ Integrated all StockMonitor functionality into Index.php
- ✅ Updated view with conditional rendering
- ✅ Removed redundant route and component files
- ✅ Updated DepartmentObserver to remove separate Stock Monitor entry

**Files changed:**
- `app/Livewire/BranchDashboard/Production/KitchenModule/Index.php` - Enhanced with tabs and stock monitor logic
- `resources/views/livewire/branch-dashboard/production/kitchen-module/index.blade.php` - Added tab UI and stock monitor content
- `routes/branch-route.php` - Removed stock-monitor route
- `app/Observers/DepartmentObserver.php` - Merged entries into single "Kitchen Module"
- Deleted: `StockMonitor.php` and `stock-monitor.blade.php`

**Route Changes:**
```php
// Remove this route from the module group:
Route::get('/stock-monitor', StockMonitor::class)->name('stock-monitor');

// Keep the main route unchanged:
Route::get('/', Index::class)->name('index');
```

### 5. Report Pages ✅ **COMPLETED**

**Merged 9 individual reports into 3 grouped report pages:**

**Operations Reports** (4 reports):
- Production Efficiency Report
- Quality Metrics Report
- Waste Analysis Report
- Cost Analysis Report

**Performance Reports** (3 reports):
- Recipe Performance Report
- Shift Summary Report
- Ingredient Utilization Report

**Planning Reports** (2 reports):
- Pipeline Status Report
- Capacity Planning Report

**Rationale:**
- Too many individual report pages created navigation complexity
- Related reports can be grouped logically by business function
- Users often need to compare related metrics within the same category
- **Reduced from 9 pages to 3 pages** (67% reduction)

**Implementation completed:**
- ✅ Created `OperationsReports.php`, `PerformanceReports.php`, `PlanningReports.php` components
- ✅ Added tab-based navigation within each grouped report
- ✅ Created corresponding Blade views with `@livewire` includes for existing report components
- ✅ Updated routes to include new grouped report endpoints
- ✅ Updated DepartmentObserver to use grouped report entries
- ✅ Removed old individual report entries from database
- ✅ Kept individual report routes for backward compatibility

**Files created:**
- `app/Livewire/BranchDashboard/Production/Reports/OperationsReports.php`
- `app/Livewire/BranchDashboard/Production/Reports/PerformanceReports.php`
- `app/Livewire/BranchDashboard/Production/Reports/PlanningReports.php`
- `resources/views/livewire/branch-dashboard/production/reports/operations-reports.blade.php`
- `resources/views/livewire/branch-dashboard/production/reports/performance-reports.blade.php`
- `resources/views/livewire/branch-dashboard/production/reports/planning-reports.blade.php`

**Database updated:**
- Removed 9 old individual report entries
- Added 3 new grouped report entries

**Route Changes:**
```php
// Replace these routes:
Route::get('/efficiency', ProductionEfficiency\Index::class)->name('efficiency');
Route::get('/quality', QualityMetrics\Index::class)->name('quality');
Route::get('/waste', WasteAnalysis\Index::class)->name('waste');
Route::get('/cost', CostAnalysis\Index::class)->name('cost');

// With grouped routes:
Route::get('/operations', Reports\OperationsReports::class)->name('operations');
Route::get('/performance', Reports\PerformanceReports::class)->name('performance');
Route::get('/planning', Reports\PlanningReports::class)->name('planning');
```

**Updated DepartmentObserver:**
```php
protected function getDefaultProductionPages(): array
{
    return [
        // ... existing pages ...

        // Grouped Reports Section
        [
            'name' => 'Operations Reports',
            'slug' => 'reports-operations',
            'route_name' => "branch-dashboard.production.reports.operations",
            'icon' => 'chart-bar',
            'order' => 16,
        ],
        [
            'name' => 'Performance Reports',
            'slug' => 'reports-performance',
            'route_name' => "branch-dashboard.production.reports.performance",
            'icon' => 'star',
            'order' => 17,
        ],
        [
            'name' => 'Planning Reports',
            'slug' => 'reports-planning',
            'route_name' => "branch-dashboard.production.reports.planning",
            'icon' => 'server',
            'order' => 18,
        ],
    ];
}
```

**Code Structure:**
```php
// app/Livewire/BranchDashboard/Production/Reports/OperationsReports.php
class OperationsReports extends Component
{
    public $activeReport = 'efficiency'; // 'efficiency', 'quality', 'waste', 'cost'

    public $dateRange = '30'; // days
    public $startDate = null;
    public $endDate = null;

    public function switchReport($reportType)
    {
        $this->activeReport = $reportType;
        $this->loadReportData();
    }

    public function getReportDataProperty()
    {
        return match($this->activeReport) {
            'efficiency' => $this->getEfficiencyData(),
            'quality' => $this->getQualityData(),
            'waste' => $this->getWasteData(),
            'cost' => $this->getCostData(),
        };
    }

    private function getEfficiencyData()
    {
        // Include logic from ProductionEfficiency/Index.php
        return [
            'efficiency_rate' => 85.5,
            'target' => 90.0,
            'trends' => $this->calculateEfficiencyTrends(),
        ];
    }

    // Include methods from all 4 report components
}

// Similar structure for PerformanceReports.php and PlanningReports.php
```

**Route Changes:**
```php
// Replace these routes:
Route::get('/efficiency', ProductionEfficiency\Index::class)->name('efficiency');
Route::get('/quality', QualityMetrics\Index::class)->name('quality');
Route::get('/waste', WasteAnalysis\Index::class)->name('waste');
Route::get('/cost', CostAnalysis\Index::class)->name('cost');

// With grouped routes:
Route::get('/operations', Reports\OperationsReports::class)->name('operations');
Route::get('/performance', Reports\PerformanceReports::class)->name('performance');
Route::get('/planning', Reports\PlanningReports::class)->name('planning');
```

**Updated DepartmentObserver:**
```php
protected function getDefaultProductionPages(): array
{
    return [
        // ... existing pages ...

        // Grouped Reports Section
        [
            'name' => 'Operations Reports',
            'slug' => 'reports-operations',
            'route_name' => "branch-dashboard.production.reports.operations",
            'icon' => 'chart-bar',
            'order' => 16,
        ],
        [
            'name' => 'Performance Reports',
            'slug' => 'reports-performance',
            'route_name' => "branch-dashboard.production.reports.performance",
            'icon' => 'star',
            'order' => 17,
        ],
        [
            'name' => 'Planning Reports',
            'slug' => 'reports-planning',
            'route_name' => "branch-dashboard.production.reports.planning",
            'icon' => 'server',
            'order' => 18,
        ],
    ];
}
```

## Pages That Should NOT Be Merged

### Standalone Pages (10 pages remain separate)
- Products - Distinct product management
- Product Types - Product categorization
- Daily Produce - Production tracking
- Raw Material Tracking - Inventory tracking
- Shift Closing - Shift management
- Approve Sales Callbacks - Different callback workflow

## Summary

**Current:** 25 production pages
**After merge:** 13 pages (48% reduction) ✅
**Completed:** 2 merges + 4 page removals
- ✅ Kitchen Module: 2 → 1 page
- ✅ Reports: 9 → 3 pages
- ✅ Removed: Add Recipe, Edit Recipe, Recipe Detail, Create Inventory Callback

**Final Production Pages (13 total):**
1. Products
2. Product Types
3. Recipes
4. Production Requests
5. Daily Produce
6. Raw Material Tracking
7. Shift Closing
8. View Callbacks
9. Approve Sales Callbacks
10. Kitchen Module
11. Operations Reports
12. Performance Reports
13. Planning Reports

**Remaining Merge Opportunities:**
- **Recipes** could be enhanced with inline add/edit/detail modals
- **Production Requests** could be combined with a "Create Request" modal
- **View Callbacks** could include creation functionality
- **Approve Sales Callbacks** remains separate (different workflow)

**Merge Plan:**
1. **Recipe Management:** 4 → 1 page (-3) **[PARTIALLY COMPLETED - removed 3 pages]**
2. **Request Management:** 3 → 2 pages (-1)
3. **Callback Management:** 3 → 2 pages (-1) **[PARTIALLY COMPLETED - removed 1 page]**
4. ✅ **Kitchen Module:** 2 → 1 page (-1) **[COMPLETED]**
5. ✅ **Reports:** 9 → 3 pages (-6) **[COMPLETED]**

This consolidation will significantly improve navigation while preserving all functionality.</content>
<parameter name="filePath">md/merge_some_production_pages.md