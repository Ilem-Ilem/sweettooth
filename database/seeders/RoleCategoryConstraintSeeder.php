<?php

namespace Database\Seeders;

use App\Models\RoleCategoryConstraint;
use Illuminate\Database\Seeder;

class RoleCategoryConstraintSeeder extends Seeder
{
    public function run(): void
    {
        // Department-independent roles: no constraints by default
        RoleCategoryConstraint::query()->delete();
        $this->command->info('Role category constraints cleared (department-independent roles enabled).');
    }
}
