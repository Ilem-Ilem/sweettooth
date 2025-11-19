# Database Migrations Structure - Sales & Stock

## SALES TABLES

### 1. SalesShift Model (sales_shifts table)
**Model:** `App\Models\SalesShift`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| branch_id | UUID | NO | NO | | branches(id) | Branch location |
| department_id | BIGINT | NO | NO | | departments(id) | Sales department |
| employee_id | UUID | NO | NO | | employees(id) | Sales person on shift |
| shift_number | VARCHAR | NO | YES | | | Unique shift identifier |
| shift_date | DATE | NO | NO | | | Date of shift |
| shift_type | ENUM | NO | NO | | | morning, afternoon, night |
| clock_in | TIMESTAMP | YES | NO | NULL | | Clock in time |
| clock_out | TIMESTAMP | YES | NO | NULL | | Clock out time |
| opening_cash | DECIMAL(10,2) | NO | NO | 0 | | Starting cash float |
| closing_cash | DECIMAL(10,2) | NO | NO | 0 | | Ending cash amount |
| expected_cash | DECIMAL(10,2) | NO | NO | 0 | | Expected cash (opening + sales) |
| cash_variance | DECIMAL(10,2) | NO | NO | 0 | | Cash difference |
| status | ENUM | NO | NO | active | | active, closed, submitted, verified |
| verified_by | UUID | YES | NO | NULL | employees(id) | Verifying employee |
| notes | TEXT | YES | NO | NULL | | Additional notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Branch, Department, Employee (as employee_id), Employee (as verified_by)
- HasMany: Sales, ProductStocks, ProductCallbacks

---

### 2. Sale Model (sales table)
**Model:** `App\Models\Sale`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| sales_shift_id | BIGINT | YES | NO | NULL | sales_shifts(id) | Associated shift |
| branch_id | UUID | YES | NO | NULL | branches(id) | Sales branch |
| department_id | BIGINT | YES | NO | NULL | departments(id) | Sales department |
| sold_by | UUID | NO | NO | | employees(id) | Selling employee |
| sale_number | VARCHAR | NO | YES | | | Unique receipt/sale number |
| sale_time | TIMESTAMP | NO | NO | | | Transaction timestamp |
| subtotal | DECIMAL(10,2) | NO | NO | 0 | | Pre-tax/discount amount |
| tax | DECIMAL(10,2) | NO | NO | 0 | | Tax amount |
| discount | DECIMAL(10,2) | NO | NO | 0 | | Discount amount |
| total | DECIMAL(10,2) | NO | NO | 0 | | Final sale total |
| status | ENUM | NO | NO | completed | | pending, completed, cancelled, refunded, hold |
| order_type | ENUM | NO | NO | dine_in | | dine-in, takeaway, delivery, dine_in, glovo, transfer |
| notes | TEXT | YES | NO | NULL | | Sale notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Index:** (branch_id, department_id, sale_time)

**Relationships:**
- BelongsTo: SalesShift, Branch, Department, Employee (as sold_by)
- HasMany: SaleItems, Payments

---

### 3. SaleItem Model (sale_items table)
**Model:** `App\Models\SaleItem`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| sale_id | BIGINT | NO | NO | | sales(id) | Associated sale |
| product_id | UUID | NO | NO | | products(id) | Product sold |
| quantity | DECIMAL(10,2) | NO | NO | | | Units sold |
| unit_price | DECIMAL(10,2) | NO | NO | | | Price per unit |
| subtotal | DECIMAL(10,2) | NO | NO | | | qty × unit_price |
| discount | DECIMAL(10,2) | NO | NO | 0 | | Item-level discount |
| total | DECIMAL(10,2) | NO | NO | | | subtotal - discount |
| notes | TEXT | YES | NO | NULL | | Item notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Sale, Product

---

### 4. Payment Model (payments table)
**Model:** `App\Models\Payment`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| sale_id | BIGINT | NO | NO | | sales(id) | Associated sale |
| payment_method | ENUM | NO | NO | cash | | cash, pos, transfer, card, mobile |
| amount | DECIMAL(10,2) | NO | NO | | | Payment amount |
| reference_number | VARCHAR | YES | NO | NULL | | Transaction reference |
| payment_time | TIMESTAMP | NO | NO | | | Payment timestamp |
| status | ENUM | NO | NO | completed | | pending, completed, failed, refunded |
| notes | TEXT | YES | NO | NULL | | Payment notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Sale

---

## STOCK TABLES (SALES AREA)

### 5. ProductStock Model (product_stocks table)
**Model:** `App\Models\ProductStock`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| sales_shift_id | BIGINT | NO | NO | | sales_shifts(id) | Associated shift |
| product_id | UUID | NO | NO | | products(id) | Product |
| stock_date | DATE | NO | NO | | | Stock record date |
| shift_type | ENUM | NO | NO | | | morning, afternoon |
| opening_quantity | DECIMAL(12,2) | NO | NO | 0 | | Stock from previous shift |
| addition_quantity | DECIMAL(12,2) | NO | NO | 0 | | Received from kitchen |
| production_date | DATE | YES | NO | NULL | | When product was made |
| expiry_date | DATE | YES | NO | NULL | | Product expiry |
| callback_quantity | DECIMAL(12,2) | NO | NO | 0 | | Rejected items |
| redress_quantity | DECIMAL(12,2) | NO | NO | 0 | | Items needing adjustment |
| total_available | DECIMAL(12,2) | NO | NO | 0 | | opening + addition - callback - redress |
| transfer_quantity | DECIMAL(12,2) | NO | NO | 0 | | Sent to other departments |
| glovo_quantity | DECIMAL(12,2) | NO | NO | 0 | | Sold via Glovo |
| quantity_sold | DECIMAL(12,2) | NO | NO | 0 | | Regular sales |
| closing_quantity | DECIMAL(12,2) | NO | NO | 0 | | Remaining stock |
| amount | DECIMAL(12,2) | NO | NO | 0 | | Total sales amount |
| notes | TEXT | YES | NO | NULL | | Stock notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Unique Index:** (sales_shift_id, product_id, stock_date, shift_type)

**Relationships:**
- BelongsTo: SalesShift, Product
- HasMany: ProductCallbacks

---

### 6. ProductCallback Model (product_callbacks table)
**Model:** `App\Models\ProductCallback`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| product_stock_id | BIGINT | NO | NO | | product_stocks(id) | Associated stock record |
| product_id | UUID | NO | NO | | products(id) | Product being returned |
| sales_shift_id | BIGINT | NO | NO | | sales_shifts(id) | Associated shift |
| recorded_by | UUID | NO | NO | | employees(id) | Recording employee |
| quantity | DECIMAL(12,2) | NO | NO | | | Returned quantity |
| reason | ENUM | NO | NO | | | expired, damaged, quality_issue, customer_return, other |
| notes | TEXT | YES | NO | NULL | | Callback details |
| callback_time | TIMESTAMP | NO | NO | | | When callback occurred |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Indexes:** 
- (product_id, callback_time)
- (sales_shift_id, callback_time)
- reason

**Relationships:**
- BelongsTo: ProductStock, Product, SalesShift, Employee (as recorded_by)

---

## INVENTORY TABLES

### 7. Item Model (items table)
**Model:** `App\Models\Item`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| branch_id | UUID | NO | NO | | branches(id) | Owning branch |
| name | VARCHAR | NO | NO | | | Item name |
| sku | VARCHAR | NO | YES | | | Stock keeping unit |
| category | ENUM | NO | NO | | | raw_material, packaging, consumable, equipment |
| uom | ENUM | NO | NO | | | grams, kg, liters, ml, pcs, units, bags, cartons |
| description | TEXT | YES | NO | NULL | | Item description |
| reorder_level | DECIMAL(10,2) | YES | NO | NULL | | Min stock threshold |
| max_stock_level | DECIMAL(10,2) | YES | NO | NULL | | Max stock threshold |
| status | ENUM | NO | NO | active | | active, inactive |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Branch
- HasMany: Stocks, StockMovements, RecipeIngredients, PurchaseItems

---

### 8. Stock Model (stocks table)
**Model:** `App\Models\Stock`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| branch_id | UUID | NO | NO | | branches(id) | Branch location |
| item_id | BIGINT | NO | NO | | items(id) | Item in stock |
| quantity_available | DECIMAL(12,2) | NO | NO | 0 | | Available qty |
| quantity_reserved | DECIMAL(12,2) | NO | NO | 0 | | Reserved qty |
| quantity_damaged | DECIMAL(12,2) | NO | NO | 0 | | Damaged qty |
| average_cost | DECIMAL(12,4) | NO | NO | 0 | | Avg cost per unit |
| last_stock_take_date | DATE | YES | NO | NULL | | Last physical count |
| health_status | ENUM | NO | NO | good | | good, warning, critical, expired |
| expiry_date | DATE | YES | NO | NULL | | Item expiry |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Unique Index:** (branch_id, item_id)

**Relationships:**
- BelongsTo: Branch, Item
- HasMany: StockMovements

---

### 9. StockMovement Model (stock_movements table)
**Model:** `App\Models\StockMovement`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| stock_id | BIGINT | NO | NO | | stocks(id) | Associated stock |
| type | ENUM | NO | NO | | | in, out, adjustment, transfer, damaged, return |
| quantity | DECIMAL(12,2) | NO | NO | | | Quantity moved |
| quantity_before | DECIMAL(12,2) | NO | NO | | | Qty before movement |
| quantity_after | DECIMAL(12,2) | NO | NO | | | Qty after movement |
| reference_type | VARCHAR | YES | NO | NULL | | purchase, dispatch, adjustment |
| reference_id | BIGINT | YES | NO | NULL | | Reference record ID |
| moved_by | UUID | YES | NO | NULL | employees(id) | Moving employee |
| notes | TEXT | YES | NO | NULL | | Movement notes |
| movement_date | TIMESTAMP | NO | NO | | | When movement occurred |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Stock, Employee (as moved_by)

---

### 10. Purchase Model (purchases table)
**Model:** `App\Models\Purchase`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| branch_id | UUID | NO | NO | | branches(id) | Purchasing branch |
| recorded_by | UUID | NO | NO | | employees(id) | Recording employee |
| purchase_number | VARCHAR | NO | YES | | | Unique PO number |
| purchase_date | DATE | NO | NO | | | Purchase date |
| supplier_name | VARCHAR | NO | NO | | | Supplier name |
| supplier_contact | VARCHAR | YES | NO | NULL | | Supplier contact |
| total_fob_fc | DECIMAL(12,2) | NO | NO | 0 | | FOB in foreign currency |
| total_fob_ngn | DECIMAL(12,2) | NO | NO | 0 | | FOB in NGN |
| other_costs | DECIMAL(12,2) | NO | NO | 0 | | Additional costs |
| landing_cost | DECIMAL(12,2) | NO | NO | 0 | | Total landing cost |
| total_cost | DECIMAL(12,2) | NO | NO | 0 | | Total purchase cost |
| currency | VARCHAR(3) | NO | NO | NGN | | Currency code |
| exchange_rate | DECIMAL(10,4) | NO | NO | 1 | | Exchange rate |
| payment_status | ENUM | NO | NO | pending | | paid, partial, pending |
| notes | TEXT | YES | NO | NULL | | Purchase notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Branch, Employee (as recorded_by)
- HasMany: PurchaseItems

---

## PRODUCTION TABLES

### 11. Product Model (products table)
**Model:** `App\Models\Product`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | UUID | NO | YES | | | Primary Key |
| name | VARCHAR | NO | NO | | | Product name |
| sku | VARCHAR | NO | YES | | | Stock keeping unit |
| branch_id | UUID | YES | NO | NULL | branches(id) | Branch-specific product |
| product_type_id | BIGINT | NO | NO | | product_types(id) | Product type |
| category_id | BIGINT | YES | NO | NULL | | Category (nullable) |
| description | TEXT | YES | NO | NULL | | Product description |
| price | DECIMAL(10,2) | NO | NO | 0 | | Selling price |
| cost | DECIMAL(10,2) | YES | NO | NULL | | Cost price (margin calc) |
| shelf_life_days | INTEGER | NO | NO | 0 | | Shelf life in days |
| uom | ENUM | NO | NO | pcs | | grams, kg, liters, ml, pcs, units |
| recipe_yield | DECIMAL(10,2) | NO | NO | 1 | | Units per recipe batch |
| recipe_yield_weight | DECIMAL(10,2) | YES | NO | NULL | | Weight/volume per batch (g/ml) |
| unit_weight | DECIMAL(10,2) | YES | NO | NULL | | Weight of one unit (g/ml) |
| yield_percentage | DECIMAL(5,2) | NO | NO | 100 | | Expected yield % (waste account) |
| is_active | BOOLEAN | NO | NO | true | | Active status |
| is_available | BOOLEAN | NO | NO | true | | Available for production/sale |
| image_url | VARCHAR | YES | NO | NULL | | Product image path |
| allergens | JSON | YES | NO | NULL | | List of allergens |
| tags | JSON | YES | NO | NULL | | Product tags for filtering |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |
| deleted_at | TIMESTAMP | YES | NO | NULL | | Soft delete timestamp |

**Indexes:** product_type_id, category_id, is_active, is_available, created_at

**Relationships:**
- BelongsTo: Branch, ProductType
- HasMany: SaleItems, ProductStocks, ProductCallbacks, RecipeIngredients

---

### 12. Recipe Model (recipes table)
**Model:** `App\Models\Recipe`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| branch_id | UUID | NO | NO | | branches(id) | Branch |
| department_id | BIGINT | NO | NO | | departments(id) | Production department |
| product_name | VARCHAR | NO | NO | | | Recipe/product name |
| sku | VARCHAR | NO | YES | | | Unique SKU |
| product_type | ENUM | NO | NO | | | gelato_base, gelato_flavor, pastry, hot_kitchen, beverage |
| cost_per_unit | DECIMAL(10,4) | NO | NO | 0 | | Cost to produce one unit |
| uom | ENUM | NO | NO | pcs | | grams, kg, liters, ml, pcs, units |
| yield_quantity | DECIMAL(10,2) | NO | NO | 1 | | Units this recipe produces |
| preparation_time | INTEGER | YES | NO | NULL | | Time in minutes |
| instructions | TEXT | YES | NO | NULL | | Production instructions |
| status | ENUM | NO | NO | active | | active, inactive, testing |
| created_by | UUID | NO | NO | | employees(id) | Creating employee |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Branch, Department, Employee (as created_by)
- HasMany: RecipeIngredients, DailyProduces, ProductionRequests, RawMaterialUtilizations

---

### 13. RecipeIngredient Model (recipe_ingredients table)
**Model:** `App\Models\RecipeIngredient`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| recipe_id | BIGINT | NO | NO | | recipes(id) | Associated recipe |
| item_id | BIGINT | NO | NO | | items(id) | Ingredient item |
| quantity | DECIMAL(12,4) | NO | NO | | | Qty needed per recipe unit |
| uom | ENUM | NO | NO | | | grams, kg, liters, ml, pcs, units |
| sort_order | INTEGER | NO | NO | 0 | | Display order |
| notes | TEXT | YES | NO | NULL | | Ingredient notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Relationships:**
- BelongsTo: Recipe, Item

---

### 14. DailyProduce Model (daily_produces table)
**Model:** `App\Models\DailyProduce`

| Column | Type | Nullable | Unique | Default | Foreign Key | Description |
|--------|------|----------|--------|---------|-------------|-------------|
| id | BIGINT | NO | YES | | | Primary Key |
| shift_id | BIGINT | NO | NO | | shifts(id) | Associated shift |
| recipe_id | BIGINT | NO | NO | | recipes(id) | Recipe produced |
| produce_date | DATE | NO | NO | | | Production date |
| shift_type | ENUM | NO | NO | | | morning, afternoon |
| opening_quantity | DECIMAL(12,2) | NO | NO | 0 | | From previous shift |
| requested_quantity | DECIMAL(12,2) | NO | NO | 0 | | Requested from inventory |
| produced_quantity | DECIMAL(12,2) | NO | NO | 0 | | Produced this shift |
| sent_out_quantity | DECIMAL(12,2) | NO | NO | 0 | | Sent to sales |
| order_quantity | DECIMAL(12,2) | NO | NO | 0 | | Ordered but not sent |
| callback_quantity | DECIMAL(12,2) | NO | NO | 0 | | Bad/rejected items |
| closing_quantity | DECIMAL(12,2) | NO | NO | 0 | | Remaining at shift end |
| expected_closing | DECIMAL(12,2) | NO | NO | 0 | | System calculated closing |
| variance | DECIMAL(12,2) | NO | NO | 0 | | Difference (closing - expected) |
| notes | TEXT | YES | NO | NULL | | Production notes |
| created_at | TIMESTAMP | NO | NO | | | Record creation |
| updated_at | TIMESTAMP | NO | NO | | | Last update |

**Unique Index:** (shift_id, recipe_id)

**Relationships:**
- BelongsTo: Shift, Recipe
- HasMany: ProductionRecords

---

---

## RELATIONSHIP SUMMARY

### Sales → Stock Flow
```
SalesShift (1) ─────── (N) ProductStock
                ├────── (N) Sale
                └────── (N) ProductCallback

Sale (1) ───── (N) SaleItem
      └────── (N) Payment

SaleItem → Product
ProductCallback → Product
```

### Inventory → Production → Sales Flow
```
Item (1) ─────── (N) Stock
      ├────── (N) StockMovement
      ├────── (N) RecipeIngredient
      └────── (N) PurchaseItem

Stock (1) ──── (N) StockMovement

Purchase (1) ──── (N) PurchaseItem
                  └── (N) Item

Recipe (1) ─────── (N) RecipeIngredient
       ├────────── (N) DailyProduce
       ├────────── (N) ProductionRequest
       └────────── (N) RawMaterialUtilization

DailyProduce (1) ─── (N) ProductionRecord
             └────── (N) ProductionRequest
```

### Key Foreign Keys:
- **SalesShift:** branch_id, department_id, employee_id, verified_by
- **Sale:** sales_shift_id, branch_id, department_id, sold_by
- **SaleItem:** sale_id, product_id
- **Payment:** sale_id
- **ProductStock:** sales_shift_id, product_id
- **ProductCallback:** product_stock_id, product_id, sales_shift_id, recorded_by
- **Item:** branch_id
- **Stock:** branch_id, item_id
- **StockMovement:** stock_id, moved_by
- **Purchase:** branch_id, recorded_by
- **Product:** branch_id, product_type_id
- **Recipe:** branch_id, department_id, created_by
- **RecipeIngredient:** recipe_id, item_id
- **DailyProduce:** shift_id, recipe_id
