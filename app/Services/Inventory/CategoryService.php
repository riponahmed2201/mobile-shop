<?php

namespace App\Services\Inventory;

use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class CategoryService
{
    /**
     * Get all categories for the current tenant
     */
    public function getCategoriesForTenant(?int $tenantId = null, ?int $excludeId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            $query = ProductCategory::where('tenant_id', $tenantId);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            return $query->orderBy('category_name')->get();
        } catch (Exception $e) {
            Log::error('Error fetching categories for tenant', [
                'tenant_id' => $tenantId,
                'exclude_id' => $excludeId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get categories query for DataTables
     */
    public function getCategoriesQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return ProductCategory::where('tenant_id', $tenantId);
        } catch (Exception $e) {
            Log::error('Error creating categories query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data): ProductCategory
    {
        try {
            $tenantId = $this->getTenantId();
            
            // Validate parent category belongs to same tenant if provided
            if (isset($data['parent_category_id']) && $data['parent_category_id']) {
                $this->validateParentCategory($data['parent_category_id'], $tenantId);
            }

            $category = ProductCategory::create([
                'tenant_id' => $tenantId,
                'category_name' => $data['category_name'],
                'category_type' => $data['category_type'],
                'parent_category_id' => $data['parent_category_id'] ?? null,
                'is_active' => true,
            ]);

            Log::info('Category created successfully', [
                'category_id' => $category->id,
                'category_name' => $category->category_name,
                'tenant_id' => $tenantId
            ]);

            return $category;
        } catch (Exception $e) {
            Log::error('Error creating category', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing category
     */
    public function updateCategory(ProductCategory $category, array $data): ProductCategory
    {
        try {
            // Validate parent category belongs to same tenant if provided
            if (isset($data['parent_category_id']) && $data['parent_category_id']) {
                $this->validateParentCategory($data['parent_category_id'], $category->tenant_id, $category->id);
            }

            // Prevent setting category as its own parent
            if (isset($data['parent_category_id']) && $data['parent_category_id'] == $category->id) {
                throw new Exception('A category cannot be its own parent.');
            }

            $category->update($data);

            Log::info('Category updated successfully', [
                'category_id' => $category->id,
                'category_name' => $category->category_name
            ]);

            return $category->fresh();
        } catch (Exception $e) {
            Log::error('Error updating category', [
                'category_id' => $category->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a category
     */
    public function deleteCategory(ProductCategory $category): bool
    {
        try {
            // Check if category has children
            if ($category->children()->count() > 0) {
                throw new Exception('Cannot delete category with child categories.');
            }

            // Check if category has products
            $productsCount = \App\Models\Inventory\Product::where('category_id', $category->id)->count();
            if ($productsCount > 0) {
                throw new Exception('Cannot delete category with associated products.');
            }

            $categoryId = $category->id;
            $categoryName = $category->category_name;
            
            $deleted = $category->delete();

            if ($deleted) {
                Log::info('Category deleted successfully', [
                    'category_id' => $categoryId,
                    'category_name' => $categoryName
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting category', [
                'category_id' => $category->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate parent category belongs to same tenant
     */
    private function validateParentCategory(int $parentId, int $tenantId, ?int $excludeId = null): void
    {
        $parent = ProductCategory::where('id', $parentId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$parent) {
            throw new Exception('Parent category not found or does not belong to your tenant.');
        }

        if ($excludeId && $parent->id == $excludeId) {
            throw new Exception('A category cannot be its own parent.');
        }
    }

    /**
     * Get current tenant ID
     */
    private function getTenantId(): int
    {
        if (auth()->check()) {
            return auth()->user()->tenant_id;
        }
        
        // Fallback for testing if no auth
        return 1;
    }
}

