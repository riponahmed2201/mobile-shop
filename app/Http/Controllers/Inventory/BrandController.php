<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Brand;
use App\Http\Requests\Inventory\Brand\StoreBrandRequest;
use App\Http\Requests\Inventory\Brand\UpdateBrandRequest;
use App\Services\Inventory\BrandService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    protected BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->brandService->getBrandsQuery();

                return DataTables::of($query)
                    ->addColumn('logo', function ($row) {
                        if ($row->brand_logo_url) {
                            return '<img src="' . asset('storage/' . $row->brand_logo_url) . '" alt="Logo" style="max-height: 40px;">';
                        }
                        return '<span class="badge bg-label-secondary">No Logo</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('brands.edit', $row->id);
                        $deleteUrl = route('brands.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$editUrl.'"><i class="ti tabler-pencil me-1"></i> Edit</a>
                                    <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                        '.$csrf.$method.'
                                        <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['logo', 'action'])
                    ->make(true);
            }

            return view('brands.index');
        } catch (Exception $e) {
            Log::error('Error in BrandController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load brands data.'
                ], 500);
            }

            return redirect()->route('brands.index')
                ->with('error', 'An error occurred while loading brands. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $logo = $request->hasFile('brand_logo') ? $request->file('brand_logo') : null;

            $this->brandService->createBrand($validated, $logo);

            return redirect()->route('brands.index')
                ->with('success', 'Brand created successfully.');
        } catch (Exception $e) {
            Log::error('Error in BrandController@store', [
                'data' => $request->except(['brand_logo']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create brand: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand): View
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $logo = $request->hasFile('brand_logo') ? $request->file('brand_logo') : null;

            $this->brandService->updateBrand($brand, $validated, $logo);

            return redirect()->route('brands.index')
                ->with('success', 'Brand updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in BrandController@update', [
                'brand_id' => $brand->id,
                'data' => $request->except(['brand_logo']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        try {
            $this->brandService->deleteBrand($brand);

            return redirect()->route('brands.index')
                ->with('success', 'Brand deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in BrandController@destroy', [
                'brand_id' => $brand->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('brands.index')
                ->with('error', 'Failed to delete brand: ' . $e->getMessage());
        }
    }
}
