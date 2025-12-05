<?php

namespace App\Services\Purchase;

use App\Models\Purchase\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Exception;

class SupplierService
{
    /**
     * Get all suppliers for the current tenant
     */
    public function getSuppliersForTenant(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Supplier::forTenant($tenantId)
                ->orderBy('supplier_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching suppliers for tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get suppliers query for DataTables
     */
    public function getSuppliersQuery(?int $tenantId = null)
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Supplier::forTenant($tenantId);
        } catch (Exception $e) {
            Log::error('Error creating suppliers query', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get active suppliers for dropdown
     */
    public function getActiveSuppliersForDropdown(?int $tenantId = null): Collection
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            return Supplier::forTenant($tenantId)
                ->active()
                ->orderBy('supplier_name')
                ->get();
        } catch (Exception $e) {
            Log::error('Error fetching active suppliers for dropdown', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a new supplier
     */
    public function createSupplier(array $data): Supplier
    {
        try {
            // Set tenant_id if not provided
            if (!isset($data['tenant_id'])) {
                $data['tenant_id'] = $this->getTenantId();
            }

            // Check if supplier with same name or mobile already exists
            $existingSupplier = Supplier::forTenant($data['tenant_id'])
                ->where(function($query) use ($data) {
                    $query->where('supplier_name', $data['supplier_name'])
                          ->orWhere('mobile', $data['mobile']);
                })
                ->first();

            if ($existingSupplier) {
                throw new Exception('A supplier with this name or mobile number already exists.');
            }

            return Supplier::create($data);
        } catch (Exception $e) {
            Log::error('Error creating supplier', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update a supplier
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        try {
            // Check if supplier with same name or mobile already exists (excluding current)
            $existingSupplier = Supplier::forTenant($supplier->tenant_id)
                ->where('id', '!=', $supplier->id)
                ->where(function($query) use ($data) {
                    $query->where('supplier_name', $data['supplier_name'])
                          ->orWhere('mobile', $data['mobile']);
                })
                ->first();

            if ($existingSupplier) {
                throw new Exception('A supplier with this name or mobile number already exists.');
            }

            $supplier->update($data);

            return $supplier->fresh();
        } catch (Exception $e) {
            Log::error('Error updating supplier', [
                'supplier_id' => $supplier->id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a supplier
     */
    public function deleteSupplier(Supplier $supplier): bool
    {
        try {
            // Check if supplier has any purchase orders
            if ($supplier->purchaseOrders()->count() > 0) {
                throw new Exception('Cannot delete supplier with existing purchase orders. Deactivate instead.');
            }

            return $supplier->delete();
        } catch (Exception $e) {
            Log::error('Error deleting supplier', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Toggle supplier active status
     */
    public function toggleSupplierStatus(Supplier $supplier): Supplier
    {
        try {
            $supplier->update([
                'is_active' => !$supplier->is_active
            ]);

            return $supplier->fresh();
        } catch (Exception $e) {
            Log::error('Error toggling supplier status', [
                'supplier_id' => $supplier->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get supplier statistics
     */
    public function getSupplierStatistics(?int $tenantId = null): array
    {
        try {
            $tenantId = $tenantId ?? $this->getTenantId();

            $totalSuppliers = Supplier::forTenant($tenantId)->count();
            $activeSuppliers = Supplier::forTenant($tenantId)->active()->count();
            $inactiveSuppliers = $totalSuppliers - $activeSuppliers;

            // Get suppliers with outstanding balances
            $suppliersWithBalance = Supplier::forTenant($tenantId)
                ->where('outstanding_balance', '>', 0)
                ->count();

            $totalOutstanding = Supplier::forTenant($tenantId)
                ->sum('outstanding_balance');

            // Get top suppliers by purchase amount
            $topSuppliers = Supplier::forTenant($tenantId)
                ->with(['purchaseOrders' => function($query) {
                    $query->where('order_status', '!=', 'CANCELLED');
                }])
                ->get()
                ->map(function($supplier) {
                    return [
                        'id' => $supplier->id,
                        'name' => $supplier->supplier_name,
                        'total_purchases' => $supplier->total_purchases,
                        'pending_orders' => $supplier->pending_orders_count,
                    ];
                })
                ->sortByDesc('total_purchases')
                ->take(5)
                ->values();

            return [
                'total_suppliers' => $totalSuppliers,
                'active_suppliers' => $activeSuppliers,
                'inactive_suppliers' => $inactiveSuppliers,
                'suppliers_with_balance' => $suppliersWithBalance,
                'total_outstanding' => $totalOutstanding,
                'top_suppliers' => $topSuppliers,
            ];
        } catch (Exception $e) {
            Log::error('Error fetching supplier statistics', [
                'tenant_id' => $tenantId,
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