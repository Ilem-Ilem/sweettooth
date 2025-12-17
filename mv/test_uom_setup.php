#!/usr/bin/env php
<?php
/**
 * Quick verification script for UOM setup
 * Run with: php test_uom_setup.php
 */

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use App\Models\UnitOfMeasure;
use App\Models\Item;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;

echo "=== UOM Setup Verification ===\n\n";

// Check 1: UnitOfMeasure model
echo "✓ Check 1: UnitOfMeasure model\n";
try {
    $count = UnitOfMeasure::count();
    echo "  - Model loaded successfully\n";
    echo "  - Units in database: $count\n";
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Check 2: Table existence
echo "\n✓ Check 2: Table structure\n";
$tables = [
    'units_of_measure' => ['code', 'name', 'symbol', 'category', 'sort_order', 'is_active'],
    'items' => ['uom_id'],
    'products' => ['uom_id'],
    'recipes' => ['uom_id'],
    'recipe_ingredients' => ['uom_id'],
];

foreach ($tables as $table => $columns) {
    if (Schema::hasTable($table)) {
        echo "  - Table '$table' exists\n";
        foreach ($columns as $col) {
            if (Schema::hasColumn($table, $col)) {
                echo "    ✓ Column '$col' exists\n";
            } else {
                echo "    ✗ Column '$col' missing\n";
            }
        }
    } else {
        echo "  ✗ Table '$table' does not exist\n";
    }
}

// Check 3: Model relationships
echo "\n✓ Check 3: Model relationships\n";
try {
    // Just check the methods exist
    $methods = [
        Item::class => 'unitOfMeasure',
        Product::class => 'unitOfMeasure',
        Recipe::class => 'unitOfMeasure',
        RecipeIngredient::class => 'unitOfMeasure',
    ];
    
    foreach ($methods as $modelClass => $method) {
        if (method_exists($modelClass, $method)) {
            echo "  ✓ $modelClass::$method() exists\n";
        } else {
            echo "  ✗ $modelClass::$method() missing\n";
        }
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
}

// Check 4: Fillable attributes
echo "\n✓ Check 4: Model fillable attributes\n";
$fillables = [
    'Item' => ['uom_id'],
    'Product' => ['uom_id'],
    'Recipe' => ['uom_id'],
    'RecipeIngredient' => ['uom_id'],
];

foreach ($fillables as $model => $attrs) {
    $modelClass = "App\\Models\\$model";
    $instance = new $modelClass();
    foreach ($attrs as $attr) {
        if (in_array($attr, $instance->getFillable())) {
            echo "  ✓ $model has '$attr' in fillable\n";
        } else {
            echo "  ✗ $model missing '$attr' in fillable\n";
        }
    }
}

echo "\n=== Verification Complete ===\n";
?>
