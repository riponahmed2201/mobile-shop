<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase\PurchaseOrder;
use App\Services\Purchase\PurchaseOrderService;
use App\Services\Purchase\SupplierService;
use App\Http\Requests\Purchase\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\Purchase\PurchaseOrder\UpdatePurchaseOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $purchaseOrderService;
    protected SupplierService $supplierService;

    public function __construct(
        PurchaseOrderService $purchaseOrderService,
        SupplierService $supplierService
    ) {
        $this->purchaseOrderService = $purchaseOrderService;
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->purchaseOrderService->getPurchaseOrdersQuery();

                return DataTables::of($query)
                    ->addColumn('supplier_name', function ($row) {
                        return $row->supplier ? $row->supplier->supplier_name : '-';
                    })
                    ->addColumn('order_info', function ($row) {
                        $info = '<div class="d-flex flex-column">';
                        $info .= '<small><strong>' . $row->po_number . '</strong></small>';
                        $info .= '<small>Items: ' . $row->total_quantity . ' | Value: ৳' . number_format($row->total_amount, 2) . '</small>';
                        $info .= '</div>';
                        return $info;
                    })
                    ->addColumn('status_badge', function ($row) {
                        return '<span class="badge ' . $row->status_badge_class . '">' . $row->status_label . '</span>';
                    })
                    ->addColumn('payment_info', function ($row) {
                        $info = '<div class="d-flex flex-column">';
                        $info .= '<small><span class="badge ' . $row->payment_status_badge_class . '">' . $row->payment_status_label . '</span></small>';
                        $info .= '<small>Paid: ৳' . number_format($row->paid_amount, 2) . '</small>';
                        if ($row->due_amount > 0) {
                            $info .= '<small class="text-danger">Due: ৳' . number_format($row->due_amount, 2) . '</small>';
                        }
                        $info .= '</div>';
                        return $info;
                    })
                    ->addColumn('delivery_info', function ($row) {
                        $info = '<div class="d-flex flex-column">';
                        $info .= '<small>PO Date: ' . $row->po_date->format('d M Y') . '</small>';
                        if ($row->expected_delivery_date) {
                            $expected = $row->expected_delivery_date;
                            $today = now()->startOfDay();
                            $isOverdue = $expected < $today && $row->order_status !== 'RECEIVED';

                            $badgeClass = $isOverdue ? 'bg-danger' : 'bg-info';
                            $info .= '<small><span class="badge ' . $badgeClass . '">Expected: ' . $expected->format('d M Y') . '</span></small>';
                        }
                        if ($row->order_status === 'RECEIVED') {
                            $info .= '<small class="text-success"><i class="ti tabler-check me-1"></i>Received</small>';
                        }
                        $info .= '</div>';
                        return $info;
                    })
                    ->addColumn('created_by_name', function ($row) {
                        return $row->createdBy ? $row->createdBy->username : '-';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route('purchase-orders.show', $row->id);
                        $editUrl = route('purchase-orders.edit', $row->id);
                        $deleteUrl = route('purchase-orders.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');

                        $actions = '<div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">';

                        $actions .= '<a class="dropdown-item" href="' . $viewUrl . '"><i class="ti tabler-eye me-1"></i> View Details</a>';

                        if ($row->can_edit) {
                            $actions .= '<a class="dropdown-item" href="' . $editUrl . '"><i class="ti tabler-pencil me-1"></i> Edit</a>';
                            $actions .= '<form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this purchase order?\');" style="display:inline;">
                                ' . $csrf . $method . '
                                <button type="submit" class="dropdown-item text-danger"><i class="ti tabler-trash me-1"></i> Delete</button>
                            </form>';
                        }

                        // Status update actions
                        if ($row->order_status === 'DRAFT') {
                            $actions .= '<div class="dropdown-divider"></div>';
                            $actions .= '<a class="dropdown-item" href="#" onclick="updateStatus(' . $row->id . ', \'CONFIRMED\')"><i class="ti tabler-send me-1"></i> Confirm Order</a>';
                        }

                        if ($row->can_receive) {
                            $actions .= '<div class="dropdown-divider"></div>';
                            $actions .= '<a class="dropdown-item" href="#" onclick="updateStatus(' . $row->id . ', \'RECEIVED\')"><i class="ti tabler-package me-1"></i> Mark as Received</a>';
                        }

                        if (in_array($row->order_status, ['DRAFT', 'CONFIRMED'])) {
                            $actions .= '<a class="dropdown-item text-danger" href="#" onclick="updateStatus(' . $row->id . ', \'CANCELLED\')"><i class="ti tabler-x me-1"></i> Cancel Order</a>';
                        }

                        $actions .= '</div></div>';

                        return $actions;
                    })
                    ->rawColumns(['action', 'order_info', 'status_badge', 'payment_info', 'delivery_info'])
                    ->make(true);
            }

            // Get statistics for dashboard
            $statistics = $this->purchaseOrderService->getPurchaseOrderStatistics();

            return view('purchase-orders.index', compact('statistics'));
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load purchase orders data.'
                ], 500);
            }

            return redirect()->route('dashboard')
                ->with('error', 'An error occurred while loading purchase orders. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $suppliers = $this->supplierService->getActiveSuppliersForDropdown();
            $products = $this->purchaseOrderService->getProductsForDropdown();

            return view('purchase-orders.create', compact('suppliers', 'products'));
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('purchase-orders.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseOrderRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $purchaseOrder = $this->purchaseOrderService->createPurchaseOrder($validated);

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('success', 'Purchase order created successfully.');
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $purchaseOrder): View|RedirectResponse
    {
        try {
            return view('purchase-orders.show', compact('purchaseOrder'));
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@show', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('purchase-orders.index')
                ->with('error', 'An error occurred while loading purchase order details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder): View|RedirectResponse
    {
        try {
            // Only allow editing if status is DRAFT
            if ($purchaseOrder->order_status !== 'DRAFT') {
                return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                    ->with('error', 'Only draft purchase orders can be edited.');
            }

            $suppliers = $this->supplierService->getActiveSuppliersForDropdown();
            $products = $this->purchaseOrderService->getProductsForDropdown();

            return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@edit', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('purchase-orders.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->purchaseOrderService->updatePurchaseOrder($purchaseOrder, $validated);

            return redirect()->route('purchase-orders.show', $purchaseOrder->id)
                ->with('success', 'Purchase order updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@update', [
                'po_id' => $purchaseOrder->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Update purchase order status
     */
    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $request->validate([
                'status' => 'required|in:DRAFT,CONFIRMED,RECEIVED,CANCELLED'
            ]);

            $this->purchaseOrderService->updatePurchaseOrderStatus($purchaseOrder, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Purchase order status updated successfully.',
                'status' => $purchaseOrder->fresh()->status_label,
                'badge_class' => $purchaseOrder->fresh()->status_badge_class
            ]);
        } catch (Exception $e) {
            Log::error('Error updating purchase order status', [
                'po_id' => $purchaseOrder->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update purchase order status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrderService->deletePurchaseOrder($purchaseOrder);

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase order deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in PurchaseOrderController@destroy', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('purchase-orders.index')
                ->with('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }
}
