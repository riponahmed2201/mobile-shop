<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\Quotation;
use App\Services\Sales\QuotationService;
use App\Services\CustomerService;
use App\Services\Inventory\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    protected QuotationService $quotationService;
    protected CustomerService $customerService;
    protected ProductService $productService;

    public function __construct(
        QuotationService $quotationService,
        CustomerService $customerService,
        ProductService $productService
    ) {
        $this->quotationService = $quotationService;
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
                $query = $this->quotationService->getQuotationsQuery();

                return DataTables::of($query)
                    ->addColumn('quotation_number', function ($row) {
                        return $row->quotation_number;
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : '-';
                    })
                    ->addColumn('quotation_date', function ($row) {
                        return $row->quotation_date->format('d M Y');
                    })
                    ->addColumn('total_amount', function ($row) {
                        return number_format($row->total_amount, 2);
                    })
                    ->addColumn('status', function ($row) {
                        $badgeClass = match($row->status) {
                            'DRAFT' => 'bg-secondary',
                            'SENT' => 'bg-info',
                            'ACCEPTED' => 'bg-success',
                            'REJECTED' => 'bg-danger',
                            'EXPIRED' => 'bg-warning',
                            'CONVERTED' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->status . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $showUrl = route('quotations.show', $row->id);
                        $editUrl = route('quotations.edit', $row->id);
                        $convertUrl = route('quotations.convert', $row->id);
                        $deleteUrl = route('quotations.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        $actions = '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$showUrl.'"><i class="ti ti-eye me-1"></i> View</a>';
                        
                        if ($row->status !== 'CONVERTED') {
                            $actions .= '<a class="dropdown-item" href="'.$editUrl.'"><i class="ti ti-pencil me-1"></i> Edit</a>';
                            $actions .= '<form action="'.$convertUrl.'" method="POST" style="display:inline;">
                                            '.$csrf.'
                                            <button type="submit" class="dropdown-item"><i class="ti ti-check me-1"></i> Convert to Sale</button>
                                        </form>';
                            $actions .= '<form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                            '.$csrf.$method.'
                                            <button type="submit" class="dropdown-item"><i class="ti ti-trash me-1"></i> Delete</button>
                                        </form>';
                        }
                        
                        $actions .= '</div></div>';
                        return $actions;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('quotations.index');
        } catch (Exception $e) {
            Log::error('Error in QuotationController@index', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to load quotations data.'], 500);
            }

            return redirect()->route('quotations.index')
                ->with('error', 'An error occurred while loading quotations.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = $this->customerService->getCustomersForTenant();
        $products = $this->productService->getProductsForTenant();
        
        return view('quotations.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'quotation_date' => 'required|date',
                'valid_until_date' => 'nullable|date|after:quotation_date',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'terms_conditions' => 'nullable|string',
                'items' => 'required|array|min:1',
            ]);

            $items = $request->input('items');
            $this->quotationService->createQuotation($validated, $items);

            return redirect()->route('quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (Exception $e) {
            Log::error('Error in QuotationController@store', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation): View
    {
        $quotation->load(['items.product', 'customer', 'creator']);
        return view('quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation): View
    {
        $quotation->load('items');
        $customers = $this->customerService->getCustomersForTenant();
        $products = $this->productService->getProductsForTenant();
        
        return view('quotations.edit', compact('quotation', 'customers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'quotation_date' => 'required|date',
                'valid_until_date' => 'nullable|date',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
            ]);

            $items = $request->input('items');
            $this->quotationService->updateQuotation($quotation, $validated, $items);

            return redirect()->route('quotations.index')
                ->with('success', 'Quotation updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in QuotationController@update', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update quotation: ' . $e->getMessage());
        }
    }

    /**
     * Convert quotation to sale
     */
    public function convertToSale(Request $request, Quotation $quotation): RedirectResponse
    {
        try {
            $saleData = [
                'payment_method' => $request->input('payment_method', 'CASH'),
                'paid_amount' => $request->input('paid_amount', 0),
                'sale_type' => 'RETAIL',
            ];

            $sale = $this->quotationService->convertToSale($quotation, $saleData);

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Quotation converted to sale successfully.');
        } catch (Exception $e) {
            Log::error('Error in QuotationController@convertToSale', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to convert quotation: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation): RedirectResponse
    {
        try {
            $this->quotationService->deleteQuotation($quotation);

            return redirect()->route('quotations.index')
                ->with('success', 'Quotation deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in QuotationController@destroy', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('quotations.index')
                ->with('error', 'Failed to delete quotation: ' . $e->getMessage());
        }
    }
}
