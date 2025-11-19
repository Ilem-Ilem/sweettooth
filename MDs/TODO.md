# TODO: Product-Sales Department Relationship & Production Dispatch

## 🎯 Goal
Fix the production daily-produce system so that:
1. ✅ **Recipe yield is used** - Production records batches (e.g., 2 batches), system calculates total quantity (e.g., 2 × 20 cups = 40 cups)
2. ⏳ **Products have relationships with specific sales departments** - Each product belongs to specific sales department(s)
3. ⏳ **Production can dispatch to specific sales departments** - When sending products from production, they select which sales department
4. ⏳ **POS shows only department products** - Sales POS only displays products that belong to that specific sales department

---

## ✅ Completed Tasks

### 1. Recipe Yield-Based Production ✅
**Files Modified:**
- `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php`
- `resources/views/livewire/branch-dashboard/production/daily-produce/index.blade.php`

**Changes Made:**
- ✅ Added `$batchesProduced` property to track number of batches
- ✅ Auto-calculates `$batchQuantityProduced = batches × recipe.yield_quantity`
- ✅ Updated modal to ask for "Number of Batches" instead of raw quantity
- ✅ Shows calculation: "2 batches × 20 cups = 40 cups total"
- ✅ Quality control still works on total quantity (approved/rejected)

**Example:**
```
Recipe: Vanilla Gelato (yield: 20 cups per batch)
User produces: 2 batches
System calculates: 2 × 20 = 40 cups total
User specifies: 38 approved, 2 rejected
```

---

## ⏳ Pending Tasks

### 2. Product-Department Relationship (Many-to-Many) ⏳

**Current Status:**
- ✅ Pivot table `department_product` already exists
- ✅ Product model already has `departments()` relationship
- ❌ POS is NOT filtering products by department (shows all products)

**What Needs to be Fixed:**

#### A. Verify/Update Product Model Relationship
**File:** `app/Models/Product.php`
```php
// Already exists - verify it's working:
public function departments(): BelongsToMany
{
    return $this->belongsToMany(Department::class, 'department_product')
        ->withPivot(['is_available', 'department_price', 'sort_order'])
        ->withTimestamps();
}

// Add scope for filtering by department
public function scopeForDepartment($query, $departmentId)
{
    return $query->whereHas('departments', function ($q) use ($departmentId) {
        $q->where('department_id', $departmentId)
          ->where('is_available', true);
    });
}
```

#### B. Update POS to Filter Products by Department
**File:** `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php`

**Current Code (Line ~529):**
```php
public function getProductsProperty(): Collection
{
    // Currently shows ALL products - needs filtering!
}
```

**Fix Required:**
```php
public function getProductsProperty(): Collection
{
    return Product::query()
        ->active()
        ->available()
        ->forDepartment($this->departmentId) // Add this filter!
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            });
        })
        ->orderBy('name')
        ->get();
}
```

#### C. Add Product Management UI (Optional - for admins to assign products to departments)
**Location:** Consider creating a product assignment page for admins
- Allow selecting which departments each product belongs to
- Set department-specific pricing
- Set availability per department

---

### 3. Production Dispatch to Specific Sales Department ⏳

**Current Status:**
- ✅ `ProductDispatch` model exists
- ✅ Has `sales_shift_id` column
- ❌ Does NOT have `sales_department_id` column
- ❌ Production UI doesn't let users select which sales department

**What Needs to be Done:**

#### A. Add Migration for sales_department_id
**New File:** `database/migrations/YYYY_MM_DD_add_sales_department_to_product_dispatches.php`
```php
Schema::table('product_dispatches', function (Blueprint $table) {
    $table->unsignedBigInteger('sales_department_id')->nullable()->after('sales_shift_id');

    $table->foreign('sales_department_id')
          ->references('id')
          ->on('departments')
          ->onDelete('set null');

    $table->index('sales_department_id');
});
```

#### B. Update ProductDispatch Model
**File:** `app/Models/ProductDispatch.php`
```php
// Add to fillable
protected $fillable = [
    // ... existing fields
    'sales_department_id',
];

// Add relationship
public function salesDepartment(): BelongsTo
{
    return $this->belongsTo(Department::class, 'sales_department_id');
}
```

#### C. Update Production Dispatch Logic
**File:** `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php` (Line ~646)

**Current createProductDispatch Method:**
```php
private function createProductDispatch($produce, $quantity)
{
    // Currently doesn't specify sales department
    ProductDispatch::create([
        // ... existing fields
        'sales_shift_id' => ???, // How is this determined?
    ]);
}
```

**Fix Required:**
Add sales department selection when dispatching:
```php
// Add property to store selected sales department
public $dispatchSalesDepartmentId = null;

// Update the batch dispatch section in the view
// Add dropdown to select sales department for each batch dispatch
```

#### D. Update Batch Dispatch UI
**File:** `resources/views/livewire/branch-dashboard/production/daily-produce/index.blade.php`

**Location:** Batch Management Section (around line 407-546)

**Add:**
- Dropdown to select sales department when setting `quantity_sent_out`
- Store `sales_department_id` with the dispatch
- Show which department each batch was sent to

**Proposed UI Change:**
```blade
<!-- For each batch: -->
<tr>
    <!-- ... existing columns ... -->

    <!-- NEW: Sales Department Selection -->
    <td class="px-3 py-2">
        <select wire:model="batchSalesDepartment.{{ $batch['id'] }}"
                class="...">
            <option value="">-- Select Sales Dept --</option>
            @foreach($salesDepartments as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </td>

    <!-- Sent Out -->
    <td class="px-3 py-2">
        <input type="number"
               wire:model.blur="batchQuantities.{{ $batch['id'] }}.quantity_sent_out"
               wire:change="updateBatchQuantity({{ $batch['id'] }}, 'quantity_sent_out')">
    </td>
</tr>
```

---

### 4. ProductStock Tracking by Sales Department ⏳

**Investigation Needed:**
- Check `ProductStock` model to see if it already tracks by department
- If not, may need to add `sales_department_id` to `product_stocks` table
- Ensure stock is tracked separately per department

**File to Check:** `app/Models/ProductStock.php`

---

## 📋 Implementation Checklist

### Phase 1: Product-Department Filtering (POS) ✋ HIGH PRIORITY
- [ ] Add `scopeForDepartment()` to Product model
- [ ] Update `getProductsProperty()` in POS to filter by department
- [ ] Test: POS should only show products assigned to that department
- [ ] Verify department_product pivot table has data

### Phase 2: Production Dispatch Department Selection
- [ ] Create migration: add `sales_department_id` to `product_dispatches`
- [ ] Update ProductDispatch model (fillable, relationship)
- [ ] Add `$salesDepartments` property to DailyProduce/Index.php
- [ ] Add `$batchSalesDepartment` array to track department per batch
- [ ] Update batch dispatch UI to include department dropdown
- [ ] Update `updateBatchQuantity()` to save sales_department_id
- [ ] Update `createProductDispatch()` to include sales_department_id
- [ ] Test: Dispatching products should require selecting sales department

### Phase 3: Product Assignment UI (Admin) - OPTIONAL
- [ ] Create page for admins to assign products to departments
- [ ] Allow setting department-specific pricing
- [ ] Allow enabling/disabling products per department

### Phase 4: Testing & Validation
- [ ] Test: Create product and assign to Sales Department A
- [ ] Test: POS for Department A shows the product
- [ ] Test: POS for Department B does NOT show the product
- [ ] Test: Production dispatches product to Department A
- [ ] Test: Department A's stock increases
- [ ] Test: Department B's stock is unaffected

---

## 🔍 Questions to Answer

1. **How are products currently assigned to departments?**
   - Is there existing data in `department_product` table?
   - Is there a UI to manage this, or is it done manually in database?

2. **How does ProductStock work with departments?**
   - Does `product_stocks` table have `sales_department_id`?
   - Or does it rely on `sales_shift_id` → Shift → Department?

3. **What are the sales departments?**
   - List all sales departments (e.g., Main Counter, Gelato Counter, Catering, etc.)
   - Are they branch-specific or global?

4. **Batch dispatching workflow:**
   - Can one batch be split across multiple departments?
   - Or is each batch sent to only ONE department?

---

## 📁 Key Files Reference

### Models
- `app/Models/Product.php` - Product with departments relationship
- `app/Models/Department.php` - Department model
- `app/Models/ProductDispatch.php` - Dispatch tracking
- `app/Models/ProductStock.php` - Stock per department/shift
- `app/Models/DailyProduce.php` - Production tracking

### Controllers/Livewire
- `app/Livewire/BranchDashboard/Production/DailyProduce/Index.php` - Production page
- `app/Livewire/BranchDashboard/SalesDashboard/Pos/Index.php` - POS page

### Migrations
- `database/migrations/2025_11_06_100524_create_department_product_table.php` - Product-Department pivot
- `database/migrations/2025_10_23_120000_create_product_dispatches_table.php` - Dispatch table
- Need: Migration to add `sales_department_id` to `product_dispatches`

### Views
- `resources/views/livewire/branch-dashboard/production/daily-produce/index.blade.php` - Production UI
- `resources/views/livewire/branch-dashboard/sales-dashboard/pos/index.blade.php` - POS UI (need to verify path)

---

## 🚀 Next Steps

**IMMEDIATE ACTION:**
1. ✅ Create this TODO.md ← YOU ARE HERE
2. Read ProductStock model to understand department tracking
3. Check if department_product table has data
4. Implement Phase 1: Filter POS products by department
5. Test POS filtering works correctly
6. Implement Phase 2: Add sales department to production dispatch

---

## 💡 Notes

- Recipe yield system is now working perfectly! ✅
- The pivot table `department_product` already exists with proper structure
- Main work is connecting the existing relationships in the UI/logic
- May need to seed some initial product-department assignments for testing
