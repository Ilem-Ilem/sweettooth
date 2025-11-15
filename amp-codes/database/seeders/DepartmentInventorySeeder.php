<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Item;
use Illuminate\Database\Seeder;

class DepartmentInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 30 department-specific items
        $departmentItems = [
            // Raw materials (15)
            ['name' => 'Heavy Cream', 'sku' => 'RM-CREAM-001', 'category' => 'raw_material', 'uom' => 'liters'],
            ['name' => 'Whole Milk', 'sku' => 'RM-MILK-001', 'category' => 'raw_material', 'uom' => 'liters'],
            ['name' => 'Skim Milk Powder', 'sku' => 'RM-SKMPOW-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Glucose Syrup', 'sku' => 'RM-GLUCOSE-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Emulsifier', 'sku' => 'RM-EMUL-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Stabilizer', 'sku' => 'RM-STAB-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Gelatin', 'sku' => 'RM-GELATIN-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Hazelnut Paste', 'sku' => 'RM-HAZEL-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Almond Paste', 'sku' => 'RM-ALMOND-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Honey', 'sku' => 'RM-HONEY-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Dark Chocolate', 'sku' => 'RM-DARKCHO-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'White Chocolate', 'sku' => 'RM-WHITECHO-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Raspberry Puree', 'sku' => 'RM-RASBERRPURE-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Mango Puree', 'sku' => 'RM-MANGOPURE-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Lemon Juice', 'sku' => 'RM-LEMONJUICE-001', 'category' => 'raw_material', 'uom' => 'liters'],

            // Packaging (10)
            ['name' => 'Sugar Cones', 'sku' => 'PKG-SUGARCONE-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Chocolate Cones', 'sku' => 'PKG-CHOCONE-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Plastic Containers (500ml)', 'sku' => 'PKG-CONT-500', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Plastic Containers (1L)', 'sku' => 'PKG-CONT-1L', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Wooden Sticks', 'sku' => 'PKG-STICK-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Wooden Spoons', 'sku' => 'PKG-WOODSPOON-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Parchment Paper', 'sku' => 'PKG-PARCHPAPER-001', 'category' => 'packaging', 'uom' => 'kg'],
            ['name' => 'Aluminum Foil', 'sku' => 'PKG-FOIL-001', 'category' => 'packaging', 'uom' => 'kg'],
            ['name' => 'Plastic Wrap', 'sku' => 'PKG-WRAP-001', 'category' => 'packaging', 'uom' => 'kg'],
            ['name' => 'Delivery Boxes', 'sku' => 'PKG-DELBOX-001', 'category' => 'packaging', 'uom' => 'pcs'],

            // Consumables (3)
            ['name' => 'Food Grade Oil', 'sku' => 'CONS-OIL-001', 'category' => 'consumable', 'uom' => 'liters'],
            ['name' => 'Degreaser', 'sku' => 'CONS-DEGREASE-001', 'category' => 'consumable', 'uom' => 'liters'],
            ['name' => 'Food Grade Lubricant', 'sku' => 'CONS-LUBR-001', 'category' => 'consumable', 'uom' => 'liters'],

            // Equipment (2)
            ['name' => 'Mixing Bowls', 'sku' => 'EQP-BOWL-001', 'category' => 'equipment', 'uom' => 'pcs'],
            ['name' => 'Temperature Probe', 'sku' => 'EQP-PROBE-001', 'category' => 'equipment', 'uom' => 'pcs'],
        ];

        $departments = Department::all();

        foreach ($departments as $department) {
            foreach ($departmentItems as $itemData) {
                Item::create(array_merge($itemData, [
                    'branch_id' => $department->branch_id,
                    'description' => $itemData['name'] . ' for ' . $department->name . ' department',
                    'reorder_level' => 5,
                    'max_stock_level' => 50,
                    'status' => 'active',
                ]));
            }
        }
    }
}
