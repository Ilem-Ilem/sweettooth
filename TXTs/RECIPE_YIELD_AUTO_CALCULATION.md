# Recipe Yield Auto-Calculation Feature

## Overview
This feature automatically synchronizes recipe yield quantities across product creation and recipe creation forms, with real-time calculation based on batch weight and unit weight inputs.

## Problem Statement
Previously, users had to manually maintain consistency between:
- **Recipe Yield (Units)**: How many units one batch produces
- **Total Batch Weight/Volume (g/ml)**: Total weight of one batch
- **Unit Weight/Volume (g/ml)**: Weight of one unit

This led to potential data inconsistencies where the yield quantity might not match the math: `Yield = Batch Weight ÷ Unit Weight`

## Solution Architecture

### Auto-Calculation Logic
The system now automatically calculates recipe yield using the formula:
```
Recipe Yield = Total Batch Weight ÷ Unit Weight
```

When both batch weight and unit weight are provided, the recipe yield is automatically calculated and updated in real-time.

### Components Modified

#### 1. Products Livewire Component
**File**: `/app/Livewire/BranchDashboard/Production/Products.php`

**Changes**:
- Added `#[Computed]` property `calculatedRecipeYield()` that calculates: `recipe_yield_weight / unit_weight`
- Added watchers `updatedRecipeYieldWeight()` and `updatedUnitWeight()` to auto-update `recipe_yield` when weights change
- Added `validateYieldConsistency()` method that validates:
  - If both weights provided, yield must equal the calculated value (with 0.01 tolerance for rounding)
  - If batch weight provided, unit weight is required
  - If unit weight provided, batch weight is required

**Key Methods**:
```php
#[Computed]
public function calculatedRecipeYield()
```
Returns the auto-calculated yield quantity or null if weights are incomplete.

```php
public function validateYieldConsistency()
```
Validates the relationship between yield, batch weight, and unit weight, preventing inconsistent data.

#### 2. Recipe Add Livewire Component
**File**: `/app/Livewire/BranchDashboard/Production/Recipes/Add.php`

**Changes**:
- Added properties: `$recipe_yield_weight` and `$unit_weight`
- Added `#[Computed]` property `calculatedYieldQuantity()` for the same calculation
- Added `updatedProductId()` watcher that automatically populates recipe fields when a product is selected:
  - Copies `recipe_yield`, `recipe_yield_weight`, `unit_weight`, and `uom` from the selected product
- Added watchers `updatedRecipeYieldWeight()` and `updatedUnitWeight()` for auto-calculation
- Added same `validateYieldConsistency()` validation as Products component

**Key Methods**:
```php
public function updatedProductId()
```
When a product is selected, this automatically populates all recipe yield-related fields from the product definition, ensuring consistency from the start.

### UI Components Modified

#### 1. Product Modal
**File**: `/resources/views/livewire/branch-dashboard/production/partials/product-modal.blade.php`

**Changes**:
- Changed recipe yield input to `wire:model.live="recipe_yield"` for real-time updates
- Changed batch weight input to `wire:model.live="recipe_yield_weight"`
- Changed unit weight input to `wire:model.live="unit_weight"`
- Added informational text indicating auto-calculation
- Highlighted the weight/volume section with blue styling to draw attention
- Made labels **dynamic based on selected UOM** (Unit of Measure):
  - Grams (g): "Weight (g)"
  - Kg: "Weight (kg)"
  - Liters: "Volume (L)"
  - Milliliters: "Volume (ml)"
  - Pieces: "Count (pcs)"
  - Units: "Quantity (units)"

**UI Feedback**:
- Users see "Auto-calculated if batch weight and unit weight are provided" hints
- Real-time calculation shows in the recipe_yield field as users type in weight fields
- Labels adapt to the selected unit of measure, eliminating confusion

#### 2. Recipe Add Form
**File**: `/resources/views/livewire/branch-dashboard/production/recipes/add.blade.php`

**Changes**:
- Added weight/volume section with blue background highlighting
- Batch weight and unit weight fields use `wire:model.live` for instant calculation
- Yield quantity field shows "Auto-calculated from batch weight ÷ unit weight" hint
- Users can select a product, which auto-populates all yield-related fields
- Made labels **dynamic based on selected UOM** (same as Product Modal)
  - Adapts to Grams, Kg, Liters, Ml, Pieces, or Units
  - Labels update when UOM selection changes

## Usage Flow

### Creating a Product
1. User enters basic product information
2. User enters **Total Batch Weight** (e.g., 1200g)
3. User enters **Unit Weight** (e.g., 100g)
4. System automatically calculates **Recipe Yield**: 1200 ÷ 100 = 12 units
5. User can adjust recipe yield manually if needed (validation will alert if inconsistent with weights)

### Creating a Recipe
1. User selects a product name
2. If the product exists, fields are auto-populated:
   - Yield Quantity (from product's recipe_yield)
   - Total Batch Weight
   - Unit Weight
   - Unit of Measure
3. User can adjust weights, and yield quantity auto-updates
4. User adds ingredients and saves
5. System validates consistency before saving

## Validation Rules

The system enforces these validation rules:

### Rule 1: Both Weights Present
If both batch weight AND unit weight are provided:
- Recipe yield MUST equal: batch weight ÷ unit weight
- Tolerance: ±0.01 for rounding differences
- Error message guides user to the expected value

### Rule 2: Incomplete Weight Pairs
- If batch weight is provided → unit weight is required
- If unit weight is provided → batch weight is required
- System prevents partial weight data

### Rule 3: Manual Yield Override
- Users can set recipe yield without weights
- Users can set recipe yield and weights independently
- Validation only enforces consistency if BOTH weights are present

## Data Integrity

### Database Schema
The products table stores:
- `recipe_yield` - Number of units per batch
- `recipe_yield_weight` - Total weight of one batch (nullable)
- `unit_weight` - Weight of one unit (nullable)

These fields work together to define precise production specifications.

### Consistency Guarantee
When both weight fields are populated, the recipe_yield is guaranteed to match the calculation (within rounding tolerance) through:
1. Real-time auto-calculation in the UI
2. Server-side validation before saving
3. Computed properties that always return current calculated value

## Dynamic Unit Labels Feature

A key improvement to the system ensures that field labels adapt to the selected **Unit of Measure** (UOM), eliminating confusion:

### Problem Solved
Previously, labels showed fixed "(g/ml)" notation, which was misleading for:
- Solid products measured in grams or pieces
- Liquid products measured in liters or milliliters
- Any product type not involving weight/volume measurements

### Solution Implemented
Labels now dynamically update based on the selected UOM:

| UOM Selected | Label Shows |
|--------------|-------------|
| Grams | "Weight (g)" |
| Kilograms | "Weight (kg)" |
| Liters | "Volume (L)" |
| Milliliters | "Volume (ml)" |
| Pieces | "Count (pcs)" |
| Units | "Quantity (units)" |

**Examples**:
- Creating chocolate cake (grams): "Total Batch Weight (g)" and "Unit Weight (g)"
- Creating liquid gelato base (ml): "Total Batch Volume (ml)" and "Unit Volume (ml)"
- Creating pastry (pieces): "Total Batch Count (pcs)" and "Unit Count (pcs)"

This removes ambiguity and makes the form user-friendly for all product types.

## Benefits

1. **Data Consistency**: Eliminates manual entry errors
2. **Real-Time Feedback**: Users see calculations immediately
3. **Flexible Input**: Users can provide either weights or yields
4. **Product Synchronization**: Recipes automatically inherit product specifications
5. **Validation Safety**: System prevents inconsistent data from being saved
6. **User Guidance**: Clear hints and error messages explain the auto-calculation
7. **Adaptive Labels**: Field labels dynamically reflect the selected unit of measure

## Examples

### Example 1: Chocolate Cake
- Total Batch Weight: 2400g
- Unit Weight: 200g (individual serving)
- Auto-Calculated Yield: 12 units per batch

### Example 2: Gelato Base
- Total Batch Weight: 5000ml
- Unit Weight: 100ml (per serving scoop)
- Auto-Calculated Yield: 50 units per batch

### Example 3: Pastry Dough
- Total Batch Weight: 1000g
- Unit Weight: 50g (per pastry)
- Auto-Calculated Yield: 20 units per batch

## Testing Checklist

- [ ] Create product with batch and unit weights → yields auto-calculated
- [ ] Edit product and change weights → yield updates in real-time
- [ ] Create recipe and select product → all fields auto-populate
- [ ] Change weights in recipe → yield auto-updates
- [ ] Save product with inconsistent weights → validation error appears
- [ ] Try to save with only one weight → validation error appears
- [ ] Manually set yield without weights → saves successfully
- [ ] View product/recipe edit modal → all fields show correct values

## Future Enhancements

1. **Batch Splitting**: Auto-calculate when dividing large batches
2. **Yield History**: Track yield changes over time
3. **Waste Analysis**: Incorporate yield_percentage in auto-calculations
4. **Production Reports**: Show efficiency metrics based on weights
5. **Bulk Import**: Support CSV import with weight validation
