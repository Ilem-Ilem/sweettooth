<?php

namespace App\Console\Commands;

use App\Models\UnitOfMeasure;
use Illuminate\Console\Command;

class SeedBaseUoms extends Command
{
    protected $signature = 'uoms:seed';
    protected $description = 'Seed base units of measure required for production data import';

    public function handle(): int
    {
        $uoms = [
            // Weight/Mass
            ['code' => 'g', 'name' => 'Grams', 'symbol' => 'g', 'category' => 'weight', 'sort_order' => 1],
            ['code' => 'kg', 'name' => 'Kilograms', 'symbol' => 'kg', 'category' => 'weight', 'sort_order' => 2],
            ['code' => 'mg', 'name' => 'Milligrams', 'symbol' => 'mg', 'category' => 'weight', 'sort_order' => 3],
            
            // Volume/Liquid
            ['code' => 'ml', 'name' => 'Milliliters', 'symbol' => 'ml', 'category' => 'volume', 'sort_order' => 4],
            ['code' => 'l', 'name' => 'Liters', 'symbol' => 'l', 'category' => 'volume', 'sort_order' => 5],
            ['code' => 'cl', 'name' => 'Centiliters', 'symbol' => 'cl', 'category' => 'volume', 'sort_order' => 6],
            
            // Count/Units
            ['code' => 'pcs', 'name' => 'Pieces', 'symbol' => 'pcs', 'category' => 'count', 'sort_order' => 7],
            ['code' => 'unit', 'name' => 'Units', 'symbol' => 'unit', 'category' => 'count', 'sort_order' => 8],
            ['code' => 'dozen', 'name' => 'Dozen', 'symbol' => 'doz', 'category' => 'count', 'sort_order' => 9],
            
            // Portion/Serving
            ['code' => 'portion', 'name' => 'Portions', 'symbol' => 'portion', 'category' => 'portion', 'sort_order' => 10],
            ['code' => 'serving', 'name' => 'Servings', 'symbol' => 'serving', 'category' => 'portion', 'sort_order' => 11],
            
            // Container/Packaging
            ['code' => 'pack', 'name' => 'Packs', 'symbol' => 'pk', 'category' => 'package', 'sort_order' => 12],
            ['code' => 'box', 'name' => 'Boxes', 'symbol' => 'box', 'category' => 'package', 'sort_order' => 13],
            ['code' => 'bottle', 'name' => 'Bottles', 'symbol' => 'btl', 'category' => 'package', 'sort_order' => 14],
            ['code' => 'jar', 'name' => 'Jars', 'symbol' => 'jar', 'category' => 'package', 'sort_order' => 15],
            ['code' => 'bag', 'name' => 'Bags', 'symbol' => 'bag', 'category' => 'package', 'sort_order' => 16],
            ['code' => 'container', 'name' => 'Containers', 'symbol' => 'cnt', 'category' => 'package', 'sort_order' => 17],
            
            // Kitchen/Cooking
            ['code' => 'cup', 'name' => 'Cups', 'symbol' => 'cup', 'category' => 'cooking', 'sort_order' => 18],
            ['code' => 'tbsp', 'name' => 'Tablespoons', 'symbol' => 'tbsp', 'category' => 'cooking', 'sort_order' => 19],
            ['code' => 'tsp', 'name' => 'Teaspoons', 'symbol' => 'tsp', 'category' => 'cooking', 'sort_order' => 20],
            ['code' => 'scoop', 'name' => 'Scoops', 'symbol' => 'scoop', 'category' => 'cooking', 'sort_order' => 21],
            ['code' => 'pinch', 'name' => 'Pinches', 'symbol' => 'pinch', 'category' => 'cooking', 'sort_order' => 22],
            
            // Recipe/WIP
            ['code' => 'recipe', 'name' => 'Recipe Units', 'symbol' => 'recipe', 'category' => 'recipe', 'sort_order' => 23],
            ['code' => 'batch', 'name' => 'Batches', 'symbol' => 'batch', 'category' => 'recipe', 'sort_order' => 24],
            
            // Miscellaneous
            ['code' => 'roll', 'name' => 'Rolls', 'symbol' => 'roll', 'category' => 'misc', 'sort_order' => 25],
            ['code' => 'sheet', 'name' => 'Sheets', 'symbol' => 'sheet', 'category' => 'misc', 'sort_order' => 26],
            ['code' => 'set', 'name' => 'Sets', 'symbol' => 'set', 'category' => 'misc', 'sort_order' => 27],
        ];

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($uoms as $uomData) {
            $uom = UnitOfMeasure::where('code', $uomData['code'])->first();

            if ($uom) {
                // Update existing UOM
                $uom->update([
                    'name' => $uomData['name'],
                    'symbol' => $uomData['symbol'],
                    'category' => $uomData['category'],
                    'sort_order' => $uomData['sort_order'],
                    'is_active' => true,
                ]);
                $updated++;
            } else {
                // Create new UOM
                UnitOfMeasure::create([
                    ...$uomData,
                    'description' => "{$uomData['name']} ({$uomData['symbol']})",
                    'is_active' => true,
                ]);
                $created++;
            }
        }

        $this->info("UOM seeding completed:");
        $this->info("  Created: {$created}");
        $this->info("  Updated: {$updated}");
        $this->info("  Skipped: {$skipped}");

        // Create UOM conversions for weight
        $this->createUomConversions();

        return 0;
    }

    private function createUomConversions(): void
    {
        $conversions = [
            // Weight conversions (base: grams)
            ['from' => 'kg', 'to' => 'g', 'factor' => 1000],
            ['from' => 'g', 'to' => 'kg', 'factor' => 0.001],
            ['from' => 'g', 'to' => 'mg', 'factor' => 1000],
            ['from' => 'mg', 'to' => 'g', 'factor' => 0.001],
            
            // Volume conversions (base: milliliters)
            ['from' => 'l', 'to' => 'ml', 'factor' => 1000],
            ['from' => 'ml', 'to' => 'l', 'factor' => 0.001],
            ['from' => 'cl', 'to' => 'ml', 'factor' => 10],
            ['from' => 'ml', 'to' => 'cl', 'factor' => 0.1],
            
            // Count conversions
            ['from' => 'dozen', 'to' => 'pcs', 'factor' => 12],
            ['from' => 'pcs', 'to' => 'dozen', 'factor' => 1/12],
        ];

        $conversionCreated = 0;

        foreach ($conversions as $conv) {
            $fromUom = UnitOfMeasure::where('code', $conv['from'])->first();
            $toUom = UnitOfMeasure::where('code', $conv['to'])->first();

            if ($fromUom && $toUom) {
                \App\Models\UomConversion::updateOrCreate(
                    [
                        'from_uom_id' => $fromUom->id,
                        'to_uom_id' => $toUom->id,
                    ],
                    [
                        'conversion_factor' => $conv['factor'],
                        'is_active' => true,
                    ]
                );
                $conversionCreated++;
            }
        }

        $this->info("  UOM conversions created/updated: {$conversionCreated}");
    }
}
