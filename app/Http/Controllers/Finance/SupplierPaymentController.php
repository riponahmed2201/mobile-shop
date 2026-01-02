<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\SupplierPayment;
use App\Models\Purchase\Supplier;
use App\Models\Purchase\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of supplier payments
     */
    public function index(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        $query = SupplierPayment::where('tenant_id', $tenantId)
            ->with(['supplier', 'purchaseOrder', 'paidBy']);

        // Apply filters
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        $payments = $query->latest('payment_date')->paginate(20);
        $totalPaid = $query->sum('amount');
        
        // Get suppliers with outstanding balance for filter
        $suppliers = Supplier::where('tenant_id', $tenantId)
            ->where('outstanding_balance', '>', 0)
            ->orderBy('supplier_name')
            ->get();

        return view('supplier-payments.index', compact('payments', 'suppliers', 'totalPaid'));
    }

    /**
     * Show the form for creating a new supplier payment
     */
    public function create(Request $request): View
    {
        $tenantId = session('tenant_id', 1);
        
        // Get suppliers with outstanding balance
        $suppliers = Supplier::where('tenant_id', $tenantId)
            ->where('outstanding_balance', '>', 0)
            ->orderBy('supplier_name')
            ->get();

        // If supplier is pre-selected, get their pending purchase orders
        $purchaseOrders = [];
        if ($request->filled('supplier_id')) {
            $purchaseOrders = PurchaseOrder::where('tenant_id', $tenantId)
                ->where('supplier_id', $request->supplier_id)
                ->whereIn('payment_status', ['PENDING', 'PARTIAL'])
                ->where('due_amount', '>', 0)
                ->get();
        }

        return view('supplier-payments.create', compact('suppliers', 'purchaseOrders'));
    }

    /**
     * Store a newly created supplier payment
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'purchase_order_id' => 'nullable|exists:purchase_orders,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|in:CASH,CARD,BANK,BKASH,NAGAD,OTHER',
                'payment_date' => 'required|date',
                'reference_number' => 'nullable|string|max:100',
                'notes' => 'nullable|string',
            ]);

            $validated['tenant_id'] = session('tenant_id', 1);
            $validated['paid_by'] = auth()->id() ?? 1;

            // Validate amount doesn't exceed outstanding balance
            $supplier = Supplier::find($validated['supplier_id']);
            if ($validated['amount'] > $supplier->outstanding_balance) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Payment amount cannot exceed outstanding balance of ' . number_format($supplier->outstanding_balance, 2));
            }

            // If purchase order is specified, validate amount doesn't exceed due amount
            if ($validated['purchase_order_id']) {
                $po = PurchaseOrder::find($validated['purchase_order_id']);
                if ($validated['amount'] > $po->due_amount) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Payment amount cannot exceed PO due amount of ' . number_format($po->due_amount, 2));
                }
            }

            SupplierPayment::create($validated);

            return redirect()->route('finance.supplier-payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (Exception $e) {
            Log::error('Error in SupplierPaymentController@store', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified supplier payment
     */
    public function show(SupplierPayment $supplierPayment): View
    {
        $supplierPayment->load(['supplier', 'purchaseOrder', 'paidBy']);
        return view('supplier-payments.show', compact('supplierPayment'));
    }

    /**
     * Generate payment voucher
     */
    public function voucher(SupplierPayment $supplierPayment): View
    {
        $supplierPayment->load(['supplier', 'purchaseOrder', 'paidBy']);
        return view('supplier-payments.voucher', compact('supplierPayment'));
    }
}
