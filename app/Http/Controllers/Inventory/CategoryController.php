<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\ProductCategory;
use App\Http\Requests\Inventory\Category\StoreCategoryRequest;
use App\Http\Requests\Inventory\Category\UpdateCategoryRequest;
use App\Services\Inventory\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->categoryService->getCategoriesQuery();

                return DataTables::of($query)
                    ->addColumn('action', function ($row) {
                        $editUrl = route('categories.edit', $row->id);
                        $deleteUrl = route('categories.destroy', $row->id);
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

            return view('categories.index');
        } catch (Exception $e) {
            Log::error('Error in CategoryController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load categories data.'
                ], 500);
            }

            return redirect()->route('categories.index')
                ->with('error', 'An error occurred while loading categories. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|RedirectResponse
    {
        try {
            $categories = $this->categoryService->getCategoriesForTenant();
            return view('categories.create', compact('categories'));
        } catch (Exception $e) {
            Log::error('Error in CategoryController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('categories.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->categoryService->createCategory($validated);

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (Exception $e) {
            Log::error('Error in CategoryController@store', [
                'data' => $validated ?? $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $category): View|RedirectResponse
    {
        try {
            $categories = $this->categoryService->getCategoriesForTenant(null, $category->id);
            return view('categories.edit', compact('category', 'categories'));
        } catch (Exception $e) {
            Log::error('Error in CategoryController@edit', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('categories.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, ProductCategory $category): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->categoryService->updateCategory($category, $validated);

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in CategoryController@update', [
                'category_id' => $category->id,
                'data' => $validated ?? $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $category): RedirectResponse
    {
        try {
            $this->categoryService->deleteCategory($category);

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in CategoryController@destroy', [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('categories.index')
                ->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}
