<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\ParsesProductionJson;
use App\Models\Branch;
use App\Models\Item;
use App\Models\UnitOfMeasure;
use Illuminate\Console\Command;

class ImportItemsFromProductionData extends Command
{
    use ParsesProductionJson;

    protected $signature = 'import:production-items
        {path? : Path to JSON file or directory (defaults to real_data/receips-sweetooth-main)}
        {--branch-id= : Branch UUID to assign items to}
        {--dry-run : Parse only, do not write to database}';

    protected $description = 'Import all ingredients from production JSON files as items';

    protected array $uomCache = [];
    protected array $itemCache = [];

    public function handle(): int
    {
        $path = $this->argument('path') ?: base_path('real_data/receips-sweetooth-main');
        $dryRun = (bool) $this->option('dry-run');
        $branchId = $this->resolveBranchId($this->option('branch-id'));

        if (! $branchId) {
            $this->error('No branch found. Provide --branch-id or create a branch first.');
            return 1;
        }

        $rows = $this->loadJsonRows($path);
        if (empty($rows)) {
            $this->warn('No rows to import.');
            return 0;
        }

        // Extract all unique ingredients from all production records
        $ingredients = $this->extractUniqueIngredients($rows);
        
        $this->info("Found " . count($ingredients) . " unique ingredients to import.");

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar(count($ingredients));
        $bar->start();

        foreach ($ingredients as $ingredient) {
            $name = $ingredient['name'];
            $externalId = $ingredient['external_id'];
            $uomToken = $ingredient['most_common_uom'];
            $avgCost = $ingredient['avg_cost'];

            $item = $this->findOrCreateItem($name, $externalId, $branchId, $uomToken, $avgCost, $dryRun);

            if ($item === 'created') {
                $created++;
            } elseif ($item === 'updated') {
                $updated++;
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Item import completed:");
        $this->info("  Created: {$created}");
        $this->info("  Updated: {$updated}");
        $this->info("  Skipped: {$skipped}");

        return 0;
    }

    protected function extractUniqueIngredients(array $rows): array
    {
        $ingredients = [];

        foreach ($rows as $row) {
            foreach ($row['ingredients'] ?? [] as $ing) {
                [$name, $externalId] = $this->parseNameAndExternalId($ing['ingredient'] ?? '');
                if ($name === '') {
                    continue;
                }

                [$inputQty, $uomToken] = $this->parseQuantityAndUom($ing['input_qty'] ?? '');
                $totalPrice = $this->parseMoney($ing['total_price'] ?? '');
                $unitCost = $inputQty > 0 ? ($totalPrice / $inputQty) : 0;

                $key = $externalId ?: $this->normalizeKey($name);

                if (! isset($ingredients[$key])) {
                    $ingredients[$key] = [
                        'name' => $name,
                        'external_id' => $externalId,
                        'uom_tokens' => [],
                        'costs' => [],
                        'most_common_uom' => null,
                        'avg_cost' => 0,
                    ];
                }

                if ($uomToken) {
                    $ingredients[$key]['uom_tokens'][$uomToken] = 
                        ($ingredients[$key]['uom_tokens'][$uomToken] ?? 0) + 1;
                }

                if ($unitCost > 0) {
                    $ingredients[$key]['costs'][] = $unitCost;
                }
            }
        }

        // Calculate most common UOM and average cost
        foreach ($ingredients as &$ingredient) {
            if (! empty($ingredient['uom_tokens'])) {
                arsort($ingredient['uom_tokens']);
                $ingredient['most_common_uom'] = array_key_first($ingredient['uom_tokens']);
            }

            if (! empty($ingredient['costs'])) {
                $ingredient['avg_cost'] = array_sum($ingredient['costs']) / count($ingredient['costs']);
            }
        }

        return $ingredients;
    }

    protected function findOrCreateItem(
        string $name,
        ?string $externalId,
        string $branchId,
        ?string $uomToken,
        float $avgCost,
        bool $dryRun
    ): string {
        $key = $externalId ?: $this->normalizeKey($name);
        
        // Check cache first
        if (isset($this->itemCache[$key])) {
            return 'skipped';
        }

        // Try to find existing item
        $query = Item::query()->where('branch_id', $branchId);
        if ($externalId) {
            $query->where('sku', $externalId);
        } else {
            $query->whereRaw('LOWER(name) = ?', [strtolower($name)]);
        }
        
        $item = $query->first();

        $uomId = $uomToken ? $this->getUomIdByToken($uomToken) : null;
        if (! $uomId) {
            $uomId = UnitOfMeasure::where('code', 'pcs')->value('id');
        }

        $category = $this->classifyIngredientCategory($name);

        if ($item) {
            // Update existing item if needed
            $needsUpdate = false;
            $updateData = [];

            if (! $item->uom_id && $uomId) {
                $updateData['uom_id'] = $uomId;
                $needsUpdate = true;
            }

            if ($avgCost > 0 && (! $item->unit_price || $item->unit_price == 0)) {
                $updateData['unit_price'] = round($avgCost, 4);
                $updateData['last_unit_price'] = round($avgCost, 4);
                $needsUpdate = true;
            }

            if ($needsUpdate && ! $dryRun) {
                $item->update($updateData);
                $this->itemCache[$key] = $item->id;
                return 'updated';
            }

            $this->itemCache[$key] = $item->id;
            return 'skipped';
        }

        // Create new item
        if (! $dryRun) {
            $item = Item::create([
                'branch_id' => $branchId,
                'name' => $name,
                'sku' => $externalId ?: $this->generateUniqueSku($name, 'ITM'),
                'category' => $category,
                'uom_id' => $uomId,
                'unit_price' => $avgCost > 0 ? round($avgCost, 4) : 0,
                'last_unit_price' => $avgCost > 0 ? round($avgCost, 4) : 0,
                'status' => 'active',
                'requires_request' => true,
            ]);
            $this->itemCache[$key] = $item->id;
            return 'created';
        }

        return 'created';
    }

    protected function getUomIdByToken(string $token): ?int
    {
        if (isset($this->uomCache[$token])) {
            return $this->uomCache[$token];
        }

        $code = $this->uomCodeFromToken($token);
        if (! $code) {
            // Default to 'pcs' for unknown UOMs
            $code = 'pcs';
        }

        $uomId = UnitOfMeasure::where('code', $code)->value('id');
        $this->uomCache[$token] = $uomId;

        return $uomId;
    }

    protected function classifyIngredientCategory(string $name): string
    {
        $name = strtolower($name);

        // Dairy products
        if (preg_match('/(milk|butter|cream|yogurt|cheese|ghee)/i', $name)) {
            return 'dairy';
        }

        // Flour and grains
        if (preg_match('/(flour|wheat|oats|rice|corn|maize|barley|quinoa|pasta|noodle)/i', $name)) {
            return 'grains';
        }

        // Sugar and sweeteners
        if (preg_match('/(sugar|honey|syrup|molasses|stevia|sweetener)/i', $name)) {
            return 'sweeteners';
        }

        // Fruits and vegetables
        if (preg_match('/(apple|banana|orange|lemon|lime|tomato|potato|onion|garlic|carrot|lettuce|spinach|fruit|vegetable|berry|mango|pineapple|papaya|peach|pear|plum|grape)/i', $name)) {
            return 'produce';
        }

        // Meat and protein
        if (preg_match('/(beef|chicken|pork|lamb|fish|salmon|tuna|shrimp|bacon|sausage|ham|turkey|duck|egg)/i', $name)) {
            return 'protein';
        }

        // Spices and seasonings
        if (preg_match('/(salt|pepper|spice|seasoning|herb|cinnamon|vanilla|cumin|paprika|turmeric|oregano|basil|thyme|rosemary)/i', $name)) {
            return 'spices';
        }

        // Oils and fats
        if (preg_match('/(oil|fat|lard|shortening|margarine)/i', $name)) {
            return 'oils_fats';
        }

        // Beverages
        if (preg_match('/(coffee|tea|juice|soda|water|wine|beer|liqueur|baileys)/i', $name)) {
            return 'beverages';
        }

        // Baking ingredients
        if (preg_match('/(yeast|baking.*powder|baking.*soda|gelatin|pectin|cocoa|chocolate)/i', $name)) {
            return 'baking';
        }

        // Packaging
        if (preg_match('/(cup|container|box|bag|pack|wrap|foil|paper|napkin|straw)/i', $name)) {
            return 'packaging';
        }

        // WIP (Work in Progress)
        if (preg_match('/(\bwip\b)/i', $name)) {
            return 'wip';
        }

        return 'other';
    }

    protected function generateUniqueSku(string $name, string $prefix = 'ITM'): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 8));
        if ($base === '') {
            $base = 'ITEM';
        }

        $sku = $prefix . '-' . $base;
        $counter = 1;

        while (Item::where('sku', $sku)->exists()) {
            $sku = $prefix . '-' . $base . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $sku;
    }
}
