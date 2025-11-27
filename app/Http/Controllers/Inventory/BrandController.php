<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Brand;
use App\Http\Requests\Inventory\Brand\StoreBrandRequest;
use App\Http\Requests\Inventory\Brand\UpdateBrandRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Brand::query();
            
            if (auth()->check()) {
                 $query->where('tenant_id', auth()->user()->tenant_id);
            } else {
                // Fallback for testing if no auth, though in prod this should be guarded
                $query->where('tenant_id', 1);
            }

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
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();

        $tenantId = auth()->check() ? auth()->user()->tenant_id : 1;

        $logoPath = null;
        if ($request->hasFile('brand_logo')) {
            $logoPath = $request->file('brand_logo')->store('brands', 'public');
        }

        Brand::create([
            'tenant_id' => $tenantId,
            'brand_name' => $validated['brand_name'],
            'brand_logo_url' => $logoPath,
            'is_active' => true,
        ]);

        return redirect()->route('brands.index')->with('success', 'Brand created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $validated = $request->validated();

        $data = [
            'brand_name' => $validated['brand_name'],
        ];

        if ($request->hasFile('brand_logo')) {
            // Delete old logo if exists
            if ($brand->brand_logo_url) {
                \Storage::disk('public')->delete($brand->brand_logo_url);
            }
            $data['brand_logo_url'] = $request->file('brand_logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('brands.index')->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully.');
    }
}
