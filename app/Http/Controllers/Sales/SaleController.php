<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Services\Sales\SaleService;
use App\Services\CustomerService;
use App\Services\Inventory\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    protected SaleService $saleService;
    protected CustomerService $customerService;
    protected ProductService $productService;

    public function __construct(
        SaleService $saleService,
        CustomerService $customerService,
        ProductService $productService
    ) {
        $this->saleService = $saleService;
        $this->customerService = $customerService;
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->saleService->getSalesQuery();

                return DataTables::of($query)
                    ->addColumn('invoice_number', function ($row) {
                        return $row->invoice_number ?: '-';
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : 'Walk-in Customer';
                    })
                    ->addColumn('sale_date', function ($row) {
                        return $row->sale_date->format('d M Y');
                    })
                    ->addColumn('total_amount', function ($row) {
                        return number_format($row->total_amount, 2);
                    })
                    ->addColumn('payment_status', function ($row) {
                        $badgeClass = match($row->payment_status) {
                            'PAID' => 'bg-success',
                            'PARTIAL' => 'bg-warning',
                            'UNPAID' => 'bg-danger',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->payment_status . '</span>';
                    })
                    ->addColumn('sale_status', function ($row) {
                        $badgeClass = match($row->sale_status) {
                            'COMPLETED' => 'bg-success',
                            'CANCELLED' => 'bg-danger',
                            'RETURNED' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->sale_status . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $showUrl = route('sales.show', $row->id);
                        $editUrl = route('sales.edit', $row->id);
                        $deleteUrl = route('sales.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$showUrl.'"><i class="ti ti-eye me-1"></i> View</a>
                                    <a class="dropdown-item" href="'.$editUrl.'"><i class="ti ti-pencil me-1"></i> Edit</a>
                                    <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                        '.$csrf.$method.'
                                        <button type="submit" class="dropdown-item"><i class="ti ti-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['payment_status', 'sale_status', 'action'])
                    ->make(true);
            }

            return view('sales.index');
        } catch (Exception $e) {
            Log::error('Error in SaleController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load sales data.'
                ], 500);
            }

            return redirect()->route('sales.index')
                ->with('error', 'An error occurred while loading sales. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = $this->customerService->getCustomersForTenant();
        $products = $this->productService->getProductsForTenant();
        
        return view('sales.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'sale_date' => 'required|date',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:CASH,CARD,BKASH,NAGAD,BANK,EMI,MIXED',
                'sale_type' => 'required|in:RETAIL,WHOLESALE,EMI',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.total_price' => 'required|numeric|min:0',
            ]);

            $items = $request->input('items');
            $this->saleService->createSale($validated, $items);

            return redirect()->route('sales.index')
                ->with('success', 'Sale created successfully.');
        } catch (Exception $e) {
            Log::error('Error in SaleController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create sale: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale): View
    {
        $sale->load(['items.product', 'customer', 'soldBy']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale): View
    {
        $sale->load('items');
        $customers = $this->customerService->getCustomersForTenant();
        $products = $this->productService->getProductsForTenant();
        
        return view('sales.edit', compact('sale', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'sale_date' => 'required|date',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'paid_amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:CASH,CARD,BKASH,NAGAD,BANK,EMI,MIXED',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
            ]);

            $items = $request->input('items');
            $this->saleService->updateSale($sale, $validated, $items);

            return redirect()->route('sales.index')
                ->with('success', 'Sale updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in SaleController@update', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update sale: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        try {
            $this->saleService->deleteSale($sale);

            return redirect()->route('sales.index')
                ->with('success', 'Sale deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in SaleController@destroy', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('sales.index')
                ->with('error', 'Failed to delete sale: ' . $e->getMessage());
        }
    }
}
