<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase\Supplier;
use App\Services\Purchase\SupplierService;
use App\Http\Requests\Purchase\Supplier\StoreSupplierRequest;
use App\Http\Requests\Purchase\Supplier\UpdateSupplierRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->supplierService->getSuppliersQuery();

                return DataTables::of($query)
                    ->addColumn('contact_info', function ($row) {
                        $info = '<div class="d-flex flex-column">';
                        $info .= '<small><i class="ti tabler-phone me-1"></i>' . $row->mobile . '</small>';
                        if ($row->email) {
                            $info .= '<small><i class="ti tabler-mail me-1"></i>' . $row->email . '</small>';
                        }
                        $info .= '</div>';
                        return $info;
                    })
                    ->addColumn('purchase_info', function ($row) {
                        $info = '<div class="d-flex flex-column">';
                        $info .= '<small><strong>Total: ৳' . number_format($row->total_purchases, 2) . '</strong></small>';
                        $info .= '<small>Pending Orders: ' . $row->pending_orders_count . '</small>';
                        $info .= '</div>';
                        return $info;
                    })
                    ->addColumn('credit_info', function ($row) {
                        $info = '<div class="d-flex flex-column">';
                        $info .= '<small>Limit: ৳' . number_format($row->credit_limit, 2) . '</small>';
                        $info .= '<small>Balance: ৳' . number_format($row->outstanding_balance, 2) . '</small>';
                        $available = $row->available_credit;
                        $badgeClass = $available > 0 ? 'bg-success' : 'bg-danger';
                        $info .= '<small class="badge ' . $badgeClass . '">Available: ৳' . number_format($available, 2) . '</small>';
                        $info .= '</div>';
                        return $info;
                    })
                    ->addColumn('is_active', function ($row) {
                        return $row->is_active
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('suppliers.edit', $row->id);
                        $deleteUrl = route('suppliers.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');

                        $actions = '<div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">';

                        $actions .= '<a class="dropdown-item" href="' . route('suppliers.show', $row->id) . '"><i class="ti tabler-eye me-1"></i> View</a>';
                        $actions .= '<a class="dropdown-item" href="' . $editUrl . '"><i class="ti tabler-pencil me-1"></i> Edit</a>';

                        // Toggle active/inactive
                        if ($row->is_active) {
                            $actions .= '<a class="dropdown-item text-warning" href="#" onclick="toggleStatus(' . $row->id . ', false)"><i class="ti tabler-eye-off me-1"></i> Deactivate</a>';
                        } else {
                            $actions .= '<a class="dropdown-item text-success" href="#" onclick="toggleStatus(' . $row->id . ', true)"><i class="ti tabler-eye me-1"></i> Activate</a>';
                        }

                        // Only show delete if no purchase orders exist
                        if ($row->purchaseOrders()->count() == 0) {
                            $actions .= '<form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this supplier?\');" style="display:inline;">
                                ' . $csrf . $method . '
                                <button type="submit" class="dropdown-item text-danger"><i class="ti tabler-trash me-1"></i> Delete</button>
                            </form>';
                        }

                        $actions .= '</div></div>';

                        return $actions;
                    })
                    ->rawColumns(['action', 'contact_info', 'purchase_info', 'credit_info', 'is_active'])
                    ->make(true);
            }

            // Get statistics for dashboard
            $statistics = $this->supplierService->getSupplierStatistics();

            return view('suppliers.index', compact('statistics'));
        } catch (Exception $e) {
            Log::error('Error in SupplierController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load suppliers data.'
                ], 500);
            }

            return redirect()->route('dashboard')
                ->with('error', 'An error occurred while loading suppliers. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            return view('suppliers.create');
        } catch (Exception $e) {
            Log::error('Error in SupplierController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('suppliers.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->supplierService->createSupplier($validated);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        } catch (Exception $e) {
            Log::error('Error in SupplierController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create supplier: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): View|RedirectResponse
    {
        try {
            // Load related data
            $supplier->load(['purchaseOrders' => function($query) {
                $query->with('items.product')->orderBy('po_date', 'desc');
            }]);

            return view('suppliers.show', compact('supplier'));
        } catch (Exception $e) {
            Log::error('Error in SupplierController@show', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('suppliers.index')
                ->with('error', 'An error occurred while loading supplier details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier): View|RedirectResponse
    {
        try {
            return view('suppliers.edit', compact('supplier'));
        } catch (Exception $e) {
            Log::error('Error in SupplierController@edit', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('suppliers.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->supplierService->updateSupplier($supplier, $validated);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in SupplierController@update', [
                'supplier_id' => $supplier->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update supplier: ' . $e->getMessage());
        }
    }

    /**
     * Toggle supplier active status
     */
    public function toggleStatus(Supplier $supplier): RedirectResponse
    {
        try {
            $this->supplierService->toggleSupplierStatus($supplier);

            $status = $supplier->is_active ? 'activated' : 'deactivated';

            return redirect()->route('suppliers.index')
                ->with('success', "Supplier {$status} successfully.");
        } catch (Exception $e) {
            Log::error('Error toggling supplier status', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('suppliers.index')
                ->with('error', 'Failed to update supplier status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            $this->supplierService->deleteSupplier($supplier);

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in SupplierController@destroy', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('suppliers.index')
                ->with('error', 'Failed to delete supplier: ' . $e->getMessage());
        }
    }
}
