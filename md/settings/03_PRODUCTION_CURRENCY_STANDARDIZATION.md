# 03_PRODUCTION_CURRENCY_STANDARDIZATION.md

## Production System Currency Standardization

### Current Issues in Production Components

#### 1. Production Recipes (`app/Livewire/BranchDashboard/Production/Recipes.php`)
**Problem Areas:**
- Recipe cost calculations lack currency formatting
- Ingredient pricing not currency-aware
- Cost analysis displays without currency symbols
- Production yield calculations don't account for currency

**Required Changes:**
```php
// Add currency context to recipe costs
private function calculateRecipeCost(int $recipeId): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $multiCurrency = Settings::currencyLocalization('multi_currency', 'disabled') === 'enabled';
    
    $baseCost = $this->calculateIngredientCosts($recipeId);
    $laborCost = $this->calculateLaborCosts($recipeId);
    $overheadCost = $this->calculateOverheadCosts($recipeId);
    
    return [
        'ingredient_cost' => $this->formatCurrency($baseCost),
        'labor_cost' => $this->formatCurrency($laborCost),
        'overhead_cost' => $this->formatCurrency($overheadCost),
        'total_cost' => $this->formatCurrency($baseCost + $laborCost + $overheadCost),
        'currency' => $currency,
        'cost_per_unit' => $this->formatCurrency($this->calculateCostPerUnit($recipeId))
    ];
}

// Currency conversion for ingredient pricing
private function getIngredientCostInBaseCurrency(Ingredient $ingredient): float
{
    $ingredientCurrency = $ingredient->currency ?? 'NGN';
    $baseCurrency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    if ($ingredientCurrency === $baseCurrency) {
        return $ingredient->cost;
    }
    
    return (new MultiCurrencyService())->convert($ingredient->cost, $ingredientCurrency, $baseCurrency);
}
```

#### 2. Production Daily Production (`app/Livewire/BranchDashboard/Production/DailyProduce/Index.php`)
**Problem Areas:**
- Production value calculations not currency-formatted
- Waste cost calculations missing currency context
- Production efficiency metrics not currency-aware

**Required Changes:**
```php
// Currency-aware production metrics
private function calculateProductionMetrics(): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'total_production_value' => $this->formatCurrency($totalValue),
        'total_waste_cost' => $this->formatCurrency($wasteCost),
        'production_efficiency' => $efficiency . '%',
        'cost_variance' => $this->formatCurrency($costVariance),
        'currency_symbol' => $this->getCurrencySymbol($currency),
        'labor_cost_per_unit' => $this->formatCurrency($laborCostPerUnit)
    ];
}

// Waste calculation with currency conversion
private function calculateWasteCost($productionItems): float
{
    $totalWasteCost = 0;
    foreach ($productionItems as $item) {
        $wasteCost = $this->calculateItemWasteCost($item);
        $totalWasteCost += $wasteCost;
    }
    return $totalWasteCost;
}
```

#### 3. Production Raw Material Tracking (`app/Livewire/BranchDashboard/Production/RawMaterialTracking.php`)
**Problem Areas:**
- Material value displays lack currency symbols
- Usage cost calculations not currency-aware
- Inventory value tracking missing currency context

**Required Changes:**
```php
// Material valuation with currency
private function getMaterialValuation($material): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    $unitCost = $this->getMaterialCostInBaseCurrency($material);
    $totalValue = $unitCost * $material->current_stock;
    
    return [
        'material_name' => $material->name,
        'current_stock' => $material->current_stock,
        'unit_cost' => $this->formatCurrency($unitCost),
        'total_value' => $this->formatCurrency($totalValue),
        'currency' => $currency,
        'last_updated' => $material->updated_at->format($this->getDateFormat())
    ];
}

// Cost trend analysis with currency context
private function generateCostTrendAnalysis(): array
{
    return [
        'currency' => Settings::currencyLocalization('primary_currency', 'NGN'),
        'trend_data' => $this->getHistoricalCostData(),
        'forecast_costs' => $this->forecastMaterialCosts(),
        'currency_impact' => $this->calculateCurrencyImpactOnCosts()
    ];
}
```

#### 4. Production Cost Analysis (`app/Livewire/BranchDashboard/Production/Reports/CostAnalysis/Index.php`)
**Problem Areas:**
- Cost analysis reports not currency-formatted
- Comparison metrics lack currency symbols
- Efficiency calculations missing monetary context

**Required Changes:**
```php
// Comprehensive cost analysis with currency
private function generateDetailedCostAnalysis(): array
{
    $currency = Settings::currencyLocalization('primary_currency', 'NGN');
    
    return [
        'production_costs' => [
            'direct_materials' => $this->formatCurrency($directMaterials),
            'direct_labor' => $this->formatCurrency($directLabor),
            'manufacturing_overhead' => $this->formatCurrency($overhead),
            'total_production_cost' => $this->formatCurrency($totalCost)
        ],
        'cost_per_unit' => $this->formatCurrency($costPerUnit),
        'currency_symbol' => $this->getCurrencySymbol($currency),
        'cost_variance_analysis' => [
            'standard_cost' => $this->formatCurrency($standardCost),
            'actual_cost' => $this->formatCurrency($actualCost),
            'variance_amount' => $this->formatCurrency(abs($variance)),
            'variance_percentage' => abs($variance / $standardCost * 100) . '%'
        ]
    ];
}
```

### Database Schema Updates Needed
- `recipes` table: Add currency columns for cost calculations
- `ingredients` table: Track currency for each ingredient
- `production_batches`: Include currency context for production costs
- `waste_records`: Currency-aware waste cost tracking

### Testing Requirements
- Test recipe cost calculations with currency switching
- Verify production efficiency metrics currency conversion
- Test waste cost calculations across different currencies
- Validate raw material valuation currency consistency