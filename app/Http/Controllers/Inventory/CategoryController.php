<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory\ProductCategory;
use App\Http\Requests\Inventory\Category\StoreCategoryRequest;
use App\Http\Requests\Inventory\Category\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ProductCategory::query();
            if (auth()->check()) {
                 $query->where('tenant_id', auth()->user()->tenant_id);
            } else {
                $query->where('tenant_id', 1);
            }

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
    }

    public function create()
    {
        $categories = ProductCategory::where('tenant_id', auth()->check() ? auth()->user()->tenant_id : 1)->get();
        return view('categories.create', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        $tenantId = auth()->check() ? auth()->user()->tenant_id : 1;

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'category_name' => $validated['category_name'],
            'category_type' => $validated['category_type'],
            'parent_category_id' => $validated['parent_category_id'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(ProductCategory $category)
    {
        $categories = ProductCategory::where('tenant_id', auth()->check() ? auth()->user()->tenant_id : 1)->where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'categories'));
    }

    public function update(UpdateCategoryRequest $request, ProductCategory $category)
    {
        $validated = $request->validated();

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
