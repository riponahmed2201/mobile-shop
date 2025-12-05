<?php

namespace App\Http\Controllers\Repair;

use App\Http\Controllers\Controller;
use App\Models\Repair\RepairPartCatalog;
use App\Models\Purchase\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RepairPartCatalogController extends Controller
{
    /**
     * Display a listing of repair parts catalog
     */
    public function index(Request $request): View
    {
        $query = RepairPartCatalog::forTenant()->with(['primarySupplier']);

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low_stock':
                    $query->where('current_stock', '<=', DB::raw('min_stock_level'));
                    break;
                case 'out_of_stock':
                    $query->where('current_stock', '<=', 0);
                    break;
                case 'reorder':
                    $query->whereRaw('current_stock <= reorder_level');
                    break;
            }
        }

        if ($request->filled('supplier')) {
            $query->where('primary_supplier_id', $request->supplier);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('part_name', 'like', "%{$search}%")
                  ->orWhere('part_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $parts = $query->orderBy('part_name')->paginate(15);

        // Get filter options
        $categories = RepairPartCatalog::forTenant()->distinct()->pluck('category')->filter()->sort();
        $brands = RepairPartCatalog::forTenant()->distinct()->pluck('brand')->filter()->sort();
        $suppliers = Supplier::where('tenant_id', auth()->user()->tenant_id ?? 1)->active()->orderBy('supplier_name')->get();

        // Get statistics
        $statistics = $this->getCatalogStatistics();

        return view('repair-parts.index', compact('parts', 'categories', 'brands', 'suppliers', 'statistics'));
    }

    /**
     * Show the form for creating a new repair part
     */
    public function create(): View|RedirectResponse
    {
        try {
            $suppliers = Supplier::where('tenant_id', auth()->user()->tenant_id ?? 1)
                ->active()
                ->orderBy('supplier_name')
                ->get();

            $categories = RepairPartCatalog::forTenant()
                ->distinct()
                ->pluck('category')
                ->filter()
                ->sort();

            return view('repair-parts.create', compact('suppliers', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in RepairPartCatalogController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('repair-parts.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created repair part
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'part_name' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'compatible_devices' => 'nullable|array',
            'current_stock' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'primary_supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_part_code' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'bin_location' => 'nullable|string|max:50',
        ]);

        try {
            $data = $request->all();
            $data['tenant_id'] = auth()->user()->tenant_id ?? 1;
            $data['part_code'] = RepairPartCatalog::generatePartCode();
            $data['created_by'] = auth()->id();

            RepairPartCatalog::create($data);

            return redirect()->route('repair-parts.index')
                ->with('success', 'Repair part added to catalog successfully.');
        } catch (\Exception $e) {
            Log::error('Error in RepairPartCatalogController@store', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add repair part. Please try again.');
        }
    }

    /**
     * Display the specified repair part
     */
    public function show(RepairPartCatalog $repairPart): View|RedirectResponse
    {
        try {
            $this->authorize('view', $repairPart);

            $repairPart->load(['primarySupplier', 'repairParts.repairTicket.customer']);

            // Get usage statistics
            $usageStats = [
                'total_used' => $repairPart->repairParts->sum('quantity'),
                'repairs_count' => $repairPart->repairParts->pluck('repair_ticket_id')->unique()->count(),
                'recent_repairs' => $repairPart->repairParts->take(5)->map(function ($repairPart) {
                    return [
                        'ticket_number' => $repairPart->repairTicket->ticket_number,
                        'customer' => $repairPart->repairTicket->customer->full_name,
                        'quantity' => $repairPart->quantity,
                        'date' => $repairPart->created_at->format('d M Y'),
                    ];
                }),
            ];

            return view('repair-parts.show', compact('repairPart', 'usageStats'));
        } catch (\Exception $e) {
            Log::error('Error in RepairPartCatalogController@show', [
                'part_id' => $repairPart->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('repair-parts.index')
                ->with('error', 'Unable to load repair part details.');
        }
    }

    /**
     * Show the form for editing the specified repair part
     */
    public function edit(RepairPartCatalog $repairPart): View|RedirectResponse
    {
        try {
            $this->authorize('update', $repairPart);

            $suppliers = Supplier::where('tenant_id', auth()->user()->tenant_id ?? 1)
                ->active()
                ->orderBy('supplier_name')
                ->get();

            $categories = RepairPartCatalog::forTenant()
                ->distinct()
                ->pluck('category')
                ->filter()
                ->sort();

            return view('repair-parts.edit', compact('repairPart', 'suppliers', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error in RepairPartCatalogController@edit', [
                'part_id' => $repairPart->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('repair-parts.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified repair part
     */
    public function update(Request $request, RepairPartCatalog $repairPart): RedirectResponse
    {
        $request->validate([
            'part_name' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'subcategory' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'compatible_devices' => 'nullable|array',
            'current_stock' => 'nullable|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'primary_supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_part_code' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'bin_location' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'is_discontinued' => 'boolean',
        ]);

        try {
            $this->authorize('update', $repairPart);

            $data = $request->all();
            $data['updated_by'] = auth()->id();

            $repairPart->update($data);

            return redirect()->route('repair-parts.show', $repairPart)
                ->with('success', 'Repair part updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in RepairPartCatalogController@update', [
                'part_id' => $repairPart->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update repair part. Please try again.');
        }
    }

    /**
     * Remove the specified repair part
     */
    public function destroy(RepairPartCatalog $repairPart): RedirectResponse
    {
        try {
            $this->authorize('delete', $repairPart);

            // Check if part is used in any repair tickets
            if ($repairPart->repairParts()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete repair part that has been used in repairs.');
            }

            $repairPart->delete();

            return redirect()->route('repair-parts.index')
                ->with('success', 'Repair part deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in RepairPartCatalogController@destroy', [
                'part_id' => $repairPart->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete repair part. Please try again.');
        }
    }

    /**
     * Update stock level
     */
    public function updateStock(Request $request, RepairPartCatalog $repairPart): JsonResponse
    {
        $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->authorize('update', $repairPart);

            $newStock = $repairPart->current_stock + $request->adjustment;

            if ($newStock < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock adjustment would result in negative stock level.'
                ], 422);
            }

            $repairPart->update([
                'current_stock' => $newStock,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully.',
                'new_stock' => $newStock,
                'stock_status' => $repairPart->fresh()->stock_status_label,
                'badge_class' => $repairPart->stock_status_badge_class
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating stock', [
                'part_id' => $repairPart->id,
                'adjustment' => $request->adjustment,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update stock. Please try again.'
            ], 500);
        }
    }

    /**
     * Get catalog statistics
     */
    private function getCatalogStatistics(): array
    {
        $tenantId = auth()->user()->tenant_id ?? 1;

        return [
            'total_parts' => RepairPartCatalog::forTenant($tenantId)->count(),
            'active_parts' => RepairPartCatalog::forTenant($tenantId)->active()->count(),
            'low_stock_parts' => RepairPartCatalog::forTenant($tenantId)->lowStock()->count(),
            'out_of_stock_parts' => RepairPartCatalog::forTenant($tenantId)->outOfStock()->count(),
            'total_value' => RepairPartCatalog::forTenant($tenantId)->sum(DB::raw('current_stock * COALESCE(purchase_price, 0)')),
            'categories_count' => RepairPartCatalog::forTenant($tenantId)->distinct('category')->count('category'),
        ];
    }
}
