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
                    ->addColumn('product_code', function ($row) {
                        return $row->product_code ?: '-';
                    })
                    ->addColumn('brand_name', function ($row) {
                        return $row->brand ? $row->brand->brand_name : '-';
                    })
                    ->addColumn('category_name', function ($row) {
                        return $row->category ? $row->category->category_name : '-';
                    })
                    ->addColumn('purchase_price', function ($row) {
                        return number_format($row->purchase_price, 2);
                    })
                    ->addColumn('selling_price', function ($row) {
                        return number_format($row->selling_price, 2);
                    })
                    ->addColumn('mrp', function ($row) {
                        return $row->mrp ? number_format($row->mrp, 2) : '-';
                    })
                    ->addColumn('current_stock', function ($row) {
                        $badgeClass = $row->current_stock <= $row->min_stock_level ? 'bg-danger' : 'bg-success';
                        return '<span class="badge ' . $badgeClass . '">' . $row->current_stock . ' ' . $row->unit . '</span>';
                    })
                    ->addColumn('product_type', function ($row) {
                        $badgeClass = match($row->product_type) {
                            'MOBILE' => 'bg-primary',
                            'ACCESSORY' => 'bg-info',
                            'PARTS' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->product_type . '</span>';
                    })
                    ->addColumn('is_active', function ($row) {
                        return $row->is_active 
                            ? '<span class="badge bg-success">Active</span>' 
                            : '<span class="badge bg-danger">Inactive</span>';
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
                    ->rawColumns(['action', 'current_stock', 'product_type', 'is_active'])
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
            $image = $request->hasFile('product_image') ? $request->file('product_image') : null;

            $this->productService->createProduct($validated, $image);

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
            $image = $request->hasFile('product_image') ? $request->file('product_image') : null;

            $this->productService->updateProduct($product, $validated, $image);

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
