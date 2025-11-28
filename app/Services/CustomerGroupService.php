<?php

namespace App\Services;

use App\Models\CustomerGroup;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class CustomerGroupService
{
    /**
     * Get all groups for the current tenant
     */
    public function getGroupsForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return CustomerGroup::where('tenant_id', $tenantId)
                ->withCount('customers')
                ->orderBy('group_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching customer groups for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get groups query for DataTables
     */
    public function getGroupsQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();
            
            return CustomerGroup::where('tenant_id', $tenantId)
                ->withCount('customers');
        } catch (Exception $e) {
            Log::error('Error creating customer groups query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new customer group
     */
    public function createGroup(array $data): CustomerGroup
    {
        try {
            $tenantId = $this->getTenantId();
            
            // Check if group name already exists for this tenant
            $existingGroup = CustomerGroup::where('tenant_id', $tenantId)
                ->where('group_name', $data['group_name'])
                ->first();

            if ($existingGroup) {
                throw new Exception('A group with this name already exists.');
            }

            $group = CustomerGroup::create(array_merge($data, [
                'tenant_id' => $tenantId,
                'discount_percentage' => $data['discount_percentage'] ?? 0.00,
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            ]));

            Log::info('Customer group created successfully', [
                'group_id' => $group->id,
                'group_name' => $group->group_name,
                'tenant_id' => $tenantId
            ]);

            return $group;
        } catch (Exception $e) {
            Log::error('Error creating customer group', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update an existing group
     */
    public function updateGroup(CustomerGroup $group, array $data): CustomerGroup
    {
        try {
            // Check if group name already exists for another group in same tenant
            if (isset($data['group_name']) && $data['group_name'] != $group->group_name) {
                $existingGroup = CustomerGroup::where('tenant_id', $group->tenant_id)
                    ->where('group_name', $data['group_name'])
                    ->where('id', '!=', $group->id)
                    ->first();

                if ($existingGroup) {
                    throw new Exception('A group with this name already exists.');
                }
            }

            // Handle is_active checkbox
            if (isset($data['is_active'])) {
                $data['is_active'] = (bool)$data['is_active'];
            }

            $group->update($data);

            Log::info('Customer group updated successfully', [
                'group_id' => $group->id,
                'group_name' => $group->group_name
            ]);

            return $group->fresh();
        } catch (Exception $e) {
            Log::error('Error updating customer group', [
                'group_id' => $group->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a group
     */
    public function deleteGroup(CustomerGroup $group): bool
    {
        try {
            $groupId = $group->id;
            $groupName = $group->group_name;
            
            $deleted = $group->delete();

            if ($deleted) {
                Log::info('Customer group deleted successfully', [
                    'group_id' => $groupId,
                    'group_name' => $groupName
                ]);
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('Error deleting customer group', [
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Add customers to group
     */
    public function addCustomersToGroup(CustomerGroup $group, array $customerIds): void
    {
        try {
            $group->customers()->syncWithoutDetaching($customerIds);

            Log::info('Customers added to group', [
                'group_id' => $group->id,
                'customer_count' => count($customerIds)
            ]);
        } catch (Exception $e) {
            Log::error('Error adding customers to group', [
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Remove customers from group
     */
    public function removeCustomersFromGroup(CustomerGroup $group, array $customerIds): void
    {
        try {
            $group->customers()->detach($customerIds);

            Log::info('Customers removed from group', [
                'group_id' => $group->id,
                'customer_count' => count($customerIds)
            ]);
        } catch (Exception $e) {
            Log::error('Error removing customers from group', [
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Sync customers for group (replace all)
     */
    public function syncCustomersForGroup(CustomerGroup $group, array $customerIds): void
    {
        try {
            $group->customers()->sync($customerIds);

            Log::info('Customers synced for group', [
                'group_id' => $group->id,
                'customer_count' => count($customerIds)
            ]);
        } catch (Exception $e) {
            Log::error('Error syncing customers for group', [
                'group_id' => $group->id,
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

