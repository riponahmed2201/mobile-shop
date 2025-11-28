<?php

namespace App\Services\Sales;

use App\Models\Sales\EmiPlan;
use App\Models\Sales\EmiInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class EmiService
{
    /**
     * Get EMI plans query for DataTables
     */
    public function getEmiPlansQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return EmiPlan::where('tenant_id', $tenantId)
                ->with(['sale', 'customer']);
        } catch (Exception $e) {
            Log::error('Error creating EMI plans query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create EMI plan with installments
     */
    public function createEmiPlan(array $data): EmiPlan
    {
        DB::beginTransaction();
        
        try {
            $tenantId = $this->getTenantId();

            // Create EMI plan
            $emiPlan = EmiPlan::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'remaining_amount' => $data['total_amount'] - $data['down_payment'],
            ]));

            // Generate installments
            $this->generateInstallments($emiPlan);

            DB::commit();

            Log::info('EMI plan created successfully', [
                'emi_plan_id' => $emiPlan->id,
                'sale_id' => $emiPlan->sale_id
            ]);

            return $emiPlan->load('installments');
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating EMI plan', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate installments for EMI plan
     */
    private function generateInstallments(EmiPlan $emiPlan): void
    {
        $startDate = Carbon::parse($emiPlan->start_date);

        for ($i = 1; $i <= $emiPlan->number_of_installments; $i++) {
            EmiInstallment::create([
                'emi_plan_id' => $emiPlan->id,
                'installment_number' => $i,
                'due_date' => $startDate->copy()->addMonths($i - 1),
                'amount' => $emiPlan->installment_amount,
                'status' => 'PENDING',
            ]);
        }
    }

    /**
     * Record installment payment
     */
    public function recordPayment(EmiInstallment $installment, array $paymentData): EmiInstallment
    {
        DB::beginTransaction();
        
        try {
            if ($installment->status === 'PAID') {
                throw new Exception('This installment has already been paid.');
            }

            // Update installment
            $installment->update([
                'paid_amount' => $paymentData['paid_amount'],
                'payment_date' => $paymentData['payment_date'] ?? now(),
                'payment_method' => $paymentData['payment_method'],
                'status' => 'PAID',
                'notes' => $paymentData['notes'] ?? null,
            ]);

            // Update EMI plan
            $emiPlan = $installment->emiPlan;
            $emiPlan->increment('paid_installments');
            $emiPlan->decrement('remaining_amount', $installment->paid_amount);

            // Check if all installments are paid
            if ($emiPlan->paid_installments >= $emiPlan->number_of_installments) {
                $emiPlan->update(['status' => 'COMPLETED']);
            }

            DB::commit();

            Log::info('EMI installment payment recorded', [
                'installment_id' => $installment->id,
                'emi_plan_id' => $emiPlan->id,
                'amount' => $installment->paid_amount
            ]);

            return $installment->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error recording EMI payment', [
                'installment_id' => $installment->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check and mark overdue installments
     */
    public function checkOverdue(): int
    {
        try {
            $overdueCount = EmiInstallment::where('status', 'PENDING')
                ->where('due_date', '<', now())
                ->update(['status' => 'OVERDUE']);

            if ($overdueCount > 0) {
                Log::info('Marked installments as overdue', [
                    'count' => $overdueCount
                ]);
            }

            return $overdueCount;
        } catch (Exception $e) {
            Log::error('Error checking overdue installments', [
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
