<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SetupPermissionsSummarySeeder extends Seeder
{
    /**
     * Display a summary of the permissions setup.
     */
    public function run(): void
    {
        $this->command->info('=== SWEETTOOTH PERMISSIONS SETUP SUMMARY ===');
        $this->command->newLine();

        // Super Admin Information
        $superAdmin = User::where('email', 'admin@sweettooth.local')->first();
        $this->command->info('SUPER ADMIN CONFIGURATION:');
        $this->command->line("  Email: {$superAdmin->email}");
        $this->command->line("  Name: {$superAdmin->name}");
        $this->command->line("  Roles: " . $superAdmin->roles->pluck('name')->join(', '));
        $this->command->line("  Total Permissions: " . $superAdmin->getAllPermissions()->count());
        $this->command->newLine();

        // Total counts
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        
        $this->command->info('SYSTEM STATISTICS:');
        $this->command->line("  Total Users: $totalUsers");
        $this->command->line("  Total Roles: $totalRoles");
        $this->command->line("  Total Permissions: $totalPermissions");
        $this->command->newLine();

        // Role distribution
        $this->command->info('ROLE DISTRIBUTION:');
        $roleCounts = [];
        foreach (Role::all() as $role) {
            $count = $role->users->count();
            if ($count > 0) {
                $roleCounts[$role->name] = $count;
            }
        }
        
        arsort($roleCounts); // Sort by count descending
        foreach ($roleCounts as $roleName => $count) {
            $this->command->line("  $roleName: $count users");
        }
        $this->command->newLine();

        $this->command->info('PERMISSIONS ASSIGNMENT LOGIC:');
        $this->command->line('  • Super Admin user (admin@sweettooth.local) has ALL permissions via Super Admin role');
        $this->command->line('  • Regular users have roles assigned based on their department');
        $this->command->line('  • Each department has appropriate role hierarchy (Manager → Supervisor → Staff)');
        $this->command->line('  • Permissions are inherited through roles using Spatie Laravel Permission package');
        $this->command->newLine();

        $this->command->info('CONFIGURATION COMPLETE!');
        $this->command->line('Super Admin user has been granted all system permissions.');
        $this->command->line('Regular users have been assigned roles based on their departments.');
        $this->command->line('System is now ready for secure role-based access control.');
    }
}