<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sales\Sale;
use App\Models\Inventory\Product;
use App\Models\Customer;
use App\Models\Sales\ReturnModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current tenant ID (assuming it's available in session or auth user)
        // For now, we'll assume the user belongs to a tenant or we get it from the request/session.
        // If using a package like stancl/tenancy, it might be automatic, but based on models, it seems manual.
        // Let's assume the authenticated user has a tenant_id.
        $tenantId = auth()->user()->tenant_id;

        if (!$tenantId) {
            // Fallback or error if no tenant ID found for user
            return redirect()->route('login')->with('error', 'No tenant associated with this user.');
        }

        // 1. Key Metrics
        $totalSales = Sale::where('tenant_id', $tenantId)->sum('total_amount');
        $todaySales = Sale::where('tenant_id', $tenantId)
            ->whereDate('sale_date', Carbon::today())
            ->sum('total_amount');
        $totalOrders = Sale::where('tenant_id', $tenantId)->count();
        $totalCustomers = Customer::where('tenant_id', $tenantId)->count();
        
        // 2. Product Stats
        $totalProducts = Product::where('tenant_id', $tenantId)->count();
        $lowStockProducts = Product::where('tenant_id', $tenantId)
            ->whereColumn('current_stock', '<=', 'min_stock_level')
            ->take(5)
            ->get();
            
        // 3. Recent Activity
        $recentSales = Sale::where('tenant_id', $tenantId)
            ->with('customer')
            ->latest('sale_date')
            ->take(10)
            ->get();

        // 4. Sales Chart Data (Last 7 Days)
        $salesChartData = [];
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('d M');
            $salesChartData[] = Sale::where('tenant_id', $tenantId)
                ->whereDate('sale_date', $date)
                ->sum('total_amount');
        }

        // 5. Top Selling Products
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id') // Need to join sales to filter by tenant
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenantId)
            ->select('products.product_name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalSales',
            'todaySales',
            'totalOrders',
            'totalCustomers',
            'totalProducts',
            'lowStockProducts',
            'recentSales',
            'salesChartData',
            'dates',
            'topProducts'
        ));
    }
}
