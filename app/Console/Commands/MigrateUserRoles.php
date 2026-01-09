<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class MigrateUserRoles extends Command
{
    protected $signature = 'users:migrate-roles {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Migrate existing users from old roles to simplified role system';

    protected array $roleMapping = [
        // Old roles -> New roles
        'Super Admin' => 'Super Admin',
        'MD' => 'Super Admin',
        'Managing Director' => 'Super Admin',
        'Admin' => 'Super Admin',

        // Department Heads -> Manager
        'Head of Production' => 'Manager',
        'Sales Manager' => 'Manager',
        'HR Manager' => 'Manager',
        'Inventory Manager' => 'Manager',

        // Supervisors -> Supervisor
        'Supervisor' => 'Supervisor',
        'Till Supervisor' => 'Supervisor',
        'Sales Supervisor' => 'Supervisor',

        // Specialists -> Specialist
        'Chef' => 'Specialist',
        'Head of Gelato' => 'Specialist',
        'Confectionaries Manager' => 'Specialist',

        // Production Staff -> Production Staff
        'Kitchen Staff' => 'Production Staff',
        'Gelato Production Staff' => 'Production Staff',
        'Confectionaries Production Staff' => 'Production Staff',

        // Sales Staff -> Sales Staff
        'Cashier' => 'Sales Staff',
        'Sales Associate' => 'Sales Staff',
        'Junior Cashier' => 'Sales Staff',
        'Corner Store Staff' => 'Sales Staff',
        'Corner Store Manager' => 'Sales Staff', // Downgrade to staff level

        // Inventory -> Inventory Staff
        'Stock Controller' => 'Inventory Staff',
        'Store Keeper' => 'Inventory Staff',

        // HR -> HR Staff
        'HR Officer' => 'HR Staff',

        // Generic
        'Employee' => 'Employee',
        'Viewer' => 'Viewer',
    ];

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $users = User::with('roles')->get();

        $this->info($isDryRun ? 'DRY RUN: Showing role migration changes' : 'Migrating user roles to simplified system...');
        $this->newLine();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $changes = [];

        foreach ($users as $user) {
            $oldRoles = $user->roles->pluck('name')->toArray();
            $newRoles = $this->mapRoles($oldRoles);

            if ($this->rolesChanged($oldRoles, $newRoles)) {
                $changes[] = [
                    'user' => $user->name . ' (' . $user->email . ')',
                    'old_roles' => $oldRoles,
                    'new_roles' => $newRoles,
                ];

                if (!$isDryRun) {
                    // Remove old roles
                    $user->roles()->detach();

                    // Assign new roles
                    foreach ($newRoles as $newRole) {
                        if ($role = Role::where('name', $newRole)->first()) {
                            $user->assignRole($role);
                        }
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($changes)) {
            $this->info('✅ No role changes needed.');
            return;
        }

        // Display summary
        $this->table(
            ['User', 'Old Roles', 'New Roles'],
            array_map(function ($change) {
                return [
                    $change['user'],
                    implode(', ', $change['old_roles']),
                    implode(', ', $change['new_roles']),
                ];
            }, $changes)
        );

        $this->newLine();
        $this->info($isDryRun
            ? "DRY RUN: Would migrate " . count($changes) . " users."
            : "✅ Successfully migrated " . count($changes) . " users to simplified roles."
        );
    }

    private function mapRoles(array $oldRoles): array
    {
        $newRoles = [];

        foreach ($oldRoles as $oldRole) {
            if (isset($this->roleMapping[$oldRole])) {
                $newRole = $this->roleMapping[$oldRole];
                if (!in_array($newRole, $newRoles)) {
                    $newRoles[] = $newRole;
                }
            }
        }

        return array_unique($newRoles);
    }

    private function rolesChanged(array $oldRoles, array $newRoles): bool
    {
        sort($oldRoles);
        sort($newRoles);

        // Map old roles to new for comparison
        $mappedOldRoles = array_map(function ($role) {
            return $this->roleMapping[$role] ?? $role;
        }, $oldRoles);

        sort($mappedOldRoles);

        return $mappedOldRoles !== $newRoles;
    }
}