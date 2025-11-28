<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
    public function createProduct(array $data, ?UploadedFile $image = null): Product
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

            $imagePath = null;
            if ($image) {
                $imagePath = $this->storeImage($image);
            }

            // Handle specifications JSON
            $specifications = null;
            if (isset($data['specifications']) && !empty(trim($data['specifications']))) {
                if (is_string($data['specifications'])) {
                    $decoded = json_decode($data['specifications'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $specifications = $decoded;
                    }
                } else {
                    $specifications = $data['specifications'];
                }
            }

            $product = Product::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'product_type' => $data['product_type'] ?? 'MOBILE',
                'current_stock' => $data['current_stock'] ?? 0,
                'min_stock_level' => $data['min_stock_level'] ?? 5,
                'reorder_level' => $data['reorder_level'] ?? 10,
                'unit' => $data['unit'] ?? 'PCS',
                'warranty_period' => $data['warranty_period'] ?? 0,
                'product_image_url' => $imagePath,
                'specifications' => $specifications,
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
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
    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
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

            $updateData = $data;

            // Handle specifications JSON
            if (isset($data['specifications'])) {
                if (!empty(trim($data['specifications']))) {
                    if (is_string($data['specifications'])) {
                        $decoded = json_decode($data['specifications'], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $updateData['specifications'] = $decoded;
                        } else {
                            $updateData['specifications'] = null;
                        }
                    } else {
                        $updateData['specifications'] = $data['specifications'];
                    }
                } else {
                    $updateData['specifications'] = null;
                }
            }

            // Handle is_active checkbox
            if (isset($data['is_active'])) {
                $updateData['is_active'] = (bool)$data['is_active'];
            }

            if ($image) {
                // Delete old image if exists
                if ($product->product_image_url) {
                    $this->deleteImage($product->product_image_url);
                }
                $updateData['product_image_url'] = $this->storeImage($image);
            }

            $product->update($updateData);

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

            // Delete image if exists
            if ($product->product_image_url) {
                $this->deleteImage($product->product_image_url);
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
     * Store product image
     */
    private function storeImage(UploadedFile $image): string
    {
        try {
            return $image->store('products', 'public');
        } catch (Exception $e) {
            Log::error('Error storing product image', [
                'file_name' => $image->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to store product image: ' . $e->getMessage());
        }
    }

    /**
     * Delete product image
     */
    private function deleteImage(string $imagePath): bool
    {
        try {
            if (Storage::disk('public')->exists($imagePath)) {
                return Storage::disk('public')->delete($imagePath);
            }
            return false;
        } catch (Exception $e) {
            Log::error('Error deleting product image', [
                'image_path' => $imagePath,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception here, just log it
            return false;
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

