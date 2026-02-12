<?php

require_once __DIR__.'/vendor/autoload.php';

// Create a Laravel application instance
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Item;
use App\Models\Stock;

// Get all items with their stock
$items = Item::with(['stocks'])->get();

echo "Total items in system: " . $items->count() . "\n";

$itemsWithStock = 0;
$itemsWithoutStock = 0;

foreach ($items as $item) {
    if ($item->stocks->count() > 0) {
        $itemsWithStock++;
        $available = $item->stocks->sum('quantity_available');
        $reserved = $item->stocks->sum('quantity_reserved');
        $damaged = $item->stocks->sum('quantity_damaged');
        
        echo "Item: {$item->name} (ID: {$item->id})\n";
        echo "  Available: {$available}\n";
        echo "  Reserved: {$reserved}\n";
        echo "  Damaged: {$damaged}\n";
        echo "  Total: " . ($available + $reserved + $damaged) . "\n";
        echo "  Reorder Point: {$item->reorder_point}\n";
        echo "  Min Stock Level: {$item->min_stock_level}\n";
        echo "  Max Stock Level: {$item->max_stock_level}\n";
        echo "---\n";
    } else {
        $itemsWithoutStock++;
    }
}

echo "\nItems with stock: $itemsWithStock\n";
echo "Items without stock: $itemsWithoutStock\n";