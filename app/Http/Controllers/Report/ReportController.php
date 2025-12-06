<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $salesReportService;

    public function __construct(SalesReportService $salesReportService)
    {
        $this->salesReportService = $salesReportService;
    }

    /**
     * Sales Report
     */
    public function sales(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'customer_id' => $request->input('customer_id'),
            'product_id' => $request->input('product_id'),
            'brand_id' => $request->input('brand_id'),
            'category_id' => $request->input('category_id'),
            'sale_type' => $request->input('sale_type'),
            'payment_method' => $request->input('payment_method'),
        ];

        // Get report data
        $reportData = $this->salesReportService->generateReport($filters);

        // Get filter options
        $customers = Customer::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'customer_code']);

        $products = Product::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('product_name')
            ->get(['id', 'product_name', 'model_name']);

        $brands = Brand::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('brand_name')
            ->get(['id', 'brand_name']);

        $categories = ProductCategory::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        return view('reports.sales', compact(
            'reportData',
            'filters',
            'customers',
            'products',
            'brands',
            'categories'
        ));
    }

    /**
     * Export Sales Report
     */
    public function exportSales(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'customer_id' => $request->input('customer_id'),
            'product_id' => $request->input('product_id'),
            'brand_id' => $request->input('brand_id'),
            'category_id' => $request->input('category_id'),
            'sale_type' => $request->input('sale_type'),
            'payment_method' => $request->input('payment_method'),
        ];

        return $this->salesReportService->exportToExcel($filters);
    }

    /**
     * Inventory Report
     */
    public function inventory(Request $request)
    {
        // To be implemented
        return view('reports.inventory');
    }

    /**
     * Customer Report
     */
    public function customers(Request $request)
    {
        // To be implemented
        return view('reports.customers');
    }

    /**
     * Financial Report
     */
    public function financial(Request $request)
    {
        // To be implemented
        return view('reports.financial');
    }

    /**
     * Product Performance Report
     */
    public function productPerformance(Request $request)
    {
        // To be implemented
        return view('reports.product-performance');
    }
}

