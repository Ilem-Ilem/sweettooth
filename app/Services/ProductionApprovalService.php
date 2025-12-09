<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\ApprovalAuditRequest;
use Illuminate\Support\Facades\DB;

class ProductionApprovalService
{
    /**
     * Execute approved product creation
     */
    public static function executeProductCreation(ApprovalAuditRequest $request): ?Product
    {
        $payload = $request->payload;

        try {
            $product = Product::create([
                'name' => $payload['name'],
                'sku' => strtoupper($payload['sku']),
                'product_type_id' => $payload['product_type_id'],
                'category_id' => $payload['category_id'] ?? null,
                'description' => $payload['description'] ?? null,
                'price' => $payload['price'],
                'cost' => $payload['cost'] ?? null,
                'shelf_life_days' => $payload['shelf_life_days'],
                'uom' => $payload['uom'],
                'is_active' => $payload['is_active'] ?? true,
                'is_available' => $payload['is_available'] ?? true,
                'image_url' => $payload['image_url'] ?? null,
                'allergens' => $payload['allergens'] ?? [],
                'tags' => $payload['tags'] ?? [],
            ]);

            return $product;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create product: " . $e->getMessage());
        }
    }

    /**
     * Execute approved product update
     */
    public static function executeProductUpdate(ApprovalAuditRequest $request): ?Product
    {
        $payload = $request->payload;
        $productId = $payload['id'] ?? null;

        if (!$productId) {
            throw new \Exception("Product ID not found in approval request");
        }

        try {
            $product = Product::findOrFail($productId);

            $product->update([
                'name' => $payload['name'],
                'sku' => strtoupper($payload['sku']),
                'product_type_id' => $payload['product_type_id'],
                'category_id' => $payload['category_id'] ?? null,
                'description' => $payload['description'] ?? null,
                'price' => $payload['price'],
                'cost' => $payload['cost'] ?? null,
                'shelf_life_days' => $payload['shelf_life_days'],
                'uom' => $payload['uom'],
                'is_active' => $payload['is_active'] ?? true,
                'is_available' => $payload['is_available'] ?? true,
                'image_url' => $payload['image_url'] ?? null,
                'allergens' => $payload['allergens'] ?? [],
                'tags' => $payload['tags'] ?? [],
            ]);

            return $product;
        } catch (\Exception $e) {
            throw new \Exception("Failed to update product: " . $e->getMessage());
        }
    }

    /**
     * Execute approved product deletion
     */
    public static function executeProductDeletion(ApprovalAuditRequest $request): ?Product
    {
        $productId = $request->payload['id'] ?? null;

        if (!$productId) {
            throw new \Exception("Product ID not found in approval request");
        }

        try {
            $product = Product::findOrFail($productId);
            $product->delete();
            return $product;
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete product: " . $e->getMessage());
        }
    }

    /**
     * Execute approved bulk product deletion
     */
    public static function executeProductBulkDeletion(ApprovalAuditRequest $request): array
    {
        $ids = $request->payload['ids'] ?? [];

        if (empty($ids)) {
            throw new \Exception("No product IDs found in approval request");
        }

        try {
            $deleted = [];
            foreach ($ids as $id) {
                $product = Product::find($id);
                if ($product) {
                    $deleted[] = $product->id;
                    $product->delete();
                }
            }
            return $deleted;
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete products: " . $e->getMessage());
        }
    }

    /**
     * Execute approved recipe creation
     */
    public static function executeRecipeCreation(ApprovalAuditRequest $request): ?Recipe
    {
        $payload = $request->payload;
        $actor = $request->requester;  // Use property, not method

        if (!$actor) {
            throw new \Exception("Requester not found for recipe creation");
        }

        try {
            $branchId = $payload['branch_id'] ?? null;

            $recipe = DB::transaction(function () use ($payload, $actor, $branchId) {
                $recipe = Recipe::create([
                    'branch_id' => $branchId,
                    'product_id' => $payload['product_id'] ?? null,
                    'product_name' => $payload['product_name'],
                    'sku' => strtoupper($payload['sku']),
                    'department_id' => $payload['department_id'],
                    'product_type' => $payload['product_type'],
                    'cost_per_unit' => $payload['cost_per_unit'],
                    'uom' => $payload['uom'],
                    'yield_quantity' => $payload['yield_quantity'],
                    'preparation_time' => $payload['preparation_time'] ?? null,
                    'instructions' => $payload['instructions'] ?? null,
                    'status' => $payload['status'] ?? 'active',
                    'created_by_id' => $actor->id,
                    'created_by_type' => get_class($actor),
                ]);

                // Save ingredients
                if (!empty($payload['ingredients']) && is_array($payload['ingredients'])) {
                    foreach ($payload['ingredients'] as $index => $ingredient) {
                        if (!empty($ingredient['item_id'])) {
                            RecipeIngredient::create([
                                'recipe_id' => $recipe->id,
                                'item_id' => $ingredient['item_id'],
                                'quantity' => $ingredient['quantity'],
                                'uom' => $ingredient['uom'],
                                'cost_per_unit' => $ingredient['cost_per_unit'] ?? 0,
                                'waste_percentage' => $ingredient['waste_percentage'] ?? 0,
                                'sort_order' => $index + 1,
                                'notes' => $ingredient['notes'] ?? null,
                                'preparation_notes' => $ingredient['preparation_notes'] ?? null,
                            ]);
                        }
                    }
                }

                // Log recipe creation
                ProductionAuditService::logRecipeCreated($actor, $recipe, $payload['ingredients'] ?? []);

                return $recipe;
            });

            return $recipe;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create recipe: " . $e->getMessage());
        }
    }

    /**
     * Execute approved recipe update
     */
    public static function executeRecipeUpdate(ApprovalAuditRequest $request): ?Recipe
    {
        $payload = $request->payload;
        $recipeId = $payload['id'] ?? null;
        $actor = $request->requester;  // Use property, not method

        if (!$recipeId) {
            throw new \Exception("Recipe ID not found in approval request");
        }

        if (!$actor) {
            throw new \Exception("Requester not found for recipe update");
        }

        try {
            $recipe = Recipe::findOrFail($recipeId);

            DB::transaction(function () use ($recipe, $payload, $actor) {
                // Update basic recipe fields
                $recipe->update([
                    'product_id' => $payload['product_id'] ?? null,
                    'product_name' => $payload['product_name'],
                    'sku' => strtoupper($payload['sku']),
                    'department_id' => $payload['department_id'],
                    'product_type' => $payload['product_type'],
                    'cost_per_unit' => $payload['cost_per_unit'],
                    'uom' => $payload['uom'],
                    'yield_quantity' => $payload['yield_quantity'],
                    'recipe_yield_weight' => $payload['recipe_yield_weight'] ?? null,
                    'unit_weight' => $payload['unit_weight'] ?? null,
                    'preparation_time' => $payload['preparation_time'] ?? null,
                    'instructions' => $payload['instructions'] ?? null,
                    'status' => $payload['status'] ?? 'active',
                ]);

                // Update ingredients - delete old ones and create new ones
                if (!empty($payload['ingredients']) && is_array($payload['ingredients'])) {
                    $recipe->ingredients()->delete();
                    
                    foreach ($payload['ingredients'] as $index => $ingredient) {
                        if (!empty($ingredient['item_id'])) {
                            // Skip 'id' field if present in payload from edit form
                            $ingredientData = [
                                'recipe_id' => $recipe->id,
                                'item_id' => $ingredient['item_id'],
                                'quantity' => $ingredient['quantity'],
                                'uom' => $ingredient['uom'],
                                'cost_per_unit' => $ingredient['cost_per_unit'] ?? 0,
                                'waste_percentage' => $ingredient['waste_percentage'] ?? 0,
                                'sort_order' => $index + 1,
                                'notes' => $ingredient['notes'] ?? null,
                                'preparation_notes' => $ingredient['preparation_notes'] ?? null,
                            ];
                            RecipeIngredient::create($ingredientData);
                        }
                    }
                }

                // Log recipe update
                ProductionAuditService::logRecipeUpdated($actor, $recipe, $payload['ingredients'] ?? []);
            });

            return $recipe;
        } catch (\Exception $e) {
            throw new \Exception("Failed to update recipe: " . $e->getMessage());
        }
    }

    /**
     * Execute approved recipe deletion
     */
    public static function executeRecipeDeletion(ApprovalAuditRequest $request): ?Recipe
    {
        $recipeId = $request->payload['id'] ?? null;
        $actor = $request->requester;  // Use property, not method

        if (!$recipeId) {
            throw new \Exception("Recipe ID not found in approval request");
        }

        try {
            $recipe = Recipe::findOrFail($recipeId);

            DB::transaction(function () use ($recipe, $actor) {
                // Log deletion before deleting
                if ($actor) {
                    ProductionAuditService::logRecipeDeleted(
                        $actor,
                        $recipe,
                        'Approved deletion request'
                    );
                }

                // Delete ingredients and recipe
                $recipe->ingredients()->delete();
                $recipe->delete();
            });

            return $recipe;
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete recipe: " . $e->getMessage());
        }
    }

    /**
     * Execute approved bulk recipe deletion
     */
    public static function executeRecipeBulkDeletion(ApprovalAuditRequest $request): array
    {
        $ids = $request->payload['ids'] ?? [];
        $actor = $request->requester;

        if (empty($ids)) {
            throw new \Exception("No recipe IDs found in approval request");
        }

        try {
            $deleted = [];
            foreach ($ids as $id) {
                $recipe = Recipe::find($id);
                if ($recipe) {
                    DB::transaction(function () use ($recipe, $actor) {
                        // Log deletion before deleting
                        if ($actor) {
                            ProductionAuditService::logRecipeDeleted(
                                $actor,
                                $recipe,
                                'Approved bulk deletion request'
                            );
                        }

                        // Delete ingredients and recipe
                        $recipe->ingredients()->delete();
                        $recipe->delete();
                    });

                    $deleted[] = $id;
                }
            }
            return $deleted;
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete recipes: " . $e->getMessage());
        }
    }

    /**
     * Create approval request for product creation
     */
    public static function requestProductCreation($requester, array $productData, string $reason): ApprovalAuditRequest
    {
        return ApprovalAuditRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'action' => 'product:create',
            'description' => $reason,
            'payload' => $productData,
            'status' => 'pending',
        ]);
    }

    /**
     * Create approval request for product update
     */
    public static function requestProductUpdate($requester, array $productData, string $reason): ApprovalAuditRequest
    {
        return ApprovalAuditRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'action' => 'product:edit',
            'description' => $reason,
            'payload' => $productData,
            'status' => 'pending',
        ]);
    }

    /**
     * Create approval request for product deletion
     */
    public static function requestProductDeletion($requester, int $productId, string $reason): ApprovalAuditRequest
    {
        return ApprovalAuditRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'action' => 'product:delete',
            'description' => $reason,
            'payload' => ['id' => $productId],
            'status' => 'pending',
        ]);
    }

    /**
     * Create approval request for recipe creation
     */
    public static function requestRecipeCreation($requester, array $recipeData, string $reason): ApprovalAuditRequest
    {
        return ApprovalAuditRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'action' => 'recipe:create_recipe',
            'description' => $reason,
            'payload' => $recipeData,
            'status' => 'pending',
        ]);
    }

    /**
     * Create approval request for recipe deletion
     */
    public static function requestRecipeDeletion($requester, int $recipeId, string $reason): ApprovalAuditRequest
    {
        return ApprovalAuditRequest::create([
            'requester_id' => $requester->id,
            'requester_type' => get_class($requester),
            'action' => 'recipe:delete_recipe',
            'description' => $reason,
            'payload' => ['id' => $recipeId],
            'status' => 'pending',
        ]);
    }
}
