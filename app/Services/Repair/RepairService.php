<?php

namespace App\Services\Repair;

use App\Models\Repair\RepairTicket;
use App\Models\Repair\RepairPart;
use App\Models\Repair\RepairStatusHistory;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RepairService
{
    /**
     * Create a new repair ticket
     */
    public function createTicket(array $data): RepairTicket
    {
        DB::beginTransaction();
        try {
            $data['tenant_id'] = auth()->user()->tenant_id ?? 1;
            $data['ticket_number'] = $this->generateTicketNumber();
            $data['created_by'] = auth()->id();

            $ticket = RepairTicket::create($data);

            // Add initial status to history
            $this->addStatusHistory($ticket->id, null, $ticket->status, 'Ticket created');

            DB::commit();
            return $ticket->load(['customer', 'assignedTo', 'createdBy']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating repair ticket', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update a repair ticket
     */
    public function updateTicket(RepairTicket $ticket, array $data): RepairTicket
    {
        DB::beginTransaction();
        try {
            $oldStatus = $ticket->status;
            $ticket->update($data);

            // Add status change to history if status changed
            if ($oldStatus !== $data['status']) {
                $this->addStatusHistory($ticket->id, $oldStatus, $data['status'], $data['status_notes'] ?? null);
            }

            DB::commit();
            return $ticket->fresh(['customer', 'assignedTo', 'createdBy', 'parts']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating repair ticket', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Update ticket status
     */
    public function updateStatus(RepairTicket $ticket, string $newStatus, ?string $notes = null): RepairTicket
    {
        DB::beginTransaction();
        try {
            $oldStatus = $ticket->status;
            $ticket->update(['status' => $newStatus]);

            // Add status change to history
            $this->addStatusHistory($ticket->id, $oldStatus, $newStatus, $notes);

            // Update actual delivery date if delivered
            if ($newStatus === 'DELIVERED') {
                $ticket->update(['actual_delivery_date' => now()]);
            }

            DB::commit();
            return $ticket->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating ticket status', [
                'ticket_id' => $ticket->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Add parts to repair ticket
     */
    public function addParts(RepairTicket $ticket, array $partsData): void
    {
        DB::beginTransaction();
        try {
            foreach ($partsData as $partData) {
                $partData['total_price'] = $partData['quantity'] * $partData['unit_price'];
                RepairPart::create(array_merge($partData, ['repair_ticket_id' => $ticket->id]));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding parts to repair ticket', [
                'ticket_id' => $ticket->id,
                'parts_data' => $partsData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update parts for repair ticket
     */
    public function updateParts(RepairTicket $ticket, array $partsData): void
    {
        DB::beginTransaction();
        try {
            // Remove existing parts
            $ticket->parts()->delete();

            // Add new parts
            $this->addParts($ticket, $partsData);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating parts for repair ticket', [
                'ticket_id' => $ticket->id,
                'parts_data' => $partsData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Assign ticket to technician
     */
    public function assignTicket(RepairTicket $ticket, int $technicianId): RepairTicket
    {
        return $this->updateTicket($ticket, ['assigned_to' => $technicianId]);
    }

    /**
     * Get tickets by status
     */
    public function getTicketsByStatus(string $status, ?int $tenantId = null)
    {
        return RepairTicket::forTenant($tenantId)
            ->byStatus($status)
            ->with(['customer', 'assignedTo', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get tickets assigned to technician
     */
    public function getTicketsByTechnician(int $technicianId, ?int $tenantId = null)
    {
        return RepairTicket::forTenant($tenantId)
            ->assignedTo($technicianId)
            ->with(['customer', 'assignedTo', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get repair statistics
     */
    public function getRepairStatistics(?int $tenantId = null): array
    {
        $query = RepairTicket::forTenant($tenantId);

        return [
            'total_tickets' => $query->count(),
            'received' => $query->byStatus('RECEIVED')->count(),
            'in_progress' => $query->byStatus('IN_PROGRESS')->count(),
            'ready' => $query->byStatus('READY')->count(),
            'delivered' => $query->byStatus('DELIVERED')->count(),
            'cancelled' => $query->byStatus('CANCELLED')->count(),
            'urgent' => $query->byPriority('URGENT')->count(),
            'warranty_repairs' => $query->where('warranty_repair', true)->count(),
            'average_repair_time' => $this->getAverageRepairTime($tenantId),
            'total_revenue' => $query->sum('final_cost'),
        ];
    }

    /**
     * Get average repair time in days
     */
    private function getAverageRepairTime(?int $tenantId = null): float
    {
        $tickets = RepairTicket::forTenant($tenantId)
            ->where('status', 'DELIVERED')
            ->whereNotNull('actual_delivery_date')
            ->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalDays = $tickets->sum(function ($ticket) {
            return $ticket->received_date->diffInDays($ticket->actual_delivery_date);
        });

        return round($totalDays / $tickets->count(), 1);
    }

    /**
     * Generate unique ticket number
     */
    private function generateTicketNumber(): string
    {
        $date = now()->format('Ymd');
        $tenantId = auth()->user()->tenant_id ?? 1;

        $latest = RepairTicket::where('tenant_id', $tenantId)
            ->where('ticket_number', 'like', "RT{$tenantId}{$date}%")
            ->orderBy('ticket_number', 'desc')
            ->first();

        $sequence = 1;
        if ($latest) {
            $sequence = intval(substr($latest->ticket_number, -4)) + 1;
        }

        return sprintf("RT%d%s%04d", $tenantId, $date, $sequence);
    }

    /**
     * Add status change to history
     */
    private function addStatusHistory(int $ticketId, ?string $oldStatus, string $newStatus, ?string $notes = null): void
    {
        RepairStatusHistory::create([
            'repair_ticket_id' => $ticketId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => auth()->id(),
            'notes' => $notes,
        ]);
    }

    /**
     * Get overdue tickets
     */
    public function getOverdueTickets(?int $tenantId = null)
    {
        return RepairTicket::forTenant($tenantId)
            ->where('status', '!=', 'DELIVERED')
            ->where('status', '!=', 'CANCELLED')
            ->where('estimated_delivery_date', '<', now())
            ->with(['customer', 'assignedTo'])
            ->orderBy('estimated_delivery_date', 'asc')
            ->get();
    }

    /**
     * Get tickets needing attention
     */
    public function getTicketsNeedingAttention(?int $tenantId = null)
    {
        return RepairTicket::forTenant($tenantId)
            ->whereIn('status', ['RECEIVED', 'DIAGNOSED', 'PARTS_PENDING'])
            ->where('updated_at', '<', now()->subDays(3))
            ->with(['customer', 'assignedTo'])
            ->orderBy('updated_at', 'asc')
            ->get();
    }
}
