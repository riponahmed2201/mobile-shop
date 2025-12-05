<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\ProductImei;
use App\Models\Inventory\Product;
use App\Services\Inventory\ImeiService;
use App\Http\Requests\Inventory\Imei\StoreImeiRequest;
use App\Http\Requests\Inventory\Imei\UpdateImeiRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class ImeiController extends Controller
{
    protected ImeiService $imeiService;

    public function __construct(ImeiService $imeiService)
    {
        $this->imeiService = $imeiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->imeiService->getImeiQuery();

                return DataTables::of($query)
                    ->addColumn('product_name', function ($row) {
                        return $row->product ? $row->product->product_name : '-';
                    })
                    ->addColumn('model_name', function ($row) {
                        return $row->product ? $row->product->model_name : '-';
                    })
                    ->addColumn('brand_name', function ($row) {
                        return $row->product && $row->product->brand ? $row->product->brand->brand_name : '-';
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : '-';
                    })
                    ->addColumn('status', function ($row) {
                        $badgeClass = match($row->status) {
                            'IN_STOCK' => 'bg-success',
                            'SOLD' => 'bg-primary',
                            'DEFECTIVE' => 'bg-danger',
                            'RETURNED' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . str_replace('_', ' ', $row->status) . '</span>';
                    })
                    ->addColumn('warranty_status', function ($row) {
                        if (!$row->warranty_expiry_date) {
                            return '<span class="text-muted">No Warranty</span>';
                        }

                        $daysLeft = now()->diffInDays($row->warranty_expiry_date, false);

                        if ($daysLeft < 0) {
                            return '<span class="badge bg-danger">Expired</span>';
                        } elseif ($daysLeft <= 30) {
                            return '<span class="badge bg-warning">Expires Soon (' . $daysLeft . ' days)</span>';
                        } else {
                            return '<span class="badge bg-success">Active</span>';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('imei.edit', $row->id);
                        $deleteUrl = route('imei.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');

                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="' . route('imei.show', $row->id) . '"><i class="ti tabler-eye me-1"></i> View</a>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="ti tabler-pencil me-1"></i> Edit</a>
                                    <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                        ' . $csrf . $method . '
                                        <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['action', 'status', 'warranty_status'])
                    ->make(true);
            }

            return view('imei.index');
        } catch (Exception $e) {
            Log::error('Error in ImeiController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load IMEI data.'
                ], 500);
            }

            return redirect()->route('imei.index')
                ->with('error', 'An error occurred while loading IMEI data. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $products = $this->imeiService->getProductsForDropdown();

            return view('imei.create', compact('products'));
        } catch (Exception $e) {
            Log::error('Error in ImeiController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('imei.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImeiRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->imeiService->createImei($validated);

            return redirect()->route('imei.index')
                ->with('success', 'IMEI record created successfully.');
        } catch (Exception $e) {
            Log::error('Error in ImeiController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create IMEI record: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductImei $imei): View|RedirectResponse
    {
        try {
            return view('imei.show', compact('imei'));
        } catch (Exception $e) {
            Log::error('Error in ImeiController@show', [
                'imei_id' => $imei->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('imei.index')
                ->with('error', 'An error occurred while loading IMEI details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductImei $imei): View|RedirectResponse
    {
        try {
            $products = $this->imeiService->getProductsForDropdown();

            return view('imei.edit', compact('imei', 'products'));
        } catch (Exception $e) {
            Log::error('Error in ImeiController@edit', [
                'imei_id' => $imei->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('imei.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImeiRequest $request, ProductImei $imei): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->imeiService->updateImei($imei, $validated);

            return redirect()->route('imei.index')
                ->with('success', 'IMEI record updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in ImeiController@update', [
                'imei_id' => $imei->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update IMEI record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductImei $imei): RedirectResponse
    {
        try {
            $this->imeiService->deleteImei($imei);

            return redirect()->route('imei.index')
                ->with('success', 'IMEI record deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in ImeiController@destroy', [
                'imei_id' => $imei->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('imei.index')
                ->with('error', 'Failed to delete IMEI record: ' . $e->getMessage());
        }
    }
}
