<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\Services\CustomerGroupService;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class CustomerGroupController extends Controller
{
    protected CustomerGroupService $groupService;
    protected CustomerService $customerService;

    public function __construct(CustomerGroupService $groupService, CustomerService $customerService)
    {
        $this->groupService = $groupService;
        $this->customerService = $customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = $this->groupService->getGroupsQuery();

                return DataTables::of($query)
                    ->addColumn('members_count', function ($row) {
                        return '<span class="badge bg-info">' . $row->customers_count . ' members</span>';
                    })
                    ->addColumn('discount_percentage', function ($row) {
                        return $row->discount_percentage > 0 
                            ? '<span class="badge bg-success">' . number_format($row->discount_percentage, 2) . '%</span>' 
                            : '-';
                    })
                    ->addColumn('color_badge', function ($row) {
                        if ($row->color) {
                            return '<span class="badge" style="background-color: ' . $row->color . ';">' . $row->group_name . '</span>';
                        }
                        return $row->group_name;
                    })
                    ->addColumn('is_active', function ($row) {
                        return $row->is_active 
                            ? '<span class="badge bg-success">Active</span>' 
                            : '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl = route('customer-groups.edit', $row->id);
                        $membersUrl = route('customer-groups.members', $row->id);
                        $deleteUrl = route('customer-groups.destroy', $row->id);
                        $csrf = csrf_field();
                        $method = method_field('DELETE');
                        
                        return '
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="'.$membersUrl.'"><i class="ti tabler-users me-1"></i> Manage Members</a>
                                    <a class="dropdown-item" href="'.$editUrl.'"><i class="ti tabler-pencil me-1"></i> Edit</a>
                                    <form action="'.$deleteUrl.'" method="POST" onsubmit="return confirm(\'Are you sure?\');" style="display:inline;">
                                        '.$csrf.$method.'
                                        <button type="submit" class="dropdown-item"><i class="ti tabler-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </div>
                        ';
                    })
                    ->rawColumns(['members_count', 'discount_percentage', 'color_badge', 'is_active', 'action'])
                    ->make(true);
            }

            return view('customers.groups.index');
        } catch (Exception $e) {
            Log::error('Error in CustomerGroupController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Failed to load customer groups data.'
                ], 500);
            }

            return redirect()->route('customer-groups.index')
                ->with('error', 'An error occurred while loading customer groups. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('customers.groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'group_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'min_purchase_amount' => 'nullable|numeric|min:0',
                'min_purchase_count' => 'nullable|integer|min:0',
                'color' => 'nullable|string|max:20',
                'is_active' => 'nullable|boolean',
            ]);

            $this->groupService->createGroup($validated);

            return redirect()->route('customer-groups.index')
                ->with('success', 'Customer group created successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerGroupController@store', [
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create customer group: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerGroup $customerGroup): View
    {
        return view('customers.groups.edit', compact('customerGroup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerGroup $customerGroup): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'group_name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'min_purchase_amount' => 'nullable|numeric|min:0',
                'min_purchase_count' => 'nullable|integer|min:0',
                'color' => 'nullable|string|max:20',
                'is_active' => 'nullable|boolean',
            ]);

            $this->groupService->updateGroup($customerGroup, $validated);

            return redirect()->route('customer-groups.index')
                ->with('success', 'Customer group updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerGroupController@update', [
                'group_id' => $customerGroup->id,
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update customer group: ' . $e->getMessage());
        }
    }

    /**
     * Show group members management page
     */
    public function members(CustomerGroup $customerGroup): View
    {
        $customers = $this->customerService->getCustomersForTenant();
        $groupCustomers = $customerGroup->customers->pluck('id')->toArray();
        
        return view('customers.groups.members', compact('customerGroup', 'customers', 'groupCustomers'));
    }

    /**
     * Update group members
     */
    public function updateMembers(Request $request, CustomerGroup $customerGroup): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'customer_ids' => 'nullable|array',
                'customer_ids.*' => 'exists:customers,id',
            ]);

            $customerIds = $validated['customer_ids'] ?? [];
            $this->groupService->syncCustomersForGroup($customerGroup, $customerIds);

            return redirect()->route('customer-groups.members', $customerGroup->id)
                ->with('success', 'Group members updated successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerGroupController@updateMembers', [
                'group_id' => $customerGroup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update group members: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerGroup $customerGroup): RedirectResponse
    {
        try {
            $this->groupService->deleteGroup($customerGroup);

            return redirect()->route('customer-groups.index')
                ->with('success', 'Customer group deleted successfully.');
        } catch (Exception $e) {
            Log::error('Error in CustomerGroupController@destroy', [
                'group_id' => $customerGroup->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('customer-groups.index')
                ->with('error', 'Failed to delete customer group: ' . $e->getMessage());
        }
    }
}

