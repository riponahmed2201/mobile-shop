<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ProductCategory;
use App\Http\Requests\Inventory\Product\StoreProductRequest;
use App\Http\Requests\Inventory\Product\UpdateProductRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['brand', 'category']);
            if (auth()->check()) {
                 $query->where('tenant_id', auth()->user()->tenant_id);
            } else {
                $query->where('tenant_id', 1);
            }

            return DataTables::of($query)
                ->addColumn('brand_name', function ($row) {
                    return $row->brand ? $row->brand->brand_name : '-';
                })
                ->addColumn('category_name', function ($row) {
                    return $row->category ? $row->category->category_name : '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('products.edit', $row->id);
                    $deleteUrl = route('products.destroy', $row->id);
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
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('products.index');
    }

    public function create()
    {
        $tenantId = auth()->check() ? auth()->user()->tenant_id : 1;
        $brands = Brand::where('tenant_id', $tenantId)->get();
        $categories = ProductCategory::where('tenant_id', $tenantId)->get();
        return view('products.create', compact('brands', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $tenantId = auth()->check() ? auth()->user()->tenant_id : 1;

        Product::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'product_type' => $validated['product_type'] ?? 'MOBILE',
            'current_stock' => $validated['current_stock'] ?? 0,
            'is_active' => true,
        ]));

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $tenantId = auth()->check() ? auth()->user()->tenant_id : 1;
        $brands = Brand::where('tenant_id', $tenantId)->get();
        $categories = ProductCategory::where('tenant_id', $tenantId)->get();
        return view('products.edit', compact('product', 'brands', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
