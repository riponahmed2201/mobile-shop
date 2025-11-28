<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Sales\ReturnModel;
use App\Services\Sales\ReturnService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Sales\StoreReturnRequest;

class ReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->returnService->getReturnsQuery();

                return DataTables::of($query)
                    ->addColumn('return_number', function ($row) {
                        return $row->return_number;
                    })
                    ->addColumn('sale_invoice', function ($row) {
                        return $row->sale ? $row->sale->invoice_number : '-';
                    })
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : '-';
                    })
                    ->addColumn('return_date', function ($row) {
                        return $row->return_date->format('d M Y');
                    })
                    ->addColumn('total_amount', function ($row) {
                        return number_format($row->total_amount, 2);
                    })
                    ->addColumn('return_type', function ($row) {
                        $badgeClass = match($row->return_type) {
                            'REFUND' => 'bg-info',
                            'EXCHANGE' => 'bg-warning',
                            'STORE_CREDIT' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->return_type . '</span>';
                    })
                    ->addColumn('status', function ($row) {
                        $badgeClass = match($row->status) {
                            'PENDING' => 'bg-warning',
                            'APPROVED' => 'bg-success',
                            'REJECTED' => 'bg-danger',
                            'COMPLETED' => 'bg-primary',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->status . '</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $showUrl = route('returns.show', $row->id);
                        $approveUrl = route('returns.approve', $row->id);
                        $rejectUrl = route('returns.reject', $row->id);
                        $processUrl = route('returns.process', $row->id);
                        $csrf = csrf_field();
                        
                        $actions = '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$showUrl.'"><i class="ti ti-eye me-1"></i> View</a>';
                        
                        if ($row->status === 'PENDING') {
                            $actions .= '<form action="'.$approveUrl.'" method="POST" style="display:inline;">
                                            '.$csrf.'
                                            <button type="submit" class="dropdown-item"><i class="ti ti-check me-1"></i> Approve</button>
                                        </form>';
                            $actions .= '<form action="'.$rejectUrl.'" method="POST" style="display:inline;">
                                            '.$csrf.'
                                            <button type="submit" class="dropdown-item"><i class="ti ti-x me-1"></i> Reject</button>
                                        </form>';
                        }
                        
                        if ($row->status === 'APPROVED') {
                            $actions .= '<form action="'.$processUrl.'" method="POST" style="display:inline;">
                                            '.$csrf.'
                                            <button type="submit" class="dropdown-item"><i class="ti ti-package me-1"></i> Process</button>
                                        </form>';
                        }
                        
                        $actions .= '</div></div>';
                        return $actions;
                    })
                    ->rawColumns(['return_type', 'status', 'action'])
                    ->make(true);
            }

            return view('returns.index');
        } catch (Exception $e) {
            Log::error('Error in ReturnController@index', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json(['error' => 'Failed to load returns data.'], 500);
            }

            return redirect()->route('returns.index')
                ->with('error', 'An error occurred while loading returns.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $sales = \App\Models\Sales\Sale::with('customer')->latest()->take(50)->get(); // Limit to last 50 sales for performance
        return view('returns.create', compact('sales'));
    }

    /**
     * Store a newly created resource in storage.
     */


// ... (inside class)

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReturnRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $items = $request->input('items');
            $this->returnService->createReturn($validated, $items);

            return redirect()->route('returns.index')
                ->with('success', 'Return created successfully.');
        } catch (Exception $e) {
            Log::error('Error in ReturnController@store', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create return: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReturnModel $return): View
    {
        $return->load(['items.product', 'sale', 'customer', 'approver', 'processor']);
        return view('returns.show', compact('return'));
    }

    /**
     * Approve a return
     */
    public function approve(Request $request, ReturnModel $return): RedirectResponse
    {
        try {
            $notes = $request->input('notes');
            $this->returnService->approveReturn($return, $notes);

            return redirect()->route('returns.show', $return->id)
                ->with('success', 'Return approved successfully.');
        } catch (Exception $e) {
            Log::error('Error in ReturnController@approve', [
                'return_id' => $return->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to approve return: ' . $e->getMessage());
        }
    }

    /**
     * Reject a return
     */
    public function reject(Request $request, ReturnModel $return): RedirectResponse
    {
        try {
            $notes = $request->input('notes');
            $this->returnService->rejectReturn($return, $notes);

            return redirect()->route('returns.show', $return->id)
                ->with('success', 'Return rejected successfully.');
        } catch (Exception $e) {
            Log::error('Error in ReturnController@reject', [
                'return_id' => $return->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to reject return: ' . $e->getMessage());
        }
    }

    /**
     * Process a return (complete refund/exchange)
     */
    public function process(ReturnModel $return): RedirectResponse
    {
        try {
            $this->returnService->processReturn($return);

            return redirect()->route('returns.show', $return->id)
                ->with('success', 'Return processed successfully.');
        } catch (Exception $e) {
            Log::error('Error in ReturnController@process', [
                'return_id' => $return->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to process return: ' . $e->getMessage());
        }
    }
}
