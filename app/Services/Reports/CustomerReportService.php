<?php

namespace App\Services\Reports;

use App\Models\Customer;
use App\Models\Sales\Sale;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerReportService
{
    /**
     * Generate customer report
     */
    public function generateReport(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Build base query
        $query = Customer::where('tenant_id', $tenantId);

        // Apply filters
        $this->applyFilters($query, $filters);

        // Get customers data
        $customers = $query->orderBy('total_purchases', 'desc')
            ->get();

        // Calculate statistics
        $statistics = $this->calculateStatistics($customers, $filters);

        // Get top customers by purchases
        $topCustomers = $this->getTopCustomers($filters);

        // Get customer acquisition trend
        $acquisitionTrend = $this->getCustomerAcquisitionTrend($filters);

        // Get customers by type
        $customersByType = $this->getCustomersByType($filters);

        // Get recent customers
        $recentCustomers = $this->getRecentCustomers($filters);

        // Get inactive customers
        $inactiveCustomers = $this->getInactiveCustomers($filters);

        return [
            'customers' => $customers,
            'statistics' => $statistics,
            'topCustomers' => $topCustomers,
            'acquisitionTrend' => $acquisitionTrend,
            'customersByType' => $customersByType,
            'recentCustomers' => $recentCustomers,
            'inactiveCustomers' => $inactiveCustomers,
        ];
    }

    /**
     * Apply filters to query
     */
    protected function applyFilters($query, array $filters): void
    {
        if (!empty($filters['customer_type'])) {
            $query->where('customer_type', $filters['customer_type']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'active');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['min_purchases'])) {
            $query->where('total_purchases', '>=', $filters['min_purchases']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'like', '%' . $filters['city'] . '%');
        }
    }

    /**
     * Calculate statistics
     */
    protected function calculateStatistics($customers, array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        // Total customers
        $totalCustomers = $customers->count();
        
        // Active vs Inactive
        $activeCustomers = $customers->where('is_active', true)->count();
        $inactiveCustomers = $customers->where('is_active', false)->count();

        // Customer types
        $newCustomers = $customers->where('customer_type', 'NEW')->count();
        $regularCustomers = $customers->where('customer_type', 'REGULAR')->count();
        $vipCustomers = $customers->where('customer_type', 'VIP')->count();

        // Total revenue from all customers
        $totalRevenue = $customers->sum('total_purchases');

        // Average purchase per customer
        $avgPurchasePerCustomer = $totalCustomers > 0 ? $totalRevenue / $totalCustomers : 0;

        // Customers with purchases
        $customersWithPurchases = $customers->where('total_purchases', '>', 0)->count();
        $purchaseRate = $totalCustomers > 0 ? ($customersWithPurchases / $totalCustomers) * 100 : 0;

        // Top customer
        $topCustomer = $customers->where('total_purchases', '>', 0)->first();
        $topCustomerSpending = $topCustomer ? $topCustomer->total_purchases : 0;

        // Recent acquisitions (last 30 days)
        $recentAcquisitions = Customer::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        // Customers by last purchase date
        $activeThisMonth = $customers->where('last_purchase_date', '>=', Carbon::now()->startOfMonth())->count();
        $activeLastMonth = $customers
            ->where('last_purchase_date', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('last_purchase_date', '<', Carbon::now()->startOfMonth())
            ->count();

        // Customers without purchases
        $customersNoPurchases = $customers->where('total_purchases', '<=', 0)->count();

        // Growth rate (compare with previous period)
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from']) : Carbon::now()->subMonth();
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to']) : Carbon::now();
        $daysDiff = $dateFrom->diffInDays($dateTo);

        $previousPeriodFrom = $dateFrom->copy()->subDays($daysDiff);
        $previousPeriodTo = $dateTo->copy()->subDays($daysDiff);

        $previousCustomers = Customer::where('tenant_id', $tenantId)
            ->whereDate('created_at', '>=', $previousPeriodFrom)
            ->whereDate('created_at', '<=', $previousPeriodTo)
            ->count();

        $currentPeriodCustomers = Customer::where('tenant_id', $tenantId)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->count();

        $growthRate = $previousCustomers > 0 
            ? (($currentPeriodCustomers - $previousCustomers) / $previousCustomers) * 100 
            : 0;

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'inactive_customers' => $inactiveCustomers,
            'new_customers' => $newCustomers,
            'regular_customers' => $regularCustomers,
            'vip_customers' => $vipCustomers,
            'total_revenue' => $totalRevenue,
            'avg_purchase_per_customer' => $avgPurchasePerCustomer,
            'customers_with_purchases' => $customersWithPurchases,
            'purchase_rate' => $purchaseRate,
            'top_customer_spending' => $topCustomerSpending,
            'recent_acquisitions' => $recentAcquisitions,
            'active_this_month' => $activeThisMonth,
            'active_last_month' => $activeLastMonth,
            'customers_no_purchases' => $customersNoPurchases,
            'growth_rate' => $growthRate,
        ];
    }

    /**
     * Get top customers by purchases
     */
    protected function getTopCustomers(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Customer::where('tenant_id', $tenantId)
            ->where('total_purchases', '>', 0);

        if (!empty($filters['customer_type'])) {
            $query->where('customer_type', $filters['customer_type']);
        }

        // Get sales count for each customer
        $customers = $query->orderBy('total_purchases', 'desc')
            ->limit($limit)
            ->get();

        // Add sales count to each customer
        foreach ($customers as $customer) {
            $customer->sales_count = Sale::where('customer_id', $customer->id)->count();
        }

        return $customers->toArray();
    }

    /**
     * Get customer acquisition trend
     */
    protected function getCustomerAcquisitionTrend(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $dateFrom = !empty($filters['date_from']) ? $filters['date_from'] : Carbon::now()->subDays(30)->format('Y-m-d');
        $dateTo = !empty($filters['date_to']) ? $filters['date_to'] : Carbon::now()->format('Y-m-d');

        $query = Customer::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as customer_count')
        )
        ->where('tenant_id', $tenantId)
        ->whereDate('created_at', '>=', $dateFrom)
        ->whereDate('created_at', '<=', $dateTo);

        $trend = $query->groupBy('date')
            ->orderBy('date')
            ->get();

        return $trend->toArray();
    }

    /**
     * Get customers grouped by type
     */
    protected function getCustomersByType(array $filters): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Customer::select(
            'customer_type',
            DB::raw('COUNT(*) as customer_count'),
            DB::raw('SUM(total_purchases) as total_revenue'),
            DB::raw('AVG(total_purchases) as avg_purchase')
        )
        ->where('tenant_id', $tenantId)
        ->groupBy('customer_type');

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $types = $query->orderBy('total_revenue', 'desc')
            ->get();

        return $types->toArray();
    }

    /**
     * Get recent customers
     */
    protected function getRecentCustomers(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Customer::where('tenant_id', $tenantId);

        if (!empty($filters['customer_type'])) {
            $query->where('customer_type', $filters['customer_type']);
        }

        $customers = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $customers->toArray();
    }

    /**
     * Get inactive customers (no purchase in last 90 days)
     */
    protected function getInactiveCustomers(array $filters, $limit = 10): array
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Customer::where('tenant_id', $tenantId)
            ->where('total_purchases', '>', 0)
            ->where(function($q) {
                $q->whereNull('last_purchase_date')
                    ->orWhere('last_purchase_date', '<', Carbon::now()->subDays(90));
            });

        if (!empty($filters['customer_type'])) {
            $query->where('customer_type', $filters['customer_type']);
        }

        $customers = $query->orderBy('last_purchase_date', 'asc')
            ->limit($limit)
            ->get();

        return $customers->toArray();
    }

    /**
     * Export report to Excel
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

        $query = Customer::where('tenant_id', $tenantId);

        $this->applyFilters($query, $filters);

        $customers = $query->orderBy('total_purchases', 'desc')->get();

        $filename = 'customer_report_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Customer Code',
                'Full Name',
                'Mobile',
                'Email',
                'City',
                'Customer Type',
                'Total Purchases',
                'Loyalty Points',
                'Last Purchase Date',
                'Registered Date',
                'Status'
            ]);

            // Add data rows
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->customer_code ?? '-',
                    $customer->full_name,
                    $customer->mobile_primary ?? '-',
                    $customer->email ?? '-',
                    $customer->city ?? '-',
                    $customer->customer_type,
                    number_format($customer->total_purchases, 2),
                    $customer->loyalty_points ?? 0,
                    $customer->last_purchase_date ? $customer->last_purchase_date->format('Y-m-d') : '-',
                    $customer->created_at->format('Y-m-d'),
                    $customer->is_active ? 'Active' : 'Inactive'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

