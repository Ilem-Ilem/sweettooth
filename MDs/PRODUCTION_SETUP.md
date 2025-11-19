# Production Module Setup Guide

## 1. Run Migrations
First, run the migrations to create the necessary tables:

```bash
php artisan migrate
```

This will create:
- `department_pages` table
- Add `slug` column to `departments` table

## 2. Run the Department Pages Seeder
Seed the department pages for all existing production departments:

```bash
php artisan db:seed --class=DepartmentPageSeeder
```

This will:
- Auto-generate slugs for all production departments
- Create 13 default pages for each production department:
  - Products
  - Product Types
  - Recipes (Index, Add, Edit, Detail)
  - Production Requests
  - Daily Produce
  - Raw Material Tracking
  - Module (Index, Stock Monitor)
  - Request (Create, Index)

## 3. Access the Production Menu
Visit: `/branch-dashboard/production`

You'll see:
- All production departments as tabs
- Click on a department to see its pages
- Click on any page card to navigate to that page

## Route Structure

### Old Routes (Commented Out):
```
/branch-dashboard/production/products
/branch-dashboard/production/recipes
etc.
```

### New Dynamic Routes:
```
/branch-dashboard/production/{dept-slug}/products
/branch-dashboard/production/{dept-slug}/recipes
etc.
```

Examples:
- `/branch-dashboard/production/kitchen/products`
- `/branch-dashboard/production/bakery/recipes`
- `/branch-dashboard/production/pastry/daily-produce`

## Managing Department Pages

You can manage pages through the database or by creating an admin interface:

### Add a New Page
```php
DepartmentPage::create([
    'department_id' => 1,
    'name' => 'Quality Control',
    'slug' => 'quality-control',
    'route_name' => 'branch-dashboard.production.kitchen.quality-control',
    'icon' => 'heroicon-o-check-circle',
    'order' => 14,
    'is_active' => true,
]);
```

### Deactivate a Page
```php
DepartmentPage::where('slug', 'recipes-edit')->update(['is_active' => false]);
```

### Change Page Order
```php
DepartmentPage::where('id', 5)->update(['order' => 1]);
```

## Notes

- The `DepartmentObserver` is created but not yet registered (waiting for role-based access setup)
- Old static routes are commented out but preserved for reference
- All existing Livewire components are intact and working with the new dynamic routes
