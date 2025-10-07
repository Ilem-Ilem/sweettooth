<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class MDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find the MD role
        $role = Role::firstOrCreate(['name' => 'MD']);

        // ===============================
        // 🔹 DEFINE ALL PERMISSIONS
        // ===============================

        $permissions = [

            // 👨‍🏭 Employee & HR
            'view employees', 'create employees', 'edit employees', 'delete employees',
            'approve employee leave', 'view employee attendance', 'manage payroll',
            'view hr reports', 'assign departments', 'approve recruitment',
            'view employee profile', 'generate employee report', 'terminate employee',
            'view salary structure', 'update employee role', 'view performance reviews',

            // 🏭 Production & Manufacturing
            'view production batches', 'create production batch', 'edit production batch', 'delete production batch',
            'approve production plan', 'view production schedule', 'manage production line', 'monitor production status',
            'record production output', 'update production cost', 'view quality control report',
            'approve quality control', 'view wastage reports', 'update machinery status', 'log equipment maintenance',

            // 📦 Inventory & Warehousing
            'view inventory', 'create inventory item', 'edit inventory item', 'delete inventory item',
            'adjust stock levels', 'view stock history', 'transfer stock', 'receive stock',
            'issue raw materials', 'view warehouse report', 'manage warehouse location',
            'view expiry tracking', 'mark damaged goods', 'approve restock', 'monitor inventory alerts',

            // 🚚 Procurement & Logistics
            'view suppliers', 'create supplier', 'edit supplier', 'delete supplier',
            'approve purchase order', 'create purchase order', 'edit purchase order', 'view purchase history',
            'receive goods', 'approve vendor payment', 'view logistics status', 'schedule delivery',
            'approve transportation', 'track shipment', 'view import/export records', 'manage procurement report',

            // 💰 Sales & Finance
            'view sales records', 'create sales order', 'edit sales order', 'delete sales order',
            'approve sales invoice', 'process refund', 'view financial dashboard', 'approve expense',
            'view profit and loss', 'manage tax settings', 'generate sales reports',
            'approve discounts', 'manage pricing structure', 'view payment history',
            'view cash flow', 'update financial policy',

            // ⚙️ System & Administration
            'view audit logs', 'manage users', 'manage roles', 'manage permissions',
            'view system settings', 'update company info', 'backup database', 'restore backup',
            'view activity logs', 'manage notifications', 'view dashboard', 'access API tokens',
            'view analytics', 'view KPI metrics', 'manage departments', 'configure approval workflow',
            'access admin panel', 'view change history', 'enable maintenance mode', 'disable maintenance mode',

            // 🧾 Compliance & Reporting
            'generate compliance report', 'view supplier compliance', 'approve compliance status',
            'view environmental reports', 'manage food safety records', 'view traceability logs',
            'approve export documents', 'review regulatory submissions', 'view recall reports',
        ];

        // Ensure no duplicates (safe check)
        $permissions = array_unique($permissions);

        // ===============================
        // 🔹 CREATE PERMISSIONS
        // ===============================

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Assign all permissions to MD
        $role->syncPermissions($permissions);

        // ===============================
        // 🔹 CREATE MD USER
        // ===============================

        $user = User::firstOrCreate(
            ['email' => 'md@foodcompany.com'],
            [
                'name' => 'Managing Director',
                'password' => Hash::make('password'), // change after seeding
            ]
        );

        $user->assignRole($role);

        $this->command->info('✅ MD role, permissions, and user created successfully with full system access.');
    }
}
