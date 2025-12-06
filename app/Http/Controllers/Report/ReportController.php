<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Customer;
use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Services\Reports\SalesReportService;
use App\Services\Reports\InventoryReportService;
use App\Services\Reports\CustomerReportService;
use App\Services\Reports\ProductPerformanceReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $salesReportService;
    protected $inventoryReportService;
    protected $customerReportService;
    protected $productPerformanceReportService;

    public function __construct(
        SalesReportService $salesReportService,
        InventoryReportService $inventoryReportService,
        CustomerReportService $customerReportService,
        ProductPerformanceReportService $productPerformanceReportService
    ) {
        $this->salesReportService = $salesReportService;
        $this->inventoryReportService = $inventoryReportService;
        $this->customerReportService = $customerReportService;
        $this->productPerformanceReportService = $productPerformanceReportService;
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
        $filters = [
            'date_from' => $request->input('date_from', Carbon::now()->subDays(30)->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'brand_id' => $request->input('brand_id'),
            'category_id' => $request->input('category_id'),
            'product_type' => $request->input('product_type'),
            'stock_status' => $request->input('stock_status'),
            'is_active' => $request->input('is_active'),
        ];

        // Get report data
        $reportData = $this->inventoryReportService->generateReport($filters);

        // Get filter options
        $brands = Brand::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('brand_name')
            ->get(['id', 'brand_name']);

        $categories = ProductCategory::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        return view('reports.inventory', compact(
            'reportData',
            'filters',
            'brands',
            'categories'
        ));
    }

    /**
     * Export Inventory Report
     */
    public function exportInventory(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'brand_id' => $request->input('brand_id'),
            'category_id' => $request->input('category_id'),
            'product_type' => $request->input('product_type'),
            'stock_status' => $request->input('stock_status'),
            'is_active' => $request->input('is_active'),
        ];

        return $this->inventoryReportService->exportToExcel($filters);
    }

    /**
     * Customer Report
     */
    public function customers(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'customer_type' => $request->input('customer_type'),
            'is_active' => $request->input('is_active'),
            'min_purchases' => $request->input('min_purchases'),
            'city' => $request->input('city'),
        ];

        // Get report data
        $reportData = $this->customerReportService->generateReport($filters);

        return view('reports.customers', compact(
            'reportData',
            'filters'
        ));
    }

    /**
     * Export Customer Report
     */
    public function exportCustomers(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'customer_type' => $request->input('customer_type'),
            'is_active' => $request->input('is_active'),
            'min_purchases' => $request->input('min_purchases'),
            'city' => $request->input('city'),
        ];

        return $this->customerReportService->exportToExcel($filters);
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
        $filters = [
            'date_from' => $request->input('date_from', Carbon::now()->subMonth()->format('Y-m-d')),
            'date_to' => $request->input('date_to', Carbon::now()->format('Y-m-d')),
            'brand_id' => $request->input('brand_id'),
            'category_id' => $request->input('category_id'),
        ];

        // Get report data
        $reportData = $this->productPerformanceReportService->generateReport($filters);

        // Get filter options
        $brands = Brand::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('brand_name')
            ->get(['id', 'brand_name']);

        $categories = ProductCategory::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('category_name')
            ->get(['id', 'category_name']);

        return view('reports.product-performance', compact(
            'reportData',
            'filters',
            'brands',
            'categories'
        ));
    }

    /**
     * Export Product Performance Report
     */
    public function exportProductPerformance(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'brand_id' => $request->input('brand_id'),
            'category_id' => $request->input('category_id'),
        ];

        return $this->productPerformanceReportService->exportToExcel($filters);
    }
}

