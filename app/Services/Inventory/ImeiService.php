<?php

namespace App\Services\Inventory;

use App\Models\Inventory\ProductImei;
use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class ImeiService
{
    /**
     * Get all IMEI records for the current tenant
     */
    public function getImeiForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return ProductImei::with(['product.brand', 'customer'])
                ->where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching IMEI records for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get IMEI query for DataTables
     */
    public function getImeiQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return ProductImei::with(['product.brand', 'customer'])
                ->where('tenant_id', $tenantId);
        } catch (Exception $e) {
            Log::error('Error creating IMEI query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get products for dropdown (only mobiles and accessories that might have IMEI)
     */
    public function getProductsForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->whereIn('product_type', ['MOBILE', 'ACCESSORY'])
                ->orderBy('product_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching products for IMEI dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new IMEI record
     */
    public function createImei(array $data): ProductImei
    {
        try {
            // Set tenant_id if not provided
            if (!isset($data['tenant_id'])) {
                $data['tenant_id'] = $this->getTenantId();
            }

            // Check if IMEI number already exists
            $existingImei = ProductImei::where('imei_number', $data['imei_number'])
                ->where('tenant_id', $data['tenant_id'])
                ->first();

            if ($existingImei) {
                throw new Exception('IMEI number already exists in the system.');
            }

            // Validate product belongs to tenant
            $product = Product::where('id', $data['product_id'])
                ->where('tenant_id', $data['tenant_id'])
                ->first();

            if (!$product) {
                throw new Exception('Product not found or does not belong to your tenant.');
            }

            return ProductImei::create($data);
        } catch (Exception $e) {
            Log::error('Error creating IMEI record', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an IMEI record
     */
    public function updateImei(ProductImei $imei, array $data): ProductImei
    {
        try {
            // Check if IMEI number already exists (excluding current record)
            $existingImei = ProductImei::where('imei_number', $data['imei_number'])
                ->where('tenant_id', $imei->tenant_id)
                ->where('id', '!=', $imei->id)
                ->first();

            if ($existingImei) {
                throw new Exception('IMEI number already exists in the system.');
            }

            // Validate product belongs to tenant if product_id is being changed
            if (isset($data['product_id']) && $data['product_id'] != $imei->product_id) {
                $product = Product::where('id', $data['product_id'])
                    ->where('tenant_id', $imei->tenant_id)
                    ->first();

                if (!$product) {
                    throw new Exception('Product not found or does not belong to your tenant.');
                }
            }

            $imei->update($data);

            return $imei->fresh(['product.brand', 'customer']);
        } catch (Exception $e) {
            Log::error('Error updating IMEI record', [
                'imei_id' => $imei->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete an IMEI record
     */
    public function deleteImei(ProductImei $imei): bool
    {
        try {
            // Check if IMEI is sold and has associated sale
            if ($imei->status === 'SOLD' && $imei->sold_to_customer_id) {
                throw new Exception('Cannot delete IMEI record that has been sold. Please return the item first.');
            }

            return $imei->delete();
        } catch (Exception $e) {
            Log::error('Error deleting IMEI record', [
                'imei_id' => $imei->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get IMEI statistics
     */
    public function getImeiStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $stats = ProductImei::where('tenant_id', $tenantId)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $total = array_sum($stats);

            // Calculate warranty expiry statistics
            $expiringSoon = ProductImei::where('tenant_id', $tenantId)
                ->whereNotNull('warranty_expiry_date')
                ->where('warranty_expiry_date', '>', now())
                ->where('warranty_expiry_date', '<=', now()->addDays(30))
                ->count();

            $expired = ProductImei::where('tenant_id', $tenantId)
                ->whereNotNull('warranty_expiry_date')
                ->where('warranty_expiry_date', '<', now())
                ->count();

            return [
                'total' => $total,
                'in_stock' => $stats['IN_STOCK'] ?? 0,
                'sold' => $stats['SOLD'] ?? 0,
                'defective' => $stats['DEFECTIVE'] ?? 0,
                'returned' => $stats['RETURNED'] ?? 0,
                'expiring_soon' => $expiringSoon,
                'expired' => $expired,
            ];
        } catch (Exception $e) {
            Log::error('Error fetching IMEI statistics', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
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
