<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AssignDepartmentUsersAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::query()
            ->where('code', 'PHC-002')
            ->orWhere('name', 'like', '%Port Harcourt%')
            ->first();

        if (! $branch) {
            $this->command->error('Port Harcourt branch not found. Seed branch data first.');

            return;
        }

        $categories = $this->ensureCategories();
        $departments = $this->ensureDepartments($branch, $categories);

        $requirements = [
            'hr' => ['HR Manager', 'HR Officer'],
            'accounting' => ['Accounting Manager', 'Accountant'],
            'production' => ['Head of Production', 'Production Supervisor'],
            'hot_kitchen_production' => ['Production Staff'],
            'pastry_production' => ['Production Staff'],
            'gelato_production' => ['Production Staff'],
            'cornerstone_production' => ['Production Staff'],
            'sales' => ['Sales Manager', 'Sales Supervisor'],
            'till_concession' => ['Sales Staff'],
            'corner_store' => ['Sales Staff'],
            'inventory_store' => ['Inventory Manager', 'Inventory Supervisor', 'Inventory Staff'],
        ];

        $created = 0;
        $updated = 0;
        $counter = 1;
        $passwordHash = Hash::make('password');

        foreach ($requirements as $departmentSlug => $roleNames) {
            $department = $departments[$departmentSlug] ?? null;
            if (! $department) {
                continue;
            }

            foreach ($roleNames as $roleName) {
                $role = Role::query()
                    ->where('guard_name', 'web')
                    ->where('name', $roleName)
                    ->first();

                if (! $role) {
                    $this->command->warn("Role '{$roleName}' was not found. Skipping {$department->name}.");

                    continue;
                }

                $email = $this->buildEmail($department->slug ?? Str::slug($department->name), $roleName, $counter);

                $user = User::query()->firstOrNew(['email' => $email]);
                $isNew = ! $user->exists;

                $user->fill([
                    'name' => $this->buildName($department->name, $roleName),
                    'password' => $passwordHash,
                    'branch_id' => $branch->id,
                    'last_accessed_branch_id' => $branch->id,
                    'department_id' => $department->id,
                    'is_active' => true,
                    'employment_status' => 'active',
                    'user_type' => 'employee',
                    'employee_number' => $this->buildEmployeeNumber($email),
                    'email_verified_at' => now(),
                ]);
                $user->save();
                $user->syncRoles([$role]);

                if ($isNew) {
                    $created++;
                } else {
                    $updated++;
                }

                $counter++;
            }
        }

        $this->command->info("Department login users seeded. Created: {$created}, Updated: {$updated}");
        $this->command->warn('Default password for created users: password');
    }

    /**
     * @return array<string, DepartmentCategory>
     */
    private function ensureCategories(): array
    {
        return [
            'sales' => DepartmentCategory::updateOrCreate(
                ['name' => 'Sales'],
                ['description' => 'Sales departments.']
            ),
            'production' => DepartmentCategory::updateOrCreate(
                ['name' => 'Production'],
                ['description' => 'Production departments.']
            ),
            'support' => DepartmentCategory::updateOrCreate(
                ['name' => 'Support'],
                ['description' => 'Support departments.']
            ),
            'accounting' => DepartmentCategory::updateOrCreate(
                ['name' => 'Accounting'],
                ['description' => 'Accounting departments.']
            ),
        ];
    }

    /**
     * @param  array<string, DepartmentCategory>  $categories
     * @return array<string, Department>
     */
    private function ensureDepartments(Branch $branch, array $categories): array
    {
        $definitions = [
            'hr' => [
                'name' => 'HR',
                'category' => 'support',
                'description' => 'Human resources department.',
            ],
            'accounting' => [
                'name' => 'Accounting',
                'category' => 'accounting',
                'description' => 'Accounting department.',
            ],
            'production' => [
                'name' => 'Production',
                'category' => 'production',
                'description' => 'Production department.',
            ],
            'hot_kitchen_production' => [
                'name' => 'Hot Kitchen Production',
                'category' => 'production',
                'description' => 'Hot kitchen production department.',
            ],
            'pastry_production' => [
                'name' => 'Pastry Production',
                'category' => 'production',
                'description' => 'Pastry production department.',
            ],
            'gelato_production' => [
                'name' => 'Gelato Production',
                'category' => 'production',
                'description' => 'Gelato production department.',
            ],
            'cornerstone_production' => [
                'name' => 'Cornerstone Production',
                'category' => 'production',
                'description' => 'Cornerstone production department.',
            ],
            'sales' => [
                'name' => 'Sales',
                'category' => 'sales',
                'description' => 'Sales department.',
            ],
            'till_concession' => [
                'name' => 'Till/Concession',
                'category' => 'sales',
                'description' => 'Till and concession sales department.',
            ],
            'corner_store' => [
                'name' => 'Corner Store',
                'category' => 'sales',
                'description' => 'Corner Store sales department.',
            ],
            'inventory_store' => [
                'name' => 'Inventory/Store',
                'category' => 'support',
                'description' => 'Inventory/store department.',
            ],
        ];

        $departments = [];
        foreach ($definitions as $slug => $definition) {
            $department = Department::query()->where('name', $definition['name'])->first();
            if (! $department) {
                $department = new Department();
            }

            $department->fill([
                'branch_id' => $branch->id,
                'slug' => $slug,
                'name' => $definition['name'],
                'category_id' => $categories[$definition['category']]->id,
                'description' => $definition['description'],
            ]);
            $department->save();

            $departments[$slug] = $department;
        }

        return $departments;
    }

    private function buildEmail(string $departmentSlug, string $roleName, int $counter): string
    {
        $roleSlug = Str::slug($roleName, '.');

        return "{$departmentSlug}.{$roleSlug}.{$counter}@sweettooth.local";
    }

    private function buildName(string $departmentName, string $roleName): string
    {
        return "{$departmentName} {$roleName}";
    }

    private function buildEmployeeNumber(string $email): string
    {
        return 'PHC-AUTO-'.strtoupper(substr(sha1($email), 0, 8));
    }
}
