<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class ProductService
{
    /**
     * Get all products for the current tenant
     */
    public function getProductsForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return Product::with(['brand', 'category'])
                ->where('tenant_id', $tenantId)
                ->orderBy('product_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching products for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get products query for DataTables
     */
    public function getProductsQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return Product::with(['brand', 'category'])
                ->where('tenant_id', $tenantId);
        } catch (Exception $e) {
            Log::error('Error creating products query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get brands for dropdown
     */
    public function getBrandsForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return Brand::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('brand_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching brands for dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get categories for dropdown
     */
    public function getCategoriesForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return ProductCategory::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('category_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching categories for dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new product
     */
    public function createProduct(array $data): Product
    {
        try {
            $tenantId = $this->getTenantId();
            
            // Validate brand belongs to tenant
            if (isset($data['brand_id'])) {
                $this->validateBrand($data['brand_id'], $tenantId);
            }

            // Validate category belongs to tenant
            if (isset($data['category_id'])) {
                $this->validateCategory($data['category_id'], $tenantId);
            }

            $product = Product::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'product_type' => $data['product_type'] ?? 'MOBILE',
                'current_stock' => $data['current_stock'] ?? 0,
                'is_active' => true,
            ]));

            Log::info('Product created successfully', [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'tenant_id' => $tenantId
            ]);

            return $product;
        } catch (Exception $e) {
            Log::error('Error creating product', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        try {
            // Validate brand belongs to tenant if changed
            if (isset($data['brand_id']) && $data['brand_id'] != $product->brand_id) {
                $this->validateBrand($data['brand_id'], $product->tenant_id);
            }

            // Validate category belongs to tenant if changed
            if (isset($data['category_id']) && $data['category_id'] != $product->category_id) {
                $this->validateCategory($data['category_id'], $product->tenant_id);
            }

            $product->update($data);

            Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'product_name' => $product->product_name
            ]);

            return $product->fresh();
        } catch (Exception $e) {
            Log::error('Error updating product', [
                'product_id' => $product->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a product
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            // Check if product has stock
            if ($product->current_stock > 0) {
                throw new Exception('Cannot delete product with existing stock. Please adjust stock to zero first.');
            }

            $productId = $product->id;
            $productName = $product->product_name;
            
            $deleted = $product->delete();

            if ($deleted) {
                Log::info('Product deleted successfully', [
                    'product_id' => $productId,
                    'product_name' => $productName
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting product', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Validate brand belongs to tenant
     */
    private function validateBrand(int $brandId, int $tenantId): void
    {
        $brand = Brand::where('id', $brandId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (!$brand) {
            throw new Exception('Brand not found or does not belong to your tenant.');
        }
    }

    /**
     * Validate category belongs to tenant
     */
    private function validateCategory(int $categoryId, int $tenantId): void
    {
        $category = ProductCategory::where('id', $categoryId)
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            throw new Exception('Category not found or does not belong to your tenant.');
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

