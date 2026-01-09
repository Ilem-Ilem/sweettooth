# 01 - Database Design & Migrations

## 📊 Table Overview

The advanced RBAC system extends the existing Spatie tables with domain-specific context tables.

### Existing Spatie Tables (Unchanged)
- `roles` - Role definitions
- `permissions` - Permission definitions
- `model_has_roles` - User role assignments
- `model_has_permissions` - Direct permission assignments
- `role_has_permissions` - Role permission mappings

### New Domain Tables
- `categories` - Business module categories
- `departments` - Operational departments within categories
- `role_department_mappings` - Which roles are valid in which departments
- `user_department_roles` - User assignments with department context

## 🗂️ Existing Table Schemas (Leveraged)

### 1. `department_categories` Table (Already Exists)

Top-level business modules that group related functionality. This table already exists in SweetTooth.

```sql
-- Existing table structure
CREATE TABLE department_categories (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Existing Sample Data:**
```sql
INSERT INTO department_categories (id, name, description) VALUES
(UUID(), 'Sales', 'Departments focused on selling products and services, customer acquisition, and revenue generation.'),
(UUID(), 'Production', 'Departments responsible for manufacturing, production processes, and quality control.'),
(UUID(), 'Support', 'Departments providing assistance, customer service, and technical support services.');
```

### 2. `departments` Table (Already Exists - Minor Extensions Needed)

Operational units within categories, scoped to specific branches. This table already exists but needs extensions for advanced RBAC.

```sql
-- Existing table structure (simplified)
CREATE TABLE departments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT, -- Note: Uses BIGINT, not UUID
    branch_id CHAR(36) NULL,
    category_id CHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    enable_table_management BOOLEAN DEFAULT FALSE,
    table_management_settings JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES department_categories(id) ON DELETE CASCADE,

    UNIQUE KEY unique_dept_name (name) -- Note: Unique by name only
);

-- Proposed extensions for advanced RBAC
ALTER TABLE departments ADD COLUMN slug VARCHAR(255) NULL AFTER name;
ALTER TABLE departments ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER description;
ALTER TABLE departments ADD COLUMN manager_user_id CHAR(36) NULL AFTER is_active;
ALTER TABLE departments ADD FOREIGN KEY (manager_user_id) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE departments ADD UNIQUE KEY unique_dept_branch_slug (slug, branch_id);
ALTER TABLE departments ADD INDEX idx_active (is_active);
ALTER TABLE departments ADD INDEX idx_manager (manager_user_id);
```

**Existing Sample Data Structure:**
Departments are already created per branch with category associations:
- Sales category → POS, Corner Store departments
- Production category → Kitchen, Gelato, Confectionery departments
- Support category → HR, Customer Service departments

### 3. `role_category_constraints` Table (New - Simplified Approach)

Instead of complex department mappings, we constrain roles by category and add validation rules. This leverages the existing user-department relationships.

```sql
CREATE TABLE role_category_constraints (
    id CHAR(36) PRIMARY KEY,
    role_name VARCHAR(255) NOT NULL, -- Use role name for simplicity
    category_id CHAR(36) NOT NULL,
    department_type ENUM('specific', 'category_wide', 'branch_wide') DEFAULT 'specific',
    allowed_department_slugs JSON NULL, -- For specific constraints: ["pos", "corner-store"]
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES department_categories(id) ON DELETE CASCADE,

    UNIQUE KEY unique_role_category (role_name, category_id),
    INDEX idx_category (category_id),
    INDEX idx_active (is_active)
);
```

**Sample Data:**
```sql
INSERT INTO role_category_constraints (id, role_name, category_id, department_type, allowed_department_slugs) VALUES
-- Production roles
(UUID(), 'Kitchen Staff', (SELECT id FROM department_categories WHERE name = 'Production'), 'specific', '["main-kitchen"]'),
(UUID(), 'Chef', (SELECT id FROM department_categories WHERE name = 'Production'), 'specific', '["main-kitchen"]'),
(UUID(), 'Head of Production', (SELECT id FROM department_categories WHERE name = 'Production'), 'category_wide', NULL),

-- Gelato roles
(UUID(), 'Gelato Production Staff', (SELECT id FROM department_categories WHERE name = 'Production'), 'specific', '["gelato-production"]'),
(UUID(), 'Head of Gelato', (SELECT id FROM department_categories WHERE name = 'Production'), 'specific', '["gelato-production"]'),

-- Sales roles
(UUID(), 'Cashier', (SELECT id FROM department_categories WHERE name = 'Sales'), 'specific', '["pos"]'),
(UUID(), 'Sales Associate', (SELECT id FROM department_categories WHERE name = 'Sales'), 'specific', '["pos"]'),
(UUID(), 'Corner Store Staff', (SELECT id FROM department_categories WHERE name = 'Sales'), 'specific', '["corner-store"]'),
(UUID(), 'Sales Manager', (SELECT id FROM department_categories WHERE name = 'Sales'), 'category_wide', NULL);
```

### 4. Enhanced User-Role Validation (No New Tables Needed)

Instead of creating complex assignment tables, we leverage the existing `users.department_id` relationship and add validation logic.

**Existing User Table Extensions:**
```sql
-- The users table already has:
ALTER TABLE users ADD COLUMN department_id BIGINT UNSIGNED NULL;
ALTER TABLE users ADD FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL;
```

**Role Assignment Approach:**
- Users get assigned Spatie roles normally via `model_has_roles`
- Department context comes from `user.department_id`
- Validation ensures role is appropriate for user's department category
- No additional assignment tables needed - use existing relationships

**Sample User Data:**
```sql
-- Existing user with department assignment
UPDATE users SET department_id = (
    SELECT id FROM departments WHERE slug = 'pos' AND branch_id = '019b6d6a-191b-7068-817b-94e02d09e859'
) WHERE email = 'cashier@sweettooth.com';

-- Assign role via Spatie (existing functionality)
INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES
((SELECT id FROM roles WHERE name = 'Cashier'), 'App\\Models\\User', (SELECT id FROM users WHERE email = 'cashier@sweettooth.com'));
```

## 🔄 Migration Files

### 1. Extend Departments Table
```php
// database/migrations/2025_01_01_000001_extend_departments_table.php
Schema::table('departments', function (Blueprint $table) {
    // Add slug for URL-friendly department identification
    $table->string('slug')->nullable()->after('name');

    // Add active status
    $table->boolean('is_active')->default(true)->after('description');

    // Add department manager
    $table->uuid('manager_user_id')->nullable()->after('is_active');
    $table->foreign('manager_user_id')->references('id')->on('users')->onDelete('set null');

    // Add unique constraint for slug per branch
    $table->unique(['slug', 'branch_id'], 'unique_dept_branch_slug');

    // Add indexes
    $table->index('is_active');
    $table->index('manager_user_id');
});
```

### 2. Create Role Category Constraints Table
```php
// database/migrations/2025_01_01_000002_create_role_category_constraints_table.php
Schema::create('role_category_constraints', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('role_name');
    $table->uuid('category_id');
    $table->enum('department_type', ['specific', 'category_wide', 'branch_wide'])->default('specific');
    $table->json('allowed_department_slugs')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->foreign('category_id')->references('id')->on('department_categories')->onDelete('cascade');
    $table->unique(['role_name', 'category_id'], 'unique_role_category');
    $table->index(['category_id', 'is_active']);
});
```

### 3. Ensure User Department Relationship
```php
// database/migrations/2025_01_01_000003_ensure_user_department_relationship.php
Schema::table('users', function (Blueprint $table) {
    // Ensure department_id relationship exists (should already be there)
    if (!Schema::hasColumn('users', 'department_id')) {
        $table->unsignedBigInteger('department_id')->nullable()->after('branch_id');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
    }

    // Add index if not exists
    if (!Schema::hasTable('users_department_id_index')) {
        $table->index('department_id');
    }
});
```

## 🔗 Relationship Diagrams

### ERD Overview (Leveraging Existing Structure)
```
department_categories (1) ──── (M) departments ──── (1) users (via department_id)
    │                                        │
    │                                        │
    └── (1) role_category_constraints        └── (M) model_has_roles ─── roles
             │                                                     │
             │                                                     │
             └── constrains role assignments based on department context
```

### Key Relationships
- **department_categories → departments**: Categories contain departments (existing)
- **departments → users**: Users belong to departments via `department_id` (existing)
- **users → roles**: Users have roles via Spatie `model_has_roles` (existing)
- **role_category_constraints**: New validation layer constraining role usage by category/department
- **Branch scoping**: All departments are branch-scoped (existing)

## 📈 Data Integrity

### Foreign Key Constraints
- All relationships use CASCADE DELETE where appropriate
- SET NULL for audit trail preservation (assigned_by)
- UUID foreign keys for consistency

### Unique Constraints
- Department slugs unique per branch
- Role-department mappings unique per role-department combination
- User role assignments unique per user-role-department

### Indexes
- Optimized for common query patterns
- Composite indexes for complex authorization checks

## 🎯 Next Steps

With the database structure in place, proceed to [02 - Models & Relationships](./02-models-relationships.md) to understand the Eloquent implementations.</content>
<parameter name="filePath">md/advance_RBAS/01-database-design.md