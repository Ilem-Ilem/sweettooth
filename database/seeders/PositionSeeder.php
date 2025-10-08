<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Department;
use Spatie\Permission\Models\Role;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get departments
        $kitchen = Department::where('name', 'Kitchen')->first();
        $gelato = Department::where('name', 'Gelato Production')->first();
        $confectionariesProd = Department::where('name', 'Confectionaries Production')->first();
        $till = Department::where('name', 'Till')->first();
        $cornerStore = Department::where('name', 'Corner Store')->first();
        $confectionariesSales = Department::where('name', 'Confectionaries Sales')->first();
        $inventory = Department::where('name', 'Inventory/Store')->first();
        $hr = Department::where('name', 'HR')->first();

        // Get roles
        $roles = Role::where('guard_name', 'employees')->get()->keyBy('name');

        // Level 1: Executive
        $md = Position::create([
            'name' => 'Managing Director',
            'department_id' => null, // Corporate level
            'role_id' => $roles->get('Managing Director')->id ?? null,
            'reports_to' => null,
            'level' => 1,
            'description' => 'Overall business leadership and strategic direction',
        ]);

        // Level 2: Management (Reports to MD)
        $headOfProduction = Position::create([
            'name' => 'Head of Production',
            'department_id' => null, // Oversees all production departments
            'role_id' => $roles->get('Head of Production')->id ?? null,
            'reports_to' => $md->id,
            'level' => 2,
            'description' => 'Oversees Kitchen, Gelato, and Confectionaries production',
        ]);

        $salesManager = Position::create([
            'name' => 'Sales Manager',
            'department_id' => null, // Oversees all sales departments
            'role_id' => $roles->get('Sales Manager')->id ?? null,
            'reports_to' => $md->id,
            'level' => 2,
            'description' => 'Oversees Till and Confectionaries sales operations',
        ]);

        $hrManager = Position::create([
            'name' => 'HR Manager',
            'department_id' => $hr?->id,
            'role_id' => $roles->get('HR Manager')->id ?? null,
            'reports_to' => $md->id,
            'level' => 2,
            'description' => 'Human resources management and employee relations',
        ]);

        $inventoryManager = Position::create([
            'name' => 'Inventory Manager',
            'department_id' => $inventory?->id,
            'role_id' => $roles->get('Inventory Manager')->id ?? null,
            'reports_to' => $md->id,
            'level' => 2,
            'description' => 'Manages all stock and inventory control',
        ]);

        // Level 3: Department Heads/Supervisors (Reports to their respective managers)

        // Production positions
        if ($kitchen) {
            $chef = Position::create([
                'name' => 'Chef',
                'department_id' => $kitchen->id,
                'role_id' => $roles->get('Chef')->id ?? null,
                'reports_to' => $headOfProduction->id,
                'level' => 3,
                'description' => 'Manages kitchen operations and food preparation',
            ]);

            Position::create([
                'name' => 'Kitchen Staff',
                'department_id' => $kitchen->id,
                'role_id' => $roles->get('Kitchen Staff')->id ?? null,
                'reports_to' => $chef->id,
                'level' => 3,
                'description' => 'Food preparation and kitchen support',
            ]);
        }

        if ($gelato) {
            $headOfGelato = Position::create([
                'name' => 'Head of Gelato',
                'department_id' => $gelato->id,
                'role_id' => $roles->get('Head of Gelato')->id ?? null,
                'reports_to' => $headOfProduction->id,
                'level' => 3,
                'description' => 'Manages gelato/ice cream production',
            ]);

            Position::create([
                'name' => 'Gelato Production Staff',
                'department_id' => $gelato->id,
                'role_id' => $roles->get('Gelato Production Staff')->id ?? null,
                'reports_to' => $headOfGelato->id,
                'level' => 3,
                'description' => 'Gelato production and quality control',
            ]);
        }

        if ($confectionariesProd) {
            $confectionariesManager = Position::create([
                'name' => 'Confectionaries Manager',
                'department_id' => $confectionariesProd->id,
                'role_id' => $roles->get('Confectionaries Manager')->id ?? null,
                'reports_to' => $headOfProduction->id,
                'level' => 3,
                'description' => 'Manages confectionery production',
            ]);

            Position::create([
                'name' => 'Confectionaries Production Staff',
                'department_id' => $confectionariesProd->id,
                'role_id' => $roles->get('Confectionaries Production Staff')->id ?? null,
                'reports_to' => $confectionariesManager->id,
                'level' => 3,
                'description' => 'Confectionery production and packaging',
            ]);
        }

        // Sales positions
        if ($till) {
            $tillSupervisor = Position::create([
                'name' => 'Till Supervisor',
                'department_id' => $till->id,
                'role_id' => $roles->get('Till Supervisor')->id ?? null,
                'reports_to' => $salesManager->id,
                'level' => 3,
                'description' => 'Supervises till operations and cashiers',
            ]);

            Position::create([
                'name' => 'Cashier',
                'department_id' => $till->id,
                'role_id' => $roles->get('Cashier')->id ?? null,
                'reports_to' => $tillSupervisor->id,
                'level' => 3,
                'description' => 'Processes sales transactions at till',
            ]);
        }

        if ($cornerStore) {
            $cornerStoreManager = Position::create([
                'name' => 'Corner Store Manager',
                'department_id' => $cornerStore->id,
                'role_id' => $roles->get('Corner Store Manager')->id ?? null,
                'reports_to' => $salesManager->id,
                'level' => 3,
                'description' => 'Manages corner store operations',
            ]);

            Position::create([
                'name' => 'Corner Store Staff',
                'department_id' => $cornerStore->id,
                'role_id' => $roles->get('Corner Store Staff')->id ?? null,
                'reports_to' => $cornerStoreManager->id,
                'level' => 3,
                'description' => 'Customer service and on-demand food sales',
            ]);
        }

        if ($confectionariesSales) {
            $confectionariesSalesManager = Position::create([
                'name' => 'Confectionaries Sales Manager',
                'department_id' => $confectionariesSales->id,
                'role_id' => null,
                'reports_to' => $salesManager->id,
                'level' => 3,
                'description' => 'Manages confectionery sales',
            ]);

            Position::create([
                'name' => 'Confectionaries Sales Staff',
                'department_id' => $confectionariesSales->id,
                'role_id' => $roles->get('Confectionaries Sales Staff')->id ?? null,
                'reports_to' => $confectionariesSalesManager->id,
                'level' => 3,
                'description' => 'Sales and customer service for confectionery items',
            ]);
        }

        // Support positions
        if ($inventory) {
            Position::create([
                'name' => 'Stock Controller',
                'department_id' => $inventory->id,
                'role_id' => $roles->get('Stock Controller')->id ?? null,
                'reports_to' => $inventoryManager->id,
                'level' => 3,
                'description' => 'Inventory tracking and stock management',
            ]);

            Position::create([
                'name' => 'Store Keeper',
                'department_id' => $inventory->id,
                'role_id' => $roles->get('Store Keeper')->id ?? null,
                'reports_to' => $inventoryManager->id,
                'level' => 3,
                'description' => 'Receives and manages warehouse stock',
            ]);
        }

        if ($hr) {
            Position::create([
                'name' => 'HR Officer',
                'department_id' => $hr->id,
                'role_id' => $roles->get('HR Officer')->id ?? null,
                'reports_to' => $hrManager->id,
                'level' => 3,
                'description' => 'HR operations and employee support',
            ]);
        }

        $this->command->info("✅ " . Position::count() . " positions created successfully with reporting hierarchy.");
    }
}
