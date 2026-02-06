<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccountingRolesSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        // Reset cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $accountingManager = Role::firstOrCreate(
            ['name' => 'Accounting Manager', 'guard_name' => $guard],
            [
                'description' => 'Accounting and financial management',
                'display_order' => 14,
            ]
        );

        $accountant = Role::firstOrCreate(
            ['name' => 'Accountant', 'guard_name' => $guard],
            [
                'description' => 'Accounting operations and reporting',
                'display_order' => 15,
            ]
        );

        $managerPermissions = [
            'access_accounting', 'view_financial_reports',
            'manage_accounts', 'manage_periods', 'create_journal_entries', 'reconcile_bank_accounts',
            'view-chart-accounts', 'create-accounts', 'edit-accounts',
            'view-gl-entries', 'create-gl-entries', 'post-gl-entries', 'reverse-gl-entries',
            'view-accounting-reports', 'reconcile-accounts', 'manage-bank-accounts',
            'view-trial-balance', 'view-financial-statements',
            'manage-accounting-period', 'view-account-reconciliation',
            'view-dashboard', 'view-analytics',
        ];

        $accountantPermissions = [
            'access_accounting', 'view_financial_reports',
            'create_journal_entries', 'reconcile_bank_accounts',
            'view-chart-accounts', 'view-gl-entries',
            'view-accounting-reports', 'reconcile-accounts', 'manage-bank-accounts',
            'view-trial-balance', 'view-financial-statements',
            'view-account-reconciliation',
            'view-dashboard',
        ];

        // Ensure permissions exist (non-destructive: only creates missing ones)
        $this->ensurePermissionsExist(array_unique(array_merge($managerPermissions, $accountantPermissions)), $guard);

        // Sync only valid permissions for this guard
        $accountingManager->syncPermissions($this->getPermissionsFor($managerPermissions, $guard));
        $accountant->syncPermissions($this->getPermissionsFor($accountantPermissions, $guard));

        $this->command?->info('✅ Accounting roles seeded safely (non-destructive).');
    }

    /**
     * Create missing permissions without deleting existing ones.
     *
     * @param array<int, string> $permissions
     */
    private function ensurePermissionsExist(array $permissions, string $guard): void
    {
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => $guard],
                ['description' => str_replace('-', ' ', ucfirst($permission))]
            );
        }
    }

    /**
     * Fetch permissions that exist for the guard.
     *
     * @param array<int, string> $permissions
     * @return \Illuminate\Support\Collection<int, \Spatie\Permission\Models\Permission>
     */
    private function getPermissionsFor(array $permissions, string $guard)
    {
        return Permission::where('guard_name', $guard)
            ->whereIn('name', $permissions)
            ->get();
    }
}
