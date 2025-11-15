# Database Seeders Documentation

All seeders are located in `/amp-codes/database/seeders/` and follow the dependency order in `DatabaseSeeder.php`.

## Seeder Overview

### 1. UserSeeder
- Creates 4 test users with different roles
- Users:
  - Super Admin (admin@sweettooth.local)
  - Branch Manager (manager@sweettooth.local)
  - Department Head (depthead@sweettooth.local)
  - Employee (employee@sweettooth.local)
- All passwords: `password123`

### 2. BranchSeeder
- Creates 3 branches:
  - Main Branch (MB001)
  - Downtown Branch (DB002)
  - Airport Branch (AB003)

### 3. DepartmentCategorySeeder
- Creates 5 department categories:
  - Production
  - Sales & Service
  - Inventory
  - Administration
  - Finance

### 4. DepartmentSeeder
- Creates 10 departments across branches:
  - Main Branch: Gelato Production, Pastry Production, Hot Kitchen, Sales Counter, Delivery Service, Warehouse (6 departments)
  - Downtown Branch: Gelato Production, Sales Counter (2 departments)
  - Airport Branch: Sales Counter, Mini Warehouse (2 departments)

### 5. EmployeeSeeder
- Creates 8 employees across departments:
  - 1 Manager
  - 2 Gelato Production employees
  - 1 Pastry Production employee
  - 2 Sales Counter employees
  - 1 Warehouse employee
  - 1 Downtown branch employee
- All employees have passwords: `password123`

### 6. PermissionSeeder
- Creates 4 roles: Admin, Manager, Supervisor, Employee
- Assigns permissions for:
  - User, Employee, Department, Product management
  - Recipe, Item, Sale, Shift management
  - Production operations

### 7. ItemSeeder
**20 items per branch (60 total items)**
- Raw Materials (8): Milk, Sugar, Cocoa, Vanilla, Strawberry, Pistachio, Eggs, Butter
- Packaging (8): Cones, Cups, Spoons, Napkins, Bags, Boxes, Lids
- Consumables (2): Cleaning supplies, Sanitizer
- Equipment (2): Scoop, Display case

### 8. DepartmentInventorySeeder
**30 items per department**
- Raw Materials (15): Cream, Milk, Powders, Syrups, Purees, Chocolates, Juices
- Packaging (10): Cones, Containers, Sticks, Paper products
- Consumables (3): Oil, Degreaser, Lubricant
- Equipment (2): Bowls, Thermometer

### 9. ProductTypeSeeder
- Creates 6 product types across departments:
  - Gelato Department: Gelato Base (GB), Gelato Flavor (GF)
  - Pastry Department: Pastry (PT), Cake (CK)
  - Hot Kitchen: Beverage (BEV), Hot Food (HF)

### 10. ProductSeeder
**10 products per branch (30 total)**
- Gelato products: Vanilla, Chocolate, Strawberry, Pistachio
- Pastry: Croissant, Danish
- Cakes: Chocolate Cake, Cheesecake
- Beverages: Espresso, Cappuccino

### 11. RecipeSeeder
- Creates 8 recipes in main branch:
  - Vanilla Gelato
  - Chocolate Gelato
  - Strawberry Gelato
  - Pistachio Gelato
  - Coffee Gelato
  - Hazelnut Gelato
  - Mint Chocolate Gelato
  - Lemon Gelato

### 12. RecipeIngredientSeeder
- Associates 3-5 ingredients per recipe from raw materials

### 13. ShiftSeeder
- Creates 15 shifts per employee over 30 days
- Shift types: Morning, Afternoon, Night
- Includes clock-in/out times

### 14. LeaveTypeSeeder
- Creates 7 leave types:
  - Annual Leave (20 days/year)
  - Sick Leave (10 days/year)
  - Emergency Leave (3 days/year)
  - Maternity Leave (90 days)
  - Paternity Leave (10 days)
  - Unpaid Leave
  - Study Leave (5 days/year)

### 15. GlobalBusinessConfigurationSeeder
- Creates 3 business configurations for each branch
- Includes tax rates, service charges, discounts
- Enables/disables features per branch

### 16. SalesShiftSeeder
- Creates 30 sales shifts per branch
- 3 shifts per day (morning, afternoon, night)
- Tracks opening/closing cash and totals

### 17. SalesSeeder
- Creates 5-15 sales per shift (450+ sales total)
- 2-5 items per sale
- Calculates tax, service charge, discounts
- Updates shift totals

## Data Summary

| Entity | Count |
|--------|-------|
| Users | 4 |
| Branches | 3 |
| Departments | 10 |
| Employees | 8 |
| Items per Branch | 20 |
| Items per Department | 30 |
| Products per Branch | 10 |
| Product Types | 6 |
| Recipes | 8 |
| Leave Types | 7 |
| Shifts per Employee | 15 |
| Sales Shifts per Branch | 30 |
| Total Sales | 450+ |

## Running the Seeders

```bash
# Run all seeders
php artisan db:seed --class=DatabaseSeeder

# Run specific seeder
php artisan db:seed --class=ItemSeeder

# Fresh database with seeders
php artisan migrate:fresh --seed
```

## Notes

- All SKUs are unique
- Relationships are properly established
- Date ranges use the last 30 days for shifts and sales
- Branch-specific reorder levels and stock levels are configured
- All timestamps are automatically set
- Soft deletes are configured for appropriate models
