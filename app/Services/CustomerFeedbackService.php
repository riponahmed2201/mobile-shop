<?php

namespace App\Services;

use App\Models\CustomerFeedback;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomerFeedbackService
{
    /**
     * Get all feedback for the current tenant
     */
    public function getFeedbackForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return CustomerFeedback::with(['customer', 'responder'])
                ->where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching feedback for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get feedback query for DataTables
     */
    public function getFeedbackQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return CustomerFeedback::with(['customer', 'responder'])
                ->where('tenant_id', $tenantId);
        } catch (Exception $e) {
            Log::error('Error creating feedback query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new feedback
     */
    public function createFeedback(array $data): CustomerFeedback
    {
        try {
            $tenantId = $this->getTenantId();
            
            $feedback = CustomerFeedback::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'is_public' => isset($data['is_public']) ? (bool)$data['is_public'] : false,
            ]));

            Log::info('Customer feedback created successfully', [
                'feedback_id' => $feedback->id,
                'tenant_id' => $tenantId
            ]);

            return $feedback->load(['customer', 'responder']);
        } catch (Exception $e) {
            Log::error('Error creating customer feedback', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update feedback response
     */
    public function respondToFeedback(CustomerFeedback $feedback, string $responseText): CustomerFeedback
    {
        try {
            $feedback->update([
                'response_text' => $responseText,
                'responded_by' => auth()->id(),
                'responded_at' => now(),
            ]);

            Log::info('Feedback response added successfully', [
                'feedback_id' => $feedback->id
            ]);

            return $feedback->fresh()->load(['customer', 'responder']);
        } catch (Exception $e) {
            Log::error('Error responding to feedback', [
                'feedback_id' => $feedback->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a feedback
     */
    public function deleteFeedback(CustomerFeedback $feedback): bool
    {
        try {
            $feedbackId = $feedback->id;
            
            $deleted = $feedback->delete();

            if ($deleted) {
                Log::info('Feedback deleted successfully', [
                    'feedback_id' => $feedbackId
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting feedback', [
                'feedback_id' => $feedback->id,
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

