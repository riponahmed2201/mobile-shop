<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Services\CustomerFeedbackService;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class CustomerFeedbackController extends Controller
{
    protected CustomerFeedbackService $feedbackService;
    protected CustomerService $customerService;

    public function __construct(CustomerFeedbackService $feedbackService, CustomerService $customerService)
    {
        $this->feedbackService = $feedbackService;
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->feedbackService->getFeedbackQuery();

                return DataTables::of($query)
                    ->addColumn('customer_name', function ($row) {
                        return $row->customer ? $row->customer->full_name : 'Anonymous';
                    })
                    ->addColumn('customer_mobile', function ($row) {
                        return $row->customer ? $row->customer->mobile_primary : '-';
                    })
                    ->addColumn('rating_stars', function ($row) {
                        $stars = '';
                        for ($i = 1; $i <= 5; $i++) {
                            $stars .= $i <= $row->rating 
                                ? '<i class="ti tabler-star-filled text-warning"></i>' 
                                : '<i class="ti tabler-star text-muted"></i>';
                        }
                        return $stars;
                    })
                    ->addColumn('feedback_type', function ($row) {
                        $badgeClass = match($row->feedback_type) {
                            'SALE' => 'bg-success',
                            'REPAIR' => 'bg-info',
                            'SERVICE' => 'bg-primary',
                            'GENERAL' => 'bg-secondary',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->feedback_type . '</span>';
                    })
                    ->addColumn('is_public', function ($row) {
                        return $row->is_public 
                            ? '<span class="badge bg-success">Public</span>' 
                            : '<span class="badge bg-secondary">Private</span>';
                    })
                    ->addColumn('has_response', function ($row) {
                        return $row->response_text 
                            ? '<span class="badge bg-info">Responded</span>' 
                            : '<span class="badge bg-warning">Pending</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $viewUrl = route('feedback.show', $row->id);
                        $deleteUrl = route('feedback.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$viewUrl.'"><i class="ti tabler-eye me-1"></i> View</a>
                                    <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                        '.$csrf.$method.'
                                        <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['rating_stars', 'feedback_type', 'is_public', 'has_response', 'action'])
                    ->make(true);
            }

            return view('customers.feedback.index');
        } catch (Exception $e) {
            Log::error('Error in CustomerFeedbackController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load feedback data.'
                ], 500);
            }

            return redirect()->route('feedback.index')
                ->with('error', 'An error occurred while loading feedback. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $customers = $this->customerService->getCustomersForTenant();
        return view('customers.feedback.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|exists:customers,id',
                'sale_id' => 'nullable|exists:sales,id',
                'repair_ticket_id' => 'nullable|exists:repair_tickets,id',
                'feedback_type' => 'required|in:SALE,REPAIR,SERVICE,GENERAL',
                'rating' => 'required|integer|min:1|max:5',
                'feedback_text' => 'nullable|string',
                'is_public' => 'nullable|boolean',
            ]);

            $this->feedbackService->createFeedback($validated);

            return redirect()->route('feedback.index')
                ->with('success', 'Feedback created successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerFeedbackController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create feedback: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerFeedback $feedback): View
    {
        $feedback->load(['customer', 'responder']);
        return view('customers.feedback.show', compact('feedback'));
    }

    /**
     * Respond to feedback
     */
    public function respond(Request $request, CustomerFeedback $feedback): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'response_text' => 'required|string',
            ]);

            $this->feedbackService->respondToFeedback($feedback, $validated['response_text']);

            return redirect()->route('feedback.show', $feedback->id)
                ->with('success', 'Response added successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerFeedbackController@respond', [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add response: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerFeedback $feedback): RedirectResponse
    {
        try {
            $this->feedbackService->deleteFeedback($feedback);

            return redirect()->route('feedback.index')
                ->with('success', 'Feedback deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerFeedbackController@destroy', [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('feedback.index')
                ->with('error', 'Failed to delete feedback: ' . $e->getMessage());
        }
    }
}

