<?php

namespace App\Http\Controllers\Repair;

use App\Http\Controllers\Controller;
use App\Models\Repair\RepairTicket;
use App\Models\Customer;
use App\Models\User;
use App\Services\Repair\RepairService;
use App\Http\Requests\Repair\RepairTicket\StoreRepairTicketRequest;
use App\Http\Requests\Repair\RepairTicket\UpdateRepairTicketRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RepairController extends Controller
{
    protected RepairService $repairService;

    public function __construct(RepairService $repairService)
    {
        $this->repairService = $repairService;
    }

    /**
     * Display a listing of repair tickets
     */
    public function index(Request $request): View
    {
        $query = RepairTicket::forTenant()
            ->with(['customer', 'assignedTo', 'createdBy']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        if ($request->filled('customer')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->customer . '%')
                  ->orWhere('mobile_primary', 'like', '%' . $request->customer . '%');
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $statistics = $this->repairService->getRepairStatistics();

        // Get technicians for filter
        $technicians = User::where('tenant_id', auth()->user()->tenant_id ?? 1)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('repairs.index', compact('tickets', 'statistics', 'technicians'));
    }

    /**
     * Show the form for creating a new repair ticket
     */
    public function create(): View|RedirectResponse
    {
        try {
        $customers = Customer::where('tenant_id', auth()->user()->tenant_id ?? 1)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

            return view('repairs.create', compact('customers'));
        } catch (\Exception $e) {
            Log::error('Error in RepairController@create', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('repairs.index')
                ->with('error', 'An error occurred while loading the create form. Please try again.');
        }
    }

    /**
     * Store a newly created repair ticket
     */
    public function store(StoreRepairTicketRequest $request): RedirectResponse
    {
        try {
            $ticket = $this->repairService->createTicket($request->validated());

            return redirect()->route('repairs.show', $ticket)
                ->with('success', 'Repair ticket created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in RepairController@store', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create repair ticket. Please try again.');
        }
    }

    /**
     * Display the specified repair ticket
     */
    public function show(RepairTicket $repairTicket): View|RedirectResponse
    {
        try {
            $this->authorize('view', $repairTicket);

            $repairTicket->load(['customer', 'assignedTo', 'createdBy', 'parts', 'statusHistory.changedBy']);

            // Get technicians for assignment
            $technicians = User::where('tenant_id', auth()->user()->tenant_id ?? 1)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return view('repairs.show', compact('repairTicket', 'technicians'));
        } catch (\Exception $e) {
            Log::error('Error in RepairController@show', [
                'ticket_id' => $repairTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('repairs.index')
                ->with('error', 'Unable to load repair ticket details.');
        }
    }

    /**
     * Show the form for editing the specified repair ticket
     */
    public function edit(RepairTicket $repairTicket): View|RedirectResponse
    {
        try {
            $this->authorize('update', $repairTicket);

            if (!$repairTicket->can_edit) {
                return redirect()->route('repairs.show', $repairTicket)
                    ->with('error', 'This repair ticket cannot be edited.');
            }

        $customers = Customer::where('tenant_id', auth()->user()->tenant_id ?? 1)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

            return view('repairs.edit', compact('repairTicket', 'customers'));
        } catch (\Exception $e) {
            Log::error('Error in RepairController@edit', [
                'ticket_id' => $repairTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('repairs.index')
                ->with('error', 'An error occurred while loading the edit form. Please try again.');
        }
    }

    /**
     * Update the specified repair ticket
     */
    public function update(UpdateRepairTicketRequest $request, RepairTicket $repairTicket): RedirectResponse
    {
        try {
            $this->authorize('update', $repairTicket);

            $this->repairService->updateTicket($repairTicket, $request->validated());

            return redirect()->route('repairs.show', $repairTicket)
                ->with('success', 'Repair ticket updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in RepairController@update', [
                'ticket_id' => $repairTicket->id,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update repair ticket. Please try again.');
        }
    }

    /**
     * Remove the specified repair ticket
     */
    public function destroy(RepairTicket $repairTicket): RedirectResponse
    {
        try {
            $this->authorize('delete', $repairTicket);

            if (!in_array($repairTicket->status, ['RECEIVED', 'CANCELLED'])) {
                return redirect()->back()
                    ->with('error', 'Cannot delete repair ticket that is in progress or completed.');
            }

            $repairTicket->delete();

            return redirect()->route('repairs.index')
                ->with('success', 'Repair ticket deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in RepairController@destroy', [
                'ticket_id' => $repairTicket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete repair ticket. Please try again.');
        }
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, RepairTicket $repairTicket): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:RECEIVED,DIAGNOSED,IN_PROGRESS,PARTS_PENDING,READY,DELIVERED,CANCELLED',
                'notes' => 'nullable|string|max:500'
            ]);

            $this->authorize('update', $repairTicket);

            $this->repairService->updateStatus($repairTicket, $request->status, $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Ticket status updated successfully.',
                'status' => $repairTicket->fresh()->status_label,
                'badge_class' => $repairTicket->status_badge_class
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating ticket status', [
                'ticket_id' => $repairTicket->id,
                'new_status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket status. Please try again.'
            ], 500);
        }
    }

    /**
     * Assign ticket to technician
     */
    public function assign(Request $request, RepairTicket $repairTicket): JsonResponse
    {
        try {
            $request->validate([
                'assigned_to' => 'required|exists:users,id'
            ]);

            $this->authorize('update', $repairTicket);

            $this->repairService->assignTicket($repairTicket, $request->assigned_to);

            $technician = User::find($request->assigned_to);

            return response()->json([
                'success' => true,
                'message' => 'Ticket assigned successfully.',
                'technician_name' => $technician->name
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning ticket', [
                'ticket_id' => $repairTicket->id,
                'technician_id' => $request->assigned_to,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign ticket. Please try again.'
            ], 500);
        }
    }

    /**
     * Get tickets by status for dashboard
     */
    public function getByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status', 'all');
            $limit = $request->get('limit', 10);

            if ($status === 'all') {
                $tickets = RepairTicket::forTenant()
                    ->with(['customer', 'assignedTo'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
            } else {
                $tickets = $this->repairService->getTicketsByStatus($status);
            }

            return response()->json([
                'success' => true,
                'tickets' => $tickets->take($limit)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting tickets by status', [
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load tickets.'
            ], 500);
        }
    }
}
