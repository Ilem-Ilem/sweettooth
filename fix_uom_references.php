<?php
/**
 * Script to fix all UOM references in the codebase
 * Replaces ->uom with appropriate relationship or accessor
 */

$dir = __DIR__ . '/app';
$files = [];

// Recursively find all PHP files
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[] = $file->getPathname();
    }
}

echo "Found " . count($files) . " PHP files\n";

$changes = 0;
$patterns = [
    // Pattern 1: item->uom -> item->unitOfMeasure?->symbol
    '/\$([a-zA-Z_][a-zA-Z0-9_]*)->item->uom\b/' => '$\1->item->unitOfMeasure?->symbol',
    // Pattern 2: item->uom => item->unitOfMeasure?->symbol (in arrays)
    "/'uom' => \\\$([a-zA-Z_][a-zA-Z0-9_]*)->item->uom/" => "'uom' => \$\1->item->unitOfMeasure?->symbol",
    // Pattern 3: product->uom -> product->unitOfMeasure?->symbol
    '/\$([a-zA-Z_][a-zA-Z0-9_]*)->product->uom\b/' => '$\1->product->unitOfMeasure?->symbol',
    // Pattern 4: recipe->uom -> recipe->unitOfMeasure?->symbol
    '/\$([a-zA-Z_][a-zA-Z0-9_]*)->recipe->uom\b/' => '$\1->recipe->unitOfMeasure?->symbol',
    // Pattern 5: ingredient->uom -> ingredient->unitOfMeasure?->symbol
    '/\$([a-zA-Z_][a-zA-Z0-9_]*)->uom\b/' => '$\1->unitOfMeasure?->symbol',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    $originalContent = $content;

    // Check if file contains uom references
    if (strpos($content, '->uom') === false) {
        continue;
    }

    echo "\nProcessing: " . str_replace(__DIR__, '', $file) . "\n";

    // Manual pattern matching because we need context-aware replacement
    $lines = explode("\n", $content);
    $newLines = [];

    foreach ($lines as $line) {
        $originalLine = $line;

        // Skip migrations, model definitions with fillable arrays, and comments
        if (strpos($line, 'migration') !== false || 
            strpos($line, '@param') !== false ||
            strpos($line, '//') === 0) {
            $newLines[] = $line;
            continue;
        }

        // Replace various patterns
        $line = preg_replace_callback(
            '/(\$[a-zA-Z_][a-zA-Z0-9_]*)->item->uom\b/',
            function($matches) {
                return $matches[1] . '->item->unitOfMeasure?->symbol';
            },
            $line
        );

        $line = preg_replace_callback(
            '/(\$[a-zA-Z_][a-zA-Z0-9_]*)->product->uom\b/',
            function($matches) {
                return $matches[1] . '->product->unitOfMeasure?->symbol';
            },
            $line
        );

        $line = preg_replace_callback(
            '/(\$[a-zA-Z_][a-zA-Z0-9_]*)->recipe->uom\b/',
            function($matches) {
                return $matches[1] . '->recipe->unitOfMeasure?->symbol';
            },
            $line
        );

        // Handle ->uom without nested relationship (direct property access on model instances)
        // But skip if it's part of ->uom_id or fillable arrays
        if (strpos($line, '->uom_id') === false && 
            strpos($line, "'uom'") === false && 
            strpos($line, '"uom"') === false &&
            strpos($line, '$this->uom') === false &&
            strpos($line, 'uom =>') === false) {
            
            $line = preg_replace_callback(
                '/(\$[a-zA-Z_][a-zA-Z0-9_]*)->uom\b(?!_)/',
                function($matches) {
                    return $matches[1] . '->unitOfMeasure?->symbol';
                },
                $line
            );
        }

        if ($line !== $originalLine) {
            echo "  Changed: " . trim($originalLine) . "\n";
            echo "       to: " . trim($line) . "\n";
            $changes++;
        }

        $newLines[] = $line;
    }

    // Write back if changes were made
    if ($content !== implode("\n", $newLines)) {
        file_put_contents($file, implode("\n", $newLines));
        echo "  ✓ File updated\n";
    }
}

echo "\n\nTotal pattern replacements made: $changes\n";
echo "\nIMPORTANT: Please review changes manually, especially in Livewire components and Services\n";
echo "Many files still need fixes for array assignments like \$this->uom = ...\n";
?>
