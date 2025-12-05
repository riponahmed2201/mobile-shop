<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StockAdjustment;
use App\Services\Inventory\StockAdjustmentService;
use App\Http\Requests\Inventory\StockAdjustment\StoreStockAdjustmentRequest;
use App\Http\Requests\Inventory\StockAdjustment\UpdateStockAdjustmentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class StockAdjustmentController extends Controller
{
    protected StockAdjustmentService $stockAdjustmentService;

    public function __construct(StockAdjustmentService $stockAdjustmentService)
    {
        $this->stockAdjustmentService = $stockAdjustmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->stockAdjustmentService->getStockAdjustmentsQuery();

                return DataTables::of($query)
                    ->addColumn('product_name', function ($row) {
                        return $row->product ? $row->product->product_name : '-';
                    })
                    ->addColumn('brand_name', function ($row) {
                        return $row->product && $row->product->brand ? $row->product->brand->brand_name : '-';
                    })
                    ->addColumn('adjustment_type_label', function ($row) {
                        $badgeClass = match($row->adjustment_type) {
                            'ADD' => 'bg-success',
                            'REMOVE' => 'bg-danger',
                            'DAMAGED' => 'bg-warning',
                            'LOST' => 'bg-danger',
                            'FOUND' => 'bg-info',
                            'RETURN' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->adjustment_type_label . '</span>';
                    })
                    ->addColumn('signed_quantity', function ($row) {
                        $colorClass = str_starts_with($row->signed_quantity, '+') ? 'text-success' : 'text-danger';
                        return '<span class="' . $colorClass . '">' . $row->signed_quantity . '</span>';
                    })
                    ->addColumn('adjusted_by_name', function ($row) {
                        return $row->adjustedBy ? $row->adjustedBy->username : '-';
                    })
                    ->addColumn('adjustment_date_formatted', function ($row) {
                        return $row->adjustment_date ? $row->adjustment_date->format('d M Y H:i') : '-';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('stock-adjustments.edit', $row->id);
                        $deleteUrl = route('stock-adjustments.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');

                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="' . route('stock-adjustments.show', $row->id) . '"><i class="ti tabler-eye me-1"></i> View</a>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="ti tabler-pencil me-1"></i> Edit</a>
                                    <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this stock adjustment? This will reverse the stock change.\');" style="display:inline;">
                                        ' . $csrf . $method . '
                                        <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['action', 'adjustment_type_label', 'signed_quantity'])
                    ->make(true);
            }

            return view('stock-adjustments.index');
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load stock adjustment data.'
                ], 500);
            }

            return redirect()->route('stock-adjustments.index')
                ->with('error', 'An error occurred while loading stock adjustment data. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $products = $this->stockAdjustmentService->getProductsForDropdown();

            return view('stock-adjustments.create', compact('products'));
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-adjustments.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockAdjustmentRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->stockAdjustmentService->createStockAdjustment($validated);

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment created successfully.');
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockAdjustment $stockAdjustment): View|RedirectResponse
    {
        try {
            return view('stock-adjustments.show', compact('stockAdjustment'));
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@show', [
                'adjustment_id' => $stockAdjustment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-adjustments.index')
                ->with('error', 'An error occurred while loading stock adjustment details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockAdjustment $stockAdjustment): View|RedirectResponse
    {
        try {
            $products = $this->stockAdjustmentService->getProductsForDropdown();

            return view('stock-adjustments.edit', compact('stockAdjustment', 'products'));
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@edit', [
                'adjustment_id' => $stockAdjustment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-adjustments.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockAdjustmentRequest $request, StockAdjustment $stockAdjustment): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->stockAdjustmentService->updateStockAdjustment($stockAdjustment, $validated);

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@update', [
                'adjustment_id' => $stockAdjustment->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update stock adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockAdjustment $stockAdjustment): RedirectResponse
    {
        try {
            $this->stockAdjustmentService->deleteStockAdjustment($stockAdjustment);

            return redirect()->route('stock-adjustments.index')
                ->with('success', 'Stock adjustment deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in StockAdjustmentController@destroy', [
                'adjustment_id' => $stockAdjustment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-adjustments.index')
                ->with('error', 'Failed to delete stock adjustment: ' . $e->getMessage());
        }
    }
}
