<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class BrandService
{
    /**
     * Get all brands for the current tenant
     */
    public function getBrandsForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return Brand::where('tenant_id', $tenantId)
                ->orderBy('brand_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching brands for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get brands query for DataTables
     */
    public function getBrandsQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return Brand::where('tenant_id', $tenantId);
        } catch (Exception $e) {
            Log::error('Error creating brands query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new brand
     */
    public function createBrand(array $data, ?UploadedFile $logo = null): Brand
    {
        try {
            $tenantId = $this->getTenantId();
            
            $logoPath = null;
            if ($logo) {
                $logoPath = $this->storeLogo($logo);
            }

            $brand = Brand::create([
                'tenant_id' => $tenantId,
                'brand_name' => $data['brand_name'],
                'brand_logo_url' => $logoPath,
                'is_active' => true,
            ]);

            Log::info('Brand created successfully', [
                'brand_id' => $brand->id,
                'brand_name' => $brand->brand_name,
                'tenant_id' => $tenantId
            ]);

            return $brand;
        } catch (Exception $e) {
            Log::error('Error creating brand', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing brand
     */
    public function updateBrand(Brand $brand, array $data, ?UploadedFile $logo = null): Brand
    {
        try {
            $updateData = [
                'brand_name' => $data['brand_name'],
            ];

            if ($logo) {
                // Delete old logo if exists
                if ($brand->brand_logo_url) {
                    $this->deleteLogo($brand->brand_logo_url);
                }
                $updateData['brand_logo_url'] = $this->storeLogo($logo);
            }

            $brand->update($updateData);

            Log::info('Brand updated successfully', [
                'brand_id' => $brand->id,
                'brand_name' => $brand->brand_name
            ]);

            return $brand->fresh();
        } catch (Exception $e) {
            Log::error('Error updating brand', [
                'brand_id' => $brand->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a brand
     */
    public function deleteBrand(Brand $brand): bool
    {
        try {
            // Delete logo if exists
            if ($brand->brand_logo_url) {
                $this->deleteLogo($brand->brand_logo_url);
            }

            $brandId = $brand->id;
            $brandName = $brand->brand_name;
            
            $deleted = $brand->delete();

            if ($deleted) {
                Log::info('Brand deleted successfully', [
                    'brand_id' => $brandId,
                    'brand_name' => $brandName
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting brand', [
                'brand_id' => $brand->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Store brand logo
     */
    private function storeLogo(UploadedFile $logo): string
    {
        try {
            return $logo->store('brands', 'public');
        } catch (Exception $e) {
            Log::error('Error storing brand logo', [
                'file_name' => $logo->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw new Exception('Failed to store brand logo: ' . $e->getMessage());
        }
    }

    /**
     * Delete brand logo
     */
    private function deleteLogo(string $logoPath): bool
    {
        try {
            if (Storage::disk('public')->exists($logoPath)) {
                return Storage::disk('public')->delete($logoPath);
            }
            return false;
        } catch (Exception $e) {
            Log::error('Error deleting brand logo', [
                'logo_path' => $logoPath,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception here, just log it
            return false;
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

