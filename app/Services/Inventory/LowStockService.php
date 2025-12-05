<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class LowStockService
{
    /**
     * Get all low stock products for the current tenant
     */
    public function getLowStockProducts(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand', 'category')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '<=', \DB::raw('reorder_level'))
                ->orderBy('current_stock', 'asc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching low stock products', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get low stock products query for DataTables
     */
    public function getLowStockQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand', 'category')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '<=', \DB::raw('reorder_level'));
        } catch (Exception $e) {
            Log::error('Error creating low stock query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get critical stock products (below minimum level)
     */
    public function getCriticalStockProducts(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand', 'category')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '<=', \DB::raw('min_stock_level'))
                ->orderBy('current_stock', 'asc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching critical stock products', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStockProducts(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Product::with('brand', 'category')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '=', 0)
                ->orderBy('product_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching out of stock products', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get low stock statistics
     */
    public function getLowStockStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $totalLowStock = Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '<=', \DB::raw('reorder_level'))
                ->count();

            $criticalStock = Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '<=', \DB::raw('min_stock_level'))
                ->count();

            $outOfStock = Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where('current_stock', '=', 0)
                ->count();

            $totalProducts = Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->count();

            $lowStockPercentage = $totalProducts > 0 ? round(($totalLowStock / $totalProducts) * 100, 1) : 0;

            // Get products that need immediate attention (below min level or out of stock)
            $urgentProducts = Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->where(function($query) {
                    $query->where('current_stock', '=', 0)
                          ->orWhere('current_stock', '<=', \DB::raw('min_stock_level'));
                })
                ->count();

            return [
                'total_low_stock' => $totalLowStock,
                'critical_stock' => $criticalStock,
                'out_of_stock' => $outOfStock,
                'total_products' => $totalProducts,
                'low_stock_percentage' => $lowStockPercentage,
                'urgent_products' => $urgentProducts,
            ];
        } catch (Exception $e) {
            Log::error('Error fetching low stock statistics', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get stock status for a product
     */
    public function getProductStockStatus(Product $product): array
    {
        $status = 'NORMAL';
        $severity = 'success';
        $message = 'Stock level is good';

        if ($product->current_stock == 0) {
            $status = 'OUT_OF_STOCK';
            $severity = 'danger';
            $message = 'Product is out of stock';
        } elseif ($product->current_stock <= $product->min_stock_level) {
            $status = 'CRITICAL';
            $severity = 'danger';
            $message = 'Critical stock level - immediate action required';
        } elseif ($product->current_stock <= $product->reorder_level) {
            $status = 'LOW';
            $severity = 'warning';
            $message = 'Low stock level - consider reordering';
        }

        $suggestedReorder = max(0, ($product->reorder_level * 2) - $product->current_stock);

        return [
            'status' => $status,
            'severity' => $severity,
            'message' => $message,
            'current_stock' => $product->current_stock,
            'min_stock_level' => $product->min_stock_level,
            'reorder_level' => $product->reorder_level,
            'suggested_reorder' => $suggestedReorder,
            'stock_gap' => max(0, $product->min_stock_level - $product->current_stock),
        ];
    }

    /**
     * Get products by stock status
     */
    public function getProductsByStatus(string $status, ?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $query = Product::with('brand', 'category')
                ->where('tenant_id', $tenantId)
                ->where('is_active', true);

            switch ($status) {
                case 'OUT_OF_STOCK':
                    $query->where('current_stock', '=', 0);
                    break;
                case 'CRITICAL':
                    $query->where('current_stock', '<=', \DB::raw('min_stock_level'))
                          ->where('current_stock', '>', 0);
                    break;
                case 'LOW':
                    $query->where('current_stock', '<=', \DB::raw('reorder_level'))
                          ->where('current_stock', '>', \DB::raw('min_stock_level'));
                    break;
                case 'NORMAL':
                    $query->where('current_stock', '>', \DB::raw('reorder_level'));
                    break;
            }

            return $query->orderBy('current_stock', 'asc')->get();
        } catch (Exception $e) {
            Log::error('Error fetching products by status', [
                'status' => $status,
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
