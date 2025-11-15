<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainBranch = Branch::where('code', 'MB001')->first();
        $downBranch = Branch::where('code', 'DB002')->first();
        $airportBranch = Branch::where('code', 'AB003')->first();

        // 20 items per branch
        $items = [
            // Raw materials - 8 items
            ['name' => 'Milk Cream Base', 'sku' => 'SKU-MILK-001', 'category' => 'raw_material', 'uom' => 'liters'],
            ['name' => 'Sugar', 'sku' => 'SKU-SUGAR-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Cocoa Powder', 'sku' => 'SKU-COCOA-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Vanilla Extract', 'sku' => 'SKU-VANILLA-001', 'category' => 'raw_material', 'uom' => 'liters'],
            ['name' => 'Strawberry Puree', 'sku' => 'SKU-STRAWB-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Pistachio Paste', 'sku' => 'SKU-PIST-001', 'category' => 'raw_material', 'uom' => 'kg'],
            ['name' => 'Egg Yolks', 'sku' => 'SKU-EGGS-001', 'category' => 'raw_material', 'uom' => 'pcs'],
            ['name' => 'Butter', 'sku' => 'SKU-BUTTER-001', 'category' => 'raw_material', 'uom' => 'kg'],

            // Packaging - 8 items
            ['name' => 'Waffle Cones', 'sku' => 'SKU-CONE-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Paper Cups (125ml)', 'sku' => 'SKU-CUP-125', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Paper Cups (250ml)', 'sku' => 'SKU-CUP-250', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Plastic Spoons', 'sku' => 'SKU-SPOON-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Napkins', 'sku' => 'SKU-NAPKIN-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Paper Bags', 'sku' => 'SKU-BAG-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Carton Boxes', 'sku' => 'SKU-BOX-001', 'category' => 'packaging', 'uom' => 'pcs'],
            ['name' => 'Plastic Lids', 'sku' => 'SKU-LIDS-001', 'category' => 'packaging', 'uom' => 'pcs'],

            // Consumables - 2 items
            ['name' => 'Cleaning Supplies', 'sku' => 'SKU-CLEAN-001', 'category' => 'consumable', 'uom' => 'liters'],
            ['name' => 'Sanitizer', 'sku' => 'SKU-SANIT-001', 'category' => 'consumable', 'uom' => 'liters'],

            // Equipment - 2 items
            ['name' => 'Ice Cream Scoop', 'sku' => 'SKU-SCOOP-001', 'category' => 'equipment', 'uom' => 'pcs'],
            ['name' => 'Gelato Display Case', 'sku' => 'SKU-DISPLAY-001', 'category' => 'equipment', 'uom' => 'pcs'],
        ];

        $branches = [
            ['branch' => $mainBranch, 'reorder' => 10, 'max' => 100],
            ['branch' => $downBranch, 'reorder' => 5, 'max' => 50],
            ['branch' => $airportBranch, 'reorder' => 3, 'max' => 30],
        ];

        foreach ($branches as $branchData) {
            foreach ($items as $item) {
                Item::create(array_merge($item, [
                    'branch_id' => $branchData['branch']->id,
                    'description' => ucfirst($item['name']) . ' for ' . $branchData['branch']->name,
                    'reorder_level' => $branchData['reorder'],
                    'max_stock_level' => $branchData['max'],
                    'status' => 'active',
                ]));
            }
        }
    }
}
