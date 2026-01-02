<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\PaymentCollection;
use App\Models\Customer;
use App\Models\Sales\Sale;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentCollectionController extends Controller
{
    /**
     * Display a listing of payment collections
     */
    public function index(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        $query = PaymentCollection::where('tenant_id', $tenantId)
            ->with(['customer', 'sale', 'collectedBy']);

        // Apply filters
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        $payments = $query->latest('payment_date')->paginate(20);
        $totalCollected = $query->sum('amount');
        
        // Get customers with outstanding balance for filter
        $customers = Customer::where('tenant_id', $tenantId)
            ->where('outstanding_balance', '>', 0)
            ->orderBy('full_name')
            ->get();

        return view('payment-collections.index', compact('payments', 'customers', 'totalCollected'));
    }

    /**
     * Show the form for creating a new payment collection
     */
    public function create(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        // Get customers with outstanding balance
        $customers = Customer::where('tenant_id', $tenantId)
            ->where('outstanding_balance', '>', 0)
            ->orderBy('full_name')
            ->get();

        // If customer is pre-selected, get their pending sales
        $sales = [];
        if ($request->filled('customer_id')) {
            $sales = Sale::where('tenant_id', $tenantId)
                ->where('customer_id', $request->customer_id)
                ->whereIn('payment_status', ['UNPAID', 'PARTIAL'])
                ->where('due_amount', '>', 0)
                ->get();
        }

        return view('payment-collections.create', compact('customers', 'sales'));
    }

    /**
     * Store a newly created payment collection
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'sale_id' => 'nullable|exists:sales,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:CASH,CARD,BKASH,NAGAD,BANK,OTHER',
                'payment_date' => 'required|date',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            $validated['tenant_id'] = session('tenant_id', 1);
            $validated['collected_by'] = auth()->id() ?? 1;

            // Validate amount doesn't exceed outstanding balance
            $customer = Customer::find($validated['customer_id']);
            if ($validated['amount'] > $customer->outstanding_balance) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Payment amount cannot exceed outstanding balance of ' . number_format($customer->outstanding_balance, 2));
            }

            // If sale is specified, validate amount doesn't exceed due amount
            if ($validated['sale_id']) {
                $sale = Sale::find($validated['sale_id']);
                if ($validated['amount'] > $sale->due_amount) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Payment amount cannot exceed sale due amount of ' . number_format($sale->due_amount, 2));
                }
            }

            PaymentCollection::create($validated);

            return redirect()->route('finance.payment-collections.index')
                ->with('success', 'Payment collected successfully.');
        } catch (Exception $e) {
            Log::error('Error in PaymentCollectionController@store', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment collection
     */
    public function show(PaymentCollection $paymentCollection): View
    {
        $paymentCollection->load(['customer', 'sale', 'collectedBy']);
        return view('payment-collections.show', compact('paymentCollection'));
    }

    /**
     * Generate payment receipt
     */
    public function receipt(PaymentCollection $paymentCollection): View
    {
        $paymentCollection->load(['customer', 'sale', 'collectedBy']);
        return view('payment-collections.receipt', compact('paymentCollection'));
    }
}
