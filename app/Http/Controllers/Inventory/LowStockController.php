<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Services\Inventory\LowStockService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class LowStockController extends Controller
{
    protected LowStockService $lowStockService;

    public function __construct(LowStockService $lowStockService)
    {
        $this->lowStockService = $lowStockService;
    }

    /**
     * Display a listing of low stock products
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->lowStockService->getLowStockQuery();

                return DataTables::of($query)
                    ->addColumn('product_name', function ($row) {
                        return $row->product_name;
                    })
                    ->addColumn('brand_name', function ($row) {
                        return $row->brand ? $row->brand->brand_name : '-';
                    })
                    ->addColumn('category_name', function ($row) {
                        return $row->category ? $row->category->category_name : '-';
                    })
                    ->addColumn('stock_info', function ($row) {
                        $stockStatus = $this->lowStockService->getProductStockStatus($row);
                        $badgeClass = match($stockStatus['status']) {
                            'OUT_OF_STOCK' => 'bg-danger',
                            'CRITICAL' => 'bg-danger',
                            'LOW' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        $statusText = match($stockStatus['status']) {
                            'OUT_OF_STOCK' => 'Out of Stock',
                            'CRITICAL' => 'Critical',
                            'LOW' => 'Low Stock',
                            default => 'Normal'
                        };

                        return '
                            <div class="d-flex flex-column">
                                <span class="badge ' . $badgeClass . ' mb-1">' . $statusText . '</span>
                                <small class="text-muted">
                                    Current: ' . $row->current_stock . ' ' . $row->unit . '<br>
                                    Min: ' . $row->min_stock_level . ' | Reorder: ' . $row->reorder_level . '
                                </small>
                            </div>
                        ';
                    })
                    ->addColumn('suggested_action', function ($row) {
                        $stockStatus = $this->lowStockService->getProductStockStatus($row);

                        if ($stockStatus['status'] === 'OUT_OF_STOCK') {
                            return '<span class="text-danger fw-bold">Urgent: Restock Immediately</span>';
                        } elseif ($stockStatus['status'] === 'CRITICAL') {
                            return '<span class="text-danger">Reorder: ' . $stockStatus['suggested_reorder'] . ' ' . $row->unit . '</span>';
                        } elseif ($stockStatus['status'] === 'LOW') {
                            return '<span class="text-warning">Consider Reordering</span>';
                        }

                        return '<span class="text-success">Stock OK</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route('products.show', $row->id);
                        $editUrl = route('products.edit', $row->id);

                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="' . $viewUrl . '"><i class="ti tabler-eye me-1"></i> View Product</a>
                                    <a class="dropdown-item" href="' . $editUrl . '"><i class="ti tabler-pencil me-1"></i> Edit Product</a>
                                    <a class="dropdown-item" href="#" onclick="showStockDetails(' . $row->id . ')"><i class="ti tabler-info-circle me-1"></i> Stock Details</a>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['action', 'stock_info', 'suggested_action'])
                    ->make(true);
            }

            // Get statistics for dashboard
            $statistics = $this->lowStockService->getLowStockStatistics();

            return view('low-stock.index', compact('statistics'));
        } catch (Exception $e) {
            Log::error('Error in LowStockController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load low stock data.'
                ], 500);
            }

            return redirect()->route('dashboard')
                ->with('error', 'An error occurred while loading low stock alerts. Please try again.');
        }
    }

    /**
     * Get detailed stock information for a product
     */
    public function getStockDetails(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id'
            ]);

            $product = \App\Models\Inventory\Product::with('brand', 'category')
                ->where('id', $request->product_id)
                ->where('tenant_id', auth()->user()->tenant_id ?? 1)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }

            $stockDetails = $this->lowStockService->getProductStockStatus($product);

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                    'stock_details' => $stockDetails,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error getting stock details', [
                'product_id' => $request->product_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load stock details.'
            ], 500);
        }
    }

    /**
     * Get low stock statistics for dashboard
     */
    public function getStatistics()
    {
        try {
            $statistics = $this->lowStockService->getLowStockStatistics();

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);
        } catch (Exception $e) {
            Log::error('Error getting low stock statistics', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics.'
            ], 500);
        }
    }

    /**
     * Show critical stock alerts (below minimum level)
     */
    public function critical(Request $request): View
    {
        try {
            $criticalProducts = $this->lowStockService->getCriticalStockProducts();

            return view('low-stock.critical', compact('criticalProducts'));
        } catch (Exception $e) {
            Log::error('Error in LowStockController@critical', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('low-stock.index')
                ->with('error', 'An error occurred while loading critical stock alerts.');
        }
    }

    /**
     * Show out of stock products
     */
    public function outOfStock(Request $request): View
    {
        try {
            $outOfStockProducts = $this->lowStockService->getOutOfStockProducts();

            return view('low-stock.out-of-stock', compact('outOfStockProducts'));
        } catch (Exception $e) {
            Log::error('Error in LowStockController@outOfStock', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('low-stock.index')
                ->with('error', 'An error occurred while loading out of stock products.');
        }
    }
}
