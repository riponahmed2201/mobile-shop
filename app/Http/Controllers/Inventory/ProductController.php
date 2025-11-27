<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Product;
use App\Services\Inventory\ProductService;
use App\Http\Requests\Inventory\Product\StoreProductRequest;
use App\Http\Requests\Inventory\Product\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->productService->getProductsQuery();

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
        } catch (Exception $e) {
            Log::error('Error in ProductController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load products data.'
                ], 500);
            }

            return redirect()->route('products.index')
                ->with('error', 'An error occurred while loading products. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $brands = $this->productService->getBrandsForDropdown();
            $categories = $this->productService->getCategoriesForDropdown();
            
            return view('products.create', compact('brands', 'categories'));
        } catch (Exception $e) {
            Log::error('Error in ProductController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('products.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->productService->createProduct($validated);

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
        } catch (Exception $e) {
            Log::error('Error in ProductController@store', [
                'data' => $request->except(['product_image']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View|RedirectResponse
    {
        try {
            $brands = $this->productService->getBrandsForDropdown();
            $categories = $this->productService->getCategoriesForDropdown();
            
            return view('products.edit', compact('product', 'brands', 'categories'));
        } catch (Exception $e) {
            Log::error('Error in ProductController@edit', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('products.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->productService->updateProduct($product, $validated);

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in ProductController@update', [
                'product_id' => $product->id,
                'data' => $request->except(['product_image']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            $this->productService->deleteProduct($product);

            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in ProductController@destroy', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
