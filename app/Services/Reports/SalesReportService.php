<?php

namespace App\Services\Reports;

use App\Models\Sales\Sale;
use App\Models\Sales\SaleItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportService
{
    /**
     * Generate sales report
     */
    public function generateReport(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Build base query
        $query = Sale::with(['customer', 'soldBy', 'items.product'])
            ->where('tenant_id', $tenantId);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Get sales data
        $sales = $query->orderBy('sale_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($sales, $filters);

        // Get top products
        $topProducts = $this->getTopProducts($filters);

        // Get daily sales trend
        $dailyTrend = $this->getDailyTrend($filters);

        // Get sales by payment method
        $paymentMethods = $this->getSalesByPaymentMethod($filters);

        // Get sales by type
        $saleTypes = $this->getSalesByType($filters);

        return [
            'sales' => $sales,
            'statistics' => $statistics,
            'topProducts' => $topProducts,
            'dailyTrend' => $dailyTrend,
            'paymentMethods' => $paymentMethods,
            'saleTypes' => $saleTypes,
        ];
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['sale_type'])) {
            $query->where('sale_type', $filters['sale_type']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // Filter by product
        if (!empty($filters['product_id'])) {
            $query->whereHas('items', function ($q) use ($filters) {
                $q->where('product_id', $filters['product_id']);
            });
        }

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $query->whereHas('items.product', function ($q) use ($filters) {
                $q->where('brand_id', $filters['brand_id']);
            });
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->whereHas('items.product', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }
    }

    /**
     * Calculate statistics
     */
    protected function calculateStatistics($sales, array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Current period stats
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $totalQuantity = $sales->sum(function ($sale) {
            return $sale->total_quantity;
        });
        $averageOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Total profit (sum of all items' profit)
        $totalProfit = $sales->sum(function ($sale) {
            return $sale->items->sum(function ($item) {
                $purchasePrice = $item->product->purchase_price ?? 0;
                return ($item->unit_price - $purchasePrice) * $item->quantity;
            });
        });

        // Calculate profit margin
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        // Calculate previous period for comparison
        $dateFrom = Carbon::parse($filters['date_from']);
        $dateTo = Carbon::parse($filters['date_to']);
        $daysDiff = $dateFrom->diffInDays($dateTo) + 1;

        $previousPeriodFrom = $dateFrom->copy()->subDays($daysDiff);
        $previousPeriodTo = $dateTo->copy()->subDays($daysDiff);

        $previousSales = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', '>=', $previousPeriodFrom)
            ->whereDate('sale_date', '<=', $previousPeriodTo)
            ->get();

        $previousRevenue = $previousSales->sum('total_amount');
        $previousTotalSales = $previousSales->count();

        // Calculate growth percentages
        $revenueGrowth = $previousRevenue > 0 
            ? (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 
            : 0;

        $salesGrowth = $previousTotalSales > 0 
            ? (($totalSales - $previousTotalSales) / $previousTotalSales) * 100 
            : 0;

        // Count unique customers
        $uniqueCustomers = $sales->pluck('customer_id')->filter()->unique()->count();

        // Payment method breakdown
        $cashSales = $sales->where('payment_method', 'CASH')->sum('total_amount');
        $cardSales = $sales->where('payment_method', 'CARD')->sum('total_amount');
        $mobileBankingSales = $sales->where('payment_method', 'MOBILE_BANKING')->sum('total_amount');
        $emiSales = $sales->where('payment_method', 'EMI')->sum('total_amount');

        return [
            'total_sales' => $totalSales,
            'total_revenue' => $totalRevenue,
            'total_quantity' => $totalQuantity,
            'total_profit' => $totalProfit,
            'profit_margin' => $profitMargin,
            'average_order_value' => $averageOrderValue,
            'unique_customers' => $uniqueCustomers,
            'revenue_growth' => $revenueGrowth,
            'sales_growth' => $salesGrowth,
            'cash_sales' => $cashSales,
            'card_sales' => $cardSales,
            'mobile_banking_sales' => $mobileBankingSales,
            'emi_sales' => $emiSales,
        ];
    }

    /**
     * Get top selling products
     */
    protected function getTopProducts(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(total_price) as total_revenue'),
            DB::raw('COUNT(DISTINCT sale_id) as sales_count')
        )
        ->whereHas('sale', function ($q) use ($tenantId, $filters) {
            $q->where('tenant_id', $tenantId);
            
            if (!empty($filters['date_from'])) {
                $q->whereDate('sale_date', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $q->whereDate('sale_date', '<=', $filters['date_to']);
            }
        })
        ->with('product')
        ->groupBy('product_id')
        ->orderBy('total_quantity', 'desc')
        ->limit($limit)
        ->get();

        return $query->toArray();
    }

    /**
     * Get daily sales trend
     */
    protected function getDailyTrend(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Get sales grouped by date
        $query = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            'id',
            'total_amount'
        )
        ->with('items:id,sale_id,quantity')
        ->where('tenant_id', $tenantId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        $sales = $query->orderBy('sale_date')->get();

        // Group by date and calculate totals
        $trend = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->sale_date)->format('Y-m-d');
        })->map(function ($dailySales) {
            return [
                'date' => $dailySales->first()->sale_date->format('Y-m-d'),
                'sales_count' => $dailySales->count(),
                'revenue' => $dailySales->sum('total_amount'),
                'quantity' => $dailySales->sum(function ($sale) {
                    return $sale->items->sum('quantity');
                })
            ];
        })->values();

        return $trend->toArray();
    }

    /**
     * Get sales by payment method
     */
    protected function getSalesByPaymentMethod(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Sale::select(
            'payment_method',
            DB::raw('COUNT(*) as sales_count'),
            DB::raw('SUM(total_amount) as total_amount')
        )
        ->where('tenant_id', $tenantId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        $methods = $query->groupBy('payment_method')
            ->orderBy('total_amount', 'desc')
            ->get();

        return $methods->toArray();
    }

    /**
     * Get sales by type
     */
    protected function getSalesByType(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Sale::select(
            'sale_type',
            DB::raw('COUNT(*) as sales_count'),
            DB::raw('SUM(total_amount) as total_amount')
        )
        ->where('tenant_id', $tenantId);

        if (!empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        $types = $query->groupBy('sale_type')
            ->orderBy('total_amount', 'desc')
            ->get();

        return $types->toArray();
    }

    /**
     * Export report to Excel
     */
    public function exportToExcel(array $filters)
    {
        // To be implemented with PhpSpreadsheet or Laravel Excel
        // For now, return CSV
        return $this->exportToCsv($filters);
    }

    /**
     * Export report to CSV
     */
    protected function exportToCsv(array $filters)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Sale::with(['customer', 'soldBy', 'items.product'])
            ->where('tenant_id', $tenantId);

        $this->applyFilters($query, $filters);

        $sales = $query->orderBy('sale_date', 'desc')->get();

        $filename = 'sales_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Invoice No',
                'Date',
                'Customer',
                'Phone',
                'Sale Type',
                'Payment Method',
                'Items',
                'Quantity',
                'Subtotal',
                'Discount',
                'Tax',
                'Total Amount',
                'Sold By'
            ]);

            // Add data rows
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->invoice_number,
                    $sale->sale_date->format('Y-m-d'),
                    $sale->customer ? $sale->customer->full_name : 'Walk-in Customer',
                    $sale->customer ? $sale->customer->mobile_primary : '-',
                    $sale->sale_type,
                    $sale->payment_method,
                    $sale->items->count(),
                    $sale->total_quantity,
                    number_format($sale->subtotal, 2),
                    number_format($sale->discount_amount, 2),
                    number_format($sale->tax_amount, 2),
                    number_format($sale->total_amount, 2),
                    $sale->soldBy ? $sale->soldBy->name : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

