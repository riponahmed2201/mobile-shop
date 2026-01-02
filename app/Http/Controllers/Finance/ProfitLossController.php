<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Sales\Sale;
use App\Models\Finance\Expense;
use App\Models\Finance\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    /**
     * Display profit & loss statement
     */
    public function index(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        // Date range (default to current month)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // REVENUE SECTION
        // Total Sales Revenue
        $totalSalesRevenue = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->whereIn('sale_status', ['COMPLETED'])
            ->sum('total_amount');

        // Sales by Type
        $salesByType = Sale::where('tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->whereIn('sale_status', ['COMPLETED'])
            ->select('sale_type', DB::raw('SUM(total_amount) as total'))
            ->groupBy('sale_type')
            ->get();

        // EXPENSES SECTION
        // Total Expenses
        $totalExpenses = Expense::where('tenant_id', $tenantId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Expenses by Category
        $expensesByCategory = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expense_categories.category_name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('expense_categories.id', 'expense_categories.category_name')
            ->get();

        // COST OF GOODS SOLD (COGS)
        // Calculate from purchase orders or use a simplified approach
        $cogs = Sale::where('sales.tenant_id', $tenantId)
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->whereIn('sale_status', ['COMPLETED'])
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->sum(DB::raw('sale_items.quantity * products.purchase_price'));

        // CALCULATIONS
        $grossProfit = $totalSalesRevenue - $cogs;
        $netProfit = $grossProfit - $totalExpenses;
        $profitMargin = $totalSalesRevenue > 0 ? ($netProfit / $totalSalesRevenue) * 100 : 0;

        return view('profit-loss.index', compact(
            'totalSalesRevenue',
            'salesByType',
            'cogs',
            'grossProfit',
            'totalExpenses',
            'expensesByCategory',
            'netProfit',
            'profitMargin',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export profit & loss report
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('info', 'Export functionality coming soon.');
    }
}
