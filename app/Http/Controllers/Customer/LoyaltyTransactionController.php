<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyTransaction;
use App\Services\LoyaltyTransactionService;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class LoyaltyTransactionController extends Controller
{
    protected LoyaltyTransactionService $loyaltyService;
    protected CustomerService $customerService;

    public function __construct(LoyaltyTransactionService $loyaltyService, CustomerService $customerService)
    {
        $this->loyaltyService = $loyaltyService;
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $customerId = $request->get('customer_id');
                $query = $this->loyaltyService->getTransactionsQuery(null, $customerId);

                return DataTables::of($query)
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : '-';
                    })
                    ->addColumn('customer_mobile', function ($row) {
                        return $row->customer ? $row->customer->mobile_primary : '-';
                    })
                    ->addColumn('transaction_type', function ($row) {
                        $badgeClass = match($row->transaction_type) {
                            'EARNED' => 'bg-success',
                            'REDEEMED' => 'bg-danger',
                            'EXPIRED' => 'bg-warning',
                            'ADJUSTED' => 'bg-info',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->transaction_type . '</span>';
                    })
                    ->addColumn('points', function ($row) {
                        $color = in_array($row->transaction_type, ['EARNED', 'ADJUSTED']) ? 'text-success' : 'text-danger';
                        $sign = in_array($row->transaction_type, ['EARNED', 'ADJUSTED']) ? '+' : '-';
                        return '<span class="' . $color . '">' . $sign . $row->points . '</span>';
                    })
                    ->addColumn('reference', function ($row) {
                        if ($row->reference_type && $row->reference_id) {
                            return $row->reference_type . ' #' . $row->reference_id;
                        }
                        return '-';
                    })
                    ->addColumn('action', function ($row) {
                        $deleteUrl = route('loyalty.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        return '
                            <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure? This will reverse the points.\');" style="display:inline;">
                                '.$csrf.$method.'
                                <button type="submit" class="btn btn-sm btn-danger"><i class="ti tabler-trash me-1"></i> Delete</button>
                            </form>
                        ';
                    })
                    ->rawColumns(['transaction_type', 'points', 'action'])
                    ->make(true);
            }

            $customers = $this->customerService->getCustomersForTenant();
            return view('customers.loyalty.index', compact('customers'));
        } catch (Exception $e) {
            Log::error('Error in LoyaltyTransactionController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load loyalty transactions data.'
                ], 500);
            }

            return redirect()->route('loyalty.index')
                ->with('error', 'An error occurred while loading loyalty transactions. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = $this->customerService->getCustomersForTenant();
        return view('customers.loyalty.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'transaction_type' => 'required|in:EARNED,REDEEMED,EXPIRED,ADJUSTED',
                'points' => 'required|integer|min:1',
                'reference_type' => 'nullable|string|max:50',
                'reference_id' => 'nullable|integer',
                'description' => 'nullable|string',
                'expiry_date' => 'nullable|date',
            ]);

            // Check if customer has enough points for redemption
            if ($validated['transaction_type'] === 'REDEEMED') {
                $currentPoints = $this->loyaltyService->getCustomerPoints($validated['customer_id']);
                if ($currentPoints < $validated['points']) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Customer does not have enough loyalty points.');
                }
            }

            $this->loyaltyService->createTransaction($validated);

            return redirect()->route('loyalty.index')
                ->with('success', 'Loyalty transaction created successfully.');
        } catch (Exception $e) {
            Log::error('Error in LoyaltyTransactionController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create loyalty transaction: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoyaltyTransaction $loyaltyTransaction): RedirectResponse
    {
        try {
            $this->loyaltyService->deleteTransaction($loyaltyTransaction);

            return redirect()->route('loyalty.index')
                ->with('success', 'Loyalty transaction deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in LoyaltyTransactionController@destroy', [
                'transaction_id' => $loyaltyTransaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('loyalty.index')
                ->with('error', 'Failed to delete loyalty transaction: ' . $e->getMessage());
        }
    }
}

