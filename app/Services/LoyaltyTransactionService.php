<?php

namespace App\Services;

use App\Models\LoyaltyTransaction;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class LoyaltyTransactionService
{
    /**
     * Get all loyalty transactions for the current tenant
     */
    public function getTransactionsForTenant(?int $tenantId = null, ?int $customerId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            $query = LoyaltyTransaction::with(['customer'])
                ->where('tenant_id', $tenantId);
            
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            
            return $query->orderBy('created_at', 'desc')->get();
        } catch (Exception $e) {
            Log::error('Error fetching loyalty transactions for tenant', [
                'tenant_id' => $tenantId,
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get transactions query for DataTables
     */
    public function getTransactionsQuery(?int $tenantId = null, ?int $customerId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            $query = LoyaltyTransaction::with(['customer'])
                ->where('tenant_id', $tenantId);
            
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            
            return $query;
        } catch (Exception $e) {
            Log::error('Error creating loyalty transactions query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new loyalty transaction
     */
    public function createTransaction(array $data): LoyaltyTransaction
    {
        try {
            $tenantId = $this->getTenantId();
            
            DB::beginTransaction();
            
            $transaction = LoyaltyTransaction::create(array_merge($data, [
                'tenant_id' => $tenantId,
            ]));

            // Update customer loyalty points
            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                if ($data['transaction_type'] === 'EARNED') {
                    $customer->increment('loyalty_points', $data['points']);
                } elseif ($data['transaction_type'] === 'REDEEMED') {
                    $customer->decrement('loyalty_points', $data['points']);
                } elseif ($data['transaction_type'] === 'ADJUSTED') {
                    // For adjusted, we need to calculate the difference
                    $currentPoints = $customer->loyalty_points;
                    $newPoints = $data['points'];
                    $difference = $newPoints - $currentPoints;
                    if ($difference > 0) {
                        $customer->increment('loyalty_points', $difference);
                    } else {
                        $customer->decrement('loyalty_points', abs($difference));
                    }
                }
            }

            DB::commit();

            Log::info('Loyalty transaction created successfully', [
                'transaction_id' => $transaction->id,
                'customer_id' => $data['customer_id'],
                'tenant_id' => $tenantId
            ]);

            return $transaction->load('customer');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating loyalty transaction', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a transaction
     */
    public function deleteTransaction(LoyaltyTransaction $transaction): bool
    {
        try {
            DB::beginTransaction();
            
            // Reverse the points if transaction was EARNED or REDEEMED
            $customer = $transaction->customer;
            if ($customer) {
                if ($transaction->transaction_type === 'EARNED') {
                    $customer->decrement('loyalty_points', $transaction->points);
                } elseif ($transaction->transaction_type === 'REDEEMED') {
                    $customer->increment('loyalty_points', $transaction->points);
                }
            }

            $transactionId = $transaction->id;
            $deleted = $transaction->delete();

            DB::commit();

            if ($deleted) {
                Log::info('Loyalty transaction deleted successfully', [
                    'transaction_id' => $transactionId
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting loyalty transaction', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get customer's current loyalty points
     */
    public function getCustomerPoints(int $customerId): int
    {
        try {
            $customer = Customer::find($customerId);
            return $customer ? $customer->loyalty_points : 0;
        } catch (Exception $e) {
            Log::error('Error getting customer loyalty points', [
                'customer_id' => $customerId,
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

