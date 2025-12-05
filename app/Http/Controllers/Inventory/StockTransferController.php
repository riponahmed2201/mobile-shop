<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\StockTransfer;
use App\Services\Inventory\StockTransferService;
use App\Http\Requests\Inventory\StockTransfer\StoreStockTransferRequest;
use App\Http\Requests\Inventory\StockTransfer\UpdateStockTransferRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class StockTransferController extends Controller
{
    protected StockTransferService $stockTransferService;

    public function __construct(StockTransferService $stockTransferService)
    {
        $this->stockTransferService = $stockTransferService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->stockTransferService->getStockTransfersQuery();

                return DataTables::of($query)
                    ->addColumn('transfer_details', function ($row) {
                        return $row->from_location . ' → ' . $row->to_location;
                    })
                    ->addColumn('total_items', function ($row) {
                        return $row->items->count();
                    })
                    ->addColumn('total_quantity', function ($row) {
                        return $row->total_quantity;
                    })
                    ->addColumn('status_badge', function ($row) {
                        return '<span class="badge ' . $row->status_badge_class . '">' . $row->status_label . '</span>';
                    })
                    ->addColumn('transferred_by_name', function ($row) {
                        return $row->transferredBy ? $row->transferredBy->username : '-';
                    })
                    ->addColumn('transfer_date_formatted', function ($row) {
                        return $row->transfer_date->format('d M Y');
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('stock-transfers.edit', $row->id);
                        $deleteUrl = route('stock-transfers.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');

                        $actions = '<div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="ti tabler-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">';

                        $actions .= '<a class="dropdown-item" href="' . route('stock-transfers.show', $row->id) . '"><i class="ti tabler-eye me-1"></i> View</a>';

                        if ($row->status === 'PENDING') {
                            $actions .= '<a class="dropdown-item" href="' . $editUrl . '"><i class="ti tabler-pencil me-1"></i> Edit</a>';
                            $actions .= '<form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure you want to delete this transfer? This will restore the stock levels.\');" style="display:inline;">
                                ' . $csrf . $method . '
                                <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                            </form>';
                        }

                        // Status update actions
                        if ($row->status === 'PENDING') {
                            $actions .= '<div class="dropdown-divider"></div>';
                            $actions .= '<a class="dropdown-item" href="#" onclick="updateStatus(' . $row->id . ', \'IN_TRANSIT\')"><i class="ti tabler-truck me-1"></i> Mark In Transit</a>';
                        }

                        if ($row->status === 'IN_TRANSIT') {
                            $actions .= '<div class="dropdown-divider"></div>';
                            $actions .= '<a class="dropdown-item" href="#" onclick="updateStatus(' . $row->id . ', \'COMPLETED\')"><i class="ti tabler-check me-1"></i> Mark Completed</a>';
                        }

                        if (in_array($row->status, ['PENDING', 'IN_TRANSIT'])) {
                            $actions .= '<a class="dropdown-item text-danger" href="#" onclick="updateStatus(' . $row->id . ', \'CANCELLED\')"><i class="ti tabler-x me-1"></i> Cancel Transfer</a>';
                        }

                        $actions .= '</div></div>';

                        return $actions;
                    })
                    ->rawColumns(['action', 'status_badge'])
                    ->make(true);
            }

            return view('stock-transfers.index');
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load stock transfer data.'
                ], 500);
            }

            return redirect()->route('stock-transfers.index')
                ->with('error', 'An error occurred while loading stock transfer data. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $products = $this->stockTransferService->getProductsForDropdown();
            $locations = $this->stockTransferService->getTransferLocations();

            return view('stock-transfers.create', compact('products', 'locations'));
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-transfers.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockTransferRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->stockTransferService->createStockTransfer($validated);

            return redirect()->route('stock-transfers.index')
                ->with('success', 'Stock transfer created successfully.');
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create stock transfer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StockTransfer $stockTransfer): View|RedirectResponse
    {
        try {
            return view('stock-transfers.show', compact('stockTransfer'));
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@show', [
                'transfer_id' => $stockTransfer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-transfers.index')
                ->with('error', 'An error occurred while loading stock transfer details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockTransfer $stockTransfer): View|RedirectResponse
    {
        try {
            // Only allow editing if status is PENDING
            if ($stockTransfer->status !== 'PENDING') {
                return redirect()->route('stock-transfers.show', $stockTransfer->id)
                    ->with('error', 'Only pending transfers can be edited.');
            }

            $products = $this->stockTransferService->getProductsForDropdown();
            $locations = $this->stockTransferService->getTransferLocations();

            return view('stock-transfers.edit', compact('stockTransfer', 'products', 'locations'));
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@edit', [
                'transfer_id' => $stockTransfer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-transfers.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockTransferRequest $request, StockTransfer $stockTransfer): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->stockTransferService->updateStockTransfer($stockTransfer, $validated);

            return redirect()->route('stock-transfers.index')
                ->with('success', 'Stock transfer updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@update', [
                'transfer_id' => $stockTransfer->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update stock transfer: ' . $e->getMessage());
        }
    }

    /**
     * Update transfer status via AJAX
     */
    public function updateStatus(Request $request, StockTransfer $stockTransfer)
    {
        try {
            $request->validate([
                'status' => 'required|in:PENDING,IN_TRANSIT,COMPLETED,CANCELLED'
            ]);

            $this->stockTransferService->updateTransferStatus($stockTransfer, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Transfer status updated successfully.',
                'status' => $stockTransfer->fresh()->status_label,
                'badge_class' => $stockTransfer->fresh()->status_badge_class
            ]);
        } catch (Exception $e) {
            Log::error('Error updating transfer status', [
                'transfer_id' => $stockTransfer->id,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update transfer status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockTransfer $stockTransfer): RedirectResponse
    {
        try {
            $this->stockTransferService->deleteStockTransfer($stockTransfer);

            return redirect()->route('stock-transfers.index')
                ->with('success', 'Stock transfer deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in StockTransferController@destroy', [
                'transfer_id' => $stockTransfer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('stock-transfers.index')
                ->with('error', 'Failed to delete stock transfer: ' . $e->getMessage());
        }
    }
}
