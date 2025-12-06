<?php

namespace App\Services\Reports;

use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Models\Inventory\StockAdjustment;
use App\Models\Inventory\StockTransfer;
use App\Models\Sales\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryReportService
{
    /**
     * Generate inventory report
     */
    public function generateReport(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Build base query
        $query = Product::with(['brand', 'category'])
            ->where('tenant_id', $tenantId);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Get products data
        $products = $query->orderBy('product_name')
            ->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($products, $filters);

        // Get stock movement
        $stockMovement = $this->getStockMovement($filters);

        // Get top stock value products
        $topStockValue = $this->getTopStockValueProducts($filters);

        // Get low stock products
        $lowStockProducts = $this->getLowStockProducts($filters);

        // Get stock by category
        $stockByCategory = $this->getStockByCategory($filters);

        // Get stock by brand
        $stockByBrand = $this->getStockByBrand($filters);

        return [
            'products' => $products,
            'statistics' => $statistics,
            'stockMovement' => $stockMovement,
            'topStockValue' => $topStockValue,
            'lowStockProducts' => $lowStockProducts,
            'stockByCategory' => $stockByCategory,
            'stockByBrand' => $stockByBrand,
        ];
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'in_stock':
                    $query->where('current_stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereRaw('current_stock > 0 AND current_stock <= reorder_level');
                    break;
                case 'critical_stock':
                    $query->whereRaw('current_stock > 0 AND current_stock <= min_stock_level');
                    break;
            }
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'active');
        }
    }

    /**
     * Calculate statistics
     */
    protected function calculateStatistics($products, array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Total products
        $totalProducts = $products->count();
        
        // Total stock quantity
        $totalStockQuantity = $products->sum('current_stock');

        // Total stock value (at purchase price)
        $totalStockValue = $products->sum(function ($product) {
            return $product->current_stock * $product->purchase_price;
        });

        // Potential revenue (at selling price)
        $potentialRevenue = $products->sum(function ($product) {
            return $product->current_stock * $product->selling_price;
        });

        // Potential profit
        $potentialProfit = $potentialRevenue - $totalStockValue;

        // Stock status counts
        $inStockCount = $products->where('current_stock', '>', 0)->count();
        $outOfStockCount = $products->where('current_stock', '<=', 0)->count();
        $lowStockCount = $products->filter(function ($product) {
            return $product->current_stock > 0 && $product->current_stock <= $product->reorder_level;
        })->count();
        $criticalStockCount = $products->filter(function ($product) {
            return $product->current_stock > 0 && $product->current_stock <= $product->min_stock_level;
        })->count();

        // Active vs Inactive
        $activeProducts = $products->where('is_active', true)->count();
        $inactiveProducts = $products->where('is_active', false)->count();

        // Stock turnover (last 30 days)
        $dateFrom = Carbon::now()->subDays(30);
        $soldQuantity = SaleItem::whereHas('sale', function ($q) use ($tenantId, $dateFrom) {
            $q->where('tenant_id', $tenantId)
                ->where('sale_date', '>=', $dateFrom);
        })->sum('quantity');

        $stockTurnoverRate = $totalStockQuantity > 0 
            ? round(($soldQuantity / $totalStockQuantity) * 100, 2) 
            : 0;

        // Average stock value per product
        $avgStockValuePerProduct = $totalProducts > 0 
            ? $totalStockValue / $totalProducts 
            : 0;

        return [
            'total_products' => $totalProducts,
            'total_stock_quantity' => $totalStockQuantity,
            'total_stock_value' => $totalStockValue,
            'potential_revenue' => $potentialRevenue,
            'potential_profit' => $potentialProfit,
            'in_stock_count' => $inStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'low_stock_count' => $lowStockCount,
            'critical_stock_count' => $criticalStockCount,
            'active_products' => $activeProducts,
            'inactive_products' => $inactiveProducts,
            'stock_turnover_rate' => $stockTurnoverRate,
            'avg_stock_value_per_product' => $avgStockValuePerProduct,
        ];
    }

    /**
     * Get stock movement (adjustments + sales)
     */
    protected function getStockMovement(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;
        
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        // Stock adjustments
        $adjustments = StockAdjustment::with('product')
            ->where('tenant_id', $tenantId)
            ->whereDate('adjustment_date', '>=', $dateFrom)
            ->whereDate('adjustment_date', '<=', $dateTo)
            ->orderBy('adjustment_date', 'desc')
            ->limit(10)
            ->get();

        // Recent sales (stock out)
        $recentSales = SaleItem::with(['sale', 'product'])
            ->whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
                $q->where('tenant_id', $tenantId)
                    ->whereDate('sale_date', '>=', $dateFrom)
                    ->whereDate('sale_date', '<=', $dateTo);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'adjustments' => $adjustments,
            'recent_sales' => $recentSales,
        ];
    }

    /**
     * Get products with highest stock value
     */
    protected function getTopStockValueProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Product::select('*')
            ->selectRaw('(current_stock * purchase_price) as stock_value')
            ->where('tenant_id', $tenantId)
            ->where('current_stock', '>', 0);

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $products = $query->orderBy('stock_value', 'desc')
            ->limit($limit)
            ->get();

        return $products->toArray();
    }

    /**
     * Get low stock products
     */
    protected function getLowStockProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Product::where('tenant_id', $tenantId)
            ->whereRaw('current_stock > 0 AND current_stock <= reorder_level');

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $products = $query->orderBy('current_stock')
            ->limit($limit)
            ->get();

        return $products->toArray();
    }

    /**
     * Get stock grouped by category
     */
    protected function getStockByCategory(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Product::select(
            'category_id',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('SUM(current_stock) as total_stock'),
            DB::raw('SUM(current_stock * purchase_price) as total_value')
        )
        ->with('category')
        ->where('tenant_id', $tenantId)
        ->groupBy('category_id');

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        $categories = $query->orderBy('total_value', 'desc')
            ->get();

        return $categories->toArray();
    }

    /**
     * Get stock grouped by brand
     */
    protected function getStockByBrand(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Product::select(
            'brand_id',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('SUM(current_stock) as total_stock'),
            DB::raw('SUM(current_stock * purchase_price) as total_value')
        )
        ->with('brand')
        ->where('tenant_id', $tenantId)
        ->whereNotNull('brand_id')
        ->groupBy('brand_id');

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        $brands = $query->orderBy('total_value', 'desc')
            ->get();

        return $brands->toArray();
    }

    /**
     * Export report to CSV
     */
    public function exportToExcel(array $filters)
    {
        return $this->exportToCsv($filters);
    }

    /**
     * Export report to CSV
     */
    protected function exportToCsv(array $filters)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Product::with(['brand', 'category'])
            ->where('tenant_id', $tenantId);

        $this->applyFilters($query, $filters);

        $products = $query->orderBy('product_name')->get();

        $filename = 'inventory_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Product Code',
                'Product Name',
                'Brand',
                'Category',
                'Type',
                'Model',
                'Current Stock',
                'Min Level',
                'Reorder Level',
                'Unit',
                'Purchase Price',
                'Selling Price',
                'Stock Value',
                'Potential Revenue',
                'Status'
            ]);

            // Add data rows
            foreach ($products as $product) {
                $stockValue = $product->current_stock * $product->purchase_price;
                $potentialRevenue = $product->current_stock * $product->selling_price;
                
                // Determine stock status
                if ($product->current_stock <= 0) {
                    $status = 'Out of Stock';
                } elseif ($product->current_stock <= $product->min_stock_level) {
                    $status = 'Critical';
                } elseif ($product->current_stock <= $product->reorder_level) {
                    $status = 'Low Stock';
                } else {
                    $status = 'In Stock';
                }

                fputcsv($file, [
                    $product->product_code ?? '-',
                    $product->product_name,
                    $product->brand ? $product->brand->brand_name : '-',
                    $product->category ? $product->category->category_name : '-',
                    $product->product_type,
                    $product->model_name ?? '-',
                    $product->current_stock,
                    $product->min_stock_level,
                    $product->reorder_level,
                    $product->unit,
                    number_format($product->purchase_price, 2),
                    number_format($product->selling_price, 2),
                    number_format($stockValue, 2),
                    number_format($potentialRevenue, 2),
                    $status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

