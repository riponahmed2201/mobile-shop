<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        $query = Expense::where('tenant_id', $tenantId)
            ->with(['category', 'createdBy']);

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('expense_category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('expense_date', '<=', $request->end_date);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $expenses = $query->latest('expense_date')->paginate(20);
        $totalExpenses = $query->sum('amount');
        
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('expenses.index', compact('expenses', 'categories', 'totalExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tenantId = session('tenant_id', 1);
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('expenses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'expense_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:CASH,CARD,BANK,BKASH,NAGAD,OTHER',
                'reference_number' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'receipt_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $validated['tenant_id'] = session('tenant_id', 1);
            $validated['created_by'] = auth()->id() ?? 1;

            // Handle file upload
            if ($request->hasFile('receipt_file')) {
                $path = $request->file('receipt_file')->store('receipts', 'public');
                $validated['receipt_file_url'] = $path;
            }

            Expense::create($validated);

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense recorded successfully.');
        } catch (Exception $e) {
            Log::error('Error in ExpenseController@store', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record expense: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense): View
    {
        $expense->load(['category', 'createdBy']);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense): View
    {
        $tenantId = session('tenant_id', 1);
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'expense_category_id' => 'required|exists:expense_categories,id',
                'expense_date' => 'required|date',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:CASH,CARD,BANK,BKASH,NAGAD,OTHER',
                'reference_number' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'receipt_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            // Handle file upload
            if ($request->hasFile('receipt_file')) {
                // Delete old file if exists
                if ($expense->receipt_file_url) {
                    Storage::disk('public')->delete($expense->receipt_file_url);
                }
                
                $path = $request->file('receipt_file')->store('receipts', 'public');
                $validated['receipt_file_url'] = $path;
            }

            $expense->update($validated);

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in ExpenseController@update', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update expense: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        try {
            // Delete receipt file if exists
            if ($expense->receipt_file_url) {
                Storage::disk('public')->delete($expense->receipt_file_url);
            }

            $expense->delete();

            return redirect()->route('finance.expenses.index')
                ->with('success', 'Expense deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in ExpenseController@destroy', [
                'expense_id' => $expense->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('finance.expenses.index')
                ->with('error', 'Failed to delete expense: ' . $e->getMessage());
        }
    }
}
