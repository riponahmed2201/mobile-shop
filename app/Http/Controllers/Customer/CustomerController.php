<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->customerService->getCustomersQuery();

                return DataTables::of($query)
                    ->addColumn('customer_code', function ($row) {
                        return $row->customer_code ?: '-';
                    })
                    ->addColumn('mobile_primary', function ($row) {
                        return $row->mobile_primary;
                    })
                    ->addColumn('customer_type', function ($row) {
                        $badgeClass = match($row->customer_type) {
                            'NEW' => 'bg-secondary',
                            'REGULAR' => 'bg-primary',
                            'VIP' => 'bg-warning',
                            'WHOLESALE' => 'bg-info',
                            default => 'bg-secondary'
                        };
                        return '<span class="badge ' . $badgeClass . '">' . $row->customer_type . '</span>';
                    })
                    ->addColumn('total_purchases', function ($row) {
                        return number_format($row->total_purchases, 2);
                    })
                    ->addColumn('outstanding_balance', function ($row) {
                        $badgeClass = $row->outstanding_balance > 0 ? 'bg-danger' : 'bg-success';
                        return '<span class="badge ' . $badgeClass . '">' . number_format($row->outstanding_balance, 2) . '</span>';
                    })
                    ->addColumn('loyalty_points', function ($row) {
                        return $row->loyalty_points > 0 ? '<span class="badge bg-info">' . $row->loyalty_points . '</span>' : '-';
                    })
                    ->addColumn('is_active', function ($row) {
                        return $row->is_active 
                            ? '<span class="badge bg-success">Active</span>' 
                            : '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('customers.edit', $row->id);
                        $deleteUrl = route('customers.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$editUrl.'"><i class="ti tabler-pencil me-1"></i> Edit</a>
                                    <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                        '.$csrf.$method.'
                                        <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['customer_type', 'outstanding_balance', 'loyalty_points', 'is_active', 'action'])
                    ->make(true);
            }

            return view('customers.index');
        } catch (Exception $e) {
            Log::error('Error in CustomerController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load customers data.'
                ], 500);
            }

            return redirect()->route('customers.index')
                ->with('error', 'An error occurred while loading customers. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $tags = $request->input('tags', []);

            $this->customerService->createCustomer($validated, $tags);

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerController@store', [
                'data' => $request->except(['tags']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer): View
    {
        $customer->load('tags');
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $tags = $request->input('tags', []);

            $this->customerService->updateCustomer($customer, $validated, $tags);

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerController@update', [
                'customer_id' => $customer->id,
                'data' => $request->except(['tags']),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $this->customerService->deleteCustomer($customer);

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerController@destroy', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('customers.index')
                ->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }
}
