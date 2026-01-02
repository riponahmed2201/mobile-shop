<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tenantId = session('tenant_id', 1);
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->withCount('expenses')
            ->latest()
            ->get();

        return view('expense-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('expense-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|string|max:100',
                'description' => 'nullable|string',
            ]);

            $validated['tenant_id'] = session('tenant_id', 1);
            $validated['is_active'] = $request->has('is_active');

            ExpenseCategory::create($validated);

            return redirect()->route('finance.expense-categories.index')
                ->with('success', 'Expense category created successfully.');
        } catch (Exception $e) {
            Log::error('Error in ExpenseCategoryController@store', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create expense category: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseCategory $expenseCategory): View
    {
        return view('expense-categories.edit', compact('expenseCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|string|max:100',
                'description' => 'nullable|string',
            ]);

            $validated['is_active'] = $request->has('is_active');

            $expenseCategory->update($validated);

            return redirect()->route('finance.expense-categories.index')
                ->with('success', 'Expense category updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in ExpenseCategoryController@update', [
                'category_id' => $expenseCategory->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update expense category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory): RedirectResponse
    {
        try {
            // Check if category has expenses
            if ($expenseCategory->expenses()->count() > 0) {
                return redirect()->route('finance.expense-categories.index')
                    ->with('error', 'Cannot delete category with existing expenses.');
            }

            $expenseCategory->delete();

            return redirect()->route('finance.expense-categories.index')
                ->with('success', 'Expense category deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in ExpenseCategoryController@destroy', [
                'category_id' => $expenseCategory->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('finance.expense-categories.index')
                ->with('error', 'Failed to delete expense category: ' . $e->getMessage());
        }
    }
}
