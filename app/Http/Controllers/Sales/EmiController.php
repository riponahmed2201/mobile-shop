<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\EmiPlan;
use App\Models\Sales\EmiInstallment;
use App\Services\Sales\EmiService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Sales\StoreEmiPaymentRequest;

class EmiController extends Controller
{
    protected EmiService $emiService;

    public function __construct(EmiService $emiService)
    {
        $this->emiService = $emiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->emiService->getEmiPlansQuery();

                return DataTables::of($query)
                    ->addColumn('sale_invoice', function ($row) {
                        return $row->sale ? $row->sale->invoice_number : '-';
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : '-';
                    })
                    ->addColumn('total_amount', function ($row) {
                        return number_format($row->total_amount, 2);
                    })
                    ->addColumn('down_payment', function ($row) {
                        return number_format($row->down_payment, 2);
                    })
                    ->addColumn('installment_amount', function ($row) {
                        return number_format($row->installment_amount, 2);
                    })
                    ->addColumn('progress', function ($row) {
                        $percentage = ($row->paid_installments / $row->number_of_installments) * 100;
                        return $row->paid_installments . ' / ' . $row->number_of_installments . ' (' . round($percentage) . '%)';
                    })
                    ->addColumn('remaining_amount', function ($row) {
                        return number_format($row->remaining_amount, 2);
                    })
                    ->addColumn('status', function ($row) {
                        $badgeClass = match($row->status) {
                            'ACTIVE' => 'bg-success',
                            'COMPLETED' => 'bg-primary',
                            'DEFAULTED' => 'bg-danger',
                            'CANCELLED' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->status . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $showUrl = route('emi.show', $row->id);

                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$showUrl.'"><i class="ti tabler-eye me-1"></i> View Details</a>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('emi.index');
        } catch (Exception $e) {
            Log::error('Error in EmiController@index', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to load EMI plans data.'], 500);
            }

            return redirect()->route('emi.index')
                ->with('error', 'An error occurred while loading EMI plans.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmiPlan $emiPlan): View
    {
        $emiPlan->load(['sale', 'customer', 'installments']);
        return view('emi.show', compact('emiPlan'));
    }

    /**
     * Display EMI agreement/invoice
     */
    public function agreement(EmiPlan $emiPlan): View
    {
        $emiPlan->load(['sale', 'customer', 'installments']);
        return view('emi.agreement', compact('emiPlan'));
    }

    /**
     * Display installment payment receipt
     */
    public function receipt(EmiInstallment $installment): View
    {
        $installment->load(['emiPlan.customer', 'emiPlan.installments']);
        return view('emi.receipt', compact('installment'));
    }

    /**
     * Record installment payment
     */


// ... (inside class)

    /**
     * Record installment payment
     */
    public function recordPayment(StoreEmiPaymentRequest $request, EmiPlan $emiPlan): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $installment = EmiInstallment::findOrFail($validated['installment_id']);
            $this->emiService->recordPayment($installment, $validated);

            return redirect()->route('emi.show', $emiPlan->id)
                ->with('success', 'Payment recorded successfully.');
        } catch (Exception $e) {
            Log::error('Error in EmiController@recordPayment', [
                'emi_plan_id' => $emiPlan->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }
}
