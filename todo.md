● NEXT FEATURE TO IMPLEMENT: Department & Position Structure

  ---
  WHY THIS FEATURE FIRST?

  Your current department seeder creates generic departments (HR, Finance,
  Production Line A, etc.). But your SweetTooth business needs specific
  departments like Kitchen, Gelato, Till, Corner Store. Without the correct
  department structure, you can't:
  - Assign employees to the right departments
  - Build department-specific dashboards
  - Track production vs sales workflows
  - Implement proper role-based access control

  ---
  IMPLEMENTATION OUTLINE

  STEP 1: Fix Department Structure

  What you need:
  - Update DepartmentSeeder.php to create SweetTooth-specific departments
  instead of generic ones

  The 8 Core Departments:

  PRODUCTION (makes products):
  1. Kitchen - Prepares food for Till, Confectionaries, Corner Store
  2. Gelato Production - Makes gelato/ice cream
  3. Confectionaries Production - Makes confectionery items

  SALES (sells products):
  4. Till - Sells ready-made snacks
  5. Corner Store - On-demand food sales
  6. Confectionaries Sales - Sells confectionery items

  SUPPORT:
  7. Inventory/Store - Manages all stock
  8. HR - Human resources (corporate level)

  How it works:
  - Corporate departments (HR, Finance) have branch_id = null (company-wide)
  - Each branch gets its own set of: Kitchen, Gelato, Till, Corner Store,
  Confectionaries Production, Confectionaries Sales, Inventory
  - So if you have 5 branches, you'll have: 2 corporate + (7 departments × 5
   branches) = 37 departments total

  ---
  STEP 2: Add Department Category Field

  Current state:
  - Departments table only has type (production/sales)

  What you need:
  - Add category field to distinguish: production, sales, support
  - Why? So you can group departments logically (Inventory is support, not
  sales)

  How to do it:
  - Create new migration: add_category_to_departments_table.php
  - Add column: $table->enum('category', ['production', 'sales', 
  'support'])->default('sales')
  - Update Department model to include category in fillable array

  ---
  STEP 3: Create Position/Job Titles System

  Why you need this:
  - Right now, employees have a position field (just text: "Manager",
  "Chef", etc.)
  - You need structured positions with hierarchy (who reports to whom)

  Create positions table:
  - id
  - name (e.g., "Managing Director", "Head of Production", "Chef")
  - department_id (which department this position belongs to)
  - reports_to (which position this role reports to)
  - level (1=executive, 2=management, 3=staff)
  - description

  Position Hierarchy Example:
  Managing Director (level 1)
  ├── Head of Production (level 2, reports to MD)
  │   ├── Chef (level 3, reports to Head of Production)
  │   ├── Head of Gelato (level 3, reports to Head of Production)
  │   └── Confectionaries Manager (level 3, reports to Head of Production)
  ├── Sales Manager (level 2, reports to MD)
  │   ├── Till Supervisor (level 3, reports to Sales Manager)
  │   └── Corner Store Manager (level 3, reports to Sales Manager)
  ├── HR Manager (level 2, reports to MD)
  └── Inventory Manager (level 2, reports to MD)

  How to implement:
  1. Create migration: create_positions_table.php
  2. Create Position model with self-referencing relationship
  3. Create PositionSeeder with all SweetTooth positions

  ---
  STEP 4: Update Employee Model

  Current state:
  - Employee has position (text field)
  - Employee has department_id

  What to add:
  - position_id (foreign key to positions table)
  - manager_id (foreign key to employees table - who is their manager)

  Why?
  - Structured positions instead of free text
  - Clear reporting lines (Chef reports to Head of Production)
  - Can query: "Show me all employees who report to John"

  How to do it:
  1. Create migration: add_position_and_manager_to_employees_table.php
  2. Add columns: position_id, manager_id
  3. Update Employee model with relationships:
    - position() - belongsTo Position
    - manager() - belongsTo Employee
    - subordinates() - hasMany Employee (people who report to this employee)

  ---
  STEP 5: Update Permissions & Roles

  Current permissions are generic:
  - view-orders, create-orders, etc.

  What you need - Department-specific permissions:

  Production permissions:
  - view-production-queue
  - start-production
  - complete-production
  - manage-recipes

  Sales permissions:
  - process-sale
  - issue-refund
  - view-daily-sales
  - close-register

  Inventory permissions:
  - receive-stock
  - transfer-stock
  - adjust-inventory
  - view-stock-levels

  Management permissions:
  - view-department-reports
  - approve-production
  - manage-staff-schedule
  - view-analytics

  How to implement:
  1. Update PermissionSeeder with department-specific permissions
  2. Group permissions by department/role
  3. Create roles that match your positions:
    - Managing Director → all permissions
    - Head of Production → production permissions + reports
    - Chef → kitchen production permissions
    - Cashier (Till) → sales permissions only
    - Inventory Manager → inventory permissions + reports

  ---
  STEP 6: Create Role Seeder

  Why separate from PermissionSeeder?
  - Roles should match your actual organizational structure
  - Each position should have a corresponding role

  Roles to create:
  1. Managing Director - all permissions
  2. Head of Production - oversee Kitchen, Gelato, Confectionaries
  production
  3. Chef - manage kitchen operations
  4. Head of Gelato - manage gelato production
  5. Sales Manager - oversee Till & Confectionaries sales
  6. Corner Store Manager - manage corner store
  7. HR Manager - manage HR functions
  8. Inventory Manager - manage inventory
  9. Till Cashier - process sales at till
  10. Kitchen Staff - production work
  11. Gelato Staff - gelato production
  12. Corner Store Staff - corner store sales

  How to do it:
  1. Create RoleSeeder.php
  2. Create each role with appropriate permissions
  3. Use guard employees for all branch/department staff
  4. Keep web guard for super admin only

  ---
  STEP 7: Update DatabaseSeeder

  Current order:
  PermissionSeeder::class,
  MDSeeder::class,
  BranchSeeder::class,
  DepartmentSeeder::class,

  New order needed:
  PermissionSeeder::class,      // 1. Create permissions first
  RoleSeeder::class,             // 2. Create roles with permissions
  MDSeeder::class,               // 3. Create MD user
  BranchSeeder::class,           // 4. Create branches
  DepartmentSeeder::class,       // 5. Create departments per branch
  PositionSeeder::class,         // 6. Create positions
  EmployeeSeeder::class,         // 7. Create sample employees (optional)

  Why this order matters:
  - Can't create roles without permissions
  - Can't create departments without branches
  - Can't create positions without departments
  - Can't assign roles to employees without roles existing

  ---
  TESTING STEPS

  After implementing all above:

  1. Fresh migration:
  php artisan migrate:fresh --seed
  2. Verify departments created:
  php artisan tinker
  >>> Department::count()
  >>> Department::where('type', 'production')->get()
  3. Verify positions created:
  >>> Position::with('reportsTo')->get()
  4. Verify roles assigned correctly:
  >>> Role::with('permissions')->where('name', 'Chef')->first()

  ---
  AFTER THIS FEATURE IS COMPLETE:

  You'll have:
  ✅ Proper SweetTooth department structure
  ✅ Position hierarchy with reporting lines
  ✅ Department-specific permissions
  ✅ Roles matching your organizational structure
  ✅ Foundation to build department-specific dashboards

  NEXT FEATURE AFTER THIS:
  Create the business models (Products, Inventory, Orders, Sales) and then
  build department-specific dashboards.

  ---
  Summary: Fix your foundation (departments, positions, roles) before
  building features on top. Right now your seeders create generic
  departments - you need SweetTooth-specific ones. This is the critical
  first step.


🧩 DAY 1 — Department Structure Setup

TODO: Remove default/generic departments.

TODO: Add SweetTooth-specific departments:

Kitchen

Gelato Production

Confectionaries Production

Till

Corner Store

Confectionaries Sales

Inventory/Store

HR

TODO: Ensure HR and Finance are global (no branch_id).

TODO: Create per-branch departments dynamically.

TODO: Seed and verify department structure.

🧱 DAY 2 — Add Department Category

TODO: Add category field to departments table (production, sales, support).

TODO: Update seeder to assign correct categories:

Production: Kitchen, Gelato, Confectionaries

Sales: Till, Corner Store, Confectionaries Sales

Support: Inventory, HR, Finance

TODO: Update model $fillable.

TODO: Verify seeding and relationships.

🏗️ DAY 3 — Position System

TODO: Create positions table with fields: name, department_id, reports_to, level, description.

TODO: Define Position model relationships:

belongsTo Department

belongsTo reports_to (self)

hasMany subordinates (self)

TODO: Seed SweetTooth positions (MD, Managers, Supervisors).

TODO: Define hierarchy and reporting structure.

TODO: Verify relationships in Tinker.

👥 DAY 4 — Employee Model Update

TODO: Add position_id and manager_id to employees table.

TODO: Define relationships in Employee model:

belongsTo Position

belongsTo Manager (self)

hasMany Subordinates (self)

TODO: Connect employees to positions and managers.

TODO: Verify seeded links.

⚙️ DAY 5 — Final Integration & Test

TODO: Rerun full migration + seeding.

TODO: Confirm HR and Finance departments are global.

TODO: Confirm each branch has correct departments.

TODO: Confirm positions and hierarchies render correctly.

TODO: Prepare for next phase — roles & permissions (Spatie integration).
