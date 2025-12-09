# Recipe Yield Auto-Calculation - Quick Reference

## The Formula
```
Yield = Batch Weight ÷ Unit Weight
```

Example: 1200g batch ÷ 100g unit = 12 units per batch

## Files to Know

| File | Purpose | Key Method |
|------|---------|-----------|
| `/app/Livewire/BranchDashboard/Production/Products.php` | Create/edit products | `calculatedRecipeYield()` |
| `/app/Livewire/BranchDashboard/Production/Recipes/Add.php` | Create recipes | `calculatedYieldQuantity()`, `updatedProductId()` |
| `/resources/views/livewire/branch-dashboard/production/partials/product-modal.blade.php` | Product form UI | Uses `wire:model.live` |
| `/resources/views/livewire/branch-dashboard/production/recipes/add.blade.php` | Recipe form UI | Weight/Volume section |

## Key Methods

### Products Component

**`calculatedRecipeYield()`** - Computed Property
```php
if ($this->recipe_yield_weight && $this->unit_weight && $this->unit_weight > 0) {
    return round($this->recipe_yield_weight / $this->unit_weight, 2);
}
return null;
```

**`updatedRecipeYieldWeight()`** - Watcher
Fires when batch weight changes, updates recipe_yield

**`updatedUnitWeight()`** - Watcher
Fires when unit weight changes, updates recipe_yield

**`validateYieldConsistency()`** - Validation
Enforces 3 rules before save

### Recipe Add Component

**`updatedProductId()`** - Watcher
```php
if ($this->product_id) {
    $product = Product::find($this->product_id);
    if ($product) {
        $this->yield_quantity = $product->recipe_yield ?? 1;
        $this->recipe_yield_weight = $product->recipe_yield_weight;
        $this->unit_weight = $product->unit_weight;
        $this->uom = $product->uom ?? 'grams';
    }
}
```

## UI Interactions

### Product Modal
```html
<input wire:model.live="recipe_yield_weight"> <!-- Triggers auto-calc -->
<input wire:model.live="unit_weight">          <!-- Triggers auto-calc -->
<input wire:model.live="recipe_yield">         <!-- Auto-updates -->
```

### Recipe Form
```html
<input wire:model.live="recipe_yield_weight"> <!-- Triggers auto-calc -->
<input wire:model.live="unit_weight">          <!-- Triggers auto-calc -->
<input wire:model.live="yield_quantity">       <!-- Auto-updates -->
```

## Validation Rules

```php
// Rule 1: If both weights exist, yield must match calculation
if ($this->recipe_yield_weight && $this->unit_weight) {
    $expectedYield = round($this->recipe_yield_weight / $this->unit_weight, 2);
    if (abs((float)$this->recipe_yield - $expectedYield) > 0.01) {
        // Error: Inconsistent yield
    }
}

// Rule 2: Weights must come in pairs
if ($this->recipe_yield_weight && !$this->unit_weight) {
    // Error: Unit weight required
}
if (!$this->recipe_yield_weight && $this->unit_weight) {
    // Error: Batch weight required
}
```

## Testing Scenarios

### ✅ Should Work
- Enter 1200 batch weight + 100 unit weight = 12 yield
- Select product with weights = auto-populate all fields
- Change weights = yield updates instantly
- Save product/recipe with weights = succeeds

### ❌ Should Fail
- Save 1200 batch + 100 unit but yield = 10 (inconsistent)
- Save batch weight without unit weight
- Save unit weight without batch weight

## Common Operations

### Modify Product Yield
1. Go to Products > Edit Product
2. Enter batch and unit weight
3. Watch yield auto-calculate
4. Click Update

### Create Recipe from Product
1. Go to Recipes > Add Recipe
2. Select product in dropdown
3. All yield fields auto-populate
4. Optionally adjust weights (yield auto-updates)
5. Add ingredients and save

### Manual Yield Setup
1. Leave weight fields empty
2. Enter recipe yield manually
3. Saves successfully (no weight validation)

## Debugging Tips

**Issue**: Yield not updating?
- Check `wire:model.live` is used (not `wire:model`)
- Check Livewire component's watcher methods exist
- Check browser console for JavaScript errors

**Issue**: Validation error on save?
- Verify both weights are provided (or neither)
- Verify yield = batch weight ÷ unit weight (±0.01 tolerance)
- Check error message - it will show expected value

**Issue**: Product selection not auto-populating?
- Check `updatedProductId()` method exists
- Verify product has recipe_yield, recipe_yield_weight, unit_weight fields
- Check if product_id is properly bound in form

## Database Schema

Products table columns:
```sql
recipe_yield          DECIMAL(10,2)  -- Units per batch
recipe_yield_weight   DECIMAL(10,2)  -- Total batch weight (nullable)
unit_weight          DECIMAL(10,2)  -- Weight per unit (nullable)
```

## Performance Notes

- Calculated properties are lightweight (simple division)
- Real-time updates use Livewire's `wire:model.live` (minimal network calls)
- Validation runs on client (instant feedback) and server (safety)
- No database queries needed for calculations

## Links

- Full Documentation: `/TXTs/RECIPE_YIELD_AUTO_CALCULATION.md`
- Implementation Summary: `/RECIPE_YIELD_FEATURE_SUMMARY.md`
