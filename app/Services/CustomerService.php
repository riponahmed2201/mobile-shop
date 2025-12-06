<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerTag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Str;

class CustomerService
{
    /**
     * Get all customers for the current tenant
     */
    public function getCustomersForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Customer::where('tenant_id', $tenantId)
                ->orderBy('full_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching customers for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get customers query for DataTables
     */
    public function getCustomersQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Customer::where('tenant_id', $tenantId)->latest();
        } catch (Exception $e) {
            Log::error('Error creating customers query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Generate unique customer code
     */
    private function generateCustomerCode(int $tenantId): string
    {
        $prefix = 'CUST';
        $date = date('ym'); // Year and Month (e.g., 2512 for Dec 2025)

        $lastCustomer = Customer::where('tenant_id', $tenantId)
            ->whereNotNull('customer_code')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCustomer && $lastCustomer->customer_code) {
            // Extract the last 4 digits from the customer code
            $lastNumber = (int) substr($lastCustomer->customer_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new customer
     */
    public function createCustomer(array $data, ?array $tags = null): Customer
    {
        try {
            $tenantId = $this->getTenantId();

            // Generate customer code if not provided
            if (empty($data['customer_code'])) {
                $data['customer_code'] = $this->generateCustomerCode($tenantId);
            }

            // Check if mobile already exists for this tenant
            $existingCustomer = Customer::where('tenant_id', $tenantId)
                ->where('mobile_primary', $data['mobile_primary'])
                ->first();

            if ($existingCustomer) {
                throw new Exception('A customer with this mobile number already exists.');
            }

            $customer = Customer::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'customer_type' => $data['customer_type'] ?? 'NEW',
                'total_purchases' => $data['total_purchases'] ?? 0.00,
                'total_repairs' => $data['total_repairs'] ?? 0,
                'loyalty_points' => $data['loyalty_points'] ?? 0,
                'credit_limit' => $data['credit_limit'] ?? 0.00,
                'outstanding_balance' => $data['outstanding_balance'] ?? 0.00,
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
                'created_by' => auth()->id(),
            ]));

            // Add tags if provided
            if ($tags && is_array($tags) && !empty($tags[0])) {
                // Handle comma-separated tags
                $tagArray = [];
                foreach ($tags as $tag) {
                    if (is_string($tag) && strpos($tag, ',') !== false) {
                        $tagArray = array_merge($tagArray, array_map('trim', explode(',', $tag)));
                    } else {
                        $tagArray[] = trim($tag);
                    }
                }

                foreach ($tagArray as $tag) {
                    if (!empty(trim($tag))) {
                        CustomerTag::create([
                            'customer_id' => $customer->id,
                            'tag_name' => trim($tag),
                        ]);
                    }
                }
            }

            Log::info('Customer created successfully', [
                'customer_id' => $customer->id,
                'customer_name' => $customer->full_name,
                'tenant_id' => $tenantId
            ]);

            return $customer->load('tags');
        } catch (Exception $e) {
            Log::error('Error creating customer', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing customer
     */
    public function updateCustomer(Customer $customer, array $data, ?array $tags = null): Customer
    {
        try {
            // Check if mobile already exists for another customer in same tenant
            if (isset($data['mobile_primary']) && $data['mobile_primary'] != $customer->mobile_primary) {
                $existingCustomer = Customer::where('tenant_id', $customer->tenant_id)
                    ->where('mobile_primary', $data['mobile_primary'])
                    ->where('id', '!=', $customer->id)
                    ->first();

                if ($existingCustomer) {
                    throw new Exception('A customer with this mobile number already exists.');
                }
            }

            // Handle is_active checkbox
            if (isset($data['is_active'])) {
                $data['is_active'] = (bool)$data['is_active'];
            }

            $customer->update($data);

            // Update tags if provided
            if ($tags !== null) {
                // Delete existing tags
                $customer->tags()->delete();

                // Add new tags
                if (is_array($tags) && !empty($tags[0])) {
                    // Handle comma-separated tags
                    $tagArray = [];
                    foreach ($tags as $tag) {
                        if (is_string($tag) && strpos($tag, ',') !== false) {
                            $tagArray = array_merge($tagArray, array_map('trim', explode(',', $tag)));
                        } else {
                            $tagArray[] = trim($tag);
                        }
                    }

                    foreach ($tagArray as $tag) {
                        if (!empty(trim($tag))) {
                            CustomerTag::create([
                                'customer_id' => $customer->id,
                                'tag_name' => trim($tag),
                            ]);
                        }
                    }
                }
            }

            Log::info('Customer updated successfully', [
                'customer_id' => $customer->id,
                'customer_name' => $customer->full_name
            ]);

            return $customer->fresh()->load('tags');
        } catch (Exception $e) {
            Log::error('Error updating customer', [
                'customer_id' => $customer->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a customer
     */
    public function deleteCustomer(Customer $customer): bool
    {
        try {
            // Check if customer has outstanding balance
            if ($customer->outstanding_balance > 0) {
                throw new Exception('Cannot delete customer with outstanding balance. Please clear the balance first.');
            }

            $customerId = $customer->id;
            $customerName = $customer->full_name;

            $deleted = $customer->delete();

            if ($deleted) {
                Log::info('Customer deleted successfully', [
                    'customer_id' => $customerId,
                    'customer_name' => $customerName
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting customer', [
                'customer_id' => $customer->id,
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

        // Fallback for testing if no auth
        return 1;
    }
}

