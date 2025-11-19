# 📊 SweetTooth Database Schema Documentation

**Generated:** 2025-10-22
**System:** Laravel-based Inventory & Sales Management System
**Database:** MySQL/PostgreSQL

---

## 📑 Table of Contents

1. [Core Tables](#core-tables)
   - [Branches](#1-branches)
   - [Employees](#2-employees)
   - [Departments](#3-departments)
   - [Department Categories](#4-department_categories)
2. [Inventory Management](#inventory-management)
   - [Items](#5-items)
   - [Purchases](#6-purchases)
   - [Purchase Items](#7-purchase_items)
   - [Stocks](#8-stocks)
   - [Stock Movements](#9-stock_movements)
   - [Item Requests](#10-item_requests)
   - [Item Request Details](#11-item_request_details)
   - [Item Dispatches](#12-item_dispatches)
   - [Approved Items](#13-approved_items)
   - [Stock Takes](#14-stock_takes)
   - [Stock Take Details](#15-stock_take_details)
3. [Production Management](#production-management)
   - [Product Types](#16-product_types)
   - [Products](#17-products)
   - [Recipes](#18-recipes)
   - [Recipe Ingredients](#19-recipe_ingredients)
   - [Shifts (Production)](#20-shifts)
   - [Daily Produces](#21-daily_produces)
   - [Production Requests](#22-production_requests)
   - [Raw Material Utilizations](#23-raw_material_utilizations)
4. [Sales Management](#sales-management)
   - [Sales Shifts](#24-sales_shifts)
   - [Product Stocks](#25-product_stocks)
   - [Product Callbacks](#26-product_callbacks)
   - [Sales](#27-sales)
   - [Sale Items](#28-sale_items)
   - [Payments](#29-payments)
5. [Employee Management](#employee-management)
   - [Clock Ins](#30-clock_ins)
   - [Employee Shifts](#31-employee_shifts)
6. [Database Relationships](#database-relationships)

---

## Core Tables

### 1. **branches**

Stores branch/location information for the organization.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique branch identifier |
| `name` | VARCHAR(255) | UNIQUE, NOT NULL | Branch name |
| `code` | VARCHAR(255) | UNIQUE, NOT NULL | Unique branch code |
| `location` | VARCHAR(255) | NOT NULL | Physical address |
| `phone` | VARCHAR(255) | NULLABLE | Contact phone |
| `email` | VARCHAR(255) | UNIQUE, NULLABLE | Contact email |
| `description` | TEXT | NULLABLE | Optional description |
| `manager_user_id` | UUID | FOREIGN KEY → employees(id), NULLABLE | Branch manager |
| `country` | VARCHAR(255) | NULLABLE | Country |
| `state` | VARCHAR(255) | NULLABLE | State or region |
| `city` | VARCHAR(255) | NULLABLE | City |
| `postal_code` | VARCHAR(255) | NULLABLE | Postal/Zip code |
| `timezone` | VARCHAR(255) | NULLABLE | Branch timezone |
| `is_active` | BOOLEAN | DEFAULT TRUE | Active status |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Relationships:**
- Has many: `employees`, `departments`, `items`, `purchases`, `stocks`, `recipes`, `products`, `sales`, `item_requests`, `item_dispatches`
- Belongs to: `employees` (as manager)

---

### 2. **employees**

Stores employee information and profile data.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Unique employee identifier |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NULLABLE | Assigned branch |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NULLABLE | Assigned department |
| `employee_number` | VARCHAR(50) | UNIQUE, NOT NULL | Employee number |
| `name` | VARCHAR(255) | NOT NULL | Full name |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Email address |
| `phone` | VARCHAR(50) | NULLABLE | Phone number |
| `address` | TEXT | NULLABLE | Home address |
| `date_of_birth` | DATE | NULLABLE | Date of birth |
| `gender` | ENUM | NULLABLE | male, female, other, prefer_not_to_say |
| `nationality` | VARCHAR(100) | NULLABLE | Nationality |
| `emergency_contact_name` | VARCHAR(255) | NULLABLE | Emergency contact name |
| `emergency_contact_phone` | VARCHAR(50) | NULLABLE | Emergency contact phone |
| `hire_date` | DATE | NOT NULL | Hiring date |
| `termination_date` | DATE | NULLABLE | Termination date |
| `status` | ENUM | DEFAULT 'active' | active, inactive, terminated, on_probation, on_leave |
| `probation_end_date` | DATE | NULLABLE | End of probation period |
| `shift_preference` | ENUM | NULLABLE | morning, afternoon, night, rotating, flexible |
| `salary` | DECIMAL(10,2) | NULLABLE | Monthly salary |
| `hourly_rate` | DECIMAL(10,2) | NULLABLE | Hourly rate |
| `tax_id` | VARCHAR(50) | NULLABLE | Tax identification number |
| `bank_account` | VARCHAR(100) | NULLABLE | Bank account number |
| `allergies` | TEXT | NULLABLE | Food allergies |
| `profile_photo` | VARCHAR(255) | NULLABLE | Profile photo path |
| `last_performance_review_date` | DATE | NULLABLE | Last performance review |
| `performance_rating` | DECIMAL(3,1) | NULLABLE | Performance rating (0-5) |
| `password` | VARCHAR(255) | NOT NULL | Hashed password |
| `position` | VARCHAR(255) | NULLABLE | Job position |
| `manager_id` | UUID | FOREIGN KEY → employees(id), NULLABLE | Direct manager |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Relationships:**
- Belongs to: `branches`, `departments`, `employees` (as manager)
- Has many: `shifts`, `sales`, `clock_ins`, `item_requests`, `item_dispatches`, `purchases`, `stock_takes`, `daily_produces`

---

### 3. **departments**

Stores department information within branches.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Department ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NULLABLE | Associated branch |
| `category_id` | UUID | FOREIGN KEY → department_categories(id), NOT NULL | Department category |
| `name` | VARCHAR(255) | UNIQUE, NOT NULL | Department name |
| `description` | TEXT | NULLABLE | Department description |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`, `department_categories`
- Has many: `employees`, `product_types`, `recipes`, `item_requests`, `shifts`, `sales`

---

### 4. **department_categories**

Categorizes departments (e.g., Kitchen, Sales, Admin).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Category ID |
| `name` | VARCHAR(255) | UNIQUE, NOT NULL | Category name |
| `description` | TEXT | NOT NULL | Category description |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Has many: `departments`

---

## Inventory Management

### 5. **items**

Stores raw materials, packaging, and consumables.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Item ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch owning the item |
| `name` | VARCHAR(255) | NOT NULL | Item name |
| `sku` | VARCHAR(255) | UNIQUE, NOT NULL | Stock keeping unit |
| `category` | ENUM | NOT NULL | raw_material, packaging, consumable, equipment |
| `uom` | ENUM | NOT NULL | grams, kg, liters, ml, pcs, units, bags, cartons |
| `description` | TEXT | NULLABLE | Item description |
| `reorder_level` | DECIMAL(10,2) | NULLABLE | Minimum stock level |
| `max_stock_level` | DECIMAL(10,2) | NULLABLE | Maximum stock level |
| `status` | ENUM | DEFAULT 'active' | active, inactive |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`
- Has many: `purchase_items`, `stocks`, `item_request_details`, `item_dispatches`, `recipe_ingredients`, `raw_material_utilizations`

---

### 6. **purchases**

Records purchase orders from suppliers.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Purchase ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Purchasing branch |
| `recorded_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Employee who recorded |
| `purchase_number` | VARCHAR(255) | UNIQUE, NOT NULL | Purchase order number |
| `purchase_date` | DATE | NOT NULL | Date of purchase |
| `supplier_name` | VARCHAR(255) | NOT NULL | Supplier name |
| `supplier_contact` | VARCHAR(255) | NULLABLE | Supplier contact info |
| `total_fob_fc` | DECIMAL(12,2) | DEFAULT 0 | Total FOB foreign currency |
| `total_fob_ngn` | DECIMAL(12,2) | DEFAULT 0 | Total FOB in NGN |
| `other_costs` | DECIMAL(12,2) | DEFAULT 0 | Additional costs |
| `landing_cost` | DECIMAL(12,2) | DEFAULT 0 | Total landing cost |
| `total_cost` | DECIMAL(12,2) | DEFAULT 0 | Total purchase cost |
| `currency` | VARCHAR(3) | DEFAULT 'NGN' | Currency code |
| `exchange_rate` | DECIMAL(10,4) | DEFAULT 1 | Exchange rate |
| `payment_status` | ENUM | DEFAULT 'pending' | paid, partial, pending |
| `notes` | TEXT | NULLABLE | Additional notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`, `employees` (as recorded_by)
- Has many: `purchase_items`

---

### 7. **purchase_items**

Individual items within a purchase order.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Purchase item ID |
| `purchase_id` | BIGINT | FOREIGN KEY → purchases(id), NOT NULL | Associated purchase |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Purchased item |
| `quantity` | DECIMAL(12,2) | NOT NULL | Quantity purchased |
| `uom` | ENUM | NOT NULL | grams, kg, liters, ml, pcs, units, bags, cartons |
| `fob_fc` | DECIMAL(12,2) | DEFAULT 0 | FOB foreign currency |
| `fob_ngn` | DECIMAL(12,2) | DEFAULT 0 | FOB in NGN |
| `other_costs` | DECIMAL(12,2) | DEFAULT 0 | Additional costs per item |
| `landing_cost` | DECIMAL(12,2) | DEFAULT 0 | Landing cost |
| `total_cost` | DECIMAL(12,2) | NOT NULL | Total cost for this item |
| `cost_per_unit` | DECIMAL(12,4) | NOT NULL | Cost per unit |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `purchases`, `items`

---

### 8. **stocks**

Current stock levels per branch per item.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Stock ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Item |
| `quantity_available` | DECIMAL(12,2) | DEFAULT 0 | Available quantity |
| `quantity_reserved` | DECIMAL(12,2) | DEFAULT 0 | Reserved quantity |
| `quantity_damaged` | DECIMAL(12,2) | DEFAULT 0 | Damaged quantity |
| `average_cost` | DECIMAL(12,4) | DEFAULT 0 | Average cost per unit |
| `last_stock_take_date` | DATE | NULLABLE | Last stock take date |
| `health_status` | ENUM | DEFAULT 'good' | good, warning, critical, expired |
| `expiry_date` | DATE | NULLABLE | Expiry date |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- UNIQUE: `(branch_id, item_id)`

**Relationships:**
- Belongs to: `branches`, `items`
- Has many: `stock_movements`

---

### 9. **stock_movements**

Tracks all stock movements (in, out, adjustments).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Movement ID |
| `stock_id` | BIGINT | FOREIGN KEY → stocks(id), NOT NULL | Stock record |
| `type` | ENUM | NOT NULL | in, out, adjustment, transfer, damaged, return |
| `quantity` | DECIMAL(12,2) | NOT NULL | Quantity moved |
| `quantity_before` | DECIMAL(12,2) | NOT NULL | Quantity before movement |
| `quantity_after` | DECIMAL(12,2) | NOT NULL | Quantity after movement |
| `reference_type` | VARCHAR(255) | NULLABLE | purchase, dispatch, adjustment |
| `reference_id` | BIGINT | NULLABLE | ID of reference record |
| `moved_by` | UUID | FOREIGN KEY → employees(id), NULLABLE | Employee who moved |
| `notes` | TEXT | NULLABLE | Movement notes |
| `movement_date` | TIMESTAMP | NOT NULL | Date/time of movement |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `stocks`, `employees` (as moved_by)

---

### 10. **item_requests**

Requests for items from departments to inventory.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Request ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Requesting branch |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NOT NULL | Requesting department |
| `requested_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Requesting employee |
| `request_number` | VARCHAR(255) | UNIQUE, NOT NULL | Request number |
| `request_date` | DATE | NOT NULL | Date of request |
| `shift` | ENUM | NULLABLE | morning, afternoon |
| `status` | ENUM | DEFAULT 'pending' | pending, approved, partially_dispatched, completed, cancelled |
| `approved_by` | UUID | FOREIGN KEY → employees(id), NULLABLE | Approving employee |
| `approved_at` | TIMESTAMP | NULLABLE | Approval timestamp |
| `notes` | TEXT | NULLABLE | Request notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`, `departments`, `employees` (as requested_by, approved_by)
- Has many: `item_request_details`, `item_dispatches`, `approved_items`, `production_requests`

---

### 11. **item_request_details**

Line items for item requests.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Detail ID |
| `request_id` | BIGINT | FOREIGN KEY → item_requests(id), NOT NULL | Associated request |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Requested item |
| `quantity_requested` | DECIMAL(12,2) | NOT NULL | Requested quantity |
| `quantity_approved` | DECIMAL(12,2) | DEFAULT 0 | Approved quantity |
| `quantity_dispatched` | DECIMAL(12,2) | DEFAULT 0 | Dispatched quantity |
| `uom` | ENUM | NOT NULL | grams, kg, liters, ml, pcs, units, bags, cartons |
| `notes` | TEXT | NULLABLE | Item notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `item_requests`, `items`

---

### 12. **item_dispatches**

Records of item dispatches to departments.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Dispatch ID |
| `request_id` | BIGINT | FOREIGN KEY → item_requests(id), NOT NULL | Associated request |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Dispatched item |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `dispatched_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Dispatching employee |
| `received_by` | UUID | FOREIGN KEY → employees(id), NULLABLE | Receiving employee |
| `quantity` | DECIMAL(12,2) | NOT NULL | Dispatched quantity |
| `uom` | ENUM | NOT NULL | grams, kg, liters, ml, pcs, units, bags, cartons |
| `dispatch_time` | TIMESTAMP | NOT NULL | Dispatch timestamp |
| `received_time` | TIMESTAMP | NULLABLE | Receipt timestamp |
| `shift` | ENUM | NULLABLE | morning, afternoon, night |
| `notes` | TEXT | NULLABLE | Dispatch notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `item_requests`, `items`, `branches`, `employees` (as dispatched_by, received_by)

---

### 13. **approved_items**

Items that have been approved for dispatch.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Approved item ID |
| `request_id` | BIGINT | FOREIGN KEY → item_requests(id), NOT NULL | Associated request |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Approved item |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `approved_by` | UUID | NOT NULL | Approving employee |
| `quantity` | DECIMAL(12,2) | NOT NULL | Approved quantity |
| `uom` | ENUM | NOT NULL | grams, kg, liters, ml, pcs, units, bags, cartons |
| `approved_time` | TIMESTAMP | NOT NULL | Approval timestamp |
| `status` | ENUM | DEFAULT 'pending' | pending, dispatched |
| `shift` | ENUM | NULLABLE | morning, afternoon, night |
| `notes` | TEXT | NULLABLE | Approval notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `item_requests`, `items`, `branches`

---

### 14. **stock_takes**

Physical stock verification records.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Stock take ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `stock_take_number` | VARCHAR(255) | UNIQUE, NOT NULL | Stock take number |
| `stock_take_date` | DATE | NOT NULL | Date of stock take |
| `type` | ENUM | NOT NULL | daily, weekly, monthly, annual, ad_hoc |
| `conducted_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Conducting employee |
| `status` | ENUM | DEFAULT 'in_progress' | in_progress, completed, verified |
| `verified_by` | UUID | FOREIGN KEY → employees(id), NULLABLE | Verifying employee |
| `verified_at` | TIMESTAMP | NULLABLE | Verification timestamp |
| `notes` | TEXT | NULLABLE | Stock take notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`, `employees` (as conducted_by, verified_by)
- Has many: `stock_take_details`

---

### 15. **stock_take_details**

Individual item counts during stock take.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Detail ID |
| `stock_take_id` | BIGINT | FOREIGN KEY → stock_takes(id), NOT NULL | Associated stock take |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Item counted |
| `system_quantity` | DECIMAL(12,2) | NOT NULL | System recorded quantity |
| `physical_quantity` | DECIMAL(12,2) | NOT NULL | Physically counted quantity |
| `variance` | DECIMAL(12,2) | NOT NULL | Difference (physical - system) |
| `variance_type` | ENUM | DEFAULT 'match' | surplus, shortage, match |
| `notes` | TEXT | NULLABLE | Variance notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `stock_takes`, `items`

---

## Production Management

### 16. **product_types**

Categories for products (e.g., Gelato Base, Pastry).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Product type ID |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NOT NULL | Associated department |
| `name` | VARCHAR(255) | UNIQUE, NOT NULL | Product type name |
| `code` | VARCHAR(50) | UNIQUE, NOT NULL | Short code (GB, GF, PT) |
| `description` | TEXT | NULLABLE | Description |
| `status` | ENUM | DEFAULT 'active' | active, inactive |
| `sort_order` | INTEGER | DEFAULT 0 | Display order |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Indexes:**
- `department_id`, `status`, `sort_order`

**Relationships:**
- Belongs to: `departments`
- Has many: `products`

---

### 17. **products**

Final products for sale.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | UUID | PRIMARY KEY | Product ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NULLABLE | Branch-specific product |
| `product_type_id` | BIGINT | FOREIGN KEY → product_types(id), NOT NULL | Product type |
| `category_id` | BIGINT | NULLABLE | Additional category |
| `name` | VARCHAR(255) | NOT NULL | Product name |
| `sku` | VARCHAR(255) | UNIQUE, NOT NULL | Stock keeping unit |
| `description` | TEXT | NULLABLE | Product description |
| `price` | DECIMAL(10,2) | DEFAULT 0 | Selling price |
| `cost` | DECIMAL(10,2) | NULLABLE | Cost price for margin calculation |
| `shelf_life_days` | INTEGER | DEFAULT 0 | Shelf life in days |
| `uom` | ENUM | DEFAULT 'pcs' | grams, kg, liters, ml, pcs, units |
| `recipe_yield` | DECIMAL(10,2) | DEFAULT 1 | Units per recipe batch |
| `recipe_yield_weight` | DECIMAL(10,2) | NULLABLE | Total weight/volume per batch (grams/ml) |
| `unit_weight` | DECIMAL(10,2) | NULLABLE | Weight of one unit (grams/ml) |
| `yield_percentage` | DECIMAL(5,2) | DEFAULT 100 | Expected yield % (accounts for waste) |
| `is_active` | BOOLEAN | DEFAULT TRUE | Active status |
| `is_available` | BOOLEAN | DEFAULT TRUE | Available for production/sale |
| `image_url` | VARCHAR(255) | NULLABLE | Product image path |
| `allergens` | JSON | NULLABLE | List of allergens |
| `tags` | JSON | NULLABLE | Product tags for filtering |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete timestamp |

**Indexes:**
- `product_type_id`, `category_id`, `is_active`, `is_available`, `created_at`

**Relationships:**
- Belongs to: `branches`, `product_types`
- Has many: `recipes`, `product_stocks`, `sale_items`, `product_callbacks`

---

### 18. **recipes**

Production recipes for products.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Recipe ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NOT NULL | Department |
| `product_name` | VARCHAR(255) | NOT NULL | Product name |
| `sku` | VARCHAR(255) | UNIQUE, NOT NULL | Stock keeping unit |
| `product_type` | ENUM | NOT NULL | gelato_base, gelato_flavor, pastry, hot_kitchen, beverage |
| `cost_per_unit` | DECIMAL(10,4) | DEFAULT 0 | Cost per unit produced |
| `uom` | ENUM | DEFAULT 'pcs' | grams, kg, liters, ml, pcs, units |
| `yield_quantity` | DECIMAL(10,2) | DEFAULT 1 | Units produced per batch |
| `preparation_time` | INTEGER | NULLABLE | Preparation time in minutes |
| `instructions` | TEXT | NULLABLE | Cooking/preparation instructions |
| `status` | ENUM | DEFAULT 'active' | active, inactive, testing |
| `created_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Creator employee |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`, `departments`, `employees` (as created_by)
- Has many: `recipe_ingredients`, `daily_produces`, `raw_material_utilizations`, `production_requests`

---

### 19. **recipe_ingredients**

Ingredients required for each recipe.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Ingredient ID |
| `recipe_id` | BIGINT | FOREIGN KEY → recipes(id), NOT NULL | Associated recipe |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Ingredient item |
| `quantity` | DECIMAL(12,4) | NOT NULL | Quantity needed per recipe batch |
| `uom` | ENUM | NOT NULL | grams, kg, liters, ml, pcs, units |
| `sort_order` | INTEGER | DEFAULT 0 | Display order in recipe |
| `notes` | TEXT | NULLABLE | Ingredient notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `recipes`, `items`

---

### 20. **shifts**

Production shifts for kitchen/production departments.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Shift ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NOT NULL | Department |
| `employee_id` | UUID | FOREIGN KEY → employees(id), NOT NULL | Employee on shift |
| `shift_number` | VARCHAR(255) | UNIQUE, NOT NULL | Unique shift number |
| `shift_date` | DATE | NOT NULL | Shift date |
| `shift_type` | ENUM | NOT NULL | morning, afternoon, night |
| `clock_in` | TIMESTAMP | NULLABLE | Clock in time |
| `clock_out` | TIMESTAMP | NULLABLE | Clock out time |
| `status` | ENUM | DEFAULT 'active' | active, closed, submitted |
| `notes` | TEXT | NULLABLE | Shift notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- `(branch_id, department_id, shift_date)`

**Relationships:**
- Belongs to: `branches`, `departments`, `employees`
- Has many: `daily_produces`, `production_requests`, `raw_material_utilizations`

---

### 21. **daily_produces**

Daily production records per shift.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Produce ID |
| `shift_id` | BIGINT | FOREIGN KEY → shifts(id), NOT NULL | Associated shift |
| `recipe_id` | BIGINT | FOREIGN KEY → recipes(id), NOT NULL | Recipe produced |
| `produce_date` | DATE | NOT NULL | Production date |
| `shift_type` | ENUM | NOT NULL | morning, afternoon |
| `opening_quantity` | DECIMAL(12,2) | DEFAULT 0 | Quantity from previous shift |
| `requested_quantity` | DECIMAL(12,2) | DEFAULT 0 | Items requested from inventory |
| `produced_quantity` | DECIMAL(12,2) | DEFAULT 0 | Quantity produced this shift |
| `sent_out_quantity` | DECIMAL(12,2) | DEFAULT 0 | Sent to sales departments |
| `order_quantity` | DECIMAL(12,2) | DEFAULT 0 | Ordered but not sent |
| `callback_quantity` | DECIMAL(12,2) | DEFAULT 0 | Bad/rejected items |
| `closing_quantity` | DECIMAL(12,2) | DEFAULT 0 | Remaining at shift end |
| `expected_closing` | DECIMAL(12,2) | DEFAULT 0 | System calculated closing |
| `variance` | DECIMAL(12,2) | DEFAULT 0 | Difference (closing - expected) |
| `status` | ENUM | NULLABLE | Status field (added later) |
| `notes` | TEXT | NULLABLE | Production notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- UNIQUE: `(shift_id, recipe_id)`

**Relationships:**
- Belongs to: `shifts`, `recipes`

---

### 22. **production_requests**

Production planning requests.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Request ID |
| `shift_id` | BIGINT | FOREIGN KEY → shifts(id), NOT NULL | Associated shift |
| `item_request_id` | BIGINT | FOREIGN KEY → item_requests(id), NOT NULL | Item request for raw materials |
| `recipe_id` | BIGINT | FOREIGN KEY → recipes(id), NULLABLE | Recipe to produce |
| `planned_production_quantity` | DECIMAL(12,2) | NULLABLE | Planned quantity |
| `notes` | TEXT | NULLABLE | Request notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `shifts`, `item_requests`, `recipes`

---

### 23. **raw_material_utilizations**

Tracks raw material usage efficiency.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Utilization ID |
| `shift_id` | BIGINT | FOREIGN KEY → shifts(id), NOT NULL | Production shift |
| `recipe_id` | BIGINT | FOREIGN KEY → recipes(id), NOT NULL | Recipe produced |
| `item_id` | BIGINT | FOREIGN KEY → items(id), NOT NULL | Raw material item |
| `quantity_required` | DECIMAL(12,4) | NOT NULL | Required quantity per recipe unit |
| `quantity_used` | DECIMAL(12,4) | NOT NULL | Actual quantity used |
| `units_produced` | DECIMAL(12,2) | NOT NULL | Number of recipe units produced |
| `variance` | DECIMAL(12,4) | DEFAULT 0 | Difference (used - required) |
| `variance_type` | ENUM | DEFAULT 'within_tolerance' | within_tolerance, over_used, under_used |
| `cost_impact` | DECIMAL(10,2) | DEFAULT 0 | Cost impact of variance |
| `notes` | TEXT | NULLABLE | Utilization notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- `(shift_id, recipe_id)`

**Relationships:**
- Belongs to: `shifts`, `recipes`, `items`

---

## Sales Management

### 24. **sales_shifts**

Sales department shifts (different from production shifts).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Sales shift ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NOT NULL | Department |
| `employee_id` | UUID | FOREIGN KEY → employees(id), NOT NULL | Employee on shift |
| `shift_number` | VARCHAR(255) | UNIQUE, NOT NULL | Unique shift number |
| `shift_date` | DATE | NOT NULL | Shift date |
| `shift_type` | ENUM | NOT NULL | morning, afternoon, night |
| `clock_in` | TIMESTAMP | NULLABLE | Clock in time |
| `clock_out` | TIMESTAMP | NULLABLE | Clock out time |
| `opening_cash` | DECIMAL(10,2) | DEFAULT 0 | Opening cash float |
| `closing_cash` | DECIMAL(10,2) | DEFAULT 0 | Closing cash count |
| `expected_cash` | DECIMAL(10,2) | DEFAULT 0 | Expected cash (opening + sales) |
| `cash_variance` | DECIMAL(10,2) | DEFAULT 0 | Cash difference |
| `status` | ENUM | DEFAULT 'active' | active, closed, submitted, verified |
| `verified_by` | UUID | FOREIGN KEY → employees(id), NULLABLE | Verifying employee |
| `notes` | TEXT | NULLABLE | Shift notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`, `departments`, `employees` (as employee, verified_by)
- Has many: `sales`, `product_stocks`, `product_callbacks`

---

### 25. **product_stocks**

Product stock levels in sales areas per shift.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Product stock ID |
| `sales_shift_id` | BIGINT | FOREIGN KEY → sales_shifts(id), NOT NULL | Sales shift |
| `product_id` | UUID | FOREIGN KEY → products(id), NOT NULL | Product |
| `stock_date` | DATE | NOT NULL | Stock date |
| `shift_type` | ENUM | NOT NULL | morning, afternoon |
| `opening_quantity` | DECIMAL(12,2) | DEFAULT 0 | Opening stock |
| `addition_quantity` | DECIMAL(12,2) | DEFAULT 0 | Received from kitchen |
| `production_date` | DATE | NULLABLE | Date product was produced |
| `expiry_date` | DATE | NULLABLE | Expiry date |
| `callback_quantity` | DECIMAL(12,2) | DEFAULT 0 | Bad/rejected items |
| `redress_quantity` | DECIMAL(12,2) | DEFAULT 0 | Items needing adjustment |
| `total_available` | DECIMAL(12,2) | DEFAULT 0 | Total available (opening + addition - callback - redress) |
| `transfer_quantity` | DECIMAL(12,2) | DEFAULT 0 | Transferred to other departments |
| `glovo_quantity` | DECIMAL(12,2) | DEFAULT 0 | Sold through Glovo |
| `quantity_sold` | DECIMAL(12,2) | DEFAULT 0 | Regular sales |
| `closing_quantity` | DECIMAL(12,2) | DEFAULT 0 | Remaining stock (total_available - transfer - glovo - sold) |
| `amount` | DECIMAL(12,2) | DEFAULT 0 | Total sales amount |
| `notes` | TEXT | NULLABLE | Stock notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- UNIQUE: `(sales_shift_id, product_id, stock_date, shift_type)`

**Relationships:**
- Belongs to: `sales_shifts`, `products`
- Has many: `product_callbacks`

---

### 26. **product_callbacks**

Callbacks/rejections of products in sales.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Callback ID |
| `product_stock_id` | BIGINT | FOREIGN KEY → product_stocks(id), NOT NULL | Product stock |
| `product_id` | UUID | FOREIGN KEY → products(id), NOT NULL | Product |
| `sales_shift_id` | BIGINT | FOREIGN KEY → sales_shifts(id), NOT NULL | Sales shift |
| `recorded_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Recording employee |
| `quantity` | DECIMAL(12,2) | NOT NULL | Callback quantity |
| `reason` | ENUM | NOT NULL | expired, damaged, quality_issue, customer_return, other |
| `notes` | TEXT | NULLABLE | Callback notes |
| `callback_time` | TIMESTAMP | NOT NULL | Callback timestamp |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- `(product_id, callback_time)`
- `(sales_shift_id, callback_time)`
- `reason`

**Relationships:**
- Belongs to: `product_stocks`, `products`, `sales_shifts`, `employees` (as recorded_by)

---

### 27. **sales**

Individual sales transactions.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Sale ID |
| `sales_shift_id` | BIGINT | FOREIGN KEY → sales_shifts(id), NOT NULL | Sales shift |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `department_id` | BIGINT | FOREIGN KEY → departments(id), NOT NULL | Department |
| `sold_by` | UUID | FOREIGN KEY → employees(id), NOT NULL | Selling employee |
| `sale_number` | VARCHAR(255) | UNIQUE, NOT NULL | Sale number/receipt number |
| `sale_time` | TIMESTAMP | NOT NULL | Time of sale |
| `subtotal` | DECIMAL(10,2) | DEFAULT 0 | Subtotal before tax/discount |
| `tax` | DECIMAL(10,2) | DEFAULT 0 | Tax amount |
| `discount` | DECIMAL(10,2) | DEFAULT 0 | Discount amount |
| `total` | DECIMAL(10,2) | DEFAULT 0 | Total sale amount |
| `status` | ENUM | DEFAULT 'completed' | pending, completed, cancelled, refunded |
| `order_type` | ENUM | DEFAULT 'dine_in' | dine_in, takeaway, glovo, transfer |
| `notes` | TEXT | NULLABLE | Sale notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Indexes:**
- `(branch_id, department_id, sale_time)`

**Relationships:**
- Belongs to: `sales_shifts`, `branches`, `departments`, `employees` (as sold_by)
- Has many: `sale_items`, `payments`

---

### 28. **sale_items**

Line items for each sale.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Sale item ID |
| `sale_id` | BIGINT | FOREIGN KEY → sales(id), NOT NULL | Associated sale |
| `product_id` | UUID | FOREIGN KEY → products(id), NOT NULL | Product sold |
| `quantity` | DECIMAL(10,2) | NOT NULL | Quantity sold |
| `unit_price` | DECIMAL(10,2) | NOT NULL | Price per unit |
| `subtotal` | DECIMAL(10,2) | NOT NULL | Subtotal (qty × unit_price) |
| `discount` | DECIMAL(10,2) | DEFAULT 0 | Item discount |
| `total` | DECIMAL(10,2) | NOT NULL | Total (subtotal - discount) |
| `notes` | TEXT | NULLABLE | Item notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `sales`, `products`

---

### 29. **payments**

Payment records for sales (supports split payments).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Payment ID |
| `sale_id` | BIGINT | FOREIGN KEY → sales(id), NOT NULL | Associated sale |
| `payment_method` | ENUM | DEFAULT 'cash' | cash, pos, transfer, card, mobile |
| `amount` | DECIMAL(10,2) | NOT NULL | Payment amount |
| `reference_number` | VARCHAR(255) | NULLABLE | Reference/transaction number |
| `payment_time` | TIMESTAMP | NOT NULL | Payment timestamp |
| `status` | ENUM | DEFAULT 'completed' | pending, completed, failed, refunded |
| `notes` | TEXT | NULLABLE | Payment notes |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `sales`

---

## Employee Management

### 30. **clock_ins**

Employee clock in/out records (legacy/alternative system).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Clock in ID |
| `employee_id` | UUID | FOREIGN KEY → employees(id), NOT NULL | Employee |
| `date` | DATETIMETZ | NOT NULL | Date |
| `shift` | VARCHAR(255) | NOT NULL | Shift name |
| `clock_in_time` | DATETIMETZ | NOT NULL | Clock in time |
| `clock-out-time` | DATETIMETZ | NOT NULL | Clock out time |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `employees`

---

### 31. **employee_shifts**

Employee shift definitions (legacy/alternative system).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| `id` | BIGINT | PRIMARY KEY, AUTO_INCREMENT | Employee shift ID |
| `branch_id` | UUID | FOREIGN KEY → branches(id), NOT NULL | Branch |
| `shift_name` | VARCHAR(255) | NOT NULL | Shift name |
| `start_time` | DATETIME | NOT NULL | Shift start time |
| `end_time` | DATETIME | NOT NULL | Shift end time |
| `created_at` | TIMESTAMP | | Creation timestamp |
| `updated_at` | TIMESTAMP | | Last update timestamp |

**Relationships:**
- Belongs to: `branches`

---

## Database Relationships

### Entity Relationship Overview

```
BRANCHES (1) ──────┬─────── (N) EMPLOYEES
                   ├─────── (N) DEPARTMENTS
                   ├─────── (N) ITEMS
                   ├─────── (N) PURCHASES
                   ├─────── (N) STOCKS
                   ├─────── (N) RECIPES
                   ├─────── (N) PRODUCTS
                   ├─────── (N) SALES
                   └─────── (N) ITEM_REQUESTS

DEPARTMENT_CATEGORIES (1) ──── (N) DEPARTMENTS

DEPARTMENTS (1) ────────┬───── (N) EMPLOYEES
                        ├───── (N) PRODUCT_TYPES
                        ├───── (N) RECIPES
                        ├───── (N) ITEM_REQUESTS
                        ├───── (N) SHIFTS
                        └───── (N) SALES

EMPLOYEES (1) ──────────┬───── (N) SHIFTS
                        ├───── (N) SALES_SHIFTS
                        ├───── (N) SALES (as sold_by)
                        ├───── (N) PURCHASES (as recorded_by)
                        ├───── (N) ITEM_REQUESTS (as requested_by)
                        ├───── (N) ITEM_DISPATCHES (as dispatched_by, received_by)
                        ├───── (N) STOCK_TAKES (as conducted_by, verified_by)
                        ├───── (N) RECIPES (as created_by)
                        ├───── (N) PRODUCT_CALLBACKS (as recorded_by)
                        └───── (N) CLOCK_INS

ITEMS (1) ──────────────┬───── (N) PURCHASE_ITEMS
                        ├───── (N) STOCKS
                        ├───── (N) ITEM_REQUEST_DETAILS
                        ├───── (N) ITEM_DISPATCHES
                        ├───── (N) RECIPE_INGREDIENTS
                        ├───── (N) RAW_MATERIAL_UTILIZATIONS
                        └───── (N) STOCK_TAKE_DETAILS

PURCHASES (1) ────────── (N) PURCHASE_ITEMS

STOCKS (1) ───────────── (N) STOCK_MOVEMENTS

ITEM_REQUESTS (1) ──┬─── (N) ITEM_REQUEST_DETAILS
                    ├─── (N) ITEM_DISPATCHES
                    ├─── (N) APPROVED_ITEMS
                    └─── (N) PRODUCTION_REQUESTS

PRODUCT_TYPES (1) ───── (N) PRODUCTS

PRODUCTS (1) ───────┬─── (N) RECIPES
                    ├─── (N) PRODUCT_STOCKS
                    ├─── (N) SALE_ITEMS
                    └─── (N) PRODUCT_CALLBACKS

RECIPES (1) ────────┬─── (N) RECIPE_INGREDIENTS
                    ├─── (N) DAILY_PRODUCES
                    ├─── (N) RAW_MATERIAL_UTILIZATIONS
                    └─── (N) PRODUCTION_REQUESTS

SHIFTS (1) ─────────┬─── (N) DAILY_PRODUCES
                    ├─── (N) PRODUCTION_REQUESTS
                    └─── (N) RAW_MATERIAL_UTILIZATIONS

SALES_SHIFTS (1) ──┬─── (N) SALES
                   ├─── (N) PRODUCT_STOCKS
                   └─── (N) PRODUCT_CALLBACKS

PRODUCT_STOCKS (1) ─── (N) PRODUCT_CALLBACKS

SALES (1) ──────────┬─── (N) SALE_ITEMS
                    └─── (N) PAYMENTS

STOCK_TAKES (1) ────── (N) STOCK_TAKE_DETAILS
```

---

## Key Business Flows

### 1. **Inventory Purchase Flow**
```
PURCHASES → PURCHASE_ITEMS → STOCKS → STOCK_MOVEMENTS
```

### 2. **Production Request Flow**
```
ITEM_REQUESTS → APPROVED_ITEMS → ITEM_DISPATCHES → PRODUCTION_REQUESTS → DAILY_PRODUCES
```

### 3. **Production to Sales Flow**
```
DAILY_PRODUCES → PRODUCT_STOCKS → SALES → SALE_ITEMS → PAYMENTS
```

### 4. **Raw Material Tracking**
```
RECIPES → RECIPE_INGREDIENTS → RAW_MATERIAL_UTILIZATIONS
```

### 5. **Product Callback Flow**
```
PRODUCT_STOCKS → PRODUCT_CALLBACKS
```

### 6. **Stock Verification Flow**
```
STOCKS → STOCK_TAKES → STOCK_TAKE_DETAILS
```

---

## Notes

### UUID vs BIGINT
- **UUID**: Used for `branches`, `employees`, `products`, `department_categories` (entities that may need global uniqueness)
- **BIGINT**: Used for most other tables (sequential IDs)

### Soft Deletes
Tables with soft delete capability (`deleted_at`):
- `branches`
- `employees`
- `products`
- `product_types`

### Enumerations (ENUM) Used

**Shift Types:**
- `morning`, `afternoon`, `night`

**Status Fields:**
- Employee: `active`, `inactive`, `terminated`, `on_probation`, `on_leave`
- Item Request: `pending`, `approved`, `partially_dispatched`, `completed`, `cancelled`
- Shift: `active`, `closed`, `submitted`
- Sales Shift: `active`, `closed`, `submitted`, `verified`
- Sale: `pending`, `completed`, `cancelled`, `refunded`
- Payment: `pending`, `completed`, `failed`, `refunded`

**Payment Methods:**
- `cash`, `pos`, `transfer`, `card`, `mobile`

**Order Types:**
- `dine_in`, `takeaway`, `glovo`, `transfer`

**Callback Reasons:**
- `expired`, `damaged`, `quality_issue`, `customer_return`, `other`

**Health Status:**
- `good`, `warning`, `critical`, `expired`

**Variance Types:**
- Stock Take: `surplus`, `shortage`, `match`
- Raw Material: `within_tolerance`, `over_used`, `under_used`

---

**Document End** | Last Updated: 2025-10-22
