# Seeder Setup Guide

## Quick Setup (Faster than migrate:fresh)

Run individual seeders without resetting the entire database:

### 1. Create Super Admin User
```bash
php artisan db:seed --class=SuperAdminUserSeeder
```

Credentials:
- Email: `admin@sweettooth.local`
- Password: `password`
- Role: Super Admin (web guard)

### 2. Setup Web Guard Roles
```bash
php artisan db:seed --class=WebRoleSeeder
```

Creates roles:
- Super Admin
- MD (Managing Director)
- Managing Director
- Admin

### 3. Setup Accounting
```bash
php artisan db:seed --class=ChartOfAccountsSeeder
php artisan db:seed --class=AccountingAccessControlSeeder
```

### All at Once
```bash
php artisan db:seed --class=WebRoleSeeder && \
php artisan db:seed --class=SuperAdminUserSeeder && \
php artisan db:seed --class=ChartOfAccountsSeeder && \
php artisan db:seed --class=AccountingAccessControlSeeder
```

## Full Database Reset (if needed)

```bash
php artisan migrate:fresh --seed
```

**Note:** Full reset takes time because it includes ProductionSeeder which can have unique constraint issues.

## Dashboard Access

After running the super admin seeder:

1. Login at: `http://localhost:8000/login`
2. Email: `admin@sweettooth.local`
3. Password: `password`
4. You should be automatically redirected to the super-admin dashboard

## Files Modified
- `database/seeders/DatabaseSeeder.php` - Added WebRoleSeeder and SuperAdminUserSeeder
- `database/seeders/SuperAdminUserSeeder.php` - New file
- `DASHBOARD_ACCESS_FIX.md` - Dashboard routing fix documentation
