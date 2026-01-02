<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\CashTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CashBookController extends Controller
{
    /**
     * Display cash book with transactions
     */
    public function index(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        $query = CashTransaction::where('tenant_id', $tenantId)
            ->with('createdBy');

        // Apply date filter (default to current month)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query->whereBetween('transaction_date', [$startDate, $endDate]);

        // Apply transaction type filter
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Apply payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Calculate opening balance (transactions before start date)
        $openingBalance = CashTransaction::where('tenant_id', $tenantId)
            ->where('transaction_date', '<', $startDate)
            ->sum(DB::raw('
                CASE 
                    WHEN transaction_type IN ("SALE", "PAYMENT_RECEIVED", "OPENING_BALANCE") THEN amount
                    WHEN transaction_type IN ("PURCHASE", "EXPENSE", "PAYMENT_MADE") THEN -amount
                    ELSE 0
                END
            '));

        // Calculate totals for current period
        $totalInflow = CashTransaction::where('tenant_id', $tenantId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('transaction_type', ['SALE', 'PAYMENT_RECEIVED', 'OPENING_BALANCE'])
            ->sum('amount');

        $totalOutflow = CashTransaction::where('tenant_id', $tenantId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->whereIn('transaction_type', ['PURCHASE', 'EXPENSE', 'PAYMENT_MADE'])
            ->sum('amount');

        $closingBalance = $openingBalance + $totalInflow - $totalOutflow;

        return view('cash-book.index', compact(
            'transactions',
            'openingBalance',
            'totalInflow',
            'totalOutflow',
            'closingBalance',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show form for manual transaction entry
     */
    public function create(): View
    {
        return view('cash-book.create');
    }

    /**
     * Store manual cash transaction
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'transaction_date' => 'required|date',
                'transaction_type' => 'required|in:OPENING_BALANCE,ADJUSTMENT',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'nullable|in:CASH,CARD,BKASH,NAGAD,BANK,OTHER',
                'description' => 'required|string',
            ]);

            $validated['tenant_id'] = session('tenant_id', 1);
            $validated['created_by'] = auth()->id() ?? 1;

            CashTransaction::create($validated);

            return redirect()->route('finance.cash-book.index')
                ->with('success', 'Cash transaction recorded successfully.');
        } catch (Exception $e) {
            Log::error('Error in CashBookController@store', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record transaction: ' . $e->getMessage());
        }
    }

    /**
     * Export cash book to Excel/PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('info', 'Export functionality coming soon.');
    }
}
