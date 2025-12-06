<?php

namespace App\Services\Reports;

use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Models\Sales\SaleItem;
use App\Models\Sales\Sale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductPerformanceReportService
{
    /**
     * Generate product performance report
     */
    public function generateReport(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        // Calculate statistics
        $statistics = $this->calculateStatistics($filters);

        // Get best selling products
        $bestSellers = $this->getBestSellingProducts($filters);

        // Get worst performing products
        $worstPerformers = $this->getWorstPerformingProducts($filters);

        // Get most profitable products
        $mostProfitable = $this->getMostProfitableProducts($filters);

        // Get product performance trend
        $performanceTrend = $this->getProductPerformanceTrend($filters);

        // Get performance by category
        $performanceByCategory = $this->getPerformanceByCategory($filters);

        // Get performance by brand
        $performanceByBrand = $this->getPerformanceByBrand($filters);

        // Get low turnover products
        $lowTurnover = $this->getLowTurnoverProducts($filters);

        // Get all products performance
        $productsPerformance = $this->getAllProductsPerformance($filters);

        return [
            'statistics' => $statistics,
            'bestSellers' => $bestSellers,
            'worstPerformers' => $worstPerformers,
            'mostProfitable' => $mostProfitable,
            'performanceTrend' => $performanceTrend,
            'performanceByCategory' => $performanceByCategory,
            'performanceByBrand' => $performanceByBrand,
            'lowTurnover' => $lowTurnover,
            'productsPerformance' => $productsPerformance,
        ];
    }

    /**
     * Calculate statistics
     */
    protected function calculateStatistics(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        // Total products sold
        $totalProductsSold = SaleItem::whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        })->count();

        // Total quantity sold
        $totalQuantitySold = SaleItem::whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        })->sum('quantity');

        // Total revenue
        $totalRevenue = SaleItem::whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        })->sum('total_price');

        // Total profit
        $saleItems = SaleItem::with('product')
            ->whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
                $q->where('tenant_id', $tenantId)
                    ->whereDate('sale_date', '>=', $dateFrom)
                    ->whereDate('sale_date', '<=', $dateTo);
            })->get();

        $totalProfit = $saleItems->sum(function ($item) {
            $purchasePrice = $item->product->purchase_price ?? 0;
            return ($item->unit_price - $purchasePrice) * $item->quantity;
        });

        // Average profit margin
        $avgProfitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Unique products sold
        $uniqueProductsSold = SaleItem::whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        })->distinct('product_id')->count('product_id');

        // Total active products
        $totalActiveProducts = Product::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->count();

        // Products not sold
        $productsNotSold = $totalActiveProducts - $uniqueProductsSold;

        // Average sales per product
        $avgSalesPerProduct = $uniqueProductsSold > 0 ? $totalQuantitySold / $uniqueProductsSold : 0;

        // Average revenue per product
        $avgRevenuePerProduct = $uniqueProductsSold > 0 ? $totalRevenue / $uniqueProductsSold : 0;

        return [
            'total_products_sold' => $totalProductsSold,
            'total_quantity_sold' => $totalQuantitySold,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'avg_profit_margin' => $avgProfitMargin,
            'unique_products_sold' => $uniqueProductsSold,
            'total_active_products' => $totalActiveProducts,
            'products_not_sold' => $productsNotSold,
            'avg_sales_per_product' => $avgSalesPerProduct,
            'avg_revenue_per_product' => $avgRevenuePerProduct,
        ];
    }

    /**
     * Get best selling products
     */
    protected function getBestSellingProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $query = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_revenue'),
            DB::raw('COUNT(DISTINCT sale_id) as sales_count')
        )
        ->with('product.brand')
        ->whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo, $filters) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        });

        // Apply product filters
        if (!empty($filters['brand_id'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('brand_id', $filters['brand_id']);
            });
        }

        if (!empty($filters['category_id'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        $products = $query->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();

        // Calculate profit for each product
        foreach ($products as $item) {
            if ($item->product) {
                $purchasePrice = $item->product->purchase_price ?? 0;
                $item->total_profit = ($item->total_revenue / $item->total_quantity - $purchasePrice) * $item->total_quantity;
                $item->profit_margin = $item->total_revenue > 0 ? ($item->total_profit / $item->total_revenue) * 100 : 0;
            }
        }

        return $products->toArray();
    }

    /**
     * Get worst performing products
     */
    protected function getWorstPerformingProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $query = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_revenue'),
            DB::raw('COUNT(DISTINCT sale_id) as sales_count')
        )
        ->with('product.brand')
        ->whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        });

        $products = $query->groupBy('product_id')
            ->orderBy('total_quantity', 'asc')
            ->limit($limit)
            ->get();

        return $products->toArray();
    }

    /**
     * Get most profitable products
     */
    protected function getMostProfitableProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $saleItems = SaleItem::with('product.brand')
            ->whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
                $q->where('tenant_id', $tenantId)
                    ->whereDate('sale_date', '>=', $dateFrom)
                    ->whereDate('sale_date', '<=', $dateTo);
            })
            ->get();

        // Group by product and calculate profit
        $productProfits = [];
        foreach ($saleItems as $item) {
            if (!$item->product) continue;

            $productId = $item->product_id;
            $purchasePrice = $item->product->purchase_price ?? 0;
            $profit = ($item->unit_price - $purchasePrice) * $item->quantity;

            if (!isset($productProfits[$productId])) {
                $productProfits[$productId] = [
                    'product' => $item->product,
                    'total_quantity' => 0,
                    'total_revenue' => 0,
                    'total_profit' => 0,
                    'sales_count' => 0,
                ];
            }

            $productProfits[$productId]['total_quantity'] += $item->quantity;
            $productProfits[$productId]['total_revenue'] += $item->total_price;
            $productProfits[$productId]['total_profit'] += $profit;
            $productProfits[$productId]['sales_count']++;
        }

        // Calculate profit margin and sort
        foreach ($productProfits as &$data) {
            $data['profit_margin'] = $data['total_revenue'] > 0 
                ? ($data['total_profit'] / $data['total_revenue']) * 100 
                : 0;
        }

        // Sort by profit and limit
        usort($productProfits, function ($a, $b) {
            return $b['total_profit'] <=> $a['total_profit'];
        });

        return array_slice($productProfits, 0, $limit);
    }

    /**
     * Get product performance trend
     */
    protected function getProductPerformanceTrend(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $query = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('COUNT(DISTINCT sale_items.product_id) as products_sold'),
            DB::raw('SUM(sale_items.quantity) as quantity'),
            DB::raw('SUM(sale_items.total_price) as revenue')
        )
        ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
        ->where('tenant_id', $tenantId)
        ->whereDate('sale_date', '>=', $dateFrom)
        ->whereDate('sale_date', '<=', $dateTo)
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return $query->toArray();
    }

    /**
     * Get performance by category
     */
    protected function getPerformanceByCategory(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $query = SaleItem::select(
            'products.category_id',
            DB::raw('COUNT(DISTINCT sale_items.product_id) as products_sold'),
            DB::raw('SUM(sale_items.quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total_price) as total_revenue')
        )
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->where('sales.tenant_id', $tenantId)
        ->whereDate('sales.sale_date', '>=', $dateFrom)
        ->whereDate('sales.sale_date', '<=', $dateTo)
        ->groupBy('products.category_id')
        ->orderBy('total_revenue', 'desc')
        ->get();

        // Load category names
        foreach ($query as $item) {
            $category = ProductCategory::find($item->category_id);
            $item->category_name = $category ? $category->category_name : 'Uncategorized';
        }

        return $query->toArray();
    }

    /**
     * Get performance by brand
     */
    protected function getPerformanceByBrand(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $query = SaleItem::select(
            'products.brand_id',
            DB::raw('COUNT(DISTINCT sale_items.product_id) as products_sold'),
            DB::raw('SUM(sale_items.quantity) as total_quantity'),
            DB::raw('SUM(sale_items.total_price) as total_revenue')
        )
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->where('sales.tenant_id', $tenantId)
        ->whereNotNull('products.brand_id')
        ->whereDate('sales.sale_date', '>=', $dateFrom)
        ->whereDate('sales.sale_date', '<=', $dateTo)
        ->groupBy('products.brand_id')
        ->orderBy('total_revenue', 'desc')
        ->get();

        // Load brand names
        foreach ($query as $item) {
            $brand = Brand::find($item->brand_id);
            $item->brand_name = $brand ? $brand->brand_name : 'Unknown';
        }

        return $query->toArray();
    }

    /**
     * Get low turnover products
     */
    protected function getLowTurnoverProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Get products with stock but low/no sales
        $products = Product::where('tenant_id', $tenantId)
            ->where('current_stock', '>', 0)
            ->where('is_active', true)
            ->get();

        $productsWithTurnover = [];

        foreach ($products as $product) {
            $salesCount = SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)
                        ->where('sale_date', '>=', Carbon::now()->subDays(30));
                })
                ->sum('quantity');

            $turnoverRate = $product->current_stock > 0 
                ? ($salesCount / $product->current_stock) * 100 
                : 0;

            $productsWithTurnover[] = [
                'product' => $product,
                'sales_last_30_days' => $salesCount,
                'turnover_rate' => $turnoverRate,
                'stock_value' => $product->current_stock * $product->purchase_price,
            ];
        }

        // Sort by turnover rate (ascending)
        usort($productsWithTurnover, function ($a, $b) {
            return $a['turnover_rate'] <=> $b['turnover_rate'];
        });

        return array_slice($productsWithTurnover, 0, $limit);
    }

    /**
     * Get all products performance
     */
    protected function getAllProductsPerformance(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subMonth()->format('Y-m-d');
        $dateTo = $filters['date_to'] ?? Carbon::now()->format('Y-m-d');

        $query = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_revenue'),
            DB::raw('COUNT(DISTINCT sale_id) as sales_count')
        )
        ->with(['product.brand', 'product.category'])
        ->whereHas('sale', function ($q) use ($tenantId, $dateFrom, $dateTo) {
            $q->where('tenant_id', $tenantId)
                ->whereDate('sale_date', '>=', $dateFrom)
                ->whereDate('sale_date', '<=', $dateTo);
        })
        ->groupBy('product_id')
        ->orderBy('total_revenue', 'desc')
        ->get();

        // Calculate profit for each product
        foreach ($query as $item) {
            if ($item->product) {
                $purchasePrice = $item->product->purchase_price ?? 0;
                $avgSellingPrice = $item->total_revenue / $item->total_quantity;
                $item->total_profit = ($avgSellingPrice - $purchasePrice) * $item->total_quantity;
                $item->profit_margin = $item->total_revenue > 0 ? ($item->total_profit / $item->total_revenue) * 100 : 0;
            }
        }

        return $query->toArray();
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
        $productsPerformance = $this->getAllProductsPerformance($filters);

        $filename = 'product_performance_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($productsPerformance) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Product Name',
                'Model',
                'Brand',
                'Category',
                'Quantity Sold',
                'Sales Count',
                'Total Revenue',
                'Total Profit',
                'Profit Margin %'
            ]);

            // Add data rows
            foreach ($productsPerformance as $item) {
                $product = $item['product'] ?? null;
                
                fputcsv($file, [
                    $product ? $product['product_name'] : 'N/A',
                    $product ? ($product['model_name'] ?? '-') : '-',
                    $product && isset($product['brand']) ? $product['brand']['brand_name'] : '-',
                    $product && isset($product['category']) ? $product['category']['category_name'] : '-',
                    $item['total_quantity'],
                    $item['sales_count'],
                    number_format($item['total_revenue'], 2),
                    number_format($item['total_profit'] ?? 0, 2),
                    number_format($item['profit_margin'] ?? 0, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

