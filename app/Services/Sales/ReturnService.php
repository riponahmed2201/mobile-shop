<?php

namespace App\Services\Sales;

use App\Models\Sales\ReturnModel;
use App\Models\Sales\ReturnItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ReturnService
{
    /**
     * Get returns query for DataTables
     */
    public function getReturnsQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return ReturnModel::where('tenant_id', $tenantId)
                ->with(['sale', 'customer', 'approver', 'processor']);
        } catch (Exception $e) {
            Log::error('Error creating returns query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique return number
     */
    public function generateReturnNumber(int $tenantId): string
    {
        $prefix = 'RET';
        $date = date('Ymd');
        
        $lastReturn = ReturnModel::where('tenant_id', $tenantId)
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReturn && $lastReturn->return_number) {
            $lastNumber = (int) substr($lastReturn->return_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new return
     */
    public function createReturn(array $data, array $items): ReturnModel
    {
        DB::beginTransaction();
        
        try {
            $tenantId = $this->getTenantId();
            
            // Generate return number
            if (empty($data['return_number'])) {
                $data['return_number'] = $this->generateReturnNumber($tenantId);
            }

            // Calculate total
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['total_amount'];
            }

            // Create return
            $return = ReturnModel::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'total_amount' => $totalAmount,
                'status' => 'PENDING',
            ]));

            // Create return items
            foreach ($items as $item) {
                ReturnItem::create([
                    'return_id' => $return->id,
                    'sale_item_id' => $item['sale_item_id'],
                    'product_id' => $item['product_id'],
                    'imei_id' => $item['imei_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_amount' => $item['total_amount'],
                    'condition_notes' => $item['condition_notes'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Return created successfully', [
                'return_id' => $return->id,
                'return_number' => $return->return_number
            ]);

            return $return->load('items', 'sale', 'customer');
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating return', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Approve a return
     */
    public function approveReturn(ReturnModel $return, ?string $notes = null): ReturnModel
    {
        try {
            if ($return->status !== 'PENDING') {
                throw new Exception('Only pending returns can be approved.');
            }

            $return->update([
                'status' => 'APPROVED',
                'approved_by' => auth()->id(),
                'approval_notes' => $notes,
            ]);

            Log::info('Return approved', [
                'return_id' => $return->id,
                'approved_by' => auth()->id()
            ]);

            return $return->fresh();
        } catch (Exception $e) {
            Log::error('Error approving return', [
                'return_id' => $return->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Reject a return
     */
    public function rejectReturn(ReturnModel $return, ?string $notes = null): ReturnModel
    {
        try {
            if ($return->status !== 'PENDING') {
                throw new Exception('Only pending returns can be rejected.');
            }

            $return->update([
                'status' => 'REJECTED',
                'approved_by' => auth()->id(),
                'approval_notes' => $notes,
            ]);

            Log::info('Return rejected', [
                'return_id' => $return->id,
                'rejected_by' => auth()->id()
            ]);

            return $return->fresh();
        } catch (Exception $e) {
            Log::error('Error rejecting return', [
                'return_id' => $return->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process a return (complete the refund/exchange)
     */
    public function processReturn(ReturnModel $return): ReturnModel
    {
        try {
            if ($return->status !== 'APPROVED') {
                throw new Exception('Only approved returns can be processed.');
            }

            $return->update([
                'status' => 'COMPLETED',
                'processed_by' => auth()->id(),
            ]);

            Log::info('Return processed', [
                'return_id' => $return->id,
                'processed_by' => auth()->id()
            ]);

            return $return->fresh();
        } catch (Exception $e) {
            Log::error('Error processing return', [
                'return_id' => $return->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get current tenant ID
     */
    private function getTenantId(): int
    {
        if (auth()->check()) {
            return auth()->user()->tenant_id;
        }
        
        return 1;
    }
}
