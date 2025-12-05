<?php

namespace App\Services\Inventory;

use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class StockAdjustmentService
{
    /**
     * Get all stock adjustments for the current tenant
     */
    public function getStockAdjustmentsForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return StockAdjustment::with(['product.brand', 'adjustedBy'])
                ->forTenant($tenantId)
                ->orderBy('adjustment_date', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching stock adjustments for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get stock adjustments query for DataTables
     */
    public function getStockAdjustmentsQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return StockAdjustment::with(['product.brand', 'adjustedBy'])
                ->forTenant($tenantId);
        } catch (Exception $e) {
            Log::error('Error creating stock adjustments query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get products for dropdown
     */
    public function getProductsForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->orderBy('product_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching products for stock adjustment dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new stock adjustment
     */
    public function createStockAdjustment(array $data): StockAdjustment
    {
        try {
            // Set tenant_id if not provided
            if (!isset($data['tenant_id'])) {
                $data['tenant_id'] = $this->getTenantId();
            }

            // Set adjusted_by if not provided and user is authenticated
            if (!isset($data['adjusted_by']) && auth()->check()) {
                $data['adjusted_by'] = auth()->id();
            }

            // Validate product belongs to tenant
            $product = Product::where('id', $data['product_id'])
                ->where('tenant_id', $data['tenant_id'])
                ->first();

            if (!$product) {
                throw new Exception('Product not found or does not belong to your tenant.');
            }

            // Create the adjustment
            $adjustment = StockAdjustment::create($data);

            // Update product stock based on adjustment type
            $this->updateProductStock($product, $adjustment);

            return $adjustment->fresh(['product.brand', 'adjustedBy']);
        } catch (Exception $e) {
            Log::error('Error creating stock adjustment', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update a stock adjustment
     */
    public function updateStockAdjustment(StockAdjustment $adjustment, array $data): StockAdjustment
    {
        try {
            // Validate product belongs to tenant if product_id is being changed
            if (isset($data['product_id']) && $data['product_id'] != $adjustment->product_id) {
                $product = Product::where('id', $data['product_id'])
                    ->where('tenant_id', $adjustment->tenant_id)
                    ->first();

                if (!$product) {
                    throw new Exception('Product not found or does not belong to your tenant.');
                }
            }

            // Get the old adjustment data for stock reversal
            $oldProduct = $adjustment->product;
            $oldAdjustment = clone $adjustment;

            // Update the adjustment
            $adjustment->update($data);

            // Reverse old adjustment effect
            $this->reverseStockAdjustment($oldProduct, $oldAdjustment);

            // Apply new adjustment effect
            $newProduct = $adjustment->product;
            $this->updateProductStock($newProduct, $adjustment);

            return $adjustment->fresh(['product.brand', 'adjustedBy']);
        } catch (Exception $e) {
            Log::error('Error updating stock adjustment', [
                'adjustment_id' => $adjustment->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a stock adjustment
     */
    public function deleteStockAdjustment(StockAdjustment $adjustment): bool
    {
        try {
            // Reverse the stock adjustment effect
            $product = $adjustment->product;
            $this->reverseStockAdjustment($product, $adjustment);

            return $adjustment->delete();
        } catch (Exception $e) {
            Log::error('Error deleting stock adjustment', [
                'adjustment_id' => $adjustment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update product stock based on adjustment
     */
    private function updateProductStock(Product $product, StockAdjustment $adjustment): void
    {
        $quantityChange = match($adjustment->adjustment_type) {
            'ADD', 'FOUND', 'RETURN' => $adjustment->quantity,
            'REMOVE', 'DAMAGED', 'LOST' => -$adjustment->quantity,
            default => 0
        };

        if ($quantityChange !== 0) {
            $product->increment('current_stock', $quantityChange);
        }
    }

    /**
     * Reverse stock adjustment effect
     */
    private function reverseStockAdjustment(Product $product, StockAdjustment $adjustment): void
    {
        $quantityChange = match($adjustment->adjustment_type) {
            'ADD', 'FOUND', 'RETURN' => -$adjustment->quantity,
            'REMOVE', 'DAMAGED', 'LOST' => $adjustment->quantity,
            default => 0
        };

        if ($quantityChange !== 0) {
            $product->increment('current_stock', $quantityChange);
        }
    }

    /**
     * Get stock adjustment statistics
     */
    public function getStockAdjustmentStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $stats = StockAdjustment::forTenant($tenantId)
                ->selectRaw('adjustment_type, COUNT(*) as count, SUM(quantity) as total_quantity')
                ->groupBy('adjustment_type')
                ->pluck('count', 'adjustment_type')
                ->toArray();

            $totalQuantity = StockAdjustment::forTenant($tenantId)
                ->sum('quantity');

            $recentAdjustments = StockAdjustment::forTenant($tenantId)
                ->where('adjustment_date', '>=', now()->subDays(30))
                ->count();

            return [
                'total_adjustments' => array_sum($stats),
                'total_quantity' => $totalQuantity,
                'add_count' => $stats['ADD'] ?? 0,
                'remove_count' => $stats['REMOVE'] ?? 0,
                'damaged_count' => $stats['DAMAGED'] ?? 0,
                'lost_count' => $stats['LOST'] ?? 0,
                'found_count' => $stats['FOUND'] ?? 0,
                'return_count' => $stats['RETURN'] ?? 0,
                'recent_adjustments' => $recentAdjustments,
            ];
        } catch (Exception $e) {
            Log::error('Error fetching stock adjustment statistics', [
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
